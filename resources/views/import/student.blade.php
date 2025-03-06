@extends('layouts.app')
@section('title', 'Student Import')
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
                    <div class="card card-primary" x-data="app">
                        <form action="{{ route('import.student.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="col-deep-purple">Student Import</h6>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <a href="{{ env('APP_URL').'template/student_export.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Student Data Upload Template Format (Bulk Add)</a>
                                       </div>

                                       <div class="form-group col-lg-3">
                                        <label for="branch">Select Operation:</label>
                                        <select name="operation" id="operation" class="form-control form-control-sm" required>
                                            <option value="" disabled selected>-- Choose  Operation--</option>
                                            <option value="add">Add</option>
                                            <option value="update">Update</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-3 branch">
                                        <label for="branch">Select Branch:</label>
                                        <select name="branch" id="branch" class="form-control form-control-sm">
                                            <option value="" disabled selected>-- Choose Branch --</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label for="csv_file">Upload CSV File (Max: 2MB):</label>
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
    $("#operation").on('change', function() {
        if($(this).val() == 'update') {
            $('.branch').hide();
            $('#branch').prop('required', false);
        }
        else {
            $('.branch').show();
            $('#branch').prop('required', true);
        }
        });

         document.querySelector('input[type="file"]').addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (file.size > 2 * 1024 * 1024) { // 2MB in bytes
                                    alert("File size must not exceed 2MB!");
                                    e.target.value = ""; // Clear the file input
                                }
                            });
</script>
@endsection
