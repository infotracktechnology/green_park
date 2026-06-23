@extends('layouts.app')
@section('title', 'Sickroom')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
@endsection
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('sickroom.update', $sickroom->id) }}" enctype="multipart/form-data">
              @method('PUT')
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Sick Room Entry</h6>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Branch</label>
                    <select class="select2" id="branchid" name="branch_id" required>
                      <option value="">Choose Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == $sickroom->branch_id)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Hostel</label>
                    <select class="select2" id="hostel" name="hostel_id" required>
                      <option value="">Choose Hostel</option>
                      @foreach ($hostels as $row)
                      <option value="{{ $row->id }}" @selected($row->id == $sickroom->hostel_id)>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Room No</label>
                    <select class="select2" id="room" name="room_no" required>
                      <option value="">Choose Room</option>
                      @foreach ($room as $row)
                      <option value="{{ $row }}" @selected($row == $sickroom->room_no)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Student</label>
                    <select class="select2" id="student" name="student_id" required>
                      <option value="">Choose Student</option>
                      @foreach ($student as $row)
                      <option value="{{ $row->student_id }}" @selected($row->student_id == $sickroom->student_id)>{{ $row->student_id}} {{ $row->student_name }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>In Time</label>
                    <input type="text" name="in_time" id="in_time" class="datetime-picker form-control form-control-sm" value="{{ $sickroom->in_time }}" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Type of Illness/Injury</label>
                    <textarea name="illness" rows="3" class="form-control form-control-sm" required>{{ $sickroom->illness }}</textarea>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Action Taken</label>
                    <textarea name="action_taken" rows="3" class="form-control form-control-sm">{{ $sickroom->action_taken }}</textarea>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Medical Officer's/Nurse's Note</label>
                    <textarea name="medical_note" rows="3" class="form-control form-control-sm">{{ $sickroom->medical_note }}</textarea>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Out Time</label>
                    <input type="text" name="out_time" id="out_time" value="{{ $sickroom->out_time }}" class="datetime-picker form-control form-control-sm" required>
                    <input type="hidden" name="hours_spent" id="hours_spent">
                  </div>

                  <div class="form-group col-lg-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
  flatpickr(".datetime-picker", {
      enableTime: true,
      allowInput: true,
      dateFormat: "Y-m-d H:i",
      maxDate: "today",
      plugins: [new confirmDatePlugin({ confirmText: "OK"})]
  });
  $('#out_time').change(function() {
      var startTime = new Date($('#in_time').val());
      var endTime = new Date($(this).val());
      var diff = endTime - startTime;
      var hours = diff / (1000 * 60 * 60);
      $('#hours_spent').val(hours.toFixed(1));
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