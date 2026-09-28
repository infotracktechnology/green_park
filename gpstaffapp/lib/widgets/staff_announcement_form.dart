import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';
import 'package:intl/intl.dart';
import 'package:dio/dio.dart';

import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../models/staff_announcement_model.dart';
import '../theme/app_theme.dart';
import 'multi_select_chips.dart';

/// Builds the multipart payload and performs the create/update request.
typedef StaffAnnouncementSubmit = Future<Map<String, dynamic>?> Function(
    FormData formData);

/// Shared create / edit form for Staff Announcements.
///
/// When [announcementId] is provided the form loads the existing announcement
/// (with the [StaffAnnouncementController::edit] endpoint) and submits an
/// update, otherwise it creates a new announcement.
class StaffAnnouncementForm extends StatefulWidget {
  final dynamic announcementId;
  final StaffAnnouncementSubmit onSubmit;
  final String submitLabel;

  const StaffAnnouncementForm({
    super.key,
    this.announcementId,
    required this.onSubmit,
    this.submitLabel = 'Publish Announcement',
  });

  @override
  State<StaffAnnouncementForm> createState() => _StaffAnnouncementFormState();
}

class _StaffAnnouncementFormState extends State<StaffAnnouncementForm> {
  final TextEditingController _titleController = TextEditingController();
  final TextEditingController _contentController = TextEditingController();

  bool _loading = true;
  bool _loadFailed = false;
  bool _submitting = false;

  String _academicYear = '';
  List<BranchItem> _branches = [];
  List<String> _departments = [];
  List<StaffOption> _staffOptions = [];

  String _usertype = 'GROUP';
  List<String> _selectedBranches = [];
  List<String> _selectedDepartments = [];
  String _gender = 'All';
  List<String> _selectedStaff = [];
  bool _isSchedule = false;
  DateTime _startAt = DateTime.now().add(const Duration(hours: 1));
  List<String> _existingAttachments = [];
  final List<PlatformFile> _newAttachments = [];

