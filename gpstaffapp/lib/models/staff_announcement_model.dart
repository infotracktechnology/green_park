import 'dart:convert';

/// A staff member available for INDIVIDUAL announcements.
class StaffOption {
  final String username;
  final String schoolInitial;
  final String branchId;
  final String department;
  final String displayName;

  StaffOption({
    required this.username,
    this.schoolInitial = '',
    this.branchId = '',
    this.department = '',
    String? displayName,
  }) : displayName = displayName ??
            (schoolInitial.isEmpty ? username : '$username $schoolInitial');

  factory StaffOption.fromJson(dynamic item) {
    if (item is Map) {
      final username =
          (item['username'] ?? item['biometric_no'] ?? item['name'] ?? '')
              .toString();
      final initial = (item['school_initial'] ?? '').toString();

      return StaffOption(
        username: username,
        schoolInitial: initial,
        branchId: (item['branch_id'] ?? item['branch'] ?? '').toString(),
        department: (item['department'] ?? '').toString(),
        displayName: (item['display_name'] ?? '').toString().isEmpty
            ? null
            : item['display_name'].toString(),
      );
    }
    return StaffOption(username: item.toString());
  }
}

class StaffAnnouncementModel {
  final dynamic id;
  final String? academicYear;
  final String usertype;
  final String? branch;
  final String branchNames;
  final String? department;
  final String departmentNames;
  final String? gender;
  final List<String> staffIds;
  final String title;
  final String? content;
  final int isSchedule;
  final String? startAt;
  final List<String> attachments;
  final String? createdAt;
  final String? updatedAt;

  StaffAnnouncementModel({
    required this.id,
    this.academicYear,
    this.usertype = 'GROUP',
    this.branch,
    this.branchNames = 'All',
    this.department,
    this.departmentNames = 'All',
    this.gender,
    this.staffIds = const [],
    required this.title,
    this.content,
    this.isSchedule = 0,
    this.startAt,
    this.attachments = const [],
    this.createdAt,
    this.updatedAt,
  });

  factory StaffAnnouncementModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedAttachments = [];
    if (json['attachment'] != null) {
      if (json['attachment'] is List) {
        parsedAttachments =
            (json['attachment'] as List).map((e) => e.toString()).toList();
      } else if (json['attachment'] is String) {
        try {
          final decoded = jsonDecode(json['attachment']);
          if (decoded is List) {
            parsedAttachments = decoded.map((e) => e.toString()).toList();
          } else if (json['attachment'].toString().isNotEmpty) {
            parsedAttachments = [json['attachment'].toString()];
          }
        } catch (_) {
          if (json['attachment'].toString().isNotEmpty) {
            parsedAttachments = [json['attachment'].toString()];
          }
        }
      }
    }

    List<String> parsedStaffIds = [];
    if (json['staff_ids'] is List) {
      parsedStaffIds =
          (json['staff_ids'] as List).map((e) => e.toString()).toList();
    } else if (json['staff_ids'] is String) {
      try {
        final decoded = jsonDecode(json['staff_ids']);
        if (decoded is List) {
          parsedStaffIds = decoded.map((e) => e.toString()).toList();
        }
      } catch (_) {
        parsedStaffIds = [];
      }
    }

    int sched = 0;
    if (json['is_schedule'] != null) {
      sched = int.tryParse(json['is_schedule'].toString()) ?? 0;
    }

    return StaffAnnouncementModel(
      id: json['id'] ?? '',
      academicYear: json['academic_year']?.toString(),
      usertype: json['usertype']?.toString() ?? 'GROUP',
      branch: json['branch']?.toString(),
      branchNames: (json['branch_names'] ?? 'All').toString(),
      department: json['department']?.toString(),
      departmentNames: (json['department_names'] ?? 'All').toString(),
      gender: json['gender']?.toString(),
      staffIds: parsedStaffIds,
      title: (json['title'] ?? '').toString(),
      content: json['content']?.toString(),
      isSchedule: sched,
      startAt: json['start_at']?.toString(),
      attachments: parsedAttachments,
      createdAt: json['created_at']?.toString(),
      updatedAt: json['updated_at']?.toString(),
    );
  }

  /// Comma separated branch ids as a list.
  List<String> get branchIdList {
    if (branch == null || branch!.trim().isEmpty) return [];
    return branch!
        .split(',')
        .map((e) => e.trim())
        .where((e) => e.isNotEmpty)
        .toList();
  }

  /// Comma separated departments as a list.
  List<String> get departmentList {
    if (department == null || department!.trim().isEmpty) return [];
    return department!
        .split(',')
        .map((e) => e.trim())
        .where((e) => e.isNotEmpty)
        .toList();
  }

  bool get isIndividual => usertype.toUpperCase() == 'INDIVIDUAL';
  bool get isScheduled => isSchedule == 1;

  String get cleanContent {
    if (content == null) return '';
    return content!.replaceAll(RegExp(r'<[^>]*>'), '').trim();
  }

  static String getAttachmentFileName(String path) {
    return path.split('/').last;
  }
}
