import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:dio/dio.dart';

import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import '../widgets/multi_select_chips.dart';
import '../widgets/student_selector.dart';

class CreateDiscussionVideoScreen extends StatefulWidget {
  const CreateDiscussionVideoScreen({super.key});

  @override
  State<CreateDiscussionVideoScreen> createState() =>
      _CreateDiscussionVideoScreenState();
}

class _CreateDiscussionVideoScreenState
    extends State<CreateDiscussionVideoScreen> {
  final TextEditingController _titleController = TextEditingController();
  final TextEditingController _videoIdController = TextEditingController();
  final TextEditingController _dayController = TextEditingController();

  DateTime _selectedDate = DateTime.now();
  String _selectedSubject = '';
  String _selectedPart = '';
  DateTime? _startAt;
  DateTime? _endAt;
  bool _submitting = false;

  final List<String> _subjects = ['PHYSICS', 'CHEMISTRY', 'ZOOLOGY', 'BOTANY'];
  final List<String> _parts = ['Part 1', 'Part 2', 'Part 3', 'Full Part'];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final filters =
          Provider.of<AnnouncementFilterProvider>(context, listen: false);
      filters.resetAll();
      filters.fetchMasterData();
    });
  }

  @override
  void dispose() {
    _titleController.dispose();
    _videoIdController.dispose();
    _dayController.dispose();
    super.dispose();
  }

  Future<void> _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: Colors.white,
              onSurface: AppColors.textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() => _selectedDate = picked);
    }
  }

  Future<void> _selectDateTime({required bool isStart}) async {
    final initial = isStart
        ? (_startAt ?? DateTime.now().add(const Duration(hours: 1)))
        : (_endAt ??
            (_startAt ?? DateTime.now().add(const Duration(hours: 1))));

    final pickedDate = await showDatePicker(
      context: context,
      initialDate: initial,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: Colors.white,
              onSurface: AppColors.textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );

    if (pickedDate != null && mounted) {
      final pickedTime = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.fromDateTime(initial),
        builder: (context, child) {
          return Theme(
            data: Theme.of(context).copyWith(
              colorScheme: const ColorScheme.light(
                primary: AppColors.primary,
                onPrimary: Colors.white,
                onSurface: AppColors.textPrimary,
              ),
            ),
            child: child!,
          );
        },
      );

      if (pickedTime != null && mounted) {
        final picked = DateTime(
          pickedDate.year,
          pickedDate.month,
          pickedDate.day,
          pickedTime.hour,
          pickedTime.minute,
        );

        if (!isStart && _startAt != null && _startAt!.isAfter(picked)) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
                content: Text('End time must be greater than start time.'),
                backgroundColor: AppColors.error),
          );
          return;
        }

        setState(() {
          if (isStart) {
            _startAt = picked;
            if (_endAt != null && _endAt!.isBefore(picked)) _endAt = null;
          } else {
            _endAt = picked;
          }
        });
      }
    }
  }

  Widget _buildDateTimeField(String label, DateTime? value, bool isStart) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: AppColors.textSecondary),
        ),
        const SizedBox(height: 8),
        InkWell(
          onTap: () => _selectDateTime(isStart: isStart),
          borderRadius: BorderRadius.circular(16),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            decoration: BoxDecoration(
              color: const Color(0xFFF8FAFC),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Icon(isStart ? Icons.play_arrow : Icons.stop,
                        size: 18, color: AppColors.fanta),
                    const SizedBox(width: 10),
                    Text(
                      value != null
                          ? DateFormat('dd MMM yyyy, hh:mm a').format(value)
                          : 'Select',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: value != null
                            ? AppColors.textPrimary
                            : AppColors.textMuted,
                      ),
                    ),
                  ],
                ),
                const Text(
                  'Change',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Future<void> _handleSubmit() async {
    final filters =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    final title = _titleController.text.trim();
    final videoId = _videoIdController.text.trim();
    final day = _dayController.text.trim();

    if (filters.course.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select a course.');
      return;
    }
    if (filters.branches.isEmpty) {
      _showErrorDialog(
          'Validation Error', 'Please select at least one branch.');
      return;
    }
    if (filters.usertype == 'INDIVIDUAL' && filters.student.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select a target student.');
      return;
    }
    if (day.isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter day (e.g. Day 1).');
      return;
    }
    if (_selectedSubject.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select a subject.');
      return;
    }
    if (_selectedPart.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select a part.');
      return;
    }
    if (title.isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter video title.');
      return;
    }
    if (videoId.isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter video ID.');
      return;
    }
    if (_startAt == null || _endAt == null) {
      _showErrorDialog(
          'Validation Error', 'Please select start and end date/time.');
      return;
    }
    if (_startAt!.isAfter(_endAt!)) {
      _showErrorDialog(
          'Validation Error', 'End time must be greater than start time.');
      return;
    }

    setState(() => _submitting = true);

    try {
      final formData = FormData();

      formData.fields.add(MapEntry('academic_year', filters.academicYear));
      formData.fields.add(MapEntry('usertype', filters.usertype));
      formData.fields.add(MapEntry('course', filters.course));

      for (var b in filters.branches) {
        formData.fields.add(MapEntry('branch[]', b.toString()));
      }
      for (var c in filters.coachingTypes) {
        formData.fields.add(MapEntry('coaching_type[]', c));
      }
      for (var cat in filters.category) {
        formData.fields.add(MapEntry('category[]', cat));
      }
      for (var bat in filters.batch) {
        formData.fields.add(MapEntry('batch[]', bat));
      }

      if (filters.usertype == 'INDIVIDUAL') {
        formData.fields.add(MapEntry('students', filters.student));
      } else {
        formData.fields.add(MapEntry('gender', filters.gender.isNotEmpty ? filters.gender : 'All'));
        formData.fields.add(MapEntry('section', filters.section));
      }

      formData.fields.add(MapEntry('day', day));
      formData.fields.add(
          MapEntry('date', DateFormat('yyyy-MM-dd').format(_selectedDate)));
      formData.fields.add(MapEntry('subject', _selectedSubject));
      formData.fields.add(MapEntry('part', _selectedPart));
      formData.fields.add(MapEntry('title', title));
      formData.fields.add(MapEntry('video_id', videoId));
      formData.fields.add(MapEntry(
          'start_at', DateFormat('yyyy-MM-dd HH:mm:00').format(_startAt!)));
      formData.fields.add(MapEntry(
          'end_at', DateFormat('yyyy-MM-dd HH:mm:00').format(_endAt!)));

      final dio = ApiClient().dio;
      final res = await dio.post(
        '/admin/discussionvideo',
        data: formData,
        options: Options(contentType: 'multipart/form-data'),
      );

      if (mounted) {
        if (res.data != null && res.data['status'] == true) {
          showDialog(
            context: context,
            barrierDismissible: false,
            builder: (ctx) => AlertDialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(20)),
              title: const Text('Success',
                  style: TextStyle(
                      fontWeight: FontWeight.bold, color: AppColors.primary)),
              content: const Text('Discussion video created successfully!'),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.pop(ctx);
                    Navigator.pop(context, true);
                  },
                  child: const Text('OK',
                      style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary)),
                ),
              ],
            ),
          );
        } else {
          final msg = (res.data is Map ? res.data['message'] : null) ??
              'Creation failed';
          _showErrorDialog('Error', msg.toString());
        }
      }
    } on DioException catch (e) {
      String msg = 'Submission failed';
      if (e.response?.data != null && e.response?.data is Map) {
        msg = e.response?.data['message']?.toString() ?? msg;
      }
      _showErrorDialog('Error', msg);
    } catch (e) {
      _showErrorDialog('Error', e.toString());
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  void _showErrorDialog(String title, String message) {
    if (!mounted) return;
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('OK',
                style: TextStyle(
                    fontWeight: FontWeight.bold, color: AppColors.primary)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filters = Provider.of<AnnouncementFilterProvider>(context);

    if (filters.loading) {
      return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: const Text('Add Discussion Video')),
        body: const Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(color: AppColors.fanta),
              SizedBox(height: 12),
              Text(
                'Loading master data...',
                style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Add Discussion Video'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
        child: Column(
          children: [
            // Card: Target Audience
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: AppColors.borderLight),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.02),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: AppColors.primary.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(Icons.tune,
                            size: 16, color: AppColors.primary),
                      ),
                      const SizedBox(width: 10),
                      const Text(
                        'TARGET AUDIENCE',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w900,
                          color: AppColors.textPrimary,
                          letterSpacing: 0.8,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),

                  // Academic Year (Locked)
                  const Text(
                    'ACADEMIC YEAR',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 14),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          filters.academicYear.isNotEmpty
                              ? filters.academicYear
                              : 'Active Year',
                          style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: AppColors.textPrimary),
                        ),
                        const Icon(Icons.lock_outline,
                            size: 18, color: AppColors.textMuted),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),

                  // User Type Toggle
                  const Text(
                    'USER TYPE *',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: InkWell(
                          onTap: () => filters.setUsertype('GROUP'),
                          borderRadius: BorderRadius.circular(16),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: filters.usertype == 'GROUP'
                                  ? AppColors.primary
                                  : const Color(0xFFF8FAFC),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: filters.usertype == 'GROUP'
                                    ? AppColors.primary
                                    : AppColors.border,
                              ),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.people,
                                  size: 16,
                                  color: filters.usertype == 'GROUP'
                                      ? Colors.white
                                      : AppColors.textSecondary,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Group Broadcast',
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: filters.usertype == 'GROUP'
                                        ? Colors.white
                                        : AppColors.textSecondary,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: InkWell(
                          onTap: () => filters.setUsertype('INDIVIDUAL'),
                          borderRadius: BorderRadius.circular(16),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: filters.usertype == 'INDIVIDUAL'
                                  ? AppColors.primary
                                  : const Color(0xFFF8FAFC),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: filters.usertype == 'INDIVIDUAL'
                                    ? AppColors.primary
                                    : AppColors.border,
                              ),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.person,
                                  size: 16,
                                  color: filters.usertype == 'INDIVIDUAL'
                                      ? Colors.white
                                      : AppColors.textSecondary,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Individual Student',
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: filters.usertype == 'INDIVIDUAL'
                                        ? Colors.white
                                        : AppColors.textSecondary,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Course Chips
                  const Text(
                    'COURSE *',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    child: Row(
                      children: (filters.master?.courses ?? []).map((c) {
                        final active = filters.course == c;
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: ChoiceChip(
                            label: Text(c),
                            labelStyle: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color:
                                  active ? Colors.white : AppColors.textPrimary,
                            ),
                            selected: active,
                            selectedColor: AppColors.primary,
                            backgroundColor: const Color(0xFFF8FAFC),
                            side: BorderSide(
                                color: active
                                    ? AppColors.primary
                                    : AppColors.border),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(20)),
                            onSelected: (_) => filters.setCourse(c),
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Branches Multi-select Chips
                  MultiSelectChips<BranchItem>(
                    label: 'Branches *',
                    options: filters.availableBranches,
                    selected: filters.branches,
                    labelBuilder: (b) => b.name,
                    valueBuilder: (b) => b.id,
                    onToggle: (val) => filters.toggleBranch(val),
                  ),

                  // Coaching Types Multi-select Chips
                  MultiSelectChips<String>(
                    label: 'Coaching Type',
                    options: filters.availableCoachingTypes,
                    selected: filters.coachingTypes,
                    onToggle: (val) => filters.toggleCoachingType(val),
                  ),

                  // Category (H/D) Chips (Conditional)
                  if (filters.showCategory)
                    MultiSelectChips<String>(
                      label: 'H/D (Category)',
                      options: filters.master?.hostels ?? [],
                      selected: filters.category,
                      onToggle: (val) => filters.toggleCategory(val),
                    ),

                  // Batch Chips (Conditional)
                  if (filters.showBatch)
                    MultiSelectChips<String>(
                      label: 'Batch',
                      options: filters.master?.batches ?? [],
                      selected: filters.batch,
                      onToggle: (val) => filters.toggleBatch(val),
                    ),

                  // Gender Chips
                  if (filters.showGender) ...[
                    const Text(
                      'GENDER',
                      style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: ['All', 'MALE', 'FEMALE'].map((g) {
                        final active = filters.gender == g;
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: ChoiceChip(
                            label: Text(g == 'All' ? 'All Genders' : g),
                            labelStyle: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color:
                                  active ? Colors.white : AppColors.textPrimary,
                            ),
                            selected: active,
                            selectedColor: AppColors.primary,
                            backgroundColor: const Color(0xFFF8FAFC),
                            side: BorderSide(
                                color: active
                                    ? AppColors.primary
                                    : AppColors.border),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(20)),
                            onSelected: (_) => filters.setGender(g),
                          ),
                        );
                      }).toList(),
                    ),
                    const SizedBox(height: 16),
                  ],

                  // Section Chips (For GROUP)
                  if (filters.usertype == 'GROUP' && filters.showSection) ...[
                    const Text(
                      'SECTION',
                      style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary),
                    ),
                    const SizedBox(height: 8),
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      physics: const BouncingScrollPhysics(),
                      child: Row(
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(right: 8),
                            child: ChoiceChip(
                              label: const Text('All Sections'),
                              labelStyle: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: filters.section.isEmpty
                                    ? Colors.white
                                    : AppColors.textPrimary,
                              ),
                              selected: filters.section.isEmpty,
                              selectedColor: AppColors.primary,
                              backgroundColor: const Color(0xFFF8FAFC),
                              side: BorderSide(
                                color: filters.section.isEmpty
                                    ? AppColors.primary
                                    : AppColors.border,
                              ),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20)),
                              onSelected: (_) => filters.setSection(''),
                            ),
                          ),
                          ...filters.sectionOptions.map(
                            (sec) => Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: ChoiceChip(
                                label: Text(sec),
                                labelStyle: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: filters.section == sec
                                      ? Colors.white
                                      : AppColors.textPrimary,
                                ),
                                selected: filters.section == sec,
                                selectedColor: AppColors.primary,
                                backgroundColor: const Color(0xFFF8FAFC),
                                side: BorderSide(
                                  color: filters.section == sec
                                      ? AppColors.primary
                                      : AppColors.border,
                                ),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(20)),
                                onSelected: (_) => filters.setSection(sec),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),
                  ],

                  // Student Selector Widget (For INDIVIDUAL)
                  if (filters.showStudent)
                    StudentSelector(
                      selectedStudentId: filters.student,
                      onSelectStudent: (id) => filters.setStudent(id),
                      studentOptions: filters.studentOptions,
                      loading: filters.studentLoading,
                      searchValue: filters.studentSearch,
                      onSearchChanged: (s) => filters.setStudentSearch(s),
                      onSearch: (s) => filters.fetchStudents(s),
                      sectionOptions: filters.sectionOptions,
                      selectedSection: filters.section,
                      onSelectSection: (sec) => filters.setSection(sec),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Card: Video Details
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: AppColors.borderLight),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.02),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: AppColors.fanta.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(Icons.forum_outlined,
                            size: 18, color: AppColors.fanta),
                      ),
                      const SizedBox(width: 10),
                      const Text(
                        'DISCUSSION DETAILS',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w900,
                          color: AppColors.textPrimary,
                          letterSpacing: 0.8,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),

                  // Day & Date Row
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'DAY *',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary),
                            ),
                            const SizedBox(height: 8),
                            TextField(
                              controller: _dayController,
                              style: const TextStyle(
                                  fontSize: 14, color: AppColors.textPrimary),
                              decoration: InputDecoration(
                                hintText: 'e.g. Day 1',
                                hintStyle: const TextStyle(
                                    fontSize: 14, color: AppColors.textMuted),
                                filled: true,
                                fillColor: const Color(0xFFF8FAFC),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(16),
                                  borderSide:
                                      const BorderSide(color: AppColors.border),
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(16),
                                  borderSide:
                                      const BorderSide(color: AppColors.border),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(16),
                                  borderSide: const BorderSide(
                                      color: AppColors.primary, width: 1.5),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'DATE *',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary),
                            ),
                            const SizedBox(height: 8),
                            InkWell(
                              onTap: _selectDate,
                              borderRadius: BorderRadius.circular(16),
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 14, vertical: 14),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF8FAFC),
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(color: AppColors.border),
                                ),
                                child: Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      DateFormat('dd/MM/yyyy')
                                          .format(_selectedDate),
                                      style: const TextStyle(
                                        fontSize: 13,
                                        fontWeight: FontWeight.w600,
                                        color: AppColors.textPrimary,
                                      ),
                                    ),
                                    const Icon(Icons.calendar_today,
                                        size: 16, color: AppColors.fanta),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Subject Chips
                  const Text(
                    'SUBJECT *',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    child: Row(
                      children: _subjects.map((sub) {
                        final active = _selectedSubject == sub;
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: ChoiceChip(
                            label: Text(sub),
                            labelStyle: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color:
                                  active ? Colors.white : AppColors.textPrimary,
                            ),
                            selected: active,
                            selectedColor: AppColors.primary,
                            backgroundColor: const Color(0xFFF8FAFC),
                            side: BorderSide(
                                color: active
                                    ? AppColors.primary
                                    : AppColors.border),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(20)),
                            onSelected: (_) {
                              setState(() => _selectedSubject = sub);
                            },
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Part Chips
                  const Text(
                    'PART *',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    child: Row(
                      children: _parts.map((p) {
                        final active = _selectedPart == p;
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: ChoiceChip(
                            label: Text(p),
                            labelStyle: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color:
                                  active ? Colors.white : AppColors.textPrimary,
                            ),
                            selected: active,
                            selectedColor: AppColors.fanta,
                            backgroundColor: const Color(0xFFF8FAFC),
                            side: BorderSide(
                                color: active
                                    ? AppColors.fanta
                                    : AppColors.border),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(20)),
                            onSelected: (_) {
                              setState(() => _selectedPart = p);
                            },
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Title
                  const Text(
                    'TITLE *',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _titleController,
                    style: const TextStyle(
                        fontSize: 14, color: AppColors.textPrimary),
                    decoration: InputDecoration(
                      hintText: 'Enter discussion title',
                      hintStyle: const TextStyle(
                          fontSize: 14, color: AppColors.textMuted),
                      prefixIcon: const Icon(Icons.title,
                          size: 18, color: AppColors.fanta),
                      filled: true,
                      fillColor: const Color(0xFFF8FAFC),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: AppColors.border),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: AppColors.border),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(
                            color: AppColors.primary, width: 1.5),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Video ID
                  const Text(
                    'VIDEO ID *',
                    style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _videoIdController,
                    style: const TextStyle(
                        fontSize: 14, color: AppColors.textPrimary),
                    decoration: InputDecoration(
                      hintText: 'e.g. 10243',
                      hintStyle: const TextStyle(
                          fontSize: 14, color: AppColors.textMuted),
                      prefixIcon: const Icon(Icons.numbers,
                          size: 18, color: AppColors.fanta),
                      filled: true,
                      fillColor: const Color(0xFFF8FAFC),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: AppColors.border),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: AppColors.border),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(
                            color: AppColors.primary, width: 1.5),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Start and End DateTime
                  _buildDateTimeField('START DATETIME *', _startAt, true),
                  const SizedBox(height: 16),
                  _buildDateTimeField('END DATETIME *', _endAt, false),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Submit Button
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                onPressed: _submitting ? null : _handleSubmit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.fanta,
                  foregroundColor: Colors.white,
                  elevation: 2,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                child: _submitting
                    ? const SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(
                            strokeWidth: 2.5, color: Colors.white),
                      )
                    : const Text(
                        'Submit Discussion Video',
                        style: TextStyle(
                            fontSize: 15, fontWeight: FontWeight.bold),
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
