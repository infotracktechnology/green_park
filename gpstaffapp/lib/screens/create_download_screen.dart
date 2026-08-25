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

class CreateDownloadScreen extends StatefulWidget {
  const CreateDownloadScreen({super.key});

  @override
  State<CreateDownloadScreen> createState() => _CreateDownloadScreenState();
}

class _CreateDownloadScreenState extends State<CreateDownloadScreen> {
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
      final result = await FilePicker.platform.pickFiles(
        allowMultiple: true,
        type: FileType.any,
      );
      if (result != null && result.files.isNotEmpty) {
        setState(() => _files.addAll(result.files));
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

  String _formatFileSize(int bytes) {
    if (bytes < 1024) return '$bytes B';
    if (bytes < 1024 * 1024) return '${(bytes / 1024).toStringAsFixed(1)} KB';
    return '${(bytes / (1024 * 1024)).toStringAsFixed(1)} MB';
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
            onSurface: AppColors.textPrimary,
          ),
        ),
        child: child!,
      ),
    );

    if (pickedDate != null && mounted) {
      final pickedTime = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.fromDateTime(_startAt),
        builder: (context, child) => Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: Colors.white,
              onSurface: AppColors.textPrimary,
            ),
          ),
          child: child!,
        ),
      );

      if (pickedTime != null && mounted) {
        setState(() {
          _startAt = DateTime(pickedDate.year, pickedDate.month, pickedDate.day,
              pickedTime.hour, pickedTime.minute);
        });
      }
    }
  }

  Future<void> _handleSubmit() async {
    final filters =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    final title = _titleController.text.trim();

    if (title.isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter a title.');
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
        final dateStr = DateFormat('yyyy-MM-dd HH:mm:00').format(_startAt);
        formData.fields.add(MapEntry('start_at', dateStr));
      }

      for (var file in _files) {
        if (!kIsWeb && file.path != null) {
          formData.files.add(
            MapEntry(
              'file[]',
              await MultipartFile.fromFile(file.path!, filename: file.name),
            ),
          );
        } else if (file.bytes != null) {
          formData.files.add(
            MapEntry(
              'file[]',
              MultipartFile.fromBytes(file.bytes!, filename: file.name),
            ),
          );
        }
      }

      final res = await dioPost(formData);

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
              content: const Text('Download created successfully!'),
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

  Future<Response> dioPost(FormData formData) {
    return ApiClient().dio.post('/admin/download',
        data: formData, options: Options(contentType: 'multipart/form-data'));
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
        appBar: AppBar(title: const Text('Add Download')),
        body: const Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(color: AppColors.fanta),
              SizedBox(height: 12),
              Text('Loading master data...',
                  style:
                      TextStyle(fontSize: 12, color: AppColors.textSecondary)),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Add Download')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
        child: Column(
          children: [
            _buildAudienceCard(filters),
            const SizedBox(height: 16),
            _buildDetailsCard(filters),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton.icon(
                onPressed: _submitting ? null : _handleSubmit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16)),
                ),
                icon: _submitting
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white))
                    : const Icon(Icons.cloud_upload_outlined, size: 20),
                label: Text(_submitting ? 'Saving...' : 'Save Download',
                    style: const TextStyle(
                        fontSize: 14, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _card({required Widget child}) {
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
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: child,
    );
  }

  Widget _sectionTitle(String title, IconData icon, Color color) {
    return Row(
      children: [
        Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, size: 16, color: color),
        ),
        const SizedBox(width: 10),
        Text(title,
            style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w900,
                color: AppColors.textPrimary,
                letterSpacing: 0.8)),
      ],
    );
  }

  Widget _label(String text) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(text,
            style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: AppColors.textSecondary)),
        const SizedBox(height: 8),
      ],
    );
  }

  Widget _box({required Widget child}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: child,
    );
  }

  Widget _dropdown<T>({
    required String value,
    required List<DropdownMenuItem<String>> items,
    required Function(String?) onChanged,
  }) {
    return _box(
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: value.isEmpty ? null : value,
          isExpanded: true,
          hint: const Text('Select', style: TextStyle(fontSize: 14)),
          icon:
              const Icon(Icons.keyboard_arrow_down, color: AppColors.textMuted),
          dropdownColor: Colors.white,
          items: items,
          onChanged: (v) => onChanged(v),
        ),
      ),
    );
  }

  Widget _buildAudienceCard(AnnouncementFilterProvider filters) {
    return _card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionTitle('TARGET AUDIENCE', Icons.tune, AppColors.primary),
          const SizedBox(height: 20),
          _label('ACADEMIC YEAR'),
          _box(
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
                        color: AppColors.textPrimary)),
                const Icon(Icons.lock_outline,
                    size: 18, color: AppColors.textMuted),
              ],
            ),
          ),
          const SizedBox(height: 16),
          _label('USER TYPE *'),
          Row(
            children: [
              Expanded(
                  child: _userTypeButton(
                      filters, 'GROUP', Icons.people, 'Group Broadcast')),
              const SizedBox(width: 10),
              Expanded(
                  child: _userTypeButton(
                      filters, 'INDIVIDUAL', Icons.person, 'Individual')),
            ],
          ),
          const SizedBox(height: 16),
          _label('COURSE *'),
          _dropdown(
            value: filters.course,
            items: (filters.master?.courses ?? [])
                .map((c) => DropdownMenuItem(
                    value: c,
                    child: Text(c, style: const TextStyle(fontSize: 14))))
                .toList(),
            onChanged: (v) => v != null ? filters.setCourse(v) : null,
          ),
          const SizedBox(height: 16),
          MultiSelectChips<BranchItem>(
            label: 'Branch *',
            options: filters.availableBranches,
            selected: filters.branches,
            onToggle: (b) => filters.toggleBranch(b),
            labelBuilder: (b) => b.name,
            valueBuilder: (b) => b.id,
          ),
          MultiSelectChips<String>(
            label: 'Coaching Type *',
            options: filters.availableCoachingTypes,
            selected: filters.coachingTypes,
            onToggle: (t) => filters.toggleCoachingType(t),
          ),
          if (filters.showCategory)
            MultiSelectChips<String>(
              label: 'H/D',
              options: const ['HOSTEL', 'DAYSCHOLAR'],
              selected: filters.category,
              onToggle: (c) => filters.toggleCategory(c),
            ),
          if (filters.showBatch)
            MultiSelectChips<String>(
              label: 'Batch',
              options: filters.master?.batches ?? [],
              selected: filters.batch,
              onToggle: (b) => filters.toggleBatch(b),
            ),
          if (filters.showGender) ...[
            _label('GENDER *'),
            _dropdown(
              value: filters.gender == 'All' ? '' : filters.gender,
              items: [
                const DropdownMenuItem(
                    value: '',
                    child: Text('All Gender', style: TextStyle(fontSize: 14))),
                ...['MALE', 'FEMALE'].map((g) => DropdownMenuItem(
                    value: g,
                    child: Text(g, style: const TextStyle(fontSize: 14)))),
              ],
              onChanged: (v) => filters.setGender(v!.isEmpty ? 'All' : v),
            ),
            const SizedBox(height: 16),
          ],
          if (filters.usertype == 'INDIVIDUAL')
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
            )
          else if (filters.showSection &&
              filters.sectionOptions.isNotEmpty) ...[
            _label('SECTION'),
            _dropdown(
              value: filters.section,
              items: filters.sectionOptions
                  .map((s) => DropdownMenuItem(
                      value: s,
                      child: Text(s, style: const TextStyle(fontSize: 14))))
                  .toList(),
              onChanged: (v) => filters.setSection(v ?? ''),
            ),
            const SizedBox(height: 16),
          ],
        ],
      ),
    );
  }

  Widget _userTypeButton(AnnouncementFilterProvider filters, String type,
      IconData icon, String text) {
    final isSelected = filters.usertype == type;
    return InkWell(
      onTap: () => filters.setUsertype(type),
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.primary : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
              color: isSelected ? AppColors.primary : AppColors.border),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon,
                size: 16,
                color: isSelected ? Colors.white : AppColors.textSecondary),
            const SizedBox(width: 8),
            Text(text,
                style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color:
                        isSelected ? Colors.white : AppColors.textSecondary)),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailsCard(AnnouncementFilterProvider filters) {
    return _card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionTitle('DOWNLOAD DETAILS', Icons.edit_note, AppColors.fanta),
          const SizedBox(height: 20),
          _label('TITLE *'),
          TextField(
            controller: _titleController,
            style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary),
            decoration: InputDecoration(
              hintText: 'Enter download title',
              hintStyle:
                  const TextStyle(fontSize: 13, color: AppColors.textMuted),
              filled: true,
              fillColor: const Color(0xFFF8FAFC),
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: AppColors.border)),
              focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: AppColors.primary)),
            ),
          ),
          const SizedBox(height: 16),

          // Schedule toggle
          InkWell(
            onTap: () => setState(() => _isSchedule = !_isSchedule),
            borderRadius: BorderRadius.circular(12),
            child: Row(
              children: [
                Container(
                  width: 22,
                  height: 22,
                  decoration: BoxDecoration(
                    color: _isSchedule ? AppColors.primary : Colors.white,
                    borderRadius: BorderRadius.circular(6),
                    border: Border.all(
                        color:
                            _isSchedule ? AppColors.primary : AppColors.border,
                        width: 1.5),
                  ),
                  child: _isSchedule
                      ? const Icon(Icons.check, size: 15, color: Colors.white)
                      : null,
                ),
                const SizedBox(width: 10),
                const Text('Is Schedule (Publish later)',
                    style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary)),
              ],
            ),
          ),
          if (_isSchedule) ...[
            const SizedBox(height: 16),
            _label('START DATETIME *'),
            InkWell(
              onTap: _selectDateTime,
              borderRadius: BorderRadius.circular(16),
              child: _box(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      DateFormat('dd MMM yyyy, hh:mm a').format(_startAt),
                      style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textPrimary),
                    ),
                    const Icon(Icons.calendar_today_outlined,
                        size: 18, color: AppColors.textMuted),
                  ],
                ),
              ),
            ),
          ],
          const SizedBox(height: 16),

          // Files
          _label('ATTACHMENTS (PDF files)'),
          ..._files.asMap().entries.map((entry) {
            final index = entry.key;
            final file = entry.value;
            return Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.04),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppColors.primary.withOpacity(0.2)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.attach_file,
                        color: AppColors.primary, size: 20),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(file.name,
                              style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.primary),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis),
                          Text(_formatFileSize(file.size),
                              style: const TextStyle(
                                  fontSize: 10, color: AppColors.textMuted)),
                        ],
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close,
                          color: AppColors.error, size: 18),
                      onPressed: () => setState(() => _files.removeAt(index)),
                    ),
                  ],
                ),
              ),
            );
          }),
          InkWell(
            onTap: _pickFiles,
            borderRadius: BorderRadius.circular(16),
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 14),
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.05),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                  color: AppColors.primary.withOpacity(0.4),
                  style: BorderStyle.solid,
                ),
              ),
              child: const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.cloud_upload_outlined,
                      color: AppColors.primary, size: 20),
                  SizedBox(width: 8),
                  Text('Pick Files',
                      style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
