import 'dart:io';
import 'package:open_filex/open_filex.dart';
import 'package:path_provider/path_provider.dart';
import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;
import '../models/chairman_report_model.dart';

class ChairmanReportPdfService {
  static const PdfColor _headerBgColor = PdfColor.fromInt(0xFFEEECE1);
  static const PdfColor _omrBgColor = PdfColor.fromInt(0xFFFDE088);
  static const PdfColor _totBgColor = PdfColor.fromInt(0xFFD9EDF7);
  static const PdfColor _netBgColor = PdfColor.fromInt(0xFFF9F9D8);
  static const PdfColor _headerRedColor = PdfColor.fromInt(0xFFDC1414);

  /// Generates and opens a PDF for the Chairman's Report on the client device
  static Future<File> generateAndOpenPdf({
    required ChairmanReportModel report,
    List<ChairmanStudentResultModel>? studentsToInclude,
  }) async {
    final students = studentsToInclude ?? report.results;
    final subjects = report.subjects;
    final subjectCount = subjects.length;

    // Determine page orientation matching dump_report.blade.php
    final pageFormat = subjectCount <= 1
        ? PdfPageFormat.a4
        : PdfPageFormat.a4.landscape;

    // Check if campus should be hidden (when all students belong to online/test batch)
    final hideCampus = students.isNotEmpty &&
        students.every((result) {
          final ct = (result.coachingType ?? '').trim().toUpperCase();
          return ct == 'ONLINE LIVE' ||
              ct == 'ONLINE RECORDED' ||
              ct == 'TEST BATCH';
        });

    final allOffline = report.allOffline;

    // Coaching types list for banner
    final coachingTypes = students
        .map((r) => (r.coachingType ?? '').trim().toUpperCase())
        .where((t) => t.isNotEmpty)
        .toSet()
        .join(' / ');

    final testName = report.testName ?? 'Exam';
    final totalMarks = report.totalMarks;

    final doc = pw.Document();

    // Fonts & Styles
    const baseFontSize = 6.8;
    const headerFontSize = 7.0;

    final baseStyle = pw.TextStyle(
      fontSize: baseFontSize,
      fontWeight: pw.FontWeight.bold,
      color: PdfColors.black,
    );

    final headerStyle = pw.TextStyle(
      fontSize: headerFontSize,
      fontWeight: pw.FontWeight.bold,
      color: PdfColors.black,
    );

    // Build Table Column Widths
    final Map<int, pw.TableColumnWidth> columnWidths = {};
    int colIndex = 0;

    // Fixed Columns
    columnWidths[colIndex++] = const pw.FixedColumnWidth(22); // S.NO
    columnWidths[colIndex++] = const pw.FixedColumnWidth(34); // SID
    columnWidths[colIndex++] = const pw.FixedColumnWidth(26); // MODE / BATCH
    columnWidths[colIndex++] = hideCampus
        ? const pw.FlexColumnWidth(3.0)
        : const pw.FlexColumnWidth(2.4); // STUDENT NAME
    columnWidths[colIndex++] = const pw.FixedColumnWidth(18); // SEX
    if (!hideCampus) {
      columnWidths[colIndex++] = const pw.FlexColumnWidth(1.4); // CAMPUS
    }
    columnWidths[colIndex++] = const pw.FixedColumnWidth(26); // SEC / C TYPE

    // Subject Columns (1 column per subject, internally divided into R/W/L/TOT)
    final double subjectColWidth = subjectCount > 1 ? 78.0 : 120.0;
    for (int i = 0; i < subjectCount; i++) {
      columnWidths[colIndex++] = pw.FixedColumnWidth(subjectColWidth);
    }

    // Total Column
    columnWidths[colIndex++] = const pw.FixedColumnWidth(34); // TOTAL

    // Build Header Rows
    final tableRows = <pw.TableRow>[
      _buildTableHeaderRow(
        subjects: subjects,
        totalMarks: totalMarks,
        allOffline: allOffline,
        hideCampus: hideCampus,
        headerStyle: headerStyle,
      ),
    ];

    // Build Student Rows
    for (int i = 0; i < students.length; i++) {
      final student = students[i];
      tableRows.add(
        _buildStudentRow(
          student: student,
          index: i + 1,
          subjects: subjects,
          allOffline: allOffline,
          hideCampus: hideCampus,
          baseStyle: baseStyle,
        ),
      );
    }

    doc.addPage(
      pw.MultiPage(
        pageFormat: pageFormat,
        margin: const pw.EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        header: (context) {
          return pw.Container(
            margin: const pw.EdgeInsets.only(bottom: 8),
            decoration: pw.BoxDecoration(
              border: pw.Border.all(color: PdfColors.black, width: 0.8),
            ),
            child: pw.Column(
              children: [
                // Institution Name
                pw.Container(
                  padding: const pw.EdgeInsets.symmetric(vertical: 4),
                  alignment: pw.Alignment.center,
                  child: pw.Text(
                    'GREEN PARK COACHING CENTRE, NAMAKKAL',
                    style: pw.TextStyle(
                      fontSize: 11,
                      fontWeight: pw.FontWeight.bold,
                      color: PdfColors.black,
                    ),
                  ),
                ),
                pw.Divider(color: PdfColors.black, height: 1, thickness: 0.6),
                // Exam & Coaching Title
                pw.Container(
                  padding: const pw.EdgeInsets.symmetric(vertical: 3),
                  alignment: pw.Alignment.center,
                  child: pw.Row(
                    mainAxisAlignment: pw.MainAxisAlignment.center,
                    children: [
                      if (coachingTypes.isNotEmpty) ...[
                        pw.Text(
                          '$coachingTypes ',
                          style: pw.TextStyle(
                            fontSize: 9,
                            fontWeight: pw.FontWeight.bold,
                            color: _headerRedColor,
                          ),
                        ),
                        pw.Text(
                          '- ',
                          style: pw.TextStyle(
                            fontSize: 9,
                            fontWeight: pw.FontWeight.bold,
                            color: PdfColors.black,
                          ),
                        ),
                      ],
                      pw.Text(
                        '$testName - MARKS',
                        style: pw.TextStyle(
                          fontSize: 9,
                          fontWeight: pw.FontWeight.bold,
                          color: PdfColors.black,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
        footer: (context) {
          return pw.Container(
            alignment: pw.Alignment.centerRight,
            margin: const pw.EdgeInsets.only(top: 6),
            child: pw.Text(
              'Page ${context.pageNumber} of ${context.pagesCount}',
              style: pw.TextStyle(
                fontSize: 7.5,
                fontWeight: pw.FontWeight.bold,
                color: PdfColors.grey700,
              ),
            ),
          );
        },
        build: (context) {
          return [
            pw.Table(
              border: pw.TableBorder.all(color: PdfColors.black, width: 0.4),
              columnWidths: columnWidths,
              defaultVerticalAlignment: pw.TableCellVerticalAlignment.middle,
              children: tableRows,
            ),
          ];
        },
      ),
    );

    // Save to device directory and open
    final bytes = await doc.save();
    final safeName = testName.replaceAll(RegExp(r'[\\/:*?"<>|]'), '_');
    final dir = await getTemporaryDirectory();
    final file = File('${dir.path}/${safeName}_ChairmanReport.pdf');
    await file.writeAsBytes(bytes, flush: true);

    await OpenFilex.open(file.path);
    return file;
  }

  static pw.TableRow _buildTableHeaderRow({
    required List<String> subjects,
    required int totalMarks,
    required bool allOffline,
    required bool hideCampus,
    required pw.TextStyle headerStyle,
  }) {
    final cells = <pw.Widget>[];

    pw.Widget headerCell(String text, {double verticalPadding = 6}) {
      return pw.Container(
        color: _headerBgColor,
        padding: pw.EdgeInsets.symmetric(horizontal: 2, vertical: verticalPadding),
        alignment: pw.Alignment.center,
        child: pw.Text(
          text,
          style: headerStyle,
          textAlign: pw.TextAlign.center,
        ),
      );
    }

    cells.add(headerCell('S.NO'));
    cells.add(headerCell('SID'));
    cells.add(headerCell(allOffline ? 'BATCH' : 'MODE'));
    cells.add(headerCell('STUDENT NAME'));
    cells.add(headerCell('SEX'));
    if (!hideCampus) {
      cells.add(headerCell('CAMPUS'));
    }
    cells.add(headerCell(allOffline ? 'SEC' : 'C TYPE'));

    // Subjects with subheaders
    for (final sub in subjects) {
      cells.add(
        pw.Container(
          color: _headerBgColor,
          child: pw.Column(
            children: [
              pw.Container(
                padding: const pw.EdgeInsets.symmetric(vertical: 2.5),
                alignment: pw.Alignment.center,
                child: pw.Text(
                  sub.toUpperCase(),
                  style: headerStyle,
                  maxLines: 1,
                  textAlign: pw.TextAlign.center,
                ),
              ),
              pw.Divider(color: PdfColors.black, height: 1, thickness: 0.4),
              pw.Row(
                children: [
                  _buildSubHeaderItem('R', headerStyle),
                  _buildSubHeaderItem('W', headerStyle),
                  _buildSubHeaderItem('L', headerStyle),
                  _buildSubHeaderItem('TOT', headerStyle, isLast: true),
                ],
              ),
            ],
          ),
        ),
      );
    }

    // Total column
    cells.add(
      pw.Container(
        color: _headerBgColor,
        padding: const pw.EdgeInsets.symmetric(horizontal: 2, vertical: 2),
        alignment: pw.Alignment.center,
        child: pw.Column(
          mainAxisAlignment: pw.MainAxisAlignment.center,
          children: [
            pw.Text('TOTAL', style: headerStyle, textAlign: pw.TextAlign.center),
            if (totalMarks > 0)
              pw.Text('$totalMarks', style: headerStyle, textAlign: pw.TextAlign.center),
          ],
        ),
      ),
    );

    return pw.TableRow(
      repeat: true,
      children: cells,
    );
  }

  static pw.Widget _buildSubHeaderItem(String label, pw.TextStyle style, {bool isLast = false}) {
    return pw.Expanded(
      child: pw.Container(
        alignment: pw.Alignment.center,
        padding: const pw.EdgeInsets.symmetric(vertical: 2),
        decoration: isLast
            ? null
            : const pw.BoxDecoration(
                border: pw.Border(
                  right: pw.BorderSide(color: PdfColors.black, width: 0.4),
                ),
              ),
        child: pw.Text(label, style: style, textAlign: pw.TextAlign.center),
      ),
    );
  }

  static pw.TableRow _buildStudentRow({
    required ChairmanStudentResultModel student,
    required int index,
    required List<String> subjects,
    required bool allOffline,
    required bool hideCampus,
    required pw.TextStyle baseStyle,
  }) {
    final cells = <pw.Widget>[];

    final modeOrBatch = allOffline
        ? (student.batch ?? '')
        : (student.stmode ?? '');
    final isOmr = modeOrBatch.trim().toUpperCase() == 'OMR';

    String genderDisplay = '';
    final g = (student.gender ?? '').trim().toUpperCase();
    if (g == 'FEMALE' || g == 'F') {
      genderDisplay = 'F';
    } else if (g == 'MALE' || g == 'M') {
      genderDisplay = 'M';
    } else {
      genderDisplay = g;
    }

    pw.Widget textCell(
      String text, {
      pw.Alignment alignment = pw.Alignment.center,
      PdfColor? bgColor,
      pw.EdgeInsets padding = const pw.EdgeInsets.symmetric(horizontal: 2, vertical: 2.5),
    }) {
      return pw.Container(
        color: bgColor,
        padding: padding,
        alignment: alignment,
        child: pw.Text(
          text,
          style: baseStyle,
          maxLines: 1,
          overflow: pw.TextOverflow.clip,
        ),
      );
    }

    cells.add(textCell('$index'));
    cells.add(textCell(student.studentId));
    cells.add(textCell(
      modeOrBatch,
      bgColor: isOmr ? _omrBgColor : null,
    ));
    cells.add(textCell(
      student.studentName,
      alignment: pw.Alignment.centerLeft,
      padding: const pw.EdgeInsets.symmetric(horizontal: 3, vertical: 2.5),
    ));
    cells.add(textCell(genderDisplay));
    if (!hideCampus) {
      cells.add(textCell(
        student.campus ?? '',
        alignment: pw.Alignment.centerLeft,
        padding: const pw.EdgeInsets.symmetric(horizontal: 3, vertical: 2.5),
      ));
    }
    cells.add(textCell(student.section ?? student.coachingType ?? ''));

    // Subject breakdown (R, W, L, TOT)
    for (final sub in subjects) {
      SubjectMarkModel? mark;
      // Match case-insensitive
      for (final entry in student.subjectMarks.entries) {
        if (entry.key.trim().toUpperCase() == sub.trim().toUpperCase()) {
          mark = entry.value;
          break;
        }
      }

      final r = mark?.right ?? 0;
      final w = mark?.wrong ?? 0;
      final l = mark?.left ?? 0;
      final tot = mark?.total ?? 0;

      cells.add(
        pw.Container(
          child: pw.Row(
            children: [
              _buildSubDataCell('$r', baseStyle),
              _buildSubDataCell('$w', baseStyle),
              _buildSubDataCell('$l', baseStyle),
              _buildSubDataCell(
                '$tot',
                baseStyle,
                bgColor: _totBgColor,
                isLast: true,
              ),
            ],
          ),
        ),
      );
    }

    // Total Net Mark
    cells.add(
      pw.Container(
        color: _netBgColor,
        padding: const pw.EdgeInsets.symmetric(horizontal: 2, vertical: 2.5),
        alignment: pw.Alignment.center,
        child: pw.Text(
          '${student.mark}',
          style: baseStyle,
          textAlign: pw.TextAlign.center,
        ),
      ),
    );

    return pw.TableRow(
      children: cells,
    );
  }

  static pw.Widget _buildSubDataCell(
    String text,
    pw.TextStyle style, {
    PdfColor? bgColor,
    bool isLast = false,
  }) {
    return pw.Expanded(
      child: pw.Container(
        color: bgColor,
        alignment: pw.Alignment.center,
        padding: const pw.EdgeInsets.symmetric(vertical: 2.5),
        decoration: isLast
            ? null
            : const pw.BoxDecoration(
                border: pw.Border(
                  right: pw.BorderSide(color: PdfColors.black, width: 0.4),
                ),
              ),
        child: pw.Text(
          text,
          style: style,
          textAlign: pw.TextAlign.center,
          maxLines: 1,
        ),
      ),
    );
  }
}
