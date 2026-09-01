import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../models/student_detail_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'student_edit_screen.dart';

class StudentListScreen extends StatefulWidget {
  final bool canEdit;

  const StudentListScreen({super.key, this.canEdit = true});

  @override
  State<StudentListScreen> createState() => _StudentListScreenState();
}

class _StudentListScreenState extends State<StudentListScreen> {
  final TextEditingController _searchController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  List<StudentDetailModel> _students = [];
  bool _loading = false;
  bool _loadingMore = false;
  String? _errorMessage;

  int _currentPage = 1;
  int _lastPage = 1;
  int _totalStudents = 0;

  String? _selectedBranchId;
  String _selectedCourse = '';
  String _selectedCoachingType = '';
  String _selectedSection = '';

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initMasterData();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
            _scrollController.position.maxScrollExtent - 200 &&
        !_loading &&
        !_loadingMore &&
        _currentPage < _lastPage) {
      _fetchStudents(page: _currentPage + 1, isLoadMore: true);
    }
  }

  Future<void> _initMasterData() async {
    final filterProvider =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    if (filterProvider.master == null) {
      await filterProvider.fetchMasterData();
    }
    _fetchStudents(page: 1);
  }

  Future<void> _fetchStudents({int page = 1, bool isLoadMore = false}) async {
    if (isLoadMore) {
      setState(() => _loadingMore = true);
    } else {
      setState(() {
        _loading = true;
        _errorMessage = null;
        if (page == 1) _students = [];
      });
    }

    try {
      final dio = ApiClient().dio;
      final queryParams = <String, dynamic>{
        'page': page,
        'per_page': 20,
      };

      final search = _searchController.text.trim();
      if (search.isNotEmpty) {
        queryParams['search'] = search;
      }
      if (_selectedBranchId != null && _selectedBranchId!.isNotEmpty) {
        queryParams['branch_id'] = _selectedBranchId;
      }
      if (_selectedCourse.isNotEmpty) {
        queryParams['course'] = _selectedCourse;
      }
      if (_selectedCoachingType.isNotEmpty) {
        queryParams['coaching_type'] = _selectedCoachingType;
      }
      if (_selectedSection.isNotEmpty) {
        queryParams['section'] = _selectedSection;
      }

      final response = await dio.get(
        '/admin/student',
        queryParameters: queryParams,
      );

      if (response.data != null && response.data['status'] == true) {
        final data = response.data['students'];
        List<StudentDetailModel> loadedList = [];

        if (data is Map && data['data'] is List) {
          loadedList = (data['data'] as List)
              .map((e) =>
                  StudentDetailModel.fromJson(e is Map<String, dynamic> ? e : {}))
              .toList();
          _currentPage = data['current_page'] ?? page;
          _lastPage = data['last_page'] ?? page;
          _totalStudents = data['total'] ?? loadedList.length;
        } else if (data is List) {
          loadedList = data
              .map((e) =>
                  StudentDetailModel.fromJson(e is Map<String, dynamic> ? e : {}))
              .toList();
          _currentPage = 1;
          _lastPage = 1;
          _totalStudents = loadedList.length;
        }

        setState(() {
          if (isLoadMore) {
            _students.addAll(loadedList);
          } else {
            _students = loadedList;
          }
        });
      } else {
        setState(() {
          if (!isLoadMore) _students = [];
          _errorMessage =
              response.data?['message'] ?? 'Failed to load students.';
        });
      }
    } catch (e) {
      debugPrint('Error fetching students: $e');
      setState(() {
        if (!isLoadMore) {
          _errorMessage = 'Failed to load students list. Please try again.';
        }
      });
    } finally {
      if (mounted) {
        setState(() {
          _loading = false;
          _loadingMore = false;
        });
      }
    }
  }

  Future<void> _makeCall(String? number) async {
    if (number == null || number.trim().isEmpty) return;
    final uri = Uri.parse('tel:${number.trim()}');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  void _openEditScreen(StudentDetailModel student) async {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final isEditable = auth.user?.isAdmin == true && widget.canEdit;

    final updated = await Navigator.push<bool>(
      context,
      MaterialPageRoute(
        builder: (_) => StudentEditScreen(
          student: student,
          isReadOnly: !isEditable,
        ),
      ),
    );

    if (updated == true) {
      _fetchStudents(page: 1);
    }
  }

  @override
  Widget build(BuildContext context) {
    final filterProvider = Provider.of<AnnouncementFilterProvider>(context);
    final branches = filterProvider.master?.branches ?? [];
    final courses = filterProvider.master?.courses ?? [];
    final coachingTypes = filterProvider.master?.coachingTypes ?? [];
    final sections = filterProvider.master?.sections ?? [];

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Students Details'),
            if (_totalStudents > 0)
              Text(
                'Total: $_totalStudents students',
                style: const TextStyle(fontSize: 11, color: Colors.white70),
              ),
          ],
        ),
        backgroundColor: AppColors.primary,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: () => _fetchStudents(page: 1),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => _fetchStudents(page: 1),
        color: AppColors.primary,
        child: Column(
          children: [
            // Top Search & Filter Container
            _buildSearchAndFilters(branches, courses, coachingTypes, sections),

            // Student List
            Expanded(
              child: _buildStudentList(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchAndFilters(
    List<BranchItem> branches,
    List<String> courses,
    List<String> coachingTypes,
    List<String> sections,
  ) {
    final hasActiveFilters = (_selectedBranchId != null && _selectedBranchId!.isNotEmpty) ||
        _selectedCourse.isNotEmpty ||
        _selectedCoachingType.isNotEmpty ||
        _selectedSection.isNotEmpty ||
        _searchController.text.isNotEmpty;

    return Container(
      decoration: const BoxDecoration(
        color: AppColors.primary,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(20),
          bottomRight: Radius.circular(20),
        ),
      ),
      padding: const EdgeInsets.fromLTRB(16, 4, 16, 16),
      child: Column(
        children: [
          // Search Input
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.08),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: TextField(
              controller: _searchController,
              onSubmitted: (_) => _fetchStudents(page: 1),
              textInputAction: TextInputAction.search,
              decoration: InputDecoration(
                hintText: 'Search Name, ID, Username, Phone, Section...',
                hintStyle: const TextStyle(fontSize: 12, color: AppColors.textMuted),
                prefixIcon: const Icon(Icons.search, size: 18, color: AppColors.primary),
                suffixIcon: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (_searchController.text.isNotEmpty)
                      IconButton(
                        icon: const Icon(Icons.clear, size: 16),
                        onPressed: () {
                          _searchController.clear();
                          _fetchStudents(page: 1);
                        },
                      ),
                    IconButton(
                      icon: const Icon(Icons.arrow_forward,
                          size: 18, color: AppColors.primary),
                      onPressed: () => _fetchStudents(page: 1),
                    ),
                  ],
                ),
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                border: InputBorder.none,
              ),
            ),
          ),
          const SizedBox(height: 10),

          // Filters Row 1: Campus & Course
          Row(
            children: [
              // Branch Filter
              if (branches.length > 1) ...[
                Expanded(
                  child: _buildFilterDropdown<String>(
                    value: _selectedBranchId,
                    hint: 'Campus',
                    items: [
                      const DropdownMenuItem(value: '', child: Text('All Campus')),
                      ...branches.map((b) => DropdownMenuItem(
                            value: b.id.toString(),
                            child: Text(b.name, overflow: TextOverflow.ellipsis),
                          )),
                    ],
                    onChanged: (val) {
                      setState(() => _selectedBranchId = val);
                      _fetchStudents(page: 1);
                    },
                  ),
                ),
                const SizedBox(width: 8),
              ],

              // Course Filter
              Expanded(
                child: _buildFilterDropdown<String>(
                  value: _selectedCourse,
                  hint: 'Course',
                  items: [
                    const DropdownMenuItem(value: '', child: Text('All Course')),
                    ...courses.map((c) => DropdownMenuItem(
                          value: c,
                          child: Text(c, overflow: TextOverflow.ellipsis),
                        )),
                  ],
                  onChanged: (val) {
                    setState(() => _selectedCourse = val ?? '');
                    _fetchStudents(page: 1);
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),

          // Filters Row 2: Type & Section
          Row(
            children: [
              // Coaching Type Filter
              Expanded(
                child: _buildFilterDropdown<String>(
                  value: _selectedCoachingType,
                  hint: 'Type',
                  items: [
                    const DropdownMenuItem(value: '', child: Text('All Type')),
                    ...coachingTypes.map((t) => DropdownMenuItem(
                          value: t,
                          child: Text(t, overflow: TextOverflow.ellipsis),
                        )),
                  ],
                  onChanged: (val) {
                    setState(() => _selectedCoachingType = val ?? '');
                    _fetchStudents(page: 1);
                  },
                ),
              ),
              const SizedBox(width: 8),

              // Section Filter
              Expanded(
                child: _buildFilterDropdown<String>(
                  value: _selectedSection,
                  hint: 'Section',
                  items: [
                    const DropdownMenuItem(value: '', child: Text('All Section')),
                    ...sections.map((s) => DropdownMenuItem(
                          value: s,
                          child: Text(s, overflow: TextOverflow.ellipsis),
                        )),
                  ],
                  onChanged: (val) {
                    setState(() => _selectedSection = val ?? '');
                    _fetchStudents(page: 1);
                  },
                ),
              ),
            ],
          ),

          if (hasActiveFilters) ...[
            const SizedBox(height: 8),
            Align(
              alignment: Alignment.centerRight,
              child: GestureDetector(
                onTap: () {
                  _searchController.clear();
                  setState(() {
                    _selectedBranchId = null;
                    _selectedCourse = '';
                    _selectedCoachingType = '';
                    _selectedSection = '';
                  });
                  _fetchStudents(page: 1);
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.clear_all, size: 14, color: Colors.white),
                      SizedBox(width: 4),
                      Text(
                        'Reset Filters',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildFilterDropdown<T>({
    required T? value,
    required String hint,
    required List<DropdownMenuItem<T>> items,
    required void Function(T?) onChanged,
  }) {
    final validValue = items.any((i) => i.value == value) ? value : null;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.18),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.white.withOpacity(0.3)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<T>(
          isExpanded: true,
          dropdownColor: Colors.white,
          iconEnabledColor: Colors.white,
          value: validValue,
          hint: Text(hint,
              style: const TextStyle(fontSize: 11, color: Colors.white)),
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
          items: items,
          onChanged: onChanged,
        ),
      ),
    );
  }

  Widget _buildStudentList() {
    if (_loading) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(color: AppColors.primary),
            SizedBox(height: 14),
            Text('Loading students...',
                style: TextStyle(color: AppColors.textSecondary)),
          ],
        ),
      );
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.info_outline, size: 48, color: AppColors.error),
              const SizedBox(height: 12),
              Text(
                _errorMessage!,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: () => _fetchStudents(page: 1),
                icon: const Icon(Icons.refresh, size: 18),
                label: const Text('Try Again'),
              ),
            ],
          ),
        ),
      );
    }

    if (_students.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.person_search_outlined,
                size: 60, color: AppColors.textMuted.withOpacity(0.7)),
            const SizedBox(height: 14),
            const Text(
              'No students found',
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'Try adjusting search keywords or filters',
              style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 32),
      itemCount: _students.length + (_loadingMore ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == _students.length) {
          return const Padding(
            padding: EdgeInsets.symmetric(vertical: 16),
            child: Center(
              child: SizedBox(
                width: 24,
                height: 24,
                child: CircularProgressIndicator(strokeWidth: 2),
              ),
            ),
          );
        }

        final s = _students[index];
        return _buildStudentCard(s, index + 1);
      },
    );
  }

  Widget _buildStudentCard(StudentDetailModel s, int index) {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final isEditable = auth.user?.isAdmin == true && widget.canEdit;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Top Row: Avatar, Name, Edit / View Button
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                CircleAvatar(
                  radius: 20,
                  backgroundColor: AppColors.primary.withOpacity(0.12),
                  child: Text(
                    s.studentName.isNotEmpty
                        ? s.studentName[0].toUpperCase()
                        : '$index',
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        s.studentName,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'ID: ${s.studentId} • User: ${s.userName}',
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                // Edit / View Button
                ElevatedButton.icon(
                  onPressed: () => _openEditScreen(s),
                  icon: Icon(
                    isEditable ? Icons.edit : Icons.visibility_outlined,
                    size: 14,
                  ),
                  label: Text(
                    isEditable ? 'Edit' : 'View',
                    style: const TextStyle(fontSize: 11),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor:
                        isEditable ? AppColors.fanta : AppColors.primary,
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    minimumSize: Size.zero,
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                ),
              ],
            ),

            const Padding(
              padding: EdgeInsets.symmetric(vertical: 10),
              child: Divider(height: 1, color: AppColors.borderLight),
            ),

            // Chips / Badges Row
            Wrap(
              spacing: 6,
              runSpacing: 6,
              children: [
                if (s.course != null && s.course!.isNotEmpty)
                  _buildTag(s.course!, AppColors.primary),
                if (s.campusName != null && s.campusName!.isNotEmpty)
                  _buildTag(s.campusName!, Colors.teal),
                if (s.coachingType != null && s.coachingType!.isNotEmpty)
                  _buildTag(s.coachingType!, Colors.deepPurple),
                if (s.section != null && s.section!.isNotEmpty)
                  _buildTag('Sec: ${s.section}', AppColors.textSecondary),
                if (s.batch != null && s.batch!.isNotEmpty)
                  _buildTag('Batch: ${s.batch}', AppColors.textSecondary),
                if (s.gender != null && s.gender!.isNotEmpty)
                  _buildTag(s.gender!, Colors.indigo),
                if (s.hostelDayscholar != null && s.hostelDayscholar!.isNotEmpty)
                  _buildTag(s.hostelDayscholar!, Colors.brown),
              ],
            ),

            const SizedBox(height: 10),

            // Credentials & Teams details
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.borderLight),
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      const Icon(Icons.lock_outline,
                          size: 13, color: AppColors.textMuted),
                      const SizedBox(width: 4),
                      const Text(
                        'Pass: ',
                        style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textSecondary),
                      ),
                      Text(
                        s.password ?? '-',
                        style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary),
                      ),
                      const Spacer(),
                      if (s.teamsId != null && s.teamsId!.isNotEmpty) ...[
                        const Icon(Icons.groups_outlined,
                            size: 13, color: AppColors.textMuted),
                        const SizedBox(width: 4),
                        const Text(
                          'Teams: ',
                          style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: AppColors.textSecondary),
                        ),
                        Flexible(
                          child: Text(
                            s.teamsId!,
                            style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 10),

            // Phone numbers row (Quick Call)
            Row(
              children: [
                if (s.fatherPhNo != null && s.fatherPhNo!.isNotEmpty) ...[
                  InkWell(
                    onTap: () => _makeCall(s.fatherPhNo),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.phone_outlined,
                            size: 13, color: AppColors.primary),
                        const SizedBox(width: 2),
                        Text(
                          'F: ${s.fatherPhNo}',
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppColors.primary,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 14),
                ],
                if (s.motherPhNo != null && s.motherPhNo!.isNotEmpty) ...[
                  InkWell(
                    onTap: () => _makeCall(s.motherPhNo),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.phone_outlined,
                            size: 13, color: AppColors.fanta),
                        const SizedBox(width: 2),
                        Text(
                          'M: ${s.motherPhNo}',
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppColors.fanta,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 14),
                ],
                if (s.studentWhatsappNo != null &&
                    s.studentWhatsappNo!.isNotEmpty) ...[
                  InkWell(
                    onTap: () => _makeCall(s.studentWhatsappNo),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.chat_bubble_outline,
                            size: 13, color: Colors.teal),
                        const SizedBox(width: 2),
                        Text(
                          'S: ${s.studentWhatsappNo}',
                          style: const TextStyle(
                            fontSize: 11,
                            color: Colors.teal,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTag(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.25)),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }
}
