class StaffLeaveResponseModel {
  final bool status;
  final bool isApprover;
  final StaffLeaveProfileModel? currentStaff;
  final StaffLeaveSummaryModel summary;
  final List<String> leaveTypes;
  final List<String> sessions;
  final List<StaffLeaveModel> leaves;

  StaffLeaveResponseModel({
    required this.status,
    this.isApprover = false,
    this.currentStaff,
    required this.summary,
    required this.leaveTypes,
    required this.sessions,
    required this.leaves,
  });

  factory StaffLeaveResponseModel.fromJson(Map<String, dynamic> json) {
    return StaffLeaveResponseModel(
      status: json['status'] == true,
      isApprover: json['is_approver'] == true,
      currentStaff: json['current_staff'] is Map<String, dynamic>
          ? StaffLeaveProfileModel.fromJson(json['current_staff'])
          : null,
      summary: StaffLeaveSummaryModel.fromJson(
          json['summary'] is Map<String, dynamic> ? json['summary'] : {}),
      leaveTypes: (json['leave_types'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      sessions: (json['sessions'] as List?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      leaves: (json['leaves'] as List?)
              ?.map((e) => StaffLeaveModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class StaffLeaveProfileModel {
  final int id;
  final String name;
  final String? biometricNo;
  final String? department;
  final dynamic branchId;

  StaffLeaveProfileModel({
    required this.id,
    required this.name,
    this.biometricNo,
    this.department,
    this.branchId,
  });

  factory StaffLeaveProfileModel.fromJson(Map<String, dynamic> json) {
    return StaffLeaveProfileModel(
      id: (json['id'] as num?)?.toInt() ?? 0,
      name: json['name']?.toString() ?? '',
      biometricNo: json['biometric_no']?.toString(),
      department: json['department']?.toString(),
      branchId: json['branch_id'],
    );
  }
}

class StaffLeaveSummaryModel {
  final int totalRequests;
  final int pending;
  final int approved;
  final int rejected;
  final double totalApprovedDays;

  StaffLeaveSummaryModel({
    this.totalRequests = 0,
    this.pending = 0,
    this.approved = 0,
    this.rejected = 0,
    this.totalApprovedDays = 0.0,
  });

  factory StaffLeaveSummaryModel.fromJson(Map<String, dynamic> json) {
    return StaffLeaveSummaryModel(
      totalRequests: (json['total_requests'] as num?)?.toInt() ?? 0,
      pending: (json['pending'] as num?)?.toInt() ?? 0,
      approved: (json['approved'] as num?)?.toInt() ?? 0,
      rejected: (json['rejected'] as num?)?.toInt() ?? 0,
      totalApprovedDays:
          (json['total_approved_days'] as num?)?.toDouble() ?? 0.0,
    );
  }
}

class StaffLeaveModel {
  final int id;
  final int staffId;
  final String staffName;
  final String biometricNo;
  final String department;
  final String designation;
  final String branchName;
  final String leaveType;
  final String? fromDate;
  final String? toDate;
  final String fromDateFormatted;
  final String toDateFormatted;
  final double days;
  final String session;
  final String reason;
  final String status;
  final String approvedBy;
  final String? approvedAt;
  final String? rejectionReason;
  final String createdAt;

  StaffLeaveModel({
    required this.id,
    required this.staffId,
    required this.staffName,
    required this.biometricNo,
    required this.department,
    required this.designation,
    required this.branchName,
    required this.leaveType,
    this.fromDate,
    this.toDate,
    required this.fromDateFormatted,
    required this.toDateFormatted,
    required this.days,
    required this.session,
    required this.reason,
    required this.status,
    required this.approvedBy,
    this.approvedAt,
    this.rejectionReason,
    required this.createdAt,
  });

  factory StaffLeaveModel.fromJson(Map<String, dynamic> json) {
    return StaffLeaveModel(
      id: (json['id'] as num?)?.toInt() ?? 0,
      staffId: (json['staff_id'] as num?)?.toInt() ?? 0,
      staffName: json['staff_name']?.toString() ?? 'Staff',
      biometricNo: json['biometric_no']?.toString() ?? '-',
      department: json['department']?.toString() ?? '-',
      designation: json['designation']?.toString() ?? '-',
      branchName: json['branch_name']?.toString() ?? '-',
      leaveType: json['leave_type']?.toString() ?? '',
      fromDate: json['from_date']?.toString(),
      toDate: json['to_date']?.toString(),
      fromDateFormatted: json['from_date_formatted']?.toString() ?? '-',
      toDateFormatted: json['to_date_formatted']?.toString() ?? '-',
      days: (json['days'] as num?)?.toDouble() ?? 1.0,
      session: json['session']?.toString() ?? 'Full Day',
      reason: json['reason']?.toString() ?? '',
      status: json['status']?.toString() ?? 'Pending',
      approvedBy: json['approved_by']?.toString() ?? '-',
      approvedAt: json['approved_at']?.toString(),
      rejectionReason: json['rejection_reason']?.toString(),
      createdAt: json['created_at']?.toString() ?? '-',
    );
  }
}
