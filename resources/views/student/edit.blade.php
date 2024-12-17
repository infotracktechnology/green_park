@extends('layouts.app')
@section('title', 'Student Edit')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
         <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                   <form method="post" id="myForm" x-data="{ formStep: 1 }" action="{{ route('student.update', $student->id) }}" enctype="multipart/form-data">
                      @csrf
                      @method('PUT')

                    <div class="card-body">

                    <div class="row" x-cloak x-show="formStep === 1">

                      <div class="col-md-12 col-sm-12 mb-3">
                          <h6 class="col-deep-purple">Pesronal Details</h6>
                      </div>

                      <div class="form-group col-lg-3">
                        <label>Admission Date</label>
                         <input type="date" name="admission_date"  class="form-control form-control-sm" >
                    </div>
 

                    </div>

                    <div class="row" x-cloak x-show="formStep === 2">

                      <div class="col-md-12 col-sm-12 mb-3">
                          <h6 class="col-deep-purple">Update Address</h6>
                      </div>

                      <div class="form-group col-lg-3">
                        <label>Admission Date</label>
                         <input type="date" name="admission_date"  class="form-control form-control-sm" >
                    </div>
 

                    </div>


                      <div class="row" x-cloak x-show="formStep === 3">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple">Parent Details</h6>
                     </div>

                     <div class="form-group col-lg-3">
                        <label>Admission Date</label>
                         <input type="date" name="admission_date"  class="form-control form-control-sm" >
                    </div>
 
                      </div>


                      <div class="row" x-cloak x-show="formStep === 4">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple">Academic Details</h6>  
                        </div>

                        <div class="form-group col-lg-3">
                           <label>Admission Date</label>
                            <input type="date" name="admission_date"  class="form-control form-control-sm" >
                       </div>
    

                      </div>

                        <div class="row" x-cloak x-show="formStep === 5">

                           <div class="col-md-12 col-sm-12 mb-3">
                               <h6 class="col-deep-purple">Mark Details (if passed)</h6>
                     </div>

                     <div class="form-group col-lg-3">
                        <label>Admission Date</label>
                         <input type="date" name="admission_date"  class="form-control form-control-sm" >
                    </div>
 
                        </div>

               <div class="row" x-cloak x-show="formStep === 6">
                     <div class="col-md-12 col-sm-12 mb-3">
                     <h6 class="col-deep-purple">Pervious Neet Center</h6>
                     </div>

                  <div class="form-group col-lg-3">
                     <label>Admission Date</label>
                      <input type="date" name="admission_date"  class="form-control form-control-sm" >
                 </div>

                    </div>


                    <div class="row">
                     <div class="col-md-3 offset-md-9 col-sm-12">
                       <button x-cloak x-show="formStep > 1" x-on:click="formStep -= 1" type="button" class="btn btn-warning">
                        <i class="fas fa-arrow-left"></i> Back 
                        </button>

                       <button x-cloak x-show="formStep < 6" x-on:click="formStep += 1" type="button" class="btn btn-primary">
                          Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                      
                        <button x-cloak x-show="formStep === 6" type="submit" class="btn btn-success">Submit</button>
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


@endsection
