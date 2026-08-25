class DiscussionVideoModel {
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
  final String? day;
  final String? date;
  final String? subject;
  final String? part;
  final String title;
  final dynamic videoId;
  final String? startAt;
  final String? endAt;
  final String? createdAt;
  final String? updatedAt;

  DiscussionVideoModel({
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
    this.day,
    this.date,
    this.subject,
    this.part,
    required this.title,
    this.videoId,
    this.startAt,
    this.endAt,
    this.createdAt,
    this.updatedAt,
  });

  factory DiscussionVideoModel.fromJson(Map<String, dynamic> json) {
    return DiscussionVideoModel(
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
      day: json['day']?.toString(),
      date: json['date']?.toString(),
      subject: json['subject']?.toString(),
      part: json['part']?.toString(),
      title: (json['title'] ?? '').toString(),
      videoId: json['video_id'],
      startAt: json['start_at']?.toString(),
      endAt: json['end_at']?.toString(),
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
}
