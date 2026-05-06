@extends('layouts.app')
@section('title', 'Examinations')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">

         @if(session('success'))
          <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
          @endif

           @if(session('error'))
          <div class="alert alert-danger alert-dismissible show fade">{{ session('error') }}</div>
          @endif

          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('exam.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Add Offline Exam</h6>
                  </div>

                  <input type="hidden" name="examtype" value="OFFLINE">
                  <div class="form-group col-lg-3">
                    <label for="branch">Select Academic Year:</label>
                    <select name="academic_year" id="academic_year" class="form-control form-control-sm">
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP">GROUP</option>
                      <option value="INDIVIDUAL">INDIVIDUAL STUDENT</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All">All Gender</option>
                      <option value="MALE">MALE</option>
                      <option value="FEMALE">FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                    </select>
                  </div>

                 <div class="form-group col-lg-2">
                    <label>Test Category </label>
                    <select name="testcategory" class="form-control form-control-sm" required>
                      <option value="">Select Test Category</option>
                      @foreach ($testcategory as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-2">
                    <label>Exam ID (in the 4 ids enter any one)</label>
                    <input type="number" name="testid" class="form-control form-control-sm numberk" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Exam Name (Unique*)</label>
                    <input type="text" name="name" id="name" class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                    @error('name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Exam Date</label>
                    <input type="date" value="{{ date('Y-m-d') }}" name="exam_date" class="form-control form-control-sm">
                  </div>
                </div>

                  <div class="row m-2">

                    <div class="col-lg-1 form-check">
                      <input type="checkbox" id="physicsCheckbox" name="subject_name[]" value="Physics" class="form-check-input">
                      <label for="physicsCheckbox" class="form-check-label">Physics</label>
                    </div>
                    <div class="col-lg-2 physics-inputs" style="display: none;">
                      <label for="physicsQuestions">Physics Questions</label>
                      <input type="number" min="1" max="180" name="physics_questions" id="physicsQuestions" class="form-control form-control-sm" disabled>
                    </div>
                  </div>

                  <div class="row m-2">

                    <div class="col-lg-1 form-check">
                      <input type="checkbox" id="chemistryCheckbox" name="subject_name[]" value="Chemistry" class="form-check-input">
                      <label for="chemistryCheckbox" class="form-check-label">Chemistry</label>
                    </div>
                    <div class="col-lg-2 chemistry-inputs" style="display: none;">
                      <label for="chemistryQuestions">Chemistry Questions</label>
                      <input type="number" min="1" max="180" name="chemistry_questions" id="chemistryQuestions" class="form-control form-control-sm" disabled>
                    </div>
                  </div>

                  <div class="row m-2">

                    <div class="col-lg-1 form-check">
                      <input type="checkbox" id="botanyCheckbox" name="subject_name[]" value="Botany" class="form-check-input">
                      <label for="botanyCheckbox" class="form-check-label">Botany</label>
                    </div>
                    <div class="col-lg-2 botany-inputs" style="display: none;">
                      <label for="botanyQuestions">Botany Questions</label>
                      <input type="number" min="1" max="180" name="botany_questions" id="botanyQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    
                  </div>

                  <div class="row m-2">

                    <div class="col-lg-1 form-check">
                      <input type="checkbox" id="zoologyCheckbox" name="subject_name[]" value="Zoology" class="form-check-input">
                      <label for="zoologyCheckbox" class="form-check-label">Zoology</label>
                    </div>
                    <div class="col-lg-2 zoology-inputs" style="display: none;">
                      <label for="zoologyQuestions">Zoology Questions</label>
                      <input type="number" min="1" max="180" name="zoology_questions" id="zoologyQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    
                  </div>

                  <div class="row m-2">
                    <div class="col-lg-1 form-check">
                      <input type="checkbox" id="mathsCheckbox" name="subject_name[]" value="Mathematics" class="form-check-input">
                      <label for="mathsCheckbox" class="form-check-label">Mathematics</label>
                    </div>
                    <div class="col-lg-2 maths-inputs" style="display: none;">
                      <label for="mathsQuestions">Maths Questions</label>
                      <input type="number" min="1" max="180" name="maths_questions" id="mathsQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    
                  </div>

                  <div class="row">
                    <div class="form-group col-lg-2">
                      <label>Total Questions</label>
                      <input type="number" min="1" name="total_questions" id="total_questions" class="form-control form-control-sm" required>
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
<script>
  const subjects = [
    { key: 'physics' },
    { key: 'chemistry' },
    { key: 'botany' },
    { key: 'zoology' },
    { key: 'maths' }
  ];

  const $total = $('#total_questions');
  const $course = $('#course');

 function toggleSubjects() {
  var course = $course.val()?.toUpperCase();
  var jee = $('#mathsCheckbox').closest('.row');
  var neet = $('#botanyCheckbox, #zoologyCheckbox').closest('.row');

  if (course === 'JEE') {
    jee.show();
    neet.hide().find('input[type=checkbox]').prop('checked', false).trigger('change');
  } else {
    jee.hide().find('input[type=checkbox]').prop('checked', false).trigger('change');
    neet.show();
  }
}
 
  $course.on('change', toggleSubjects);

  toggleSubjects();
  $.each(subjects, function(_, s) {
    const $check = $('#' + s.key + 'Checkbox');
    const $inputs = $('.' + s.key + '-inputs');
    const $questions = $('#' + s.key + 'Questions');

    $check.on('change', function() {
      const checked = this.checked;
      $inputs.toggle(checked);
      $inputs.find('input').each(function() { 
        this.disabled = !checked;
        if(this.type !== 'file') this.required = checked;
      });
      if (!checked) $inputs.find('input').val('');
      calcTotal();
    });

    $questions.on('input', calcTotal);
  });

  function calcTotal() {
    let total = 0;
    $.each(subjects, function(_, s) {
      total += parseInt($('#' + s.key + 'Questions').val()) || 0;
    });
    $total.val(total);
  }
</script>
@endsection
