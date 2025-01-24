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
                     <form method="post" id="myForm" action="{{ route('exam.enableExam') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Enable Exam</h6>
                              </div>

                              @if ($errors->any())
                                  <div class="alert alert-danger">
                                      <ul>
                                          @foreach ($errors->all() as $error)
                                              <li>{{ $error }}</li>
                                          @endforeach
                                      </ul>
                                  </div>
                              @endif

                              @if (session('success'))
                                  <div class="alert alert-success">
                                      {{ session('success') }}
                                  </div>
                              @endif

                              <div class="form-group col-lg-3">
                                 <label>Test ID</label>
                                 <select name="test_id" class="form-control form-control-sm" required>
                                     <option value="">Select Test</option>
                                     @foreach ($tests as $test)
                                         <option value="{{ $test->id }}">{{ $test->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="form-group col-lg-3">
                              <label>Student</label>
                              <select name="student_id" class="form-control form-control-sm" required>
                                  <option value="">Select Student</option>
                                  @foreach ($students as $student)
                                      <option value="{{ $student->id }}">
                                          {{ $student->user_name ?? '' }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                          
                          
                          
                          
                          
                             
                              <div class="form-group col-lg-2">
                                 <label>&nbsp;</label>
                                 <button type="submit" class="btn btn-success btn-block">Enable</button>
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

@if (session('success'))
<script>
    alert("{{ session('success') }}");
</script>
@endif

@if (session('error'))
<script>
    alert("{{ session('error') }}");
</script>
@endif
@endsection