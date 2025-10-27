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

          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('exam.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Add Test</h6>
                  </div>


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

                  <div class="form-group col-lg-3">
                    <label>Test Category</label>
                    <input type="text" name="testcategory"  class="form-control form-control-sm" required>
                  </div>



                  <div class="form-group col-lg-2">
                    <label>Test ID</label>
                    <input type="number" name="testid" class="form-control form-control-sm numberk" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Test Name</label>
                    <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Exam Date</label>
                    <input type="date" value="{{ date('Y-m-d') }}" name="exam_date" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-12 d-flex align-items-start">

                    <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                      <input type="checkbox" id="physicsCheckbox" name="subject_name[]" value="Physics" class="form-check-input">
                      <label for="physicsCheckbox" class="form-check-label">Physics</label>
                    </div>
                    <div class="col-lg-2 physics-inputs" style="display: none;">
                      <label for="physicsQuestions">Physics Questions</label>
                      <input type="number" min="1" max="180" name="physics_questions" id="physicsQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-lg-2 physics-inputs" style="display: none;">
                      <label for="physicsStart">Physics Start No</label>
                      <input type="number" min="1" name="phy_start" id="physicsStart" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-2 physics-inputs" style="display: none;">
                      <label>Physics End No</label>
                      <input type="number" min="1" name="phy_end" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-3 physics-inputs" style="display: none;">
                      <label for="physicsFile">Physics Files</label>
                      <input type="file" name="physics_files[]" id="physicsFile" class="form-control form-control-sm" accept="image/*" multiple disabled onchange="validateFileCount('physicsQuestions', 'physicsFile')">
                    </div>
                  </div>

                  <div class="form-group col-lg-12 d-flex align-items-start">

                    <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                      <input type="checkbox" id="chemistryCheckbox" name="subject_name[]" value="Chemistry" class="form-check-input">
                      <label for="chemistryCheckbox" class="form-check-label">Chemistry</label>
                    </div>
                    <div class="col-lg-2 chemistry-inputs" style="display: none;">
                      <label for="chemistryQuestions">Chemistry Questions</label>
                      <input type="number" min="1" max="180" name="chemistry_questions" id="chemistryQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-lg-2 chemistry-inputs" style="display: none;">
                      <label for="chemistryStart">Chemistry Start No</label>
                      <input type="number" min="1" name="chem_start" id="chemistryStart" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-2 chemistry-inputs" style="display: none;">
                      <label>Chemistry End No</label>
                      <input type="number" min="1" name="chem_end" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-3 chemistry-inputs" style="display: none;">
                      <label for="chemistryFile">Chemistry Files</label>
                      <input type="file" name="chemistry_files[]" id="chemistryFile" class="form-control form-control-sm" accept="image/*" multiple disabled onchange="validateFileCount('chemistryQuestions', 'chemistryFile')">
                    </div>
                  </div>

                  <div class="form-group col-lg-12 d-flex align-items-start">

                    <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                      <input type="checkbox" id="botanyCheckbox" name="subject_name[]" value="Botany" class="form-check-input">
                      <label for="botanyCheckbox" class="form-check-label">Botany</label>
                    </div>
                    <div class="col-lg-2 botany-inputs" style="display: none;">
                      <label for="botanyQuestions">Botany Questions</label>
                      <input type="number" min="1" max="180" name="botony_questions" id="botanyQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-lg-2 botany-inputs" style="display: none;">
                      <label for="botanyStart">Botany Start No</label>
                      <input type="number" min="1" name="bot_start" id="botanyStart" class="form-control form-control-sm" disabled>
                    </div>


                    <div class="col-lg-2 botany-inputs" style="display: none;">
                      <label>Botany End No</label>
                      <input type="number" min="1" name="bot_end" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-3 botany-inputs" style="display: none;">
                      <label for="botanyFile">Botany Files</label>
                      <input type="file" name="botany_files[]" id="botanyFile" class="form-control form-control-sm" accept="image/*" multiple disabled onchange="validateFileCount('botanyQuestions', 'botanyFile')">
                    </div>
                  </div>

                  <div class="form-group col-lg-12 d-flex align-items-start">

                    <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                      <input type="checkbox" id="zoologyCheckbox" name="subject_name[]" value="Zoology" class="form-check-input">
                      <label for="zoologyCheckbox" class="form-check-label">Zoology</label>
                    </div>
                    <div class="col-lg-2 zoology-inputs" style="display: none;">
                      <label for="zoologyQuestions">Zoology Questions</label>
                      <input type="number" min="1" max="180" name="zoology_questions" id="zoologyQuestions" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-lg-2 zoology-inputs" style="display: none;">
                      <label for="zoologyStart">Zoology Start No</label>
                      <input type="number" min="1" name="zoo_start" id="zoologyStart" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-2 zoology-inputs" style="display: none;">
                      <label>Zoology End No</label>
                      <input type="number" min="1" name="zoo_end" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-lg-3 zoology-inputs" style="display: none;">
                      <label for="zoologyFile">Zoology Files</label>
                      <input type="file" name="zoology_files[]" id="zoologyFile" class="form-control form-control-sm" accept="image/*" multiple disabled onchange="validateFileCount('zoologyQuestions', 'zoologyFile')">
                    </div>
                  </div>

                  <div class="form-group col-lg-12 row">
                    <div class="form-group col-lg-2">
                      <label>Total Questions</label>
                      <input type="number" min="1" name="total_questions" id="total_questions" class="form-control form-control-sm" required>
                    </div>

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

  <div class="modal fade" id="categoryModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Test Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{ route('exam.testcategory') }}" method="post" enctype="multipart/form-data">
            @csrf
          <div class="row">
            <div class="form-group col-12">
               <label>Category Name</label>
              <input type="text" name="category" class="form-control form-control-sm"  required>
            </div>

            <div class="form-group col-12">
              <button type="submit" class="btn btn-primary">Add</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</div>
@endsection
@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const subjects = [
          { checkbox: 'physicsCheckbox', inputs: 'physics-inputs', questions: 'physicsQuestions', files: 'physicsFile' },
          { checkbox: 'chemistryCheckbox', inputs: 'chemistry-inputs', questions: 'chemistryQuestions', files: 'chemistryFile' },
          { checkbox: 'botanyCheckbox', inputs: 'botany-inputs', questions: 'botanyQuestions', files: 'botanyFile' },
          { checkbox: 'zoologyCheckbox', inputs: 'zoology-inputs', questions: 'zoologyQuestions', files: 'zoologyFile' }
      ];
  
      const totalQuestionsInput = document.getElementById('total_questions');
  
      subjects.forEach(subject => {
          const checkbox = document.getElementById(subject.checkbox);
          const inputs = document.querySelectorAll(`.${subject.inputs}`);
          const questionInput = document.getElementById(subject.questions);
          const fileInput = document.getElementById(subject.files);
  
          checkbox.addEventListener('change', function () {
              const isChecked = this.checked;
              inputs.forEach(input => {
                  input.style.display = isChecked ? 'block' : 'none';
                  const inputElement = input.querySelector('input');
                  if (inputElement) {
                      inputElement.disabled = !isChecked;
                      if (inputElement.type !== 'file') {
                          inputElement.required = isChecked;
                      }
                      if (!isChecked) {
                          inputElement.value = '';
                      }
                  }
              });
              calculateTotalQuestions();
          });
  
          questionInput.addEventListener('input', calculateTotalQuestions);
          fileInput.addEventListener('change', function () {
              validateFileCount(subject.questions, subject.files);
          });
      });
  
      function calculateTotalQuestions() {
          let total = 0;
          subjects.forEach(subject => {
              const input = document.getElementById(subject.questions);
              total += parseInt(input.value) || 0;
          });
          totalQuestionsInput.value = total;
      }
  });
  
  function validateFileCount(questionInputId, fileInputId) {
      const questionCount = parseInt(document.getElementById(questionInputId).value);
      const fileInput = document.getElementById(fileInputId);
      const files = fileInput.files;
  
      if (files.length !== questionCount) {
          alert(`The number of uploaded files must match the number of questions exactly for ${questionInputId.replace('Questions', '')}.`);
          fileInput.value = ""; 
      }
  }
  </script>

@endsection