  bool get _isEdit => widget.announcementId != null;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _titleController.dispose();
    _contentController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    try {
      final dio = ApiClient().dio;
      final res = await dio.get('/admin/staffannouncement/masterdata');

      if (res.data != null && res.data['status'] == true) {
        final data = res.data as Map;
        _academicYear = (data['academic_year'] ?? '').toString();
        _departments = (data['departments'] as List? ?? [])
            .map((e) => e.toString())
            .toList();
        _branches = (data['branches'] as List? ?? [])
            .map((e) => BranchItem.fromDynamic(e))
            .toList();
        _staffOptions = (data['staff'] as List? ?? [])
            .map((e) => StaffOption.fromJson(e))
            .toList();
      }

      if (_isEdit) {
        final editRes = await dio.get(
          '/admin/staffannouncement/${widget.announcementId}/edit',
        );

        if (editRes.data != null && editRes.data['status'] == true) {
          final raw = editRes.data['announcement'];
          if (raw is Map) {
            _applyAnnouncement(
              StaffAnnouncementModel.fromJson(Map<String, dynamic>.from(raw)),
            );
          }
        }
      }
    } catch (e) {
      debugPrint('Staff announcement form load error: $e');
      _loadFailed = true;
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _applyAnnouncement(StaffAnnouncementModel announcement) {
    _usertype = announcement.isIndividual ? 'INDIVIDUAL' : 'GROUP';
    _selectedBranches = announcement.branchIdList;
    _selectedDepartments = announcement.departmentList;
    _gender = (announcement.gender == null || announcement.gender!.isEmpty)
        ? 'All'
        : announcement.gender!;
    _selectedStaff = List<String>.from(announcement.staffIds);
    _isSchedule = announcement.isScheduled;
    _existingAttachments = List<String>.from(announcement.attachments);
    _titleController.text = announcement.title;
    _contentController.text = announcement.content ?? '';

    if ((announcement.academicYear ?? '').isNotEmpty) {
      _academicYear = announcement.academicYear!;
    }

    if (_isSchedule && (announcement.startAt ?? '').isNotEmpty) {
      _startAt = DateTime.tryParse(announcement.startAt!) ?? _startAt;
    }
  }

  List<StaffOption> get _availableStaff {
    final filtered = _staffOptions.where((staff) {
      final branchMatch = _selectedBranches.isEmpty ||
          _selectedBranches.contains(staff.branchId);
      final departmentMatch = _selectedDepartments.isEmpty ||
          _selectedDepartments.contains(staff.department);
      return branchMatch && departmentMatch;
    }).toList();

    // Always keep already selected staff visible (e.g. after switching filters)
    for (final username in _selectedStaff) {
      if (!filtered.any((staff) => staff.username == username)) {
        final match =
            _staffOptions.where((staff) => staff.username == username).toList();
        if (match.isNotEmpty) filtered.add(match.first);
      }
    }

    return filtered;
  }

  void _toggleBranch(dynamic value) {
    setState(() {
      final id = value.toString();
      if (_selectedBranches.contains(id)) {
        _selectedBranches.remove(id);
      } else {
        _selectedBranches.add(id);
      }
    });
  }

  void _toggleDepartment(dynamic value) {
    setState(() {
      final department = value.toString();
      if (_selectedDepartments.contains(department)) {
        _selectedDepartments.remove(department);
      } else {
        _selectedDepartments.add(department);
      }
    });
  }

  void _toggleStaff(String username) {
    setState(() {
      if (_selectedStaff.contains(username)) {
        _selectedStaff.remove(username);
      } else {
        _selectedStaff.add(username);
      }
    });
  }

  Future<void> _pickFiles() async {
    try {
      final result = await FilePicker.platform.pickFiles(
        allowMultiple: true,
        type: FileType.any,
      );

      if (result != null && result.files.isNotEmpty) {
        setState(() => _newAttachments.addAll(result.files));
      }
    } catch (e) {
      debugPrint('File picker error: $e');
      _showMessage('Failed to pick files', isError: true);
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
      firstDate: DateTime.now().subtract(const Duration(days: 1)),
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

    if (pickedDate == null || !mounted) return;

    final pickedTime = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.fromDateTime(_startAt),
    );

    if (pickedTime == null) return;

    setState(() {
      _startAt = DateTime(
        pickedDate.year,
        pickedDate.month,
        pickedDate.day,
        pickedTime.hour,
        pickedTime.minute,
      );
    });
  }

  void _showMessage(String message, {bool isError = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? AppColors.error : AppColors.success,
      ),
    );
  }

  Future<FormData> _buildFormData() async {
    final formData = FormData();

    if (_isEdit) {
      formData.fields.add(const MapEntry('_method', 'PUT'));
    }

    formData.fields.add(MapEntry('academic_year', _academicYear));
    formData.fields.add(MapEntry('usertype', _usertype));
    formData.fields.add(MapEntry('gender', _gender.isEmpty ? 'All' : _gender));
    formData.fields.add(MapEntry('title', _titleController.text.trim()));
    formData.fields.add(MapEntry('content', _contentController.text.trim()));

    for (final branch in _selectedBranches) {
      formData.fields.add(MapEntry('branch[]', branch));
    }
    for (final department in _selectedDepartments) {
      formData.fields.add(MapEntry('department[]', department));
    }
    if (_usertype == 'INDIVIDUAL') {
      for (final staff in _selectedStaff) {
        formData.fields.add(MapEntry('staff[]', staff));
      }
    }
    if (_isSchedule) {
      formData.fields.add(const MapEntry('is_schedule', '1'));
      formData.fields.add(MapEntry(
        'start_at',
        DateFormat('yyyy-MM-dd HH:mm:00').format(_startAt),
      ));
    }
    for (final path in _existingAttachments) {
      formData.fields.add(MapEntry('existing_attachment[]', path));
    }
    for (final file in _newAttachments) {
      if (!kIsWeb && file.path != null) {
        formData.files.add(MapEntry(
          'attachment[]',
          await MultipartFile.fromFile(file.path!, filename: file.name),
        ));
      } else if (file.bytes != null) {
        formData.files.add(MapEntry(
          'attachment[]',
          MultipartFile.fromBytes(file.bytes!, filename: file.name),
        ));
      }
    }

    return formData;
  }

