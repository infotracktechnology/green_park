@extends('layouts.app')

@section('title', 'Enable Exam')
@section('css')

@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
         <div class="row">
            <div class="col-md-12 col-sm-12">

                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
             @endif
             
               <div class="card card-primary">
                  <form method="post" id="myForm" action="{{ route('exam.enableExam') }}" enctype="multipart/form-data">
                     @csrf
                     <div class="card-body">
                        <div class="row">

                           <!-- Test ID Dropdown -->
                           <div class="form-group col-lg-3">
                              <label>Test ID</label>
                              <select name="test_id" id="test_id" class="form-control form-control-sm " required>
                                 <option value="">Select Test</option>
                                 @foreach ($tests as $test)
                                    <option value="{{ $test->id }}" @if($test->id == $testId) selected @endif>{{ $test->name }}</option>
                                 @endforeach
                              </select>
                           </div>

                           <div class="form-group col-lg-3">
                              <label>Student</label>
                              <select name="student_id" id="student_id" class="form-control form-control-sm select2" required>
                                  <option value="">Select Student</option>
                                  @foreach ($students as $student)
                                      <option value="{{ $student->id }}">{{ $student->name }}</option>
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

<script>
   $(document).ready(function() {
       $('.select2').select2({
           placeholder: "Select an option",
           allowClear: true
       });
   });
</script>
<script>
 $('#test_id').on('change', function() {
    var testId = $(this).val();

    if (testId) {
        $.ajax({
            url: "{{ route('exam.enable') }}",  
            type: "GET",
            data: { 
                test_id: testId,
                _token: "{{ csrf_token() }}"  
            },
            success: function(data) {
                var studentSelect = $('#student_id');
                studentSelect.empty();  
                studentSelect.append('<option value="">Select Student</option>');

               
                $.each(data, function(key, student) {
                    studentSelect.append('<option value="' + student.id + '">' + student.user_name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + error);
            }
        });
    } else {
        $('#student_id').empty().append('<option value="">Select Student</option>');
    }
});

</script>
@endsection