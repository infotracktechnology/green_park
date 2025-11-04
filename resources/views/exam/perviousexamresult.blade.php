@extends('layouts.app')

@section('title', 'Previous Exam Result')
@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}" />
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}" />
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          <div class="card card-primary">
            <form method="get" action="{{ route('exam.perviousexamresult') }}" enctype="multipart/form-data">
              <div class="card-header"><h4>Previous Exam Result</h4></div>
              <div class="card-body">
                <div class="row">
                  <div class="form-group col-lg-4">
                    <label>Exam Category</label>
                    <select name="testcategory" id="testcategory" class="select2" required>
                      <option value="">Select Category</option>
                      @foreach ($category as $row)
                      <option value="{{ $row }}" @selected($row==request('testcategory'))>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Exam Name</label>
                    <select name="examname" id="examname" class="select2" required>
                      <option value="">Select Test</option>
                      @foreach ($exam as $row)
                      <option value="{{ $row->subject }}" @selected($row->subject==request('examname'))>{{ $row->subject }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Show Marks</button>
                  </div>
                </div>

                @if($exams)
                <div class="row">
                  <div class="table-responsive">
                    <table class="table table-striped" id="examTable">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>STUDENTID</th>
                          <th>NAME</th>
                          <th>GENDER</th>
                          <th>COACHING TYPE</th>
                          <th>SECTION</th>
                          @foreach($exams->first()?->Header(request('testcategory')) as $subject)
                          <th>{{ $subject }}</th>
                          @endforeach
                          <th>TOTAL</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($exams as $key => $row)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $row->stuid }}</td>
                          <td>{{ $row->sname }}</td>
                          <td>{{ $row->sex }}</td>
                          <td>{{ $row->ctype }}</td>
                          <td>{{ $row->sec }}</td>
                          @foreach($row->getScoresForHeader(request('testcategory')) as $subject => $mark)
                          <td>{{ $mark[0] }} / {{ $mark[1] }}</td>
                          @endforeach
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                @endif
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
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/buttons.html5.min.js') }}"></script>

<script>
$('#testcategory').change(() => {
  window.location = `{{ route('exam.perviousexamresult') }}?testcategory=${$('#testcategory').val()}`;
});

$(document).ready(function() {
  $('#examTable').DataTable({
    dom: 'Bfrtip',
    searching: false,
    buttons: ['csv', 'excel']
  });
});
</script>
@endsection
