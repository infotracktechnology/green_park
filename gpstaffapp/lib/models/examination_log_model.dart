class ExamLogStatsModel {
  final int totalEligible;
  final int totalOnline;
  final int totalWriting;
  final int totalFinished;
  final int totalNotFinished;
  final int totalAbsent;

  ExamLogStatsModel({
    this.totalEligible = 0,
    this.totalOnline = 0,
    this.totalWriting = 0,
    this.totalFinished = 0,
    this.totalNotFinished = 0,
    this.totalAbsent = 0,
  });

  factory ExamLogStatsModel.fromJson(Map<String, dynamic> json) {
    return ExamLogStatsModel(
      totalEligible:
          int.tryParse(json['total_eligible']?.toString() ?? '0') ?? 0,
      totalOnline:
          int.tryParse(json['total_online']?.toString() ?? '0') ?? 0,
      totalWriting:
          int.tryParse(json['total_writing']?.toString() ?? '0') ?? 0,
      totalFinished:
          int.tryParse(json['total_finished']?.toString() ?? '0') ?? 0,
      totalNotFinished:
          int.tryParse(json['total_not_finished']?.toString() ?? '0') ?? 0,
      totalAbsent:
          int.tryParse(json['total_absent']?.toString() ?? '0') ?? 0,
    );
  }
}

class ExamLogStudentModel {
  final String studentId;
  final String studentName;
  final String course;
  final String coachingType;
  final String section;
  final String fatherPhNo;
  final String motherPhNo;
  final String campus;

  ExamLogStudentModel({
    this.studentId = '',
    this.studentName = '',
    this.course = '',
    this.coachingType = '',
    this.section = '',
    this.fatherPhNo = '',
    this.motherPhNo = '',
    this.campus = '',
  });

  factory ExamLogStudentModel.fromJson(Map<String, dynamic> json) {
    return ExamLogStudentModel(
      studentId: (json['student_id'] ?? '').toString(),
      studentName: (json['student_name'] ?? json['name'] ?? '').toString(),
      course: (json['course'] ?? '').toString(),
      coachingType: (json['coaching_type'] ?? '').toString(),
      section: (json['section'] ?? '').toString(),
      fatherPhNo:
          (json['father_ph_no'] ?? json['father_mobile'] ?? '').toString(),
      motherPhNo:
          (json['mother_ph_no'] ?? json['mother_mobile'] ?? '').toString(),
      campus: (json['campus'] ?? '').toString(),
    );
  }
}

class ExaminationLogReportModel {
  final List<String> categories;
  final List<String> exams;
  final ExamLogStatsModel? stats;
  final Map<String, List<ExamLogStudentModel>> studentDetails;
  final String? testName;

  ExaminationLogReportModel({
    this.categories = const [],
    this.exams = const [],
    this.stats,
    this.studentDetails = const {},
    this.testName,
  });

  factory ExaminationLogReportModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedCategories = [];
    if (json['category'] is List) {
      parsedCategories =
          (json['category'] as List).map((e) => e.toString()).toList();
    }

    List<String> parsedExams = [];
    if (json['exams'] is List) {
      for (var item in json['exams']) {
        if (item is Map && item['name'] != null) {
          parsedExams.add(item['name'].toString());
        } else if (item != null) {
          parsedExams.add(item.toString());
        }
      }
    }

    ExamLogStatsModel? parsedStats;
    if (json['stats'] != null && json['stats'] is Map<String, dynamic>) {
      parsedStats = ExamLogStatsModel.fromJson(json['stats']);
    }

    Map<String, List<ExamLogStudentModel>> parsedStudentDetails = {};
    if (json['studentDetails'] != null &&
        json['studentDetails'] is Map<String, dynamic>) {
      final map = json['studentDetails'] as Map<String, dynamic>;
      map.forEach((key, val) {
        if (val is List) {
          parsedStudentDetails[key] = val
              .map((e) => ExamLogStudentModel.fromJson(
                  e is Map<String, dynamic> ? e : {}))
              .toList();
        }
      });
    }

    return ExaminationLogReportModel(
      categories: parsedCategories,
      exams: parsedExams,
      stats: parsedStats,
      studentDetails: parsedStudentDetails,
      testName: json['test_name']?.toString(),
    );
  }
}
