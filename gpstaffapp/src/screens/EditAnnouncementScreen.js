import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ScrollView,
  Switch,
  Alert,
  ActivityIndicator,
  Platform,
} from 'react-native';
import DateTimePicker from '@react-native-community/datetimepicker';
import * as DocumentPicker from 'expo-document-picker';
import { Ionicons } from '@expo/vector-icons';
import { useAnnouncementFilters } from '../hooks/useAnnouncementFilters';
import MultiSelectChips from '../components/MultiSelectChips';
import StudentSelector from '../components/StudentSelector';
import API from '../api/client';

export default function EditAnnouncementScreen({ route, navigation }) {
  const { announcementId } = route.params;
  const [fetching, setFetching] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [announcement, setAnnouncement] = useState(null);

  // ---------- Filter Hook ----------
  const {
    loading: masterLoading,
    master,
    academicYear, setAcademicYear,
    usertype, setUsertype,
    course, setCourse,
    branches, setBranches,
    coachingTypes, setCoachingTypes,
    category, setCategory,
    batch, setBatch,
    gender, setGender,
    section, setSection,
    student, setStudent,
    sectionOptions,
    studentOptions, setStudentOptions,
    studentLoading,
    studentSearch, setStudentSearch,
    fetchStudents,
    showGender,
    showSection,
    showStudent,
    showCategory,
    showBatch,
    toggleSelection,
    setAllFilters,
  } = useAnnouncementFilters();

  // ---------- Form Fields ----------
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');
  const [isSchedule, setIsSchedule] = useState(0);
  const [startAt, setStartAt] = useState(new Date());
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [datePickerMode, setDatePickerMode] = useState('date');
  const [attachments, setAttachments] = useState([]);
  const [existingAttachments, setExistingAttachments] = useState([]);

  // ---------- Fetch Announcement Data ----------
  useEffect(() => {
    const fetchAnnouncementData = async () => {
      try {
        const res = await API.get(`/admin/announcement/${announcementId}/edit`);
        if (res.data.status) {
          const ann = res.data.announcement;
          setAnnouncement(ann);

          // Atomically pre-fill all filter fields
          setAllFilters({
            ...ann,
            studentOptions: res.data.students || {},
          });

          setTitle(ann.title || '');
          setContent(ann.content || '');
          setIsSchedule(ann.is_schedule == 1 ? 1 : 0);

          if (ann.start_at) {
            setStartAt(new Date(ann.start_at));
          }

          const existing = Array.isArray(ann.attachment)
            ? ann.attachment
            : typeof ann.attachment === 'string'
            ? JSON.parse(ann.attachment || '[]')
            : [];
          setExistingAttachments(existing);
        }
      } catch (err) {
        console.error('Fetch announcement error:', err);
        Alert.alert('Error', 'Failed to load announcement details');
        navigation.goBack();
      } finally {
        setFetching(false);
      }
    };

    fetchAnnouncementData();
  }, [announcementId]);

  // ---------- Attachment Handlers ----------
  const pickDocument = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: '*/*',
        multiple: true,
        copyToCacheDirectory: true,
      });

      if (!result.canceled && result.assets && result.assets.length > 0) {
        setAttachments((prev) => [...prev, ...result.assets]);
      } else if (result.type === 'success') {
        setAttachments((prev) => [...prev, result]);
      }
    } catch (err) {
      console.error('File pick error:', err);
      Alert.alert('Error', 'Failed to select attachment');
    }
  };

  const removeNewAttachment = (index) => {
    setAttachments((prev) => prev.filter((_, i) => i !== index));
  };

  const removeExistingAttachment = (index) => {
    setExistingAttachments((prev) => prev.filter((_, i) => i !== index));
  };

  const formatFileSize = (bytes) => {
    if (!bytes) return '';
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
  };

  // ---------- Submit Update ----------
  const handleSubmit = async () => {
    if (!title.trim()) {
      Alert.alert('Validation Error', 'Please enter announcement title.');
      return;
    }
    if (!course) {
      Alert.alert('Validation Error', 'Please select a course.');
      return;
    }
    if (branches.length === 0) {
      Alert.alert('Validation Error', 'Please select at least one branch.');
      return;
    }
    if (usertype === 'INDIVIDUAL' && !student) {
      Alert.alert('Validation Error', 'Please select a target student.');
      return;
    }

    setSubmitting(true);
    try {
      const formData = new FormData();
      formData.append('academic_year', academicYear);
      formData.append('usertype', usertype);
      formData.append('course', course);
      branches.forEach((b) => formData.append('branch[]', b));
      coachingTypes.forEach((c) => formData.append('coaching_type[]', c));
      category.forEach((c) => formData.append('category[]', c));
      batch.forEach((b) => formData.append('batch[]', b));
      formData.append('gender', gender);

      if (usertype === 'INDIVIDUAL') {
        formData.append('students', student);
      } else {
        formData.append('section', section);
      }

      formData.append('title', title.trim());
      formData.append('content', content.trim());

      if (isSchedule === 1) {
        formData.append('is_schedule', '1');
        const pad = (n) => String(n).padStart(2, '0');
        const dateStr = `${startAt.getFullYear()}-${pad(startAt.getMonth() + 1)}-${pad(
          startAt.getDate()
        )} ${pad(startAt.getHours())}:${pad(startAt.getMinutes())}:00`;
        formData.append('start_at', dateStr);
      }

      // Existing attachments to keep
      existingAttachments.forEach((file) => {
        formData.append('existing_attachment[]', file);
      });

      // New file attachments
      attachments.forEach((file) => {
        formData.append('attachment[]', {
          uri: Platform.OS === 'android' ? file.uri : file.uri.replace('file://', ''),
          name: file.name || 'attachment',
          type: file.mimeType || 'application/octet-stream',
        });
      });

      const res = await API.post(`/admin/announcement/${announcementId}?_method=PUT`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      if (res.data.status) {
        Alert.alert('Success', 'Announcement updated successfully!', [
          { text: 'OK', onPress: () => navigation.goBack() },
        ]);
      } else {
        Alert.alert('Error', res.data.message || 'Update failed');
      }
    } catch (err) {
      console.error('Update error:', err);
      Alert.alert('Error', err.response?.data?.message || 'Update failed');
    } finally {
      setSubmitting(false);
    }
  };

  // ---------- Loading State ----------
  if (fetching || masterLoading) {
    return (
      <View className="flex-1 justify-center items-center bg-slate-50">
        <ActivityIndicator size="large" color="#ff7700" />
        <Text className="text-xs text-gray-500 font-medium mt-3">Loading announcement...</Text>
      </View>
    );
  }

  return (
    <ScrollView className="flex-1 bg-slate-50" showsVerticalScrollIndicator={false}>
      <View className="p-5 pb-20">
        {/* Card: Target Audience */}
        <View className="bg-white rounded-3xl p-5 mb-5 border border-gray-100 shadow-sm">
          <View className="flex-row items-center mb-4">
            <View className="w-8 h-8 rounded-xl bg-primary/10 items-center justify-center mr-2.5">
              <Ionicons name="filter" size={16} color="#2b66a2" />
            </View>
            <Text className="text-sm font-bold text-gray-800 uppercase tracking-wider">
              Target Audience
            </Text>
          </View>

          {/* Academic Year */}
          <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Academic Year</Text>
          <View className="border border-gray-200 rounded-2xl px-4 py-3 mb-4 bg-gray-50 flex-row items-center justify-between">
            <Text className="text-sm font-semibold text-gray-700">{academicYear || 'Active Year'}</Text>
            <Ionicons name="lock-closed-outline" size={16} color="#9ca3af" />
          </View>

          {/* User Type */}
          <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">User Type *</Text>
          <View className="flex-row mb-4">
            {[
              { key: 'GROUP', label: 'Group Broadcast', icon: 'people' },
              { key: 'INDIVIDUAL', label: 'Individual Student', icon: 'person' },
            ].map((type) => {
              const active = usertype === type.key;
              return (
                <TouchableOpacity
                  key={type.key}
                  onPress={() => setUsertype(type.key)}
                  className={`flex-1 flex-row items-center justify-center py-2.5 px-3 rounded-2xl border mr-2 last:mr-0 ${
                    active ? 'bg-primary border-primary shadow-sm' : 'bg-gray-50 border-gray-200'
                  }`}
                >
                  <Ionicons
                    name={type.icon}
                    size={15}
                    color={active ? '#fff' : '#6b7280'}
                    style={{ marginRight: 6 }}
                  />
                  <Text className={`text-xs font-bold ${active ? 'text-white' : 'text-gray-700'}`}>
                    {type.label}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>

          {/* Course */}
          <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Course *</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} className="flex-row mb-4">
            {(master?.course || []).map((c) => {
              const active = course === c;
              return (
                <TouchableOpacity
                  key={c}
                  onPress={() => setCourse(c)}
                  className={`mr-2 px-4 py-2 rounded-full border ${
                    active ? 'bg-primary border-primary shadow-sm' : 'bg-gray-50 border-gray-200'
                  }`}
                >
                  <Text className={`text-xs font-bold ${active ? 'text-white' : 'text-gray-700'}`}>
                    {c}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </ScrollView>

          {/* Branches */}
          <MultiSelectChips
            label="Branches *"
            options={master?.branches || []}
            selected={branches}
            onToggle={(val) => toggleSelection(branches, setBranches, val)}
          />

          {/* Coaching Types */}
          <MultiSelectChips
            label="Coaching Type"
            options={coachingTypes.length ? coachingTypes : master?.coachingtype || []}
            selected={coachingTypes}
            onToggle={(val) => toggleSelection(coachingTypes, setCoachingTypes, val)}
          />

          {/* Category (H/D) */}
          {showCategory && (
            <MultiSelectChips
              label="H/D (Category)"
              options={master?.hostel || []}
              selected={category}
              onToggle={(val) => toggleSelection(category, setCategory, val)}
            />
          )}

          {/* Batch */}
          {showBatch && (
            <MultiSelectChips
              label="Batch"
              options={master?.batch || []}
              selected={batch}
              onToggle={(val) => toggleSelection(batch, setBatch, val)}
            />
          )}

          {/* Gender */}
          {showGender && (
            <View className="mb-4">
              <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Gender</Text>
              <View className="flex-row">
                {['All', 'MALE', 'FEMALE'].map((g) => {
                  const active = gender === g;
                  return (
                    <TouchableOpacity
                      key={g}
                      onPress={() => setGender(g)}
                      className={`mr-2 px-4 py-2 rounded-full border ${
                        active ? 'bg-primary border-primary' : 'bg-gray-50 border-gray-200'
                      }`}
                    >
                      <Text className={`text-xs font-bold ${active ? 'text-white' : 'text-gray-700'}`}>
                        {g === 'All' ? 'All Genders' : g}
                      </Text>
                    </TouchableOpacity>
                  );
                })}
              </View>
            </View>
          )}

          {/* Section (For GROUP) */}
          {usertype === 'GROUP' && showSection && (
            <View className="mb-4">
              <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Section</Text>
              <ScrollView horizontal showsHorizontalScrollIndicator={false} className="flex-row">
                <TouchableOpacity
                  onPress={() => setSection('')}
                  className={`mr-2 px-3.5 py-1.5 rounded-full border ${
                    !section ? 'bg-primary border-primary shadow-sm' : 'bg-gray-50 border-gray-200'
                  }`}
                >
                  <Text className={`text-xs font-bold ${!section ? 'text-white' : 'text-gray-700'}`}>
                    All Sections
                  </Text>
                </TouchableOpacity>
                {sectionOptions.map((sec) => {
                  const active = section === sec;
                  return (
                    <TouchableOpacity
                      key={sec}
                      onPress={() => setSection(sec)}
                      className={`mr-2 px-3.5 py-1.5 rounded-full border ${
                        active ? 'bg-primary border-primary shadow-sm' : 'bg-gray-50 border-gray-200'
                      }`}
                    >
                      <Text className={`text-xs font-bold ${active ? 'text-white' : 'text-gray-700'}`}>
                        {sec}
                      </Text>
                    </TouchableOpacity>
                  );
                })}
              </ScrollView>
            </View>
          )}

          {/* Searchable Student Selector (For INDIVIDUAL) */}
          {showStudent && (
            <StudentSelector
              selectedStudentId={student}
              onSelectStudent={setStudent}
              studentOptions={studentOptions}
              loading={studentLoading}
              searchValue={studentSearch}
              setSearchValue={setStudentSearch}
              onSearch={fetchStudents}
              sectionOptions={sectionOptions}
              selectedSection={section}
              onSelectSection={setSection}
            />
          )}
        </View>

        {/* Card: Announcement Details */}
        <View className="bg-white rounded-3xl p-5 mb-5 border border-gray-100 shadow-sm">
          <View className="flex-row items-center mb-4">
            <View className="w-8 h-8 rounded-xl bg-fanta/10 items-center justify-center mr-2.5">
              <Ionicons name="create-outline" size={16} color="#ff7700" />
            </View>
            <Text className="text-sm font-bold text-gray-800 uppercase tracking-wider">
              Content & Details
            </Text>
          </View>

          {/* Title */}
          <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Title *</Text>
          <TextInput
            value={title}
            onChangeText={setTitle}
            placeholder="Enter announcement headline"
            placeholderTextColor="#9ca3af"
            className="bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm text-gray-800 mb-4"
          />

          {/* Content */}
          <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Message Body</Text>
          <TextInput
            value={content}
            onChangeText={setContent}
            placeholder="Write announcement details here..."
            placeholderTextColor="#9ca3af"
            multiline
            numberOfLines={4}
            textAlignVertical="top"
            className="bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm text-gray-800 mb-4 min-h-[110px]"
          />

          {/* Attachments */}
          <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">Attachments</Text>

          {/* Current Saved Files */}
          {existingAttachments.length > 0 && (
            <View className="mb-3">
              <Text className="text-[11px] font-bold text-gray-500 uppercase mb-1.5">
                Current Saved Files ({existingAttachments.length})
              </Text>
              {existingAttachments.map((file, idx) => (
                <View
                  key={idx}
                  className="bg-gray-50 border border-gray-200 rounded-2xl px-3.5 py-2.5 mb-2 flex-row items-center justify-between"
                >
                  <View className="flex-row items-center flex-1 pr-2">
                    <Ionicons name="document-text" size={18} color="#2b66a2" />
                    <Text className="text-xs font-semibold text-gray-800 ml-2.5 flex-1" numberOfLines={1}>
                      {file.split('/').pop()}
                    </Text>
                  </View>
                  <TouchableOpacity onPress={() => removeExistingAttachment(idx)}>
                    <Ionicons name="close-circle" size={20} color="#ef4444" />
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          )}

          {/* Newly Added Files */}
          {attachments.length > 0 && (
            <View className="mb-3">
              <Text className="text-[11px] font-bold text-primary uppercase mb-1.5">
                New Files to Upload ({attachments.length})
              </Text>
              {attachments.map((file, idx) => (
                <View
                  key={idx}
                  className="bg-primary/5 border border-primary/20 rounded-2xl px-3.5 py-2.5 mb-2 flex-row items-center justify-between"
                >
                  <View className="flex-row items-center flex-1 pr-2">
                    <Ionicons name="document-attach" size={18} color="#2b66a2" />
                    <View className="ml-2.5 flex-1">
                      <Text className="text-xs font-semibold text-primary" numberOfLines={1}>
                        {file.name}
                      </Text>
                      {file.size ? (
                        <Text className="text-[10px] text-gray-400">
                          {formatFileSize(file.size)}
                        </Text>
                      ) : null}
                    </View>
                  </View>
                  <TouchableOpacity onPress={() => removeNewAttachment(idx)}>
                    <Ionicons name="close-circle" size={20} color="#ef4444" />
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          )}

          <TouchableOpacity
            onPress={pickDocument}
            className="border border-dashed border-primary/40 bg-primary/5 rounded-2xl py-3 items-center justify-center flex-row mb-4 active:bg-primary/10"
          >
            <Ionicons name="cloud-upload-outline" size={18} color="#2b66a2" className="mr-2" />
            <Text className="text-primary text-xs font-bold ml-1.5">+ Select Files to Attach</Text>
          </TouchableOpacity>

          {/* Schedule Notice Toggle */}
          <View className="flex-row items-center justify-between py-3 border-t border-gray-100">
            <View>
              <Text className="text-sm font-bold text-gray-800">Schedule Broadcast</Text>
              <Text className="text-[11px] text-gray-400">Publish at a specific future date & time</Text>
            </View>
            <Switch
              value={isSchedule === 1}
              onValueChange={(val) => setIsSchedule(val ? 1 : 0)}
              trackColor={{ false: '#e2e8f0', true: '#ff7700' }}
              thumbColor={Platform.OS === 'android' ? '#ffffff' : undefined}
            />
          </View>

          {/* DateTime Picker */}
          {isSchedule === 1 && (
            <View className="mt-3 pt-3 border-t border-gray-50">
              <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">
                Publish Date & Time
              </Text>
              <TouchableOpacity
                onPress={() => {
                  setDatePickerMode('date');
                  setShowDatePicker(true);
                }}
                className="bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 flex-row items-center justify-between"
              >
                <View className="flex-row items-center">
                  <Ionicons name="calendar-outline" size={18} color="#ff7700" />
                  <Text className="text-sm font-semibold text-gray-800 ml-2.5">
                    {startAt.toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' })}
                  </Text>
                </View>
                <Text className="text-xs font-bold text-primary">Change</Text>
              </TouchableOpacity>

              {showDatePicker && (
                <DateTimePicker
                  value={startAt}
                  mode={datePickerMode}
                  display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                  minimumDate={new Date()}
                  onChange={(event, selectedDate) => {
                    if (Platform.OS === 'android') {
                      setShowDatePicker(false);
                      if (event.type === 'set' && selectedDate) {
                        setStartAt(selectedDate);
                        if (datePickerMode === 'date') {
                          setDatePickerMode('time');
                          setShowDatePicker(true);
                        }
                      }
                    } else {
                      if (selectedDate) setStartAt(selectedDate);
                    }
                  }}
                />
              )}
            </View>
          )}
        </View>

        {/* Submit Button */}
        <TouchableOpacity
          onPress={handleSubmit}
          disabled={submitting}
          activeOpacity={0.85}
          className="bg-fanta py-4 rounded-2xl items-center shadow-lg shadow-fanta/40 active:opacity-90 flex-row justify-center"
        >
          {submitting ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <>
              <Ionicons name="checkmark-circle" size={18} color="#fff" style={{ marginRight: 8 }} />
              <Text className="text-white font-bold text-base">Update Announcement</Text>
            </>
          )}
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}