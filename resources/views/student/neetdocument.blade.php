@extends('layouts.dashboard')

@section('title', 'Upload NEET Documents')

@section('css')
@endsection

@section("meta")
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="card card-primary">
          <div class="card-header">
            <h4> <i class="fas fa-upload"></i> Upload NEET Documents</h4>
          </div>
          <div class="card-body">
            
            <!-- Success Alert -->
            @if(session('success'))
              <div class="alert alert-success">
                 {{ session('success') }}
              </div>
            @endif

            <!-- Form -->
            <form action="{{ route('student.neetdocument') }}" method="POST" enctype="multipart/form-data">
              @csrf
              
              @if(!$student->neet_confirmationpan)
              <div class="form-group mb-4">
                <label class="font-weight-bold">Upload NEET Confirmation PAN</label>
                <div class="custom-file">
                  <input type="file" name="neet_confirmationpan" class="form-control" accept=".pdf, image/*">
                </div>
                <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG. Maximum file size: 2MB.</small>
              </div>
              @endif

              @if(!$student->neet_photo)
              <div class="form-group mb-4">
                <label class="font-weight-bold">Upload NEET Photo</label>
                <div class="custom-file">
                  <input type="file" name="neet_photo" class="form-control" accept="image/*">
                </div>
                <small class="form-text text-muted">Accepted formats: JPG, JPEG, PNG. Maximum file size: 2MB.</small>
              </div>
              @endif

              <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Submit Documents</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
@endsection