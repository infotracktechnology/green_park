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
                    <button type="button" class="btn btn-primary m-2" data-toggle="modal" data-target="#rangeModal">Range Report</button>
                   
                </div>
              </form>

            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>
<!-- Range Report Modal -->
<div class="modal fade" id="rangeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Range Report
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST"id="rangeReportForm" action="{{ route('report.rangereport') }}" target="rangeDownloadFrame">
                @csrf
                <input type="hidden" name="testcategory" id="range_testcategory">
                <input type="hidden" name="test_name" id="range_test_name">

                <div class="modal-body">
                    <label>
                        <strong>Enter Marks</strong>
                    </label>
                    <div id="rangeRows">
                        <div class="row range-row mb-2">
                            <div class="col-md-8">
                                <input type="number" name="range_mark[]" class="form-control" placeholder="Enter Mark" required>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-danger remove-range"> Remove </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addRange" class="btn btn-success mt-2"> + Add Mark </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button  class="btn btn-primary"> submit</button>
                </div>
            </form>
            <iframe name="rangeDownloadFrame" id="rangeDownloadFrame" style="display:none;"></iframe>
        </div>

    </div>

</div>
@endsection

@section('js')
<script>
  $('button[data-action]').on('click', function (e) {
    e.preventDefault();
    if($('#test_name').val() == '') return;
    let action = $(this).data('action');
    $('#myForm').attr('action', action).submit();
  });


 $('#rangeModal').on('show.bs.modal', function () {
        $('#range_testcategory').val(
            $('#testcategory').val()
        );
        $('#range_test_name').val(
            $('#testname').val()
        );
    });

    $('#addRange').on('click', function () {
        let row = `
            <div class="row range-row mb-2">
                <div class="col-md-8">
                    <input type="number" name="range_mark[]" class="form-control" placeholder="Enter Mark" required>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-danger remove-range"> Remove </button>
                </div>
            </div>
        `;
        $('#rangeRows').append(row);
    });

    $(document).on('click', '.remove-range', function () {
        if ($('.range-row').length > 1) {
            $(this).closest('.range-row').remove();
        }
    });

    $('#rangeReportForm').on('submit', function () {

        let btn = $('#rangeSubmit');

        btn.prop('disabled', true);
        btn.html('Processing...');

    });

$('#testcategory').change(() => window.location = `{{ route('report.exam_analyisis', [], false) }}?testcategory=${$('#testcategory').val()}`);

</script>
@endsection