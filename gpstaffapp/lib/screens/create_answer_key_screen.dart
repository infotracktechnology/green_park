import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import 'package:intl/intl.dart';
import 'package:dio/dio.dart';
import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import '../widgets/multi_select_chips.dart';
import '../widgets/student_selector.dart';

class CreateAnswerKeyScreen extends StatefulWidget {
  const CreateAnswerKeyScreen({super.key});
  @override
  State<CreateAnswerKeyScreen> createState() => _CreateAnswerKeyScreenState();
}

class _CreateAnswerKeyScreenState extends State<CreateAnswerKeyScreen> {
  final TextEditingController _titleController = TextEditingController();
  bool _isSchedule = false;
  DateTime _startAt = DateTime.now().add(const Duration(hours: 1));
  final List<PlatformFile> _files = [];
  bool _submitting = false;

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
    super.dispose();
  }

  Future<void> _pickFiles() async {
    try {
      final r = await FilePicker.platform
          .pickFiles(allowMultiple: true, type: FileType.any);
      if (r != null && r.files.isNotEmpty) {
        setState(() => _files.addAll(r.files));
      }
    } catch (e) {
      debugPrint('File picker error: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
            content: Text('Failed to pick files'),
            backgroundColor: AppColors.error));
      }
    }
  }

  void _removeFile(int i) => setState(() => _files.removeAt(i));
  String _formatFileSize(int b) {
    if (b < 1024) return '$b B';
    if (b < 1024 * 1024) return '${(b / 1024).toStringAsFixed(1)} KB';
    return '${(b / (1024 * 1024)).toStringAsFixed(1)} MB';
  }

  Future<void> _selectDateTime() async {
    final pickedDate = await showDatePicker(
        context: context,
        initialDate: _startAt,
        firstDate: DateTime.now(),
        lastDate: DateTime.now().add(const Duration(days: 365)),
        builder: (context, child) => Theme(
            data: Theme.of(context).copyWith(
                colorScheme: const ColorScheme.light(
                    primary: AppColors.primary,
                    onPrimary: Colors.white,
                    onSurface: AppColors.textPrimary)),
            child: child!));
    if (pickedDate != null && mounted) {
      final pickedTime = await showTimePicker(
          context: context,
          initialTime: TimeOfDay.fromDateTime(_startAt),
          builder: (context, child) => Theme(
              data: Theme.of(context).copyWith(
                  colorScheme: const ColorScheme.light(
                      primary: AppColors.primary,
                      onPrimary: Colors.white,
                      onSurface: AppColors.textPrimary)),
              child: child!));
      if (pickedTime != null && mounted) {
        setState(() => _startAt = DateTime(pickedDate.year, pickedDate.month,
            pickedDate.day, pickedTime.hour, pickedTime.minute));
      }
    }
  }

  Future<void> _handleSubmit() async {
    final filters =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    final title = _titleController.text.trim();
    if (title.isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter answer key title.');
      return;
    }
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
    if (_files.isEmpty) {
      _showErrorDialog('Validation Error', 'Please attach at least one file.');
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
      formData.fields.add(MapEntry('title', title));
      if (_isSchedule) {
        formData.fields.add(const MapEntry('is_schedule', '1'));
        formData.fields.add(MapEntry(
            'start_at', DateFormat('yyyy-MM-dd HH:mm:00').format(_startAt)));
      }
      for (var file in _files) {
        if (!kIsWeb && file.path != null) {
          formData.files.add(MapEntry('file[]',
              await MultipartFile.fromFile(file.path!, filename: file.name)));
        } else if (file.bytes != null) {
          formData.files.add(MapEntry('file[]',
              MultipartFile.fromBytes(file.bytes!, filename: file.name)));
        }
      }
      final res = await ApiClient().dio.post('/admin/answerkey',
          data: formData, options: Options(contentType: 'multipart/form-data'));
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
                              fontWeight: FontWeight.bold,
                              color: AppColors.primary)),
                      content: const Text('Answer key created successfully!'),
                      actions: [
                        TextButton(
                            onPressed: () {
                              Navigator.pop(ctx);
                              Navigator.pop(context, true);
                            },
                            child: const Text('OK',
                                style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: AppColors.primary)))
                      ]));
        } else {
          _showErrorDialog(
              'Error',
              (res.data is Map ? res.data['message'] : null)?.toString() ??
                  'Creation failed');
        }
      }
    } on DioException catch (e) {
      String m = 'Submission failed';
      if (e.response?.data != null && e.response?.data is Map) {
        m = e.response?.data['message']?.toString() ?? m;
      }
      _showErrorDialog('Error', m);
    } catch (e) {
      _showErrorDialog('Error', e.toString());
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  void _showErrorDialog(String t, String m) {
    if (!mounted) return;
    showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20)),
                title: Text(t,
                    style: const TextStyle(fontWeight: FontWeight.bold)),
                content: Text(m),
                actions: [
                  TextButton(
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text('OK',
                          style: TextStyle(
                              fontWeight: FontWeight.bold,
                              color: AppColors.primary)))
                ]));
  }

  @override
  Widget build(BuildContext context) {
    final filters = Provider.of<AnnouncementFilterProvider>(context);
    if (filters.loading) {
      return Scaffold(
          backgroundColor: AppColors.background,
          appBar: AppBar(title: const Text('Add Answer Key')),
          body: const Center(
              child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                CircularProgressIndicator(color: AppColors.fanta),
                SizedBox(height: 12),
                Text('Loading master data...',
                    style:
                        TextStyle(fontSize: 12, color: AppColors.textSecondary))
              ])));
    }
    return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: const Text('Add Answer Key')),
        body: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
            child: Column(children: [
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
                            offset: const Offset(0, 2))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32,
                              height: 32,
                              decoration: BoxDecoration(
                                  color: AppColors.primary.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.tune,
                                  size: 16, color: AppColors.primary)),
                          const SizedBox(width: 10),
                          const Text('TARGET AUDIENCE',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w900,
                                  color: AppColors.textPrimary,
                                  letterSpacing: 0.8))
                        ]),
                        const SizedBox(height: 20),
                        const Text('ACADEMIC YEAR',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 14),
                            decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: AppColors.border)),
                            child: Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                      filters.academicYear.isNotEmpty
                                          ? filters.academicYear
                                          : 'Active Year',
                                      style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w600,
                                          color: AppColors.textPrimary)),
                                  const Icon(Icons.lock_outline,
                                      size: 18, color: AppColors.textMuted)
                                ])),
                        const SizedBox(height: 16),
                        const Text('USER TYPE *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        Row(children: [
                          Expanded(
                              child: InkWell(
                                  onTap: () => filters.setUsertype('GROUP'),
                                  borderRadius: BorderRadius.circular(16),
                                  child: Container(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 12),
                                      decoration: BoxDecoration(
                                          color: filters.usertype == 'GROUP'
                                              ? AppColors.primary
                                              : const Color(0xFFF8FAFC),
                                          borderRadius:
                                              BorderRadius.circular(16),
                                          border: Border.all(
                                              color: filters.usertype == 'GROUP'
                                                  ? AppColors.primary
                                                  : AppColors.border)),
                                      child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.people,
                                                size: 16,
                                                color: filters.usertype ==
                                                        'GROUP'
                                                    ? Colors.white
                                                    : AppColors.textSecondary),
                                            const SizedBox(width: 8),
                                            Text('Group Broadcast',
                                                style: TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: filters.usertype ==
                                                            'GROUP'
                                                        ? Colors.white
                                                        : AppColors
                                                            .textSecondary))
                                          ])))),
                          const SizedBox(width: 10),
                          Expanded(
                              child: InkWell(
                                  onTap: () =>
                                      filters.setUsertype('INDIVIDUAL'),
                                  borderRadius: BorderRadius.circular(16),
                                  child: Container(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 12),
                                      decoration: BoxDecoration(
                                          color:
                                              filters.usertype == 'INDIVIDUAL'
                                                  ? AppColors.primary
                                                  : const Color(0xFFF8FAFC),
                                          borderRadius:
                                              BorderRadius.circular(16),
                                          border: Border.all(
                                              color: filters.usertype ==
                                                      'INDIVIDUAL'
                                                  ? AppColors.primary
                                                  : AppColors.border)),
                                      child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.person,
                                                size: 16,
                                                color: filters.usertype ==
                                                        'INDIVIDUAL'
                                                    ? Colors.white
                                                    : AppColors.textSecondary),
                                            const SizedBox(width: 8),
                                            Text('Individual Student',
                                                style: TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: filters.usertype ==
                                                            'INDIVIDUAL'
                                                        ? Colors.white
                                                        : AppColors
                                                            .textSecondary))
                                          ]))))
                        ]),
                        const SizedBox(height: 16),
                        const Text('COURSE *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            physics: const BouncingScrollPhysics(),
                            child: Row(
                                children:
                                    (filters.master?.courses ?? []).map((c) {
                              final active = filters.course == c;
                              return Padding(
                                  padding: const EdgeInsets.only(right: 8),
                                  child: ChoiceChip(
                                      label: Text(c),
                                      labelStyle: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: active
                                              ? Colors.white
                                              : AppColors.textPrimary),
                                      selected: active,
                                      selectedColor: AppColors.primary,
                                      backgroundColor: const Color(0xFFF8FAFC),
                                      side: BorderSide(
                                          color: active
                                              ? AppColors.primary
                                              : AppColors.border),
                                      shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(20)),
                                      onSelected: (_) => filters.setCourse(c)));
                            }).toList())),
                        const SizedBox(height: 16),
                        MultiSelectChips<BranchItem>(
                            label: 'Branches *',
                            options: filters.availableBranches,
                            selected: filters.branches,
                            labelBuilder: (b) => b.name,
                            valueBuilder: (b) => b.id,
                            onToggle: (val) => filters.toggleBranch(val)),
                        MultiSelectChips<String>(
                            label: 'Coaching Type',
                            options: filters.availableCoachingTypes,
                            selected: filters.coachingTypes,
                            onToggle: (val) => filters.toggleCoachingType(val)),
                        if (filters.showCategory)
                          MultiSelectChips<String>(
                              label: 'H/D (Category)',
                              options: filters.master?.hostels ?? [],
                              selected: filters.category,
                              onToggle: (val) => filters.toggleCategory(val)),
                        if (filters.showBatch)
                          MultiSelectChips<String>(
                              label: 'Batch',
                              options: filters.master?.batches ?? [],
                              selected: filters.batch,
                              onToggle: (val) => filters.toggleBatch(val)),
                        if (filters.showGender) ...[
                          const Text('GENDER',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
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
                                        color: active
                                            ? Colors.white
                                            : AppColors.textPrimary),
                                    selected: active,
                                    selectedColor: AppColors.primary,
                                    backgroundColor: const Color(0xFFF8FAFC),
                                    side: BorderSide(
                                        color: active
                                            ? AppColors.primary
                                            : AppColors.border),
                                    shape: RoundedRectangleBorder(
                                        borderRadius:
                                            BorderRadius.circular(20)),
                                    onSelected: (_) => filters.setGender(g)));
                          }).toList()),
                          const SizedBox(height: 16)
                        ],
                        if (filters.usertype == 'GROUP' &&
                            filters.showSection) ...[
                          const Text('SECTION',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          SingleChildScrollView(
                              scrollDirection: Axis.horizontal,
                              physics: const BouncingScrollPhysics(),
                              child: Row(children: [
                                Padding(
                                    padding: const EdgeInsets.only(right: 8),
                                    child: ChoiceChip(
                                        label: const Text('All Sections'),
                                        labelStyle: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: filters.section.isEmpty
                                                ? Colors.white
                                                : AppColors.textPrimary),
                                        selected: filters.section.isEmpty,
                                        selectedColor: AppColors.primary,
                                        backgroundColor:
                                            const Color(0xFFF8FAFC),
                                        side: BorderSide(
                                            color: filters.section.isEmpty
                                                ? AppColors.primary
                                                : AppColors.border),
                                        shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(20)),
                                        onSelected: (_) =>
                                            filters.setSection(''))),
                                ...filters.sectionOptions.map((sec) => Padding(
                                    padding: const EdgeInsets.only(right: 8),
                                    child: ChoiceChip(
                                        label: Text(sec),
                                        labelStyle: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: filters.section == sec
                                                ? Colors.white
                                                : AppColors.textPrimary),
                                        selected: filters.section == sec,
                                        selectedColor: AppColors.primary,
                                        backgroundColor:
                                            const Color(0xFFF8FAFC),
                                        side: BorderSide(
                                            color: filters.section == sec
                                                ? AppColors.primary
                                                : AppColors.border),
                                        shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(20)),
                                        onSelected: (_) =>
                                            filters.setSection(sec))))
                              ]))
                        ],
                        if (filters.usertype == 'GROUP' && filters.showSection)
                          const SizedBox(height: 16),
                        if (filters.showStudent)
                          StudentSelector(
                              selectedStudentId: filters.student,
                              onSelectStudent: (id) => filters.setStudent(id),
                              studentOptions: filters.studentOptions,
                              loading: filters.studentLoading,
                              searchValue: filters.studentSearch,
                              onSearchChanged: (s) =>
                                  filters.setStudentSearch(s),
                              onSearch: (s) => filters.fetchStudents(s),
                              sectionOptions: filters.sectionOptions,
                              selectedSection: filters.section,
                              onSelectSection: (sec) =>
                                  filters.setSection(sec)),
                      ])),
              const SizedBox(height: 16),
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
                            offset: const Offset(0, 2))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32,
                              height: 32,
                              decoration: BoxDecoration(
                                  color: AppColors.fanta.withOpacity(0.12),
                                  borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.fact_check_outlined,
                                  size: 20, color: AppColors.fanta)),
                          const SizedBox(width: 10),
                          const Text('ANSWER KEY DETAILS',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w900,
                                  color: AppColors.textPrimary,
                                  letterSpacing: 0.8))
                        ]),
                        const SizedBox(height: 20),
                        const Text('TITLE *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        TextField(
                            controller: _titleController,
                            style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary),
                            decoration: const InputDecoration(
                                hintText: 'Enter answer key title')),
                        const SizedBox(height: 16),
                        const Text('ATTACHMENTS *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        if (_files.isNotEmpty)
                          ListView.separated(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: _files.length,
                              separatorBuilder: (_, __) =>
                                  const SizedBox(height: 6),
                              itemBuilder: (context, i) {
                                final f = _files[i];
                                return Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 12, vertical: 10),
                                    decoration: BoxDecoration(
                                        color: const Color(0xFFF8FAFC),
                                        borderRadius: BorderRadius.circular(14),
                                        border: Border.all(
                                            color: AppColors.border)),
                                    child: Row(children: [
                                      const Icon(Icons.description_outlined,
                                          color: AppColors.primary, size: 20),
                                      const SizedBox(width: 10),
                                      Expanded(
                                          child: Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                            Text(f.name,
                                                style: const TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color:
                                                        AppColors.textPrimary),
                                                maxLines: 1,
                                                overflow:
                                                    TextOverflow.ellipsis),
                                            Text(_formatFileSize(f.size),
                                                style: const TextStyle(
                                                    fontSize: 10,
                                                    color: AppColors.textMuted))
                                          ])),
                                      IconButton(
                                          icon: const Icon(Icons.close,
                                              color: AppColors.error, size: 18),
                                          onPressed: () => _removeFile(i))
                                    ]));
                              }),
                        const SizedBox(height: 10),
                        InkWell(
                            onTap: _pickFiles,
                            borderRadius: BorderRadius.circular(16),
                            child: Container(
                                width: double.infinity,
                                padding:
                                    const EdgeInsets.symmetric(vertical: 14),
                                decoration: BoxDecoration(
                                    color: AppColors.primary.withOpacity(0.05),
                                    borderRadius: BorderRadius.circular(16),
                                    border: Border.all(
                                        color: AppColors.primary
                                            .withOpacity(0.4))),
                                child: const Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.cloud_upload_outlined,
                                          color: AppColors.primary, size: 20),
                                      SizedBox(width: 8),
                                      Text('+ Select Files to Attach',
                                          style: TextStyle(
                                              fontSize: 13,
                                              fontWeight: FontWeight.bold,
                                              color: AppColors.primary))
                                    ]))),
                        const SizedBox(height: 16),
                        const Divider(height: 1, color: AppColors.borderLight),
                        const SizedBox(height: 14),
                        Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Expanded(
                                  child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                    Text('Schedule Publish',
                                        style: TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: AppColors.textPrimary)),
                                    Text(
                                        'Publish at a specific future date & time',
                                        style: TextStyle(
                                            fontSize: 11,
                                            color: AppColors.textMuted))
                                  ])),
                              Switch(
                                  value: _isSchedule,
                                  activeColor: AppColors.fanta,
                                  onChanged: (v) =>
                                      setState(() => _isSchedule = v))
                            ]),
                        if (_isSchedule) ...[
                          const SizedBox(height: 14),
                          const Text('PUBLISH DATE & TIME',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          InkWell(
                              onTap: _selectDateTime,
                              borderRadius: BorderRadius.circular(16),
                              child: Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 16, vertical: 14),
                                  decoration: BoxDecoration(
                                      color: const Color(0xFFF8FAFC),
                                      borderRadius: BorderRadius.circular(16),
                                      border:
                                          Border.all(color: AppColors.border)),
                                  child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Row(children: [
                                          const Icon(Icons.calendar_today,
                                              size: 18, color: AppColors.fanta),
                                          const SizedBox(width: 10),
                                          Text(
                                              DateFormat('dd MMM yyyy, hh:mm a')
                                                  .format(_startAt),
                                              style: const TextStyle(
                                                  fontSize: 14,
                                                  fontWeight: FontWeight.w600,
                                                  color: AppColors.textPrimary))
                                        ]),
                                        const Text('Change',
                                            style: TextStyle(
                                                fontSize: 12,
                                                fontWeight: FontWeight.bold,
                                                color: AppColors.primary))
                                      ])))
                        ]
                      ])),
              const SizedBox(height: 24),
              SizedBox(
                  width: double.infinity,
                  height: 54,
                  child: ElevatedButton(
                      onPressed: _submitting ? null : _handleSubmit,
                      style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.fanta,
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(18)),
                          elevation: 4,
                          shadowColor: AppColors.fanta.withOpacity(0.4)),
                      child: _submitting
                          ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                  color: Colors.white, strokeWidth: 2.5))
                          : const Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                  Icon(Icons.fact_check,
                                      color: Colors.white, size: 22),
                                  SizedBox(width: 8),
                                  Text('Publish Answer Key',
                                      style: TextStyle(
                                          fontSize: 16,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.white))
                                ])))
            ])));
  }
}
