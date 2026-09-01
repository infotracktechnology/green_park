class IndividualBiometricResponseModel {
  final bool status;
  final String? message;
  final IndividualBiometricStaffInfo? staff;
  final IndividualBiometricSummary summary;
  final List<IndividualBiometricDayLog> logs;

  IndividualBiometricResponseModel({
    required this.status,
    this.message,
    this.staff,
    required this.summary,
    required this.logs,
  });

  factory IndividualBiometricResponseModel.fromJson(Map<String, dynamic> json) {
    return IndividualBiometricResponseModel(
      status: json['status'] == true,
      message: json['message']?.toString(),
      staff: json['staff'] is Map<String, dynamic>
          ? IndividualBiometricStaffInfo.fromJson(json['staff'])
          : null,
      summary: IndividualBiometricSummary.fromJson(
          json['summary'] is Map<String, dynamic> ? json['summary'] : {}),
      logs: (json['logs'] as List?)
              ?.map((e) =>
                  IndividualBiometricDayLog.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class IndividualBiometricStaffInfo {
  final String name;
  final String biometricNo;
  final String department;
  final String designation;
  final String branch;

  IndividualBiometricStaffInfo({
    required this.name,
    required this.biometricNo,
    required this.department,
    required this.designation,
    required this.branch,
  });

  factory IndividualBiometricStaffInfo.fromJson(Map<String, dynamic> json) {
    return IndividualBiometricStaffInfo(
      name: json['name']?.toString() ?? '',
      biometricNo: json['biometric_no']?.toString() ?? '-',
      department: json['department']?.toString() ?? '-',
      designation: json['designation']?.toString() ?? '-',
      branch: json['branch']?.toString() ?? '-',
    );
  }
}

class IndividualBiometricSummary {
  final String month;
  final String monthKey;
  final int totalDaysEvaluated;
  final int presentDays;
  final int halfDays;
  final int absentDays;
  final double totalPresentCount;
  final double totalHours;

  IndividualBiometricSummary({
    this.month = '',
    this.monthKey = '',
    this.totalDaysEvaluated = 0,
    this.presentDays = 0,
    this.halfDays = 0,
    this.absentDays = 0,
    this.totalPresentCount = 0.0,
    this.totalHours = 0.0,
  });

  factory IndividualBiometricSummary.fromJson(Map<String, dynamic> json) {
    return IndividualBiometricSummary(
      month: json['month']?.toString() ?? '',
      monthKey: json['month_key']?.toString() ?? '',
      totalDaysEvaluated:
          (json['total_days_evaluated'] as num?)?.toInt() ?? 0,
      presentDays: (json['present_days'] as num?)?.toInt() ?? 0,
      halfDays: (json['half_days'] as num?)?.toInt() ?? 0,
      absentDays: (json['absent_days'] as num?)?.toInt() ?? 0,
      totalPresentCount:
          (json['total_present_count'] as num?)?.toDouble() ?? 0.0,
      totalHours: (json['total_hours'] as num?)?.toDouble() ?? 0.0,
    );
  }
}

class IndividualBiometricDayLog {
  final String date;
  final String dateFormatted;
  final String dayName;
  final bool isToday;
  final String firstIn;
  final String lastOut;
  final String session1;
  final String session2;
  final double hours;
  final double day;
  final String status;
  final String timeLogs;

  IndividualBiometricDayLog({
    required this.date,
    required this.dateFormatted,
    required this.dayName,
    this.isToday = false,
    required this.firstIn,
    required this.lastOut,
    required this.session1,
    required this.session2,
    required this.hours,
    required this.day,
    required this.status,
    required this.timeLogs,
  });

  factory IndividualBiometricDayLog.fromJson(Map<String, dynamic> json) {
    return IndividualBiometricDayLog(
      date: json['date']?.toString() ?? '',
      dateFormatted: json['date_formatted']?.toString() ?? '-',
      dayName: json['day_name']?.toString() ?? '',
      isToday: json['is_today'] == true,
      firstIn: json['first_in']?.toString() ?? '-',
      lastOut: json['last_out']?.toString() ?? '-',
      session1: json['session1']?.toString() ?? 'A',
      session2: json['session2']?.toString() ?? 'A',
      hours: (json['hours'] as num?)?.toDouble() ?? 0.0,
      day: (json['day'] as num?)?.toDouble() ?? 0.0,
      status: json['status']?.toString() ?? 'Absent',
      timeLogs: json['time_logs']?.toString() ?? '-',
    );
  }
}
