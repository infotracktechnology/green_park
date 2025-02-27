@extends('layouts.app')

@section('title', 'Examinations')
@section('css')

@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
             <div class="col-12">
                  <div class="card card-primary">
                     <form method="post" id="myForm" action="{{ route('exam.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Add Test</h6>
                              </div>

                              <div class="form-group col-lg-3">
                                <label>Branch</label>
                                <select name="branch_id[]" class="select" multiple required>
                                    <option value="">Select Branch</option>
                                    <option value="{{ $branches->implode('id', ',') }}">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
    

                              <div class="form-group col-lg-3">
                                 <label>Coaching Type</label>

                                 <select name="coaching_type[]" id="coaching_type" class="select" multiple required>
                                    <option value="">Select Coaching Type</option>
                                    <option value="Offline,Online Recorded,Online Live,Test Series,11,12">All</option>
                                    <option value="Offline">Offline</option>
                                    <option value="Online Recorded">Online Recorded</option>
                                    <option value="Online Live">Online Live</option>
                                    <option value="Test Series">Test Series</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                 </select>
                              </div>
                              <div class="form-group col-lg-3">
                                <label>Test ID <span class="text-danger">(should be unique*)</span></label>
                                <input type="number" name="id" id="id" class="form-control form-control-sm numberk @error('id') is-invalid @enderror" required>
                                @error('id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                              <div class="form-group col-lg-3">
                                <label>Test Name</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" required>
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

                                <div class="col-lg-2 botany-inputs" style="display: none;">
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

// function validateForm() {
//     const totalQuestions = parseInt(document.getElementById('total_questions').value);
//     const startNumbers = [
//         { input: 'physicsStart', name: 'Physics' },
//         { input: 'chemistryStart', name: 'Chemistry' },
//         { input: 'botanyStart', name: 'Botany' },
//         { input: 'zoologyStart', name: 'Zoology' }
//     ];

//     for (let i = 0; i < startNumbers.length; i++) {
//         const startInput = document.getElementById(startNumbers[i].input);
//         if (startInput && startInput.value) {
//             const startNumber = parseInt(startInput.value);
//             if (startNumber > totalQuestions) {
//                 alert(`${startNumbers[i].name} start number cannot be higher than the total number of questions.`);
//                 return false;
//             }
//         }
//     }

//     const subjects = [
//         { checkbox: 'physicsCheckbox', questions: 'physicsQuestions', files: 'physicsFile' },
//         { checkbox: 'chemistryCheckbox', questions: 'chemistryQuestions', files: 'chemistryFile' },
//         { checkbox: 'botanyCheckbox', questions: 'botanyQuestions', files: 'botanyFile' },
//         { checkbox: 'zoologyCheckbox', questions: 'zoologyQuestions', files: 'zoologyFile' }
//     ];

//     for (let i = 0; i < subjects.length; i++) {
//         const checkbox = document.getElementById(subjects[i].checkbox);
//         if (checkbox.checked) {
//             const questionCount = parseInt(document.getElementById(subjects[i].questions).value);
//             const fileInput = document.getElementById(subjects[i].files);
//             const files = fileInput.files;

//             if (files.length !== questionCount) {
//                 alert(`The number of uploaded files must match the number of questions exactly for ${subjects[i].checkbox.replace('Checkbox', '')}.`);
//                 return false;
//             }
//         }
//     }

//     return true;
// }
</script>
@endsection