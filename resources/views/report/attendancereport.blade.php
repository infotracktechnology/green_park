@extends('layouts.app')
@section('title', 'Attendance Daily Report')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <form method="get" action="{{ route('report.attendance') }}">
              <div class="card-header">
                <h4>Daily Attendance Report</h4>
              </div>
              <div class="card-body">

                <div class="row mb-3">
                  <div class="col-lg-2">
                    <label for="branch">Branch</label>
                    <select name="branch_id" id="branch" class="form-control form-control-sm" required onchange="this.form.submit()">
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == request('branch_id'))>
                        {{ $branch->name }}
                      </option>
                      @endforeach
                    </select>
                  </div>

                 <div class="col-lg-2">
                    <label for="course">Course</label>
                    <select name="course" id="course" class="form-control form-control-sm">
                        <option value="">All Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->course }}"
                                @selected(request('course') == $course->course)>
                                {{ $course->course }}
                            </option>
                        @endforeach
                    </select>
                </div>

                  <div class="col-lg-2">
                      <label>Section</label>
                      <select name="section[]" id="section" class="form-control form-control-sm select2" multiple>
                          <option value="">All Section</option>
                          @foreach($sections as $section)
                              <option value="{{ $section->section }}"
                                  @selected(in_array($section->section,(array) request('section', [])))>
                                  {{ $section->section }}
                              </option>
                          @endforeach
                      </select>
                  </div>

                  <div class="col-lg-2">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date')  ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                  </div>

                  <div class="col-lg-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </div>
                </div>
                @if(count($attendances))
                <div class="table-responsive">
                  <table class="table table-striped" id="attendance-table">
                    <thead>
                        <tr>
                            <th rowspan="2">S.NO</th>
                            <th rowspan="2">Section Name</th>
                            <th rowspan="2">Boys</th>
                            <th rowspan="2">Girls</th>
                            <th rowspan="2">Total</th>

                            <th colspan="2" class="text-center">Morning</th>
                            <th colspan="2" class="text-center">Afternoon</th>
                            {{-- <th colspan="2" class="text-center">Overall</th> --}}
                        </tr>

                        <tr>
                            <th>Present</th>
                            <th>Absent</th>

                            <th>Present</th>
                            <th>Absent</th>

                            {{-- <th>Present</th>
                            <th>Absent</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                          $boys_total = $girls_total = $total = 0;
                          $morning_present = $morning_absent = 0;
                          $afternoon_present = $afternoon_absent = 0;
                          $present = $absent = 0;
                      ?>
                      @foreach($attendances as $row)
                          <?php
                              $boys_total += $row['boys'];
                              $girls_total += $row['girls'];
                              $total += $row['total'];
                              $morning_present += $row['morning_present'];
                              $morning_absent += $row['morning_absent'];
                              $afternoon_present += $row['afternoon_present'];
                              $afternoon_absent += $row['afternoon_absent'];
                              $present += $row['present'];
                              $absent += $row['absent'];
                          ?>

                          <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $row['section'] }}</td>
                              <td>{{ $row['boys'] }}</td>
                              <td>{{ $row['girls'] }}</td>
                              <td>{{ $row['total'] }}</td>
                              <td><a href="javascript:void(0)" class="badge badge-success text-white view-students"data-type="Morning Present"data-students='@json($row["morning_present_students"])'> {{ $row['morning_present'] }} </a> </td>
                              <td><a href="javascript:void(0)" class="badge badge-danger text-white view-students"data-type="Morning Absent"data-students='@json($row["morning_absent_students"])'> {{ $row['morning_absent'] }} </a> </td>
                              <td><a href="javascript:void(0)" class="badge badge-success text-white view-students"data-type="Afternoon Present"data-students='@json($row["afternoon_present_students"])'> {{ $row['afternoon_present'] }} </a> </td>
                              <td><a href="javascript:void(0)" class="badge badge-danger text-white view-students"data-type="Afternoon Absent"data-students='@json($row["afternoon_absent_students"])'> {{ $row['afternoon_absent'] }} </a> </td>
                              {{-- <td><a href="javascript:void(0)" class="badge badge-danger text-white view-students"data-type="Absent"data-students='@json($row["absent_students"])'> {{ $row['absent'] }} </a> </td>
                              <td><a href="javascript:void(0)" class="badge badge-success text-white view-students"data-type="Present"data-students='@json($row["present_students"])'> {{ $row['present'] }} </a> </td> --}}
                          </tr>

                      @endforeach
                  </tbody>
                    <tfoot>
                      <tr>
                          <td colspan="2" class="font-16 fw-bold text-center">Total</td>
                          <td>{{ $boys_total }}</td>
                          <td>{{ $girls_total }}</td>
                          <td>{{ $total }}</td>
                          <td>{{ $morning_present }}</td>
                          <td>{{ $morning_absent }}</td>
                          <td>{{ $afternoon_present }}</td>
                          <td>{{ $afternoon_absent }}</td>
                          {{-- <td>{{ $present }}</td>
                          <td>{{ $absent }}</td> --}}
                      </tr>
                  </tfoot>
                  </table>
                </div>
                @endif
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<div class="modal fade" id="studentsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Students List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="studentListContainer" class="list-group row" style="flex-direction: row; flex-wrap: wrap;"></ul>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.print.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
  $(document).ready(function () {
    var table = $("#attendance-table").DataTable({
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          footer: true,
        },
        {
           extend: "pdfHtml5",
            className: 'btn btn-danger btn-sm ml-1',
            action: function (e, dt, node, config) {
                exportToPDF(dt);
            }
        }
      ],
      pageLength: 25,
    });
  });

