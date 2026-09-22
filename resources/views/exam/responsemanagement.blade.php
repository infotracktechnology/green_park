@extends('layouts.app')
@section('title', 'Response Management')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Response Management</h4>
                        </div>

                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <ul class="nav nav-tabs" id="responseTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="student-tab" data-toggle="tab" href="#student" role="tab" aria-controls="student" aria-selected="true">
                                        Student ID
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="test-tab" data-toggle="tab" href="#test" role="tab" aria-controls="test" aria-selected="false">
                                        Test ID
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="both-tab" data-toggle="tab" href="#both" role="tab" aria-controls="both" aria-selected="false">
                                        Student ID + Test ID
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content pt-4" id="responseTabContent">

                                {{-- 1. STUDENT ID ONLY --}}
                                <div class="tab-pane fade show active" id="student" role="tabpanel" aria-labelledby="student-tab">
                                    <form method="POST" action="{{ route('exam.responsemanagement') }}">
                                        @csrf
                                        <input type="hidden" name="change_type" value="student">

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Exam Name</label>
                                                <select name="testname" class="form-control select2" required>
                                                    <option value="">Select Exam Name</option>
                                                    @foreach($examNames as $examName)
                                                    <option value="{{ $examName }}"{{ old('testname') == $examName ? 'selected' : '' }}> {{ $examName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Old Student ID</label>
                                                <input type="text" name="wrong_student_id" class="form-control" placeholder="Enter Old Student ID" value="{{ old('wrong_student_id') }}" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>New Student ID</label>
                                                <input type="text" name="correct_student_id" class="form-control" placeholder="Enter New Student ID" value="{{ old('correct_student_id') }}" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Change Student ID</button>
                                    </form>
                                </div>

                                {{-- TEST ID ONLY  --}}
                                <div class="tab-pane fade" id="test" role="tabpanel" aria-labelledby="test-tab">
                                    <form method="POST" action="{{ route('exam.responsemanagement') }}">
                                        @csrf
                                        <input type="hidden" name="change_type" value="test">

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Exam Name</label>
                                                <select name="testname" class="form-control select2" required>
                                                    <option value="">Select Exam Name</option>
                                                    @foreach($examNames as $examName)
                                                    <option value="{{ $examName }}"{{ old('testname') == $examName ? 'selected' : '' }}> {{ $examName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Student ID</label>
                                                <input type="text" name="student_id" class="form-control" placeholder="Enter Student ID" value="{{ old('student_id') }}" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Old Test ID</label>
                                                <input type="text" name="old_test_id" class="form-control" placeholder="Enter Old Test ID" value="{{ old('old_test_id') }}" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>New Test ID</label>
                                                <input type="text" name="new_test_id" class="form-control" placeholder="Enter New Test ID" value="{{ old('new_test_id') }}" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Change Test ID</button>
                                    </form>
                                </div>

                                 {{-- STUDENT ID + TEST ID --}}

                                <div class="tab-pane fade" id="both" role="tabpanel" aria-labelledby="both-tab">
                                    <form method="POST" action="{{ route('exam.responsemanagement') }}">
                                        @csrf
                                        <input type="hidden" name="change_type" value="both">

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Exam Name</label>
                                                <select name="testname" class="form-control select2" required>
                                                    <option value="">Select Exam Name</option>
                                                    @foreach($examNames as $examName)
                                                    <option value="{{ $examName }}"{{ old('testname') == $examName ? 'selected' : '' }}> {{ $examName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Old Student ID</label>
                                                <input type="text" name="wrong_student_id" class="form-control" placeholder="Enter Old Student ID" value="{{ old('wrong_student_id') }}" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>New Student ID</label>
                                                <input type="text" name="correct_student_id" class="form-control" placeholder="Enter New Student ID" value="{{ old('correct_student_id') }}" required>
                                            </div>
                                        
                                            <div class="form-group col-md-3">
                                                <label>Old Test ID</label>
                                                <input type="text" name="old_test_id" class="form-control" placeholder="Enter Old Test ID" value="{{ old('old_test_id') }}" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>New Test ID</label>
                                                <input type="text" name="new_test_id" class="form-control" placeholder="Enter New Test ID" value="{{ old('new_test_id') }}" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Change Student & Test ID</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
    <script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('responseActiveTab', $(e.target).attr('href'));
            });

            let activeTab = localStorage.getItem('responseActiveTab');
            if (activeTab) {
                $('#responseTab a[href="' + activeTab + '"]').tab('show');
            }
        });
    </script>
@endsection