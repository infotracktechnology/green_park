import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class BranchSwitchScreen extends StatefulWidget {
  const BranchSwitchScreen({super.key});

  @override
  State<BranchSwitchScreen> createState() => _BranchSwitchScreenState();
}

class _BranchSwitchScreenState extends State<BranchSwitchScreen> {
  List<BranchItem> _branches = [];
  dynamic _activeBranchId;
  bool _loading = false;
  dynamic _switchingBranchId;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchAssignedBranches();
  }

  Future<void> _fetchAssignedBranches() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    final auth = Provider.of<AuthProvider>(context, listen: false);
    final filterProvider =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    final currentBranch = auth.user?.branch;

    try {
      final dio = ApiClient().dio;
      final response = await dio.get('/admin/branchswitch');

      if (response.data != null && response.data['status'] == true) {
        final list = response.data['branches_list'];
        final active = response.data['active_branch'] ?? currentBranch;

        if (list is List && mounted) {
          setState(() {
            _branches = list.map((e) => BranchItem.fromDynamic(e)).toList();
            _activeBranchId = active;
          });
        }
      } else if (mounted) {
        // Fallback to master data branches if any
        if (filterProvider.master?.branches.isNotEmpty == true) {
          setState(() {
            _branches = filterProvider.master!.branches;
            _activeBranchId = currentBranch;
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching assigned branches: $e');
      if (mounted) {
        // Fallback
        if (filterProvider.master?.branches.isNotEmpty == true) {
          setState(() {
            _branches = filterProvider.master!.branches;
            _activeBranchId = auth.user?.branch;
          });
        } else {
          setState(() {
            _errorMessage = 'Failed to load assigned branches.';
          });
        }
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _performSwitch(BranchItem branch) async {
    setState(() => _switchingBranchId = branch.id);
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final filterProvider =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);

    final res = await auth.switchBranch(branch.id);

    if (mounted) {
      setState(() => _switchingBranchId = null);
      if (res['success'] == true) {
        setState(() => _activeBranchId = branch.id);
        // Refresh master data to reflect newly selected active branch
        await filterProvider.fetchMasterData();

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Branch switched to ${branch.name}'),
              backgroundColor: AppColors.success,
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(res['message'] ?? 'Failed to switch branch'),
            backgroundColor: AppColors.error,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Switch Branch'),
        backgroundColor: AppColors.primary,
        elevation: 0,
      ),
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            )
          : _errorMessage != null && _branches.isEmpty
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.error_outline,
                            size: 48, color: AppColors.error),
                        const SizedBox(height: 12),
                        Text(
                          _errorMessage!,
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 16),
                        ElevatedButton.icon(
                          onPressed: _fetchAssignedBranches,
                          icon: const Icon(Icons.refresh, size: 18),
                          label: const Text('Try Again'),
                        ),
                      ],
                    ),
                  ),
                )
              : _branches.isEmpty
                  ? const Center(
                      child: Text(
                        'No assigned branches found.',
                        style: TextStyle(color: AppColors.textSecondary),
                      ),
                    )
                  : RefreshIndicator(
                      onRefresh: _fetchAssignedBranches,
                      color: AppColors.primary,
                      child: ListView(
                        padding: const EdgeInsets.all(16),
                        children: [
                          Container(
                            padding: const EdgeInsets.all(14),
                            margin: const EdgeInsets.only(bottom: 16),
                            decoration: BoxDecoration(
                              color: AppColors.primary.withOpacity(0.08),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: AppColors.primary.withOpacity(0.2),
                              ),
                            ),
                            child: const Row(
                              children: [
                                Icon(Icons.info_outline,
                                    size: 20, color: AppColors.primary),
                                SizedBox(width: 10),
                                Expanded(
                                  child: Text(
                                    'Select a branch to switch your active administration campus.',
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w500,
                                      color: AppColors.primaryDark,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          ..._branches.map((branch) {
                            final isCurrent = _activeBranchId != null &&
                                _activeBranchId.toString() ==
                                    branch.id.toString();
                            final isSwitching =
                                _switchingBranchId == branch.id;

                            return Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                  color: isCurrent
                                      ? AppColors.primary
                                      : AppColors.borderLight,
                                  width: isCurrent ? 1.5 : 1,
                                ),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.03),
                                    blurRadius: 8,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: ListTile(
                                contentPadding: const EdgeInsets.symmetric(
                                    horizontal: 16, vertical: 8),
                                leading: Container(
                                  width: 44,
                                  height: 44,
                                  decoration: BoxDecoration(
                                    color: isCurrent
                                        ? AppColors.primary.withOpacity(0.12)
                                        : AppColors.background,
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Icon(
                                    Icons.business,
                                    color: isCurrent
                                        ? AppColors.primary
                                        : AppColors.textSecondary,
                                    size: 22,
                                  ),
                                ),
                                title: Text(
                                  branch.name,
                                  style: TextStyle(
                                    fontSize: 15,
                                    fontWeight: isCurrent
                                        ? FontWeight.bold
                                        : FontWeight.w600,
                                    color: AppColors.textPrimary,
                                  ),
                                ),
                                subtitle: Text(
                                  'Branch ID: ${branch.id}',
                                  style: const TextStyle(
                                    fontSize: 11,
                                    color: AppColors.textMuted,
                                  ),
                                ),
                                trailing: isCurrent
                                    ? Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 10, vertical: 5),
                                        decoration: BoxDecoration(
                                          color: AppColors.success
                                              .withOpacity(0.12),
                                          borderRadius:
                                              BorderRadius.circular(20),
                                          border: Border.all(
                                              color: AppColors.success
                                                  .withOpacity(0.3)),
                                        ),
                                        child: const Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Icon(Icons.check_circle,
                                                size: 14,
                                                color: AppColors.success),
                                            SizedBox(width: 4),
                                            Text(
                                              'Active',
                                              style: TextStyle(
                                                fontSize: 11,
                                                fontWeight: FontWeight.bold,
                                                color: AppColors.success,
                                              ),
                                            ),
                                          ],
                                        ),
                                      )
                                    : ElevatedButton(
                                        onPressed: isSwitching
                                            ? null
                                            : () => _performSwitch(branch),
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: AppColors.fanta,
                                          foregroundColor: Colors.white,
                                          padding: const EdgeInsets.symmetric(
                                              horizontal: 14, vertical: 8),
                                          minimumSize: Size.zero,
                                          shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(10),
                                          ),
                                          elevation: 0,
                                        ),
                                        child: isSwitching
                                            ? const SizedBox(
                                                width: 14,
                                                height: 14,
                                                child:
                                                    CircularProgressIndicator(
                                                  strokeWidth: 2,
                                                  color: Colors.white,
                                                ),
                                              )
                                            : const Text(
                                                'Switch',
                                                style: TextStyle(
                                                  fontSize: 12,
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                      ),
                              ),
                            );
                          }),
                        ],
                      ),
                    ),
    );
  }
}
