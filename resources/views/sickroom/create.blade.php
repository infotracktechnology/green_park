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
          @if(session('warning'))
          <div class="alert alert-warning">
              {{ session('warning') }}
          </div>
          @endif
          @if(session('error'))
          <div class="alert alert-danger">
              {{ session('error') }}
          </div>
          @endif
          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('sickroom.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Sick Room Entry</h6>
                  </div>

                  <div class="form-group col-lg-3">
                      <label>Student</label>
                      <select class="select2" id="student" name="student_id" required>
                          <option value="">Choose Student</option>

                          @foreach($students as $student)
                              <option value="{{ $student->student_id }}">
                                  {{ $student->student_id }} - {{ $student->student_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Branch</label>
                    <select class="select2" id="branchid" name="branch_id" required disabled>
                      <option value="">Choose Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Hostel</label>
                    <select class="select2" id="hostel" name="hostel_id" required disabled>
                      <option value="">Choose Hostel</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Room No</label>
                    <select class="select2" id="room" name="room_no" required disabled>
                      <option value="">Choose Room</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select class="select2" id="sections" name="sections" required disabled>
                      <option value="">Choose Section</option>
                    </select>
                  </div>

                  


                  <div class="form-group col-lg-2">
                    <label>In Time</label>
                    <input type="text" name="in_time" id="in_time" class="datetime-picker form-control form-control-sm" value="{{ now()->format('Y-m-d H:i') }}" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Type of Illness/Injury</label>
                    <textarea name="illness" rows="3" class="form-control form-control-sm" required></textarea>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Action Taken</label>
                    <textarea name="action_taken" rows="3" class="form-control form-control-sm"></textarea>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Medical Officer's/Nurse's Note</label>
                    <textarea name="medical_note" rows="3" class="form-control form-control-sm"></textarea>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Out Time</label>
                    <input type="text" name="out_time" id="out_time" class="datetime-picker form-control form-control-sm" required>
                    <input type="hidden" name="hours_spent" id="hours_spent">
                  </div>

                <div class="form-group col-lg-2">
                    <label>Expense</label>
                    <input type="number" name="expense" class="form-control form-control-sm" value="0" min="0">
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

const Hostelfetch = (params) => $.get('{{ route("sickroom.create") }}', params);

const hostel = $('#hostel');
const room = $('#room');
const student = $('#student');

$("#student").change(function () {
    let studentId = $(this).val();
    if (!studentId) {
        return;
    }
    Hostelfetch({ student: studentId }).then((data) => {
        if (data.success) {
            $('#branchid').val(data.branch_id) .trigger('change.select2');
            hostel.html(` <option value="${data.hostel_id}" selected> ${data.hostel_name} </option> `).trigger('change.select2');
            room.html(` <option value="${data.room_no}" selected> ${data.room_no} </option> `).trigger('change.select2');
            $('#sections').html(` <option value="${data.sections}" selected> ${data.sections} </option> `).trigger('change.select2');
              }

    }).fail(function () {
        console.log('Student details fetch failed');
    });
});


</script>
@endsection