class UserModel {
  final dynamic id;
  final String username;
  final String? type;
  final dynamic branch;
  final String? branchIds;
  final String? email;
  final String? mobile;
  final Map<String, dynamic>? rawJson;

  UserModel({
    required this.id,
    required this.username,
    this.type,
    this.branch,
    this.branchIds,
    this.email,
    this.mobile,
    this.rawJson,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? json['user_id'] ?? json['student_id'] ?? '',
      username:
          (json['username'] ?? json['user_name'] ?? json['name'] ?? 'User')
              .toString(),
      type: json['type']?.toString() ?? json['role']?.toString() ?? 'staff',
      branch: json['branch'],
      branchIds: json['branch_ids']?.toString(),
      email: json['email']?.toString(),
      mobile: json['mobile']?.toString(),
      rawJson: json,
    );
  }

  Map<String, dynamic> toJson() {
    if (rawJson != null) return rawJson!;
    return {
      'id': id,
      'username': username,
      'type': type,
      'branch': branch,
      'branch_ids': branchIds,
      'email': email,
      'mobile': mobile,
    };
  }

  bool get isAdmin => (type ?? '').toLowerCase().trim() == 'admin';
  bool get isBranchAdmin => (type ?? '').toLowerCase().trim() == 'branch admin';
  bool get isStaff => !isAdmin && !isBranchAdmin;
}
