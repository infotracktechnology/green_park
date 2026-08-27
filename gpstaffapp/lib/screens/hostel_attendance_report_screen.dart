import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../api/api_client.dart';
import '../models/hostel_attendance_report_model.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class HostelAttendanceReportScreen extends StatefulWidget {
  const HostelAttendanceReportScreen({super.key});

  @override
  State<HostelAttendanceReportScreen> createState() =>
      _HostelAttendanceReportScreenState();
}

class _HostelAttendanceReportScreenState
    extends State<HostelAttendanceReportScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  DateTime _fromDate = DateTime.now();
  DateTime _toDate = DateTime.now();
  String? _selectedBranchId;
  String? _selectedHostelId;
  String? _selectedSection;
  String? _selectedRoomNo;

  List<BranchItem> _branches = [];
  List<HostelItemModel> _hostels = [];
  List<String> _sections = [];
  List<String> _rooms = [];
  List<HostelAttendanceItemModel> _records = [];

  bool _loading = false;
  bool _loadingDependencies = false;
  String? _errorMessage;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _tabController.addListener(() {
      if (_tabController.indexIsChanging) {
        setState(() {
          _records = [];
          _searchQuery = '';
        });
      }
    });

    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initData();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _initData() async {
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
        await _fetchHostelsForBranch(_selectedBranchId!);
      }
    }
  }

  Future<void> _fetchHostelsForBranch(String branchId) async {
    setState(() {
      _loadingDependencies = true;
      _hostels = [];
      _selectedHostelId = null;
      _sections = [];
      _selectedSection = null;
      _rooms = [];
      _selectedRoomNo = null;
      _records = [];
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get(
        '/admin/hostel_attendance',
        queryParameters: {
          'branch_id': branchId,
          'room_branch_id': branchId,
          'active_tab': _tabController.index == 0 ? 'section_tab' : 'room_tab',
        },
      );

      if (response.data != null && response.data['status'] == true) {
        final model =
            HostelAttendanceReportResponseModel.fromJson(response.data);
        setState(() {
          _hostels = model.hostels;
          if (_hostels.isNotEmpty) {
            _selectedHostelId = _hostels.first.id.toString();
          }
        });

        if (_selectedHostelId != null) {
          await _fetchSubOptions(_selectedHostelId!);
        }
      }
    } catch (e) {
      debugPrint('Error fetching hostels: $e');
    } finally {
      if (mounted) setState(() => _loadingDependencies = false);
    }
  }

  Future<void> _fetchSubOptions(String hostelId) async {
    setState(() {
      _loadingDependencies = true;
      _sections = [];
      _selectedSection = null;
      _rooms = [];
      _selectedRoomNo = null;
    });

    try {
      final dio = ApiClient().dio;
      final isSectionTab = _tabController.index == 0;

      final response = await dio.get(
        '/admin/hostel_attendance',
        queryParameters: {
          'branch_id': _selectedBranchId,
          'room_branch_id': _selectedBranchId,
          'hostel_id': hostelId,
          'room_hostel_id': hostelId,
          'active_tab': isSectionTab ? 'section_tab' : 'room_tab',
        },
      );

      if (response.data != null && response.data['status'] == true) {
        final model =
            HostelAttendanceReportResponseModel.fromJson(response.data);
        setState(() {
          _sections = model.sections;
          if (_sections.isNotEmpty) {
            _selectedSection = _sections.first;
          }
          _rooms = model.rooms;
          if (_rooms.isNotEmpty) {
            _selectedRoomNo = _rooms.first;
          }
        });

        if ((isSectionTab && _selectedSection != null) ||
            (!isSectionTab && _selectedRoomNo != null)) {
          await _fetchReport();
        }
      }
    } catch (e) {
      debugPrint('Error fetching sub options: $e');
    } finally {
      if (mounted) setState(() => _loadingDependencies = false);
    }
  }

  Future<void> _fetchReport() async {
    final isSectionTab = _tabController.index == 0;

    if (_selectedBranchId == null || _selectedHostelId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select branch and hostel')),
      );
      return;
    }

    if (isSectionTab && (_selectedSection == null || _selectedSection!.isEmpty)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a section')),
      );
      return;
    }

    if (!isSectionTab && (_selectedRoomNo == null || _selectedRoomNo!.isEmpty)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a room number')),
      );
      return;
    }

    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final fromStr = DateFormat('yyyy-MM-dd').format(_fromDate);
      final toStr = DateFormat('yyyy-MM-dd').format(_toDate);

      final queryParams = <String, dynamic>{
        'from_date': fromStr,
        'to_date': toStr,
        'active_tab': isSectionTab ? 'section_tab' : 'room_tab',
      };

      if (isSectionTab) {
        queryParams['branch_id'] = _selectedBranchId;
        queryParams['hostel_id'] = _selectedHostelId;
        queryParams['section'] = _selectedSection;
      } else {
        queryParams['room_branch_id'] = _selectedBranchId;
        queryParams['room_hostel_id'] = _selectedHostelId;
        queryParams['room_no'] = _selectedRoomNo;
      }

      final response = await dio.get(
        '/admin/hostel_attendance',
        queryParameters: queryParams,
      );

      if (response.data != null && response.data['status'] == true) {
        final model =
            HostelAttendanceReportResponseModel.fromJson(response.data);
        setState(() {
          _records = model.records;
        });
      } else {
        setState(() {
          _records = [];
          _errorMessage =
              response.data?['message'] ?? 'Failed to load attendance logs';
        });
      }
    } catch (e) {
      debugPrint('Error fetching hostel attendance: $e');
      setState(() {
        _errorMessage = 'Failed to load hostel attendance report.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _pickDateRange() async {
    final picked = await showDateRangePicker(
      context: context,
      initialDateRange: DateTimeRange(start: _fromDate, end: _toDate),
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

    if (picked != null) {
      setState(() {
        _fromDate = picked.start;
        _toDate = picked.end;
      });
      _fetchReport();
    }
  }

  List<HostelAttendanceItemModel> get _filteredRecords {
    if (_searchQuery.isEmpty) return _records;
    final query = _searchQuery.toLowerCase().trim();
    return _records.where((r) {
      return r.studentName.toLowerCase().contains(query) ||
          r.studentId.toLowerCase().contains(query) ||
          r.section.toLowerCase().contains(query) ||
          r.roomNo.toLowerCase().contains(query);
    }).toList();
  }

  int get _morningPresent =>
      _records.where((r) => r.morning.toUpperCase() == 'P').length;
  int get _morningAbsent =>
      _records.where((r) => r.morning.toUpperCase() == 'A').length;
  int get _eveningPresent =>
      _records.where((r) => r.evening.toUpperCase() == 'P').length;
  int get _eveningAbsent =>
      _records.where((r) => r.evening.toUpperCase() == 'A').length;

  @override
  Widget build(BuildContext context) {
    final filtered = _filteredRecords;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Hostel Attendance Report'),
        backgroundColor: AppColors.primary,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          indicatorWeight: 3,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold),
          tabs: const [
            Tab(text: 'Section Wise', icon: Icon(Icons.class_outlined, size: 18)),
            Tab(text: 'Room Wise', icon: Icon(Icons.meeting_room_outlined, size: 18)),
          ],
        ),
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
              if (_records.isNotEmpty) ...[
                _buildStatsOverviewCard(),
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
    final isSectionTab = _tabController.index == 0;

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
            // Branch & Hostel Row
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
                            hint: const Text('Branch',
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
                                _fetchHostelsForBranch(val);
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
                        'HOSTEL',
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
                            value: _selectedHostelId,
                            hint: const Text('Hostel',
                                style: TextStyle(fontSize: 13)),
                            items: _hostels.map((h) {
                              return DropdownMenuItem<String>(
                                value: h.id.toString(),
                                child: Text(
                                  h.name,
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
                                setState(() => _selectedHostelId = val);
                                _fetchSubOptions(val);
                              }
                            },
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Section or Room Dropdown & Date Range Picker
            Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        isSectionTab ? 'SECTION' : 'ROOM NO',
                        style: const TextStyle(
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
                          child: isSectionTab
                              ? DropdownButton<String>(
                                  isExpanded: true,
                                  value: _selectedSection,
                                  hint: const Text('Section',
                                      style: TextStyle(fontSize: 13)),
                                  items: _sections.map((s) {
                                    return DropdownMenuItem<String>(
                                      value: s,
                                      child: Text(
                                        s,
                                        style: const TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600,
                                          color: AppColors.textPrimary,
                                        ),
                                      ),
                                    );
                                  }).toList(),
                                  onChanged: (val) {
                                    if (val != null) {
                                      setState(() => _selectedSection = val);
                                      _fetchReport();
                                    }
                                  },
                                )
                              : DropdownButton<String>(
                                  isExpanded: true,
                                  value: _selectedRoomNo,
                                  hint: const Text('Room No',
                                      style: TextStyle(fontSize: 13)),
                                  items: _rooms.map((r) {
                                    return DropdownMenuItem<String>(
                                      value: r,
                                      child: Text(
                                        'Room $r',
                                        style: const TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600,
                                          color: AppColors.textPrimary,
                                        ),
                                      ),
                                    );
                                  }).toList(),
                                  onChanged: (val) {
                                    if (val != null) {
                                      setState(() => _selectedRoomNo = val);
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
                        'DATE RANGE',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary,
                          letterSpacing: 0.5,
                        ),
                      ),
                      const SizedBox(height: 6),
                      InkWell(
                        onTap: _pickDateRange,
                        borderRadius: BorderRadius.circular(14),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 10, vertical: 12),
                          decoration: BoxDecoration(
                            color: AppColors.background,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Flexible(
                                child: Text(
                                  '${DateFormat('dd/MM').format(_fromDate)} - ${DateFormat('dd/MM').format(_toDate)}',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    color: AppColors.textPrimary,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                              const Icon(Icons.date_range_outlined,
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
            const SizedBox(height: 14),

            // Submit Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _loading || _loadingDependencies ? null : _fetchReport,
                icon: _loading
                    ? const SizedBox(
                        width: 16,
                        height: 16,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.search, size: 18),
                label: const Text('View Hostel Report'),
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

  Widget _buildStatsOverviewCard() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Container(
        padding: const EdgeInsets.all(14),
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
        child: Row(
          children: [
            Expanded(
              child: _buildMiniStat(
                label: 'Total Logs',
                value: '${_records.length}',
                color: AppColors.primary,
                icon: Icons.list_alt,
              ),
            ),
            Container(width: 1, height: 38, color: AppColors.borderLight),
            Expanded(
              child: _buildMiniStat(
                label: 'Morning (P/A)',
                value: '$_morningPresent / $_morningAbsent',
                color: Colors.teal,
                icon: Icons.wb_sunny_outlined,
              ),
            ),
            Container(width: 1, height: 38, color: AppColors.borderLight),
            Expanded(
              child: _buildMiniStat(
                label: 'Evening (P/A)',
                value: '$_eveningPresent / $_eveningAbsent',
                color: AppColors.fanta,
                icon: Icons.nightlight_outlined,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMiniStat({
    required String label,
    required String value,
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
                fontSize: 14,
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
            fontSize: 10,
            fontWeight: FontWeight.w600,
            color: AppColors.textSecondary,
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
          hintText: 'Search student name, ID or room...',
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

  Widget _buildContent(List<HostelAttendanceItemModel> filtered) {
    if (_loading) {
      return const Padding(
        padding: EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              CircularProgressIndicator(color: AppColors.primary),
              SizedBox(height: 16),
              Text(
                'Fetching hostel attendance records...',
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

    if (_records.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(40),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.hotel_outlined, size: 56, color: AppColors.textMuted),
              SizedBox(height: 14),
              Text(
                'No hostel attendance records',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              SizedBox(height: 6),
              Text(
                'Select branch, hostel, criteria and date range.',
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
          child: Text('No matching students found',
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
        final r = filtered[index];
        final isMorningP = r.morning.toUpperCase() == 'P';
        final isEveningP = r.evening.toUpperCase() == 'P';

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
                // Top Row: Student Name & Room/Date Chip
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    CircleAvatar(
                      radius: 18,
                      backgroundColor: AppColors.primary.withOpacity(0.12),
                      child: Text(
                        '${index + 1}',
                        style: const TextStyle(
                          fontSize: 11,
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
                            r.studentName,
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: AppColors.textPrimary,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'ID: ${r.studentId} • Sec: ${r.section} • Room: ${r.roomNo}',
                            style: const TextStyle(
                              fontSize: 11,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: AppColors.background,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: Text(
                        r.date,
                        style: const TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ),
                  ],
                ),

                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 10),
                  child: Divider(height: 1, color: AppColors.borderLight),
                ),

                // Morning & Evening Status Rows
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: isMorningP
                              ? AppColors.success.withOpacity(0.08)
                              : AppColors.error.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(
                            color: isMorningP
                                ? AppColors.success.withOpacity(0.25)
                                : AppColors.error.withOpacity(0.25),
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Row(
                              children: [
                                Icon(Icons.wb_sunny_outlined,
                                    size: 14, color: AppColors.textSecondary),
                                SizedBox(width: 4),
                                Text(
                                  'Morning',
                                  style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.w600,
                                    color: AppColors.textPrimary,
                                  ),
                                ),
                              ],
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 7, vertical: 2),
                              decoration: BoxDecoration(
                                color: isMorningP
                                    ? AppColors.success
                                    : AppColors.error,
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                r.morning,
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
                    const SizedBox(width: 10),
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: isEveningP
                              ? AppColors.success.withOpacity(0.08)
                              : AppColors.error.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(
                            color: isEveningP
                                ? AppColors.success.withOpacity(0.25)
                                : AppColors.error.withOpacity(0.25),
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Row(
                              children: [
                                Icon(Icons.nightlight_outlined,
                                    size: 14, color: AppColors.textSecondary),
                                SizedBox(width: 4),
                                Text(
                                  'Evening',
                                  style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.w600,
                                    color: AppColors.textPrimary,
                                  ),
                                ),
                              ],
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 7, vertical: 2),
                              decoration: BoxDecoration(
                                color: isEveningP
                                    ? AppColors.success
                                    : AppColors.error,
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                r.evening,
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
              ],
            ),
          ),
        );
      },
    );
  }
}
