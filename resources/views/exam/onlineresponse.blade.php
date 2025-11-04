@extends('layouts.app')

@section('title', 'Online Response')
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
            <form method="post" class="no-loader" action="{{ route('exam.onlineresponse.download') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-header"><h4>Online Response(csv file)</h4></div>
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
                        @foreach ($exams as $exam)
                        <option value="{{ $exam->name}}"> {{ $exam->name }}</option>
                        @endforeach
                      </select>
                    </div>

                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Download</button>
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
$('#testcategory').change(() => window.location = `{{ route('exam.onlineresponse') }}?testcategory=${$('#testcategory').val()}`);
</script>
@endsection