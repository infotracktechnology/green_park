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
                    <select name="branch_id" id="branch" class="form-control form-control-sm" required>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == request('branch_id'))>
                        {{ $branch->name }}
                      </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-lg-2">
                      <label>Course</label>
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
                      <select name="section" id="section" class="form-control form-control-sm">
                          <option value="">All Section</option>
                          @foreach($sections as $section)
                              <option value="{{ $section->section }}"
                                  @selected(request('section') == $section->section)>
                                  {{ $section->section }}
                              </option>
                          @endforeach
                      </select>
                  </div>

                  <div class="col-lg-2">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date')  ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                  </div>

                  <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </div>
                </div>
                @if(count($attendances))
                <div class="table-responsive">
                  <table class="table table-striped" id="attendance-table">
                    <thead>
                      <tr>
                        <th>S.NO</th>
                        <th>Section Name</th>
                        <th>Boys</th>
                        <th>Girls</th>
                        <th>Total</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Present (%)</th>
                        <th>Absent (%)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                       $boys_total=$girls_total=$total=$present=$absent = 0;
                       ?>
                      @foreach($attendances as $row)
                      <?php
                        $boys_total += $row['boys'];
                        $girls_total += $row['girls'];
                        $total += $row['total'];
                        $present += $row['present'];
                        $absent += $row['absent'];
                    ?>
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['section'] }}</td>
                        <td>{{ $row['boys'] }}</td>
                        <td>{{ $row['girls'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td> <a href="javascript:void(0)" class="badge badge-success text-white view-students" data-type="Present" data-students='@json($row["present_students"])'>{{ $row['present'] }}</a></td>
                        <td> <a href="javascript:void(0)" class="badge badge-danger text-white view-students" data-type="Absent" data-students='@json($row["absent_students"])'>{{ $row['absent'] }} </a></td>
                        <td>{{ round($row['present'] * 100 / $row['total'], 2) }}</td>
                        <td>{{ round($row['absent'] * 100 / $row['total'], 2) }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="2" class="font-16 fw-bold text-center">Total</td>
                        <td>{{ $boys_total }}</td>
                        <td>{{ $girls_total }}</td>
                        <td>{{ $total }}</td>
                        <td>{{ $present }}</td>
                        <td>{{ $absent }}</td>
                        <td>{{ round($present * 100 / $total, 2) }}</td>
                        <td>{{ round($absent * 100 / $total, 2) }}</td>
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

<script>
  $(document).ready(function () {
    $("#attendance-table").DataTable({
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          footer: true,
        }
      ],
      pageLength: 25,
    });
  });
  
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