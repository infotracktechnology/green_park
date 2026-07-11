@extends('layouts.dashboard')

@section('title', 'Upload NEET Scorecard')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">

                <div class="card card-primary">
                    <div class="card-header">
                        <h4><i class="fas fa-file-medical"></i> Upload NEET Scorecard</h4>
                    </div>

                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible show fade">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('student.neetscorecard') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Student ID</label>
                                        <input type="text" class="form-control" value="{{ $student->student_id }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Student Name</label>
                                        <input type="text" class="form-control" value="{{ $student->student_name }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Course</label>
                                        <input type="text"class="form-control" value="{{ $student->course }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NEET Application Number <span class="text-danger">*</span></label>
                                        <input type="text"name="neetappno" class="form-control" value="{{ old('neetappno', $student->neetappno) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NEET Roll Number <span class="text-danger">*</span></label>
                                        <input type="text" name="neetrollno"class="form-control" value="{{ old('neetrollno', $student->neetrollno) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NEET Category <span class="text-danger">*</span></label>
                                        <select name="neetcomm" class="form-control select2"  required>
                                            <option value="">Select Category</option>
                                            <option value="GENERAL" {{ ($student->neetcomm=='GENERAL')?'selected':'' }}>GENERAL</option>
                                            <option value="GEN-EWS" {{ ($student->neetcomm=='GEN-EWS')?'selected':'' }}>GEN-EWS</option>
                                            <option value="OBC-NCL" {{ ($student->neetcomm=='OBC-NCL')?'selected':'' }}>OBC-NCL</option>
                                            <option value="SC" {{ ($student->neetcomm=='SC')?'selected':'' }}>SC</option>
                                            <option value="ST" {{ ($student->neetcomm=='ST')?'selected':'' }}>ST</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Special Category <span class="text-danger">*</span></label>
                                        <select name="neetspecialcategory" class="form-control select2"  required>
                                            <option value="">Select Category</option>
                                            <option value="7.5%" {{ ($student->neetspecialcategory=='7.5%')?'selected':'' }}>7.5%</option>
                                            <option value="PWD" {{ ($student->neetspecialcategory=='PWD')?'selected':'' }}>PWD</option>
                                            <option value="EX-ARMY" {{ ($student->neetspecialcategory=='EX-ARMY')?'selected':'' }}>EX-ARMY</option>
                                            <option value="SPORTS" {{ ($student->neetspecialcategory=='SPORTS')?'selected':'' }}>SPORTS</option>
                                            <option value="IRT" {{ ($student->neetspecialcategory=='IRT')?'selected':'' }}>IRT</option>
                                            <option value="NIL" {{ ($student->neetspecialcategory=='NIL')?'selected':'' }}>NIL</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NEET Mark <span class="text-danger">*</span></label>
                                        <input type="number" name="neetmark" class="form-control" value="{{ old('neetmark', $student->neetmark) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Upload NEET Scorecard <span class="text-danger">*</span></label>

                                        <input type="file" name="neet_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ $student->neet_file ? 'disabled' : 'required' }}>
                                        <small class="text-muted">
                                            Allowed formats : PDF, JPG, JPEG, PNG (Max 2MB)
                                        </small>

                                        @if($student->neet_file)
                                            <div class="mt-2">
                                                <a href="{{ url($student->neet_file) }}" target="_blank" class="btn btn-sm">
                                                    <i class="fas fa-eye"></i> View Uploaded Scorecard
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(!$student->neet_file)
                            <div class="text-right">
                                <button class="btn btn-primary btn-lg" {{ $student->neet_file ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Submit
                                </button>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You have already uploaded your NEET Scorecard. Please contact the administration if you need to upload it again.
                            </div>
                            @endif

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection