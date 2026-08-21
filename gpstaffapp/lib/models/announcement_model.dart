import 'dart:convert';

class AnnouncementModel {
  final dynamic id;
  final String? academicYear;
  final String usertype;
  final String? course;
  final dynamic branch;
  final dynamic coachingType;
  final dynamic category;
  final dynamic batch;
  final String? gender;
  final String? section;
  final dynamic students;
  final String title;
  final String? content;
  final int isSchedule;
  final String? startAt;
  final List<String> attachments;
  final String? createdAt;
  final String? updatedAt;

  AnnouncementModel({
    required this.id,
    this.academicYear,
    this.usertype = 'GROUP',
    this.course,
    this.branch,
    this.coachingType,
    this.category,
    this.batch,
    this.gender,
    this.section,
    this.students,
    required this.title,
    this.content,
    this.isSchedule = 0,
    this.startAt,
    this.attachments = const [],
    this.createdAt,
    this.updatedAt,
  });

  factory AnnouncementModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedAttachments = [];
    if (json['attachment'] != null) {
      if (json['attachment'] is List) {
        parsedAttachments = (json['attachment'] as List).map((e) => e.toString()).toList();
      } else if (json['attachment'] is String) {
        try {
          final decoded = jsonDecode(json['attachment']);
          if (decoded is List) {
            parsedAttachments = decoded.map((e) => e.toString()).toList();
          } else {
            parsedAttachments = [json['attachment'].toString()];
          }
        } catch (_) {
          if (json['attachment'].toString().isNotEmpty) {
            parsedAttachments = [json['attachment'].toString()];
          }
        }
      }
    }

    int sched = 0;
    if (json['is_schedule'] != null) {
      sched = int.tryParse(json['is_schedule'].toString()) ?? 0;
    }

    return AnnouncementModel(
      id: json['id'] ?? '',
      academicYear: json['academic_year']?.toString(),
      usertype: json['usertype']?.toString() ?? 'GROUP',
      course: json['course']?.toString(),
      branch: json['branch'],
      coachingType: json['coaching_type'],
      category: json['category'],
      batch: json['batch'],
      gender: json['gender']?.toString() ?? 'All',
      section: json['section']?.toString(),
      students: json['students'],
      title: (json['title'] ?? '').toString(),
      content: json['content']?.toString(),
      isSchedule: sched,
      startAt: json['start_at']?.toString(),
      attachments: parsedAttachments,
      createdAt: json['created_at']?.toString(),
      updatedAt: json['updated_at']?.toString(),
    );
  }

  String get cleanContent {
    if (content == null) return '';
    return content!.replaceAll(RegExp(r'<[^>]*>'), '').trim();
  }

  String get branchDisplay {
    if (branch == null) return 'All';
    if (branch is List) return (branch as List).join(', ');
    return branch.toString();
  }

  String get coachingTypeDisplay {
    if (coachingType == null || coachingType.toString().isEmpty) return 'ALL';
    if (coachingType is List) return (coachingType as List).join(', ');
    return coachingType.toString();
  }

  String get categoryDisplay {
    if (category == null || category.toString().isEmpty) return 'All Categories';
    if (category is List) return (category as List).join(', ');
    return category.toString();
  }

  String get batchDisplay {
    if (batch == null || batch.toString().isEmpty) return 'All Batches';
    if (batch is List) return (batch as List).join(', ');
    return batch.toString();
  }

  static String getAttachmentFileName(String path) {
    return path.split('/').last;
  }
}
