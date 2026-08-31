import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api/api_client.dart';
import '../models/staff_profile_model.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class StaffProfileScreen extends StatefulWidget {
  final dynamic staffId;

  const StaffProfileScreen({super.key, this.staffId});

  @override
  State<StaffProfileScreen> createState() => _StaffProfileScreenState();
}

class _StaffProfileScreenState extends State<StaffProfileScreen> {
  StaffProfileModel? _profile;
  bool _loading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchProfile();
  }

  Future<void> _fetchProfile() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final endpoint = widget.staffId != null
          ? '/admin/staff/${widget.staffId}'
          : '/admin/staff/profile';

      final response = await dio.get(endpoint);

      if (response.data != null && response.data['status'] == true) {
        final staffData = response.data['staff'] ?? response.data['data'];
        if (staffData != null) {
          setState(() {
            _profile = StaffProfileModel.fromJson(
              staffData is Map<String, dynamic> ? staffData : {},
            );
          });
        } else {
          setState(() => _errorMessage = 'Profile data not found');
        }
      } else {
        setState(() {
          _errorMessage =
              response.data?['message'] ?? 'Failed to load staff profile';
        });
      }
    } catch (e) {
      debugPrint('Error fetching staff profile: $e');
      if (mounted) {
        final auth = Provider.of<AuthProvider>(context, listen: false);
        if (auth.user?.rawJson != null) {
          setState(() {
            _profile = StaffProfileModel.fromJson(auth.user!.rawJson!);
            _errorMessage = null;
          });
        } else {
          setState(() {
            _errorMessage = 'Failed to load profile. Please try again.';
          });
        }
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _makeCall(String? number) async {
    if (number == null || number.trim().isEmpty) return;
    final uri = Uri.parse('tel:${number.trim()}');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _sendEmail(String? email) async {
    if (email == null || email.trim().isEmpty) return;
    final uri = Uri.parse('mailto:${email.trim()}');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Staff Profile'),
        backgroundColor: AppColors.primary,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: _fetchProfile,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchProfile,
        color: AppColors.primary,
        child: _buildBody(),
      ),
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(color: AppColors.primary),
            SizedBox(height: 14),
            Text('Loading profile...',
                style: TextStyle(color: AppColors.textSecondary)),
          ],
        ),
      );
    }

    if (_errorMessage != null && _profile == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 48, color: AppColors.error),
              const SizedBox(height: 12),
              Text(
                _errorMessage!,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: _fetchProfile,
                icon: const Icon(Icons.refresh, size: 18),
                label: const Text('Try Again'),
              ),
            ],
          ),
        ),
      );
    }

    final p = _profile!;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      physics: const AlwaysScrollableScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Header Card
          _buildHeaderCard(p),
          const SizedBox(height: 16),

          // Quick Action Contacts
          if ((p.mobNo != null && p.mobNo!.isNotEmpty) ||
              (p.email != null && p.email!.isNotEmpty)) ...[
            _buildQuickActionCard(p),
            const SizedBox(height: 16),
          ],

          // Personal Details
          _buildSectionCard(
            title: 'Personal Details',
            icon: Icons.person_outline,
            items: [
              _buildInfoRow('Full Name', p.name),
              if (p.schoolInitial != null && p.schoolInitial!.isNotEmpty)
                _buildInfoRow('School Initial', p.schoolInitial!),
              if (p.gender != null && p.gender!.isNotEmpty)
                _buildInfoRow('Gender', p.gender!),
              if (p.dob != null && p.dob!.isNotEmpty)
                _buildInfoRow('Date of Birth', p.dob!),
              if (p.age != null && p.age!.isNotEmpty)
                _buildInfoRow('Age', '${p.age} years'),
              if (p.bloodGroup != null && p.bloodGroup!.isNotEmpty)
                _buildInfoRow('Blood Group', p.bloodGroup!),
              if (p.maritalStatus != null && p.maritalStatus!.isNotEmpty)
                _buildInfoRow('Marital Status', p.maritalStatus!),
              if (p.nationality != null && p.nationality!.isNotEmpty)
                _buildInfoRow('Nationality', p.nationality!),
              if (p.religion != null && p.religion!.isNotEmpty)
                _buildInfoRow('Religion', p.religion!),
              if (p.community != null && p.community!.isNotEmpty)
                _buildInfoRow('Community', p.community!),
              if (p.caste != null && p.caste!.isNotEmpty)
                _buildInfoRow('Caste', p.caste!),
              if (p.aadhaarNo != null && p.aadhaarNo!.isNotEmpty)
                _buildInfoRow('Aadhaar No', p.aadhaarNo!),
            ],
          ),
          const SizedBox(height: 16),

          // Contact & Address
          _buildSectionCard(
            title: 'Contact & Address',
            icon: Icons.location_on_outlined,
            items: [
              if (p.mobNo != null && p.mobNo!.isNotEmpty)
                _buildInfoRow('Primary Mobile', p.mobNo!, isPhone: true),
              if (p.alternateMobNo != null && p.alternateMobNo!.isNotEmpty)
                _buildInfoRow('Alternate Mobile', p.alternateMobNo!,
                    isPhone: true),
              if (p.email != null && p.email!.isNotEmpty)
                _buildInfoRow('Email Address', p.email!, isEmail: true),
              if (p.addressLine1 != null && p.addressLine1!.isNotEmpty)
                _buildInfoRow('Address Line 1', p.addressLine1!),
              if (p.addressLine2 != null && p.addressLine2!.isNotEmpty)
                _buildInfoRow('Address Line 2', p.addressLine2!),
              if (p.city != null && p.city!.isNotEmpty)
                _buildInfoRow('City / District', p.city!),
              if (p.state != null && p.state!.isNotEmpty)
                _buildInfoRow('State', p.state!),
              if (p.pincode != null && p.pincode!.isNotEmpty)
                _buildInfoRow('Pincode', p.pincode!),
            ],
          ),
          const SizedBox(height: 16),

          // Employment & Academic
          _buildSectionCard(
            title: 'Employment & Academic',
            icon: Icons.work_outline,
            items: [
              if (p.designation != null && p.designation!.isNotEmpty)
                _buildInfoRow('Designation', p.designation!),
              if (p.department != null && p.department!.isNotEmpty)
                _buildInfoRow('Department', p.department!),
              if (p.branchName != null && p.branchName!.isNotEmpty)
                _buildInfoRow('Campus / Branch', p.branchName!),
              if (p.shiftName != null && p.shiftName!.isNotEmpty)
                _buildInfoRow('Work Shift', p.shiftName!),
              if (p.staffType != null && p.staffType!.isNotEmpty)
                _buildInfoRow('Staff Type', p.staffType!),
              if (p.hostelDayscholar != null && p.hostelDayscholar!.isNotEmpty)
                _buildInfoRow('Hostel / Day Scholar', p.hostelDayscholar!),
              if (p.dateOfJoining != null && p.dateOfJoining!.isNotEmpty)
                _buildInfoRow('Date of Joining', p.dateOfJoining!),
              if (p.qualifications != null && p.qualifications!.isNotEmpty)
                _buildInfoRow('Qualifications', p.qualifications!),
              if (p.experience != null && p.experience!.isNotEmpty)
                _buildInfoRow('Experience', p.experience!),
              if (p.previousSchool != null && p.previousSchool!.isNotEmpty)
                _buildInfoRow('Previous School', p.previousSchool!),
              if (p.classHandlingType != null &&
                  p.classHandlingType!.isNotEmpty)
                _buildInfoRow('Class Handling Type', p.classHandlingType!),
              if (p.paperCorrection != null && p.paperCorrection!.isNotEmpty)
                _buildInfoRow('Paper Correction', p.paperCorrection!),
              if (p.handelingClass != null && p.handelingClass!.isNotEmpty)
                _buildInfoRow('Handling Class', p.handelingClass!),
            ],
          ),
          const SizedBox(height: 16),

          // Class & Subject Assignments (if present)
          if (p.classAssign != null || p.subAssign != null) ...[
            _buildAssignmentsCard(p),
            const SizedBox(height: 16),
          ],

          // Family & Spouse Details
          _buildSectionCard(
            title: 'Family Details',
            icon: Icons.family_restroom_outlined,
            items: [
              if (p.fatherName != null && p.fatherName!.isNotEmpty)
                _buildInfoRow('Father Name', p.fatherName!),
              if (p.motherName != null && p.motherName!.isNotEmpty)
                _buildInfoRow('Mother Name', p.motherName!),
              if (p.fatherPhNo != null && p.fatherPhNo!.isNotEmpty)
                _buildInfoRow('Parent Mobile No', p.fatherPhNo!, isPhone: true),
              if (p.spouseName != null && p.spouseName!.isNotEmpty)
                _buildInfoRow('Spouse Name', p.spouseName!),
              if (p.spousePhNo != null && p.spousePhNo!.isNotEmpty)
                _buildInfoRow('Spouse Mobile No', p.spousePhNo!, isPhone: true),
              if (p.spouseOccupation != null && p.spouseOccupation!.isNotEmpty)
                _buildInfoRow('Spouse Occupation', p.spouseOccupation!),
            ],
          ),
          const SizedBox(height: 30),
        ],
      ),
    );
  }

  Widget _buildHeaderCard(StaffProfileModel p) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 32,
                backgroundColor: AppColors.primary.withOpacity(0.12),
                child: Text(
                  p.name.isNotEmpty ? p.name[0].toUpperCase() : 'S',
                  style: const TextStyle(
                    fontSize: 26,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      p.name,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    if (p.designation != null && p.designation!.isNotEmpty) ...[
                      const SizedBox(height: 3),
                      Text(
                        p.designation!,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                    if (p.department != null && p.department!.isNotEmpty) ...[
                      const SizedBox(height: 2),
                      Text(
                        'Dept: ${p.department}',
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.textMuted,
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ],
          ),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 14),
            child: Divider(height: 1, color: AppColors.borderLight),
          ),
          // Badges Row
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              if (p.biometricNo != null && p.biometricNo!.isNotEmpty)
                _buildBadge('Bio ID: ${p.biometricNo}', AppColors.primary),
              if (p.branchName != null && p.branchName!.isNotEmpty)
                _buildBadge(p.branchName!, Colors.teal),
              if (p.shiftName != null && p.shiftName!.isNotEmpty)
                _buildBadge('Shift: ${p.shiftName}', Colors.deepPurple),
              if (p.staffType != null && p.staffType!.isNotEmpty)
                _buildBadge(p.staffType!, Colors.indigo),
              if (p.hostelDayscholar != null && p.hostelDayscholar!.isNotEmpty)
                _buildBadge(p.hostelDayscholar!, Colors.brown),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionCard(StaffProfileModel p) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          if (p.mobNo != null && p.mobNo!.isNotEmpty)
            InkWell(
              onTap: () => _makeCall(p.mobNo),
              borderRadius: BorderRadius.circular(10),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                child: Row(
                  children: [
                    const Icon(Icons.phone_outlined,
                        size: 18, color: AppColors.primary),
                    const SizedBox(width: 6),
                    Text(
                      'Call: ${p.mobNo}',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: AppColors.primary,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          if (p.email != null && p.email!.isNotEmpty)
            InkWell(
              onTap: () => _sendEmail(p.email),
              borderRadius: BorderRadius.circular(10),
              child: const Padding(
                padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                child: Row(
                  children: [
                    Icon(Icons.email_outlined,
                        size: 18, color: AppColors.fanta),
                    SizedBox(width: 6),
                    Text(
                      'Email',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: AppColors.fanta,
                      ),
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required List<Widget> items,
  }) {
    final validItems = items.where((element) => element is! SizedBox).toList();
    if (validItems.isEmpty) return const SizedBox.shrink();

    return Container(
      padding: const EdgeInsets.all(18),
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
        children: [
          Row(
            children: [
              Icon(icon, size: 20, color: AppColors.primary),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                  letterSpacing: 0.3,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          ...items,
        ],
      ),
    );
  }

  Widget _buildAssignmentsCard(StaffProfileModel p) {
    final classSections = _formatSections(p.classAssign);
    final subAssignment = _formatSubjectAssign(p.subAssign);

    if (classSections.isEmpty && subAssignment.isEmpty) {
      return const SizedBox.shrink();
    }

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.assignment_outlined,
                  size: 20, color: AppColors.primary),
              SizedBox(width: 8),
              Text(
                'Class & Subject Assignments',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          if (classSections.isNotEmpty)
            _buildInfoRow('Class Assignment', classSections),
          if (subAssignment.isNotEmpty)
            _buildInfoRow('Subject Assignment', subAssignment),
        ],
      ),
    );
  }

  String _formatSections(dynamic assign) {
    if (assign == null) return '';
    if (assign is String) {
      final trimmed = assign.trim();
      if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
        try {
          final decoded = jsonDecode(trimmed);
          return _formatSections(decoded);
        } catch (_) {}
      }
      return trimmed;
    }
    if (assign is List) {
      return assign
          .map((e) => e.toString().trim())
          .where((e) => e.isNotEmpty)
          .join(', ');
    }
    if (assign is Map) {
      final sections = assign['sections'] ?? assign['section'];
      return _formatSections(sections);
    }
    return assign.toString();
  }

  String _formatSubjectAssign(dynamic assign) {
    if (assign == null) return '';
    if (assign is String) {
      final trimmed = assign.trim();
      if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
        try {
          final decoded = jsonDecode(trimmed);
          return _formatSubjectAssign(decoded);
        } catch (_) {}
      }
      return trimmed;
    }
    if (assign is Map) {
      final subject = assign['subject']?.toString();
      final sections = _formatSections(assign['sections'] ?? assign['section']);
      if (subject != null && subject.isNotEmpty && sections.isNotEmpty) {
        return '$subject ($sections)';
      } else if (subject != null && subject.isNotEmpty) {
        return subject;
      } else if (sections.isNotEmpty) {
        return sections;
      }
    }
    return _formatSections(assign);
  }

  Widget _buildInfoRow(
    String label,
    String value, {
    bool isPhone = false,
    bool isEmail = false,
  }) {
    if (value.trim().isEmpty) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: AppColors.textSecondary,
              ),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: InkWell(
              onTap: isPhone
                  ? () => _makeCall(value)
                  : isEmail
                      ? () => _sendEmail(value)
                      : null,
              child: Text(
                value,
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: isPhone || isEmail
                      ? AppColors.primary
                      : AppColors.textPrimary,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBadge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: color.withOpacity(0.25)),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }
}
