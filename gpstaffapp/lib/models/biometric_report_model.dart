class BiometricStaffModel {
  final String branch;
  final String department;
  final String schoolInitial;
  final String name;
  final String biometricNo;
  final String firstIn;
  final String lastOut;
  final String session1;
  final String session2;
  final String timeLogs;
  final String date;
  final dynamic hours;
  final dynamic day;

  BiometricStaffModel({
    this.branch = '',
    this.department = '',
    this.schoolInitial = '',
    this.name = '',
    this.biometricNo = '',
    this.firstIn = '-',
    this.lastOut = '-',
    this.session1 = 'A',
    this.session2 = 'A',
    this.timeLogs = '-',
    this.date = '',
    this.hours = 0,
    this.day = 0.0,
  });

  factory BiometricStaffModel.fromJson(Map<String, dynamic> json) {
    return BiometricStaffModel(
      branch: (json['branch'] ?? '').toString(),
      department: (json['department'] ?? '').toString(),
      schoolInitial: (json['school_initial'] ?? '').toString(),
      name: (json['name'] ?? '').toString(),
      biometricNo: (json['biometric_no'] ?? '').toString(),
      firstIn: (json['first_in'] ?? '-').toString(),
      lastOut: (json['last_out'] ?? '-').toString(),
      session1: (json['session1'] ?? 'A').toString(),
      session2: (json['session2'] ?? 'A').toString(),
      timeLogs: (json['time_logs'] ?? '-').toString(),
      date: (json['date'] ?? '').toString(),
      hours: json['hours'] ?? 0,
      day: json['day'] ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'branch': branch,
      'department': department,
      'school_initial': schoolInitial,
      'name': name,
      'biometric_no': biometricNo,
      'first_in': firstIn,
      'last_out': lastOut,
      'session1': session1,
      'session2': session2,
      'time_logs': timeLogs,
      'date': date,
      'hours': hours,
      'day': day,
    };
  }
}
