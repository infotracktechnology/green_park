@extends('layouts.app')
@section('title', 'Hostel In/Out Register')

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
              <h4>Hostel In/Out Register</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3 offset-lg-9">
                  <button class="btn m-b-10 btn-primary" data-toggle="modal" data-target="#OutEntry">Add Out Entry</button>
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
                          <th>Date & Time Leaving (Out)</th>
                          <th>Purpose/Reason</th>
                          <th>Contact No</th>
                          <th>Return Entry (In)</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($register as $key => $row)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $row->hostel?->name }}</td>
                          <td>{{ $row->room_no }}</td>
                          <td>{{ $row->student_id }}</td>
                          <td>{{ $row->student?->student_name }}</td>
                          <td>{{ $row->datetime_out->format('d/m/Y h:i A') }}</td>
                          <td>{{ $row->reason }}</td>
                          <td>{{ $row->contact_out }}</td>
                          <td><button class="btn btn-primary inentry" data-row="{{ json_encode($row) }}">In Entry</button></td>
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


<div id="InEntry" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update In Entry</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="get" action="{{ route('hostel.inoutregister') }}">

        <div class="modal-body">
          <div class="form-group">
            <label>Date & Time of Return (In)</label>
            <input type="hidden" name="update">
            <input type="text" name="datetime_in" class="datetime-picker form-control form-control-sm" required>
          </div> 
        </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div id="OutEntry" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Out Entry</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" action="{{ route('hostel.inoutregister') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
          <div class="form-group col-lg-6">
            <label>Branch</label>
            <select class="select2" id="branchid"  name="branch" required>
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
            <label>Datetime Leaving (Out)</label>
            <input type="text" name="datetime_out" class="datetime-picker form-control form-control-sm" required>
          </div>          

          <div class="form-group col-lg-6">
            <label>Contact No (Out)</label>
            <input type="text" name="contact_out" class="form-control form-control-sm">
          </div>

          <div class="form-group col-lg-12">
            <label>Purpose/Reason</label>
            <textarea name="reason" rows="3" class="form-control form-control-sm" required></textarea>
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

   $('.inentry').click(function(){
    var row = $(this).data('row');
    $('#InEntry').modal('show');
    $('#InEntry').find('input[name="update"]').val(row.id);
   });

</script>
@endsection