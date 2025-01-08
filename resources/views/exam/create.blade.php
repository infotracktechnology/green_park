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
                            <div class="form-group col-lg-3">
                                <label>Subject Name</label>
                                <select name="subject_name" id="subject_name" class="form-control form-control-sm" required>
                                    <option value="All">All</option>
                                    <option value="Physics">Physics</option>
                                    <option value="Chemistry">Chemistry</option>
                                    <option value="Botony">Botony</option>
                                    <option value="zoology">zoology</option>
                                 </select>
                              
                            </div>
                            <div class="form-group col-lg-2">
                                <label>Physics Questions</label>
                                <input type="number" min="1" name="physics_questions" class="form-control form-control-sm">
                            </div>
                            
                            <div class="form-group col-lg-2">
                                <label>Chemistry Questions</label>
                                <input type="number" min="1" name="chemistry_questions" class="form-control form-control-sm">
                            </div>
                            
                            <div class="form-group col-lg-2">
                                <label>Botony Questions</label>
                                <input type="number" min="1" name="botony_questions" class="form-control form-control-sm">
                            </div>

                            <div class="form-group col-lg-2">
                                <label>Zoology Questions</label>
                                <input type="number" min="1" name="zoology_questions" class="form-control form-control-sm">
                            </div>
                            
                            <div class="form-group col-lg-2">
                                <label>Total Questions</label>
                                <input type="number" min="1" name="total_questions" id="total_questions" class="form-control form-control-sm" required>
                            </div>
                            
                           



                             <div class="form-group col-lg-2">
                                <label>Physics Start No</label>
                                <input type="number" min="1" name="phy_start" class="form-control form-control-sm" required>
                            </div>



                            <div class="form-group col-lg-2">
                                <label>Chemistry Start No</label>
                                <input type="number" min="1" name="chem_start" class="form-control form-control-sm" required>
                            </div>



                            <div class="form-group col-lg-2">
                                <label>Botony Start No</label>
                                <input type="number" min="1" name="bot_start" class="form-control form-control-sm" required>
                            </div> 

                          <div class="form-group col-lg-2">
                                <label>Zoology Start No</label>
                                <input type="number" min="1" name="zoo_start" class="form-control form-control-sm" required>
                            </div>


                            <div class="form-group col-lg-3">
                                <label>Questions Files</label>
                                <input type="file" name="questions[]" class="form-control form-control-sm" multiple required accept="image/*" onchange="filesize(this);">
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
    if(["image/png", "image/jpeg", "image/jpg"].indexOf(file.type) == -1) {
        alert("Please upload a valid image file!");
        input.value = "";
    }
}
</script>

<script>
    const phyQuestionsInput = document.querySelector('input[name="physics_questions"]');
    const chemQuestionsInput = document.querySelector('input[name="chemistry_questions"]');
    const botonyQuestionsInput = document.querySelector('input[name="botony_questions"]');
    const zoologyQuestionsInput = document.querySelector('input[name="zoology_questions"]');
    const totalQuestionsInput = document.querySelector('input[name="total_questions"]');

    phyQuestionsInput.addEventListener('input', calculateTotalQuestions);
    chemQuestionsInput.addEventListener('input', calculateTotalQuestions);
    botonyQuestionsInput.addEventListener('input', calculateTotalQuestions);
    zoologyQuestionsInput.addEventListener('input', calculateTotalQuestions);

    function calculateTotalQuestions() {
        const phyQuestions = parseInt(phyQuestionsInput.value) || 0;
        const chemQuestions = parseInt(chemQuestionsInput.value) || 0;
        const botonyQuestions = parseInt(botonyQuestionsInput.value) || 0;
        const zoologyQuestions = parseInt(zoologyQuestionsInput.value) || 0;

        const totalQuestions = phyQuestions + chemQuestions + botonyQuestions + zoologyQuestions;

        totalQuestionsInput.value = totalQuestions;
    }
</script>

@endsection
