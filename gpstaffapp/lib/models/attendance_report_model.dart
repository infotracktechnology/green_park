class AttendanceSectionItemModel {
  final String section;
  final int boys;
  final int girls;
  final int total;
  final int present;
  final int absent;
  final List<String> presentStudents;
  final List<String> absentStudents;

  AttendanceSectionItemModel({
    this.section = '',
    this.boys = 0,
    this.girls = 0,
    this.total = 0,
    this.present = 0,
    this.absent = 0,
    this.presentStudents = const [],
    this.absentStudents = const [],
  });

  double get presentPercent => total > 0 ? (present * 100 / total) : 0.0;
  double get absentPercent => total > 0 ? (absent * 100 / total) : 0.0;

  factory AttendanceSectionItemModel.fromJson(Map<String, dynamic> json) {
    List<String> parseStudentList(dynamic val) {
      if (val is List) {
        return val.map((e) => e.toString()).toList();
      }
      return [];
    }

    return AttendanceSectionItemModel(
      section: (json['section'] ?? '').toString(),
      boys: int.tryParse(json['boys']?.toString() ?? '0') ?? 0,
      girls: int.tryParse(json['girls']?.toString() ?? '0') ?? 0,
      total: int.tryParse(json['total']?.toString() ?? '0') ?? 0,
      present: int.tryParse(json['present']?.toString() ?? '0') ?? 0,
      absent: int.tryParse(json['absent']?.toString() ?? '0') ?? 0,
      presentStudents: parseStudentList(json['present_students']),
      absentStudents: parseStudentList(json['absent_students']),
    );
  }
}

class AttendanceReportResponseModel {
  final List<String> courses;
  final List<String> sections;
  final List<AttendanceSectionItemModel> attendances;

  AttendanceReportResponseModel({
    this.courses = const [],
    this.sections = const [],
    this.attendances = const [],
  });

  factory AttendanceReportResponseModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedCourses = [];
    if (json['courses'] is List) {
      parsedCourses =
          (json['courses'] as List).map((e) => e.toString()).toList();
    }

    List<String> parsedSections = [];
    if (json['sections'] is List) {
      parsedSections =
          (json['sections'] as List).map((e) => e.toString()).toList();
    }

    List<AttendanceSectionItemModel> parsedAttendances = [];
    if (json['attendances'] is List) {
      parsedAttendances = (json['attendances'] as List)
          .map((e) => AttendanceSectionItemModel.fromJson(
              e is Map<String, dynamic> ? e : {}))
          .toList();
    }

    return AttendanceReportResponseModel(
      courses: parsedCourses,
      sections: parsedSections,
      attendances: parsedAttendances,
    );
  }
}
