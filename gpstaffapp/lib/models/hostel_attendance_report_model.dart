class HostelAttendanceItemModel {
  final String studentId;
  final String studentName;
  final String course;
  final String coachingType;
  final String section;
  final String roomNo;
  final String date;
  final String morning;
  final String evening;

  HostelAttendanceItemModel({
    this.studentId = '',
    this.studentName = '',
    this.course = '',
    this.coachingType = '',
    this.section = '',
    this.roomNo = '',
    this.date = '',
    this.morning = '-',
    this.evening = '-',
  });

  factory HostelAttendanceItemModel.fromJson(Map<String, dynamic> json) {
    return HostelAttendanceItemModel(
      studentId: (json['student_id'] ?? '').toString(),
      studentName: (json['student_name'] ?? '').toString(),
      course: (json['course'] ?? '').toString(),
      coachingType: (json['coaching_type'] ?? '').toString(),
      section: (json['section'] ?? '').toString(),
      roomNo: (json['room_no'] ?? '').toString(),
      date: (json['date'] ?? '').toString(),
      morning: (json['morning'] ?? '-').toString(),
      evening: (json['evening'] ?? '-').toString(),
    );
  }
}

class HostelItemModel {
  final dynamic id;
  final String name;

  HostelItemModel({required this.id, required this.name});

  factory HostelItemModel.fromJson(Map<String, dynamic> json) {
    return HostelItemModel(
      id: json['id'] ?? '',
      name: (json['name'] ?? json['hostel_name'] ?? '').toString(),
    );
  }
}

class HostelAttendanceReportResponseModel {
  final List<HostelItemModel> hostels;
  final List<String> sections;
  final List<String> rooms;
  final List<HostelAttendanceItemModel> records;
  final String activeTab;

  HostelAttendanceReportResponseModel({
    this.hostels = const [],
    this.sections = const [],
    this.rooms = const [],
    this.records = const [],
    this.activeTab = 'section_tab',
  });

  factory HostelAttendanceReportResponseModel.fromJson(
      Map<String, dynamic> json) {
    List<HostelItemModel> parsedHostels = [];
    if (json['hostels'] is List) {
      parsedHostels = (json['hostels'] as List)
          .map((e) => HostelItemModel.fromJson(
              e is Map<String, dynamic> ? e : {'id': e, 'name': e.toString()}))
          .toList();
    }

    List<String> parsedSections = [];
    if (json['sections'] is List) {
      parsedSections =
          (json['sections'] as List).map((e) => e.toString()).toList();
    }

    List<String> parsedRooms = [];
    if (json['rooms'] is List) {
      parsedRooms = (json['rooms'] as List).map((e) => e.toString()).toList();
    }

    List<HostelAttendanceItemModel> parsedRecords = [];
    if (json['records'] is List) {
      parsedRecords = (json['records'] as List)
          .map((e) => HostelAttendanceItemModel.fromJson(
              e is Map<String, dynamic> ? e : {}))
          .toList();
    }

    return HostelAttendanceReportResponseModel(
      hostels: parsedHostels,
      sections: parsedSections,
      rooms: parsedRooms,
      records: parsedRecords,
      activeTab: json['active_tab']?.toString() ?? 'section_tab',
    );
  }
}