function exportToPDF(dt) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'pt', 'a4');
    const pageWidth = doc.internal.pageSize.getWidth();   
    const pageHeight = doc.internal.pageSize.getHeight();
    const reportDate = $('#date').val() || '';
    let formattedDate = '-';
    let dayName = '-';

    if (reportDate) {
        const dateObj = new Date(reportDate + 'T00:00:00');
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const year = dateObj.getFullYear();
        formattedDate = `${day}.${month}.${year}`;
        dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' }).toUpperCase();
    }

    const branchText = $('#branch option:selected').text().trim();
    const branchName = (branchText && branchText !== 'Select Branch') ? branchText : '';
    const cityName = branchName.includes(',') ? branchName.split(',').pop().trim() : branchName;
    const titleText = "GREEN PARK COACHING CENTRE" + (cityName ? ", " + cityName : "");

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(14);
    doc.setTextColor(0, 140, 70);
    doc.text(titleText, pageWidth / 2, 35, { align: 'center' });
    doc.setFontSize(10);
    doc.setTextColor(90, 90, 90);
    doc.text("DAILY ATTENDANCE REPORT", pageWidth / 2, 50, { align: 'center' });
    doc.setFontSize(9);
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(80, 50, 130); 
    doc.text("DATE : " + formattedDate, 25, 72);
    doc.text("DAY : " + dayName, pageWidth - 25, 72, { align: 'right' });
    doc.setDrawColor(210, 210, 210);
    doc.setLineWidth(0.8);
    doc.line(25, 78, pageWidth - 25, 78);

    const previousPageLen = dt.page.len();
    dt.page.len(-1).draw();

    doc.autoTable({
        html: '#attendance-table',
        startY: 86,
        theme: 'grid',
        margin: { left: 25, right: 25, bottom: 30 },
        styles: {
            font: 'helvetica',
            fontSize: 7.5,
            cellPadding: 3.5,
            halign: 'center',
            valign: 'middle',
            lineColor: [220, 220, 220],
            lineWidth: 0.5
        },
        headStyles: {
            fillColor: [41, 128, 185], 
            textColor: 255,
            fontStyle: 'bold',
            fontSize: 8,
            halign: 'center'
        },
        footStyles: {
            fillColor: [242, 244, 246],
            textColor: [30, 30, 30],
            fontStyle: 'bold',
            fontSize: 8,
            halign: 'center'
        },
        alternateRowStyles: {
            fillColor: [252, 252, 252]
        },
        didParseCell: function (data) {
            if (data.column.index === 1 && data.section === 'body') {
                data.cell.styles.halign = 'left';
            }
        },
        didDrawPage: function (data) {
            const totalPages = doc.internal.getNumberOfPages();
            doc.setFontSize(7.5);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(140, 140, 140);
            doc.text(
                `Page ${data.pageNumber} of ${totalPages}`,
                pageWidth / 2,
                pageHeight - 12,
                { align: 'center' }
            );
        }
    });
    dt.page.len(previousPageLen).draw();
    const safeDateStr = formattedDate !== '-' ? '_' + formattedDate.replace(/\./g, '-') : '';
    doc.save(`Attendance_Report${safeDateStr}.pdf`);
}
  
$('.view-students').on('click', function() {
        var type = $(this).data('type');
        var students = $(this).data('students');
        
        $('#modalTitle').text(type + ' Students');
        $('#studentListContainer').empty();

        if (students.length > 0) {
            $.each(students, function(index, name) {
                $('#studentListContainer').append('<li class="list-group-item col-md-6 border-0"><i class="fas fa-user mr-2 text-primary"></i>' + name + '</li>');
            });
        } else {
            $('#studentListContainer').append('<li class="list-group-item col-12 border-0 text-center text-muted">No students found.</li>');
        }

        $('#studentsModal').modal('show');
    });
  
</script>
@endsection