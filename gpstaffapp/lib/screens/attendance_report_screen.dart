import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../api/api_client.dart';
import '../models/attendance_report_model.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class AttendanceReportScreen extends StatefulWidget {
  const AttendanceReportScreen({super.key});

  @override
  State<AttendanceReportScreen> createState() => _AttendanceReportScreenState();
}

class _AttendanceReportScreenState extends State<AttendanceReportScreen> {
  DateTime _selectedDate = DateTime.now();
  String? _selectedBranchId;
  String _selectedSection = '';

  List<BranchItem> _branches = [];
  List<String> _sections = [];
  List<AttendanceSectionItemModel> _attendances = [];

  bool _loading = false;
  String? _errorMessage;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initFilters();
    });
  }

  Future<void> _initFilters() async {
    final filterProvider =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final currentBranch = auth.user?.branch;

    if (filterProvider.master == null) {
      await filterProvider.fetchMasterData();
    }

    if (mounted) {
      setState(() {
        _branches = filterProvider.master?.branches ?? [];
        if (_branches.isNotEmpty) {
          if (currentBranch != null &&
              _branches.any((b) => b.id.toString() == currentBranch.toString())) {
            _selectedBranchId = currentBranch.toString();
          } else {
            _selectedBranchId = _branches.first.id.toString();
          }
        }
      });

      if (_selectedBranchId != null) {
        _fetchReport();
      }
    }
  }

  Future<void> _fetchReport() async {
    if (_selectedBranchId == null || _selectedBranchId!.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a branch')),
      );
      return;
    }

    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final formattedDate = DateFormat('yyyy-MM-dd').format(_selectedDate);

      final queryParams = <String, dynamic>{
        'branch_id': _selectedBranchId,
        'date': formattedDate,
      };

      if (_selectedSection.isNotEmpty) {
        queryParams['section'] = _selectedSection;
      }

      final response = await dio.get(
        '/admin/attendance_report',
        queryParameters: queryParams,
      );

      if (response.data != null && response.data['status'] == true) {
        final model = AttendanceReportResponseModel.fromJson(response.data);
        setState(() {
          _attendances = model.attendances;
          if (model.sections.isNotEmpty) {
            _sections = model.sections;
          }
        });
      } else {
        setState(() {
          _attendances = [];
          _errorMessage =
              response.data?['message'] ?? 'Failed to load attendance records';
        });
      }
    } catch (e) {
      debugPrint('Error fetching attendance report: $e');
      setState(() {
        _errorMessage = 'Failed to load attendance report. Please try again.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: Colors.white,
              onSurface: AppColors.textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null && picked != _selectedDate) {
      setState(() => _selectedDate = picked);
      _fetchReport();
    }
  }

  int get _totalBoys =>
      _attendances.fold(0, (sum, item) => sum + item.boys);
  int get _totalGirls =>
      _attendances.fold(0, (sum, item) => sum + item.girls);
  int get _totalStudents =>
      _attendances.fold(0, (sum, item) => sum + item.total);
  int get _totalPresent =>
      _attendances.fold(0, (sum, item) => sum + item.present);
  int get _totalAbsent =>
      _attendances.fold(0, (sum, item) => sum + item.absent);

  double get _overallPresentPercent =>
      _totalStudents > 0 ? (_totalPresent * 100 / _totalStudents) : 0.0;
  double get _overallAbsentPercent =>
      _totalStudents > 0 ? (_totalAbsent * 100 / _totalStudents) : 0.0;

  List<AttendanceSectionItemModel> get _filteredAttendances {
    if (_searchQuery.isEmpty) return _attendances;
    final query = _searchQuery.toLowerCase().trim();
    return _attendances
        .where((item) => item.section.toLowerCase().contains(query))
        .toList();
  }

  void _showStudentListModal(
      String title, List<String> studentNames, Color color) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => _AttendanceStudentListBottomSheet(
        title: title,
        studentNames: studentNames,
        headerColor: color,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _filteredAttendances;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Daily Attendance Report'),
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
              _buildFilterCard(),
              if (_attendances.isNotEmpty) ...[
                _buildSummaryStatsCard(),
                _buildSearchBar(),
              ],
              _buildContent(filtered),
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
            // Branch & Date Row
            Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'BRANCH',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary,
                          letterSpacing: 0.5,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        decoration: BoxDecoration(
                          color: AppColors.background,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: AppColors.border),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<String>(
                            isExpanded: true,
                            value: _selectedBranchId,
                            hint: const Text('Select Branch',
                                style: TextStyle(fontSize: 13)),
                            items: _branches.map((b) {
                              return DropdownMenuItem<String>(
                                value: b.id.toString(),
                                child: Text(
                                  b.name,
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
                                setState(() => _selectedBranchId = val);
                                _fetchReport();
                              }
                            },
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'DATE',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary,
                          letterSpacing: 0.5,
                        ),
                      ),
                      const SizedBox(height: 6),
                      InkWell(
                        onTap: _pickDate,
                        borderRadius: BorderRadius.circular(14),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 12),
                          decoration: BoxDecoration(
                            color: AppColors.background,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                DateFormat('dd/MM/yyyy').format(_selectedDate),
                                style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textPrimary,
                                ),
                              ),
                              const Icon(Icons.calendar_month_outlined,
                                  size: 16, color: AppColors.primary),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Section Filter
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'SECTION',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textSecondary,
                    letterSpacing: 0.5,
                  ),
                ),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(
                    color: AppColors.background,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      isExpanded: true,
                      value: _selectedSection,
                      items: [
                        const DropdownMenuItem(
                          value: '',
                          child: Text('All Sections',
                              style: TextStyle(fontSize: 13)),
                        ),
                        ..._sections.map((s) => DropdownMenuItem(
                              value: s,
                              child: Text(s,
                                  style: const TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w600)),
                            )),
                      ],
                      onChanged: (val) {
                        if (val != null) {
                          setState(() => _selectedSection = val);
                          _fetchReport();
                        }
                      },
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),

            // Submit Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _loading ? null : _fetchReport,
                icon: _loading
                    ? const SizedBox(
                        width: 16,
                        height: 16,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.search, size: 18),
                label: const Text('View Attendance Report'),
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

  Widget _buildSummaryStatsCard() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Container(
        padding: const EdgeInsets.all(16),
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
          children: [
            Row(
              children: [
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Total',
                    value: '$_totalStudents',
                    sub: '$_totalBoys B / $_totalGirls G',
                    color: AppColors.primary,
                    icon: Icons.groups_outlined,
                  ),
                ),
                Container(
                    width: 1, height: 44, color: AppColors.borderLight),
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Present',
                    value: '$_totalPresent',
                    sub: '${_overallPresentPercent.toStringAsFixed(1)}%',
                    color: AppColors.success,
                    icon: Icons.check_circle_outline,
                  ),
                ),
                Container(
                    width: 1, height: 44, color: AppColors.borderLight),
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Absent',
                    value: '$_totalAbsent',
                    sub: '${_overallAbsentPercent.toStringAsFixed(1)}%',
                    color: AppColors.error,
                    icon: Icons.cancel_outlined,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryMetric({
    required String label,
    required String value,
    required String sub,
    required Color color,
    required IconData icon,
  }) {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 14, color: color),
            const SizedBox(width: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 17,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        Text(
          sub,
          style: TextStyle(
            fontSize: 10,
            fontWeight: FontWeight.w600,
            color: color.withOpacity(0.85),
          ),
        ),
      ],
    );
  }

  Widget _buildSearchBar() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
      child: TextField(
        onChanged: (val) => setState(() => _searchQuery = val),
        decoration: InputDecoration(
          hintText: 'Search section...',
          prefixIcon:
              const Icon(Icons.search, size: 18, color: AppColors.textSecondary),
          suffixIcon: _searchQuery.isNotEmpty
              ? IconButton(
                  icon: const Icon(Icons.clear, size: 16),
                  onPressed: () => setState(() => _searchQuery = ''),
                )
              : null,
          filled: true,
          fillColor: Colors.white,
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
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
    );
  }

  Widget _buildContent(List<AttendanceSectionItemModel> filtered) {
    if (_loading) {
      return const Padding(
        padding: EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              CircularProgressIndicator(color: AppColors.primary),
              SizedBox(height: 16),
              Text(
                'Fetching attendance records...',
                style: TextStyle(color: AppColors.textSecondary),
              ),
            ],
          ),
        ),
      );
    }

    if (_errorMessage != null) {
      return Padding(
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
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
                onPressed: _fetchReport,
                icon: const Icon(Icons.refresh, size: 18),
                label: const Text('Try Again'),
              ),
            ],
          ),
        ),
      );
    }

    if (_attendances.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.event_busy_outlined,
                  size: 56, color: AppColors.textMuted),
              SizedBox(height: 14),
              Text(
                'No attendance data found',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              SizedBox(height: 6),
              Text(
                'Select branch, date and criteria to view attendance.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
      );
    }

    if (filtered.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(32),
        child: Center(
          child: Text('No matching sections found',
              style: TextStyle(color: AppColors.textSecondary)),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(16, 6, 16, 32),
      itemCount: filtered.length,
      itemBuilder: (context, index) {
        final item = filtered[index];
        return _buildSectionCard(item, index + 1);
      },
    );
  }

  Widget _buildSectionCard(AttendanceSectionItemModel item, int index) {
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
            // Header Row: Section name & Total
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Container(
                      width: 28,
                      height: 28,
                      decoration: BoxDecoration(
                        color: AppColors.primary.withOpacity(0.12),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        '$index',
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary,
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Text(
                      item.section,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                  ],
                ),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppColors.background,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Text(
                    'Total: ${item.total} (${item.boys}B / ${item.girls}G)',
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textSecondary,
                    ),
                  ),
                ),
              ],
            ),

            const Padding(
              padding: EdgeInsets.symmetric(vertical: 12),
              child: Divider(height: 1, color: AppColors.borderLight),
            ),

            // Present & Absent Badges (Clickable to view names)
            Row(
              children: [
                // Present Badge
                Expanded(
                  child: InkWell(
                    onTap: item.presentStudents.isNotEmpty
                        ? () => _showStudentListModal(
                            '${item.section} - Present Students',
                            item.presentStudents,
                            AppColors.success)
                        : null,
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          vertical: 8, horizontal: 10),
                      decoration: BoxDecoration(
                        color: AppColors.success.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                            color: AppColors.success.withOpacity(0.25)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Present',
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                              Text(
                                '${item.presentPercent.toStringAsFixed(1)}%',
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.success,
                                ),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: AppColors.success,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Row(
                              children: [
                                Text(
                                  '${item.present}',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.white,
                                  ),
                                ),
                                if (item.presentStudents.isNotEmpty) ...[
                                  const SizedBox(width: 3),
                                  const Icon(Icons.visibility_outlined,
                                      size: 11, color: Colors.white),
                                ],
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 10),

                // Absent Badge
                Expanded(
                  child: InkWell(
                    onTap: item.absentStudents.isNotEmpty
                        ? () => _showStudentListModal(
                            '${item.section} - Absent Students',
                            item.absentStudents,
                            AppColors.error)
                        : null,
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          vertical: 8, horizontal: 10),
                      decoration: BoxDecoration(
                        color: AppColors.error.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                            color: AppColors.error.withOpacity(0.25)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Absent',
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                              Text(
                                '${item.absentPercent.toStringAsFixed(1)}%',
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.error,
                                ),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: AppColors.error,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Row(
                              children: [
                                Text(
                                  '${item.absent}',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.white,
                                  ),
                                ),
                                if (item.absentStudents.isNotEmpty) ...[
                                  const SizedBox(width: 3),
                                  const Icon(Icons.visibility_outlined,
                                      size: 11, color: Colors.white),
                                ],
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _AttendanceStudentListBottomSheet extends StatefulWidget {
  final String title;
  final List<String> studentNames;
  final Color headerColor;

  const _AttendanceStudentListBottomSheet({
    required this.title,
    required this.studentNames,
    required this.headerColor,
  });

  @override
  State<_AttendanceStudentListBottomSheet> createState() =>
      _AttendanceStudentListBottomSheetState();
}

class _AttendanceStudentListBottomSheetState
    extends State<_AttendanceStudentListBottomSheet> {
  String _search = '';

  List<String> get _filtered {
    if (_search.isEmpty) return widget.studentNames;
    final query = _search.toLowerCase().trim();
    return widget.studentNames
        .where((name) => name.toLowerCase().contains(query))
        .toList();
  }

  @override
  Widget build(BuildContext context) {
    final list = _filtered;

    return Container(
      height: MediaQuery.of(context).size.height * 0.75,
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
            padding: const EdgeInsets.fromLTRB(20, 6, 16, 10),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        widget.title,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: widget.headerColor,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      Text(
                        'Total: ${widget.studentNames.length} students',
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
          ),

          // Search Field
          if (widget.studentNames.length > 5)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
              child: TextField(
                onChanged: (val) => setState(() => _search = val),
                decoration: InputDecoration(
                  hintText: 'Search student name...',
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

          // List
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
                      final name = list[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 8),
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 10),
                        decoration: BoxDecoration(
                          color: AppColors.background,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: AppColors.borderLight),
                        ),
                        child: Row(
                          children: [
                            CircleAvatar(
                              radius: 14,
                              backgroundColor:
                                  widget.headerColor.withOpacity(0.12),
                              child: Text(
                                '${index + 1}',
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: widget.headerColor,
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Text(
                                name,
                                style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textPrimary,
                                ),
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
