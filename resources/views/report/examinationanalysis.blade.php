@extends('layouts.app')
@section('title', 'Examination Analysis')
@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12">

          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
          </div>
          @endif

          <div class="card card-primary">
            <div class="card-body">
              <h6 class="mb-3">Examination Analysis Reports</h6>

              <form method="post" id="myForm" enctype="multipart/form-data">
                @csrf

              <div class="row">
               <div class="form-group col-lg-4">
                      <label>Exam Category</label>
                      <select name="testcategory" id="testcategory" class="select2" required>
                        <option value="">Select Category</option>
                        @foreach ($category as $row)
                        <option value="{{ $row }}" @selected($row==request('testcategory'))>
                          {{ $row }}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group col-lg-4">
                      <label>Exam Name</label>
                      <select name="test_name" id="testname" class="select2" required>
                        <option value="">Select Test</option>
                        @foreach ($exams as $row)
                        <option value="{{ $row}}" @selected($row==request('test_name'))> {{ $row }}</option>
                        @endforeach
                      </select>
                    </div>

              </div>
              
                <div class="row">
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.leastattempted') }}">Least Attempted Questions</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.commontracktopper') }}">SUbject Wise Marks</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.errorlist') }}">Error List Analysis</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.branchwisemarks') }}">Branch Wise Marks Analysis</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.sectionwisemarks') }}">Section Wise Marks Analysis</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.sectionwisetopper') }}">Section Wise Topper Marks</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.subjectwisemarks') }}">Student Subject Wise Analysis</button>
                   <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.overallmarkanalysis') }}">Overall Marks Analysis</button>
                   
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
  $('button.btn').on('click', function (e) {
    e.preventDefault();
    if($('#test_name').val() == '') return;
    let action = $(this).data('action');
    $('#myForm').attr('action', action).submit();
  });

$('#testcategory').change(() => window.location = `{{ route('report.exam_analyisis') }}?testcategory=${$('#testcategory').val()}`);

</script>
@endsection