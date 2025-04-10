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
                     <form method="post" id="myForm" action="{{ route('sickroom.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Sick Room Entry</h6>
                             </div>
   
        <div class="form-group col-lg-3">
            <label>Class</label>
            <input type="text" name="student_class" class="form-control form-control-sm" required>
        </div>

        <div class="form-group col-lg-3">
            <label>Section</label>
            <select name="section" id="section" class="form-control" required>
                <option value="">Select Section</option>
                @foreach($sections as $section)
                    <option value="{{ $section }}">{{ $section }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group col-lg-3">
            <label>Student ID</label>
            <select name="student_id" id="student_id" class="form-control select2" required>
                <option value="">Select Student ID</option>
            </select>
        </div>
        
    
        <div class="form-group col-lg-3">
            <label>Room No</label>
            <input type="text" name="room_no" class="form-control form-control-sm" required>
        </div>

        <div class="form-group col-lg-3">
            <label>In Time</label>
            <input type="text" name="in_time"  class="datetime-picker form-control form-control-sm" value="{{ now()->format('Y-m-d H:i') }}"  required>
        </div>
        

        <div class="form-group col-lg-3">
            <label>Out Time</label>
            <input type="text" name="out_time" class="datetime-picker form-control form-control-sm">
        </div>

        <div class="form-group col-lg-3">
            <label>Reason</label>
            <textarea name="reason" class="form-control form-control-sm"></textarea>
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
        plugins: [
            new confirmDatePlugin({
                confirmText: "OK",
                showAlways: false,
                theme: "light"
            })
        ]
    });

    $('#out_time').change(function () {
        $('#out_time_error').text('');
        const start = new Date($('#in_time').val());
        const end = new Date($('#out_time').val());

        if ($('#in_time').val() && end <= start) {
            $('#out_time_error').text('End time must be greater than start time.');
            $('#out_time').val('');
        }
    });
</script>
<script>
    const allStudents = @json($students);
     document.getElementById('section').addEventListener('change', function () {
        const selectedSection = this.value;
        const studentSelect = document.getElementById('student_id');
        studentSelect.innerHTML = '<option value="">Select Student ID</option>';

        if (selectedSection) {
            const filtered = allStudents.filter(s => s.section === selectedSection);
            filtered.forEach(student => {
                const option = document.createElement('option');
                option.value = student.student_id;
                option.text = `${student.student_id} - ${student.user_name}`;
                studentSelect.appendChild(option);
            });
        }
    });
</script>
@endsection

