import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import 'package:dio/dio.dart';
import '../api/api_client.dart';
import '../models/master_data_model.dart';
import '../providers/announcement_filter_provider.dart';
import '../theme/app_theme.dart';
import '../widgets/multi_select_chips.dart';
import '../widgets/student_selector.dart';

class CreateAchievementScreen extends StatefulWidget {
  const CreateAchievementScreen({super.key});
  @override
  State<CreateAchievementScreen> createState() =>
      _CreateAchievementScreenState();
}

class _CreateAchievementScreenState extends State<CreateAchievementScreen> {
  final TextEditingController _contentController = TextEditingController();
  final TextEditingController _linkController = TextEditingController();
  bool _isVideo = false;
  bool _isImage = false;
  bool _isPdf = false;
  bool _isLink = false;
  PlatformFile? _videoFile;
  final List<PlatformFile> _imageFiles = [];
  PlatformFile? _pdfFile;
  bool _submitting = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final filters =
          Provider.of<AnnouncementFilterProvider>(context, listen: false);
      filters.resetAll();
      filters.fetchMasterData();
    });
  }

  @override
  void dispose() {
    _contentController.dispose();
    _linkController.dispose();
    super.dispose();
  }

  Future<void> _pickVideo() async {
    try {
      final r = await FilePicker.platform.pickFiles(type: FileType.video);
      if (r != null && r.files.isNotEmpty) {
        final file = r.files.first;
        if (file.size > 40 * 1024 * 1024) {
          _showErrorDialog('Validation Error',
              'The uploaded video exceeds the maximum allowed size of 40MB.');
          return;
        }
        setState(() => _videoFile = file);
      }
    } catch (e) {
      debugPrint('Video picker error: $e');
    }
  }

  Future<void> _pickImages() async {
    try {
      final r = await FilePicker.platform
          .pickFiles(allowMultiple: true, type: FileType.image);
      if (r != null && r.files.isNotEmpty) {
        setState(() => _imageFiles.addAll(r.files));
      }
    } catch (e) {
      debugPrint('Image picker error: $e');
    }
  }

  Future<void> _pickPdf() async {
    try {
      final r = await FilePicker.platform
          .pickFiles(type: FileType.custom, allowedExtensions: ['pdf']);
      if (r != null && r.files.isNotEmpty) {
        setState(() => _pdfFile = r.files.first);
      }
    } catch (e) {
      debugPrint('PDF picker error: $e');
    }
  }

  String _formatFileSize(int b) {
    if (b < 1024) return '$b B';
    if (b < 1024 * 1024) return '${(b / 1024).toStringAsFixed(1)} KB';
    return '${(b / (1024 * 1024)).toStringAsFixed(1)} MB';
  }

  Future<void> _handleSubmit() async {
    final filters =
        Provider.of<AnnouncementFilterProvider>(context, listen: false);
    if (filters.course.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select a course.');
      return;
    }
    if (filters.branches.isEmpty) {
      _showErrorDialog(
          'Validation Error', 'Please select at least one branch.');
      return;
    }
    if (filters.usertype == 'INDIVIDUAL' && filters.student.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select a target student.');
      return;
    }
    if (_contentController.text.trim().isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter content.');
      return;
    }
    // At least one file category must be selected
    if (!_isVideo && !_isImage && !_isPdf && !_isLink) {
      _showErrorDialog(
          'Validation Error', 'Please select at least one file category.');
      return;
    }
    if (_isVideo && _videoFile == null) {
      _showErrorDialog('Validation Error', 'Please select a video file.');
      return;
    }
    if (_isVideo && _videoFile != null && _videoFile!.size > 40 * 1024 * 1024) {
      _showErrorDialog('Validation Error',
          'The uploaded video exceeds the maximum allowed size of 40MB.');
      return;
    }
    if (_isImage && _imageFiles.isEmpty) {
      _showErrorDialog('Validation Error', 'Please select at least one image.');
      return;
    }
    if (_isPdf && _pdfFile == null) {
      _showErrorDialog('Validation Error', 'Please select a PDF file.');
      return;
    }
    if (_isLink && _linkController.text.trim().isEmpty) {
      _showErrorDialog('Validation Error', 'Please enter a link.');
      return;
    }

    setState(() => _submitting = true);
    try {
      final formData = FormData();
      formData.fields.add(MapEntry('academic_year', filters.academicYear));
      formData.fields.add(MapEntry('usertype', filters.usertype));
      formData.fields.add(MapEntry('course', filters.course));
      for (var b in filters.branches) {
        formData.fields.add(MapEntry('branch[]', b.toString()));
      }
      for (var c in filters.coachingTypes) {
        formData.fields.add(MapEntry('coaching_type[]', c));
      }
      for (var cat in filters.category) {
        formData.fields.add(MapEntry('category[]', cat));
      }
      for (var bat in filters.batch) {
        formData.fields.add(MapEntry('batch[]', bat));
      }
      formData.fields.add(MapEntry('gender', filters.gender));
      if (filters.usertype == 'INDIVIDUAL') {
        formData.fields.add(MapEntry('students', filters.student));
      } else {
        formData.fields.add(MapEntry('section', filters.section));
      }
      formData.fields.add(MapEntry('content', _contentController.text.trim()));

      // filecategory
      if (_isVideo) {
        formData.fields.add(const MapEntry('filecategory[]', 'Video'));
      }
      if (_isImage) {
        formData.fields.add(const MapEntry('filecategory[]', 'Image'));
      }
      if (_isPdf) {
        formData.fields.add(const MapEntry('filecategory[]', 'pdf'));
      }
      if (_isLink) {
        formData.fields.add(const MapEntry('filecategory[]', 'Link'));
      }

      // Files
      if (_isVideo && _videoFile != null) {
        if (!kIsWeb && _videoFile!.path != null) {
          formData.files.add(MapEntry(
              'video',
              await MultipartFile.fromFile(_videoFile!.path!,
                  filename: _videoFile!.name)));
        } else if (_videoFile!.bytes != null) {
          formData.files.add(MapEntry(
              'video',
              MultipartFile.fromBytes(_videoFile!.bytes!,
                  filename: _videoFile!.name)));
        }
      }
      if (_isImage) {
        for (var img in _imageFiles) {
          if (!kIsWeb && img.path != null) {
            formData.files.add(MapEntry('images[]',
                await MultipartFile.fromFile(img.path!, filename: img.name)));
          } else if (img.bytes != null) {
            formData.files.add(MapEntry('images[]',
                MultipartFile.fromBytes(img.bytes!, filename: img.name)));
          }
        }
      }
      if (_isPdf && _pdfFile != null) {
        if (!kIsWeb && _pdfFile!.path != null) {
          formData.files.add(MapEntry(
              'pdf',
              await MultipartFile.fromFile(_pdfFile!.path!,
                  filename: _pdfFile!.name)));
        } else if (_pdfFile!.bytes != null) {
          formData.files.add(MapEntry(
              'pdf',
              MultipartFile.fromBytes(_pdfFile!.bytes!,
                  filename: _pdfFile!.name)));
        }
      }
      if (_isLink) {
        formData.fields.add(MapEntry('link', _linkController.text.trim()));
      }

      final res = await ApiClient().dio.post('/admin/achievement',
          data: formData, options: Options(contentType: 'multipart/form-data'));
      if (mounted) {
        if (res.data != null && res.data['status'] == true) {
          showDialog(
              context: context,
              barrierDismissible: false,
              builder: (ctx) => AlertDialog(
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20)),
                      title: const Text('Success',
                          style: TextStyle(
                              fontWeight: FontWeight.bold,
                              color: AppColors.primary)),
                      content: const Text(
                          'MBBS/BDS Counselling information added successfully!'),
                      actions: [
                        TextButton(
                            onPressed: () {
                              Navigator.pop(ctx);
                              Navigator.pop(context, true);
                            },
                            child: const Text('OK',
                                style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: AppColors.primary)))
                      ]));
        } else {
          _showErrorDialog(
              'Error',
              (res.data is Map ? res.data['message'] : null)?.toString() ??
                  'Creation failed');
        }
      }
    } on DioException catch (e) {
      String m = 'Submission failed';
      if (e.response?.data != null && e.response?.data is Map) {
        m = e.response?.data['message']?.toString() ?? m;
      }
      _showErrorDialog('Error', m);
    } catch (e) {
      _showErrorDialog('Error', e.toString());
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  void _showErrorDialog(String t, String m) {
    if (!mounted) return;
    showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20)),
                title: Text(t,
                    style: const TextStyle(fontWeight: FontWeight.bold)),
                content: Text(m),
                actions: [
                  TextButton(
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text('OK',
                          style: TextStyle(
                              fontWeight: FontWeight.bold,
                              color: AppColors.primary)))
                ]));
  }

  @override
  Widget build(BuildContext context) {
    final filters = Provider.of<AnnouncementFilterProvider>(context);
    if (filters.loading) {
      return Scaffold(
          backgroundColor: AppColors.background,
          appBar: AppBar(title: const Text('Add MBBS/BDS Counselling')),
          body: const Center(
              child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                CircularProgressIndicator(color: AppColors.fanta),
                SizedBox(height: 12),
                Text('Loading master data...',
                    style:
                        TextStyle(fontSize: 12, color: AppColors.textSecondary))
              ])));
    }
    return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: const Text('Add MBBS/BDS Counselling')),
        body: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
            child: Column(children: [
              Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: AppColors.borderLight),
                      boxShadow: [
                        BoxShadow(
                            color: Colors.black.withOpacity(0.02),
                            blurRadius: 8,
                            offset: const Offset(0, 2))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32,
                              height: 32,
                              decoration: BoxDecoration(
                                  color: AppColors.primary.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.tune,
                                  size: 16, color: AppColors.primary)),
                          const SizedBox(width: 10),
                          const Text('TARGET AUDIENCE',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w900,
                                  color: AppColors.textPrimary,
                                  letterSpacing: 0.8))
                        ]),
                        const SizedBox(height: 20),
                        const Text('ACADEMIC YEAR',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 14),
                            decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: AppColors.border)),
                            child: Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                      filters.academicYear.isNotEmpty
                                          ? filters.academicYear
                                          : 'Active Year',
                                      style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w600,
                                          color: AppColors.textPrimary)),
                                  const Icon(Icons.lock_outline,
                                      size: 18, color: AppColors.textMuted)
                                ])),
                        const SizedBox(height: 16),
                        const Text('USER TYPE *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        Row(children: [
                          Expanded(
                              child: InkWell(
                                  onTap: () => filters.setUsertype('GROUP'),
                                  borderRadius: BorderRadius.circular(16),
                                  child: Container(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 12),
                                      decoration: BoxDecoration(
                                          color: filters.usertype == 'GROUP'
                                              ? AppColors.primary
                                              : const Color(0xFFF8FAFC),
                                          borderRadius:
                                              BorderRadius.circular(16),
                                          border: Border.all(
                                              color: filters.usertype == 'GROUP'
                                                  ? AppColors.primary
                                                  : AppColors.border)),
                                      child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.people,
                                                size: 16,
                                                color: filters.usertype ==
                                                        'GROUP'
                                                    ? Colors.white
                                                    : AppColors.textSecondary),
                                            const SizedBox(width: 8),
                                            Text('Group Broadcast',
                                                style: TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: filters.usertype ==
                                                            'GROUP'
                                                        ? Colors.white
                                                        : AppColors
                                                            .textSecondary))
                                          ])))),
                          const SizedBox(width: 10),
                          Expanded(
                              child: InkWell(
                                  onTap: () =>
                                      filters.setUsertype('INDIVIDUAL'),
                                  borderRadius: BorderRadius.circular(16),
                                  child: Container(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 12),
                                      decoration: BoxDecoration(
                                          color:
                                              filters.usertype == 'INDIVIDUAL'
                                                  ? AppColors.primary
                                                  : const Color(0xFFF8FAFC),
                                          borderRadius:
                                              BorderRadius.circular(16),
                                          border: Border.all(
                                              color: filters.usertype ==
                                                      'INDIVIDUAL'
                                                  ? AppColors.primary
                                                  : AppColors.border)),
                                      child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.person,
                                                size: 16,
                                                color: filters.usertype ==
                                                        'INDIVIDUAL'
                                                    ? Colors.white
                                                    : AppColors.textSecondary),
                                            const SizedBox(width: 8),
                                            Text('Individual Student',
                                                style: TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: filters.usertype ==
                                                            'INDIVIDUAL'
                                                        ? Colors.white
                                                        : AppColors
                                                            .textSecondary))
                                          ]))))
                        ]),
                        const SizedBox(height: 16),
                        const Text('COURSE *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            physics: const BouncingScrollPhysics(),
                            child: Row(
                                children:
                                    (filters.master?.courses ?? []).map((c) {
                              final active = filters.course == c;
                              return Padding(
                                  padding: const EdgeInsets.only(right: 8),
                                  child: ChoiceChip(
                                      label: Text(c),
                                      labelStyle: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: active
                                              ? Colors.white
                                              : AppColors.textPrimary),
                                      selected: active,
                                      selectedColor: AppColors.primary,
                                      backgroundColor: const Color(0xFFF8FAFC),
                                      side: BorderSide(
                                          color: active
                                              ? AppColors.primary
                                              : AppColors.border),
                                      shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(20)),
                                      onSelected: (_) => filters.setCourse(c)));
                            }).toList())),
                        const SizedBox(height: 16),
                        MultiSelectChips<BranchItem>(
                            label: 'Branches *',
                            options: filters.availableBranches,
                            selected: filters.branches,
                            labelBuilder: (b) => b.name,
                            valueBuilder: (b) => b.id,
                            onToggle: (val) => filters.toggleBranch(val)),
                        MultiSelectChips<String>(
                            label: 'Coaching Type',
                            options: filters.availableCoachingTypes,
                            selected: filters.coachingTypes,
                            onToggle: (val) => filters.toggleCoachingType(val)),
                        if (filters.showCategory)
                          MultiSelectChips<String>(
                              label: 'H/D (Category)',
                              options: filters.master?.hostels ?? [],
                              selected: filters.category,
                              onToggle: (val) => filters.toggleCategory(val)),
                        if (filters.showBatch)
                          MultiSelectChips<String>(
                              label: 'Batch',
                              options: filters.master?.batches ?? [],
                              selected: filters.batch,
                              onToggle: (val) => filters.toggleBatch(val)),
                        if (filters.showGender) ...[
                          const Text('GENDER',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          Row(
                              children: ['All', 'MALE', 'FEMALE'].map((g) {
                            final active = filters.gender == g;
                            return Padding(
                                padding: const EdgeInsets.only(right: 8),
                                child: ChoiceChip(
                                    label: Text(g == 'All' ? 'All Genders' : g),
                                    labelStyle: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: active
                                            ? Colors.white
                                            : AppColors.textPrimary),
                                    selected: active,
                                    selectedColor: AppColors.primary,
                                    backgroundColor: const Color(0xFFF8FAFC),
                                    side: BorderSide(
                                        color: active
                                            ? AppColors.primary
                                            : AppColors.border),
                                    shape: RoundedRectangleBorder(
                                        borderRadius:
                                            BorderRadius.circular(20)),
                                    onSelected: (_) => filters.setGender(g)));
                          }).toList()),
                          const SizedBox(height: 16)
                        ],
                        if (filters.usertype == 'GROUP' &&
                            filters.showSection) ...[
                          const Text('SECTION',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          SingleChildScrollView(
                              scrollDirection: Axis.horizontal,
                              physics: const BouncingScrollPhysics(),
                              child: Row(children: [
                                Padding(
                                    padding: const EdgeInsets.only(right: 8),
                                    child: ChoiceChip(
                                        label: const Text('All Sections'),
                                        labelStyle: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: filters.section.isEmpty
                                                ? Colors.white
                                                : AppColors.textPrimary),
                                        selected: filters.section.isEmpty,
                                        selectedColor: AppColors.primary,
                                        backgroundColor:
                                            const Color(0xFFF8FAFC),
                                        side: BorderSide(
                                            color: filters.section.isEmpty
                                                ? AppColors.primary
                                                : AppColors.border),
                                        shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(20)),
                                        onSelected: (_) =>
                                            filters.setSection(''))),
                                ...filters.sectionOptions.map((sec) => Padding(
                                    padding: const EdgeInsets.only(right: 8),
                                    child: ChoiceChip(
                                        label: Text(sec),
                                        labelStyle: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: filters.section == sec
                                                ? Colors.white
                                                : AppColors.textPrimary),
                                        selected: filters.section == sec,
                                        selectedColor: AppColors.primary,
                                        backgroundColor:
                                            const Color(0xFFF8FAFC),
                                        side: BorderSide(
                                            color: filters.section == sec
                                                ? AppColors.primary
                                                : AppColors.border),
                                        shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(20)),
                                        onSelected: (_) =>
                                            filters.setSection(sec))))
                              ]))
                        ],
                        if (filters.usertype == 'GROUP' && filters.showSection)
                          const SizedBox(height: 16),
                        if (filters.showStudent)
                          StudentSelector(
                              selectedStudentId: filters.student,
                              onSelectStudent: (id) => filters.setStudent(id),
                              studentOptions: filters.studentOptions,
                              loading: filters.studentLoading,
                              searchValue: filters.studentSearch,
                              onSearchChanged: (s) =>
                                  filters.setStudentSearch(s),
                              onSearch: (s) => filters.fetchStudents(s),
                              sectionOptions: filters.sectionOptions,
                              selectedSection: filters.section,
                              onSelectSection: (sec) =>
                                  filters.setSection(sec)),
                      ])),
              const SizedBox(height: 16),
              Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: AppColors.borderLight),
                      boxShadow: [
                        BoxShadow(
                            color: Colors.black.withOpacity(0.02),
                            blurRadius: 8,
                            offset: const Offset(0, 2))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32,
                              height: 32,
                              decoration: BoxDecoration(
                                  color: AppColors.fanta.withOpacity(0.12),
                                  borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.campaign_outlined,
                                  size: 20, color: AppColors.fanta)),
                          const SizedBox(width: 10),
                          const Text('MBBS/BDS COUNSELLING DETAILS',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w900,
                                  color: AppColors.textPrimary,
                                  letterSpacing: 0.8))
                        ]),
                        const SizedBox(height: 20),
                        const Text('CONTENT *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        TextField(
                            controller: _contentController,
                            maxLines: 4,
                            style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary),
                            decoration: InputDecoration(
                                hintText: 'Enter content / description',
                                filled: true,
                                fillColor: const Color(0xFFF8FAFC),
                                border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16),
                                    borderSide: const BorderSide(
                                        color: AppColors.border)),
                                enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16),
                                    borderSide: const BorderSide(
                                        color: AppColors.border)),
                                focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16),
                                    borderSide: const BorderSide(
                                        color: AppColors.primary, width: 1.5)))),
                        const SizedBox(height: 16),
                        const Text('FILE CATEGORIES *',
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textSecondary)),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 10,
                          children: [
                            ChoiceChip(
                              label: const Text('Video'),
                              labelStyle: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: _isVideo
                                      ? Colors.white
                                      : AppColors.textPrimary),
                              selected: _isVideo,
                              selectedColor: AppColors.primary,
                              backgroundColor: const Color(0xFFF8FAFC),
                              side: BorderSide(
                                  color: _isVideo
                                      ? AppColors.primary
                                      : AppColors.border),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20)),
                              onSelected: (v) => setState(() => _isVideo = v),
                            ),
                            ChoiceChip(
                              label: const Text('Image'),
                              labelStyle: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: _isImage
                                      ? Colors.white
                                      : AppColors.textPrimary),
                              selected: _isImage,
                              selectedColor: AppColors.primary,
                              backgroundColor: const Color(0xFFF8FAFC),
                              side: BorderSide(
                                  color: _isImage
                                      ? AppColors.primary
                                      : AppColors.border),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20)),
                              onSelected: (v) => setState(() => _isImage = v),
                            ),
                            ChoiceChip(
                              label: const Text('PDF'),
                              labelStyle: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: _isPdf
                                      ? Colors.white
                                      : AppColors.textPrimary),
                              selected: _isPdf,
                              selectedColor: AppColors.primary,
                              backgroundColor: const Color(0xFFF8FAFC),
                              side: BorderSide(
                                  color: _isPdf
                                      ? AppColors.primary
                                      : AppColors.border),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20)),
                              onSelected: (v) => setState(() => _isPdf = v),
                            ),
                            ChoiceChip(
                              label: const Text('Link'),
                              labelStyle: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: _isLink
                                      ? Colors.white
                                      : AppColors.textPrimary),
                              selected: _isLink,
                              selectedColor: AppColors.primary,
                              backgroundColor: const Color(0xFFF8FAFC),
                              side: BorderSide(
                                  color: _isLink
                                      ? AppColors.primary
                                      : AppColors.border),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20)),
                              onSelected: (v) => setState(() => _isLink = v),
                            ),
                          ],
                        ),
                        if (_isVideo) ...[
                          const SizedBox(height: 16),
                          const Text('VIDEO (max 40MB) *',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 12),
                            decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: AppColors.border)),
                            child: Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(
                                    child: InkWell(
                                      onTap: _pickVideo,
                                      child: Row(
                                        children: [
                                          const Icon(Icons.video_file,
                                              color: AppColors.primary,
                                              size: 22),
                                          const SizedBox(width: 10),
                                          Expanded(
                                            child: Text(
                                                _videoFile != null
                                                    ? '${_videoFile!.name} (${_formatFileSize(_videoFile!.size)})'
                                                    : 'Select video file',
                                                style: TextStyle(
                                                    fontSize: 13,
                                                    fontWeight:
                                                        _videoFile != null
                                                            ? FontWeight.bold
                                                            : FontWeight.normal,
                                                    color: _videoFile != null
                                                        ? AppColors.textPrimary
                                                        : AppColors.textMuted),
                                                maxLines: 1,
                                                overflow:
                                                    TextOverflow.ellipsis),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  if (_videoFile != null)
                                    IconButton(
                                      icon: const Icon(Icons.close,
                                          color: AppColors.error, size: 18),
                                      onPressed: () =>
                                          setState(() => _videoFile = null),
                                    )
                                  else
                                    InkWell(
                                      onTap: _pickVideo,
                                      child: const Text('Browse',
                                          style: TextStyle(
                                              fontSize: 12,
                                              fontWeight: FontWeight.bold,
                                              color: AppColors.primary)),
                                    ),
                                ]),
                          ),
                        ],
                        if (_isImage) ...[
                          const SizedBox(height: 16),
                          const Text('IMAGES *',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          if (_imageFiles.isNotEmpty)
                            ListView.separated(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                itemCount: _imageFiles.length,
                                separatorBuilder: (_, __) =>
                                    const SizedBox(height: 6),
                                itemBuilder: (context, i) {
                                  final img = _imageFiles[i];
                                  return Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 12, vertical: 10),
                                      decoration: BoxDecoration(
                                          color: const Color(0xFFF8FAFC),
                                          borderRadius:
                                              BorderRadius.circular(14),
                                          border: Border.all(
                                              color: AppColors.border)),
                                      child: Row(children: [
                                        const Icon(Icons.image,
                                            color: AppColors.primary, size: 20),
                                        const SizedBox(width: 10),
                                        Expanded(
                                            child: Text(
                                                '${img.name} (${_formatFileSize(img.size)})',
                                                style: const TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color:
                                                        AppColors.textPrimary),
                                                maxLines: 1,
                                                overflow:
                                                    TextOverflow.ellipsis)),
                                        IconButton(
                                            icon: const Icon(Icons.close,
                                                color: AppColors.error,
                                                size: 18),
                                            onPressed: () => setState(
                                                () => _imageFiles.removeAt(i)))
                                      ]));
                                }),
                          const SizedBox(height: 8),
                          InkWell(
                              onTap: _pickImages,
                              borderRadius: BorderRadius.circular(16),
                              child: Container(
                                  width: double.infinity,
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 12),
                                  decoration: BoxDecoration(
                                      color:
                                          AppColors.primary.withOpacity(0.05),
                                      borderRadius: BorderRadius.circular(16),
                                      border: Border.all(
                                          color: AppColors.primary
                                              .withOpacity(0.4))),
                                  child: const Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Icon(Icons.add_photo_alternate_outlined,
                                            color: AppColors.primary, size: 20),
                                        SizedBox(width: 8),
                                        Text('Select Images',
                                            style: TextStyle(
                                                fontSize: 13,
                                                fontWeight: FontWeight.bold,
                                                color: AppColors.primary))
                                      ]))),
                        ],
                        if (_isPdf) ...[
                          const SizedBox(height: 16),
                          const Text('PDF *',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 12),
                            decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: AppColors.border)),
                            child: Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(
                                    child: InkWell(
                                      onTap: _pickPdf,
                                      child: Row(
                                        children: [
                                          const Icon(Icons.picture_as_pdf,
                                              color: AppColors.primary,
                                              size: 22),
                                          const SizedBox(width: 10),
                                          Expanded(
                                            child: Text(
                                                _pdfFile != null
                                                    ? '${_pdfFile!.name} (${_formatFileSize(_pdfFile!.size)})'
                                                    : 'Select PDF file',
                                                style: TextStyle(
                                                    fontSize: 13,
                                                    fontWeight:
                                                        _pdfFile != null
                                                            ? FontWeight.bold
                                                            : FontWeight.normal,
                                                    color: _pdfFile != null
                                                        ? AppColors.textPrimary
                                                        : AppColors.textMuted),
                                                maxLines: 1,
                                                overflow:
                                                    TextOverflow.ellipsis),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  if (_pdfFile != null)
                                    IconButton(
                                      icon: const Icon(Icons.close,
                                          color: AppColors.error, size: 18),
                                      onPressed: () =>
                                          setState(() => _pdfFile = null),
                                    )
                                  else
                                    InkWell(
                                      onTap: _pickPdf,
                                      child: const Text('Browse',
                                          style: TextStyle(
                                              fontSize: 12,
                                              fontWeight: FontWeight.bold,
                                              color: AppColors.primary)),
                                    ),
                                ]),
                          ),
                        ],
                        if (_isLink) ...[
                          const SizedBox(height: 16),
                          const Text('LINK *',
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: AppColors.textSecondary)),
                          const SizedBox(height: 8),
                          TextField(
                              controller: _linkController,
                              keyboardType: TextInputType.url,
                              style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textPrimary),
                              decoration: InputDecoration(
                                  hintText: 'https://example.com',
                                  filled: true,
                                  fillColor: const Color(0xFFF8FAFC),
                                  border: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(16),
                                      borderSide: const BorderSide(
                                          color: AppColors.border)),
                                  enabledBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(16),
                                      borderSide: const BorderSide(
                                          color: AppColors.border)),
                                  focusedBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(16),
                                      borderSide: const BorderSide(
                                          color: AppColors.primary,
                                          width: 1.5)))),
                        ],
                      ])),
              const SizedBox(height: 24),
              SizedBox(
                  width: double.infinity,
                  height: 54,
                  child: ElevatedButton(
                      onPressed: _submitting ? null : _handleSubmit,
                      style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.fanta,
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(18)),
                          elevation: 4,
                          shadowColor: AppColors.fanta.withOpacity(0.4)),
                      child: _submitting
                          ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                  color: Colors.white, strokeWidth: 2.5))
                          : const Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                  Icon(Icons.campaign,
                                      color: Colors.white, size: 22),
                                  SizedBox(width: 8),
                                  Text('Publish MBBS/BDS Counselling',
                                      style: TextStyle(
                                          fontSize: 15,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.white))
                                ])))
            ])));
  }
}
