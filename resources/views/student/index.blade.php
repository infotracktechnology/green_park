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
            <div class="card-body">
              <div class="row">

                <div class="col-md-10 col-sm-12">
                  <h6 class="col-deep-purple">Students Details</h6>
                </div>

                {{-- <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{ route('student.create') }}" class="btn btn-primary btn-block">Add Students</a>
              </div> --}}

            </div>

            <form action="{{route('student.index')}}" id="myForm" method="get">
              <div class="col-md-4 form-group">
                <select class="form-control form-control-sm" onchange="document.getElementById('myForm').submit();" name="course">
                  <option value="">Select Course</option>
                  @foreach($course as $row)
                  <option value="{{$row}}" @selected($row==request('course'))>{{$row}}</option>
                  @endforeach
                </select>
              </div>
            </form>


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
                      <th>User Name</th>
                      <th>Password</th>
                      <th>Gender</th>
                      <th>Father No</th>
                      <th>Mother No</th>
                      <th>Edit </th>
                      <th>Inactive</th>
                    </tr>

                  </thead>

                  <tbody>
                    @foreach ($students as $student)
                    <tr>
                      <td>{{$student->student_id}}</td>
                      <td>{{$student->student_name}}</td>
                      <td>{{$student->course}}</td>
                      <td>{{$student->branch->name}}</td>
                      <td>{{$student->coaching_type}}</td>
                      <td>{{$student->hostel_dayscholar}}</td>
                      <td>{{$student->section}}</td>
                      <td>{{$student->batch}}</td>
                      <td>{{$student->user_name}}</td>
                      <td>{{$student->password_1}}</td>
                      <td>{{$student->gender}}</td>
                      <td>{{$student->father_ph_no}}</td>
                      <td>{{$student->mother_ph_no}}</td>
                      <td>
                        <a href="{{route('student.edit', $student->id)}}" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a>
                      </td>

                      <td>
                        <button type="button" class="btn btn-danger inactive" data-toggle="modal" data-target="#Inactive" data-id="{{$student->id}}">Inactive</button>
                        {{-- <form action="{{route('student.destroy', $student->id)}}" method="post" onsubmit="return confirm('Are you sure you want to Inactive this student?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                        </form> --}}
                      </td>
                    </tr>
                    @endforeach

                  </tbody>
                </table>
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

<div id="Inactive" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Inactive Form</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" id="InactiveForm" onsubmit="return confirm('Are you sure you want to Inactive this student?')" enctype="multipart/form-data">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <div class="form-group">
            <label>Remark/Reason</label>
            <textarea name="remarks" rows="3" class="form-control form-control-sm" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
  const table = $('#myTable').DataTable({
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  });

  $('.inactive').on('click',function () {
    var id = $(this).data('id');
    $('#InactiveForm').attr("action", "{{ route('student.destroy', ':id') }}".replace(':id', id));
    $('#Inactive').modal('show');
  });
      
</script>
@endsection