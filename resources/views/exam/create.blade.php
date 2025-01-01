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
                  <div class="card card-primary" x-data="{ questions:  [{'code': ''}]}">
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
                                    <option value="" disabled selected>Select Branch</option>
                                    <option value="">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
    

                              <div class="form-group col-lg-3">
                                 <label>Coaching Type</label>
                                 <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required>
                                    <option value="">Select Coaching Type</option>
                                    <option value="All">All</option>
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
                                    <option value="">Select Subject Name</option>
                                    <option value="All">All</option>
                                    <option value="Physics">Physics</option>
                                    <option value="Chemistry">Chemistry</option>
                                    <option value="Biology">Biology</option>
                                 </select>
                              
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Physics Questions</label>
                                <input type="number" min="1" name="phy_questions" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Chemistry Questions</label>
                                <input type="number" min="1" name="chem_questions" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Biology Questions</label>
                                <input type="number" min="1" name="bio_questions" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Total Questions</label>
                                <input type="number" min="1" name="total_questions" id="total_questions" class="form-control form-control-sm" required>
                            </div>
                            
                           



                            <div class="form-group col-lg-3">
                                <label>Physics Start NO</label>
                                <input type="number" min="1" name="phy_start" class="form-control form-control-sm" required>
                            </div>



                            <div class="form-group col-lg-3">
                                <label>Chemistry Start No</label>
                                <input type="number" min="1" name="chem_start" class="form-control form-control-sm" required>
                            </div>



                            <div class="form-group col-lg-3">
                                <label>Biology Start No</label>
                                <input type="number" min="1" name="bio_start" class="form-control form-control-sm" required>
                            </div>

                          


                            <div class="form-group col-lg-3">
                                <label>Questions Files</label>
                                <input type="file" name="questions[]" class="form-control form-control-sm" multiple required accept="image/*">
                            </div>
                            

                            
                                <div class="form-group col-lg-12">
                                   <button type="submit" class="btn btn-primary">Submit</button>
                                </div>

                            {{-- <div class="form-group col-lg-3">
                                <label>Total Duration (in hours)</label>
                                <input type="number" min="1" step="0.5" name="duration" class="form-control form-control-sm" required>
                            </div> --}}

                    

                             {{-- <div class="form-group col-lg-12">
                                <button type="button" x-on:click="questions.push({})" class="btn btn-warning">Add Question</button>
                             </div>

                           </div>

                            <template x-for="(question, index) in questions" :key="index">
                             <div class="row question" :key="index">
                            
                                <div class="form-group col-lg-2">
                                    <label>Question Code</label>
                                    <input type="text" :name="`question[${index}][code]`" class="form-control form-control-sm" required>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Image <span class="col-red">(size should be less than 250kb)</span></label>
                                    <input type="file" onchange="filesize(this);" :name="`question[${index}][image]`" class="form-control form-control-sm" required>
                                   
                                </div>
                                <div class="col-lg-1">
                                    <button type="button"  x-on:click="questions.splice(index, 1)" class="btn btn-danger mt-4"><i class="fa fa-trash"></i></button>
                                </div>
                             </div>
                            </template> --}}


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
    var file = input.files[0];
    if(file.size > 250000){
        alert("File size must not exceed 250kb!");
        input.value = "";
    }
    if(["image/png", "image/jpeg", "image/jpg"].indexOf(file.type) == -1) {
        alert("Please upload a valid image file!");
        input.value = "";
    }
}
$("#myForm").submit(function(e) {
    e.preventDefault();
    if($(".question").length == 0){
        alert("Please add at least one question");
        return;
    }
    this.submit();
})
</script>

<script>
    const phyQuestionsInput = document.querySelector('input[name="phy_questions"]');
    const chemQuestionsInput = document.querySelector('input[name="chem_questions"]');
    const bioQuestionsInput = document.querySelector('input[name="bio_questions"]');
    const totalQuestionsInput = document.querySelector('input[name="total_questions"]');

    phyQuestionsInput.addEventListener('input', calculateTotalQuestions);
    chemQuestionsInput.addEventListener('input', calculateTotalQuestions);
    bioQuestionsInput.addEventListener('input', calculateTotalQuestions);

    function calculateTotalQuestions() {
        const phyQuestions = parseInt(phyQuestionsInput.value) || 0;
        const chemQuestions = parseInt(chemQuestionsInput.value) || 0;
        const bioQuestions = parseInt(bioQuestionsInput.value) || 0;

        const totalQuestions = phyQuestions + chemQuestions + bioQuestions;

        totalQuestionsInput.value = totalQuestions;
    }
</script>

@endsection
