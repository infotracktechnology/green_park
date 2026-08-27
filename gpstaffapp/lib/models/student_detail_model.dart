class StudentDetailModel {
  final dynamic id;
  final String studentId;
  final String studentName;
  final String userName;
  final String? password;
  final dynamic campus;
  final String? campusName;
  final String? course;
  final String? coachingType;
  final String? hostelDayscholar;
  final String? acNonac;
  final String? section;
  final String? batch;
  final String? gender;
  final String? dob;
  final String? age;
  final String? admissionDate;
  final String? aadharCardNo;
  final String? nationality;
  final String? religion;
  final String? community;
  final String? caste;
  final String? bloodGroup;
  final String? studentWhatsappNo;
  final String? phNo1;
  final String? phNo2;
  final String? fatherName;
  final String? fatherPhNo;
  final String? motherName;
  final String? motherPhNo;
  final String? institutionBillType;
  final String? teamsId;
  final String? teamsPassword;
  final String? description;
  final String? photo;

  StudentDetailModel({
    required this.id,
    required this.studentId,
    required this.studentName,
    this.userName = '',
    this.password,
    this.campus,
    this.campusName,
    this.course,
    this.coachingType,
    this.hostelDayscholar,
    this.acNonac,
    this.section,
    this.batch,
    this.gender,
    this.dob,
    this.age,
    this.admissionDate,
    this.aadharCardNo,
    this.nationality,
    this.religion,
    this.community,
    this.caste,
    this.bloodGroup,
    this.studentWhatsappNo,
    this.phNo1,
    this.phNo2,
    this.fatherName,
    this.fatherPhNo,
    this.motherName,
    this.motherPhNo,
    this.institutionBillType,
    this.teamsId,
    this.teamsPassword,
    this.description,
    this.photo,
  });

  factory StudentDetailModel.fromJson(Map<String, dynamic> json) {
    String? bName;
    if (json['branch'] is Map) {
      bName = json['branch']['name']?.toString();
    }

    return StudentDetailModel(
      id: json['id'] ?? '',
      studentId: (json['student_id'] ?? '').toString(),
      studentName: (json['student_name'] ?? json['name'] ?? '').toString(),
      userName: (json['user_name'] ?? json['username'] ?? '').toString(),
      password: json['password']?.toString(),
      campus: json['campus'],
      campusName: bName ?? json['branch_name']?.toString(),
      course: json['course']?.toString(),
      coachingType: json['coaching_type']?.toString(),
      hostelDayscholar: json['hostel_dayscholar']?.toString(),
      acNonac: json['ac_nonac']?.toString(),
      section: json['section']?.toString(),
      batch: json['batch']?.toString(),
      gender: json['gender']?.toString(),
      dob: json['dob']?.toString(),
      age: json['age']?.toString(),
      admissionDate: json['admission_date']?.toString(),
      aadharCardNo: json['aadhar_card_no']?.toString(),
      nationality: json['nationality']?.toString(),
      religion: json['religion']?.toString(),
      community: json['community']?.toString(),
      caste: json['caste']?.toString(),
      bloodGroup: json['blood_group']?.toString(),
      studentWhatsappNo:
          (json['student_whatsapp_no'] ?? json['ph_no1'])?.toString(),
      phNo1: (json['ph_no1'] ?? json['student_whatsapp_no'])?.toString(),
      phNo2: json['ph_no2']?.toString(),
      fatherName: json['father_name']?.toString(),
      fatherPhNo: (json['father_ph_no'] ?? json['father_mobile'])?.toString(),
      motherName: json['mother_name']?.toString(),
      motherPhNo: (json['mother_ph_no'] ?? json['mother_mobile'])?.toString(),
      institutionBillType: json['institution_bill_type']?.toString(),
      teamsId: json['teams_id']?.toString(),
      teamsPassword: json['teams_password']?.toString(),
      description: json['description']?.toString(),
      photo: json['photo']?.toString(),
    );
  }

  Map<String, dynamic> toUpdateJson() {
    return {
      'admission_date': admissionDate,
      'campus': campus,
      'course': course,
      'student_name': studentName,
      'password': password,
      'coaching_type': coachingType,
      'hostel_dayscholar': hostelDayscholar,
      'ac_nonac': acNonac,
      'section': section,
      'batch': batch,
      'gender': gender,
      'dob': dob,
      'age': age,
      'aadhar_card_no': aadharCardNo,
      'nationality': nationality,
      'religion': religion,
      'community': community,
      'caste': caste,
      'blood_group': bloodGroup,
      'student_whatsapp_no': studentWhatsappNo,
      'ph_no1': phNo1 ?? studentWhatsappNo,
      'ph_no2': phNo2,
      'father_ph_no': fatherPhNo,
      'mother_ph_no': motherPhNo,
      'institution_bill_type': institutionBillType,
      'teams_id': teamsId,
      'teams_password': teamsPassword,
      'description': description,
    };
  }
}
