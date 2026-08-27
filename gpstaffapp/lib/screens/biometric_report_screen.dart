import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../api/api_client.dart';
import '../models/biometric_report_model.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';

class BiometricReportScreen extends StatefulWidget {
  const BiometricReportScreen({super.key});

  @override
  State<BiometricReportScreen> createState() => _BiometricReportScreenState();
}

class _BiometricReportScreenState extends State<BiometricReportScreen> {
  DateTime _selectedDate = DateTime.now();
  String? _selectedBranchId;
  List<BranchItem> _branches = [];

  List<BiometricStaffModel> _allStaffs = [];
  bool _loading = false;
  String? _errorMessage;
  String _searchQuery = '';
  String _statusFilter = 'ALL'; // ALL, PRESENT, HALF_DAY, ABSENT

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initializeData();
    });
  }

  Future<void> _initializeData() async {
    final filterProvider =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    if (filterProvider.master == null) {
      await filterProvider.fetchMasterData();
    }

    if (mounted) {
      setState(() {
        _branches = filterProvider.master?.branches ?? [];
        if (_branches.isNotEmpty && _selectedBranchId == null) {
          _selectedBranchId = _branches.first.id.toString();
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

      final response = await dio.get(
        '/admin/biometric/report',
        queryParameters: {
          'branch_id': _selectedBranchId,
          'date': formattedDate,
        },
      );

      if (response.data != null) {
        final status = response.data['status'] == true;
        final list = response.data['staffs'];

        if (response.data['branches'] != null && _branches.isEmpty) {
          final bList = response.data['branches'];
          if (bList is List) {
            _branches = bList.map((e) => BranchItem.fromDynamic(e)).toList();
          }
        }

        if (status && list is List) {
          setState(() {
            _allStaffs =
                list.map((e) => BiometricStaffModel.fromJson(e)).toList();
          });
        } else {
          final message = response.data['message'] ?? 'Failed to load report';
          setState(() {
            _allStaffs = [];
            _errorMessage = message.toString();
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching biometric report: $e');
      setState(() {
        _errorMessage = 'Failed to load biometric data. Please try again.';
      });
    } finally {
      if (mounted) {
        setState(() => _loading = false);
      }
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
      setState(() {
        _selectedDate = picked;
      });
      _fetchReport();
    }
  }

  List<BiometricStaffModel> get _filteredStaffs {
    return _allStaffs.where((staff) {
      final query = _searchQuery.toLowerCase().trim();
      final matchesSearch = query.isEmpty ||
          staff.name.toLowerCase().contains(query) ||
          staff.biometricNo.toLowerCase().contains(query) ||
          staff.department.toLowerCase().contains(query) ||
          staff.schoolInitial.toLowerCase().contains(query);

      if (!matchesSearch) return false;

      final dayVal = double.tryParse(staff.day.toString()) ?? 0.0;
      if (_statusFilter == 'PRESENT') {
        return dayVal == 1.0;
      } else if (_statusFilter == 'HALF_DAY') {
        return dayVal == 0.5;
      } else if (_statusFilter == 'ABSENT') {
        return dayVal == 0.0;
      }

      return true;
    }).toList();
  }

  int get _presentCount => _allStaffs
      .where((s) => (double.tryParse(s.day.toString()) ?? 0.0) == 1.0)
      .length;

  int get _halfDayCount => _allStaffs
      .where((s) => (double.tryParse(s.day.toString()) ?? 0.0) == 0.5)
      .length;

  int get _absentCount => _allStaffs
      .where((s) => (double.tryParse(s.day.toString()) ?? 0.0) == 0.0)
      .length;

  @override
  Widget build(BuildContext context) {
    final filtered = _filteredStaffs;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Biometric Report'),
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
              // Filter Card
              _buildFilterHeader(),

              // Stats summary
              if (_allStaffs.isNotEmpty) _buildStatsSection(),

              // Search & Filter Tabs
              if (_allStaffs.isNotEmpty) _buildSearchAndFilterTabs(),

              // Content Area
              _buildContent(filtered),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFilterHeader() {
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
            // Branch Dropdown
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
              padding: const EdgeInsets.symmetric(horizontal: 14),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  isExpanded: true,
                  value: _selectedBranchId,
                  hint: const Text('Select Branch'),
                  items: _branches.map((b) {
                    return DropdownMenuItem<String>(
                      value: b.id.toString(),
                      child: Text(
                        b.name,
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textPrimary,
                        ),
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
            const SizedBox(height: 14),

            // Date Selection Row
            Row(
              children: [
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
                              horizontal: 14, vertical: 12),
                          decoration: BoxDecoration(
                            color: AppColors.background,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                DateFormat('dd MMM yyyy').format(_selectedDate),
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textPrimary,
                                ),
                              ),
                              const Icon(Icons.calendar_month_outlined,
                                  size: 18, color: AppColors.primary),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                Padding(
                  padding: const EdgeInsets.only(top: 20),
                  child: ElevatedButton.icon(
                    onPressed: _loading ? null : _fetchReport,
                    icon: _loading
                        ? const SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              color: Colors.white,
                            ),
                          )
                        : const Icon(Icons.search, size: 18),
                    label: const Text('View'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.fanta,
                      padding: const EdgeInsets.symmetric(
                          horizontal: 20, vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
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

  Widget _buildStatsSection() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Row(
        children: [
          Expanded(
            child: _buildStatBadge(
              label: 'Total Staff',
              count: _allStaffs.length,
              color: AppColors.primary,
              icon: Icons.people_alt_outlined,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildStatBadge(
              label: 'Full Day',
              count: _presentCount,
              color: AppColors.success,
              icon: Icons.check_circle_outline,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildStatBadge(
              label: 'Half Day',
              count: _halfDayCount,
              color: AppColors.warning,
              icon: Icons.timelapse_outlined,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildStatBadge(
              label: 'Absent',
              count: _absentCount,
              color: AppColors.error,
              icon: Icons.cancel_outlined,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatBadge({
    required String label,
    required int count,
    required Color color,
    required IconData icon,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
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
          Icon(icon, size: 18, color: color),
          const SizedBox(height: 4),
          Text(
            '$count',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              fontSize: 9,
              fontWeight: FontWeight.w600,
              color: AppColors.textSecondary,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Widget _buildSearchAndFilterTabs() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
      child: Column(
        children: [
          // Search box
          TextField(
            onChanged: (val) => setState(() => _searchQuery = val),
            decoration: InputDecoration(
              hintText: 'Search by staff name, ID or department...',
              prefixIcon: const Icon(Icons.search,
                  size: 20, color: AppColors.textSecondary),
              suffixIcon: _searchQuery.isNotEmpty
                  ? IconButton(
                      icon: const Icon(Icons.clear, size: 18),
                      onPressed: () => setState(() => _searchQuery = ''),
                    )
                  : null,
              filled: true,
              fillColor: Colors.white,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
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
          const SizedBox(height: 12),

          // Filter Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: [
                _buildFilterChip('ALL', 'All (${_allStaffs.length})'),
                const SizedBox(width: 8),
                _buildFilterChip('PRESENT', 'Full Day ($_presentCount)'),
                const SizedBox(width: 8),
                _buildFilterChip('HALF_DAY', 'Half Day ($_halfDayCount)'),
                const SizedBox(width: 8),
                _buildFilterChip('ABSENT', 'Absent ($_absentCount)'),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String key, String label) {
    final isSelected = _statusFilter == key;
    return ChoiceChip(
      label: Text(
        label,
        style: TextStyle(
          fontSize: 12,
          fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
          color: isSelected ? Colors.white : AppColors.textPrimary,
        ),
      ),
      selected: isSelected,
      selectedColor: AppColors.primary,
      backgroundColor: Colors.white,
      side: BorderSide(
        color: isSelected ? AppColors.primary : AppColors.border,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      onSelected: (selected) {
        if (selected) {
          setState(() => _statusFilter = key);
        }
      },
    );
  }

  Widget _buildContent(List<BiometricStaffModel> filtered) {
    if (_loading) {
      return const Padding(
        padding: EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              CircularProgressIndicator(color: AppColors.primary),
              SizedBox(height: 16),
              Text(
                'Fetching biometric attendance...',
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
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.info_outline, size: 52, color: AppColors.error),
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
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                ),
              ),
            ],
          ),
        ),
      );
    }

    if (_allStaffs.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.fingerprint,
                  size: 64, color: AppColors.textMuted.withOpacity(0.5)),
              const SizedBox(height: 16),
              const Text(
                'No attendance records found',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              const Text(
                'Select branch and date to view biometric logs.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
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
        padding: EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.search_off, size: 48, color: AppColors.textMuted),
              SizedBox(height: 12),
              Text(
                'No matching staff records found',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(16, 4, 16, 32),
      itemCount: filtered.length,
      itemBuilder: (context, index) {
        final staff = filtered[index];
        return _buildStaffCard(staff, index + 1);
      },
    );
  }

  Widget _buildStaffCard(BiometricStaffModel staff, int index) {
    final isSession1Present = staff.session1 == 'P';
    final isSession2Present = staff.session2 == 'P';
    final dayVal = double.tryParse(staff.day.toString()) ?? 0.0;

    Color dayColor = AppColors.error;
    String dayLabel = 'Absent';
    if (dayVal == 1.0) {
      dayColor = AppColors.success;
      dayLabel = 'Full Day';
    } else if (dayVal == 0.5) {
      dayColor = AppColors.warning;
      dayLabel = 'Half Day';
    }

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
            // Top Row: Staff Details & Day Status
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                CircleAvatar(
                  radius: 20,
                  backgroundColor: AppColors.primary.withOpacity(0.12),
                  child: Text(
                    staff.schoolInitial.isNotEmpty
                        ? staff.schoolInitial
                        : (staff.name.isNotEmpty ? staff.name[0] : '#'),
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary,
                      fontSize: 13,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        staff.name,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Row(
                        children: [
                          if (staff.biometricNo.isNotEmpty) ...[
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 6, vertical: 2),
                              decoration: BoxDecoration(
                                color: AppColors.background,
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(color: AppColors.border),
                              ),
                              child: Text(
                                'ID: ${staff.biometricNo}',
                                style: const TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ),
                            const SizedBox(width: 6),
                          ],
                          if (staff.department.isNotEmpty)
                            Flexible(
                              child: Text(
                                staff.department,
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: AppColors.textSecondary,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
                // Day status badge
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: dayColor.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: dayColor.withOpacity(0.3)),
                  ),
                  child: Text(
                    dayLabel,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: dayColor,
                    ),
                  ),
                ),
              ],
            ),

            const Padding(
              padding: EdgeInsets.symmetric(vertical: 12),
              child: Divider(height: 1, color: AppColors.borderLight),
            ),

            // Timing & Session Badges
            Row(
              children: [
                Expanded(
                  child: _buildTimeInfo(
                    label: 'First In',
                    time: staff.firstIn,
                    icon: Icons.login,
                  ),
                ),
                Expanded(
                  child: _buildTimeInfo(
                    label: 'Last Out',
                    time: staff.lastOut,
                    icon: Icons.logout,
                  ),
                ),
                Expanded(
                  child: _buildTimeInfo(
                    label: 'Total Hours',
                    time: '${staff.hours} hrs',
                    icon: Icons.access_time,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 12),

            // Sessions Row
            Row(
              children: [
                Expanded(
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        vertical: 6, horizontal: 10),
                    decoration: BoxDecoration(
                      color: isSession1Present
                          ? AppColors.success.withOpacity(0.08)
                          : AppColors.error.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: isSession1Present
                            ? AppColors.success.withOpacity(0.2)
                            : AppColors.error.withOpacity(0.2),
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Session 1',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 6, vertical: 1),
                          decoration: BoxDecoration(
                            color: isSession1Present
                                ? AppColors.success
                                : AppColors.error,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            staff.session1,
                            style: const TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        vertical: 6, horizontal: 10),
                    decoration: BoxDecoration(
                      color: isSession2Present
                          ? AppColors.success.withOpacity(0.08)
                          : AppColors.error.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: isSession2Present
                            ? AppColors.success.withOpacity(0.2)
                            : AppColors.error.withOpacity(0.2),
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Session 2',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 6, vertical: 1),
                          decoration: BoxDecoration(
                            color: isSession2Present
                                ? AppColors.success
                                : AppColors.error,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            staff.session2,
                            style: const TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),

            if (staff.timeLogs.isNotEmpty && staff.timeLogs != '-') ...[
              const SizedBox(height: 10),
              Container(
                width: double.infinity,
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                decoration: BoxDecoration(
                  color: AppColors.background,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.fingerprint,
                        size: 14, color: AppColors.textSecondary),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        'Punches: ${staff.timeLogs}',
                        style: const TextStyle(
                          fontSize: 11,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildTimeInfo({
    required String label,
    required String time,
    required IconData icon,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 12, color: AppColors.textMuted),
            const SizedBox(width: 4),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                color: AppColors.textMuted,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
        const SizedBox(height: 3),
        Text(
          time.isNotEmpty ? time : '-',
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
      ],
    );
  }
}
