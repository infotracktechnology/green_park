@extends('layouts.app')
@section('title', 'Hostel Courier Entry')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css" />
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

          <div class="card card-primary">
            <div class="card-header">
              <h4>Hostel Courier Entry</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3 offset-lg-9">
                  <button class="btn m-b-10 btn-primary" data-toggle="modal" data-target="#AddEntry">Add Entry</button>
                </div>
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table class="table table-striped" style="width:100%;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Hostel</th>
                          <th>Room No</th>
                          <th>Student ID</th>
                          <th>Name</th>
                          <th>Date & Time of Arrival</th>
                          <th>Courier Company Name</th>
                          <th>Sender information</th>
                          <th>Details of Courier</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($hostelcourier as $key => $row)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $row->hostel?->name }}</td>
                          <td>{{ $row->room_no }}</td>
                          <td>{{ $row->student_id }}</td>
                          <td>{{ $row->student?->student_name }}</td>
                          <td>{{ $row->datetime_arrival->format('d/m/Y h:i A') }}</td>
                          <td>{{ $row->courier_company }}</td>
                          <td>{{ $row->sender_info }}</td>
                          <td>{{ $row->courier_details }}</td>
                          <td>
                            <form action="{{ route('hostel.courier') }}" method="get" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                              <input type="hidden" name="delete" value="{{ $row->id }}">
                              <button type="submit" class="btn btn-danger text-white"><i class="fa fa-trash"></i></button>
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
    </div>
  </section>
</div>




<div id="AddEntry" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Courier Entry</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" action="{{ route('hostel.courier') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-lg-6">
              <label>Branch</label>
              <select class="select2" id="branchid" name="branch" required>
                <option value="">Choose Branch</option>
                @foreach ($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-lg-6">
              <label>Hostel</label>
              <select class="select2" id="hostel" name="hostel_id" required>
                <option value="">Choose Hostel</option>
              </select>
            </div>

            <div class="form-group col-lg-6">
              <label>Room No</label>
              <select class="select2" id="room" name="room_no" required>
                <option value="">Choose Room</option>
              </select>
            </div>

            <div class="form-group col-lg-6">
              <label>Student</label>
              <select class="select2" id="student" name="student_id" required>
                <option value="">Choose Student</option>
              </select>
            </div>

            <div class="form-group col-lg-6">
              <label>Date & Time of Arrival</label>
              <input type="text" name="datetime_arrival" class="datetime-picker form-control form-control-sm" required>
            </div>

            <div class="form-group col-lg-6">
              <label>Courier Company Name</label>
              <input type="text" name="courier_company" class="form-control form-control-sm">
            </div>

            <div class="form-group col-lg-6">
              <label>Sender Information</label>
              <textarea name="sender_info" rows="3" class="form-control form-control-sm"></textarea>
            </div>

            <div class="form-group col-lg-6">
              <label>Details of Courier</label>
              <textarea name="courier_details" rows="3" class="form-control form-control-sm" required></textarea>
            </div>

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
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
  $('.table').DataTable();
  
   flatpickr(".datetime-picker", {
     enableTime: true,
     allowInput: true,
     dateFormat: "Y-m-d H:i",
     plugins: [
         new confirmDatePlugin({
             confirmText: "OK",
             showAlways: false,
         })
     ]
  });
  
   const Hostelfetch = (params) => $.get('{{ route("hostel.inoutregister") }}', params);
   const hostel = $('#hostel');
   const room = $('#room');
   const student= $('#student');
  
   $("#branchid").change(function(){
      Hostelfetch({branch: $(this).val()}).then((data) => {
        hostel.empty();
        hostel.append(`<option value="">Choose Hostel</option>`);
        $.each(data, (key, value) => {
          hostel.append(`<option value="${value.id}">${value.name}</option>`);
        });
      });
   });
  
   $("#hostel").change(function(){
      Hostelfetch({hostel: hostel.val()}).then((data) => {
        room.empty();
        room.append(`<option value="">Choose Room</option>`);
        $.each(data, (key, value) => {
          room.append(`<option value="${value}">${value}</option>`);
        });
      });
   });
  
   $("#room").change(function(){
      Hostelfetch({room: room.val(), hostel: hostel.val()}).then((data) => {
        student.empty();
        student.append(`<option value="">Choose Student</option>`);
        $.each(data, (key, value) => {
          student.append(`<option value="${value.student_id}">${value.student_id} - ${value.student_name}</option>`);
        });
      });
   });
  
</script>
@endsection