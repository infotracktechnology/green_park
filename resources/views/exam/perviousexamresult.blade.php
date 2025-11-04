@extends('layouts.app')
@section('title', 'Previous Exam Result')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="card card-primary">
        <form method="get" action="{{ route('exam.perviousexamresult') }}">
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

            @if($exams->count())
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
                    @foreach($headers as $subject)
                     <th colspan="2">{{ $subject }}</th>
                    @endforeach
                  </tr>
                  <tr>
                    <th></th><th></th><th></th><th></th><th></th><th></th>
                    @foreach($headers as $subject)
                      <th>Mark</th>
                      <th>Total</th>
                    @endforeach
                  </tr>
                  
                </thead>
                <tbody>
                  @foreach($exams as $i => $row)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->stuid }}</td>
                    <td>{{ $row->sname }}</td>
                    <td>{{ $row->sex }}</td>
                    <td>{{ $row->ctype }}</td>
                    <td>{{ $row->sec }}</td>
                    @foreach($headers as $subject)
                      <?php $data = $row->score_data[$subject] ?? ['mark'=>0,'total'=>0,]; ?>
                      <td>{{ $data['mark'] }}</td>
                      <td>{{ $data['total'] }}</td>
                    @endforeach
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </form>
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
  const category = $('#testcategory').val();
  window.location = `{{ route('exam.perviousexamresult') }}?testcategory=${category}`;
});
$(function() {
  $('#examTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['csv', 'excel'],
    searching: false
  });
});
</script>
@endsection
