@extends('layouts.app')
@section('title', 'Student Activity')
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
                    <form method="post" id="myForm" action="{{ route('studentactivity.update', $studentactivity->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <h6 class="col-deep-purple">Student Activity Entry</h6>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Section</label>
                                    <select name="section" id="section" class="form-control form-control-sm" required>
                                        <option value="">Select Section</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section }}" {{ $studentactivity->section == $section ? 'selected' : '' }}>{{ $section }}</option>
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
                                    <label>Date</label>
                                    <input type="text" name="date" class="form-control form-control-sm datetime-picker"
                                        value="{{ old('date', $studentactivity->date) }}" required>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Activity</label>
                                    <textarea name="reason" class="form-control form-control-sm">{{ old('reason', $studentactivity->reason) }}</textarea>
                                </div>

                                <div class="form-group col-lg-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
    flatpickr(".datetime-picker", {
        enableTime: false,
        allowInput: true,
        dateFormat: "Y-m-d",
        maxDate: "today",
        plugins: [
            new confirmDatePlugin({
                confirmText: "OK",
                showAlways: false,
                theme: "light"
            })
        ]
    });

    const allStudents = @json($students);
    const selectedSection = "{{ $studentactivity->section ?? '' }}";
    const selectedStudentId = "{{ $studentactivity->student_id ?? '' }}";

    const sectionDropdown = document.getElementById('section');
    const studentDropdown = document.getElementById('student_id');

    function populateStudents(section) {
        studentDropdown.innerHTML = '<option value="">Select Student</option>';
        const filtered = allStudents.filter(s => s.section === section);

        filtered.forEach(student => {
            const option = document.createElement('option');
            option.value = student.student_id;
            option.text = `${student.student_name} (${student.user_name})`;
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
