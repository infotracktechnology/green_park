import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../api/api_client.dart';
import '../models/class_video_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import 'create_class_video_screen.dart';
import 'edit_class_video_screen.dart';

class ClassVideoListScreen extends StatefulWidget {
  const ClassVideoListScreen({super.key});

  @override
  State<ClassVideoListScreen> createState() => _ClassVideoListScreenState();
}

class _ClassVideoListScreenState extends State<ClassVideoListScreen> {
  String _filterType = '';
  List<ClassVideoModel> _videos = [];
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _fetchVideos();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final filters =
          Provider.of<AnnouncementFilterProvider>(context, listen: false);
      if (filters.master == null) {
        filters.fetchMasterData();
      }
    });
  }

  Future<void> _fetchVideos() async {
    setState(() => _loading = true);
    try {
      final dio = ApiClient().dio;
      final endpoint = _filterType.isNotEmpty && _filterType != 'ALL'
          ? '/admin/classvideo?coaching_type=$_filterType'
          : '/admin/classvideo';

      final res = await dio.get(endpoint);
      if (res.data != null && res.data['status'] == true) {
        final list = res.data['classvideos'];
        if (list is List) {
          setState(() {
            _videos = list.map((e) => ClassVideoModel.fromJson(e)).toList();
          });
        }
      }
    } catch (e) {
      debugPrint('Fetch class videos error: $e');
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
    if (dateStr == null || dateStr.isEmpty) return '-';
    try {
      final dt = DateTime.parse(dateStr);
      return DateFormat('dd MMM yyyy, hh:mm a').format(dt);
    } catch (_) {
      return dateStr;
    }
  }

  void _showVideoDetailsModal(BuildContext context, ClassVideoModel item) {
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
                              child: const Icon(Icons.video_library_outlined,
                                  color: AppColors.primary, size: 20),
                            ),
                            const SizedBox(width: 10),
                            const Text(
                              'Class Video Details',
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
                            if (item.subject != null &&
                                item.subject!.isNotEmpty)
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: AppColors.primary.withOpacity(0.08),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  item.subject!,
                                  style: const TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                    color: AppColors.primary,
                                  ),
                                ),
                              ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        SelectableText(
                          '${item.subject ?? 'Subject'} - ${item.chapter ?? 'Chapter'}',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textPrimary,
                            height: 1.3,
                          ),
                        ),
                        const SizedBox(height: 6),
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
                        const SizedBox(height: 18),
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
                              if (item.usertype == 'INDIVIDUAL')
                                _buildInfoRow('Target Student',
                                    item.students?.toString() ?? 'N/A')
                              else ...[
                                _buildInfoRow('Gender', item.gender ?? 'All'),
                                _buildInfoRow(
                                    'Section',
                                    (item.section == null ||
                                            item.section!.isEmpty)
                                        ? 'All Sections'
                                        : item.section!),
                              ],
                            ],
                          ),
                        ),
                        const SizedBox(height: 18),
                        Container(
                          padding: const EdgeInsets.all(16),
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
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Row(
                                children: [
                                  Icon(Icons.videocam_outlined,
                                      size: 16, color: AppColors.fanta),
                                  SizedBox(width: 6),
                                  Text(
                                    'VIDEO DETAILS',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w900,
                                      color: AppColors.textPrimary,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 10),
                              _buildInfoRow('Subject', item.subject ?? '-'),
                              _buildInfoRow('Chapter', item.chapter ?? '-'),
                              _buildInfoRow(
                                  'Period',
                                  item.period != null
                                      ? 'Period ${item.period}'
                                      : '-'),
                              _buildInfoRow(
                                  'Video ID', item.videoId?.toString() ?? '-'),
                              _buildInfoRow(
                                  'Start At', _formatDateTime(item.startAt)),
                              _buildInfoRow(
                                  'End At', _formatDateTime(item.endAt)),
                            ],
                          ),
                        ),
                        const SizedBox(height: 18),
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
        title: const Text('Class Videos'),
      ),
      body: Column(
        children: [
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
                        _fetchVideos();
                      },
                    ),
                  );
                }).toList(),
              ),
            ),
          ),
          const Divider(height: 1, color: AppColors.borderLight),
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async {
                await Future.wait([
                  _fetchVideos(),
                  Provider.of<AnnouncementFilterProvider>(context,
                          listen: false)
                      .fetchMasterData(),
                ]);
              },
              color: AppColors.fanta,
              child: _loading && _videos.isEmpty
                  ? const Center(
                      child: CircularProgressIndicator(color: AppColors.fanta),
                    )
                  : _videos.isEmpty
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
                                    child: Icon(Icons.video_library_outlined,
                                        size: 36, color: AppColors.primary),
                                  ),
                                  SizedBox(height: 16),
                                  Text(
                                    'No Class Videos',
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      color: AppColors.textPrimary,
                                    ),
                                  ),
                                  SizedBox(height: 4),
                                  Text(
                                    'Tap + to add a new class video',
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
                          itemCount: _videos.length,
                          separatorBuilder: (_, __) =>
                              const SizedBox(height: 12),
                          itemBuilder: (context, index) {
                            final item = _videos[index];

                            return InkWell(
                              onTap: () =>
                                  _showVideoDetailsModal(context, item),
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
                                    Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Wrap(
                                          spacing: 6,
                                          runSpacing: 4,
                                          children: [
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
                                            if (item.subject != null &&
                                                item.subject!.isNotEmpty)
                                              Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        horizontal: 8,
                                                        vertical: 3),
                                                decoration: BoxDecoration(
                                                  color: AppColors.primary
                                                      .withOpacity(0.08),
                                                  borderRadius:
                                                      BorderRadius.circular(6),
                                                ),
                                                child: Text(
                                                  item.subject!,
                                                  style: const TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: AppColors.primary,
                                                  ),
                                                ),
                                              ),
                                          ],
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
                                    Text(
                                      '${item.subject ?? ''}: ${item.chapter ?? 'No Chapter'}',
                                      style: const TextStyle(
                                        fontSize: 15,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.textPrimary,
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Row(
                                      children: [
                                        if (item.period != null) ...[
                                          Text(
                                            'Period: ${item.period}',
                                            style: const TextStyle(
                                              fontSize: 12,
                                              color: AppColors.textSecondary,
                                            ),
                                          ),
                                          const Text(' • ',
                                              style: TextStyle(
                                                  color: AppColors.textMuted)),
                                        ],
                                        Text(
                                          'Video ID: ${item.videoId ?? '-'}',
                                          style: const TextStyle(
                                            fontSize: 12,
                                            color: AppColors.textSecondary,
                                          ),
                                        ),
                                      ],
                                    ),
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
                                          child: Row(
                                            children: [
                                              const Icon(Icons.schedule,
                                                  size: 14,
                                                  color: AppColors.textMuted),
                                              const SizedBox(width: 4),
                                              Expanded(
                                                child: Text(
                                                  '${_formatDateTime(item.startAt)} - ${_formatDateTime(item.endAt)}',
                                                  style: const TextStyle(
                                                      fontSize: 11,
                                                      color:
                                                          AppColors.textMuted),
                                                  maxLines: 1,
                                                  overflow:
                                                      TextOverflow.ellipsis,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        Row(
                                          children: [
                                            InkWell(
                                              onTap: () =>
                                                  _showVideoDetailsModal(
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
                                            InkWell(
                                              onTap: () async {
                                                final res =
                                                    await Navigator.push(
                                                  context,
                                                  MaterialPageRoute(
                                                    builder: (_) =>
                                                        EditClassVideoScreen(
                                                            videoId: item.id),
                                                  ),
                                                );
                                                if (res == true) _fetchVideos();
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
            MaterialPageRoute(builder: (_) => const CreateClassVideoScreen()),
          );
          if (res == true) _fetchVideos();
        },
        backgroundColor: AppColors.fanta,
        elevation: 4,
        shape: const CircleBorder(),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
    );
  }
}
