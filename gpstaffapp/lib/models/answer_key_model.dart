import 'dart:convert';

class AnswerKeyModel {
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
  final int isSchedule;
  final String? startAt;
  final List<String> files;
  final String? createdAt;
  final String? updatedAt;

  AnswerKeyModel({
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
    this.isSchedule = 0,
    this.startAt,
    this.files = const [],
    this.createdAt,
    this.updatedAt,
  });

  factory AnswerKeyModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedFiles = [];
    if (json['file_path'] != null) {
      if (json['file_path'] is List) {
        parsedFiles =
            (json['file_path'] as List).map((e) => e.toString()).toList();
      } else if (json['file_path'] is String) {
        try {
          final decoded = jsonDecode(json['file_path']);
          if (decoded is List) {
            parsedFiles = decoded.map((e) => e.toString()).toList();
          } else {
            parsedFiles = [json['file_path'].toString()];
          }
        } catch (_) {
          if (json['file_path'].toString().isNotEmpty) {
            parsedFiles = [json['file_path'].toString()];
          }
        }
      }
    }

    int sched = 0;
    if (json['is_schedule'] != null) {
      sched = int.tryParse(json['is_schedule'].toString()) ?? 0;
    }

    return AnswerKeyModel(
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
      isSchedule: sched,
      startAt: json['start_at']?.toString(),
      files: parsedFiles,
      createdAt: json['created_at']?.toString(),
      updatedAt: json['updated_at']?.toString(),
    );
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
    if (category == null || category.toString().isEmpty) {
      return 'All Categories';
    }
    if (category is List) return (category as List).join(', ');
    return category.toString();
  }

  String get batchDisplay {
    if (batch == null || batch.toString().isEmpty) return 'All Batches';
    if (batch is List) return (batch as List).join(', ');
    return batch.toString();
  }

  static String getFileName(String path) {
    return path.split('/').last;
  }
}
