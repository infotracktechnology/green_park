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
   
        <div class="form-group col-lg-3">
            <label>Class</label>
            <input type="text" name="class" value="{{ old('class', $sickroom->class) }}" class="form-control form-control-sm" required>
         </div>

        <div class="form-group col-lg-3">
            <label>Section</label>
            <select name="section" id="section" class="form-control form-control-sm" required>
                <option value="">Select Section</option>
                @foreach($sections as $section)
                    <option value="{{ $section }}" {{ $sickroom->section == $section ? 'selected' : '' }}>{{ $section }}</option>
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
            <input type="text" name="room_no" value="{{ old('room_no', $sickroom->room_no) }}" class="form-control form-control-sm" required>
        </div>

        <div class="form-group col-lg-3">
            <label>In Time</label>
            <input type="text" id="in_time" name="in_time" class="datetime-picker form-control form-control-sm" value="{{ $sickroom->in_time }}" required>
        </div>
        
        <div class="form-group col-lg-3">
            <label>Out Time</label>
            <input type="text" name="out_time" class="datetime-picker form-control form-control-sm" value="{{ $sickroom->out_time  }}" id="out_time">
            <span id="out_time_error" class="text-danger"></span>
        </div>
        
        <div class="form-group col-lg-3">
            <label>Reason</label>
            <textarea name="reason" class="form-control form-control-sm">{{ old('reason', $sickroom->reason) }}</textarea>
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
    const selectedSection = "{{ $sickroom->section }}";
    const selectedStudentId = "{{ $sickroom->student_id }}";

    const sectionDropdown = document.getElementById('section');
    const studentDropdown = document.getElementById('student_id');

    function populateStudents(section) {
        studentDropdown.innerHTML = '<option value="">Select Student ID</option>';
        const filtered = allStudents.filter(s => s.section === section);

        filtered.forEach(student => {
            const option = document.createElement('option');
            option.value = student.student_id;
            option.text = `${student.student_id} - ${student.user_name}`;
            if (student.student_id == selectedStudentId) {
                option.selected = true;
            }
            studentDropdown.appendChild(option);
        });
    }
    if (selectedSection) {
        populateStudents(selectedSection);
    }

    sectionDropdown.addEventListener('change', function () {
        populateStudents(this.value);
    });
</script>


@endsection

