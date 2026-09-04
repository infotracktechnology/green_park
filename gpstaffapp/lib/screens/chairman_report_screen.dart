import 'package:flutter/material.dart';
import '../api/api_client.dart';
import '../models/chairman_report_model.dart';
import '../services/chairman_report_pdf_service.dart';
import '../theme/app_theme.dart';

class ChairmanReportScreen extends StatefulWidget {
  const ChairmanReportScreen({super.key});

  @override
  State<ChairmanReportScreen> createState() => _ChairmanReportScreenState();
}

class _ChairmanReportScreenState extends State<ChairmanReportScreen> {
  List<String> _tests = [];
  String? _selectedTest;
  ChairmanReportModel? _reportData;

  bool _loadingTests = true;
  bool _loadingReport = false;
  bool _generatingPdf = false;
  String? _errorMessage;

  String _searchQuery = '';
  String? _selectedCampus;
  String? _selectedSection;

  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchTests();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchTests() async {
    setState(() {
      _loadingTests = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get('/admin/chairman_report');

      if (response.data != null && response.data['status'] == true) {
        final List<dynamic> testsList = response.data['tests'] ?? [];
        final parsedTests = testsList.map((e) => e.toString()).toList();

        setState(() {
          _tests = parsedTests;
          if (_tests.isNotEmpty && _selectedTest == null) {
            _selectedTest = _tests.first;
          }
        });

        if (_selectedTest != null) {
          await _fetchReportData(_selectedTest!);
        }
      } else {
        setState(() {
          _errorMessage = response.data['message'] ?? 'Failed to load tests.';
        });
      }
    } catch (e) {
      debugPrint('Error fetching tests: $e');
      setState(() {
        _errorMessage = 'Failed to load test list. Please check your network.';
      });
    } finally {
      if (mounted) setState(() => _loadingTests = false);
    }
  }

  Future<void> _fetchReportData(String testName) async {
    setState(() {
      _loadingReport = true;
      _errorMessage = null;
      _selectedCampus = null;
      _selectedSection = null;
      _searchQuery = '';
      _searchController.clear();
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get(
        '/admin/chairman_report',
        queryParameters: {'test_name': testName},
      );

      if (response.data != null && response.data['status'] == true) {
        setState(() {
          _reportData = ChairmanReportModel.fromJson(response.data);
        });
      } else {
        setState(() {
          _errorMessage = response.data['message'] ?? 'No results found for this test.';
        });
      }
    } catch (e) {
      debugPrint('Error fetching chairman report: $e');
      setState(() {
        _errorMessage = 'Failed to load report data. Please try again.';
      });
    } finally {
      if (mounted) setState(() => _loadingReport = false);
    }
  }

  Future<void> _exportPdf() async {
    if (_reportData == null || _reportData!.results.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No report data available to export.')),
      );
      return;
    }

    setState(() => _generatingPdf = true);
    try {
      final filtered = _filteredResults;
      // If the user has applied campus/section/search filters, export filtered results.
      // Otherwise, export the complete test results.
      final isFiltered = _searchQuery.isNotEmpty ||
          (_selectedCampus != null && _selectedCampus != 'All') ||
          (_selectedSection != null && _selectedSection != 'All');

      await ChairmanReportPdfService.generateAndOpenPdf(
        report: _reportData!,
        studentsToInclude: isFiltered ? filtered : _reportData!.results,
      );
    } catch (e) {
      debugPrint('Error exporting chairman report PDF: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to generate PDF: $e')),
        );
      }
    } finally {
      if (mounted) setState(() => _generatingPdf = false);
    }
  }

  List<ChairmanStudentResultModel> get _filteredResults {
    if (_reportData == null) return [];

    return _reportData!.results.where((student) {
      final matchesSearch = _searchQuery.isEmpty ||
          student.studentName.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          student.studentId.toLowerCase().contains(_searchQuery.toLowerCase());

      final matchesCampus = _selectedCampus == null ||
          _selectedCampus == 'All' ||
          (student.campus != null &&
              student.campus!.toLowerCase() == _selectedCampus!.toLowerCase());

      final matchesSection = _selectedSection == null ||
          _selectedSection == 'All' ||
          (student.section != null &&
              student.section!.toLowerCase() == _selectedSection!.toLowerCase());

      return matchesSearch && matchesCampus && matchesSection;
    }).toList();
  }

  List<String> get _availableCampuses {
    if (_reportData == null) return [];
    final set = <String>{};
    for (var r in _reportData!.results) {
      if (r.campus != null && r.campus!.trim().isNotEmpty) {
        set.add(r.campus!.trim());
      }
    }
    final list = set.toList()..sort();
    return ['All', ...list];
  }

  List<String> get _availableSections {
    if (_reportData == null) return [];
    final set = <String>{};
    for (var r in _reportData!.results) {
      if (r.section != null && r.section!.trim().isNotEmpty) {
        set.add(r.section!.trim());
      }
    }
    final list = set.toList()..sort();
    return ['All', ...list];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text(
          "Chairman's Report",
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          if (_reportData != null && _reportData!.results.isNotEmpty)
            IconButton(
              icon: _generatingPdf
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: Colors.white,
                      ),
                    )
                  : const Icon(Icons.picture_as_pdf_outlined),
              tooltip: 'Export PDF',
              onPressed: _generatingPdf ? null : _exportPdf,
            ),
        ],
      ),
      body: _loadingTests
          ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
          : RefreshIndicator(
              color: AppColors.primary,
              onRefresh: () async {
                if (_selectedTest != null) {
                  await _fetchReportData(_selectedTest!);
                } else {
                  await _fetchTests();
                }
              },
              child: Column(
                children: [
                  _buildTestSelectorCard(),
                  if (_loadingReport)
                    const Expanded(
                      child: Center(
                        child: CircularProgressIndicator(color: AppColors.primary),
                      ),
                    )
                  else if (_errorMessage != null)
                    Expanded(
                      child: Center(
                        child: Padding(
                          padding: const EdgeInsets.all(24),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.error_outline,
                                  size: 48, color: Colors.redAccent),
                              const SizedBox(height: 12),
                              Text(
                                _errorMessage!,
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                  fontSize: 14,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                              const SizedBox(height: 16),
                              ElevatedButton.icon(
                                onPressed: () {
                                  if (_selectedTest != null) {
                                    _fetchReportData(_selectedTest!);
                                  } else {
                                    _fetchTests();
                                  }
                                },
                                icon: const Icon(Icons.refresh, size: 18),
                                label: const Text('Retry'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppColors.primary,
                                  foregroundColor: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    )
                  else if (_reportData == null || _reportData!.results.isEmpty)
                    Expanded(
                      child: Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.assignment_outlined,
                                size: 56, color: Colors.grey.shade400),
                            const SizedBox(height: 12),
                            const Text(
                              'No result data available for this test.',
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    Expanded(
                      child: ListView(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        children: [
                          _buildSummaryStatsBar(),
                          const SizedBox(height: 12),
                          _buildFilterSection(),
                          const SizedBox(height: 12),
                          _buildResultsHeader(),
                          const SizedBox(height: 8),
                          ..._filteredResults.map((res) => _buildStudentResultCard(res)),
                          const SizedBox(height: 32),
                        ],
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  Widget _buildTestSelectorCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: AppColors.primary,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'SELECT EXAMINATION',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              letterSpacing: 0.8,
              color: Colors.white70,
            ),
          ),
          const SizedBox(height: 6),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 2),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
            ),
            child: DropdownButtonHideUnderline(
              child: DropdownButton<String>(
                isExpanded: true,
                value: _selectedTest,
                hint: const Text('Choose a test', style: TextStyle(fontSize: 14)),
                icon: const Icon(Icons.arrow_drop_down, color: AppColors.primary),
                items: _tests.map((test) {
                  return DropdownMenuItem<String>(
                    value: test,
                    child: Text(
                      test,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  );
                }).toList(),
                onChanged: (val) {
                  if (val != null && val != _selectedTest) {
                    setState(() => _selectedTest = val);
                    _fetchReportData(val);
                  }
                },
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryStatsBar() {
    if (_reportData == null) return const SizedBox.shrink();

    final totalStudents = _reportData!.results.length;
    final topMark = _reportData!.results.isNotEmpty ? _reportData!.results.first.mark : 0;
    final maxMarks = _reportData!.totalMarks;
    final avgMark = totalStudents > 0
        ? (_reportData!.results.map((e) => e.mark).reduce((a, b) => a + b) / totalStudents)
            .toStringAsFixed(1)
        : '0';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: _buildStatItem(
                  label: 'Total Students',
                  value: '$totalStudents',
                  icon: Icons.groups_outlined,
                  color: AppColors.primary,
                ),
              ),
              Container(width: 1, height: 36, color: Colors.grey.shade200),
              Expanded(
                child: _buildStatItem(
                  label: 'Max Total',
                  value: maxMarks > 0 ? '$maxMarks' : '-',
                  icon: Icons.star_border_outlined,
                  color: AppColors.fanta,
                ),
              ),
              Container(width: 1, height: 36, color: Colors.grey.shade200),
              Expanded(
                child: _buildStatItem(
                  label: 'Highest Mark',
                  value: '$topMark',
                  icon: Icons.emoji_events_outlined,
                  color: Colors.green,
                ),
              ),
              Container(width: 1, height: 36, color: Colors.grey.shade200),
              Expanded(
                child: _buildStatItem(
                  label: 'Average Mark',
                  value: avgMark,
                  icon: Icons.show_chart,
                  color: Colors.purple,
                ),
              ),
            ],
          ),
          if (_reportData!.subjects.isNotEmpty) ...[
            const Divider(height: 20),
            Row(
              children: [
                const Icon(Icons.menu_book, size: 15, color: AppColors.textSecondary),
                const SizedBox(width: 6),
                const Text(
                  'Subjects: ',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textSecondary,
                  ),
                ),
                Expanded(
                  child: SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: Row(
                      children: _reportData!.subjects.map((sub) {
                        return Container(
                          margin: const EdgeInsets.only(right: 6),
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withOpacity(0.08),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            sub,
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: AppColors.primary,
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildStatItem({
    required String label,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Column(
      children: [
        Icon(icon, size: 18, color: color),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        Text(
          label,
          style: const TextStyle(
            fontSize: 10,
            fontWeight: FontWeight.w500,
            color: AppColors.textSecondary,
          ),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }

  Widget _buildFilterSection() {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: Column(
        children: [
          // Search Input
          TextField(
            controller: _searchController,
            decoration: InputDecoration(
              hintText: 'Search student name or ID...',
              hintStyle: const TextStyle(fontSize: 13, color: AppColors.textSecondary),
              prefixIcon: const Icon(Icons.search, size: 20, color: AppColors.primary),
              suffixIcon: _searchQuery.isNotEmpty
                  ? IconButton(
                      icon: const Icon(Icons.clear, size: 18),
                      onPressed: () {
                        _searchController.clear();
                        setState(() => _searchQuery = '');
                      },
                    )
                  : null,
              contentPadding: const EdgeInsets.symmetric(vertical: 0, horizontal: 12),
              filled: true,
              fillColor: AppColors.background,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(10),
                borderSide: BorderSide.none,
              ),
            ),
            onChanged: (val) => setState(() => _searchQuery = val),
          ),
          const SizedBox(height: 10),
          // Campus & Section filters
          Row(
            children: [
              if (_availableCampuses.length > 2)
                Expanded(
                  child: _buildFilterDropdown(
                    label: 'Campus',
                    value: _selectedCampus ?? 'All',
                    items: _availableCampuses,
                    onChanged: (val) => setState(() => _selectedCampus = val),
                  ),
                ),
              if (_availableCampuses.length > 2 && _availableSections.length > 2)
                const SizedBox(width: 8),
              if (_availableSections.length > 2)
                Expanded(
                  child: _buildFilterDropdown(
                    label: 'Section',
                    value: _selectedSection ?? 'All',
                    items: _availableSections,
                    onChanged: (val) => setState(() => _selectedSection = val),
                  ),
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildFilterDropdown({
    required String label,
    required String value,
    required List<String> items,
    required ValueChanged<String?> onChanged,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          isExpanded: true,
          value: value,
          icon: const Icon(Icons.arrow_drop_down, size: 20),
          style: const TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
          items: items.map((item) {
            return DropdownMenuItem<String>(
              value: item,
              child: Text('$label: $item', overflow: TextOverflow.ellipsis),
            );
          }).toList(),
          onChanged: onChanged,
        ),
      ),
    );
  }

  Widget _buildResultsHeader() {
    final count = _filteredResults.length;
    final hasFilters = _searchQuery.isNotEmpty ||
        (_selectedCampus != null && _selectedCampus != 'All') ||
        (_selectedSection != null && _selectedSection != 'All');

    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          'Rank List ($count Students)',
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (hasFilters) ...[
              GestureDetector(
                onTap: () {
                  _searchController.clear();
                  setState(() {
                    _searchQuery = '';
                    _selectedCampus = null;
                    _selectedSection = null;
                  });
                },
                child: const Text(
                  'Reset',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: AppColors.fanta,
                  ),
                ),
              ),
              const SizedBox(width: 10),
            ],
            InkWell(
              onTap: _generatingPdf ? null : _exportPdf,
              borderRadius: BorderRadius.circular(8),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: AppColors.primary.withOpacity(0.25)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (_generatingPdf)
                      const SizedBox(
                        width: 13,
                        height: 13,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: AppColors.primary,
                        ),
                      )
                    else
                      const Icon(Icons.picture_as_pdf_outlined,
                          size: 15, color: AppColors.primary),
                    const SizedBox(width: 5),
                    Text(
                      _generatingPdf ? 'Exporting...' : 'Export PDF',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: AppColors.primary,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildStudentResultCard(ChairmanStudentResultModel student) {
    Color rankColor = AppColors.primary;
    if (student.sNo == 1) {
      rankColor = const Color(0xFFFFB800); // Gold
    } else if (student.sNo == 2) {
      rankColor = const Color(0xFF9E9E9E); // Silver
    } else if (student.sNo == 3) {
      rankColor = const Color(0xFFCD7F32); // Bronze
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: student.sNo <= 3 ? rankColor.withOpacity(0.5) : AppColors.borderLight,
          width: student.sNo <= 3 ? 1.5 : 1,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Theme(
        data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
        child: ExpansionTile(
          tilePadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
          leading: Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: rankColor.withOpacity(0.12),
              shape: BoxShape.circle,
              border: Border.all(color: rankColor.withOpacity(0.4)),
            ),
            child: Center(
              child: Text(
                '#${student.sNo}',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: rankColor,
                ),
              ),
            ),
          ),
          title: Row(
            children: [
              Expanded(
                child: Text(
                  student.studentName,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  '${student.mark} M',
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                ),
              ),
            ],
          ),
          subtitle: Padding(
            padding: const EdgeInsets.only(top: 4),
            child: Wrap(
              spacing: 6,
              runSpacing: 4,
              children: [
                _buildInfoBadge('ID: ${student.studentId}', Colors.grey.shade700),
                if (student.campus != null && student.campus!.isNotEmpty)
                  _buildInfoBadge(student.campus!, AppColors.primary),
                if (student.section != null && student.section!.isNotEmpty)
                  _buildInfoBadge('Sec: ${student.section}', AppColors.fanta),
                if (student.stmode != null && student.stmode!.isNotEmpty)
                  _buildInfoBadge(
                    student.stmode!,
                    student.stmode == 'OMR' ? Colors.orange.shade800 : Colors.blue.shade700,
                  ),
                if (student.gender != null && student.gender!.isNotEmpty)
                  _buildInfoBadge(student.gender!, Colors.grey.shade600),
              ],
            ),
          ),
          children: [
            Container(
              padding: const EdgeInsets.fromLTRB(14, 0, 14, 14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Divider(height: 16),
                  const Text(
                    'SUBJECT BREAKDOWN',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 0.5,
                      color: AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 8),
                  ...student.subjectMarks.entries.map((entry) {
                    final subName = entry.key;
                    final subMark = entry.value;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 6),
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: AppColors.background,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppColors.borderLight),
                      ),
                      child: Row(
                        children: [
                          Expanded(
                            flex: 3,
                            child: Text(
                              subName,
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textPrimary,
                              ),
                            ),
                          ),
                          _buildMetricBadge('R', '${subMark.right}', Colors.green),
                          const SizedBox(width: 6),
                          _buildMetricBadge('W', '${subMark.wrong}', Colors.redAccent),
                          const SizedBox(width: 6),
                          _buildMetricBadge('L', '${subMark.left}', Colors.amber.shade800),
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: AppColors.primary,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              '${subMark.total}',
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ],
                      ),
                    );
                  }),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoBadge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 10.5,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }

  Widget _buildMetricBadge(String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            '$label:',
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          const SizedBox(width: 2),
          Text(
            value,
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}
