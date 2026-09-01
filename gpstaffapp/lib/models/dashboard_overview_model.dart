class DashboardOverviewModel {
  final bool status;
  final String academicYear;
  final OverviewStatsModel overview;
  final List<BranchStrengthModel> branchWise;
  final List<CourseStrengthModel> courseWise;
  final List<CoachingTypeStrengthModel> coachingTypeWise;
  final List<SectionStrengthModel> sectionWise;
  final List<BatchStrengthModel> batchWise;

  DashboardOverviewModel({
    required this.status,
    required this.academicYear,
    required this.overview,
    required this.branchWise,
    required this.courseWise,
    required this.coachingTypeWise,
    required this.sectionWise,
    required this.batchWise,
  });

  factory DashboardOverviewModel.fromJson(Map<String, dynamic> json) {
    return DashboardOverviewModel(
      status: json['status'] == true,
      academicYear: json['academic_year']?.toString() ?? '',
      overview: OverviewStatsModel.fromJson(
          json['overview'] is Map<String, dynamic> ? json['overview'] : {}),
      branchWise: (json['branch_wise'] as List?)
              ?.map((e) =>
                  BranchStrengthModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      courseWise: (json['course_wise'] as List?)
              ?.map((e) =>
                  CourseStrengthModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      coachingTypeWise: (json['coaching_type_wise'] as List?)
              ?.map((e) =>
                  CoachingTypeStrengthModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      sectionWise: (json['section_wise'] as List?)
              ?.map((e) =>
                  SectionStrengthModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      batchWise: (json['batch_wise'] as List?)
              ?.map((e) =>
                  BatchStrengthModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class OverviewStatsModel {
  final int totalStudents;
  final int boys;
  final int girls;
  final int presentToday;
  final int absentToday;
  final double attendancePercentage;
  final double boysPercentage;
  final double girlsPercentage;
  final LoginStatsModel loginToday;

  OverviewStatsModel({
    this.totalStudents = 0,
    this.boys = 0,
    this.girls = 0,
    this.presentToday = 0,
    this.absentToday = 0,
    this.attendancePercentage = 0.0,
    this.boysPercentage = 0.0,
    this.girlsPercentage = 0.0,
    required this.loginToday,
  });

  factory OverviewStatsModel.fromJson(Map<String, dynamic> json) {
    return OverviewStatsModel(
      totalStudents: (json['total_students'] as num?)?.toInt() ?? 0,
      boys: (json['boys'] as num?)?.toInt() ?? 0,
      girls: (json['girls'] as num?)?.toInt() ?? 0,
      presentToday: (json['present_today'] as num?)?.toInt() ?? 0,
      absentToday: (json['absent_today'] as num?)?.toInt() ?? 0,
      attendancePercentage:
          (json['attendance_percentage'] as num?)?.toDouble() ?? 0.0,
      boysPercentage: (json['boys_percentage'] as num?)?.toDouble() ?? 0.0,
      girlsPercentage: (json['girls_percentage'] as num?)?.toDouble() ?? 0.0,
      loginToday: LoginStatsModel.fromJson(
          json['login_today'] is Map<String, dynamic>
              ? json['login_today']
              : {}),
    );
  }
}

class LoginStatsModel {
  final int total;
  final int web;
  final int android;
  final int ios;

  LoginStatsModel({
    this.total = 0,
    this.web = 0,
    this.android = 0,
    this.ios = 0,
  });

  factory LoginStatsModel.fromJson(Map<String, dynamic> json) {
    return LoginStatsModel(
      total: (json['total'] as num?)?.toInt() ?? 0,
      web: (json['web'] as num?)?.toInt() ?? 0,
      android: (json['android'] as num?)?.toInt() ?? 0,
      ios: (json['ios'] as num?)?.toInt() ?? 0,
    );
  }
}

class BranchStrengthModel {
  final dynamic id;
  final String name;
  final int total;
  final int offline;
  final int online;
  final int boys;
  final int girls;
  final int present;
  final int absent;
  final int loginWeb;
  final int loginAndroid;
  final int loginIos;
  final int loginTotal;
  final List<BranchSectionStrengthModel> sections;

  BranchStrengthModel({
    required this.id,
    required this.name,
    this.total = 0,
    this.offline = 0,
    this.online = 0,
    this.boys = 0,
    this.girls = 0,
    this.present = 0,
    this.absent = 0,
    this.loginWeb = 0,
    this.loginAndroid = 0,
    this.loginIos = 0,
    this.loginTotal = 0,
    required this.sections,
  });

  factory BranchStrengthModel.fromJson(Map<String, dynamic> json) {
    return BranchStrengthModel(
      id: json['id'],
      name: json['name']?.toString() ?? '',
      total: (json['total'] as num?)?.toInt() ?? 0,
      offline: (json['offline'] as num?)?.toInt() ?? 0,
      online: (json['online'] as num?)?.toInt() ?? 0,
      boys: (json['boys'] as num?)?.toInt() ?? 0,
      girls: (json['girls'] as num?)?.toInt() ?? 0,
      present: (json['present'] as num?)?.toInt() ?? 0,
      absent: (json['absent'] as num?)?.toInt() ?? 0,
      loginWeb: (json['login_web'] as num?)?.toInt() ?? 0,
      loginAndroid: (json['login_android'] as num?)?.toInt() ?? 0,
      loginIos: (json['login_ios'] as num?)?.toInt() ?? 0,
      loginTotal: (json['login_total'] as num?)?.toInt() ?? 0,
      sections: (json['sections'] as List?)
              ?.map((e) =>
                  BranchSectionStrengthModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class BranchSectionStrengthModel {
  final String section;
  final int total;
  final int offline;
  final int online;
  final int present;
  final int absent;

  BranchSectionStrengthModel({
    required this.section,
    this.total = 0,
    this.offline = 0,
    this.online = 0,
    this.present = 0,
    this.absent = 0,
  });

  factory BranchSectionStrengthModel.fromJson(Map<String, dynamic> json) {
    return BranchSectionStrengthModel(
      section: json['section']?.toString() ?? '',
      total: (json['total'] as num?)?.toInt() ?? 0,
      offline: (json['offline'] as num?)?.toInt() ?? 0,
      online: (json['online'] as num?)?.toInt() ?? 0,
      present: (json['present'] as num?)?.toInt() ?? 0,
      absent: (json['absent'] as num?)?.toInt() ?? 0,
    );
  }
}

class CourseStrengthModel {
  final String course;
  final int total;
  final int boys;
  final int girls;
  final int offline;
  final int online;

  CourseStrengthModel({
    required this.course,
    this.total = 0,
    this.boys = 0,
    this.girls = 0,
    this.offline = 0,
    this.online = 0,
  });

  factory CourseStrengthModel.fromJson(Map<String, dynamic> json) {
    return CourseStrengthModel(
      course: json['course']?.toString() ?? '',
      total: (json['total'] as num?)?.toInt() ?? 0,
      boys: (json['boys'] as num?)?.toInt() ?? 0,
      girls: (json['girls'] as num?)?.toInt() ?? 0,
      offline: (json['offline'] as num?)?.toInt() ?? 0,
      online: (json['online'] as num?)?.toInt() ?? 0,
    );
  }
}

class CoachingTypeStrengthModel {
  final String coachingType;
  final int total;
  final int boys;
  final int girls;

  CoachingTypeStrengthModel({
    required this.coachingType,
    this.total = 0,
    this.boys = 0,
    this.girls = 0,
  });

  factory CoachingTypeStrengthModel.fromJson(Map<String, dynamic> json) {
    return CoachingTypeStrengthModel(
      coachingType: json['coaching_type']?.toString() ?? '',
      total: (json['total'] as num?)?.toInt() ?? 0,
      boys: (json['boys'] as num?)?.toInt() ?? 0,
      girls: (json['girls'] as num?)?.toInt() ?? 0,
    );
  }
}

class SectionStrengthModel {
  final String section;
  final int total;
  final int offline;
  final int online;
  final int boys;
  final int girls;

  SectionStrengthModel({
    required this.section,
    this.total = 0,
    this.offline = 0,
    this.online = 0,
    this.boys = 0,
    this.girls = 0,
  });

  factory SectionStrengthModel.fromJson(Map<String, dynamic> json) {
    return SectionStrengthModel(
      section: json['section']?.toString() ?? '',
      total: (json['total'] as num?)?.toInt() ?? 0,
      offline: (json['offline'] as num?)?.toInt() ?? 0,
      online: (json['online'] as num?)?.toInt() ?? 0,
      boys: (json['boys'] as num?)?.toInt() ?? 0,
      girls: (json['girls'] as num?)?.toInt() ?? 0,
    );
  }
}

class BatchStrengthModel {
  final String batch;
  final int total;
  final int boys;
  final int girls;
  final int offline;
  final int online;

  BatchStrengthModel({
    required this.batch,
    this.total = 0,
    this.boys = 0,
    this.girls = 0,
    this.offline = 0,
    this.online = 0,
  });

  factory BatchStrengthModel.fromJson(Map<String, dynamic> json) {
    return BatchStrengthModel(
      batch: json['batch']?.toString() ?? '',
      total: (json['total'] as num?)?.toInt() ?? 0,
      boys: (json['boys'] as num?)?.toInt() ?? 0,
      girls: (json['girls'] as num?)?.toInt() ?? 0,
      offline: (json['offline'] as num?)?.toInt() ?? 0,
      online: (json['online'] as num?)?.toInt() ?? 0,
    );
  }
}
