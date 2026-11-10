@extends('layouts.app')
@section('title', 'Hostel Arrival Import')
@section('main')

<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
          <div class="card card-primary">
            <form action="{{ route('import.hostelarrival') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <h6 class="col-deep-purple">Hostel Arrival Details Import</h6>
                  </div>

                  <div class="col-md-6 col-sm-12 mb-3">
                    <a href="{{ env('APP_URL').'template/hostel_arrival.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Hostel Arrival Import Template Format</a>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="csv_file">Upload CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control form-control-sm" accept=".csv" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <button style="margin-top: 25px;" type="submit" class="btn btn-primary">Upload</button>
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
</script>
@endsection