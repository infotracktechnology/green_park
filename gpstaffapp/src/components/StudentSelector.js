import React, { useState, useMemo } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';

export default function StudentSelector({
  selectedStudentId,
  onSelectStudent,
  studentOptions = {},
  loading = false,
  onSearch,
  searchValue = '',
  setSearchValue,
  sectionOptions = [],
  selectedSection = '',
  onSelectSection,
}) {
  const [isExpanded, setIsExpanded] = useState(!selectedStudentId);
  const [localSearch, setLocalSearch] = useState('');

  // Convert studentOptions { id: name } or array to entries
  const studentList = useMemo(() => {
    if (!studentOptions) return [];
    if (Array.isArray(studentOptions)) {
      return studentOptions.map((s) => ({
        id: s?.student_id || s?.id || '',
        name: s?.student_name || s?.name || '',
      }));
    }
    return Object.entries(studentOptions).map(([id, name]) => ({
      id: id || '',
      name: name || '',
    }));
  }, [studentOptions]);

  // Filter students locally by search text
  const filteredStudents = useMemo(() => {
    const query = (localSearch || searchValue || '').toLowerCase().trim();
    if (!query) return studentList;
    return studentList.filter((s) => {
      const idStr = s?.id != null ? String(s.id).toLowerCase() : '';
      const nameStr = s?.name != null ? String(s.name).toLowerCase() : '';
      return idStr.includes(query) || nameStr.includes(query);
    });
  }, [studentList, localSearch, searchValue]);

  const selectedStudentName = useMemo(() => {
    if (!selectedStudentId) return '';
    if (studentOptions && studentOptions[selectedStudentId]) {
      return studentOptions[selectedStudentId];
    }
    const found = studentList.find(
      (s) => s?.id != null && String(s.id) === String(selectedStudentId)
    );
    return found ? found.name : String(selectedStudentId);
  }, [selectedStudentId, studentOptions, studentList]);

  return (
    <View className="mb-4">
      <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">
        Target Student *
      </Text>

      {/* Selected Student Display (When collapsed and student selected) */}
      {selectedStudentId && !isExpanded ? (
        <View className="bg-primary/5 border border-primary/20 rounded-2xl p-3.5 flex-row items-center justify-between shadow-sm">
          <View className="flex-row items-center flex-1 pr-2">
            <View className="w-10 h-10 rounded-xl bg-primary/10 items-center justify-center mr-3">
              <Ionicons name="person" size={20} color="#2b66a2" />
            </View>
            <View className="flex-1">
              <Text className="text-xs font-bold text-primary">
                ID: {String(selectedStudentId)}
              </Text>
              <Text className="text-sm font-semibold text-gray-800" numberOfLines={1}>
                {selectedStudentName || 'Student Selected'}
              </Text>
            </View>
          </View>
          <View className="flex-row items-center">
            <TouchableOpacity
              onPress={() => setIsExpanded(true)}
              className="bg-primary/10 px-3 py-1.5 rounded-lg mr-1.5 active:bg-primary/20"
            >
              <Text className="text-xs font-bold text-primary">Change</Text>
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() => {
                onSelectStudent('');
                setIsExpanded(true);
              }}
              className="bg-red-50 p-1.5 rounded-lg active:bg-red-100"
            >
              <Ionicons name="close" size={16} color="#ef4444" />
            </TouchableOpacity>
          </View>
        </View>
      ) : (
        <View className="bg-gray-50 border border-gray-200 rounded-2xl p-4 shadow-sm">
          {/* Header row in expanded box */}
          <View className="flex-row items-center justify-between mb-3">
            <View className="flex-row items-center">
              <Ionicons name="search" size={16} color="#2b66a2" style={{ marginRight: 6 }} />
              <Text className="text-xs font-bold text-gray-700">Search & Select Student</Text>
            </View>
            {selectedStudentId ? (
              <TouchableOpacity
                onPress={() => setIsExpanded(false)}
                className="bg-gray-200/80 px-2.5 py-1 rounded-lg"
              >
                <Text className="text-[11px] font-bold text-gray-600">Done</Text>
              </TouchableOpacity>
            ) : null}
          </View>

          {/* Search Input Bar */}
          <View className="bg-white border border-gray-200 rounded-xl px-3 py-2 flex-row items-center shadow-sm mb-3">
            <Ionicons name="search" size={16} color="#9ca3af" />
            <TextInput
              value={localSearch}
              onChangeText={(txt) => {
                setLocalSearch(txt);
                if (setSearchValue) setSearchValue(txt);
                if (onSearch) onSearch(txt);
              }}
              placeholder="Type name or ID to filter..."
              placeholderTextColor="#9ca3af"
              className="flex-1 text-xs text-gray-800 ml-2 py-0"
              autoCapitalize="none"
              autoCorrect={false}
            />
            {localSearch.length > 0 && (
              <TouchableOpacity
                onPress={() => {
                  setLocalSearch('');
                  if (setSearchValue) setSearchValue('');
                  if (onSearch) onSearch('');
                }}
              >
                <Ionicons name="close-circle" size={16} color="#9ca3af" />
              </TouchableOpacity>
            )}
          </View>

          {/* Section Filter Chips */}
          {sectionOptions && sectionOptions.length > 0 && (
            <View className="mb-3">
              <ScrollView horizontal showsHorizontalScrollIndicator={false} className="flex-row">
                <TouchableOpacity
                  onPress={() => {
                    if (onSelectSection) onSelectSection('');
                  }}
                  className={`mr-1.5 px-3 py-1 rounded-full border ${
                    !selectedSection
                      ? 'bg-primary border-primary'
                      : 'bg-white border-gray-200'
                  }`}
                >
                  <Text
                    className={`text-[11px] font-bold ${
                      !selectedSection ? 'text-white' : 'text-gray-600'
                    }`}
                  >
                    All Sections
                  </Text>
                </TouchableOpacity>
                {sectionOptions.map((sec) => {
                  const active = selectedSection === sec;
                  return (
                    <TouchableOpacity
                      key={sec}
                      onPress={() => {
                        if (onSelectSection) onSelectSection(sec);
                      }}
                      className={`mr-1.5 px-3 py-1 rounded-full border ${
                        active
                          ? 'bg-primary border-primary'
                          : 'bg-white border-gray-200'
                      }`}
                    >
                      <Text
                        className={`text-[11px] font-bold ${
                          active ? 'text-white' : 'text-gray-600'
                        }`}
                      >
                        {sec}
                      </Text>
                    </TouchableOpacity>
                  );
                })}
              </ScrollView>
            </View>
          )}

          {/* Matching Students List */}
          {loading ? (
            <View className="py-6 items-center justify-center">
              <ActivityIndicator size="small" color="#ff7700" />
              <Text className="text-[11px] text-gray-400 mt-2">Loading students...</Text>
            </View>
          ) : filteredStudents.length > 0 ? (
            <ScrollView
              nestedScrollEnabled={true}
              style={{ maxHeight: 220 }}
              showsVerticalScrollIndicator={true}
              className="border border-gray-100 rounded-xl bg-white p-1"
            >
              {filteredStudents.map((item) => {
                const isSelected =
                  selectedStudentId != null &&
                  item?.id != null &&
                  String(selectedStudentId) === String(item.id);
                return (
                  <TouchableOpacity
                    key={item.id}
                    onPress={() => {
                      onSelectStudent(item.id);
                      setIsExpanded(false);
                    }}
                    activeOpacity={0.7}
                    className={`p-2.5 mb-1 rounded-xl flex-row items-center justify-between ${
                      isSelected
                        ? 'bg-primary/10 border border-primary/30'
                        : 'bg-white border border-gray-50'
                    }`}
                  >
                    <View className="flex-row items-center flex-1 pr-2">
                      <View
                        className={`w-7 h-7 rounded-lg items-center justify-center mr-2 ${
                          isSelected ? 'bg-primary' : 'bg-gray-100'
                        }`}
                      >
                        <Ionicons
                          name="person"
                          size={14}
                          color={isSelected ? '#fff' : '#64748b'}
                        />
                      </View>
                      <View className="flex-1">
                        <Text
                          className={`text-xs font-bold ${
                            isSelected ? 'text-primary' : 'text-gray-800'
                          }`}
                          numberOfLines={1}
                        >
                          {item.name}
                        </Text>
                        <Text className="text-[10px] text-gray-400">
                          ID: {item.id}
                        </Text>
                      </View>
                    </View>
                    {isSelected ? (
                      <Ionicons name="checkmark-circle" size={16} color="#2b66a2" />
                    ) : (
                      <Ionicons name="chevron-forward" size={14} color="#d1d5db" />
                    )}
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          ) : (
            <View className="py-6 items-center justify-center">
              <Text className="text-xs text-gray-500 font-medium">No matching students</Text>
              <Text className="text-[10px] text-gray-400 mt-0.5">Try searching with a different term</Text>
            </View>
          )}
        </View>
      )}
    </View>
  );
}
