@extends('layouts.app')

@section('title', 'Examination Log Report')
@section('css')
<style>
  thead th{
    background-color: #56ade8 !important;
     color: #222 !important;
  }
  table th,table td {
  border: 1px solid #222 !important;
  height: 0px !important;
  }
  span{
    cursor: pointer;
  }
</style>
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
          @endif
          @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
          @endif

          <div class="card card-primary">
            <div class="card-header">
              <h4>Examination Log Report</h4>
            </div>
            <div class="card-body">
              <form method="get" action="{{ route('report.examination_log') }}">
                <div class="row">
                  <div class="form-group col-lg-4">
                    <label>Exam Category</label>
                    <select name="testcategory" id="testcategory" class="select2" required>
                      <option value="">Select Category</option>
                      @foreach ($category as $row)
                      <option value="{{ $row }}" @selected($row==request('testcategory'))>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Exam Name</label>
                    <select name="examname" id="examname" class="select2" required>
                      <option value="">Select Test</option>
                      @foreach ($exams as $exam)
                      <option value="{{ $exam->name }}" @selected($exam->name==request('examname'))>{{ $exam->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          @if($stats)
          <div class="row">
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h4>AT THE TIME OF EXAMINATION</h4>
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      1. TOTAL NUMBER OF STUDENTS ELIGIBLE FOR EXAM:
                      <span class="badge badge-primary">{{ $stats['total_eligible'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      2. TOTAL NUMBER OF STUDENTS ONLINE:
                      <span class="badge badge-primary"  data-toggle="modal" data-target="#studentModal" onclick="loadStudents('online')" role="button">{{ $stats['total_online'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      3. NUMBER OF STUDENTS WRITING THE EXAM:
                     <span class="badge badge-primary" data-toggle="modal" data-target="#studentModal" onclick="loadStudents('writing')" role="button">{{ $stats['total_writing'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      4. NUMBER OF STUDENTS FINISHED THE EXAM:
                      <span class="badge badge-primary" data-toggle="modal" data-target="#studentModal" onclick="loadStudents('finished')" role="button">{{ $stats['total_finished'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      5. NUMBER OF STUDENTS NOT FINISHED:
                      <span class="badge badge-primary badge-pill">{{ $stats['total_not_finished'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      6. NUMBER OF STUDENTS ABSENT:
                      <span class="badge badge-primary" data-toggle="modal" data-target="#studentModal" onclick="loadStudents('absent')" role="button">{{ $stats['total_absent'] }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h4>RESPONSE REPORT</h4>
                </div>
                <div class="card-body">
                  <form method="get" action="{{ route('report.examination_log') }}">
                    <input type="hidden" name="testcategory" value="{{ request('testcategory') }}">
                    <input type="hidden" name="examname" value="{{ request('examname') }}">
                    <div class="form-group">
                      <label>SEARCH BY STUDENT ID/ NAME:</label>
                      <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Enter Student ID or Name" value="{{ request('search') }}">
                        <div class="input-group-append">
                          <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                      </div>
                    </div>
                  </form>

                  @if(count($students) > 0)
                  <div class="table-responsive mt-3">
                    <table class="table table-striped table-sm">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Section</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($students as $student)
                        <tr>
                          <td>{{ $student->student_id }}</td>
                          <td>{{ $student->student_name }}</td>
                          <td>{{ $student->section }}</td>
                          <td>
                            <a href="{{ route('report.student_response.download', ['examname' => $test_name, 'student_id' => $student->student_id]) }}" class="btn btn-primary">Download</a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  @elseif(request('search'))
                  <div class="alert alert-info mt-3">No students found matching your search.</div>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="studentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Student List</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>S.No</th>
              <th>ID</th>
              <th>Name</th>
              <th>Course</th>
              <th>Coaching Type</th>
              <th>Section</th>
            </tr>
          </thead>
          <tbody id="studentTableBody"></tbody>
        </table>
      </div>

    </div>
  </div>
</div>
@endsection

@section('js')
<script>
$('#testcategory').change(() => {
    let category = $('#testcategory').val();
    if(category) {
        window.location = `{{ route('report.examination_log') }}?testcategory=${category}`;
    }
});
let studentData = @json($studentDetails);
function loadStudents(type) {
    let tbody = $('#studentTableBody');
    tbody.empty();
    studentData[type].forEach((student, index) => {
        tbody.append(`
            <tr>
                <td>${index + 1}</td>
                <td>${student.student_id}</td>
                <td>${student.student_name}</td>
                <td>${student.course}</td>
                <td>${student.coaching_type}</td>
                <td>${student.section}</td>
            </tr>
        `);
    });
    $('#studentModal').modal('show');
}
</script>
@endsection
