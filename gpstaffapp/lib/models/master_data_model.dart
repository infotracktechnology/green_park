class BranchItem {
  final dynamic id;
  final String name;

  BranchItem({required this.id, required this.name});

  factory BranchItem.fromDynamic(dynamic item) {
    if (item is Map<String, dynamic>) {
      return BranchItem(
        id: item['id'] ?? item['branch_id'] ?? item['value'] ?? '',
        name: (item['name'] ??
                item['branch_name'] ??
                item['label'] ??
                item['id'] ??
                '')
            .toString(),
      );
    }
    return BranchItem(id: item, name: item.toString());
  }
}

class MasterDataModel {
  final String activeAcademicYear;
  final List<String> courses;
  final List<BranchItem> branches;
  final List<String> coachingTypes;
  final List<String> hostels;
  final List<String> batches;
  final List<String> sections;

  MasterDataModel({
    required this.activeAcademicYear,
    required this.courses,
    required this.branches,
    required this.coachingTypes,
    required this.hostels,
    required this.batches,
    this.sections = const [],
  });

  factory MasterDataModel.fromJson(Map<String, dynamic> json) {
    String academicYear = '';
    if (json['academicyear'] != null) {
      if (json['academicyear'] is Map) {
        academicYear = (json['academicyear']['academic_year'] ?? '').toString();
      } else {
        academicYear = json['academicyear'].toString();
      }
    }

    List<String> coursesList = [];
    if (json['course'] is List) {
      coursesList = (json['course'] as List).map((e) => e.toString()).toList();
    }

    List<BranchItem> branchesList = [];
    if (json['branches'] is List) {
      branchesList = (json['branches'] as List)
          .map((e) => BranchItem.fromDynamic(e))
          .toList();
    }

    List<String> coachingTypesList = [];
    if (json['coachingtype'] is List) {
      coachingTypesList =
          (json['coachingtype'] as List).map((e) => e.toString()).toList();
    }

    List<String> hostelsList = [];
    if (json['hostel'] is List) {
      hostelsList = (json['hostel'] as List).map((e) => e.toString()).toList();
    }

    List<String> batchesList = [];
    if (json['batch'] is List) {
      batchesList = (json['batch'] as List).map((e) => e.toString()).toList();
    }

    List<String> sectionsList = [];
    if (json['section'] is List) {
      sectionsList = (json['section'] as List).map((e) => e.toString()).toList();
    } else if (json['sections'] is List) {
      sectionsList =
          (json['sections'] as List).map((e) => e.toString()).toList();
    }

    return MasterDataModel(
      activeAcademicYear: academicYear,
      courses: coursesList,
      branches: branchesList,
      coachingTypes: coachingTypesList,
      hostels: hostelsList,
      batches: batchesList,
      sections: sectionsList,
    );
  }
}