  Future<void> _handleSubmit() async {
    if (_selectedBranches.isEmpty) {
      _showMessage('Please select at least one branch.', isError: true);
      return;
    }
    if (_selectedDepartments.isEmpty) {
      _showMessage('Please select at least one department.', isError: true);
      return;
    }
    if (_usertype == 'INDIVIDUAL' && _selectedStaff.isEmpty) {
      _showMessage('Please select at least one staff member.', isError: true);
      return;
    }
    if (_titleController.text.trim().isEmpty) {
      _showMessage('Please enter a title.', isError: true);
      return;
    }

    setState(() => _submitting = true);

    try {
      final data = await widget.onSubmit(await _buildFormData());

      if (!mounted) return;

      if (data != null && (data['status'] == true || data['status'] == 1)) {
        final message = (data['message'] ??
                (_isEdit
                    ? 'Staff Announcement updated successfully.'
                    : 'Staff Announcement created successfully.'))
            .toString();

        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (ctx) => AlertDialog(
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
            title: const Text('Success',
                style: TextStyle(
                    fontWeight: FontWeight.bold, color: AppColors.primary)),
            content: Text(message),
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
        final msg = (data?['message']) ??
            (_isEdit ? 'Update failed' : 'Creation failed');
        _showMessage(msg.toString(), isError: true);
      }
    } on DioException catch (e) {
      String msg = _isEdit ? 'Update failed' : 'Submission failed';
      if (e.response?.data != null && e.response?.data is Map) {
        msg = e.response?.data['message']?.toString() ?? msg;
      }
      _showMessage(msg, isError: true);
    } catch (e) {
      _showMessage(e.toString(), isError: true);
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  Widget _card(BuildContext context, List<Widget> children) {
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
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: children,
      ),
    );
  }

  Widget _sectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, size: 16, color: AppColors.primary),
        ),
        const SizedBox(width: 10),
        Text(
          title.toUpperCase(),
          style: const TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w900,
            color: AppColors.textPrimary,
            letterSpacing: 0.8,
          ),
        ),
      ],
    );
  }

  Widget _label(String text) {
    return Text(
      text,
      style: const TextStyle(
        fontSize: 11,
        fontWeight: FontWeight.bold,
        color: AppColors.textSecondary,
      ),
    );
  }

  Widget _plainChip(String text, {bool selected = false, VoidCallback? onTap}) {
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: Material(
        color: selected ? AppColors.primary : const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(20),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(20),
              border: Border.all(
                color: selected ? AppColors.primary : AppColors.border,
              ),
            ),
            child: Text(
              text,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: selected ? Colors.white : AppColors.textPrimary,
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _lockedAcademicYear() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            _academicYear.isNotEmpty ? _academicYear : 'Active Year',
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: AppColors.textPrimary,
            ),
          ),
          const Icon(Icons.lock_outline,
              size: 18, color: AppColors.textMuted),
        ],
      ),
    );
  }

  Widget _userTypeToggle() {
    return Row(
      children: [
        Expanded(
          child: _userTypeButton(
              'GROUP', 'Group Broadcast', Icons.people_outline),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _userTypeButton(
              'INDIVIDUAL', 'Individual Staff', Icons.person_outline),
        ),
      ],
    );
  }

  Widget _userTypeButton(String value, String text, IconData icon) {
    final selected = _usertype == value;

    return InkWell(
      onTap: () => setState(() => _usertype = value),
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: selected ? AppColors.primary : AppColors.border,
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon,
                size: 16,
                color: selected ? Colors.white : AppColors.textSecondary),
            const SizedBox(width: 8),
            Flexible(
              child: Text(
                text,
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: selected ? Colors.white : AppColors.textSecondary,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _horizontalChips(List<Widget> chips) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(children: chips),
    );
  }

  Widget _genderChips() {
    return _horizontalChips([
      _plainChip('All Gender',
          selected: _gender == 'All',
          onTap: () => setState(() => _gender = 'All')),
      _plainChip('MALE',
          selected: _gender == 'MALE',
          onTap: () => setState(() => _gender = 'MALE')),
      _plainChip('FEMALE',
          selected: _gender == 'FEMALE',
          onTap: () => setState(() => _gender = 'FEMALE')),
    ]);
  }

  Widget _staffPicker() {
    final available = _availableStaff;

    if (available.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        decoration: BoxDecoration(
          color: const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: const Text(
          'No staff found for the selected branch & department.',
          style: TextStyle(fontSize: 12, color: AppColors.textMuted),
        ),
      );
    }

    return Container(
      constraints: const BoxConstraints(maxHeight: 220),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: SingleChildScrollView(
        child: Wrap(
          spacing: 8,
          runSpacing: 8,
          children: available.map((staff) {
            final selected = _selectedStaff.contains(staff.username);

            return FilterChip(
              label: Text(
                staff.displayName,
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: selected ? Colors.white : AppColors.textPrimary,
                ),
              ),
              selected: selected,
              selectedColor: AppColors.primary,
              backgroundColor: Colors.white,
              checkmarkColor: Colors.white,
              side: BorderSide(
                color: selected ? AppColors.primary : AppColors.border,
              ),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
              onSelected: (_) => _toggleStaff(staff.username),
            );
          }).toList(),
        ),
      ),
    );
  }

  Widget _attachmentTile({
    required String name,
    required String subtitle,
    required VoidCallback? onRemove,
    bool isNew = false,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          Icon(
            isNew ? Icons.upload_file : Icons.description_outlined,
            color: AppColors.primary,
            size: 20,
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  subtitle,
                  style: const TextStyle(
                      fontSize: 10, color: AppColors.textMuted),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          if (onRemove != null)
            IconButton(
              onPressed: onRemove,
              icon: const Icon(Icons.close, size: 18, color: AppColors.error),
              visualDensity: VisualDensity.compact,
            ),
        ],
      ),
    );
  }

  Widget _attachmentsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        ..._existingAttachments.map(
          (path) => _attachmentTile(
            name: StaffAnnouncementModel.getAttachmentFileName(path),
            subtitle: path,
            onRemove: () => setState(() => _existingAttachments.remove(path)),
          ),
        ),
        ..._newAttachments.map(
          (file) => _attachmentTile(
            name: file.name,
            subtitle: _formatFileSize(file.size),
            isNew: true,
            onRemove: () => setState(() => _newAttachments.remove(file)),
          ),
        ),
        OutlinedButton.icon(
          onPressed: _pickFiles,
          icon: const Icon(Icons.attach_file, size: 18),
          label: const Text(
            'Add Attachment',
            style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
          ),
          style: OutlinedButton.styleFrom(
            foregroundColor: AppColors.primary,
            side: const BorderSide(color: AppColors.border),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(14),
            ),
          ),
        ),
      ],
    );
  }

  Widget _scheduleSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Schedule Broadcast',
                  style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary),
                ),
                Text(
                  'Publish at a specific future date & time',
                  style: TextStyle(fontSize: 11, color: AppColors.textMuted),
                ),
              ],
            ),
            Switch(
              value: _isSchedule,
              activeColor: AppColors.fanta,
              onChanged: (val) => setState(() => _isSchedule = val),
            ),
          ],
        ),
        if (_isSchedule) ...[
          const SizedBox(height: 14),
          _label('PUBLISH DATE & TIME'),
          const SizedBox(height: 8),
          InkWell(
            onTap: _selectDateTime,
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
                      const Icon(Icons.calendar_today,
                          size: 18, color: AppColors.fanta),
                      const SizedBox(width: 10),
                      Text(
                        DateFormat('dd MMM yyyy, hh:mm a').format(_startAt),
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textPrimary,
                        ),
                      ),
                    ],
                  ),
                  const Text(
                    'Change',
                    style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: AppColors.primary),
                  ),
                ],
              ),
            ),
          ),
        ],
      ],
    );
  }

  Widget _submitButton() {
    return SizedBox(
      width: double.infinity,
      height: 54,
      child: ElevatedButton(
        onPressed: _submitting ? null : _handleSubmit,
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.fanta,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(18),
          ),
          elevation: 4,
          shadowColor: AppColors.fanta.withOpacity(0.4),
        ),
        child: _submitting
            ? const SizedBox(
                width: 24,
                height: 24,
                child: CircularProgressIndicator(
                    color: Colors.white, strokeWidth: 2.5),
              )
            : Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.campaign, color: Colors.white, size: 22),
                  const SizedBox(width: 8),
                  Text(
                    widget.submitLabel,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final title =
        _isEdit ? 'Edit Staff Announcement' : 'Add Staff Announcement';

    if (_loading) {
      return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: Text(title)),
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

    if (_loadFailed) {
      return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: Text(title)),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.cloud_off, size: 40, color: AppColors.textMuted),
              const SizedBox(height: 12),
              const Text(
                'Unable to load master data',
                style: TextStyle(
                    fontWeight: FontWeight.bold, color: AppColors.textPrimary),
              ),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: () {
                  setState(() {
                    _loading = true;
                    _loadFailed = false;
                  });
                  _load();
                },
                child: const Text('Retry'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: Text(title)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
        child: Column(
          children: [
            _card(context, [
              _sectionHeader('Target Audience', Icons.tune),
              const SizedBox(height: 20),
              _label('ACADEMIC YEAR'),
              const SizedBox(height: 8),
              _lockedAcademicYear(),
              const SizedBox(height: 16),
              _label('USER TYPE *'),
              const SizedBox(height: 8),
              _userTypeToggle(),
              const SizedBox(height: 20),
              MultiSelectChips<BranchItem>(
                label: 'Branch *',
                options: _branches,
                selected: _selectedBranches,
                labelBuilder: (branch) => branch.name,
                valueBuilder: (branch) => branch.id.toString(),
                onToggle: _toggleBranch,
              ),
              MultiSelectChips<String>(
                label: 'Department *',
                options: _departments,
                selected: _selectedDepartments,
                onToggle: _toggleDepartment,
              ),
              if (_usertype == 'GROUP') ...[
                _label('GENDER'),
                const SizedBox(height: 8),
                _genderChips(),
                const SizedBox(height: 16),
              ] else ...[
                _label('STAFF *'),
                const SizedBox(height: 8),
                _staffPicker(),
                const SizedBox(height: 16),
              ],
            ]),
            const SizedBox(height: 16),
            _card(context, [
              _sectionHeader('Announcement Details', Icons.campaign_outlined),
              const SizedBox(height: 20),
              _label('TITLE *'),
              const SizedBox(height: 8),
              TextField(
                controller: _titleController,
                textCapitalization: TextCapitalization.sentences,
                decoration: const InputDecoration(
                  hintText: 'Enter announcement title',
                ),
              ),
              const SizedBox(height: 16),
              _label('CONTENT'),
              const SizedBox(height: 8),
              TextField(
                controller: _contentController,
                maxLines: 5,
                textCapitalization: TextCapitalization.sentences,
                decoration: const InputDecoration(
                  hintText: 'Write the announcement content...',
                ),
              ),
            ]),
            const SizedBox(height: 16),
            _card(context, [
              _sectionHeader('Attachments', Icons.attach_file),
              const SizedBox(height: 16),
              _attachmentsSection(),
            ]),
            const SizedBox(height: 16),
            _card(context, [_scheduleSection()]),
            const SizedBox(height: 24),
            _submitButton(),
          ],
        ),
      ),
    );
  }
}
