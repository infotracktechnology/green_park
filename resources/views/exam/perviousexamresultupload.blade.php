@extends('layouts.app')
@section('title', 'Previous Exam Result Upload')


@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="card card-primary">
        <form method="post" enctype="multipart/form-data" action="{{ route('exam.perviousexamresultupload') }}">
          @csrf

          <div class="card-header">
            <div class="col-md-6">
            <h4>Previous Exam Result Upload</h4>
            </div>
            <div class="col-md-6">
              <a href="{{ env('APP_URL').'template/pervoiusexamtemplate.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Previous Exam Result Upload Template (Format)</a>
            </div>
          </div>

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

              <div class="form-group col-lg-4">
                <label>Upload File</label>
                <input type="file" name="perviousexamfile" class="form-control form-control-sm" accept=".csv" required>
                <span class="text-muted">Please upload only CSV file and file size should be less than 2MB</span>
              </div>

              <div class="form-group col-lg-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
              </div>
            </div>

          </div>

        </form>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script>
  $('#testcategory').change(() => {
    const category = $('#testcategory').val();
    window.location = `{{ route('exam.perviousexamresultupload') }}?testcategory=${category}`;
  });
</script>
@endsection