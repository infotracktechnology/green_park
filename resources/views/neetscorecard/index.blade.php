@extends('layouts.app')

@section('title', 'Document Reupload')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Document Reupload</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="studentTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Coaching Type</th>
                                    <th>Document</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $key => $student)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $student->student_id }}</td>
                                        <td>{{ $student->student_name }}</td>
                                        <td>{{ $student->branch->name ?? '-' }}</td>
                                        <td>{{ $student->course }}</td>
                                        <td>{{ $student->coaching_type }}</td>
                                        <td>@if($student->neet_file)
                                                <a href="{{ url($student->neet_file) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View 
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('neetscorecard.index') }}" method="POST">
                                                @csrf
                                                <input type="hidden"name="student_id"value="{{ $student->student_id }}">
                                                <button type="submit"class="btn btn-danger btn-sm" onclick="return confirm('Allow this student to upload again?')"><i class="fas fa-undo"></i> Allow Reupload</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
$(document).ready(function(){

    $('#studentTable').DataTable({
        pageLength:20
    });
});

</script>
@endsection
