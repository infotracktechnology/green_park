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
                     <form method="post" id="myForm" action="{{ route('exam.enable') }}" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="card-body">
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Re-Enable</h6>
                              </div>

                              <div class="form-group col-lg-3">
                                 <label>Student ID</label>
                                 <input type="text" name="student_id" class="form-control" required>
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>Test ID</label>
                                 <input type="text" name="test_id" class="form-control" required>
                              </div>
                              <div class="form-group col-lg-2">
                                 <label>&nbsp;</label>
                                 <button type="submit" class="btn btn-success btn-block">Search</button>
                              </div>

                              {{-- <div class="form-group col-lg-12">
                                 <table class="table table-bordered">
                                    <thead>
                                       <tr>
                                          <th>Question</th>
                                          <th>Student Answer</th>
                                          <th>Correct Answer</th>
                                       </tr>
                                    </thead>
                                    <tbody id="student-test-data">
                                    </tbody>
                                 </table>
                              </div>
     --}}

                              
                            
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


</script>
@endsection