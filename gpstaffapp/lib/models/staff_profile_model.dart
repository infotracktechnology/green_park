class StaffProfileModel {
  final dynamic id;
  final String name;
  final String? schoolInitial;
  final String? staffType;
  final dynamic branchId;
  final String? branchName;
  final dynamic shiftId;
  final String? shiftName;
  final String? hostelDayscholar;
  final String? gender;
  final String? dob;
  final String? age;
  final String? bloodGroup;
  final String? department;
  final String? qualifications;
  final String? nationality;
  final String? religion;
  final String? community;
  final String? caste;
  final String? mobNo;
  final String? alternateMobNo;
  final String? aadhaarNo;
  final String? email;
  final String? addressLine1;
  final String? addressLine2;
  final String? state;
  final String? city;
  final String? pincode;
  final String? photo;
  final String? biometricNo;
  final String? maritalStatus;
  final String? fatherName;
  final String? motherName;
  final String? fatherPhNo;
  final String? spouseName;
  final String? spousePhNo;
  final String? spouseOccupation;
  final String? dateOfJoining;
  final String? designation;
  final String? experience;
  final String? classHandlingType;
  final String? paperCorrection;
  final String? handelingClass;
  final String? previousSchool;
  final dynamic classAssign;
  final dynamic subAssign;
  final dynamic childrenDetails;

  StaffProfileModel({
    required this.id,
    required this.name,
    this.schoolInitial,
    this.staffType,
    this.branchId,
    this.branchName,
    this.shiftId,
    this.shiftName,
    this.hostelDayscholar,
    this.gender,
    this.dob,
    this.age,
    this.bloodGroup,
    this.department,
    this.qualifications,
    this.nationality,
    this.religion,
    this.community,
    this.caste,
    this.mobNo,
    this.alternateMobNo,
    this.aadhaarNo,
    this.email,
    this.addressLine1,
    this.addressLine2,
    this.state,
    this.city,
    this.pincode,
    this.photo,
    this.biometricNo,
    this.maritalStatus,
    this.fatherName,
    this.motherName,
    this.fatherPhNo,
    this.spouseName,
    this.spousePhNo,
    this.spouseOccupation,
    this.dateOfJoining,
    this.designation,
    this.experience,
    this.classHandlingType,
    this.paperCorrection,
    this.handelingClass,
    this.previousSchool,
    this.classAssign,
    this.subAssign,
    this.childrenDetails,
  });

  factory StaffProfileModel.fromJson(Map<String, dynamic> json) {
    String? bName;
    if (json['branch'] is Map) {
      bName = json['branch']['name']?.toString();
    } else if (json['branch_name'] != null) {
      bName = json['branch_name']?.toString();
    }

    String? sName;
    if (json['shift'] is Map) {
      sName = json['shift']['shift_name']?.toString();
    } else if (json['shift_name'] != null) {
      sName = json['shift_name']?.toString();
    }

    return StaffProfileModel(
      id: json['id'] ?? '',
      name: (json['name'] ?? json['username'] ?? 'Staff').toString(),
      schoolInitial: json['school_initial']?.toString(),
      staffType: json['staff_type']?.toString(),
      branchId: json['branch_id'],
      branchName: bName,
      shiftId: json['shiftid'],
      shiftName: sName,
      hostelDayscholar: json['hostel_dayscholar']?.toString(),
      gender: json['gender']?.toString(),
      dob: json['dob']?.toString(),
      age: json['age']?.toString(),
      bloodGroup: json['blood_group']?.toString(),
      department: json['department']?.toString(),
      qualifications: json['qualifications']?.toString(),
      nationality: json['nationality']?.toString(),
      religion: json['religion']?.toString(),
      community: json['community']?.toString(),
      caste: json['caste']?.toString(),
      mobNo: json['mob_no']?.toString(),
      alternateMobNo: json['alternate_mob_no']?.toString(),
      aadhaarNo: json['aadhaar_no']?.toString(),
      email: json['email']?.toString(),
      addressLine1: json['address_line_1']?.toString(),
      addressLine2: json['address_line_2']?.toString(),
      state: (json['state'] ?? json['State'])?.toString(),
      city: json['city']?.toString(),
      pincode: json['pincode']?.toString(),
      photo: json['photo']?.toString(),
      biometricNo: json['biometric_no']?.toString(),
      maritalStatus: json['marital_status']?.toString(),
      fatherName: json['father_name']?.toString(),
      motherName: json['mother_name']?.toString(),
      fatherPhNo: json['father_ph_no']?.toString(),
      spouseName: json['spouse_name']?.toString(),
      spousePhNo: json['spouse_ph_no']?.toString(),
      spouseOccupation: json['spouse_occupation']?.toString(),
      dateOfJoining: json['date_of_joining']?.toString(),
      designation: json['designation']?.toString(),
      experience: json['experience']?.toString(),
      classHandlingType: json['class_handling_type']?.toString(),
      paperCorrection: json['paper_correction']?.toString(),
      handelingClass: json['handeling_class']?.toString(),
      previousSchool: json['previous_school']?.toString(),
      classAssign: json['class_assign'],
      subAssign: json['sub_assign'],
      childrenDetails: json['children_details'],
    );
  }
}
