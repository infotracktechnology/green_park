@extends('layouts.app')
@section('title', 'Monthly Attendance Report')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<style>
    .report-btn { margin-bottom: 10px; width: 100%; font-weight: bold; }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          
          <!-- Filters Card -->
          <div class="card card-primary shadow-sm">
                <form method="get" action="{{ route('report.monthlyattendance') }}" id="filterForm">
                    <input type="hidden" name="report_type" id="report_type" value="{{ request('report_type', 'summary') }}">

                    <div class="card-header">
                        <h4>Monthly Attendance Report</h4>
                    </div>

                    <div class="card-body">

                        <!-- Report Category Buttons (Submit dynamically with specific report type) -->
                        <div class="row text-center mt-1">
                            {{-- <div class="col-md-2 col-6">
                                <button type="button" class="btn report-btn btn-{{ request('report_type', 'summary') == 'summary' ? 'primary shadow' : 'outline-primary' }}" onclick="submitReport('summary')">
                                    <i class="fas fa-chart-pie d-block mb-1"></i> Summary
                                </button>
                            </div> --}}
                            <div class="col-md-2 col-6">
                                <button type="button" class="btn report-btn btn-{{ request('report_type') == 'section' ? 'primary shadow' : 'outline-primary' }}" onclick="submitReport('section')">
                                    <i class="fas fa-layer-group d-block mb-1"></i> Section Wise
                                </button>
                            </div>
                            <div class="col-md-2 col-6">
                                <button type="button" class="btn report-btn btn-{{ request('report_type') == 'student' ? 'primary shadow' : 'outline-primary' }}" onclick="submitReport('student')">
                                    <i class="fas fa-user-graduate d-block mb-1"></i> Student Wise
                                </button>
                            </div>
                            <div class="col-md-2 col-6">
                                <button type="button" class="btn report-btn btn-{{ request('report_type') == 'course' ? 'primary shadow' : 'outline-primary' }}" onclick="submitReport('course')">
                                    <i class="fas fa-book d-block mb-1"></i> Course Wise
                                </button>
                            </div>
                            <div class="col-md-2 col-6">
                                <button type="button" class="btn report-btn btn-{{ request('report_type') == 'branch' ? 'primary shadow' : 'outline-primary' }}" onclick="submitReport('branch')">
                                    <i class="fas fa-building d-block mb-1"></i> Branch Wise
                                </button>
                            </div>
                            <div class="col-md-2 col-6">
                                <button type="button" class="btn report-btn btn-{{ request('report_type') == 'month' ? 'primary shadow' : 'outline-primary' }}" onclick="submitReport('month')">
                                    <i class="fas fa-calendar-alt d-block mb-1"></i> Month Wise
                                </button>
                            </div>
                        </div>

                        <div class="row mb-3 mt-4">
                            <!-- Branch Filter -->
                            <div class="col-lg-2">
                                <label>Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-control form-control-sm" required onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected($branch->id == request('branch_id'))>
                                        {{ $branch->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Course Filter -->
                            <div class="col-lg-2">
                                <label>Course</label>
                                <select name="course" class="form-control form-control-sm">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                    <option value="{{ $course }}" @selected($course == request('course'))>
                                        {{ $course }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Section Filter -->
                            <div class="col-lg-2">
                                <label>Section</label>
                                <select name="section" class="form-control form-control-sm">
                                    <option value="">All Sections</option>
                                    @foreach($sections as $section)
                                    <option value="{{ $section }}" @selected($section == request('section'))>
                                        {{ $section }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div class="col-lg-2">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" required>
                            </div>

                            <!-- End Date -->
                            <div class="col-lg-2">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" max="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                            </div>

                            <!-- Reset / Submit -->
                            <div class="col-lg-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block btn-sm">Submit Filters</button>
                            </div>
                        </div>


                    </div>
                </form>
          </div>

          <!-- Dynamic Output Container -->
          @if(request('report_type') && request('branch_id'))
          <div class="card card-success shadow-sm">
              <div class="card-header">
                  <h4>{{ ucwords(str_replace('_', ' ', request('report_type'))) }} Attendance Details</h4>
              </div>
              <div class="card-body">

                  <!-- 1. MONTHLY ATTENDANCE SUMMARY CARDS -->
                  {{-- @if(request('report_type') == 'summary' && $summary)
                  <div class="row text-center">
                      <div class="col-md-2 col-6 mb-3">
                          <div class="p-3 border rounded shadow-sm bg-light">
                              <h6 class="text-muted">Total Students</h6>
                              <h4 class="text-primary">{{ $summary->total_students }}</h4>
                          </div>
                      </div>
                      <div class="col-md-2 col-6 mb-3">
                          <div class="p-3 border rounded shadow-sm bg-light">
                              <h6 class="text-muted">Working Days</h6>
                              <h4 class="text-info">{{ $summary->working_days }}</h4>
                          </div>
                      </div>
                      <div class="col-md-3 col-6 mb-3">
                          <div class="p-3 border rounded shadow-sm bg-light">
                              <h6 class="text-muted">Total Present</h6>
                              <h4 class="text-success">{{ $summary->present }}</h4>
                          </div>
                      </div>
                      <div class="col-md-3 col-6 mb-3">
                          <div class="p-3 border rounded shadow-sm bg-light">
                              <h6 class="text-muted">Total Absent</h6>
                              <h4 class="text-danger">{{ $summary->absent }}</h4>
                          </div>
                      </div>
                      <div class="col-md-2 col-6 mb-3">
                          <div class="p-3 border rounded shadow-sm bg-light">
                              <h6 class="text-muted">Overall %</h6>
                              <h4 class="text-warning">{{ $summary->overall_percentage }}%</h4>
                          </div>
                      </div>
                  </div>
                  @endif --}}

                  <!-- 2. SECTION, STUDENT, COURSE, BRANCH WISE TABLES -->
                  @if(in_array(request('report_type'), ['section', 'student', 'course', 'branch']) && count($data))
                  <div class="table-responsive">
                      <table class="table table-striped table-hover dynamic-table">
                          <thead>
                              <tr>
                                  <th>S.No</th>
                                  @foreach(array_keys($data->first()) as $header)
                                      <th>{{ ucwords(str_replace('_', ' ', $header)) }}</th>
                                  @endforeach
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($data as $row)
                              <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  @foreach($row as $key => $val)
                                      <td>{{ is_numeric($val) && str_contains($key, 'percentage') ? $val . '%' : $val }}</td>
                                  @endforeach
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
                  @endif

                  <!-- 3. MONTH WISE TABLE -->
                  @if(request('report_type') == 'month' && count($data))
                  <div class="table-responsive">
                      <table class="table table-striped table-hover dynamic-table">
                          <thead>
                              <tr>
                                  <th>S.No</th>
                                  <th>Date</th>
                                  <th>Present Count</th>
                                  <th>Absent Count</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($data as $row)
                              <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M, Y') }}</td>
                                  <td>
                                      <a href="javascript:void(0)" class="badge badge-success text-white view-students" data-type="Present" data-students="{{ $row['present_students'] }}">
                                          {{ $row['present_count'] }}
                                      </a>
                                  </td>
                                  <td>
                                      <a href="javascript:void(0)" class="badge badge-danger text-white view-students" data-type="Absent" data-students="{{ $row['absent_students'] }}">
                                          {{ $row['absent_count'] }}
                                      </a>
                                  </td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
                  @endif

              </div>
          </div>
          @endif

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
  function submitReport(type) {
      document.getElementById('report_type').value = type;
      document.getElementById('filterForm').submit();
  }

  $(document).ready(function() {
    if ($('.dynamic-table').length > 0) {
        $('.dynamic-table').DataTable({
            dom: 'Bfrtip',
            buttons: [{   
                extend: 'excel', 
                title: 'Attendance_Report {{ request("report_type")  }}-{{ request("start_date") }}_to_{{ request("end_date") }}  ',
            }],
            pageLength: 10,
            responsive: true
        });
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
  });
</script>
@endsection