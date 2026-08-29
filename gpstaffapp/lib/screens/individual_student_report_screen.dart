import 'dart:async';
import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:path_provider/path_provider.dart';
import 'package:open_filex/open_filex.dart';
import '../api/api_client.dart';
import '../models/individual_student_report_model.dart';
import '../theme/app_theme.dart';

class IndividualStudentReportScreen extends StatefulWidget {
  const IndividualStudentReportScreen({super.key});

  @override
  State<IndividualStudentReportScreen> createState() => _IndividualStudentReportScreenState();
}

class _IndividualStudentReportScreenState extends State<IndividualStudentReportScreen> {
  final TextEditingController _searchController = TextEditingController();
  Timer? _debounce;

  List<Map<String, dynamic>> _students = [];
  Map<String, dynamic>? _selectedStudent;
  bool _searching = false;
  bool _loadingReport = false;
  bool _downloadingPdf = false;
  IndividualReportResponse? _reportData;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchStudents('');
  }

  @override
  void dispose() {
    _debounce?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchStudents(String query) async {
    setState(() => _searching = true);
    try {
      final dio = ApiClient().dio;
      final res = await dio.get('/admin/individual_student_report', queryParameters: {
        if (query.isNotEmpty) 'search': query,
        'limit': 30,
      });
      if (res.data != null && res.data['status'] == true) {
        final list = res.data['students'] as List? ?? [];
        setState(() => _students = list.map((e) => Map<String, dynamic>.from(e as Map)).toList());
      }
    } catch (e) {
      debugPrint('fetch students error $e');
    } finally {
      if (mounted) setState(() => _searching = false);
    }
  }

  void _onSearchChanged(String v) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      _fetchStudents(v.trim());
    });
  }

  Future<void> _viewReport() async {
    if (_selectedStudent == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select a student')));
      return;
    }
    setState(() {
      _loadingReport = true;
      _error = null;
      _reportData = null;
    });
    try {
      final dio = ApiClient().dio;
      final res = await dio.post('/admin/individual_student_report', data: {
        'student_id': _selectedStudent!['student_id'].toString(),
        'preview': '1',
      });
      if (res.data != null && res.data['status'] == true) {
        setState(() => _reportData = IndividualReportResponse.fromJson(res.data));
      } else {
        setState(() => _error = res.data?['message'] ?? 'Failed to load report');
      }
    } on DioException catch (e) {
      String msg = 'Failed to load report';
      if (e.response?.data != null && e.response?.data is Map) {
        msg = e.response?.data['message']?.toString() ?? msg;
      }
      setState(() => _error = msg);
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loadingReport = false);
    }
  }

  Future<void> _downloadPdf() async {
    if (_selectedStudent == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select a student')));
      return;
    }
    setState(() => _downloadingPdf = true);
    try {
      final dio = ApiClient().dio;
      final studentId = _selectedStudent!['student_id'].toString();
      final res = await dio.post(
        '/admin/individual_student_report',
        data: {'student_id': studentId, 'download': 'pdf'},
        options: Options(responseType: ResponseType.bytes),
      );
      if (res.data != null && res.data is List<int> || res.data is List) {
        final bytes = res.data is List<int> ? res.data as List<int> : (res.data as List).cast<int>();
        if (bytes.isEmpty) throw Exception('Empty PDF');
        final dir = await getTemporaryDirectory();
        final file = File('${dir.path}/${studentId}_IndividualStudentReport.pdf');
        await file.writeAsBytes(bytes, flush: true);
        final result = await OpenFilex.open(file.path);
        if (result.type != ResultType.done && mounted) {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Saved to ${file.path} - ${result.message}')));
        }
      } else if (res.data is String && (res.data as String).isNotEmpty) {
        // Fallback if dio returns string
        final bytes = (res.data as String).codeUnits;
        final dir = await getTemporaryDirectory();
        final file = File('${dir.path}/${studentId}_IndividualStudentReport.pdf');
        await file.writeAsBytes(bytes, flush: true);
        await OpenFilex.open(file.path);
      } else {
        // Try bytes from response
        final bytes = res.data;
        if (bytes is List<int>) {
          final dir = await getTemporaryDirectory();
          final file = File('${dir.path}/${studentId}_IndividualStudentReport.pdf');
          await file.writeAsBytes(bytes, flush: true);
          await OpenFilex.open(file.path);
        }
      }
    } on DioException catch (e) {
      String msg = 'PDF download failed';
      if (e.response?.data != null) {
        // If error is json, try parse
        try {
          if (e.response?.data is List<int>) {
            msg = String.fromCharCodes(e.response?.data as List<int>);
          }
        } catch (_) {}
      }
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: AppColors.error));
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.error));
    } finally {
      if (mounted) setState(() => _downloadingPdf = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Individual Student Report'),
        backgroundColor: AppColors.primary,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSearchCard(),
            const SizedBox(height: 16),
            if (_selectedStudent != null) _buildSelectedCard(),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: _loadingReport ? null : _viewReport,
                    icon: _loadingReport
                        ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Icon(Icons.visibility_outlined, size: 18),
                    label: const Text('View Report'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      padding: const EdgeInsets.symmetric(vertical: 13),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: _downloadingPdf ? null : _downloadPdf,
                    icon: _downloadingPdf
                        ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Icon(Icons.picture_as_pdf_outlined, size: 18),
                    label: const Text('Download PDF'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.fanta,
                      padding: const EdgeInsets.symmetric(vertical: 13),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            if (_error != null)
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: AppColors.error.withOpacity(0.1), borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.error.withOpacity(0.3))),
                child: Row(children: [const Icon(Icons.error_outline, color: AppColors.error, size: 18), const SizedBox(width: 8), Expanded(child: Text(_error!, style: const TextStyle(color: AppColors.error, fontSize: 12)))]),
              ),
            if (_loadingReport)
              const Padding(padding: EdgeInsets.all(32), child: Center(child: CircularProgressIndicator(color: AppColors.primary))),
            if (_reportData != null) _buildReportView(),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: AppColors.borderLight), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 8, offset: const Offset(0, 2))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('SELECT STUDENT', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.textSecondary, letterSpacing: 0.5)),
          const SizedBox(height: 10),
          TextField(
            controller: _searchController,
            onChanged: _onSearchChanged,
            decoration: InputDecoration(
              hintText: 'Search by ID or Name...',
              prefixIcon: const Icon(Icons.search, size: 18, color: AppColors.textSecondary),
              suffixIcon: _searching ? const Padding(padding: EdgeInsets.all(12), child: SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))) : (_searchController.text.isNotEmpty ? IconButton(icon: const Icon(Icons.clear, size: 16), onPressed: () { _searchController.clear(); _fetchStudents(''); }) : null),
              filled: true,
              fillColor: AppColors.background,
              contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: AppColors.border)),
            ),
          ),
          const SizedBox(height: 12),
          if (_students.isEmpty && !_searching)
            const Text('No students found', style: TextStyle(fontSize: 12, color: AppColors.textMuted))
          else
            Container(
              constraints: const BoxConstraints(maxHeight: 220),
              decoration: BoxDecoration(color: AppColors.background, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.borderLight)),
              child: ListView.separated(
                shrinkWrap: true,
                itemCount: _students.length,
                separatorBuilder: (_, __) => const Divider(height: 1, color: AppColors.borderLight),
                itemBuilder: (ctx, i) {
                  final s = _students[i];
                  final isSelected = _selectedStudent != null && _selectedStudent!['student_id'].toString() == s['student_id'].toString();
                  return InkWell(
                    onTap: () => setState(() => _selectedStudent = s),
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                      decoration: BoxDecoration(color: isSelected ? AppColors.primary.withOpacity(0.08) : Colors.transparent),
                      child: Row(
                        children: [
                          CircleAvatar(radius: 16, backgroundColor: isSelected ? AppColors.primary : AppColors.border, child: Text((s['student_name'] ?? 'S').toString().substring(0, 1).toUpperCase(), style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: isSelected ? Colors.white : AppColors.textSecondary))),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text(s['student_name']?.toString() ?? '', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.textPrimary), maxLines: 1, overflow: TextOverflow.ellipsis),
                              Text('ID: ${s['student_id']} • ${s['course'] ?? ''} ${s['section'] ?? ''}', style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                            ]),
                          ),
                          if (isSelected) const Icon(Icons.check_circle, color: AppColors.primary, size: 18),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSelectedCard() {
    final s = _selectedStudent!;
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(color: AppColors.primary.withOpacity(0.06), borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.primary.withOpacity(0.2))),
      child: Row(
        children: [
          const Icon(Icons.person, color: AppColors.primary, size: 18),
          const SizedBox(width: 8),
          Expanded(child: Text('${s['student_id']} - ${s['student_name']}', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.primary))),
          InkWell(onTap: () => setState(() { _selectedStudent = null; _reportData = null; }), child: const Icon(Icons.close, size: 16, color: AppColors.textMuted)),
        ],
      ),
    );
  }

  Widget _buildReportView() {
    final data = _reportData!;
    final student = data.student;
    final grouped = data.groupedByCategory;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Student header
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.borderLight)),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('STUDENT PARTICULARS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.textSecondary, letterSpacing: 0.5)),
              const SizedBox(height: 10),
              _infoRow('Name', student.studentName),
              _infoRow('ID', student.studentId),
              _infoRow('Course', student.course ?? '-'),
              _infoRow('Section', student.section ?? '-'),
              _infoRow('Branch', student.branchName ?? student.campus ?? '-'),
            ],
          ),
        ),
        const SizedBox(height: 16),
        // Consolidated marks per category
        ...grouped.entries.map((entry) {
          final category = entry.key;
          final rows = entry.value;
          // Determine visible subjects
          final showPhy = rows.any((r) => r.phyTot != null);
          final showChe = rows.any((r) => r.cheTot != null);
          final showBot = rows.any((r) => r.botTot != null);
          final showZoo = rows.any((r) => r.zooTot != null);
          final showBio = rows.any((r) => r.bioTot != null);
          return Container(
            margin: const EdgeInsets.only(bottom: 14),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.borderLight)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  decoration: const BoxDecoration(color: Color(0xFFF8FAFC), borderRadius: BorderRadius.only(topLeft: Radius.circular(16), topRight: Radius.circular(16))),
                  child: Text(category.toUpperCase(), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppColors.primary)),
                ),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: DataTable(
                    headingRowColor: MaterialStateProperty.all(const Color(0xFF334155)),
                    headingTextStyle: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.white),
                    dataTextStyle: const TextStyle(fontSize: 12, color: AppColors.textPrimary),
                    columnSpacing: 16,
                    columns: [
                      const DataColumn(label: Text('S.No')),
                      const DataColumn(label: Text('Date')),
                      const DataColumn(label: Text('Exam')),
                      if (showPhy) const DataColumn(label: Text('Phy')),
                      if (showChe) const DataColumn(label: Text('Che')),
                      if (showBot) const DataColumn(label: Text('Bot')),
                      if (showZoo) const DataColumn(label: Text('Zoo')),
                      if (showBio) const DataColumn(label: Text('Bio')),
                      const DataColumn(label: Text('Total')),
                    ],
                    rows: [
                      ...rows.asMap().entries.map((e) {
                        final idx = e.key;
                        final r = e.value;
                        return DataRow(cells: [
                          DataCell(Text('${idx + 1}.')),
                          DataCell(Text(r.formattedDate, style: const TextStyle(fontSize: 11))),
                          DataCell(SizedBox(width: 140, child: Text(r.subject, style: const TextStyle(fontSize: 11), maxLines: 1, overflow: TextOverflow.ellipsis))),
                          if (showPhy) DataCell(Text(r.phyTot?.toString() ?? 'AB', textAlign: TextAlign.center)),
                          if (showChe) DataCell(Text(r.cheTot?.toString() ?? 'AB', textAlign: TextAlign.center)),
                          if (showBot) DataCell(Text(r.botTot?.toString() ?? 'AB', textAlign: TextAlign.center)),
                          if (showZoo) DataCell(Text(r.zooTot?.toString() ?? 'AB', textAlign: TextAlign.center)),
                          if (showBio) DataCell(Text(r.bioTot?.toString() ?? 'AB', textAlign: TextAlign.center)),
                          DataCell(Text(r.netTot?.toString() ?? 'AB', style: const TextStyle(fontWeight: FontWeight.bold))),
                        ]);
                      }),
                      DataRow(
                        color: MaterialStateProperty.all(const Color(0xFFE2E8F0)),
                        cells: [
                          const DataCell(Text('Avg', style: TextStyle(fontWeight: FontWeight.bold))),
                          const DataCell(Text('')),
                          const DataCell(Text('')),
                          if (showPhy) DataCell(Text(data.average['phy']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.bold))),
                          if (showChe) DataCell(Text(data.average['che']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.bold))),
                          if (showBot) DataCell(Text(data.average['bot']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.bold))),
                          if (showZoo) DataCell(Text(data.average['zoo']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.bold))),
                          if (showBio) DataCell(Text(data.average['bio']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.bold))),
                          DataCell(Text(data.average['total']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.bold))),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        }),
      ],
    );
  }

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        children: [
          SizedBox(width: 90, child: Text(label, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary))),
          const Text(': ', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
          Expanded(child: Text(value, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPrimary))),
        ],
      ),
    );
  }
}
