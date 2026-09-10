@extends('layouts.app')
@section('title', 'Medical Entry')
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
          @if(session('error'))
          <div class="alert alert-danger">
              {{ session('error') }}
          </div>
          @endif
          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('medical.update', $medical->id) }}" enctype="multipart/form-data">
              @method('PUT')
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Medical Entry</h6>
                  </div>

                  <div class="form-group col-lg-3">
                      <label>Student</label>
                      <select class="select2" id="student" name="student_id" required>
                          <option value="">Choose Student</option>

                          @foreach ($student as $row)
                              <option value="{{ $row->student_id }}"
                                  @selected($row->student_id == $medical->student_id)>
                                  {{ $row->student_id }} - {{ $row->student_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Branch</label>
                    <select class="select2" id="branchid" name="branch_id" required>
                      <option value="">Choose Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == $medical->branch_id)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Hostel</label>
                    <select class="select2" id="hostel" name="hostel_id" required>
                      <option value="">Choose Hostel</option>
                      @foreach ($hostels as $row)
                      <option value="{{ $row->id }}" @selected($row->id == $medical->hostel_id)>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Room No</label>
                    <select class="select2" id="room" name="room_no" required>
                      <option value="">Choose Room</option>
                      @foreach ($room as $row)
                      <option value="{{ $row }}" @selected($row == $medical->room_no)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  {{-- <div class="form-group col-lg-3">
                    <label>Student</label>
                    <select class="select2" id="student" name="student_id" required>
                      <option value="">Choose Student</option>
                      @foreach ($student as $row)
                      <option value="{{ $row->student_id }}" @selected($row->student_id == $medical->student_id)>{{ $row->student_id}} {{ $row->student_name }}</option>
                      @endforeach
                    </select>
                  </div> --}}


                  <div class="form-group col-lg-2">
                    <label>Out Time</label>
                    <input type="text" name="in_time" id="in_time" class="datetime-picker form-control form-control-sm" value="{{ $medical->in_time }}" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Type of Illness/Injury</label>
                    <textarea name="illness" rows="3" class="form-control form-control-sm" required>{{ $medical->illness }}</textarea>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Action Taken</label>
                    <textarea name="action_taken" rows="3" class="form-control form-control-sm">{{ $medical->action_taken }}</textarea>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Medical Officer's/Nurse's Note</label>
                    <textarea name="medical_note" rows="3" class="form-control form-control-sm">{{ $medical->medical_note }}</textarea>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>IN Time</label>
                    <input type="text" name="out_time" id="out_time" value="{{ $medical->out_time }}" class="datetime-picker form-control form-control-sm" required>
                    <input type="hidden" name="hours_spent" id="hours_spent">
                  </div>

                  <div class="form-group col-lg-2">
                      <label>Expense</label>
                      <input type="number" name="expense" class="form-control form-control-sm" value="{{ $medical->expense }}" min="0">
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

  const StudentFetch = (params) => $.get('{{ route("medical.create") }}', params);

  $("#student").change(function () {
      let studentId = $(this).val();
      if (!studentId) {
          return;
      }
      StudentFetch({ student: studentId }).then(function (data) {
          if (data.success) {
              $('#branchid').val(data.branch_id).trigger('change');
              $('#hostel').html(` <option value="${data.hostel_id}" selected> ${data.hostel_name} </option>`).trigger('change');
              $('#room').html(` <option value="${data.room_no}" selected> ${data.room_no} </option> `).trigger('change');
          }

      }).fail(function () {
          console.log('Student details fetch failed');
      });
  });
</script>
@endsection