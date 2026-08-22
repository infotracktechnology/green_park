import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api/api_client.dart';
import '../models/exam_portion_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import 'create_exam_portion_screen.dart';
import 'edit_exam_portion_screen.dart';

class ExamPortionListScreen extends StatefulWidget {
  const ExamPortionListScreen({super.key});

  @override
  State<ExamPortionListScreen> createState() => _ExamPortionListScreenState();
}

class _ExamPortionListScreenState extends State<ExamPortionListScreen> {
  String _filterType = '';
  List<ExamPortionModel> _examPortions = [];
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _fetchExamPortions();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final filters =
          Provider.of<AnnouncementFilterProvider>(context, listen: false);
      if (filters.master == null) {
        filters.fetchMasterData();
      }
    });
  }

  Future<void> _fetchExamPortions() async {
    setState(() => _loading = true);
    try {
      final dio = ApiClient().dio;
      final endpoint = _filterType.isNotEmpty && _filterType != 'ALL'
          ? '/admin/examportion?coaching_type=$_filterType'
          : '/admin/examportion';

      final res = await dio.get(endpoint);
      if (res.data != null && res.data['status'] == true) {
        final list = res.data['examportions'];
        if (list is List) {
          setState(() {
            _examPortions =
                list.map((e) => ExamPortionModel.fromJson(e)).toList();
          });
        }
      }
    } catch (e) {
      debugPrint('Fetch exam portions error: $e');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  String _formatDate(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '';
    try {
      final dt = DateTime.parse(dateStr);
      return DateFormat('dd/MM/yyyy').format(dt);
    } catch (_) {
      return dateStr;
    }
  }

  String _formatDateTime(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '';
    try {
      final dt = DateTime.parse(dateStr);
      return DateFormat('dd MMM yyyy, hh:mm a').format(dt);
    } catch (_) {
      return dateStr;
    }
  }

  Future<void> _openAttachment(String url) async {
    try {
      final uri = Uri.parse(url);
      if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Could not open attachment')),
          );
        }
      }
    } catch (e) {
      debugPrint('Open attachment error: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Could not open attachment')),
        );
      }
    }
  }

  void _showDetailsModal(BuildContext context, ExamPortionModel item) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return DraggableScrollableSheet(
          initialChildSize: 0.75,
          minChildSize: 0.4,
          maxChildSize: 0.95,
          builder: (_, scrollController) {
            return Container(
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
              ),
              child: Column(
                children: [
                  // Drag Handle
                  const SizedBox(height: 12),
                  Container(
                    width: 40,
                    height: 5,
                    decoration: BoxDecoration(
                      color: AppColors.border,
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Header
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.primary.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: const Icon(Icons.description_outlined,
                                  color: AppColors.primary, size: 20),
                            ),
                            const SizedBox(width: 10),
                            const Text(
                              'Exam Portion Details',
                              style: TextStyle(
                                fontSize: 17,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textPrimary,
                              ),
                            ),
                          ],
                        ),
                        IconButton(
                          icon: const Icon(Icons.close,
                              color: AppColors.textMuted, size: 22),
                          onPressed: () => Navigator.pop(ctx),
                        ),
                      ],
                    ),
                  ),
                  const Divider(height: 1, color: AppColors.borderLight),

                  // Body Content
                  Expanded(
                    child: ListView(
                      controller: scrollController,
                      padding: const EdgeInsets.all(20),
                      children: [
                        // Badges Row
                        Wrap(
                          spacing: 8,
                          runSpacing: 6,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: AppColors.primary.withOpacity(0.12),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                item.usertype,
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.primary,
                                ),
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: AppColors.fanta.withOpacity(0.12),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                item.course ?? 'All Courses',
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.fanta,
                                ),
                              ),
                            ),
                            if (item.isSchedule == 1)
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: Colors.amber.shade100,
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  'SCHEDULED',
                                  style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.amber.shade900,
                                  ),
                                ),
                              ),
                          ],
                        ),
                        const SizedBox(height: 12),

                        // Title
                        SelectableText(
                          item.title,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textPrimary,
                            height: 1.3,
                          ),
                        ),
                        const SizedBox(height: 6),

                        // Created Date & Schedule Date
                        Row(
                          children: [
                            const Icon(Icons.access_time,
                                size: 14, color: AppColors.textMuted),
                            const SizedBox(width: 5),
                            Text(
                              'Created: ${_formatDateTime(item.createdAt)}',
                              style: const TextStyle(
                                  fontSize: 12, color: AppColors.textMuted),
                            ),
                          ],
                        ),
                        if (item.isSchedule == 1 &&
                            item.startAt != null &&
                            item.startAt!.isNotEmpty) ...[
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(Icons.schedule,
                                  size: 14, color: Colors.amber),
                              const SizedBox(width: 5),
                              Text(
                                'Scheduled for: ${_formatDateTime(item.startAt)}',
                                style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    color: Colors.amber.shade900),
                              ),
                            ],
                          ),
                        ],
                        const SizedBox(height: 18),

                        // Target Audience Card
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF8FAFC),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: AppColors.borderLight),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Row(
                                children: [
                                  Icon(Icons.tune,
                                      size: 16, color: AppColors.primary),
                                  SizedBox(width: 6),
                                  Text(
                                    'TARGET AUDIENCE',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w900,
                                      color: AppColors.textPrimary,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 12),
                              _buildInfoRow(
                                  'Academic Year', item.academicYear ?? 'N/A'),
                              _buildInfoRow(
                                  'User Type',
                                  item.usertype == 'INDIVIDUAL'
                                      ? 'Individual Student'
                                      : 'Group Broadcast'),
                              _buildInfoRow(
                                  'Course', item.course ?? 'All Courses'),
                              _buildInfoRow('Branches', item.branchDisplay),
                              _buildInfoRow(
                                  'Coaching Type', item.coachingTypeDisplay),
                              if (item.category != null &&
                                  item.category.toString().isNotEmpty)
                                _buildInfoRow(
                                    'Category (H/D)', item.categoryDisplay),
                              if (item.batch != null &&
                                  item.batch.toString().isNotEmpty)
                                _buildInfoRow('Batch', item.batchDisplay),
                              _buildInfoRow('Gender', item.gender ?? 'All'),
                              if (item.usertype == 'INDIVIDUAL')
                                _buildInfoRow('Target Student',
                                    item.students?.toString() ?? 'N/A')
                              else
                                _buildInfoRow(
                                    'Section',
                                    (item.section == null ||
                                            item.section!.isEmpty)
                                        ? 'All Sections'
                                        : item.section!),
                            ],
                          ),
                        ),
                        const SizedBox(height: 18),

                        // Attachments Card
                        if (item.attachments.isNotEmpty) ...[
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF8FAFC),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: AppColors.borderLight),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    const Icon(Icons.attachment,
                                        size: 16, color: AppColors.primary),
                                    const SizedBox(width: 6),
                                    Text(
                                      'ATTACHMENTS (${item.attachments.length})',
                                      style: const TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w900,
                                        color: AppColors.textPrimary,
                                        letterSpacing: 0.5,
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                ListView.separated(
                                  shrinkWrap: true,
                                  physics: const NeverScrollableScrollPhysics(),
                                  itemCount: item.attachments.length,
                                  separatorBuilder: (_, __) =>
                                      const SizedBox(height: 8),
                                  itemBuilder: (context, idx) {
                                    final path = item.attachments[idx];
                                    final fileName =
                                        ExamPortionModel.getAttachmentFileName(
                                            path);
                                    final fullUrl = path.startsWith('http')
                                        ? path
                                        : '${ApiClient.baseUrl}/${path.startsWith('/') ? path.substring(1) : path}';

                                    return Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 12, vertical: 10),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius: BorderRadius.circular(12),
                                        border:
                                            Border.all(color: AppColors.border),
                                      ),
                                      child: Row(
                                        children: [
                                          const Icon(
                                              Icons.picture_as_pdf_outlined,
                                              color: AppColors.primary,
                                              size: 20),
                                          const SizedBox(width: 10),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                Text(
                                                  fileName,
                                                  style: const TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color:
                                                        AppColors.textPrimary,
                                                  ),
                                                  maxLines: 1,
                                                  overflow:
                                                      TextOverflow.ellipsis,
                                                ),
                                                Text(
                                                  fullUrl,
                                                  style: const TextStyle(
                                                      fontSize: 10,
                                                      color:
                                                          AppColors.textMuted),
                                                  maxLines: 1,
                                                  overflow:
                                                      TextOverflow.ellipsis,
                                                ),
                                              ],
                                            ),
                                          ),
                                          InkWell(
                                            onTap: () =>
                                                _openAttachment(fullUrl),
                                            borderRadius:
                                                BorderRadius.circular(8),
                                            child: Container(
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                      horizontal: 8,
                                                      vertical: 4),
                                              decoration: BoxDecoration(
                                                color: AppColors.primary
                                                    .withOpacity(0.08),
                                                borderRadius:
                                                    BorderRadius.circular(8),
                                              ),
                                              child: const Row(
                                                mainAxisSize: MainAxisSize.min,
                                                children: [
                                                  Icon(
                                                      Icons.visibility_outlined,
                                                      size: 13,
                                                      color: AppColors.primary),
                                                  SizedBox(width: 3),
                                                  Text(
                                                    'View',
                                                    style: TextStyle(
                                                      fontSize: 11,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                      color: AppColors.primary,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    );
                                  },
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 18),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 110,
            child: Text(
              label,
              style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textSecondary),
            ),
          ),
          const Text(': ',
              style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filterProvider = Provider.of<AnnouncementFilterProvider>(context);
    final masterTypes = filterProvider.master?.coachingTypes ?? [];
    final coachingTypes = [
      'ALL',
      if (masterTypes.isNotEmpty)
        ...masterTypes
      else ...[
        'OFFLINE',
        'ONLINE',
        'TEST BATCH',
      ]
    ];

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Exam Portions'),
      ),
      body: Column(
        children: [
          // Filter Tabs Bar
          Container(
            color: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              physics: const BouncingScrollPhysics(),
              child: Row(
                children: coachingTypes.map((type) {
                  final active = _filterType == type ||
                      (type == 'ALL' && _filterType.isEmpty);
                  return Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: ChoiceChip(
                      label: Text(type),
                      labelStyle: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: active ? Colors.white : AppColors.textSecondary,
                      ),
                      selected: active,
                      selectedColor: AppColors.primary,
                      backgroundColor: const Color(0xFFF8FAFC),
                      side: BorderSide(
                        color: active ? AppColors.primary : AppColors.border,
                      ),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20)),
                      onSelected: (_) {
                        setState(() {
                          _filterType = type == 'ALL' ? '' : type;
                        });
                        _fetchExamPortions();
                      },
                    ),
                  );
                }).toList(),
              ),
            ),
          ),
          const Divider(height: 1, color: AppColors.borderLight),

          // Exam Portions List
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async {
                await Future.wait([
                  _fetchExamPortions(),
                  Provider.of<AnnouncementFilterProvider>(context,
                          listen: false)
                      .fetchMasterData(),
                ]);
              },
              color: AppColors.fanta,
              child: _loading && _examPortions.isEmpty
                  ? const Center(
                      child: CircularProgressIndicator(color: AppColors.fanta),
                    )
                  : _examPortions.isEmpty
                      ? ListView(
                          children: const [
                            SizedBox(height: 100),
                            Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  CircleAvatar(
                                    radius: 36,
                                    backgroundColor: Color(0xFFE0F2FE),
                                    child: Icon(Icons.description_outlined,
                                        size: 36, color: AppColors.primary),
                                  ),
                                  SizedBox(height: 16),
                                  Text(
                                    'No Exam Portions',
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      color: AppColors.textPrimary,
                                    ),
                                  ),
                                  SizedBox(height: 4),
                                  Text(
                                    'Tap + to add a new exam portion',
                                    style: TextStyle(
                                        fontSize: 12,
                                        color: AppColors.textMuted),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        )
                      : ListView.separated(
                          padding: const EdgeInsets.fromLTRB(16, 16, 16, 90),
                          itemCount: _examPortions.length,
                          separatorBuilder: (_, __) =>
                              const SizedBox(height: 12),
                          itemBuilder: (context, index) {
                            final item = _examPortions[index];

                            return InkWell(
                              onTap: () => _showDetailsModal(context, item),
                              borderRadius: BorderRadius.circular(20),
                              child: Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(20),
                                  border:
                                      Border.all(color: AppColors.borderLight),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.02),
                                      blurRadius: 6,
                                      offset: const Offset(0, 2),
                                    ),
                                  ],
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Top Row: Badges + Date
                                    Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Expanded(
                                          child: Wrap(
                                            spacing: 6,
                                            runSpacing: 4,
                                            children: [
                                              // User Type
                                              Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        horizontal: 8,
                                                        vertical: 3),
                                                decoration: BoxDecoration(
                                                  color: AppColors.primary
                                                      .withOpacity(0.1),
                                                  borderRadius:
                                                      BorderRadius.circular(6),
                                                ),
                                                child: Text(
                                                  item.usertype,
                                                  style: const TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: AppColors.primary,
                                                  ),
                                                ),
                                              ),
                                              // Course
                                              Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        horizontal: 8,
                                                        vertical: 3),
                                                decoration: BoxDecoration(
                                                  color: AppColors.fanta
                                                      .withOpacity(0.1),
                                                  borderRadius:
                                                      BorderRadius.circular(6),
                                                ),
                                                child: Text(
                                                  item.course ?? 'All',
                                                  style: const TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: AppColors.fanta,
                                                  ),
                                                ),
                                              ),
                                              // Scheduled
                                              if (item.isSchedule == 1)
                                                Container(
                                                  padding: const EdgeInsets
                                                      .symmetric(
                                                      horizontal: 8,
                                                      vertical: 3),
                                                  decoration: BoxDecoration(
                                                    color:
                                                        Colors.amber.shade100,
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            6),
                                                  ),
                                                  child: Text(
                                                    'SCHEDULED',
                                                    style: TextStyle(
                                                      fontSize: 10,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                      color:
                                                          Colors.amber.shade900,
                                                    ),
                                                  ),
                                                ),
                                            ],
                                          ),
                                        ),
                                        Text(
                                          _formatDate(item.createdAt),
                                          style: const TextStyle(
                                              fontSize: 11,
                                              color: AppColors.textMuted),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 10),

                                    // Title
                                    Text(
                                      item.title,
                                      style: const TextStyle(
                                        fontSize: 15,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.textPrimary,
                                      ),
                                    ),

                                    const SizedBox(height: 12),
                                    const Divider(
                                        height: 1,
                                        color: AppColors.borderLight),
                                    const SizedBox(height: 10),

                                    // Bottom Row: Summary & Action buttons
                                    Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Expanded(
                                          child: Row(
                                            children: [
                                              const Icon(Icons.people_outline,
                                                  size: 14,
                                                  color: AppColors.textMuted),
                                              const SizedBox(width: 4),
                                              Expanded(
                                                child: Text(
                                                  '${item.coachingType ?? "ALL"} • ${item.category ?? "All Categories"}',
                                                  style: const TextStyle(
                                                      fontSize: 11,
                                                      color:
                                                          AppColors.textMuted),
                                                  maxLines: 1,
                                                  overflow:
                                                      TextOverflow.ellipsis,
                                                ),
                                              ),
                                              if (item
                                                  .attachments.isNotEmpty) ...[
                                                const SizedBox(width: 6),
                                                Container(
                                                  padding: const EdgeInsets
                                                      .symmetric(
                                                      horizontal: 6,
                                                      vertical: 2),
                                                  decoration: BoxDecoration(
                                                    color:
                                                        const Color(0xFFF1F5F9),
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            6),
                                                  ),
                                                  child: Row(
                                                    mainAxisSize:
                                                        MainAxisSize.min,
                                                    children: [
                                                      const Icon(
                                                          Icons.attach_file,
                                                          size: 11,
                                                          color: AppColors
                                                              .primary),
                                                      const SizedBox(width: 2),
                                                      Text(
                                                        '${item.attachments.length}',
                                                        style: const TextStyle(
                                                            fontSize: 10,
                                                            fontWeight:
                                                                FontWeight.bold,
                                                            color: AppColors
                                                                .primary),
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                              ],
                                            ],
                                          ),
                                        ),
                                        Row(
                                          children: [
                                            // View Details button
                                            InkWell(
                                              onTap: () => _showDetailsModal(
                                                  context, item),
                                              borderRadius:
                                                  BorderRadius.circular(8),
                                              child: Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        horizontal: 8,
                                                        vertical: 4),
                                                decoration: BoxDecoration(
                                                  color: AppColors.primary
                                                      .withOpacity(0.08),
                                                  borderRadius:
                                                      BorderRadius.circular(8),
                                                ),
                                                child: const Row(
                                                  mainAxisSize:
                                                      MainAxisSize.min,
                                                  children: [
                                                    Icon(
                                                        Icons
                                                            .visibility_outlined,
                                                        size: 13,
                                                        color:
                                                            AppColors.primary),
                                                    SizedBox(width: 3),
                                                    Text(
                                                      'View',
                                                      style: TextStyle(
                                                        fontSize: 11,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                        color:
                                                            AppColors.primary,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            ),
                                            const SizedBox(width: 8),
                                            // Edit button
                                            InkWell(
                                              onTap: () async {
                                                final res =
                                                    await Navigator.push(
                                                  context,
                                                  MaterialPageRoute(
                                                    builder: (_) =>
                                                        EditExamPortionScreen(
                                                            portionId: item.id),
                                                  ),
                                                );
                                                if (res == true) {
                                                  _fetchExamPortions();
                                                }
                                              },
                                              borderRadius:
                                                  BorderRadius.circular(8),
                                              child: Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        horizontal: 8,
                                                        vertical: 4),
                                                decoration: BoxDecoration(
                                                  color: AppColors.fanta
                                                      .withOpacity(0.1),
                                                  borderRadius:
                                                      BorderRadius.circular(8),
                                                ),
                                                child: const Row(
                                                  mainAxisSize:
                                                      MainAxisSize.min,
                                                  children: [
                                                    Icon(Icons.edit_outlined,
                                                        size: 13,
                                                        color: AppColors.fanta),
                                                    SizedBox(width: 3),
                                                    Text(
                                                      'Edit',
                                                      style: TextStyle(
                                                        fontSize: 11,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                        color: AppColors.fanta,
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
                                  ],
                                ),
                              ),
                            );
                          },
                        ),
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final res = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const CreateExamPortionScreen()),
          );
          if (res == true) _fetchExamPortions();
        },
        backgroundColor: AppColors.fanta,
        elevation: 4,
        shape: const CircleBorder(),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
    );
  }
}
