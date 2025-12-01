@extends('layouts.app')
@section('title', 'Fees Migration')


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
        <form method="post" enctype="multipart/form-data" action="{{ route('fees.migration') }}">
          @csrf

          <div class="card-header">
            <div class="col-md-8">
              <h4>Fees Migration Upload</h4>
            </div>
            <div class="col-md-4">
              <a href="{{ env('APP_URL').'template/feesmigration.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Fees Migration Upload Template (Format)</a>
            </div>
          </div>

          <div class="card-body">
            <div class="row">

              <div class="form-group col-lg-3">
                <label for="branch">Branch</label>
                <select name="branch"  class="select2" required>
                <option value="">Select Branch</option>
                  @foreach ($branches as $branch)
                  <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-lg-2">
                <label>Course</label>
                <select name="course"  class="select2" required>
                  <option value="">Select Course</option>
                  @foreach ($course as $row)
                  <option value="{{$row}}">{{$row}}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-lg-2">
              <label>Coaching Type</label>
              <select name="coaching_type" class="select2" required>
                <option value="">Select Coaching Type</option>
                @foreach ($coachingtype as $row)
                <option value="{{$row}}">{{$row}}</option>
                @endforeach
              </select>
            </div>


            <div class="form-group col-lg-2">
              <label>Batch</label>
              <select name="batch" class="select2" required>
                <option value="">Select Batch</option>
                @foreach ($batch as $row)
                <option value="{{$row}}">{{$row}}</option>
                @endforeach
              </select>
            </div>


            <div class="form-group col-lg-3">
              <label>Upload File</label>
              <input type="file" name="feemigration" class="form-control form-control-sm" accept=".csv" required>
              <span class="text-danger">File size should be less than 2MB</span>
            </div>

            <div class="form-group col-lg-2">
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
@endsection