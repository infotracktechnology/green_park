import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../api/api_client.dart';
import '../models/staff_leave_model.dart';
import '../theme/app_theme.dart';

class StaffLeaveScreen extends StatefulWidget {
  final bool isApprovalMode;

  const StaffLeaveScreen({
    super.key,
    this.isApprovalMode = false,
  });

  @override
  State<StaffLeaveScreen> createState() => _StaffLeaveScreenState();
}

class _StaffLeaveScreenState extends State<StaffLeaveScreen> {
  StaffLeaveResponseModel? _data;
  bool _loading = true;
  String? _errorMessage;
  String _selectedStatus = 'All';
  String _selectedType = 'All';
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _fetchLeaves();
  }

  Future<void> _fetchLeaves() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    try {
      final dio = ApiClient().dio;
      final response = await dio.get('/admin/staff_leave', queryParameters: {
        if (_selectedStatus != 'All') 'status': _selectedStatus,
        if (_selectedType != 'All') 'leave_type': _selectedType,
        if (widget.isApprovalMode) 'view_all': 1,
      });

      if (response.data != null && response.data['status'] == true) {
        setState(() {
          _data = StaffLeaveResponseModel.fromJson(response.data);
        });
      } else {
        setState(() {
          _errorMessage =
              response.data?['message'] ?? 'Failed to load leave records.';
        });
      }
    } catch (e) {
      debugPrint('Error fetching staff leaves: $e');
      setState(() {
        _errorMessage = 'Failed to load leave data. Please check connection.';
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  // ADD or EDIT Leave Application
  Future<void> _saveLeaveApplication({
    int? leaveId, // if provided, it's EDIT mode
    required String leaveType,
    required DateTime fromDate,
    required DateTime toDate,
    required String session,
    required String reason,
  }) async {
    try {
      final dio = ApiClient().dio;
      final isEdit = leaveId != null;
      final url = isEdit
          ? '/admin/staff_leave/update/$leaveId'
          : '/admin/staff_leave/apply';

      final response = await dio.post(url, data: {
        'leave_type': leaveType,
        'from_date': DateFormat('yyyy-MM-dd').format(fromDate),
        'to_date': DateFormat('yyyy-MM-dd').format(toDate),
        'session': session,
        'reason': reason,
      });

      if (response.data != null && response.data['status'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(response.data['message'] ??
                  (isEdit ? 'Leave updated.' : 'Leave submitted.')),
              backgroundColor: Colors.green,
            ),
          );
          _fetchLeaves();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(response.data?['message'] ?? 'Action failed.'),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    } catch (e) {
      debugPrint('Error saving leave: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Failed to save leave. Please check inputs.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  // APPROVE / REJECT Leave (Admin only)
  Future<void> _updateLeaveStatus(int id, String status,
      {String? rejectionReason}) async {
    try {
      final dio = ApiClient().dio;
      final response = await dio.post('/admin/staff_leave/approval', data: {
        'id': id,
        'status': status,
        if (rejectionReason != null && rejectionReason.isNotEmpty)
          'rejection_reason': rejectionReason,
      });

      if (response.data != null && response.data['status'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(response.data['message'] ?? 'Status updated.'),
              backgroundColor:
                  status == 'Approved' ? Colors.green : Colors.redAccent,
            ),
          );
          _fetchLeaves();
        }
      }
    } catch (e) {
      debugPrint('Error updating leave status: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Failed to update leave status.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  // DELETE / CANCEL Leave (Staff only)
  Future<void> _deleteLeave(int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Leave Request?'),
        content: const Text(
            'Are you sure you want to delete this pending leave application?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('No'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red, foregroundColor: Colors.white),
            child: const Text('Delete'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        final dio = ApiClient().dio;
        final response = await dio.delete('/admin/staff_leave/$id');
        if (response.data != null && response.data['status'] == true) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('Leave application deleted.'),
                backgroundColor: Colors.orange,
              ),
            );
            _fetchLeaves();
          }
        }
      } catch (e) {
        debugPrint('Error deleting leave: $e');
      }
    }
  }

  // Modal Sheet for Add or Edit Leave
  void _showLeaveFormModal({StaffLeaveModel? existingLeave}) {
    final isEdit = existingLeave != null;
    final leaveTypes = _data?.leaveTypes ??
        [
          'Casual Leave (CL)',
          'Sick Leave (SL)',
          'Permission',
          'On Duty (OD)',
          'Maternity Leave',
          'Special Leave',
          'Other'
        ];
    final sessions =
        _data?.sessions ?? ['Full Day', 'Forenoon (FN)', 'Afternoon (AN)'];

    String selectedType = isEdit && leaveTypes.contains(existingLeave.leaveType)
        ? existingLeave.leaveType
        : leaveTypes.first;

    String selectedSession =
        isEdit && sessions.contains(existingLeave.session)
            ? existingLeave.session
            : sessions.first;

    DateTime fromDate = isEdit && existingLeave.fromDate != null
        ? DateTime.tryParse(existingLeave.fromDate!) ?? DateTime.now()
        : DateTime.now();

    DateTime toDate = isEdit && existingLeave.toDate != null
        ? DateTime.tryParse(existingLeave.toDate!) ?? DateTime.now()
        : DateTime.now();

    final reasonController =
        TextEditingController(text: isEdit ? existingLeave.reason : '');

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setModalState) {
          double calcDays() {
            if (fromDate.year == toDate.year &&
                fromDate.month == toDate.month &&
                fromDate.day == toDate.day &&
                selectedSession != 'Full Day') {
              return 0.5;
            }
            return (toDate.difference(fromDate).inDays + 1).toDouble();
          }

          final days = calcDays();

          return Container(
            padding: EdgeInsets.only(
              bottom: MediaQuery.of(context).viewInsets.bottom + 20,
              top: 20,
              left: 20,
              right: 20,
            ),
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
            ),
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Icon(
                        isEdit ? Icons.edit_note_outlined : Icons.event_note_outlined,
                        color: AppColors.primary,
                        size: 24,
                      ),
                      const SizedBox(width: 8),
                      Text(
                        isEdit ? 'Edit Leave Request' : 'Apply for Leave',
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 24),

                  // Leave Type Dropdown
                  const Text(
                    'Leave Type',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    decoration: BoxDecoration(
                      color: AppColors.background,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: AppColors.borderLight),
                    ),
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<String>(
                        value: selectedType,
                        isExpanded: true,
                        items: leaveTypes
                            .map((t) =>
                                DropdownMenuItem(value: t, child: Text(t)))
                            .toList(),
                        onChanged: (val) {
                          if (val != null) {
                            setModalState(() => selectedType = val);
                          }
                        },
                      ),
                    ),
                  ),
                  const SizedBox(height: 14),

                  // Dates Picker Row
                  Row(
                    children: [
                      // From Date
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'From Date',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 6),
                            InkWell(
                              onTap: () async {
                                final picked = await showDatePicker(
                                  context: context,
                                  initialDate: fromDate,
                                  firstDate: DateTime.now()
                                      .subtract(const Duration(days: 30)),
                                  lastDate: DateTime.now()
                                      .add(const Duration(days: 90)),
                                );
                                if (picked != null) {
                                  setModalState(() {
                                    fromDate = picked;
                                    if (toDate.isBefore(fromDate)) {
                                      toDate = fromDate;
                                    }
                                  });
                                }
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 12),
                                decoration: BoxDecoration(
                                  color: AppColors.background,
                                  borderRadius: BorderRadius.circular(12),
                                  border:
                                      Border.all(color: AppColors.borderLight),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.calendar_today_outlined,
                                        size: 16, color: AppColors.primary),
                                    const SizedBox(width: 8),
                                    Text(
                                      DateFormat('dd MMM yyyy')
                                          .format(fromDate),
                                      style: const TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 10),

                      // To Date
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'To Date',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 6),
                            InkWell(
                              onTap: () async {
                                final picked = await showDatePicker(
                                  context: context,
                                  initialDate: toDate.isBefore(fromDate)
                                      ? fromDate
                                      : toDate,
                                  firstDate: fromDate,
                                  lastDate: DateTime.now()
                                      .add(const Duration(days: 90)),
                                );
                                if (picked != null) {
                                  setModalState(() => toDate = picked);
                                }
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 12),
                                decoration: BoxDecoration(
                                  color: AppColors.background,
                                  borderRadius: BorderRadius.circular(12),
                                  border:
                                      Border.all(color: AppColors.borderLight),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.calendar_today_outlined,
                                        size: 16, color: AppColors.primary),
                                    const SizedBox(width: 8),
                                    Text(
                                      DateFormat('dd MMM yyyy')
                                          .format(toDate),
                                      style: const TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600),
                                    ),
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

                  // Session and Calculated Days
                  Row(
                    children: [
                      // Session Selector
                      Expanded(
                        flex: 3,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Session',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Container(
                              padding:
                                  const EdgeInsets.symmetric(horizontal: 12),
                              decoration: BoxDecoration(
                                color: AppColors.background,
                                borderRadius: BorderRadius.circular(12),
                                border:
                                    Border.all(color: AppColors.borderLight),
                              ),
                              child: DropdownButtonHideUnderline(
                                child: DropdownButton<String>(
                                  value: selectedSession,
                                  isExpanded: true,
                                  items: sessions
                                      .map((s) => DropdownMenuItem(
                                          value: s, child: Text(s)))
                                      .toList(),
                                  onChanged: (val) {
                                    if (val != null) {
                                      setModalState(
                                          () => selectedSession = val);
                                    }
                                  },
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 10),

                      // Days Pill
                      Expanded(
                        flex: 2,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Total Days',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 12),
                              decoration: BoxDecoration(
                                color: AppColors.primary.withOpacity(0.08),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                    color: AppColors.primary.withOpacity(0.3)),
                              ),
                              child: Center(
                                child: Text(
                                  '$days Day${days > 1 ? 's' : ''}',
                                  style: const TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                    color: AppColors.primary,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),

                  // Reason TextField
                  const Text(
                    'Reason for Leave',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextField(
                    controller: reasonController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText: 'Please state the reason clearly...',
                      hintStyle:
                          const TextStyle(fontSize: 13, color: Colors.grey),
                      filled: true,
                      fillColor: AppColors.background,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide:
                            const BorderSide(color: AppColors.borderLight),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide:
                            const BorderSide(color: AppColors.borderLight),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide:
                            const BorderSide(color: AppColors.primary, width: 1.5),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Submit Button
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () {
                        if (reasonController.text.trim().isEmpty) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('Please enter a reason for leave.'),
                              backgroundColor: Colors.redAccent,
                            ),
                          );
                          return;
                        }
                        Navigator.pop(ctx);
                        _saveLeaveApplication(
                          leaveId: existingLeave?.id,
                          leaveType: selectedType,
                          fromDate: fromDate,
                          toDate: toDate,
                          session: selectedSession,
                          reason: reasonController.text.trim(),
                        );
                      },
                      icon: Icon(isEdit ? Icons.save_outlined : Icons.send_rounded,
                          size: 18),
                      label: Text(
                        isEdit ? 'Update Leave Request' : 'Submit Leave Application',
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
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
        },
      ),
    );
  }

  // Admin Approval / Rejection Dialog
  void _showApprovalDialog(StaffLeaveModel leave) {
    final reasonController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('Review Leave - ${leave.staffName}'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              '${leave.leaveType} • ${leave.days} Day(s)',
              style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.bold,
                  color: AppColors.primary),
            ),
            const SizedBox(height: 4),
            Text(
              'Dates: ${leave.fromDateFormatted} to ${leave.toDateFormatted} (${leave.session})',
              style: const TextStyle(
                  fontSize: 12, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 8),
            Text(
              'Reason: "${leave.reason}"',
              style: const TextStyle(
                  fontSize: 13,
                  fontStyle: FontStyle.italic,
                  color: AppColors.textPrimary),
            ),
            const Divider(height: 20),
            TextField(
              controller: reasonController,
              decoration: const InputDecoration(
                labelText: 'Rejection Reason (if rejecting)',
                hintText: 'Optional remark...',
                border: OutlineInputBorder(),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Close'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              _updateLeaveStatus(leave.id, 'Rejected',
                  rejectionReason: reasonController.text.trim());
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              foregroundColor: Colors.white,
            ),
            child: const Text('Reject'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              _updateLeaveStatus(leave.id, 'Approved');
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.green,
              foregroundColor: Colors.white,
            ),
            child: const Text('Approve'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filteredLeaves = _getFilteredLeaves();
    final pageTitle =
        widget.isApprovalMode ? 'Leave Approval' : 'Leave Request';

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text(
          pageTitle,
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh',
            onPressed: _fetchLeaves,
          ),
        ],
      ),
      // Regular staff gets Floating Action Button to apply for leave
      floatingActionButton: !widget.isApprovalMode
          ? FloatingActionButton.extended(
              onPressed: () => _showLeaveFormModal(),
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
              icon: const Icon(Icons.add),
              label: const Text(
                'Apply Leave',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            )
          : null,
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            )
          : _errorMessage != null
              ? _buildErrorView()
              : RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: _fetchLeaves,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // 1. Summary Cards
                        if (_data != null) _buildSummaryCards(),
                        const SizedBox(height: 14),

                        // 2. Filter Tabs & Search
                        _buildFilterBar(),
                        const SizedBox(height: 14),

                        // 3. Leave Cards List
                        if (filteredLeaves.isEmpty)
                          _buildEmptyState()
                        else
                          ...filteredLeaves
                              .map((leave) => _buildLeaveCard(leave)),
                        const SizedBox(height: 80),
                      ],
                    ),
                  ),
                ),
    );
  }

  List<StaffLeaveModel> _getFilteredLeaves() {
    if (_data == null) return [];
    var list = _data!.leaves;

    if (_selectedStatus != 'All') {
      list = list.where((l) => l.status == _selectedStatus).toList();
    }

    if (_selectedType != 'All') {
      list = list.where((l) => l.leaveType == _selectedType).toList();
    }

    if (_searchQuery.trim().isNotEmpty) {
      final q = _searchQuery.toLowerCase().trim();
      list = list.where((l) {
        return l.staffName.toLowerCase().contains(q) ||
            l.leaveType.toLowerCase().contains(q) ||
            l.reason.toLowerCase().contains(q) ||
            l.department.toLowerCase().contains(q);
      }).toList();
    }

    return list;
  }

  Widget _buildSummaryCards() {
    final s = _data!.summary;

    return Row(
      children: [
        Expanded(
          child: _buildMiniStatCard(
            'Pending',
            '${s.pending}',
            Icons.pending_actions_outlined,
            Colors.amber.shade800,
            Colors.amber.shade50,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _buildMiniStatCard(
            'Approved',
            '${s.approved}',
            Icons.check_circle_outline,
            Colors.green.shade700,
            Colors.green.shade50,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _buildMiniStatCard(
            'Rejected',
            '${s.rejected}',
            Icons.cancel_outlined,
            Colors.red.shade700,
            Colors.red.shade50,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: _buildMiniStatCard(
            'Approved Days',
            '${s.totalApprovedDays}',
            Icons.calendar_today_outlined,
            Colors.teal.shade700,
            Colors.teal.shade50,
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

  Widget _buildFilterBar() {
    return Column(
      children: [
        // Status & Type Filters Row
        Row(
          children: [
            Expanded(
              child: SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: ['All', 'Pending', 'Approved', 'Rejected'].map((status) {
                    final isSelected = _selectedStatus == status;
                    return Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: FilterChip(
                        label: Text(status),
                        selected: isSelected,
                        onSelected: (val) {
                          setState(() => _selectedStatus = status);
                        },
                        selectedColor: AppColors.primary,
                        checkmarkColor: Colors.white,
                        labelStyle: TextStyle(
                          fontSize: 12,
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
            ),
            if (_data?.leaveTypes.isNotEmpty == true) ...[
              const SizedBox(width: 6),
              PopupMenuButton<String>(
                icon: Icon(
                  Icons.filter_list,
                  color: _selectedType != 'All'
                      ? AppColors.primary
                      : AppColors.textSecondary,
                  size: 20,
                ),
                tooltip: 'Filter by Leave Type',
                onSelected: (val) {
                  setState(() => _selectedType = val);
                },
                itemBuilder: (ctx) => [
                  const PopupMenuItem(
                    value: 'All',
                    child: Text('All Types'),
                  ),
                  ...?_data?.leaveTypes.map((t) => PopupMenuItem(
                        value: t,
                        child: Text(t),
                      )),
                ],
              ),
            ],
          ],
        ),
        const SizedBox(height: 8),

        // Search Bar
        TextField(
          onChanged: (val) => setState(() => _searchQuery = val),
          decoration: InputDecoration(
            hintText: 'Search by staff, reason, type...',
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

  Widget _buildLeaveCard(StaffLeaveModel leave) {
    final isPending = leave.status == 'Pending';
    final isApproved = leave.status == 'Approved';
    final isRejected = leave.status == 'Rejected';

    Color statusColor = isPending
        ? Colors.amber.shade800
        : isApproved
            ? Colors.green.shade700
            : Colors.red.shade700;

    Color statusBg = isPending
        ? Colors.amber.shade50
        : isApproved
            ? Colors.green.shade50
            : Colors.red.shade50;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isPending
              ? Colors.amber.shade300
              : AppColors.borderLight,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header: Staff Name (for admin) & Status Badge
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (widget.isApprovalMode) ...[
                      Text(
                        leave.staffName,
                        style: const TextStyle(
                          fontSize: 14.5,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      if (leave.department != '-' || leave.branchName != '-')
                        Text(
                          '${leave.department} • ${leave.branchName}',
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppColors.textSecondary,
                          ),
                        ),
                    ] else ...[
                      Text(
                        leave.leaveType,
                        style: const TextStyle(
                          fontSize: 14.5,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary,
                        ),
                      ),
                      Text(
                        'Submitted on ${leave.createdAt}',
                        style: const TextStyle(
                          fontSize: 10.5,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusBg,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: statusColor.withOpacity(0.3)),
                ),
                child: Text(
                  leave.status.toUpperCase(),
                  style: TextStyle(
                    fontSize: 10.5,
                    fontWeight: FontWeight.bold,
                    color: statusColor,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Leave Type & Duration
          Row(
            children: [
              if (widget.isApprovalMode) ...[
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    leave.leaveType,
                    style: const TextStyle(
                      fontSize: 11.5,
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
              ],
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: AppColors.fanta.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  '${leave.days} Day${leave.days > 1 ? 's' : ''} (${leave.session})',
                  style: const TextStyle(
                    fontSize: 11.5,
                    fontWeight: FontWeight.bold,
                    color: AppColors.fanta,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),

          // Date Range
          Row(
            children: [
              const Icon(Icons.date_range_outlined,
                  size: 14, color: AppColors.textSecondary),
              const SizedBox(width: 6),
              Text(
                '${leave.fromDateFormatted}  →  ${leave.toDateFormatted}',
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),

          // Reason Box
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              'Reason: ${leave.reason}',
              style: const TextStyle(
                fontSize: 12.5,
                color: AppColors.textPrimary,
                height: 1.3,
              ),
            ),
          ),

          // Approver details or rejection remark
          if (isApproved && leave.approvedBy != '-') ...[
            const SizedBox(height: 6),
            Text(
              'Approved by: ${leave.approvedBy}${leave.approvedAt != null ? ' on ${leave.approvedAt}' : ''}',
              style: TextStyle(
                fontSize: 11,
                color: Colors.green.shade800,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],

          if (isRejected &&
              leave.rejectionReason != null &&
              leave.rejectionReason!.isNotEmpty) ...[
            const SizedBox(height: 6),
            Text(
              'Rejection Reason: "${leave.rejectionReason}"',
              style: TextStyle(
                fontSize: 11,
                color: Colors.red.shade800,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],

          // ACTION BUTTONS
          if (isPending) ...[
            const Divider(height: 18),
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                // ADMIN MODE: Approve / Reject
                if (widget.isApprovalMode) ...[
                  OutlinedButton.icon(
                    onPressed: () => _showApprovalDialog(leave),
                    icon: const Icon(Icons.rate_review_outlined, size: 14),
                    label: const Text('Review / Approve'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppColors.primary,
                      side: const BorderSide(color: AppColors.primary),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 8),
                    ),
                  ),
                ] else ...[
                  // REGULAR STAFF MODE: Edit / Delete
                  OutlinedButton.icon(
                    onPressed: () => _showLeaveFormModal(existingLeave: leave),
                    icon: const Icon(Icons.edit_outlined, size: 14),
                    label: const Text('Edit'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppColors.primary,
                      side: const BorderSide(color: AppColors.primary),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 8),
                    ),
                  ),
                  const SizedBox(width: 8),
                  TextButton.icon(
                    onPressed: () => _deleteLeave(leave.id),
                    icon: const Icon(Icons.delete_outline,
                        size: 16, color: Colors.redAccent),
                    label: const Text(
                      'Delete',
                      style: TextStyle(
                          color: Colors.redAccent, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ],
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 40),
        child: Column(
          children: [
            Icon(Icons.event_busy_outlined,
                size: 54, color: Colors.grey.shade400),
            const SizedBox(height: 12),
            Text(
              widget.isApprovalMode
                  ? 'No Leave Approvals Pending'
                  : 'No Leave Requests Found',
              style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              widget.isApprovalMode
                  ? 'All staff leave applications have been reviewed.'
                  : 'Tap "Apply Leave" below to submit a new leave request.',
              style: const TextStyle(fontSize: 12, color: Colors.grey),
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
              onPressed: _fetchLeaves,
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
