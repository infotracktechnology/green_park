@extends('layouts.app')

@section('title', 'Answer key Upload')
@section('css')

@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary">
                    <form method="post" id="myForm" action="{{ route('exam.answerkey.upload') }}" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="card-body">

                           <div class="col-md-8 col-sm-12 mb-3">
                              <h6 class="col-deep-purple">Offline/OMR Upload</h6>
                             </div>
                            <div class="row">
    
                               <!-- Answer Key Upload Input -->
                               <div class="form-group col-lg-4">
                                 <label>&nbsp;</label>
                                  <input type="file" name="answer_key" class="form-control form-control-sm" accept=".csv" required>
                                  @if ($errors->has('answer_key'))
                                      <span class="text-danger">{{ $errors->first('answer_key') }}</span>
                                  @endif
                               </div>
    
                               <div class="form-group col-lg-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Upload</button>
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