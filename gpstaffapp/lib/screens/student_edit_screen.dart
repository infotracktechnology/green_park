import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../api/api_client.dart';
import '../models/student_detail_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';

class StudentEditScreen extends StatefulWidget {
  final StudentDetailModel student;
  final bool isReadOnly;

  const StudentEditScreen({
    super.key,
    required this.student,
    this.isReadOnly = false,
  });

  @override
  State<StudentEditScreen> createState() => _StudentEditScreenState();
}

class _StudentEditScreenState extends State<StudentEditScreen> {
  final _formKey = GlobalKey<FormState>();

  // Controllers
  late TextEditingController _studentNameController;
  late TextEditingController _passwordController;
  late TextEditingController _sectionController;
  late TextEditingController _batchController;
  late TextEditingController _ageController;
  late TextEditingController _aadharController;
  late TextEditingController _nationalityController;
  late TextEditingController _religionController;
  late TextEditingController _casteController;
  late TextEditingController _studentWhatsappController;
  late TextEditingController _phNo2Controller;
  late TextEditingController _fatherPhNoController;
  late TextEditingController _motherPhNoController;
  late TextEditingController _teamsIdController;
  late TextEditingController _teamsPasswordController;
  late TextEditingController _descriptionController;

  // Dropdown & Date values
  DateTime? _admissionDate;
  DateTime? _dob;
  dynamic _selectedCampus;
  String? _selectedCourse;
  String? _selectedCoachingType;
  String? _selectedHostelDayscholar;
  String? _selectedAcNonac;
  String? _selectedGender;
  String? _selectedCommunity;
  String? _selectedBloodGroup;
  String? _selectedBillType;

  bool _obscurePassword = true;
  bool _saving = false;

  final List<String> _genderOptions = ['MALE', 'FEMALE', 'Other'];
  final List<String> _communityOptions = [
    'OC',
    'BC',
    'BCM',
    'MBC/DNC',
    'SC',
    'SCA',
    'ST'
  ];
  final List<String> _bloodGroupOptions = [
    'A+',
    'A-',
    'B+',
    'B-',
    'AB+',
    'AB-',
    'O+',
    'O-'
  ];
  final List<String> _billTypeOptions = [
    'GPI,NKL',
    'GPCC,NKL',
    'GPCI,NKL',
    'GPCA,NKL',
    'GPCI,KARUR',
    'GPCI,ERODE',
    'GPCA,COIMBATORE',
    'GPA,CHENNAI',
  ];
  final List<String> _coachingTypeOptions = [
    'OFFLINE',
    'ONLINE',
    'ONLINE LIVE',
    'ONLINE RECORDED',
    'TEST BATCH'
  ];
  final List<String> _hostelOptions = ['DAYSCHOLAR', 'HOSTEL'];
  final List<String> _acOptions = ['AC', 'NON AC'];

  @override
  void initState() {
    super.initState();
    final s = widget.student;

    _studentNameController = TextEditingController(text: s.studentName);
    _passwordController = TextEditingController(text: s.password ?? '');
    _sectionController = TextEditingController(text: s.section ?? '');
    _batchController = TextEditingController(text: s.batch ?? '');
    _ageController = TextEditingController(text: s.age ?? '');
    _aadharController = TextEditingController(text: s.aadharCardNo ?? '');
    _nationalityController =
        TextEditingController(text: s.nationality ?? 'Indian');
    _religionController = TextEditingController(text: s.religion ?? '');
    _casteController = TextEditingController(text: s.caste ?? '');
    _studentWhatsappController =
        TextEditingController(text: s.studentWhatsappNo ?? s.phNo1 ?? '');
    _phNo2Controller = TextEditingController(text: s.phNo2 ?? '');
    _fatherPhNoController = TextEditingController(text: s.fatherPhNo ?? '');
    _motherPhNoController = TextEditingController(text: s.motherPhNo ?? '');
    _teamsIdController = TextEditingController(text: s.teamsId ?? '');
    _teamsPasswordController =
        TextEditingController(text: s.teamsPassword ?? '');
    _descriptionController = TextEditingController(text: s.description ?? '');

    if (s.admissionDate != null && s.admissionDate!.isNotEmpty) {
      try {
        _admissionDate = DateTime.parse(s.admissionDate!);
      } catch (_) {}
    }
    if (s.dob != null && s.dob!.isNotEmpty) {
      try {
        _dob = DateTime.parse(s.dob!);
      } catch (_) {}
    }

    _selectedCampus = s.campus?.toString();
    _selectedCourse = s.course;
    _selectedCoachingType = s.coachingType;
    _selectedHostelDayscholar = s.hostelDayscholar;
    _selectedAcNonac = s.acNonac;
    _selectedGender = s.gender;
    _selectedCommunity = s.community;
    _selectedBloodGroup = s.bloodGroup;
    _selectedBillType = s.institutionBillType;
  }

