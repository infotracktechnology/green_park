import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import 'package:intl/intl.dart';
import 'package:dio/dio.dart';
import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../models/answer_key_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import '../widgets/multi_select_chips.dart';
import '../widgets/student_selector.dart';

class EditAnswerKeyScreen extends StatefulWidget {
  final dynamic keyId;
  const EditAnswerKeyScreen({super.key, required this.keyId});
  @override
  State<EditAnswerKeyScreen> createState() => _EditAnswerKeyScreenState();
}

class _EditAnswerKeyScreenState extends State<EditAnswerKeyScreen> {
  final TextEditingController _titleController = TextEditingController();
  bool _isSchedule = false;
  DateTime _startAt = DateTime.now();
  final List<PlatformFile> _newFiles = [];
  List<String> _existingFiles = [];
  bool _fetching = true;
  bool _submitting = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadData());
  }

  @override
  void dispose() {
    _titleController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() => _fetching = true);
    final filters =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    await filters.fetchMasterData();
    try {
      final res =
          await ApiClient().dio.get('/admin/answerkey/${widget.keyId}/edit');
      if (res.data != null && res.data['status'] == true) {
        final data = res.data['answerkey'] as Map<String, dynamic>;
        final model = AnswerKeyModel.fromJson(data);
        final c = Map<String, dynamic>.from(data);
        if (res.data['students'] != null && res.data['students'] is Map) {
          c['studentOptions'] = res.data['students'];
        }
        if (res.data['section'] != null && res.data['section'] is List) {
          c['sectionOptions'] = res.data['section'];
        }
        filters.setAllFilters(c);
        _titleController.text = model.title;
        _isSchedule = model.isSchedule == 1;
        if (model.startAt != null && model.startAt!.isNotEmpty) {
          try {
            _startAt = DateTime.parse(model.startAt!);
          } catch (_) {}
        }
        _existingFiles = List<String>.from(model.files);
      }
    } catch (e) {
      debugPrint('Edit answer key fetch error: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
            content: Text('Failed to load answer key details'),
            backgroundColor: AppColors.error));
      }
    } finally {
      if (mounted) setState(() => _fetching = false);
    }
  }

  Future<void> _pickFiles() async {
    try {
      final r = await FilePicker.platform
          .pickFiles(allowMultiple: true, type: FileType.any);
      if (r != null && r.files.isNotEmpty) {
        setState(() => _newFiles.addAll(r.files));
      }
    } catch (e) {
      debugPrint('File picker error: $e');
    }
  }

  void _removeNew(int i) => setState(() => _newFiles.removeAt(i));
  void _removeExisting(int i) => setState(() => _existingFiles.removeAt(i));
  String _formatFileSize(int b) {
    if (b < 1024) return '$b B';
    if (b < 1024 * 1024) return '${(b / 1024).toStringAsFixed(1)} KB';
    return '${(b / (1024 * 1024)).toStringAsFixed(1)} MB';
  }

  Future<void> _selectDateTime() async {
    final d = await showDatePicker(
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
    if (d != null && mounted) {
      final t = await showTimePicker(
          context: context,
          initialTime: TimeOfDay.fromDateTime(_startAt),
          builder: (context, child) => Theme(
              data: Theme.of(context).copyWith(
                  colorScheme: const ColorScheme.light(
                      primary: AppColors.primary,
                      onPrimary: Colors.white,
                      onSurface: AppColors.textPrimary)),
              child: child!));
      if (t != null && mounted) {
        setState(() =>
            _startAt = DateTime(d.year, d.month, d.day, t.hour, t.minute));
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
    setState(() => _submitting = true);
    try {
      final formData = FormData();
      formData.fields.add(const MapEntry('_method', 'PUT'));
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
      formData.fields.add(MapEntry('gender', filters.gender));
      if (filters.usertype == 'INDIVIDUAL') {
        formData.fields.add(MapEntry('students', filters.student));
      } else {
        formData.fields.add(MapEntry('section', filters.section));
      }
      formData.fields.add(MapEntry('title', title));
      if (_isSchedule) {
        formData.fields.add(const MapEntry('is_schedule', '1'));
        formData.fields.add(MapEntry(
            'start_at', DateFormat('yyyy-MM-dd HH:mm:00').format(_startAt)));
      }
      for (var f in _existingFiles) {
        formData.fields.add(MapEntry('existing_file[]', f));
      }
      for (var file in _newFiles) {
        if (!kIsWeb && file.path != null) {
          formData.files.add(MapEntry('file[]',
              await MultipartFile.fromFile(file.path!, filename: file.name)));
        } else if (file.bytes != null) {
          formData.files.add(MapEntry('file[]',
              MultipartFile.fromBytes(file.bytes!, filename: file.name)));
        }
      }
      final res = await ApiClient().dio.post(
          '/admin/answerkey/${widget.keyId}?_method=PUT',
          data: formData,
          options: Options(contentType: 'multipart/form-data'));
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
                      content: const Text('Answer key updated successfully!'),
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
                  'Update failed');
        }
      }
    } on DioException catch (e) {
      String m = 'Update failed';
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

  Widget _filterCard() {
    final f = Provider.of<AnnouncementFilterProvider>(context);
    return Container(
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
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10)),
                child:
                    const Icon(Icons.tune, size: 16, color: AppColors.primary)),
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
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppColors.border)),
              child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                        f.academicYear.isNotEmpty
                            ? f.academicYear
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
                    onTap: () => f.setUsertype('GROUP'),
                    borderRadius: BorderRadius.circular(16),
                    child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        decoration: BoxDecoration(
                            color: f.usertype == 'GROUP'
                                ? AppColors.primary
                                : const Color(0xFFF8FAFC),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(
                                color: f.usertype == 'GROUP'
                                    ? AppColors.primary
                                    : AppColors.border)),
                        child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.people,
                                  size: 16,
                                  color: f.usertype == 'GROUP'
                                      ? Colors.white
                                      : AppColors.textSecondary),
                              const SizedBox(width: 8),
                              Text('Group Broadcast',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: f.usertype == 'GROUP'
                                          ? Colors.white
                                          : AppColors.textSecondary))
                            ])))),
            const SizedBox(width: 10),
            Expanded(
                child: InkWell(
                    onTap: () => f.setUsertype('INDIVIDUAL'),
                    borderRadius: BorderRadius.circular(16),
                    child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        decoration: BoxDecoration(
                            color: f.usertype == 'INDIVIDUAL'
                                ? AppColors.primary
                                : const Color(0xFFF8FAFC),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(
                                color: f.usertype == 'INDIVIDUAL'
                                    ? AppColors.primary
                                    : AppColors.border)),
                        child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.person,
                                  size: 16,
                                  color: f.usertype == 'INDIVIDUAL'
                                      ? Colors.white
                                      : AppColors.textSecondary),
                              const SizedBox(width: 8),
                              Text('Individual Student',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: f.usertype == 'INDIVIDUAL'
                                          ? Colors.white
                                          : AppColors.textSecondary))
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
                  children: (f.master?.courses ?? []).map((c) {
                final a = f.course == c;
                return Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: ChoiceChip(
                        label: Text(c),
                        labelStyle: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: a ? Colors.white : AppColors.textPrimary),
                        selected: a,
                        selectedColor: AppColors.primary,
                        backgroundColor: const Color(0xFFF8FAFC),
                        side: BorderSide(
                            color: a ? AppColors.primary : AppColors.border),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(20)),
                        onSelected: (_) => f.setCourse(c)));
              }).toList())),
          const SizedBox(height: 16),
          MultiSelectChips<BranchItem>(
              label: 'Branches *',
              options: f.availableBranches,
              selected: f.branches,
              labelBuilder: (b) => b.name,
              valueBuilder: (b) => b.id,
              onToggle: (v) => f.toggleBranch(v)),
          MultiSelectChips<String>(
              label: 'Coaching Type',
              options: f.availableCoachingTypes,
              selected: f.coachingTypes,
              onToggle: (v) => f.toggleCoachingType(v)),
          if (f.showCategory)
            MultiSelectChips<String>(
                label: 'H/D (Category)',
                options: f.master?.hostels ?? [],
                selected: f.category,
                onToggle: (v) => f.toggleCategory(v)),
          if (f.showBatch)
            MultiSelectChips<String>(
                label: 'Batch',
                options: f.master?.batches ?? [],
                selected: f.batch,
                onToggle: (v) => f.toggleBatch(v)),
          if (f.showGender) ...[
            const Text('GENDER',
                style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textSecondary)),
            const SizedBox(height: 8),
            Row(
                children: ['All', 'MALE', 'FEMALE'].map((g) {
              final a = f.gender == g;
              return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                      label: Text(g == 'All' ? 'All Genders' : g),
                      labelStyle: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: a ? Colors.white : AppColors.textPrimary),
                      selected: a,
                      selectedColor: AppColors.primary,
                      backgroundColor: const Color(0xFFF8FAFC),
                      side: BorderSide(
                          color: a ? AppColors.primary : AppColors.border),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20)),
                      onSelected: (_) => f.setGender(g)));
            }).toList()),
            const SizedBox(height: 16)
          ],
          if (f.usertype == 'GROUP' && f.showSection) ...[
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
                              color: f.section.isEmpty
                                  ? Colors.white
                                  : AppColors.textPrimary),
                          selected: f.section.isEmpty,
                          selectedColor: AppColors.primary,
                          backgroundColor: const Color(0xFFF8FAFC),
                          side: BorderSide(
                              color: f.section.isEmpty
                                  ? AppColors.primary
                                  : AppColors.border),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20)),
                          onSelected: (_) => f.setSection(''))),
                  ...f.sectionOptions.map((s) => Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                          label: Text(s),
                          labelStyle: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: f.section == s
                                  ? Colors.white
                                  : AppColors.textPrimary),
                          selected: f.section == s,
                          selectedColor: AppColors.primary,
                          backgroundColor: const Color(0xFFF8FAFC),
                          side: BorderSide(
                              color: f.section == s
                                  ? AppColors.primary
                                  : AppColors.border),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20)),
                          onSelected: (_) => f.setSection(s))))
                ]))
          ],
          if (f.usertype == 'GROUP' && f.showSection)
            const SizedBox(height: 16),
          if (f.showStudent)
            StudentSelector(
                selectedStudentId: f.student,
                onSelectStudent: (id) => f.setStudent(id),
                studentOptions: f.studentOptions,
                loading: f.studentLoading,
                searchValue: f.studentSearch,
                onSearchChanged: (s) => f.setStudentSearch(s),
                onSearch: (s) => f.fetchStudents(s),
                sectionOptions: f.sectionOptions,
                selectedSection: f.section,
                onSelectSection: (sec) => f.setSection(sec)),
        ]));
  }

  @override
  Widget build(BuildContext context) {
    final filters = Provider.of<AnnouncementFilterProvider>(context);
    if (_fetching || filters.loading) {
      return Scaffold(
          backgroundColor: AppColors.background,
          appBar: AppBar(title: const Text('Edit Answer Key')),
          body: const Center(
              child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                CircularProgressIndicator(color: AppColors.fanta),
                SizedBox(height: 12),
                Text('Loading answer key...',
                    style:
                        TextStyle(fontSize: 12, color: AppColors.textSecondary))
              ])));
    }
    return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: const Text('Edit Answer Key')),
        body: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
            child: Column(children: [
              _filterCard(),
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
                        const Text('ATTACHMENTS',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        if (_existingFiles.isNotEmpty) ...[
                          Text('CURRENT SAVED FILES (${_existingFiles.length})',
                              style: const TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 6),
                          ListView.separated(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: _existingFiles.length,
                              separatorBuilder: (_, __) =>
                                  const SizedBox(height: 6),
                              itemBuilder: (context, i) {
                                final p = _existingFiles[i];
                                final n = p.split('/').last;
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
                                          child: Text(n,
                                              style: const TextStyle(
                                                  fontSize: 12,
                                                  fontWeight: FontWeight.w600,
                                                  color: AppColors.textPrimary),
                                              maxLines: 1,
                                              overflow: TextOverflow.ellipsis)),
                                      IconButton(
                                          icon: const Icon(Icons.close,
                                              color: AppColors.error, size: 18),
                                          onPressed: () => _removeExisting(i))
                                    ]));
                              }),
                          const SizedBox(height: 12)
                        ],
                        if (_newFiles.isNotEmpty) ...[
                          Text('NEW FILES TO UPLOAD (${_newFiles.length})',
                              style: const TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.primary)),
                          const SizedBox(height: 6),
                          ListView.separated(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: _newFiles.length,
                              separatorBuilder: (_, __) =>
                                  const SizedBox(height: 6),
                              itemBuilder: (context, i) {
                                final file = _newFiles[i];
                                return Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 12, vertical: 10),
                                    decoration: BoxDecoration(
                                        color:
                                            AppColors.primary.withOpacity(0.04),
                                        borderRadius: BorderRadius.circular(14),
                                        border: Border.all(
                                            color: AppColors.primary
                                                .withOpacity(0.2))),
                                    child: Row(children: [
                                      const Icon(Icons.attach_file,
                                          color: AppColors.primary, size: 20),
                                      const SizedBox(width: 10),
                                      Expanded(
                                          child: Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                            Text(file.name,
                                                style: const TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: AppColors.primary),
                                                maxLines: 1,
                                                overflow:
                                                    TextOverflow.ellipsis),
                                            Text(_formatFileSize(file.size),
                                                style: const TextStyle(
                                                    fontSize: 10,
                                                    color: AppColors.textMuted))
                                          ])),
                                      IconButton(
                                          icon: const Icon(Icons.close,
                                              color: AppColors.error, size: 18),
                                          onPressed: () => _removeNew(i))
                                    ]));
                              }),
                          const SizedBox(height: 12)
                        ],
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
                                  Icon(Icons.check_circle_outline,
                                      color: Colors.white, size: 22),
                                  SizedBox(width: 8),
                                  Text('Update Answer Key',
                                      style: TextStyle(
                                          fontSize: 16,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.white))
                                ])))
            ])));
  }
}
