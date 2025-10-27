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
                <div class="form-group col-lg-4">
                  <label>Test Name</label>
                  <select name="test_name" id="test_name" class="select2 form-control" required>
                    <option value="">Select Test</option>
                    @foreach ($tests as $test)
                    <option value="{{ $test->name }}">{{ $test->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="row">
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.leastattempted') }}">Least Attempted Questions</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.commontracktopper') }}">Common Track Wise Objective Toppers</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.errorlist') }}">Error List Analysis</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.branchwisemarks') }}">Objective Branch Wise Total Marks</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.sectionwisemarks') }}">Objective Section Wise Total Marks</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.sectionwisetopper') }}">Section Wise Topper Marks</button>
                    <button type="button" class="btn btn-primary m-2" data-action="{{ route('report.subjectwisemarks') }}">Student Subject Wise Marks</button>
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
</script>
@endsection