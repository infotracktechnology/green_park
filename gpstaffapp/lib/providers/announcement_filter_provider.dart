import 'package:flutter/foundation.dart';
import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../models/student_model.dart';

class AnnouncementFilterProvider with ChangeNotifier {
  // Master Data
  MasterDataModel? _master;
  bool _loading = true;

  // Filter Values
  String _academicYear = '';
  String _usertype = 'GROUP';
  String _course = '';
  List<dynamic> _branches = [];
  List<String> _coachingTypes = [];
  List<String> _category = [];
  List<String> _batch = [];
  String _gender = 'All';
  String _section = '';
  String _student = '';

  // Dynamic Options
  List<String> _availableCoachingTypes = [];
  List<String> _sectionOptions = [];
  Map<String, String> _studentOptions = {};
  bool _studentLoading = false;
  String _studentSearch = '';

  // Visibility Flags (matching layouts/app.blade.php updateVisibility)
  bool _showGender = true;
  bool _showSection = true;
  bool _showStudent = false;
  bool _showCategory = false;
  bool _showBatch = false;

  // Getters
  MasterDataModel? get master => _master;
  bool get loading => _loading;

  String get academicYear => _academicYear;
  String get usertype => _usertype;
  String get course => _course;
  List<dynamic> get branches => _branches;
  List<String> get coachingTypes => _coachingTypes;
  List<String> get category => _category;
  List<String> get batch => _batch;
  String get gender => _gender;
  String get section => _section;
  String get student => _student;

  List<BranchItem> get availableBranches {
    final all = _master?.branches ?? [];
    if (['XI-OB', 'XII-OB'].contains(_course.toUpperCase())) {
      return all.where((b) {
        final id = int.tryParse(b.id.toString()) ?? 0;
        return ![1, 4, 5].contains(id);
      }).toList();
    }
    return all;
  }

  List<String> get availableCoachingTypes {
    if (_availableCoachingTypes.isNotEmpty) return _availableCoachingTypes;
    return _master?.coachingTypes ?? [];
  }

  List<String> get sectionOptions => _sectionOptions;
  Map<String, String> get studentOptions => _studentOptions;
  bool get studentLoading => _studentLoading;
  String get studentSearch => _studentSearch;

  bool get showGender => _showGender;
  bool get showSection => _showSection;
  bool get showStudent => _showStudent;
  bool get showCategory => _showCategory;
  bool get showBatch => _showBatch;

  List<StudentItem> get studentList {
    return _studentOptions.entries
        .map((e) => StudentItem(id: e.key, name: e.value))
        .toList();
  }

  AnnouncementFilterProvider();

