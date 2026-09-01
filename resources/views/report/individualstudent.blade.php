@extends('layouts.app')
@section('title', "Individual Student Report")

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          <div class="card card-primary">
            <div class="card-header">
              <h4>Individual Student Report</h4>
            </div>

            <div class="card-body">

              @if(session('error'))
                  <div class="alert alert-danger">
                      {{ session('error') }}
                  </div>
              @endif

              <form method="POST" id="myForm" action="{{ route('report.individualstudent') }}">
                  @csrf

                  <div class="row">

                      <div class="form-group col-md-3" id="student_div">
                          <label><b>Student</b></label>
                          <select name="student_id" class="form-control select2" required>
                              <option value="">Select Student</option>
                              @foreach($students as $student)
                                  <option value="{{ $student->student_id }}">
                                      {{ $student->student_id }} - {{ $student->student_name }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
   
                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    
                  <button class="btn btn-primary mt-4"> Download Report </button>
                  
                  </div>

                  </div>    

              </form>

            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script>

    $('.select2').select2({
        width: '100%'
    });

</script>
@endsection