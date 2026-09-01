import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../api/api_client.dart';
import '../models/student_attendance_entry_model.dart';
import '../theme/app_theme.dart';

class StudentAttendanceEntryScreen extends StatefulWidget {
  final String? initialSection;

  const StudentAttendanceEntryScreen({super.key, this.initialSection});

  @override
  State<StudentAttendanceEntryScreen> createState() =>
      _StudentAttendanceEntryScreenState();
}

class _StudentAttendanceEntryScreenState
    extends State<StudentAttendanceEntryScreen> {
  DateTime _selectedDate = DateTime.now();
  String _selectedSection = '';
  String _selectedTiming = 'Morning,Afternoon'; // 'Morning', 'Afternoon', 'Morning,Afternoon'
  dynamic _branchId;
  String? _academicYear;

  List<String> _handlingSections = [];
  List<String> _sections = [];
  List<StudentAttendanceItem> _students = [];

  bool _loading = false;
  bool _saving = false;
  bool _isHoliday = false;
  String? _errorMessage;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    if (widget.initialSection != null && widget.initialSection!.isNotEmpty) {
      _selectedSection = widget.initialSection!;
    }
    _fetchAttendance();
  }

  Future<void> _fetchAttendance() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final formattedDate = DateFormat('yyyy-MM-dd').format(_selectedDate);

      final queryParams = <String, dynamic>{
        'attendance_date': formattedDate,
        'attendance_timing': _selectedTiming,
      };

      if (_selectedSection.isNotEmpty) {
        queryParams['section'] = _selectedSection;
      }
      if (_branchId != null) {
        queryParams['branch_id'] = _branchId;
      }

      final response = await dio.get(
        '/admin/student_attendance',
        queryParameters: queryParams,
      );

      if (response.data != null && response.data['status'] == true) {
        final entryResponse =
            StudentAttendanceEntryResponse.fromJson(response.data);

        setState(() {
          _academicYear = entryResponse.academicYear;
          _branchId = entryResponse.branchId;
          _handlingSections = entryResponse.handlingSections;
          _sections = entryResponse.sections;
          _isHoliday = entryResponse.isHoliday;

          if (_selectedSection.isEmpty &&
              entryResponse.selectedSection != null &&
              entryResponse.selectedSection!.isNotEmpty) {
            _selectedSection = entryResponse.selectedSection!;
          }

          _students = entryResponse.students;
        });
      } else {
        setState(() {
          _errorMessage =
              response.data?['message'] ?? 'Failed to load attendance details';
        });
      }
    } catch (e) {
      debugPrint('Error loading attendance: $e');
      setState(() {
        _errorMessage = 'Failed to load attendance. Please try again.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _saveAttendance() async {
    if (_students.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No students to save attendance for')),
      );
      return;
    }

    if (_selectedSection.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a class/section')),
      );
      return;
    }

    setState(() => _saving = true);

    try {
      final dio = ApiClient().dio;
      final formattedDate = DateFormat('yyyy-MM-dd').format(_selectedDate);

      final records = <Map<String, dynamic>>[];
      final hasMorning = _selectedTiming.contains('Morning');
      final hasAfternoon = _selectedTiming.contains('Afternoon');

      for (final s in _students) {
        if (hasMorning) {
          records.add({
            'student_id': s.studentId,
            'timing': 'Morning',
            'status': s.morningStatus,
            'id': s.morningId,
          });
        }
        if (hasAfternoon) {
          records.add({
            'student_id': s.studentId,
            'timing': 'Afternoon',
            'status': s.afternoonStatus,
            'id': s.afternoonId,
          });
        }
      }

      final payload = {
        'academic_year': _academicYear,
        'branch_id': _branchId,
        'attendance_date': formattedDate,
        'section': _selectedSection,
        'records': records,
      };

      final response =
          await dio.post('/admin/student_attendance', data: payload);

      if (response.data != null && response.data['status'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Attendance saved successfully!'),
              backgroundColor: Colors.green,
            ),
          );
          _fetchAttendance();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                  response.data?['message'] ?? 'Failed to save attendance'),
              backgroundColor: AppColors.error,
            ),
          );
        }
      }
    } catch (e) {
      debugPrint('Error saving attendance: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Error saving attendance. Please try again.'),
            backgroundColor: AppColors.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  Future<void> _deleteAttendance() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Attendance?'),
        content: Text(
          'Are you sure you want to delete attendance for section "$_selectedSection" on ${DateFormat('dd/MM/yyyy').format(_selectedDate)}?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Delete'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() => _loading = true);

    try {
      final dio = ApiClient().dio;
      final formattedDate = DateFormat('yyyy-MM-dd').format(_selectedDate);

      final response = await dio.get(
        '/admin/student_attendance',
        queryParameters: {
          'attendance_date': formattedDate,
          'section': _selectedSection,
          'branch_id': _branchId,
          'timing': _selectedTiming,
          'delete': 1,
        },
      );

      if (response.data != null && response.data['status'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Attendance deleted successfully'),
              backgroundColor: Colors.green,
            ),
          );
          _fetchAttendance();
        }
      }
    } catch (e) {
      debugPrint('Error deleting attendance: $e');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _markAll(String timing, String status) {
    setState(() {
      for (final s in _students) {
        if (timing == 'Morning') {
          s.morningStatus = status;
        } else if (timing == 'Afternoon') {
          s.afternoonStatus = status;
        } else {
          s.morningStatus = status;
          s.afternoonStatus = status;
        }
      }
    });
  }

  List<StudentAttendanceItem> get _filteredStudents {
    if (_searchQuery.trim().isEmpty) return _students;
    final q = _searchQuery.toLowerCase().trim();
    return _students.where((s) {
      return s.studentName.toLowerCase().contains(q) ||
          s.studentId.toLowerCase().contains(q);
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _filteredStudents;
    final hasMorning = _selectedTiming.contains('Morning');
    final hasAfternoon = _selectedTiming.contains('Afternoon');

    int morningPresent =
        _students.where((s) => s.morningStatus == 'P').length;
    int morningAbsent =
        _students.where((s) => s.morningStatus == 'A').length;
    int afternoonPresent =
        _students.where((s) => s.afternoonStatus == 'P').length;
    int afternoonAbsent =
        _students.where((s) => s.afternoonStatus == 'A').length;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Student Attendance Entry'),
        backgroundColor: AppColors.primary,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: _fetchAttendance,
          ),
          PopupMenuButton<String>(
            onSelected: (val) {
              if (val == 'delete') _deleteAttendance();
            },
            itemBuilder: (ctx) => [
              const PopupMenuItem(
                value: 'delete',
                child: Row(
                  children: [
                    Icon(Icons.delete_outline,
                        size: 18, color: AppColors.error),
                    SizedBox(width: 8),
                    Text('Delete Entry',
                        style: TextStyle(color: AppColors.error)),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
      body: Column(
        children: [
          // Filter & Class Selection Card
          _buildFilterCard(),

          // Holiday Warning Banner
          if (_isHoliday) _buildHolidayBanner(),

          // Summary & Quick Actions Bar
          if (!_loading && _students.isNotEmpty)
            _buildSummaryBar(
              hasMorning: hasMorning,
              hasAfternoon: hasAfternoon,
              morningPresent: morningPresent,
              morningAbsent: morningAbsent,
              afternoonPresent: afternoonPresent,
              afternoonAbsent: afternoonAbsent,
            ),

          // Search Bar
          if (_students.isNotEmpty)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: Container(
                height: 44,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: TextField(
                  onChanged: (val) => setState(() => _searchQuery = val),
                  style: const TextStyle(fontSize: 13),
                  decoration: const InputDecoration(
                    hintText: 'Search by student name or ID...',
                    hintStyle:
                        TextStyle(fontSize: 12, color: AppColors.textMuted),
                    prefixIcon: Icon(Icons.search, size: 20, color: AppColors.textMuted),
                    border: InputBorder.none,
                    contentPadding: EdgeInsets.symmetric(vertical: 12),
                  ),
                ),
              ),
            ),

          // Main Content
          Expanded(
            child: _buildStudentList(filtered, hasMorning, hasAfternoon),
          ),
        ],
      ),
      bottomNavigationBar: _students.isNotEmpty
          ? _buildBottomSaveBar()
          : null,
    );
  }

  Widget _buildFilterCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(bottom: BorderSide(color: AppColors.borderLight)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section & Date Selectors Row
          Row(
            children: [
              // Section / Handling Class Selector
              Expanded(
                flex: 6,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        const Text(
                          'CLASS / SECTION',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textSecondary,
                            letterSpacing: 0.5,
                          ),
                        ),
                        if (_handlingSections.isNotEmpty) ...[
                          const SizedBox(width: 4),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 6, vertical: 1),
                            decoration: BoxDecoration(
                              color: AppColors.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: const Text(
                              'Handling',
                              style: TextStyle(
                                fontSize: 9,
                                fontWeight: FontWeight.bold,
                                color: AppColors.primary,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      decoration: BoxDecoration(
                        color: AppColors.background,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: DropdownButtonHideUnderline(
                        child: DropdownButton<String>(
                          isExpanded: true,
                          value: _sections.contains(_selectedSection)
                              ? _selectedSection
                              : (_sections.isNotEmpty ? _sections.first : ''),
                          items: _sections.map((s) {
                            final isHandling = _handlingSections.contains(s);
                            return DropdownMenuItem(
                              value: s,
                              child: Row(
                                children: [
                                  Text(
                                    s,
                                    style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: isHandling
                                          ? FontWeight.bold
                                          : FontWeight.normal,
                                      color: isHandling
                                          ? AppColors.primary
                                          : AppColors.textPrimary,
                                    ),
                                  ),
                                  if (isHandling) ...[
                                    const SizedBox(width: 6),
                                    const Icon(Icons.star,
                                        size: 12, color: Colors.amber),
                                  ],
                                ],
                              ),
                            );
                          }).toList(),
                          onChanged: (val) {
                            if (val != null) {
                              setState(() => _selectedSection = val);
                              _fetchAttendance();
                            }
                          },
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 10),

              // Date Picker
              Expanded(
                flex: 4,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'DATE',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textSecondary,
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 6),
                    InkWell(
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: _selectedDate,
                          firstDate: DateTime(2020),
                          lastDate: DateTime.now(),
                        );
                        if (picked != null) {
                          setState(() => _selectedDate = picked);
                          _fetchAttendance();
                        }
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 10),
                        decoration: BoxDecoration(
                          color: AppColors.background,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppColors.border),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              DateFormat('dd/MM/yyyy').format(_selectedDate),
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary,
                              ),
                            ),
                            const Icon(Icons.calendar_today,
                                size: 14, color: AppColors.primary),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Timing Selector
          Row(
            children: [
              _buildTimingChip('Morning,Afternoon', 'All Day (Both)'),
              const SizedBox(width: 8),
              _buildTimingChip('Morning', 'Morning Only'),
              const SizedBox(width: 8),
              _buildTimingChip('Afternoon', 'Afternoon Only'),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTimingChip(String value, String label) {
    final selected = _selectedTiming == value;
    return Expanded(
      child: InkWell(
        onTap: () {
          if (_selectedTiming != value) {
            setState(() => _selectedTiming = value);
            _fetchAttendance();
          }
        },
        borderRadius: BorderRadius.circular(10),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 7),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : AppColors.background,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(
              color: selected ? AppColors.primary : AppColors.border,
            ),
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: selected ? Colors.white : AppColors.textSecondary,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHolidayBanner() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      color: Colors.amber.shade100,
      child: const Row(
        children: [
          Icon(Icons.warning_amber_rounded, size: 18, color: Colors.orange),
          SizedBox(width: 8),
          Expanded(
            child: Text(
              'Holiday declared for this section on selected date.',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: Colors.brown,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryBar({
    required bool hasMorning,
    required bool hasAfternoon,
    required int morningPresent,
    required int morningAbsent,
    required int afternoonPresent,
    required int afternoonAbsent,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(bottom: BorderSide(color: AppColors.borderLight)),
      ),
      child: Row(
        children: [
          // Total Badge
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.blueGrey.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              'Total: ${_students.length}',
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: Colors.blueGrey,
              ),
            ),
          ),
          const SizedBox(width: 10),

          // Present / Absent Summary
          Expanded(
            child: Row(
              children: [
                if (hasMorning) ...[
                  _buildStatBadge('M: $morningPresent P', Colors.green),
                  const SizedBox(width: 4),
                  _buildStatBadge('M: $morningAbsent A', AppColors.error),
                ],
                if (hasMorning && hasAfternoon) const SizedBox(width: 6),
                if (hasAfternoon) ...[
                  _buildStatBadge('A: $afternoonPresent P', Colors.green),
                  const SizedBox(width: 4),
                  _buildStatBadge('A: $afternoonAbsent A', AppColors.error),
                ],
              ],
            ),
          ),

          // Quick Mark All
          PopupMenuButton<String>(
            tooltip: 'Quick Actions',
            icon: const Icon(Icons.flash_on, size: 20, color: AppColors.fanta),
            onSelected: (val) {
              if (val == 'all_p') _markAll('Both', 'P');
              if (val == 'all_a') _markAll('Both', 'A');
              if (val == 'm_all_p') _markAll('Morning', 'P');
              if (val == 'm_all_a') _markAll('Morning', 'A');
              if (val == 'a_all_p') _markAll('Afternoon', 'P');
              if (val == 'a_all_a') _markAll('Afternoon', 'A');
            },
            itemBuilder: (ctx) => [
              const PopupMenuItem(
                value: 'all_p',
                child: Text('Mark All Present (All Day)'),
              ),
              const PopupMenuItem(
                value: 'all_a',
                child: Text('Mark All Absent (All Day)'),
              ),
              if (hasMorning) ...[
                const PopupMenuItem(
                  value: 'm_all_p',
                  child: Text('Mark Morning All Present'),
                ),
                const PopupMenuItem(
                  value: 'm_all_a',
                  child: Text('Mark Morning All Absent'),
                ),
              ],
              if (hasAfternoon) ...[
                const PopupMenuItem(
                  value: 'a_all_p',
                  child: Text('Mark Afternoon All Present'),
                ),
                const PopupMenuItem(
                  value: 'a_all_a',
                  child: Text('Mark Afternoon All Absent'),
                ),
              ],
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatBadge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.bold,
          color: color,
        ),
      ),
    );
  }

  Widget _buildStudentList(
    List<StudentAttendanceItem> list,
    bool hasMorning,
    bool hasAfternoon,
  ) {
    if (_loading) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(color: AppColors.primary),
            SizedBox(height: 12),
            Text('Loading student roster...',
                style: TextStyle(color: AppColors.textSecondary)),
          ],
        ),
      );
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 40, color: AppColors.error),
              const SizedBox(height: 10),
              Text(_errorMessage!, textAlign: TextAlign.center),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: _fetchAttendance,
                child: const Text('Try Again'),
              ),
            ],
          ),
        ),
      );
    }

    if (list.isEmpty) {
      return const Center(
        child: Padding(
          padding: EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.people_outline,
                  size: 48, color: AppColors.textMuted),
              SizedBox(height: 10),
              Text(
                'No students found in this section',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      itemCount: list.length,
      itemBuilder: (ctx, index) {
        final s = list[index];
        return _buildStudentCard(s, index + 1, hasMorning, hasAfternoon);
      },
    );
  }

  Widget _buildStudentCard(
    StudentAttendanceItem s,
    int index,
    bool hasMorning,
    bool hasAfternoon,
  ) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 4,
            offset: const Offset(0, 1),
          ),
        ],
      ),
      child: Row(
        children: [
          // Index Badge
          CircleAvatar(
            radius: 14,
            backgroundColor: AppColors.primary.withOpacity(0.08),
            child: Text(
              '$index',
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: AppColors.primary,
              ),
            ),
          ),
          const SizedBox(width: 10),

          // Student Details
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  s.studentName,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 2),
                Row(
                  children: [
                    Text(
                      'ID: ${s.studentId}',
                      style: const TextStyle(
                        fontSize: 11,
                        color: AppColors.textMuted,
                      ),
                    ),
                    if (s.coachingType != null &&
                        s.coachingType!.isNotEmpty) ...[
                      const SizedBox(width: 6),
                      Text(
                        '• ${s.coachingType}',
                        style: const TextStyle(
                          fontSize: 10,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ),

          // Attendance Toggle Buttons
          if (hasMorning) ...[
            _buildTimingToggle(
              //label: 'M',
              status: s.morningStatus,
              onToggle: (newStatus) {
                setState(() => s.morningStatus = newStatus);
              },
            ),
          ],
          if (hasMorning && hasAfternoon) const SizedBox(width: 6),
          if (hasAfternoon) ...[
            _buildTimingToggle(
              //label: 'A',
              status: s.afternoonStatus,
              onToggle: (newStatus) {
                setState(() => s.afternoonStatus = newStatus);
              },
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildTimingToggle({
    required String status,
    required Function(String) onToggle,
  }) {
    final isP = status == 'P';
    return Container(
      height: 34,
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(
          color: isP ? Colors.green.shade400 : AppColors.error.withOpacity(0.5),
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Label indicator (M / A)
          // Padding(
          //   padding: const EdgeInsets.symmetric(horizontal: 6),
          //   child: Text(
          //     label,
          //     style: const TextStyle(
          //       fontSize: 10,
          //       fontWeight: FontWeight.bold,
          //       color: AppColors.textSecondary,
          //     ),
          //   ),
          // ),
          // Present Button
          InkWell(
            onTap: () => onToggle('P'),
            borderRadius: BorderRadius.circular(8),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
              decoration: BoxDecoration(
                color: isP ? Colors.green : Colors.transparent,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                'P',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: isP ? Colors.white : Colors.green.shade700,
                ),
              ),
            ),
          ),
          // Absent Button
          InkWell(
            onTap: () => onToggle('A'),
            borderRadius: BorderRadius.circular(8),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
              decoration: BoxDecoration(
                color: !isP ? AppColors.error : Colors.transparent,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                'A',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: !isP ? Colors.white : AppColors.error,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBottomSaveBar() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: AppColors.borderLight)),
      ),
      child: SafeArea(
        child: SizedBox(
          width: double.infinity,
          height: 48,
          child: ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(14),
              ),
            ),
            onPressed: _saving ? null : _saveAttendance,
            icon: _saving
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      color: Colors.white,
                      strokeWidth: 2,
                    ),
                  )
                : const Icon(Icons.check_circle_outline, size: 20),
            label: Text(
              _saving ? 'Saving Attendance...' : 'Save Attendance',
              style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