  // Fetch Master Data
  Future<void> fetchMasterData() async {
    _loading = true;
    notifyListeners();

    try {
      final dio = ApiClient().dio;
      final res = await dio.get('/admin/masterdata');
      if (res.data != null && res.data['status'] == true) {
        _master = MasterDataModel.fromJson(res.data);
        if (_academicYear.isEmpty && _master!.activeAcademicYear.isNotEmpty) {
          _academicYear = _master!.activeAcademicYear;
        }
        if (_availableCoachingTypes.isEmpty && _master!.coachingTypes.isNotEmpty) {
          _availableCoachingTypes = List<String>.from(_master!.coachingTypes);
        }
      }
    } catch (e) {
      debugPrint('Master data fetch error: $e');
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  // Pre-fill for Edit Announcement
  void setAllFilters(Map<String, dynamic> data) {
    if (data['academic_year'] != null) {
      _academicYear = data['academic_year'].toString();
    }
    if (data['usertype'] != null) {
      _usertype = data['usertype'].toString();
    }
    if (data['course'] != null) {
      _course = data['course'].toString();
    }

    if (data['branch'] != null) {
      if (data['branch'] is List) {
        _branches = (data['branch'] as List)
            .map((b) => int.tryParse(b.toString()) ?? b)
            .toList();
      } else {
        _branches = data['branch']
            .toString()
            .split(',')
            .where((s) => s.isNotEmpty)
            .map((b) => int.tryParse(b.trim()) ?? b.trim())
            .toList();
      }
    }

    if (data['coaching_type'] != null) {
      if (data['coaching_type'] is List) {
        _coachingTypes = (data['coaching_type'] as List).map((e) => e.toString()).toList();
      } else {
        _coachingTypes = data['coaching_type']
            .toString()
            .split(',')
            .where((s) => s.isNotEmpty)
            .map((e) => e.trim())
            .toList();
      }
    }

    if (data['category'] != null) {
      if (data['category'] is List) {
        _category = (data['category'] as List).map((e) => e.toString()).toList();
      } else {
        _category = data['category']
            .toString()
            .split(',')
            .where((s) => s.isNotEmpty)
            .map((e) => e.trim())
            .toList();
      }
    }

    if (data['batch'] != null) {
      if (data['batch'] is List) {
        _batch = (data['batch'] as List).map((e) => e.toString()).toList();
      } else {
        _batch = data['batch']
            .toString()
            .split(',')
            .where((s) => s.isNotEmpty)
            .map((e) => e.trim())
            .toList();
      }
    }

    if (data['gender'] != null) {
      _gender = data['gender'].toString();
    }
    if (data['section'] != null) {
      _section = data['section'].toString();
    }
    if (data['students'] != null) {
      _student = data['students'].toString();
    }
    if (data['sectionOptions'] != null) {
      if (data['sectionOptions'] is List) {
        _sectionOptions = (data['sectionOptions'] as List).map((e) => e.toString()).toList();
      }
    }

    if (data['studentOptions'] != null && data['studentOptions'] is Map) {
      _studentOptions = (data['studentOptions'] as Map).map(
        (key, value) => MapEntry(key.toString(), value.toString()),
      );
    }

    _updateVisibility();
    if (_sectionOptions.isEmpty && _course.isNotEmpty && _branches.isNotEmpty) {
      fetchSections();
    }
    notifyListeners();
  }

  // Setters
  void setAcademicYear(String year) {
    _academicYear = year;
    notifyListeners();
  }

  void setUsertype(String type) {
    _usertype = type;
    _updateVisibility();
    if (_usertype == 'INDIVIDUAL') {
      fetchStudents();
    }
    notifyListeners();
  }

  void setCourse(String val) {
    _course = val;

    // In JS: if XI-OB or XII-OB, remove branches 1, 4, 5
    if (['XI-OB', 'XII-OB'].contains(_course.toUpperCase())) {
      _branches.removeWhere((b) => [1, 4, 5].contains(int.tryParse(b.toString()) ?? 0));
    }

    _updateVisibility();
    fetchCoachingTypes();
    fetchSections();
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void toggleBranch(dynamic branchId) {
    if (_branches.contains(branchId)) {
      _branches.remove(branchId);
    } else {
      _branches.add(branchId);
    }
    _updateVisibility();
    fetchCoachingTypes();
    fetchSections();
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void toggleCoachingType(String type) {
    if (_coachingTypes.contains(type)) {
      _coachingTypes.remove(type);
    } else {
      _coachingTypes.add(type);
    }
    _updateVisibility();
    fetchSections();
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void toggleCategory(String cat) {
    if (_category.contains(cat)) {
      _category.remove(cat);
    } else {
      _category.add(cat);
    }
    fetchSections();
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void toggleBatch(String bat) {
    if (_batch.contains(bat)) {
      _batch.remove(bat);
    } else {
      _batch.add(bat);
    }
    fetchSections();
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void setGender(String g) {
    _gender = g;
    fetchSections();
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void setSection(String sec) {
    _section = sec;
    if (_usertype == 'INDIVIDUAL') fetchStudents();
    notifyListeners();
  }

  void setStudent(String std) {
    _student = std;
    notifyListeners();
  }

  void setStudentSearch(String search) {
    _studentSearch = search;
    notifyListeners();
  }

  // Update Dynamic Visibility (Exact mirror of layouts/app.blade.php updateVisibility)
  void _updateVisibility() {
    _showStudent = _usertype == 'INDIVIDUAL';
    _showGender = _usertype != 'INDIVIDUAL';

    final hasOffline = _coachingTypes.any((t) => t.toUpperCase().contains('OFFLINE'));
    final isGroup = _usertype == 'GROUP';

    if (hasOffline && isGroup) {
      final isTargetCourse = ['NEET', 'JEE'].contains(_course.toUpperCase());
      final branchIdNums = _branches.map((b) => int.tryParse(b.toString()) ?? 0).toList();

      if (isTargetCourse) {
        if (branchIdNums.any((id) => [1, 4, 5].contains(id))) {
          _showCategory = true;
          _showBatch = true;
          _showSection = true;
        } else if (branchIdNums.any((id) => [3, 6].contains(id))) {
          _showCategory = false;
          _showBatch = true;
          _showSection = true;
        } else {
          _showCategory = false;
          _showBatch = false;
          _showSection = true;
        }
      } else {
        _showCategory = false;
        _showBatch = false;
        _showSection = true;
      }
    } else {
      _showCategory = false;
      _showBatch = false;
      _showSection = false;
    }

    // Clear values for hidden fields as done in JS: if(!show) $el.val('');
    if (!_showCategory) _category = [];
    if (!_showBatch) _batch = [];
    if (!_showSection && _usertype != 'INDIVIDUAL') _section = '';
  }

  // Fetch Coaching Types (Matching JS branch.change / fetchData)
  Future<void> fetchCoachingTypes() async {
    if (_course.isEmpty || _branches.isEmpty) return;
    try {
      final dio = ApiClient().dio;
      final branchVal = _branches.join(',');
      final res = await dio.get('/admin/filter', queryParameters: {
        'branch': branchVal,
        'course': _course,
      });
      if (res.data is List) {
        final list = (res.data as List).map((e) => e.toString()).toList();
        _availableCoachingTypes = list;
        // Keep only valid selections
        if (_coachingTypes.isNotEmpty) {
          _coachingTypes.removeWhere((t) => !list.contains(t));
        }
      }
    } catch (e) {
      debugPrint('Coaching types error: $e');
    }
    _updateVisibility();
    notifyListeners();
  }

  // Fetch Sections (Matching JS updateSections)
  Future<void> fetchSections() async {
    if (_course.isEmpty || _branches.isEmpty || _coachingTypes.isEmpty) return;
    try {
      final dio = ApiClient().dio;
      final res = await dio.get('/admin/filter', queryParameters: {
        'gender': _gender != 'All' ? _gender : '',
        'category': _category.join(','),
        'batch': _batch.join(','),
        'type': _coachingTypes.join(','),
        'branch': _branches.join(','),
        'course': _course,
      });
      if (res.data is List) {
        _sectionOptions = (res.data as List).map((e) => e.toString()).toList();
      }
    } catch (e) {
      debugPrint('Sections error: $e');
    }
    notifyListeners();
  }

  // Fetch Students (Matching JS type.change INDIVIDUAL / student fetch)
  Future<void> fetchStudents([String? customSearch]) async {
    if (_usertype != 'INDIVIDUAL' || _course.isEmpty || _branches.isEmpty) return;
    _studentLoading = true;
    notifyListeners();

    try {
      final dio = ApiClient().dio;
      final res = await dio.get('/admin/filter', queryParameters: {
        'get_students': 1,
        'type': _coachingTypes.join(','),
        'branch': _branches.join(','),
        'course': _course,
        'category': _category.join(','),
        'batch': _batch.join(','),
        'gender': _gender != 'All' ? _gender : '',
        'section': _section,
        'search': customSearch ?? _studentSearch,
      });

      if (res.data is Map) {
        _studentOptions = (res.data as Map).map(
          (k, v) => MapEntry(k.toString(), v.toString()),
        );
      } else if (res.data is List) {
        final Map<String, String> mapped = {};
        for (var item in res.data) {
          if (item is Map) {
            final id = (item['student_id'] ?? item['id'] ?? '').toString();
            final name = (item['student_name'] ?? item['name'] ?? id).toString();
            if (id.isNotEmpty) mapped[id] = name;
          }
        }
        _studentOptions = mapped;
      } else {
        _studentOptions = {};
      }
    } catch (e) {
      debugPrint('Students error: $e');
    } finally {
      _studentLoading = false;
      notifyListeners();
    }
  }

  void resetAll() {
    _academicYear = _master?.activeAcademicYear ?? '';
    _usertype = 'GROUP';
    _course = '';
    _branches = [];
    _coachingTypes = [];
    _category = [];
    _batch = [];
    _gender = 'All';
    _section = '';
    _student = '';
    _availableCoachingTypes = _master?.coachingTypes != null ? List<String>.from(_master!.coachingTypes) : [];
    _sectionOptions = [];
    _studentOptions = {};
    _studentSearch = '';
    _updateVisibility();
    notifyListeners();
  }
}
