@extends('layouts.app')
@section('title', 'Hostel Allocation')

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
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif

          @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
          </div>
          @endif

          <div class="card card-primary">
            <div class="card-header">
              <h4>Hostel Reallocation</h4>
            </div>
            <div class="card-body">
              <form method="get" action="{{ route('room.reallocation') }}">
                <div class="row">

                  <div class=" form-group col-2">
                    <label>Branch</label>
                    <select name="branch" id="branch" class="select2" required>
                      <option value="">Choose Branch</option>
                      @foreach ($branches as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('branch'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group  col-3">
                    <label>Hostel</label>
                    <select name="hostel" id="hostel" class="select2" required>
                      <option value="">Select Hostel</option>
                      @foreach ($hostel as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('hostel'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-2">
                    <label>Rooms</label>
                    <select name="room" id="room" class="select2" required>
                      <option value="">Select Room</option>
                      @foreach ($room as $row)
                      <option value="{{ $row }}" @selected($row==request('room'))>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn m-t-25 btn-primary">Show Cots</button>
                  </div>

                </div>
              </form>


              <div class="row m-t-20">
                <div class="col-lg-9">
                  <h6>Students Cots Details</h6>
                </div>
                <div class="col-lg-3">
                  <button type="button" data-toggle="modal" data-target="#studentModal" class="btn btn-primary">Add Student</button>
                </div>
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table class="table table-striped" style="width:100%;">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Student ID</th>
                          <th>Name</th>
                          <th>Section</th>
                          <th>Room</th>
                          <th>Cot No</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($allocatedStudents as $key => $row)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $row->student_id }}</td>
                          <td>{{ $row->student_name }}</td>
                          <td>{{ $row->section }}</td>
                          <td>{{ $row->room_no }}</td>
                          <td>{{ $row->cots_no }}</td>
                          <td>
                            <a href="{{ route('room.reallocation', ['delete'=> $row->student_id]) }}" class="btn btn-danger text-white"><i class="fa fa-trash-alt"></i></a>
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

    </div>
  </section>
</div>

<div id="studentModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Student</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" action="{{ route('room.reallocation') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="hostel_id" value="{{ request('hostel') }}">
        <input type="hidden" name="room_no" value="{{ request('room') }}">
        <div class="modal-body">
          <div class="form-group">
            <label>Student</label>
            <select class="select2" required name="student_id">
              <option value="">Choose Student</option>
              @foreach ($availableStudents as $row)
              <option value="{{ $row->student_id }}">{{ $row->student_id }} - {{ $row->student_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Cot No</label>
            <input type="text" name="cots_no" class="form-control form-control-sm" required>
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
  $('.table').DataTable({
      searching: false,
      paging: false
  });
  
  const goToMenu = (params = {}) => {
    const query = new URLSearchParams(params).toString();
    window.location = `{{ route('room.reallocation') }}?${query}`;
     };
  
  $("#branch").change(function() {
      if(!this.value) return;
    goToMenu({
      branch: this.value,
    });
  });
  
  $("#hostel").change(function() {
      if(!this.value) return;
    goToMenu({
      branch: $("#branch").val(),
      hostel: this.value,
    });
  });
  
  $('.btn-danger').click(function(e){
    e.preventDefault();
    var href = $(this).attr('href');
    if(confirm('Are you sure you want to remove this student?')) {
      window.location.href = href;
    }
  });
</script>
@endsection