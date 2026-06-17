@extends('layouts.app')
@section('title', 'Student')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">

        <div class="col-md-12 col-sm-12">
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
          @endif

          <div class="card card-primary">
            <div class="card-header"><h4>Reactive Student</h4></div>
            <div class="card-body">

              {{-- <form action="{{route('students.restore')}}" method="get">
                <div class="row">

                <div class="col-md-2 form-group">
                  <select class="form-control form-control-sm" name="course">
                    <option value="">Select Course</option>
                    @foreach($course as $row)
                    <option value="{{$row}}" @selected($row==request('course'))>{{$row}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-2 form-group">
                  <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </div>
                </div>
              </form> --}}

              <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="myTable">
                    <thead>
                      <tr role="row">
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Campus</th>
                        <th>Coaching Type</th>
                        <th>H/D</th>
                        <th>Section</th>
                        <th>Batch</th>
                        <th>Inactive Date & Time</th>
                        <th>Reason/Remarks</th>
                        <th>Reactive</th>
                        <th>Action</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach ($students as $student)
                      <tr>
                        <td>{{$student->student_id}}</td>
                        <td>{{$student->student_name}}</td>
                        <td>{{$student->course}}</td>
                        <td>{{$student->branch?->name}}</td>
                        <td>{{$student->coaching_type}}</td>
                        <td>{{$student->hostel_dayscholar}}</td>
                        <td>{{$student->section}}</td>
                        <td>{{$student->batch}}</td>
                        <td>{{$student->deleted_at->format('d-m-Y h:i A')}}</td>
                        <td>{{$student->remarks}}</td>
                        <td>
                           <form action="{{route('students.restore')}}" class="no-loader" method="post" onsubmit="return confirm('Are you sure you want to restore this student?')">
                            @csrf
                            <input type="hidden" value="{{ $student->id }}" name="id" />
                            <button type="submit" class="btn btn-danger"><i class="fas fa-sync"></i></button>
                          </form>
                        </td>
                        <td>
                          <form action="{{route('students.permanentdelete')}}" class="no-loader" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this student?')">
                            @csrf
                            <input type="hidden" value="{{ $student->id }}" name="id" />
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                          </form>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>

  </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
  const table = $('#myTable').DataTable({
    // searching: false,
    paging: false,
  });
</script>
@endsection