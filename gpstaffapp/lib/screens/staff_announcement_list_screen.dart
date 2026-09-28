import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../api/api_client.dart';
import '../models/staff_announcement_model.dart';
import '../theme/app_theme.dart';
import 'create_staff_announcement_screen.dart';
import 'edit_staff_announcement_screen.dart';

/// Staff Announcements list.
///
/// [canManage] = true (Admin) allows create / edit, otherwise the
/// screen is a read-only view of the announcements targeted to the staff user.
class StaffAnnouncementListScreen extends StatefulWidget {
  final bool canManage;

  const StaffAnnouncementListScreen({super.key, this.canManage = true});

  @override
  State<StaffAnnouncementListScreen> createState() =>
      _StaffAnnouncementListScreenState();
}

class _StaffAnnouncementListScreenState
    extends State<StaffAnnouncementListScreen> {
  List<StaffAnnouncementModel> _announcements = [];
  bool _loading = false;
  String? _error;

  String get _endpoint => widget.canManage
      ? '/admin/staffannouncement'
      : '/admin/staff_announcement';

  @override
  void initState() {
    super.initState();
    _fetchAnnouncements();
  }

  Future<void> _fetchAnnouncements() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final res = await ApiClient().dio.get(_endpoint);

      if (!mounted) return;

      if (res.data != null && res.data['status'] == true) {
        final list = res.data['announcements'];
        setState(() {
          _announcements = list is List
              ? list
                  .whereType<Map>()
                  .map((e) =>
                      StaffAnnouncementModel.fromJson(Map<String, dynamic>.from(e)))
                  .toList()
              : [];
        });
      } else {
        setState(() {
          _error = (res.data is Map ? res.data['message'] : null)?.toString() ??
              'Unable to load announcements.';
        });
      }
    } catch (e) {
      debugPrint('Fetch staff announcements error: $e');
      if (mounted) setState(() => _error = 'Unable to load announcements.');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  String _formatDate(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '';
    try {
      return DateFormat('dd/MM/yyyy').format(DateTime.parse(dateStr));
    } catch (_) {
      return dateStr;
    }
  }

  String _formatDateTime(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '';
    try {
      return DateFormat('dd MMM yyyy, hh:mm a').format(DateTime.parse(dateStr));
    } catch (_) {
      return dateStr;
    }
  }

  String _attachmentUrl(String path) {
    if (path.startsWith('http')) return path;
    final clean = path.startsWith('/') ? path.substring(1) : path;
    return '${ApiClient.baseUrl}/$clean';
  }

  Future<void> _openAttachment(String url) async {
    try {
      final uri = Uri.parse(url);
      if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        _showMessage('Could not open attachment', isError: true);
      }
    } catch (e) {
      debugPrint('Open attachment error: $e');
      _showMessage('Could not open attachment', isError: true);
    }
  }

  void _showMessage(String message, {bool isError = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? AppColors.error : AppColors.success,
      ),
    );
  }

  Future<void> _openCreate() async {
    final res = await Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const CreateStaffAnnouncementScreen()),
    );

    if (res == true) _fetchAnnouncements();
  }

  Future<void> _openEdit(StaffAnnouncementModel item) async {
    final res = await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => EditStaffAnnouncementScreen(announcementId: item.id),
      ),
    );

    if (res == true) _fetchAnnouncements();
  }

  Widget _badge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        text.toUpperCase(),
        style: TextStyle(
          fontSize: 9,
          fontWeight: FontWeight.w900,
          color: color,
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: AppColors.textMuted),
          const SizedBox(width: 10),
          SizedBox(
            width: 92,
            child: Text(
              label,
              style:
                  const TextStyle(fontSize: 12, color: AppColors.textSecondary),
            ),
          ),
          Expanded(
            child: Text(
              value.isEmpty ? '-' : value,
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _attachmentRow(String path) {
    final fileName = StaffAnnouncementModel.getAttachmentFileName(path);
    final url = _attachmentUrl(path);

    return InkWell(
      onTap: () => _openAttachment(url),
      borderRadius: BorderRadius.circular(12),
      child: Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
          color: const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          children: [
            const Icon(Icons.description_outlined,
                color: AppColors.primary, size: 18),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                fileName,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const Icon(Icons.download_outlined, size: 16, color: AppColors.fanta),
          ],
        ),
      ),
    );
  }

  void _showDetails(StaffAnnouncementModel item) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => DraggableScrollableSheet(
        initialChildSize: 0.72,
        minChildSize: 0.4,
        maxChildSize: 0.95,
        expand: false,
        builder: (ctx, scrollController) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
          ),
          child: ListView(
            controller: scrollController,
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 32),
            children: [
              Center(
                child: Container(
                  width: 44,
                  height: 5,
                  decoration: BoxDecoration(
                    color: AppColors.border,
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
              ),
              const SizedBox(height: 18),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Text(
                      item.title,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  _badge(
                    item.isIndividual ? 'Individual' : 'Group',
                    item.isIndividual ? AppColors.fanta : AppColors.primary,
                  ),
                ],
              ),
              const SizedBox(height: 16),
              _infoRow(Icons.business_outlined, 'Branch', item.branchNames),
              _infoRow(
                  Icons.apartment_outlined, 'Department', item.departmentNames),
              if ((item.gender ?? '').isNotEmpty)
                _infoRow(Icons.people_outline, 'Gender', item.gender!),
              if (item.isIndividual)
                _infoRow(Icons.person_outline, 'Staff',
                    '${item.staffIds.length} selected'),
              _infoRow(Icons.event_outlined, 'Created',
                  _formatDateTime(item.createdAt)),
              if (item.isScheduled)
                _infoRow(Icons.schedule, 'Publish At',
                    _formatDateTime(item.startAt)),
              const SizedBox(height: 6),
              const Divider(height: 1, color: AppColors.borderLight),
              const SizedBox(height: 14),
              const Text(
                'CONTENT',
                style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textSecondary,
                    letterSpacing: 0.5),
              ),
              const SizedBox(height: 8),
              Text(
                item.cleanContent.isEmpty ? 'No content' : item.cleanContent,
                style: const TextStyle(
                    fontSize: 13, height: 1.5, color: AppColors.textPrimary),
              ),
              if (item.attachments.isNotEmpty) ...[
                const SizedBox(height: 18),
                const Text(
                  'ATTACHMENTS',
                  style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textSecondary,
                      letterSpacing: 0.5),
                ),
                const SizedBox(height: 8),
                ...item.attachments.map(_attachmentRow),
              ],
              if (widget.canManage) ...[
                const SizedBox(height: 18),
                SizedBox(
                  height: 48,
                  child: ElevatedButton.icon(
                    onPressed: () {
                      Navigator.pop(ctx);
                      _openEdit(item);
                    },
                    icon: const Icon(Icons.edit_outlined, size: 18),
                    label: const Text('Edit Announcement',
                        style: TextStyle(
                            fontSize: 14, fontWeight: FontWeight.bold)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                    ),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _announcementCard(StaffAnnouncementModel item) {
    return InkWell(
      onTap: () => _showDetails(item),
      borderRadius: BorderRadius.circular(20),
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
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.campaign_outlined,
                      color: AppColors.primary, size: 20),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item.title,
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 6),
                      Wrap(
                        spacing: 6,
                        runSpacing: 4,
                        children: [
                          _badge(
                            item.isIndividual ? 'Individual' : 'Group',
                            item.isIndividual
                                ? AppColors.fanta
                                : AppColors.primary,
                          ),
                          if (item.isScheduled)
                            _badge('Scheduled', AppColors.warning),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
            if (item.cleanContent.isNotEmpty) ...[
              const SizedBox(height: 12),
              Text(
                item.cleanContent,
                style: const TextStyle(
                    fontSize: 12, color: AppColors.textSecondary, height: 1.4),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ],
            const SizedBox(height: 12),
            const Divider(height: 1, color: AppColors.borderLight),
            const SizedBox(height: 10),
            Row(
              children: [
                const Icon(Icons.people_outline,
                    size: 14, color: AppColors.textMuted),
                const SizedBox(width: 4),
                Expanded(
                  child: Text(
                    '${item.branchNames} • ${item.departmentNames}',
                    style: const TextStyle(
                        fontSize: 11, color: AppColors.textMuted),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                if (item.attachments.isNotEmpty) ...[
                  const Icon(Icons.attach_file,
                      size: 13, color: AppColors.primary),
                  const SizedBox(width: 2),
                  Text(
                    '${item.attachments.length}',
                    style: const TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: AppColors.primary),
                  ),
                  const SizedBox(width: 8),
                ],
                Text(
                  _formatDate(item.createdAt),
                  style:
                      const TextStyle(fontSize: 11, color: AppColors.textMuted),
                ),
                if (widget.canManage) ..._manageActions(item),
              ],
            ),
          ],
        ),
      ),
    );
  }

  List<Widget> _manageActions(StaffAnnouncementModel item) {
    return [
      const SizedBox(width: 8),
      InkWell(
        onTap: () => _openEdit(item),
        borderRadius: BorderRadius.circular(8),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: AppColors.fanta.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: const Row(
            children: [
              Icon(Icons.edit_outlined, size: 13, color: AppColors.fanta),
              SizedBox(width: 3),
              Text(
                'Edit',
                style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: AppColors.fanta),
              ),
            ],
          ),
        ),
      ),
    ];
  }

  Widget _emptyState() {
    return ListView(
      children: const [
        SizedBox(height: 100),
        Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircleAvatar(
                radius: 36,
                backgroundColor: Color(0xFFE0F2FE),
                child: Icon(Icons.campaign_outlined,
                    size: 36, color: AppColors.primary),
              ),
              SizedBox(height: 16),
              Text(
                'No Announcements',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              SizedBox(height: 4),
              Text(
                'Announcements targeted to you will appear here',
                style: TextStyle(fontSize: 12, color: AppColors.textMuted),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text(
          widget.canManage ? 'Staff Announcements' : 'Announcements',
        ),
      ),
      body: Column(
        children: [
          if (_error != null)
            Container(
              width: double.infinity,
              margin: const EdgeInsets.fromLTRB(16, 16, 16, 0),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: AppColors.error.withOpacity(0.08),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.error.withOpacity(0.25)),
              ),
              child: Row(
                children: [
                  const Icon(Icons.error_outline,
                      size: 18, color: AppColors.error),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      _error!,
                      style: const TextStyle(
                          fontSize: 12, color: AppColors.textPrimary),
                    ),
                  ),
                  TextButton(
                    onPressed: _fetchAnnouncements,
                    child: const Text('Retry',
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: AppColors.error)),
                  ),
                ],
              ),
            ),
          Expanded(
            child: RefreshIndicator(
              onRefresh: _fetchAnnouncements,
              color: AppColors.fanta,
              child: _loading && _announcements.isEmpty
                  ? const Center(
                      child: CircularProgressIndicator(color: AppColors.fanta),
                    )
                  : _announcements.isEmpty
                      ? _emptyState()
                      : ListView.separated(
                          padding: const EdgeInsets.fromLTRB(16, 16, 16, 90),
                          itemCount: _announcements.length,
                          separatorBuilder: (_, __) =>
                              const SizedBox(height: 12),
                          itemBuilder: (context, index) =>
                              _announcementCard(_announcements[index]),
                        ),
            ),
          ),
        ],
      ),
      floatingActionButton: widget.canManage
          ? FloatingActionButton(
              onPressed: _openCreate,
              backgroundColor: AppColors.fanta,
              elevation: 4,
              shape: const CircleBorder(),
              child: const Icon(Icons.add, color: Colors.white, size: 28),
            )
          : null,
    );
  }
}
