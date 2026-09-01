import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../api/api_client.dart';
import '../models/individual_biometric_model.dart';
import '../theme/app_theme.dart';

class IndividualBiometricScreen extends StatefulWidget {
  const IndividualBiometricScreen({super.key});

  @override
  State<IndividualBiometricScreen> createState() =>
      _IndividualBiometricScreenState();
}

class _IndividualBiometricScreenState extends State<IndividualBiometricScreen> {
  DateTime _selectedMonth = DateTime.now();
  IndividualBiometricResponseModel? _data;
  bool _loading = true;
  String? _errorMessage;
  String _statusFilter = 'ALL';
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _fetchBiometricReport();
  }

  Future<void> _fetchBiometricReport() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final monthKey = DateFormat('yyyy-MM').format(_selectedMonth);

      final response = await dio.get(
        '/admin/staff/my_biometric_report',
        queryParameters: {'month': monthKey},
      );

      if (response.data != null && response.data['status'] == true) {
        setState(() {
          _data = IndividualBiometricResponseModel.fromJson(response.data);
        });
      } else {
        setState(() {
          _errorMessage = response.data?['message'] ??
              'Failed to load biometric attendance.';
        });
      }
    } catch (e) {
      debugPrint('Error fetching individual biometric: $e');
      setState(() {
        _errorMessage =
            'Failed to load biometric data. Please check connection.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _previousMonth() {
    setState(() {
      _selectedMonth = DateTime(
        _selectedMonth.year,
        _selectedMonth.month - 1,
        1,
      );
    });
    _fetchBiometricReport();
  }

  void _nextMonth() {
    final next = DateTime(
      _selectedMonth.year,
      _selectedMonth.month + 1,
      1,
    );
    if (next.isBefore(DateTime.now().add(const Duration(days: 30)))) {
      setState(() {
        _selectedMonth = next;
      });
      _fetchBiometricReport();
    }
  }

  Future<void> _selectMonthYear() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedMonth,
      firstDate: DateTime(now.year - 2, 1, 1),
      lastDate: DateTime(now.year, now.month, 1),
      initialDatePickerMode: DatePickerMode.year,
      helpText: 'SELECT MONTH & YEAR',
    );

    if (picked != null) {
      setState(() {
        _selectedMonth = DateTime(picked.year, picked.month, 1);
      });
      _fetchBiometricReport();
    }
  }

  @override
  Widget build(BuildContext context) {
    final filteredLogs = _getFilteredLogs();

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text(
          'My Biometric Attendance',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: _fetchBiometricReport,
          ),
        ],
      ),
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            )
          : _errorMessage != null
              ? _buildErrorView()
              : RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: _fetchBiometricReport,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // 1. Month Selector Bar
                        _buildMonthSelector(),
                        const SizedBox(height: 12),

                        // 2. Staff Info Card
                        if (_data?.staff != null) _buildStaffInfoCard(),
                        const SizedBox(height: 14),

                        // 3. Month Summary Cards
                        if (_data != null) _buildSummaryCards(),
                        const SizedBox(height: 14),

                        // 4. Status Filter Row & Search
                        _buildFilterAndSearchRow(),
                        const SizedBox(height: 14),

                        // 5. Daily Attendance Logs List
                        if (filteredLogs.isEmpty)
                          _buildEmptyState()
                        else
                          ...filteredLogs.map((log) => _buildDayLogCard(log)),
                        const SizedBox(height: 40),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildMonthSelector() {
    final monthLabel = DateFormat('MMMM yyyy').format(_selectedMonth);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          IconButton(
            icon: const Icon(Icons.chevron_left, color: AppColors.primary),
            tooltip: 'Previous Month',
            onPressed: _previousMonth,
          ),
          InkWell(
            onTap: _selectMonthYear,
            borderRadius: BorderRadius.circular(10),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              child: Row(
                children: [
                  const Icon(Icons.calendar_month_outlined,
                      size: 18, color: AppColors.primary),
                  const SizedBox(width: 8),
                  Text(
                    monthLabel,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(width: 4),
                  const Icon(Icons.arrow_drop_down,
                      size: 18, color: AppColors.textSecondary),
                ],
              ),
            ),
          ),
          IconButton(
            icon: const Icon(Icons.chevron_right, color: AppColors.primary),
            tooltip: 'Next Month',
            onPressed: _nextMonth,
          ),
        ],
      ),
    );
  }

  Widget _buildStaffInfoCard() {
    final staff = _data!.staff!;

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF1E3A8A), Color(0xFF3B82F6)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.blue.shade900.withOpacity(0.2),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.fingerprint, color: Colors.white, size: 24),
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
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  'Biometric ID: ${staff.biometricNo}  •  ${staff.department}',
                  style: TextStyle(
                    fontSize: 11.5,
                    color: Colors.white.withOpacity(0.9),
                  ),
                ),
              ],
            ),
          ),
          if (staff.branch.isNotEmpty && staff.branch != '-')
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.2),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                staff.branch,
                style: const TextStyle(
                  fontSize: 10.5,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSummaryCards() {
    final s = _data!.summary;

    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _buildMiniStatCard(
                'Total Present',
                '${s.totalPresentCount}',
                Icons.how_to_reg_outlined,
                Colors.green.shade700,
                Colors.green.shade50,
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: _buildMiniStatCard(
                'Full Days',
                '${s.presentDays}',
                Icons.check_circle_outline,
                Colors.teal.shade700,
                Colors.teal.shade50,
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: _buildMiniStatCard(
                'Half Days',
                '${s.halfDays}',
                Icons.timelapse_outlined,
                Colors.amber.shade800,
                Colors.amber.shade50,
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: _buildMiniStatCard(
                'Absent',
                '${s.absentDays}',
                Icons.cancel_outlined,
                Colors.red.shade700,
                Colors.red.shade50,
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.indigo.withOpacity(0.06),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.indigo.withOpacity(0.15)),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  const Icon(Icons.access_time_outlined,
                      size: 16, color: Colors.indigo),
                  const SizedBox(width: 8),
                  Text(
                    'Total Hours Worked in ${s.month}:',
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ],
              ),
              Text(
                '${s.totalHours} hrs',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Colors.indigo,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildMiniStatCard(
      String label, String count, IconData icon, Color color, Color bg) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: bg,
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, size: 16, color: color),
          ),
          const SizedBox(height: 4),
          Text(
            count,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(
            label,
            style: const TextStyle(
              fontSize: 9.5,
              fontWeight: FontWeight.w600,
              color: AppColors.textSecondary,
            ),
            textAlign: TextAlign.center,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Widget _buildFilterAndSearchRow() {
    return Column(
      children: [
        // Status Filter Chips
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: ['ALL', 'PRESENT', 'HALF DAY', 'ABSENT'].map((status) {
              final isSelected = _statusFilter == status;
              return Padding(
                padding: const EdgeInsets.only(right: 8),
                child: FilterChip(
                  label: Text(status),
                  selected: isSelected,
                  onSelected: (val) {
                    setState(() => _statusFilter = status);
                  },
                  selectedColor: AppColors.primary,
                  checkmarkColor: Colors.white,
                  labelStyle: TextStyle(
                    fontSize: 11.5,
                    fontWeight: FontWeight.bold,
                    color: isSelected ? Colors.white : AppColors.textSecondary,
                  ),
                  backgroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                    side: BorderSide(
                      color: isSelected
                          ? AppColors.primary
                          : AppColors.borderLight,
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
        ),
        const SizedBox(height: 8),

        // Search Bar
        TextField(
          onChanged: (val) => setState(() => _searchQuery = val),
          decoration: InputDecoration(
            hintText: 'Search by date, day, timing...',
            hintStyle: const TextStyle(fontSize: 12, color: Colors.grey),
            prefixIcon: const Icon(Icons.search, size: 18, color: Colors.grey),
            filled: true,
            fillColor: Colors.white,
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.borderLight),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.borderLight),
            ),
          ),
        ),
      ],
    );
  }

  List<IndividualBiometricDayLog> _getFilteredLogs() {
    if (_data == null) return [];
    var list = _data!.logs;

    if (_statusFilter != 'ALL') {
      list = list.where((log) {
        if (_statusFilter == 'PRESENT') return log.status == 'Present';
        if (_statusFilter == 'HALF DAY') return log.status == 'Half Day';
        if (_statusFilter == 'ABSENT') return log.status == 'Absent';
        return true;
      }).toList();
    }

    if (_searchQuery.trim().isNotEmpty) {
      final q = _searchQuery.toLowerCase().trim();
      list = list.where((log) {
        return log.dateFormatted.toLowerCase().contains(q) ||
            log.dayName.toLowerCase().contains(q) ||
            log.firstIn.toLowerCase().contains(q) ||
            log.lastOut.toLowerCase().contains(q) ||
            log.status.toLowerCase().contains(q);
      }).toList();
    }

    return list;
  }

  Widget _buildDayLogCard(IndividualBiometricDayLog log) {
    final isPresent = log.status == 'Present';
    final isHalfDay = log.status == 'Half Day';

    Color statusColor = isPresent
        ? Colors.green.shade700
        : isHalfDay
            ? Colors.amber.shade800
            : Colors.red.shade700;

    Color statusBg = isPresent
        ? Colors.green.shade50
        : isHalfDay
            ? Colors.amber.shade50
            : Colors.red.shade50;

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: log.isToday
              ? AppColors.primary
              : AppColors.borderLight,
          width: log.isToday ? 1.5 : 1.0,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: ExpansionTile(
        tilePadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        title: Row(
          children: [
            // Date Badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: log.isToday
                    ? AppColors.primary
                    : AppColors.background,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                '${log.dateFormatted} • ${log.dayName}',
                style: TextStyle(
                  fontSize: 12.5,
                  fontWeight: FontWeight.bold,
                  color: log.isToday ? Colors.white : AppColors.textPrimary,
                ),
              ),
            ),
            if (log.isToday) ...[
              const SizedBox(width: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: AppColors.fanta,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: const Text(
                  'TODAY',
                  style: TextStyle(
                    fontSize: 9.5,
                    fontWeight: FontWeight.w900,
                    color: Colors.white,
                  ),
                ),
              ),
            ],
            const Spacer(),
            // Status Badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: statusBg,
                borderRadius: BorderRadius.circular(6),
                border: Border.all(color: statusColor.withOpacity(0.3)),
              ),
              child: Text(
                log.status.toUpperCase(),
                style: TextStyle(
                  fontSize: 10.5,
                  fontWeight: FontWeight.bold,
                  color: statusColor,
                ),
              ),
            ),
          ],
        ),
        subtitle: Padding(
          padding: const EdgeInsets.only(top: 8),
          child: Row(
            children: [
              // In Time
              _buildTimeItem('IN', log.firstIn, Colors.blue),
              const SizedBox(width: 8),
              // Out Time
              _buildTimeItem('OUT', log.lastOut, Colors.purple),
              const SizedBox(width: 8),
              // Session badges
              _buildSessionBadge('S1: ${log.session1}', log.session1 == 'P'),
              const SizedBox(width: 4),
              _buildSessionBadge('S2: ${log.session2}', log.session2 == 'P'),
              const Spacer(),
              if (log.hours > 0)
                Text(
                  '${log.hours}h',
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: Colors.indigo,
                  ),
                ),
            ],
          ),
        ),
        children: [
          Container(
            padding: const EdgeInsets.fromLTRB(14, 0, 14, 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Divider(height: 12),
                const Text(
                  'ALL PUNCH LOGS FOR THIS DAY:',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 0.5,
                    color: AppColors.textSecondary,
                  ),
                ),
                const SizedBox(height: 4),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: AppColors.background,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    log.timeLogs,
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTimeItem(String label, String time, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Text(
        '$label: $time',
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }

  Widget _buildSessionBadge(String text, bool isPresent) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
      decoration: BoxDecoration(
        color: isPresent
            ? Colors.green.withOpacity(0.1)
            : Colors.red.withOpacity(0.1),
        borderRadius: BorderRadius.circular(4),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.bold,
          color: isPresent ? Colors.green.shade700 : Colors.red.shade700,
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 36),
        child: Column(
          children: [
            Icon(Icons.event_busy_outlined,
                size: 48, color: Colors.grey.shade400),
            const SizedBox(height: 10),
            const Text(
              'No Biometric Logs Found',
              style: TextStyle(
                fontSize: 14.5,
                fontWeight: FontWeight.bold,
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: 4),
            const Text(
              'No punch records available for the selected month/filter.',
              style: TextStyle(fontSize: 11.5, color: Colors.grey),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildErrorView() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: Colors.redAccent),
            const SizedBox(height: 12),
            Text(
              _errorMessage!,
              textAlign: TextAlign.center,
              style:
                  const TextStyle(fontSize: 14, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: _fetchBiometricReport,
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
    );
  }
}
