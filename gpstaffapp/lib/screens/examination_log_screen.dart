import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api/api_client.dart';
import '../models/examination_log_model.dart';
import '../theme/app_theme.dart';

class ExaminationLogScreen extends StatefulWidget {
  const ExaminationLogScreen({super.key});

  @override
  State<ExaminationLogScreen> createState() => _ExaminationLogScreenState();
}

class _ExaminationLogScreenState extends State<ExaminationLogScreen> {
  List<String> _categories = [];
  List<String> _exams = [];
  String? _selectedCategory;
  String? _selectedExam;

  ExamLogStatsModel? _stats;
  Map<String, List<ExamLogStudentModel>> _studentDetails = {};

  bool _loading = false;
  bool _loadingExams = false;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchInitialData();
  }

  Future<void> _fetchInitialData() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get('/admin/examination_log');

      if (response.data != null && response.data['status'] == true) {
        final model = ExaminationLogReportModel.fromJson(response.data);
        setState(() {
          _categories = model.categories;
          if (_categories.isNotEmpty && _selectedCategory == null) {
            _selectedCategory = _categories.first;
          }
        });

        if (_selectedCategory != null) {
          await _fetchExamsForCategory(_selectedCategory!);
        }
      }
    } catch (e) {
      debugPrint('Error fetching categories: $e');
      setState(() {
        _errorMessage = 'Failed to load test categories.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _fetchExamsForCategory(String category) async {
    setState(() {
      _loadingExams = true;
      _exams = [];
      _selectedExam = null;
      _stats = null;
      _studentDetails = {};
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get(
        '/admin/examination_log',
        queryParameters: {'testcategory': category},
      );

      if (response.data != null && response.data['status'] == true) {
        final model = ExaminationLogReportModel.fromJson(response.data);
        setState(() {
          _exams = model.exams;
          if (_exams.isNotEmpty) {
            _selectedExam = _exams.first;
          }
        });

        if (_selectedExam != null) {
          await _fetchReport();
        }
      }
    } catch (e) {
      debugPrint('Error fetching exams: $e');
    } finally {
      if (mounted) setState(() => _loadingExams = false);
    }
  }

  Future<void> _fetchReport() async {
    if (_selectedExam == null || _selectedExam!.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select an exam name')),
      );
      return;
    }

    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get(
        '/admin/examination_log',
        queryParameters: {
          'testcategory': _selectedCategory,
          'examname': _selectedExam,
        },
      );

      if (response.data != null && response.data['status'] == true) {
        final model = ExaminationLogReportModel.fromJson(response.data);
        setState(() {
          _stats = model.stats;
          _studentDetails = model.studentDetails;
        });
      } else {
        setState(() {
          _errorMessage =
              response.data?['message'] ?? 'Failed to load report data';
        });
      }
    } catch (e) {
      debugPrint('Error fetching exam log report: $e');
      setState(() {
        _errorMessage = 'Failed to load examination log report.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _showStudentListModal(String title, String key, Color color) {
    final students = _studentDetails[key] ?? [];

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => _StudentListBottomSheet(
        title: title,
        students: students,
        headerColor: color,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Examination Log Report'),
        backgroundColor: AppColors.primary,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: _loading ? null : _fetchReport,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchReport,
        color: AppColors.primary,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Filters Section
              _buildFilterCard(),

              // Content Area
              if (_loading)
                const Padding(
                  padding: EdgeInsets.all(40),
                  child: Center(
                    child: Column(
                      children: [
                        CircularProgressIndicator(color: AppColors.primary),
                        SizedBox(height: 16),
                        Text(
                          'Loading examination log report...',
                          style: TextStyle(color: AppColors.textSecondary),
                        ),
                      ],
                    ),
                  ),
                )
              else if (_errorMessage != null)
                Padding(
                  padding: const EdgeInsets.all(24),
                  child: Center(
                    child: Column(
                      children: [
                        const Icon(Icons.info_outline,
                            size: 48, color: AppColors.error),
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
                          onPressed: _fetchReport,
                          icon: const Icon(Icons.refresh, size: 18),
                          label: const Text('Try Again'),
                        ),
                      ],
                    ),
                  ),
                )
              else if (_stats != null) ...[
                _buildStatsSummaryGrid(),
                _buildExamLogDetailsCard(),
              ] else
                const Padding(
                  padding: EdgeInsets.all(40),
                  child: Center(
                    child: Column(
                      children: [
                        Icon(Icons.assignment_outlined,
                            size: 64, color: AppColors.textMuted),
                        SizedBox(height: 16),
                        Text(
                          'Select exam category and test name',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        SizedBox(height: 6),
                        Text(
                          'View live attendance and online status during exam.',
                          style: TextStyle(
                            fontSize: 12,
                            color: AppColors.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFilterCard() {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.primary,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Category Dropdown
            const Text(
              'EXAM CATEGORY',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: AppColors.textSecondary,
                letterSpacing: 0.5,
              ),
            ),
            const SizedBox(height: 6),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  isExpanded: true,
                  value: _selectedCategory,
                  hint: const Text('Select Category'),
                  items: _categories.map((cat) {
                    return DropdownMenuItem<String>(
                      value: cat,
                      child: Text(
                        cat,
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textPrimary,
                        ),
                      ),
                    );
                  }).toList(),
                  onChanged: (val) {
                    if (val != null && val != _selectedCategory) {
                      setState(() => _selectedCategory = val);
                      _fetchExamsForCategory(val);
                    }
                  },
                ),
              ),
            ),
            const SizedBox(height: 14),

            // Exam Name Dropdown
            const Text(
              'EXAM NAME',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: AppColors.textSecondary,
                letterSpacing: 0.5,
              ),
            ),
            const SizedBox(height: 6),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: _loadingExams
                  ? const Padding(
                      padding: EdgeInsets.symmetric(vertical: 12),
                      child: Row(
                        children: [
                          SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          ),
                          SizedBox(width: 10),
                          Text('Loading exams...',
                              style: TextStyle(fontSize: 13)),
                        ],
                      ),
                    )
                  : DropdownButtonHideUnderline(
                      child: DropdownButton<String>(
                        isExpanded: true,
                        value: _selectedExam,
                        hint: Text(_exams.isEmpty
                            ? 'No exams found'
                            : 'Select Exam Name'),
                        items: _exams.map((exam) {
                          return DropdownMenuItem<String>(
                            value: exam,
                            child: Text(
                              exam,
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          );
                        }).toList(),
                        onChanged: (val) {
                          if (val != null) {
                            setState(() => _selectedExam = val);
                            _fetchReport();
                          }
                        },
                      ),
                    ),
            ),
            const SizedBox(height: 14),

            // Submit Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _loading || _selectedExam == null ? null : _fetchReport,
                icon: const Icon(Icons.analytics_outlined, size: 18),
                label: const Text('View Exam Log Report'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.fanta,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsSummaryGrid() {
    if (_stats == null) return const SizedBox();

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: _buildQuickMetricCard(
                  title: 'Eligible',
                  value: _stats!.totalEligible,
                  color: AppColors.primary,
                  icon: Icons.people_alt_outlined,
                  onTap: null,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _buildQuickMetricCard(
                  title: 'Online',
                  value: _stats!.totalOnline,
                  color: Colors.teal,
                  icon: Icons.wifi,
                  onTap: () => _showStudentListModal(
                      'Online Students', 'online', Colors.teal),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _buildQuickMetricCard(
                  title: 'Writing',
                  value: _stats!.totalWriting,
                  color: AppColors.warning,
                  icon: Icons.edit_note_outlined,
                  onTap: () => _showStudentListModal(
                      'Students Writing Exam', 'writing', AppColors.warning),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: _buildQuickMetricCard(
                  title: 'Finished',
                  value: _stats!.totalFinished,
                  color: AppColors.success,
                  icon: Icons.check_circle_outline,
                  onTap: () => _showStudentListModal(
                      'Students Finished Exam', 'finished', AppColors.success),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _buildQuickMetricCard(
                  title: 'Not Finished',
                  value: _stats!.totalNotFinished,
                  color: Colors.orange,
                  icon: Icons.pending_actions_outlined,
                  onTap: () => _showStudentListModal(
                      'Students Not Finished', 'not_finished', Colors.orange),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _buildQuickMetricCard(
                  title: 'Absent',
                  value: _stats!.totalAbsent,
                  color: AppColors.error,
                  icon: Icons.cancel_outlined,
                  onTap: () => _showStudentListModal(
                      'Absent Students', 'absent', AppColors.error),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickMetricCard({
    required String title,
    required int value,
    required Color color,
    required IconData icon,
    VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 10),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withOpacity(0.25)),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.04),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(icon, size: 16, color: color),
                const SizedBox(width: 4),
                Text(
                  '$value',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Text(
              title,
              style: const TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w600,
                color: AppColors.textSecondary,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildExamLogDetailsCard() {
    if (_stats == null) return const SizedBox();

    return Padding(
      padding: const EdgeInsets.all(16),
      child: Container(
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
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: const BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(20),
                  topRight: Radius.circular(20),
                ),
              ),
              child: const Row(
                children: [
                  Icon(Icons.timer_outlined,
                      color: Colors.white, size: 18),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'AT THE TIME OF EXAMINATION',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            _buildStatListRow(
              index: 1,
              title: 'TOTAL NUMBER OF STUDENTS ELIGIBLE FOR EXAM',
              count: _stats!.totalEligible,
              color: AppColors.primary,
              onTap: null,
            ),
            const Divider(height: 1, color: AppColors.borderLight),
            _buildStatListRow(
              index: 2,
              title: 'TOTAL NUMBER OF STUDENTS ONLINE',
              count: _stats!.totalOnline,
              color: Colors.teal,
              onTap: () => _showStudentListModal(
                  'Online Students', 'online', Colors.teal),
            ),
            const Divider(height: 1, color: AppColors.borderLight),
            _buildStatListRow(
              index: 3,
              title: 'NUMBER OF STUDENTS WRITING THE EXAM',
              count: _stats!.totalWriting,
              color: AppColors.warning,
              onTap: () => _showStudentListModal(
                  'Students Writing Exam', 'writing', AppColors.warning),
            ),
            const Divider(height: 1, color: AppColors.borderLight),
            _buildStatListRow(
              index: 4,
              title: 'NUMBER OF STUDENTS FINISHED THE EXAM',
              count: _stats!.totalFinished,
              color: AppColors.success,
              onTap: () => _showStudentListModal(
                  'Students Finished Exam', 'finished', AppColors.success),
            ),
            const Divider(height: 1, color: AppColors.borderLight),
            _buildStatListRow(
              index: 5,
              title: 'NUMBER OF STUDENTS NOT FINISHED',
              count: _stats!.totalNotFinished,
              color: Colors.orange,
              onTap: () => _showStudentListModal(
                  'Students Not Finished', 'not_finished', Colors.orange),
            ),
            const Divider(height: 1, color: AppColors.borderLight),
            _buildStatListRow(
              index: 6,
              title: 'NUMBER OF STUDENTS ABSENT',
              count: _stats!.totalAbsent,
              color: AppColors.error,
              onTap: () => _showStudentListModal(
                  'Absent Students', 'absent', AppColors.error),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatListRow({
    required int index,
    required String title,
    required int count,
    required Color color,
    VoidCallback? onTap,
  }) {
    final isClickable = onTap != null;

    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Expanded(
              child: Text(
                '$index. $title:',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: isClickable ? FontWeight.w600 : FontWeight.w500,
                  color: AppColors.textPrimary,
                ),
              ),
            ),
            const SizedBox(width: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: color.withOpacity(0.12),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: color.withOpacity(0.3)),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '$count',
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                      color: color,
                    ),
                  ),
                  if (isClickable) ...[
                    const SizedBox(width: 4),
                    Icon(Icons.arrow_forward_ios, size: 10, color: color),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _StudentListBottomSheet extends StatefulWidget {
  final String title;
  final List<ExamLogStudentModel> students;
  final Color headerColor;

  const _StudentListBottomSheet({
    required this.title,
    required this.students,
    required this.headerColor,
  });

  @override
  State<_StudentListBottomSheet> createState() =>
      _StudentListBottomSheetState();
}

class _StudentListBottomSheetState extends State<_StudentListBottomSheet> {
  String _search = '';

  List<ExamLogStudentModel> get _filtered {
    if (_search.isEmpty) return widget.students;
    final query = _search.toLowerCase().trim();
    return widget.students.where((s) {
      return s.studentName.toLowerCase().contains(query) ||
          s.studentId.toLowerCase().contains(query) ||
          s.section.toLowerCase().contains(query) ||
          s.course.toLowerCase().contains(query);
    }).toList();
  }

  Future<void> _makeCall(String number) async {
    if (number.isEmpty) return;
    final uri = Uri.parse('tel:$number');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    final list = _filtered;

    return Container(
      height: MediaQuery.of(context).size.height * 0.85,
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.only(
          topLeft: Radius.circular(24),
          topRight: Radius.circular(24),
        ),
      ),
      child: Column(
        children: [
          // Drag handle
          Container(
            margin: const EdgeInsets.only(top: 10, bottom: 6),
            width: 40,
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              borderRadius: BorderRadius.circular(2),
            ),
          ),

          // Header
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 6, 16, 12),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.title,
                      style: TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.bold,
                        color: widget.headerColor,
                      ),
                    ),
                    Text(
                      'Total: ${widget.students.length} students',
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
                IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
          ),

          // Search field
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: TextField(
              onChanged: (val) => setState(() => _search = val),
              decoration: InputDecoration(
                hintText: 'Search student name, ID, section...',
                prefixIcon: const Icon(Icons.search, size: 18),
                suffixIcon: _search.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, size: 16),
                        onPressed: () => setState(() => _search = ''),
                      )
                    : null,
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                filled: true,
                fillColor: AppColors.background,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
              ),
            ),
          ),
          const SizedBox(height: 8),

          // Student List
          Expanded(
            child: list.isEmpty
                ? const Center(
                    child: Text(
                      'No students found',
                      style: TextStyle(color: AppColors.textSecondary),
                    ),
                  )
                : ListView.builder(
                    padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                    itemCount: list.length,
                    itemBuilder: (ctx, index) {
                      final s = list[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 10),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: AppColors.background,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: AppColors.borderLight),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            CircleAvatar(
                              radius: 18,
                              backgroundColor:
                                  widget.headerColor.withOpacity(0.12),
                              child: Text(
                                '${index + 1}',
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: widget.headerColor,
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
                                      fontSize: 14,
                                      fontWeight: FontWeight.bold,
                                      color: AppColors.textPrimary,
                                    ),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    'ID: ${s.studentId} • Sec: ${s.section} • ${s.course}',
                                    style: const TextStyle(
                                      fontSize: 11,
                                      color: AppColors.textSecondary,
                                    ),
                                  ),
                                  if (s.coachingType.isNotEmpty) ...[
                                    const SizedBox(height: 2),
                                    Text(
                                      'Type: ${s.coachingType}',
                                      style: const TextStyle(
                                        fontSize: 11,
                                        color: AppColors.textMuted,
                                      ),
                                    ),
                                  ],
                                  const SizedBox(height: 6),
                                  Row(
                                    children: [
                                      if (s.fatherPhNo.isNotEmpty) ...[
                                        InkWell(
                                          onTap: () => _makeCall(s.fatherPhNo),
                                          child: Row(
                                            mainAxisSize: MainAxisSize.min,
                                            children: [
                                              const Icon(Icons.phone_outlined,
                                                  size: 13,
                                                  color: AppColors.primary),
                                              const SizedBox(width: 2),
                                              Text(
                                                'F: ${s.fatherPhNo}',
                                                style: const TextStyle(
                                                  fontSize: 11,
                                                  color: AppColors.primary,
                                                  fontWeight: FontWeight.w500,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        const SizedBox(width: 12),
                                      ],
                                      if (s.motherPhNo.isNotEmpty) ...[
                                        InkWell(
                                          onTap: () => _makeCall(s.motherPhNo),
                                          child: Row(
                                            mainAxisSize: MainAxisSize.min,
                                            children: [
                                              const Icon(Icons.phone_outlined,
                                                  size: 13,
                                                  color: AppColors.fanta),
                                              const SizedBox(width: 2),
                                              Text(
                                                'M: ${s.motherPhNo}',
                                                style: const TextStyle(
                                                  fontSize: 11,
                                                  color: AppColors.fanta,
                                                  fontWeight: FontWeight.w500,
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
                          ],
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}
