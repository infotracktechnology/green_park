class StudentAttendanceItem {
  final dynamic id;
  final String studentId;
  final String studentName;
  final String? academicYear;
  final String? coachingType;
  final String? section;
  final String? gender;
  String morningStatus;
  String afternoonStatus;
  dynamic morningId;
  dynamic afternoonId;
  bool hasMorningEntry;
  bool hasAfternoonEntry;

  StudentAttendanceItem({
    required this.id,
    required this.studentId,
    required this.studentName,
    this.academicYear,
    this.coachingType,
    this.section,
    this.gender,
    this.morningStatus = 'P',
    this.afternoonStatus = 'P',
    this.morningId,
    this.afternoonId,
    this.hasMorningEntry = false,
    this.hasAfternoonEntry = false,
  });

  factory StudentAttendanceItem.fromJson(Map<String, dynamic> json) {
    return StudentAttendanceItem(
      id: json['id'] ?? '',
      studentId: (json['student_id'] ?? '').toString(),
      studentName: (json['student_name'] ?? '').toString(),
      academicYear: json['academic_year']?.toString(),
      coachingType: json['coaching_type']?.toString(),
      section: json['section']?.toString(),
      gender: json['gender']?.toString(),
      morningStatus: json['morning_status']?.toString() ?? 'P',
      afternoonStatus: json['afternoon_status']?.toString() ?? 'P',
      morningId: json['morning_id'],
      afternoonId: json['afternoon_id'],
      hasMorningEntry: json['has_morning_entry'] == true,
      hasAfternoonEntry: json['has_afternoon_entry'] == true,
    );
  }
}

class StudentAttendanceEntryResponse {
  final bool status;
  final String? academicYear;
  final dynamic branchId;
  final List<String> handlingSections;
  final List<String> allSections;
  final List<String> sections;
  final String? selectedSection;
  final String? attendanceDate;
  final String? attendanceTiming;
  final bool isHoliday;
  final List<StudentAttendanceItem> students;

  StudentAttendanceEntryResponse({
    required this.status,
    this.academicYear,
    this.branchId,
    required this.handlingSections,
    required this.allSections,
    required this.sections,
    this.selectedSection,
    this.attendanceDate,
    this.attendanceTiming,
    this.isHoliday = false,
    required this.students,
  });

  factory StudentAttendanceEntryResponse.fromJson(Map<String, dynamic> json) {
    List<String> parseStringList(dynamic list) {
      if (list is List) {
        return list.map((e) => e.toString().trim()).where((e) => e.isNotEmpty).toList();
      }
      return [];
    }

    final studentsList = <StudentAttendanceItem>[];
    if (json['students'] is List) {
      for (final item in json['students']) {
        if (item is Map<String, dynamic>) {
          studentsList.add(StudentAttendanceItem.fromJson(item));
        }
      }
    }

    return StudentAttendanceEntryResponse(
      status: json['status'] == true,
      academicYear: json['academic_year']?.toString(),
      branchId: json['branch_id'],
      handlingSections: parseStringList(json['handling_sections']),
      allSections: parseStringList(json['all_sections']),
      sections: parseStringList(json['sections']),
      selectedSection: json['selected_section']?.toString(),
      attendanceDate: json['attendance_date']?.toString(),
      attendanceTiming: json['attendance_timing']?.toString(),
      isHoliday: json['is_holiday'] == true,
      students: studentsList,
    );
  }
}
