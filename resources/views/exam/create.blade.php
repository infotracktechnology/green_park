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
                     <form method="post" id="myForm" action="{{ route('exam.store') }}" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="card-body">
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Add Test</h6>
                              </div>

                              <div class="form-group col-lg-3">
                                <label>Branch</label>
                                <select name="branch_id" class="form-control form-control-sm"  required>
                                    <option value="{{ $branches->implode('id', ',') }}">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
    

                              <div class="form-group col-lg-3">
                                 <label>Coaching Type</label>
                                 <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required>
                                    <option value="Offline,Online Recorded,Online Live">All</option>
                                    <option value="Offline">Offline</option>
                                    <option value="Online Recorded">Online Recorded</option>
                                    <option value="Online Live">Online Live</option>
                                 </select>
                              </div>

                              <div class="form-group col-lg-3">
                                <label>Test Name</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                            </div>
                        
                            <div class="form-group  col-lg-12 d-flex align-items-star ">
                              
                                <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                                    <input type="checkbox" id="physicsCheckbox" name=subject_name[] value="Physics" class="form-check-input">
                                    <label for="physicsCheckbox" class="form-check-label">Physics</label>
                                </div>
                                <div class="col-lg-3 physics-inputs" style="display: none;">
                                    <label for="physicsQuestions">Physics Questions</label>
                                    <input type="number" min="1" name="physics_questions" id="physicsQuestions" class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col-lg-3 physics-inputs" style="display: none;">
                                    <label for="physicsStart">Physics Start No</label>
                                    <input type="number" min="1" name="phy_start" id="physicsStart" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            
                            <div class="form-group  col-lg-12 d-flex align-items-star ">
                               
                                <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                                    <input type="checkbox" id="chemistryCheckbox" name=subject_name[] value="Chemistry" class="form-check-input">
                                    <label for="chemistryCheckbox" class="form-check-label">Chemistry</label>
                                </div>
                                <div class="col-lg-3 chemistry-inputs" style="display: none;">
                                    <label for="chemistryQuestions">Chemistry Questions</label>
                                    <input type="number" min="1" name="chemistry_questions" id="chemistryQuestions" class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col-lg-3 chemistry-inputs" style="display: none;">
                                    <label for="chemistryStart">Chemistry Start No</label>
                                    <input type="number" min="1" name="chem_start" id="chemistryStart" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            
                            <div class="form-group  col-lg-12 d-flex align-items-star ">
                               
                                <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                                    <input type="checkbox" id="botanyCheckbox" name=subject_name[] value="Botany" class="form-check-input">
                                    <label for="botanyCheckbox" class="form-check-label">Botany</label>
                                </div>
                                <div class="col-lg-3 botany-inputs" style="display: none;">
                                    <label for="botanyQuestions">Botany Questions</label>
                                    <input type="number" min="1" name="botony_questions" id="botanyQuestions" class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col-lg-3 botany-inputs" style="display: none;">
                                    <label for="botanyStart">Botany Start No</label>
                                    <input type="number" min="1" name="bot_start" id="botanyStart" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            
                            <div class="form-group  col-lg-12 d-flex align-items-star ">
                               
                                <div class="col-lg-3 form-check d-flex flex-column align-items-start">
                                    <input type="checkbox" id="zoologyCheckbox" name=subject_name[] value="Zoology" class="form-check-input">
                                    <label for="zoologyCheckbox" class="form-check-label">Zoology</label>
                                </div>
                                <div class="col-lg-3 zoology-inputs" style="display: none;">
                                    <label for="zoologyQuestions">Zoology Questions</label>
                                    <input type="number" min="1" name="zoology_questions" id="zoologyQuestions" class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col-lg-3 zoology-inputs" style="display: none;">
                                    <label for="zoologyStart">Zoology Start No</label>
                                    <input type="number" min="1" name="zoo_start" id="zoologyStart" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-12 row">
                                <div class="form-group col-lg-2">
                                    <label>Total Questions</label>
                                    <input type="number" min="1" name="total_questions" id="total_questions" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>Questions Files</label>
                                    <input type="file" name="questions[]" class="form-control form-control-sm" multiple required accept="image/*" onchange="filesize(this);">
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
function filesize(input) {
    const totalQuestions = parseInt(document.getElementById('total_questions').value);
    const files = input.files;

    if (files.length !== totalQuestions) {
        alert("The number of uploaded files must match the total number of questions exactly.");
        input.value = ""; 
    }
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjects = [
            { checkbox: 'physicsCheckbox', inputs: 'physics-inputs' },
            { checkbox: 'chemistryCheckbox', inputs: 'chemistry-inputs' },
            { checkbox: 'botanyCheckbox', inputs: 'botany-inputs' },
            { checkbox: 'zoologyCheckbox', inputs: 'zoology-inputs' }
        ];
    
        subjects.forEach(subject => {
            const checkbox = document.getElementById(subject.checkbox);
            const inputs = document.querySelectorAll(`.${subject.inputs}`);
    
            checkbox.addEventListener('change', function () {
                const isChecked = this.checked;
                inputs.forEach(input => {
                    input.style.display = isChecked ? 'block' : 'none';
                    const inputElement = input.querySelector('input');
                    if (inputElement) {
                        inputElement.disabled = !isChecked;
                        if (!isChecked) {
                            inputElement.value = '';
                        }
                    }
                });
                
            });
            
        });
       
    });
    
    </script>
    
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const subjects = [
        { checkbox: 'physicsCheckbox', input: 'physicsQuestions' },
        { checkbox: 'chemistryCheckbox', input: 'chemistryQuestions' },
        { checkbox: 'botanyCheckbox', input: 'botanyQuestions' },
        { checkbox: 'zoologyCheckbox', input: 'zoologyQuestions' }
    ];

    const totalQuestionsInput = document.getElementById('total_questions');

    subjects.forEach(subject => {
        const checkbox = document.getElementById(subject.checkbox);
        const input = document.getElementById(subject.input);
 checkbox.addEventListener('change', function () {
            input.disabled = !checkbox.checked;
            input.parentElement.style.display = checkbox.checked ? 'block' : 'none';
            if (!checkbox.checked) input.value = ''; 
            calculateTotalQuestions();
        });

       
        input.addEventListener('input', calculateTotalQuestions);
    });

    function calculateTotalQuestions() {
        let total = 0;
        subjects.forEach(subject => {
            const input = document.getElementById(subject.input);
            total += parseInt(input.value) || 0;
        });
        totalQuestionsInput.value = total;
    }
});

</script>
<script>
    function validateForm() {
        const totalQuestions = parseInt(document.getElementById('total_questions').value);
        const startNumbers = [
            { input: 'physicsStart', name: 'Physics' },
            { input: 'chemistryStart', name: 'Chemistry' },
            { input: 'botanyStart', name: 'Botany' },
            { input: 'zoologyStart', name: 'Zoology' }
        ];

        for (let i = 0; i < startNumbers.length; i++) {
            const startInput = document.getElementById(startNumbers[i].input);
            if (startInput && startInput.value) {
                const startNumber = parseInt(startInput.value);
                if (startNumber > totalQuestions) {
                    alert(`${startNumbers[i].name} start number cannot be higher than the total number of questions.`);
                    return false;
                }
            }
        }
        return true;
    }
</script>
@endsection