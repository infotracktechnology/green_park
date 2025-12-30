@extends('layouts.app')
@section('title', 'Previous Exam Result Upload')


@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      @if(session('success'))
      <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible show fade">{{ session('error') }}</div>
      @endif

      <div class="card card-primary">
        <form method="post" enctype="multipart/form-data" action="{{ route('exam.previousexamupload') }}">
          @csrf

          <div class="card-header">
            <div class="col-md-8">
              <h4>Previous Exam Result Upload</h4>
            </div>
            <div class="col-md-4">
              <a href="{{ env('APP_URL').'template/pervoiusexamtemplate.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Previous Exam Result Upload Template (Format)</a>
            </div>
          </div>

          <div class="card-body">
            <div class="row">

              <div class="form-group col-lg-4">
                <label>Upload File</label>
                <input type="file" name="perviousexamfile" class="form-control form-control-sm" accept=".csv" required>
                <small class="text-danger">File size should be less than 2MB</small>
              </div>

              <div class="form-group col-lg-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Upload</button>
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
    window.location = `{{ route('exam.previousexamupload') }}?testcategory=${category}`;
  });
</script>
@endsection