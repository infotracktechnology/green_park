import 'dart:convert';

class AchievementModel {
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
  final dynamic filecategory;
  final String? video;
  final List<String> images;
  final String? pdf;
  final String? link;
  final String? content;
  final String? createdAt;
  final String? updatedAt;

  AchievementModel({
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
    this.filecategory,
    this.video,
    this.images = const [],
    this.pdf,
    this.link,
    this.content,
    this.createdAt,
    this.updatedAt,
  });

  factory AchievementModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedImages = [];
    if (json['images'] != null) {
      if (json['images'] is List) {
        parsedImages =
            (json['images'] as List).map((e) => e.toString()).toList();
      } else if (json['images'] is String) {
        try {
          final decoded = jsonDecode(json['images']);
          if (decoded is List) {
            parsedImages = decoded.map((e) => e.toString()).toList();
          } else {
            parsedImages = [json['images'].toString()];
          }
        } catch (_) {
          if (json['images'].toString().isNotEmpty) {
            parsedImages = [json['images'].toString()];
          }
        }
      }
    }

    return AchievementModel(
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
      filecategory: json['filecategory'],
      video: json['video']?.toString(),
      images: parsedImages,
      pdf: json['pdf']?.toString(),
      link: json['link']?.toString(),
      content: (json['content'] ?? '').toString(),
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

  String get fileCategoryDisplay {
    if (filecategory == null || filecategory.toString().isEmpty) return 'None';
    if (filecategory is List) return (filecategory as List).join(', ');
    return filecategory.toString();
  }
}
