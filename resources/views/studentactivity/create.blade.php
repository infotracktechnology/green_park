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
                     <form method="post" id="myForm" action="{{ route('studentactivity.store') }}" enctype="multipart/form-data">
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
                                        <option value="{{ $section }}">{{ $section }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-lg-3">
                                <label>Student</label>
                                <select name="student_id" id="student_id" class="form-control select2" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-3">
                                <label>Date</label>
                                <input type="text" name="date" class="form-control form-control-sm datetime-picker" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="form-group col-lg-3">
                                <label>Activity </label>
                                <textarea name="reason" class="form-control form-control-sm"></textarea>
                            </div>

                            <div class="form-group col-lg-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
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

    document.getElementById('section').addEventListener('change', function () {
        const selectedSection = this.value;
        const studentSelect = document.getElementById('student_id');
        studentSelect.innerHTML = '<option value="">Select Student</option>';

        if (selectedSection) {
            const filtered = allStudents.filter(s => s.section === selectedSection);
            filtered.sort((a, b) => a.student_name.localeCompare(b.student_name));

            filtered.forEach(student => {
                const option = document.createElement('option');
                option.value = student.student_id;
                option.text = `${student.user_name} - ${student.student_name}`;
                studentSelect.appendChild(option);
            });
        }
    });
</script>
@endsection