  @override
  void dispose() {
    _studentNameController.dispose();
    _passwordController.dispose();
    _sectionController.dispose();
    _batchController.dispose();
    _ageController.dispose();
    _aadharController.dispose();
    _nationalityController.dispose();
    _religionController.dispose();
    _casteController.dispose();
    _studentWhatsappController.dispose();
    _phNo2Controller.dispose();
    _fatherPhNoController.dispose();
    _motherPhNoController.dispose();
    _teamsIdController.dispose();
    _teamsPasswordController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  void _calculateAge(DateTime birthDate) {
    final today = DateTime.now();
    int age = today.year - birthDate.year;
    final mDiff = today.month - birthDate.month;
    final dDiff = today.day - birthDate.day;
    if (mDiff < 0 || (mDiff == 0 && dDiff < 0)) {
      age--;
    }
    _ageController.text = age > 0 ? '$age' : '0';
  }

  Future<void> _pickAdmissionDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _admissionDate ?? DateTime.now(),
      firstDate: DateTime(2000),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      setState(() => _admissionDate = picked);
    }
  }

  Future<void> _pickDob() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _dob ?? DateTime(2006, 1, 1),
      firstDate: DateTime(1980),
      lastDate: DateTime.now(),
    );
    if (picked != null) {
      setState(() {
        _dob = picked;
        _calculateAge(picked);
      });
    }
  }

  Future<void> _saveChanges() async {
    if (!_formKey.currentState!.validate()) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please check required fields.')),
      );
      return;
    }

    setState(() => _saving = true);

    try {
      final dio = ApiClient().dio;
      final payload = {
        'student_name': _studentNameController.text.trim(),
        'password': _passwordController.text.trim(),
        'admission_date': _admissionDate != null
            ? DateFormat('yyyy-MM-dd').format(_admissionDate!)
            : null,
        'campus': _selectedCampus,
        'course': _selectedCourse,
        'coaching_type': _selectedCoachingType,
        'hostel_dayscholar': _selectedHostelDayscholar,
        'ac_nonac': _selectedAcNonac,
        'section': _sectionController.text.trim(),
        'batch': _batchController.text.trim(),
        'gender': _selectedGender,
        'dob': _dob != null ? DateFormat('yyyy-MM-dd').format(_dob!) : null,
        'age': _ageController.text.trim(),
        'aadhar_card_no': _aadharController.text.trim(),
        'nationality': _nationalityController.text.trim(),
        'religion': _religionController.text.trim(),
        'community': _selectedCommunity,
        'caste': _casteController.text.trim(),
        'blood_group': _selectedBloodGroup,
        'student_whatsapp_no': _studentWhatsappController.text.trim(),
        'ph_no1': _studentWhatsappController.text.trim(),
        'ph_no2': _phNo2Controller.text.trim(),
        'father_ph_no': _fatherPhNoController.text.trim(),
        'mother_ph_no': _motherPhNoController.text.trim(),
        'institution_bill_type': _selectedBillType,
        'teams_id': _teamsIdController.text.trim(),
        'teams_password': _teamsPasswordController.text.trim(),
        'description': _descriptionController.text.trim(),
      };

      final response = await dio.post(
        '/admin/student/${widget.student.id}',
        data: payload,
      );

      if (response.data != null && response.data['status'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Student details updated successfully!'),
              backgroundColor: AppColors.success,
            ),
          );
          Navigator.pop(context, true);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                  response.data?['message'] ?? 'Failed to update student.'),
              backgroundColor: AppColors.error,
            ),
          );
        }
      }
    } catch (e) {
      debugPrint('Error updating student: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Error updating student details.'),
            backgroundColor: AppColors.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final filterProvider = Provider.of<AnnouncementFilterProvider>(context);
    final branches = filterProvider.master?.branches ?? [];
    final courses = filterProvider.master?.courses ?? [];

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text(widget.isReadOnly
            ? 'Student Details'
            : 'Edit ${widget.student.studentName}'),
        backgroundColor: AppColors.primary,
        elevation: 0,
      ),
      bottomNavigationBar: widget.isReadOnly
          ? null
          : Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.06),
                    blurRadius: 10,
                    offset: const Offset(0, -4),
                  ),
                ],
              ),
              child: ElevatedButton.icon(
                onPressed: _saving ? null : _saveChanges,
                icon: _saving
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.check_circle_outline, size: 20),
                label: Text(_saving ? 'Saving...' : 'Save Changes'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.fanta,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
              ),
            ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Header Card
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 26,
                      backgroundColor: AppColors.primary.withOpacity(0.12),
                      child: Text(
                        widget.student.studentName.isNotEmpty
                            ? widget.student.studentName[0].toUpperCase()
                            : 'S',
                        style: const TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary,
                        ),
                      ),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            widget.student.studentName,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: AppColors.textPrimary,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'ID: ${widget.student.studentId} • Username: ${widget.student.userName}',
                            style: const TextStyle(
                              fontSize: 12,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Section 1: Personal Details
              _buildSectionHeader('Personal Details', Icons.person_outline),
              _buildCardContainer([
                _buildTextField(
                  label: 'Student Name *',
                  controller: _studentNameController,
                  validator: (v) =>
                      v == null || v.trim().isEmpty ? 'Name is required' : null,
                ),
                const SizedBox(height: 12),
                _buildPasswordField(),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildDatePickerField(
                        label: 'Admission Date',
                        date: _admissionDate,
                        onTap: _pickAdmissionDate,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Campus / Branch',
                        value: _selectedCampus,
                        items: branches.map((b) {
                          return DropdownMenuItem<String>(
                            value: b.id.toString(),
                            child: Text(b.name, overflow: TextOverflow.ellipsis),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedCampus = val),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Course',
                        value: _selectedCourse,
                        items: courses.map((c) {
                          return DropdownMenuItem<String>(
                            value: c,
                            child: Text(c, overflow: TextOverflow.ellipsis),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedCourse = val),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Coaching Type',
                        value: _selectedCoachingType,
                        items: _coachingTypeOptions.map((c) {
                          return DropdownMenuItem<String>(
                            value: c,
                            child: Text(c, overflow: TextOverflow.ellipsis),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedCoachingType = val),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Hostel / Dayscholar',
                        value: _selectedHostelDayscholar,
                        items: _hostelOptions.map((h) {
                          return DropdownMenuItem<String>(
                            value: h,
                            child: Text(h),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedHostelDayscholar = val),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'AC / Non AC',
                        value: _selectedAcNonac,
                        items: _acOptions.map((a) {
                          return DropdownMenuItem<String>(
                            value: a,
                            child: Text(a),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedAcNonac = val),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildTextField(
                        label: 'Section',
                        controller: _sectionController,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Batch',
                        controller: _batchController,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Gender',
                        value: _selectedGender,
                        items: _genderOptions.map((g) {
                          return DropdownMenuItem<String>(
                            value: g,
                            child: Text(g),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedGender = val),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildDatePickerField(
                        label: 'Date of Birth',
                        date: _dob,
                        onTap: _pickDob,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildTextField(
                        label: 'Age',
                        controller: _ageController,
                        readOnly: true,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Aadhar Card No',
                        controller: _aadharController,
                        keyboardType: TextInputType.number,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildTextField(
                        label: 'Nationality',
                        controller: _nationalityController,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Religion',
                        controller: _religionController,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Community',
                        value: _selectedCommunity,
                        items: _communityOptions.map((c) {
                          return DropdownMenuItem<String>(
                            value: c,
                            child: Text(c),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedCommunity = val),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Caste',
                        controller: _casteController,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Blood Group',
                        value: _selectedBloodGroup,
                        items: _bloodGroupOptions.map((b) {
                          return DropdownMenuItem<String>(
                            value: b,
                            child: Text(b),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedBloodGroup = val),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildDropdownField<String>(
                        label: 'Institution Bill Type',
                        value: _selectedBillType,
                        items: _billTypeOptions.map((b) {
                          return DropdownMenuItem<String>(
                            value: b,
                            child: Text(b, overflow: TextOverflow.ellipsis),
                          );
                        }).toList(),
                        onChanged: (val) =>
                            setState(() => _selectedBillType = val),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildTextField(
                        label: 'Microsoft Teams ID',
                        controller: _teamsIdController,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Teams Password',
                        controller: _teamsPasswordController,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                _buildTextField(
                  label: 'Description / Remarks',
                  controller: _descriptionController,
                  maxLines: 2,
                ),
              ]),

              const SizedBox(height: 20),

              // Section 2: Contact & Phone Numbers
              _buildSectionHeader('Contact & Phone Numbers', Icons.phone_outlined),
              _buildCardContainer([
                Row(
                  children: [
                    Expanded(
                      child: _buildTextField(
                        label: 'Student WhatsApp No / Phone 1',
                        controller: _studentWhatsappController,
                        keyboardType: TextInputType.phone,
                        prefixIcon: Icons.phone_android,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Secondary Phone No (ph_no2)',
                        controller: _phNo2Controller,
                        keyboardType: TextInputType.phone,
                        prefixIcon: Icons.phone_iphone,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _buildTextField(
                        label: 'Father Mobile No',
                        controller: _fatherPhNoController,
                        keyboardType: TextInputType.phone,
                        prefixIcon: Icons.phone,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _buildTextField(
                        label: 'Mother Mobile No',
                        controller: _motherPhNoController,
                        keyboardType: TextInputType.phone,
                        prefixIcon: Icons.phone,
                      ),
                    ),
                  ],
                ),
              ]),

              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4),
      child: Row(
        children: [
          Icon(icon, size: 18, color: AppColors.primary),
          const SizedBox(width: 6),
          Text(
            title,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
              letterSpacing: 0.3,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCardContainer(List<Widget> children) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
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

  Widget _buildTextField({
    required String label,
    required TextEditingController controller,
    String? Function(String?)? validator,
    TextInputType keyboardType = TextInputType.text,
    bool readOnly = false,
    int maxLines = 1,
    IconData? prefixIcon,
  }) {
    final isFieldReadOnly = widget.isReadOnly || readOnly;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textSecondary,
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          validator: validator,
          readOnly: isFieldReadOnly,
          maxLines: maxLines,
          keyboardType: keyboardType,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
          decoration: InputDecoration(
            prefixIcon:
                prefixIcon != null ? Icon(prefixIcon, size: 16) : null,
            filled: true,
            fillColor:
                isFieldReadOnly ? Colors.grey.shade100 : AppColors.background,
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.border),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.border),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPasswordField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Password',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textSecondary,
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: _passwordController,
          readOnly: widget.isReadOnly,
          obscureText: _obscurePassword,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
          decoration: InputDecoration(
            filled: true,
            fillColor: widget.isReadOnly
                ? Colors.grey.shade100
                : AppColors.background,
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            suffixIcon: IconButton(
              icon: Icon(
                _obscurePassword ? Icons.visibility_off : Icons.visibility,
                size: 18,
              ),
              onPressed: () =>
                  setState(() => _obscurePassword = !_obscurePassword),
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.border),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.border),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildDropdownField<T>({
    required String label,
    required T? value,
    required List<DropdownMenuItem<T>> items,
    required void Function(T?) onChanged,
  }) {
    final validValue = items.any((i) => i.value == value) ? value : null;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textSecondary,
          ),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        const SizedBox(height: 6),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 10),
          decoration: BoxDecoration(
            color: widget.isReadOnly
                ? Colors.grey.shade100
                : AppColors.background,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.border),
          ),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<T>(
              isExpanded: true,
              value: validValue,
              hint: const Text('Select', style: TextStyle(fontSize: 12)),
              items: items,
              onChanged: widget.isReadOnly ? null : onChanged,
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildDatePickerField({
    required String label,
    required DateTime? date,
    required VoidCallback onTap,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textSecondary,
          ),
        ),
        const SizedBox(height: 6),
        InkWell(
          onTap: widget.isReadOnly ? null : onTap,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 11),
            decoration: BoxDecoration(
              color: widget.isReadOnly
                  ? Colors.grey.shade100
                  : AppColors.background,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.border),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  date != null
                      ? DateFormat('dd/MM/yyyy').format(date)
                      : 'Select Date',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: date != null
                        ? AppColors.textPrimary
                        : AppColors.textMuted,
                  ),
                ),
                if (!widget.isReadOnly)
                  const Icon(Icons.calendar_today_outlined,
                      size: 14, color: AppColors.primary),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
