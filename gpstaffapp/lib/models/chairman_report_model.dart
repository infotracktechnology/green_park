class ChairmanReportModel {
  final bool status;
  final List<String> tests;
  final String? testName;
  final bool allOffline;
  final int totalMarks;
  final List<String> subjects;
  final List<ChairmanStudentResultModel> results;

  ChairmanReportModel({
    required this.status,
    required this.tests,
    this.testName,
    this.allOffline = false,
    this.totalMarks = 0,
    required this.subjects,
    required this.results,
  });

  factory ChairmanReportModel.fromJson(Map<String, dynamic> json) {
    return ChairmanReportModel(
      status: json['status'] == true,
      tests: (json['tests'] as List?)?.map((e) => e.toString()).toList() ?? [],
      testName: json['test_name']?.toString(),
      allOffline: json['all_offline'] == true,
      totalMarks: (json['total_marks'] as num?)?.toInt() ?? 0,
      subjects: (json['subjects'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      results: (json['results'] as List?)
              ?.map((e) => ChairmanStudentResultModel.fromJson(
                  e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class SubjectMarkModel {
  final int right;
  final int wrong;
  final int left;
  final int total;

  SubjectMarkModel({
    this.right = 0,
    this.wrong = 0,
    this.left = 0,
    this.total = 0,
  });

  factory SubjectMarkModel.fromJson(Map<String, dynamic> json) {
    return SubjectMarkModel(
      right: (json['r'] as num?)?.toInt() ?? 0,
      wrong: (json['w'] as num?)?.toInt() ?? 0,
      left: (json['l'] as num?)?.toInt() ?? 0,
      total: (json['tot'] as num?)?.toInt() ?? 0,
    );
  }
}

class ChairmanStudentResultModel {
  final int sNo;
  final String studentId;
  final String studentName;
  final String? gender;
  final String? campus;
  final String? section;
  final String? batch;
  final String? stmode;
  final String? coachingType;
  final int mark;
  final Map<String, SubjectMarkModel> subjectMarks;

  ChairmanStudentResultModel({
    required this.sNo,
    required this.studentId,
    required this.studentName,
    this.gender,
    this.campus,
    this.section,
    this.batch,
    this.stmode,
    this.coachingType,
    required this.mark,
    required this.subjectMarks,
  });

  factory ChairmanStudentResultModel.fromJson(Map<String, dynamic> json) {
    final Map<String, dynamic> subMarksRaw =
        (json['subject_marks'] as Map<String, dynamic>?) ?? {};
    final Map<String, SubjectMarkModel> parsedSubMarks = {};
    subMarksRaw.forEach((key, value) {
      if (value is Map<String, dynamic>) {
        parsedSubMarks[key] = SubjectMarkModel.fromJson(value);
      }
    });

    return ChairmanStudentResultModel(
      sNo: (json['s_no'] as num?)?.toInt() ?? 0,
      studentId: json['student_id']?.toString() ?? '',
      studentName: json['student_name']?.toString() ?? '',
      gender: json['gender']?.toString(),
      campus: json['campus']?.toString(),
      section: json['section']?.toString(),
      batch: json['batch']?.toString(),
      stmode: json['stmode']?.toString(),
      coachingType: json['coaching_type']?.toString(),
      mark: (json['mark'] as num?)?.toInt() ?? 0,
      subjectMarks: parsedSubMarks,
    );
  }
}
