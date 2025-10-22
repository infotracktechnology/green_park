@extends('layouts.app')

@section('title', 'Test Report')
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
            <form method="post" id="myForm" action="{{ route('exam.test.download') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">

                  <div class="form-group col-lg-4">
                    <label>Test Name</label>
                    <select name="test_id" id="test_id" class="select2" required>
                      <option value="">Select Test</option>
                      @foreach ($tests as $test)
                      <option value="{{ $test->testid }}">
                        {{ $test->name }}
                      </option>
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

@endsection