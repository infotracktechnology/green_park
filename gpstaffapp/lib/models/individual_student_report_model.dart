class IndividualStudentModel {
  final String studentId;
  final String studentName;
  final String? course;
  final String? campus;
  final String? section;
  final String? academicYear;
  final String? coachingType;
  final String? fatherName;
  final String? branchName;

  IndividualStudentModel({
    required this.studentId,
    required this.studentName,
    this.course,
    this.campus,
    this.section,
    this.academicYear,
    this.coachingType,
    this.fatherName,
    this.branchName,
  });

  factory IndividualStudentModel.fromJson(Map<String, dynamic> json) {
    String branchName = '';
    if (json['branch'] is Map) {
      branchName = (json['branch']['name'] ?? '').toString();
    } else if (json['branch_name'] != null) {
      branchName = json['branch_name'].toString();
    } else if (json['campus_name'] != null) {
      branchName = json['campus_name'].toString();
    }
    return IndividualStudentModel(
      studentId: (json['student_id'] ?? '').toString(),
      studentName: (json['student_name'] ?? '').toString(),
      course: json['course']?.toString(),
      campus: json['campus']?.toString(),
      section: json['section']?.toString(),
      academicYear: json['academic_year']?.toString(),
      coachingType: json['coaching_type']?.toString(),
      fatherName: json['father_name']?.toString(),
      branchName: branchName,
    );
  }
}

class IndividualReportRow {
  final String category;
  final String subject;
  final String exdate;
  final dynamic phyTot;
  final dynamic cheTot;
  final dynamic botTot;
  final dynamic zooTot;
  final dynamic bioTot;
  final dynamic netTot;

  IndividualReportRow({
    required this.category,
    required this.subject,
    required this.exdate,
    this.phyTot,
    this.cheTot,
    this.botTot,
    this.zooTot,
    this.bioTot,
    this.netTot,
  });

  factory IndividualReportRow.fromJson(Map<String, dynamic> json) {
    return IndividualReportRow(
      category: (json['category'] ?? '').toString(),
      subject: (json['subject'] ?? '').toString(),
      exdate: (json['exdate'] ?? '').toString(),
      phyTot: json['phy_tot'],
      cheTot: json['che_tot'],
      botTot: json['bot_tot'],
      zooTot: json['zoo_tot'],
      bioTot: json['bio_tot'],
      netTot: json['nettot'],
    );
  }

  String get formattedDate {
    if (exdate.isEmpty) return '';
    try {
      final dt = DateTime.parse(exdate);
      return "${dt.day.toString().padLeft(2, '0')}-${dt.month.toString().padLeft(2, '0')}-${dt.year}";
    } catch (_) {
      return exdate;
    }
  }
}

class IndividualReportResponse {
  final IndividualStudentModel student;
  final List<IndividualReportRow> report;
  final Map<String, dynamic> average;
  final int marksCount;

  IndividualReportResponse({
    required this.student,
    required this.report,
    required this.average,
    required this.marksCount,
  });

  factory IndividualReportResponse.fromJson(Map<String, dynamic> json) {
    final studentJson = json['student'] as Map<String, dynamic>? ?? {};
    final reportList = (json['report'] as List? ?? []).map((e) => IndividualReportRow.fromJson(e as Map<String, dynamic>)).toList();
    return IndividualReportResponse(
      student: IndividualStudentModel.fromJson(studentJson),
      report: reportList,
      average: Map<String, dynamic>.from(json['average'] ?? {}),
      marksCount: (json['marks_count'] ?? 0) is int ? json['marks_count'] : int.tryParse(json['marks_count'].toString()) ?? 0,
    );
  }

  Map<String, List<IndividualReportRow>> get groupedByCategory {
    final map = <String, List<IndividualReportRow>>{};
    for (var r in report) {
      map.putIfAbsent(r.category, () => []).add(r);
    }
    return map;
  }
}
