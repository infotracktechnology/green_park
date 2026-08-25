import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api/api_client.dart';
import '../models/achievement_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import 'create_achievement_screen.dart';
import 'edit_achievement_screen.dart';

class AchievementListScreen extends StatefulWidget {
  const AchievementListScreen({super.key});

  @override
  State<AchievementListScreen> createState() => _AchievementListScreenState();
}

class _AchievementListScreenState extends State<AchievementListScreen> {
  String _filterType = '';
  List<AchievementModel> _items = [];
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _fetchItems();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final filters =
          Provider.of<AnnouncementFilterProvider>(context, listen: false);
      if (filters.master == null) {
        filters.fetchMasterData();
      }
    });
  }

  Future<void> _fetchItems() async {
    setState(() => _loading = true);
    try {
      final dio = ApiClient().dio;
      final endpoint = _filterType.isNotEmpty && _filterType != 'ALL'
          ? '/admin/achievement?coaching_type=$_filterType'
          : '/admin/achievement';
      final res = await dio.get(endpoint);
      if (res.data != null && res.data['status'] == true) {
        final list = res.data['achievements'];
        if (list is List) {
          setState(() {
            _items = list.map((e) => AchievementModel.fromJson(e)).toList();
          });
        }
      }
    } catch (e) {
      debugPrint('Fetch achievements error: $e');
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

  String _cleanHtml(String? html) {
    if (html == null) return '';
    return html
        .replaceAll(RegExp(r'<[^>]*>'), ' ')
        .replaceAll('&nbsp;', ' ')
        .replaceAll(RegExp(r'\s+'), ' ')
        .trim();
  }

  Future<void> _openUrl(String url) async {
    try {
      final uri = Uri.parse(url);
      if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Could not open link')));
        }
      }
    } catch (e) {
      debugPrint('Open link error: $e');
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(const SnackBar(content: Text('Could not open link')));
      }
    }
  }

  Future<void> _openFile(String path) async {
    try {
      final normalized = path.replaceAll('\\', '/');
      final fullUrl = normalized.startsWith('http')
          ? normalized
          : '${ApiClient.baseUrl}/${normalized.startsWith('/') ? normalized.substring(1) : normalized}';
      final uri = Uri.parse(fullUrl);
      if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Could not open file')));
        }
      }
    } catch (e) {
      debugPrint('Open file error: $e');
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(const SnackBar(content: Text('Could not open file')));
      }
    }
  }

  void _showDetailsModal(BuildContext context, AchievementModel item) {
    final cleanContent = _cleanHtml(item.content);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return DraggableScrollableSheet(
          initialChildSize: 0.8,
          minChildSize: 0.5,
          maxChildSize: 0.95,
          builder: (_, scrollController) {
            return Container(
              decoration: const BoxDecoration(
                  color: Colors.white,
                  borderRadius:
                      BorderRadius.vertical(top: Radius.circular(28))),
              child: Column(
                children: [
                  const SizedBox(height: 12),
                  Container(
                      width: 40,
                      height: 5,
                      decoration: BoxDecoration(
                          color: AppColors.border,
                          borderRadius: BorderRadius.circular(10))),
                  const SizedBox(height: 12),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(children: [
                          Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                  color: AppColors.primary.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.campaign_outlined,
                                  color: AppColors.primary, size: 20)),
                          const SizedBox(width: 10),
                          const Text('MBBS/BDS Counselling Details',
                              style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textPrimary))
                        ]),
                        IconButton(
                            icon: const Icon(Icons.close,
                                color: AppColors.textMuted, size: 22),
                            onPressed: () => Navigator.pop(ctx)),
                      ],
                    ),
                  ),
                  const Divider(height: 1, color: AppColors.borderLight),
                  Expanded(
                    child: ListView(
                      controller: scrollController,
                      padding: const EdgeInsets.all(20),
                      children: [
                        Wrap(
                          spacing: 8,
                          runSpacing: 6,
                          children: [
                            Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                    color: AppColors.primary.withOpacity(0.12),
                                    borderRadius: BorderRadius.circular(8)),
                                child: Text(item.usertype,
                                    style: const TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primary))),
                            Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                    color: AppColors.fanta.withOpacity(0.12),
                                    borderRadius: BorderRadius.circular(8)),
                                child: Text(item.course ?? 'All Courses',
                                    style: const TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.fanta))),
                          ],
                        ),
                        const SizedBox(height: 12),
                        // Content
                        SelectableText(
                          cleanContent.isNotEmpty ? cleanContent : 'No content',
                          style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: AppColors.textPrimary,
                              height: 1.4),
                        ),
                        const SizedBox(height: 6),
                        Row(children: [
                          const Icon(Icons.access_time,
                              size: 14, color: AppColors.textMuted),
                          const SizedBox(width: 5),
                          Text('Created: ${_formatDateTime(item.createdAt)}',
                              style: const TextStyle(
                                  fontSize: 12, color: AppColors.textMuted))
                        ]),
                        const SizedBox(height: 18),
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                              color: const Color(0xFFF8FAFC),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: AppColors.borderLight)),
                          child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Row(children: [
                                  Icon(Icons.tune,
                                      size: 16, color: AppColors.primary),
                                  SizedBox(width: 6),
                                  Text('TARGET AUDIENCE',
                                      style: TextStyle(
                                          fontSize: 11,
                                          fontWeight: FontWeight.w900,
                                          color: AppColors.textPrimary,
                                          letterSpacing: 0.5))
                                ]),
                                const SizedBox(height: 12),
                                _buildInfoRow('Academic Year',
                                    item.academicYear ?? 'N/A'),
                                _buildInfoRow('User Type', item.usertype),
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
                              ]),
                        ),
                        const SizedBox(height: 18),
                        // File categories and attachments
                        if (item.fileCategoryDisplay != 'None') ...[
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border:
                                    Border.all(color: AppColors.borderLight)),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(children: [
                                  const Icon(Icons.attachment,
                                      size: 16, color: AppColors.primary),
                                  const SizedBox(width: 6),
                                  Text('FILES (${item.fileCategoryDisplay})',
                                      style: const TextStyle(
                                          fontSize: 11,
                                          fontWeight: FontWeight.w900,
                                          color: AppColors.textPrimary,
                                          letterSpacing: 0.5))
                                ]),
                                const SizedBox(height: 10),
                                if (item.video != null &&
                                    item.video!.isNotEmpty)
                                  _buildFileTile(
                                      'Video', item.video!, Icons.video_file),
                                if (item.images.isNotEmpty)
                                  ...item.images.map((img) => _buildFileTile(
                                      'Image', img, Icons.image)),
                                if (item.pdf != null && item.pdf!.isNotEmpty)
                                  _buildFileTile(
                                      'PDF', item.pdf!, Icons.picture_as_pdf),
                                if (item.link != null && item.link!.isNotEmpty)
                                  _buildLinkTile(item.link!),
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
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          SizedBox(
              width: 110,
              child: Text(label,
                  style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textSecondary))),
          const Text(': ',
              style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
          Expanded(
              child: Text(value,
                  style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary)))
        ]));
  }

  Widget _buildFileTile(String label, String path, IconData icon) {
    final fileName = path.replaceAll('\\', '/').split('/').last;
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.border)),
        child: Row(children: [
          Icon(icon, color: AppColors.primary, size: 20),
          const SizedBox(width: 10),
          Expanded(
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                Text(label,
                    style: const TextStyle(
                        fontSize: 10, color: AppColors.textMuted)),
                Text(fileName,
                    style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis),
              ])),
          InkWell(
              onTap: () => _openFile(path),
              borderRadius: BorderRadius.circular(8),
              child: Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                      color: AppColors.primary.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(8)),
                  child: const Row(mainAxisSize: MainAxisSize.min, children: [
                    Icon(Icons.visibility_outlined,
                        size: 13, color: AppColors.primary),
                    SizedBox(width: 3),
                    Text('View',
                        style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: AppColors.primary))
                  ]))),
        ]),
      ),
    );
  }

  Widget _buildLinkTile(String link) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.border)),
      child: Row(children: [
        const Icon(Icons.link, color: AppColors.primary, size: 20),
        const SizedBox(width: 10),
        Expanded(
            child: Text(link,
                style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary),
                maxLines: 1,
                overflow: TextOverflow.ellipsis)),
        InkWell(
            onTap: () => _openUrl(link),
            borderRadius: BorderRadius.circular(8),
            child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(8)),
                child: const Row(mainAxisSize: MainAxisSize.min, children: [
                  Icon(Icons.open_in_new, size: 13, color: AppColors.primary),
                  SizedBox(width: 3),
                  Text('Open',
                      style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary))
                ]))),
      ]),
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
      else ...['OFFLINE', 'ONLINE', 'TEST BATCH']
    ];
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('MBBS/BDS Counselling')),
      body: Column(children: [
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
                              color: active
                                  ? Colors.white
                                  : AppColors.textSecondary),
                          selected: active,
                          selectedColor: AppColors.primary,
                          backgroundColor: const Color(0xFFF8FAFC),
                          side: BorderSide(
                              color: active
                                  ? AppColors.primary
                                  : AppColors.border),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20)),
                          onSelected: (_) {
                            setState(() {
                              _filterType = type == 'ALL' ? '' : type;
                            });
                            _fetchItems();
                          }));
                }).toList()))),
        const Divider(height: 1, color: AppColors.borderLight),
        Expanded(
          child: RefreshIndicator(
            onRefresh: () async {
              await Future.wait([
                _fetchItems(),
                Provider.of<AnnouncementFilterProvider>(context, listen: false)
                    .fetchMasterData()
              ]);
            },
            color: AppColors.fanta,
            child: _loading && _items.isEmpty
                ? const Center(
                    child: CircularProgressIndicator(color: AppColors.fanta))
                : _items.isEmpty
                    ? ListView(children: const [
                        SizedBox(height: 100),
                        Center(
                            child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                              CircleAvatar(
                                  radius: 36,
                                  backgroundColor: Color(0xFFE0F2FE),
                                  child: Icon(Icons.campaign_outlined,
                                      size: 36, color: AppColors.primary)),
                              SizedBox(height: 16),
                              Text('No Entries Found',
                                  style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      color: AppColors.textPrimary)),
                              SizedBox(height: 4),
                              Text('Tap + to add MBBS/BDS counselling info',
                                  style: TextStyle(
                                      fontSize: 12, color: AppColors.textMuted))
                            ]))
                      ])
                    : ListView.separated(
                        padding: const EdgeInsets.fromLTRB(16, 16, 16, 90),
                        itemCount: _items.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 12),
                        itemBuilder: (context, index) {
                          final item = _items[index];
                          final cleanContent = _cleanHtml(item.content);

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
                                        offset: const Offset(0, 2))
                                  ]),
                              child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.spaceBetween,
                                        children: [
                                          Expanded(
                                              child: Wrap(
                                                  spacing: 6,
                                                  runSpacing: 4,
                                                  children: [
                                                Container(
                                                    padding:
                                                        const EdgeInsets
                                                            .symmetric(
                                                            horizontal: 8,
                                                            vertical: 3),
                                                    decoration: BoxDecoration(
                                                        color: AppColors.primary
                                                            .withOpacity(0.1),
                                                        borderRadius:
                                                            BorderRadius
                                                                .circular(6)),
                                                    child: Text(item.usertype,
                                                        style: const TextStyle(
                                                            fontSize: 10,
                                                            fontWeight:
                                                                FontWeight.bold,
                                                            color: AppColors
                                                                .primary))),
                                                Container(
                                                    padding:
                                                        const EdgeInsets
                                                            .symmetric(
                                                            horizontal: 8,
                                                            vertical: 3),
                                                    decoration: BoxDecoration(
                                                        color: AppColors.fanta
                                                            .withOpacity(0.1),
                                                        borderRadius:
                                                            BorderRadius
                                                                .circular(6)),
                                                    child: Text(
                                                        item.course ?? 'All',
                                                        style: const TextStyle(
                                                            fontSize: 10,
                                                            fontWeight:
                                                                FontWeight.bold,
                                                            color: AppColors
                                                                .fanta))),
                                              ])),
                                          Text(_formatDate(item.createdAt),
                                              style: const TextStyle(
                                                  fontSize: 11,
                                                  color: AppColors.textMuted))
                                        ]),
                                    const SizedBox(height: 10),
                                    Text(
                                        cleanContent.isNotEmpty
                                            ? cleanContent
                                            : 'No content',
                                        style: const TextStyle(
                                            fontSize: 15,
                                            fontWeight: FontWeight.bold,
                                            color: AppColors.textPrimary),
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis),
                                    const SizedBox(height: 12),
                                    const Divider(
                                        height: 1,
                                        color: AppColors.borderLight),
                                    const SizedBox(height: 10),
                                    Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.spaceBetween,
                                        children: [
                                          Expanded(
                                              child: Row(children: [
                                            const Icon(Icons.people_outline,
                                                size: 14,
                                                color: AppColors.textMuted),
                                            const SizedBox(width: 4),
                                            Expanded(
                                                child: Text(
                                                    '${item.coachingTypeDisplay} • ${item.categoryDisplay}',
                                                    style: const TextStyle(
                                                        fontSize: 11,
                                                        color: AppColors
                                                            .textMuted),
                                                    maxLines: 1,
                                                    overflow:
                                                        TextOverflow.ellipsis)),
                                            if (item.fileCategoryDisplay !=
                                                'None') ...[
                                              const SizedBox(width: 6),
                                              Container(
                                                  padding:
                                                      const EdgeInsets.symmetric(
                                                          horizontal: 6,
                                                          vertical: 2),
                                                  decoration: BoxDecoration(
                                                      color:
                                                          const Color(0xFFF1F5F9),
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                              6)),
                                                  child: Text(
                                                      item.fileCategoryDisplay,
                                                      style: const TextStyle(
                                                          fontSize: 10,
                                                          fontWeight:
                                                              FontWeight.bold,
                                                          color: AppColors
                                                              .primary)))
                                            ]
                                          ])),
                                          Row(children: [
                                            InkWell(
                                                onTap: () => _showDetailsModal(
                                                    context, item),
                                                borderRadius:
                                                    BorderRadius.circular(8),
                                                child: Container(
                                                    padding: const EdgeInsets
                                                        .symmetric(
                                                        horizontal: 8,
                                                        vertical: 4),
                                                    decoration: BoxDecoration(
                                                        color: AppColors.primary
                                                            .withOpacity(0.08),
                                                        borderRadius:
                                                            BorderRadius
                                                                .circular(8)),
                                                    child: const Row(
                                                        mainAxisSize:
                                                            MainAxisSize.min,
                                                        children: [
                                                          Icon(
                                                              Icons
                                                                  .visibility_outlined,
                                                              size: 13,
                                                              color: AppColors
                                                                  .primary),
                                                          SizedBox(width: 3),
                                                          Text('View',
                                                              style: TextStyle(
                                                                  fontSize: 11,
                                                                  fontWeight:
                                                                      FontWeight
                                                                          .bold,
                                                                  color: AppColors
                                                                      .primary))
                                                        ]))),
                                            const SizedBox(width: 8),
                                            InkWell(
                                                onTap: () async {
                                                  final res = await Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                          builder: (_) =>
                                                              EditAchievementScreen(
                                                                  keyId: item
                                                                      .id)));
                                                  if (res == true) {
                                                    _fetchItems();
                                                  }
                                                },
                                                borderRadius:
                                                    BorderRadius.circular(8),
                                                child: Container(
                                                    padding:
                                                        const EdgeInsets
                                                            .symmetric(
                                                            horizontal: 8,
                                                            vertical: 4),
                                                    decoration: BoxDecoration(
                                                        color: AppColors.fanta
                                                            .withOpacity(0.1),
                                                        borderRadius:
                                                            BorderRadius
                                                                .circular(8)),
                                                    child: const Row(
                                                        mainAxisSize:
                                                            MainAxisSize.min,
                                                        children: [
                                                          Icon(
                                                              Icons
                                                                  .edit_outlined,
                                                              size: 13,
                                                              color: AppColors
                                                                  .fanta),
                                                          SizedBox(width: 3),
                                                          Text('Edit',
                                                              style: TextStyle(
                                                                  fontSize: 11,
                                                                  fontWeight:
                                                                      FontWeight
                                                                          .bold,
                                                                  color: AppColors
                                                                      .fanta))
                                                        ]))),
                                          ]),
                                        ]),
                                  ]),
                            ),
                          );
                        },
                      ),
          ),
        ),
      ]),
      floatingActionButton: FloatingActionButton(
          onPressed: () async {
            final res = await Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => const CreateAchievementScreen()));
            if (res == true) _fetchItems();
          },
          backgroundColor: AppColors.fanta,
          elevation: 4,
          shape: const CircleBorder(),
          child: const Icon(Icons.add, color: Colors.white, size: 28)),
    );
  }
}
