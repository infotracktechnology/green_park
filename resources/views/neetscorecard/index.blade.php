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

                    <form action="{{route('neetscorecard.index')}}" id="myForm" method="get">
                        <div class="col-md-2 form-group">
                            <select class="form-control form-control-sm " onchange="document.getElementById('myForm').submit();" name="course">
                            <option value="">Select Course</option>
                            @foreach($course as $row)
                            <option value="{{$row}}" @selected($row==request('course'))>{{$row}}</option>
                            @endforeach
                            </select>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="studentTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    {{-- <th>Branch</th> --}}
                                    <th>Course</th>
                                    <th>Coaching Type</th>
                                    <th>NEET Application No</th>
                                    <th>NEET Roll No</th>
                                    <th>NEET Mark</th>
                                    <th>Document</th>
                                    <th>Edit</th>
                                    <th width="10%">Action</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $key => $student)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $student->student_id }}</td>
                                        <td>{{ $student->student_name }}</td>
                                        {{-- <td>{{ $student->branch->name ?? '-' }}</td> --}}
                                        <td>{{ $student->course }}</td>
                                        <td>{{ $student->coaching_type }}</td>
                                        <td>{{ $student->neetappno }}</td>
                                        <td>{{ $student->neetrollno }}</td>
                                        <td>{{ $student->neetmark }}</td>
                                        <td>@if($student->neet_file)
                                                <a href="{{ url($student->neet_file) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View 
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('neetscorecard.edit', $student->student_id) }}" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a>
                                        </td>
                                            
                                        <td>
                                            <form action="{{ route('neetscorecard.index') }}" method="POST">
                                                @csrf
                                                <input type="hidden"name="student_id"value="{{ $student->student_id }}">
                                                <button type="submit"class="btn btn-danger btn-sm" onclick="return confirm('Allow this student to upload again?')"><i class="fas fa-undo"></i> Reupload</button>
                                            </form>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm neetremark" data-student="{{ $student->student_id }}" value="{{ $student->neetremark }}" placeholder="Enter remarks">
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

let timer;

$(document).on('keyup', '.neetremark', function () {
    clearTimeout(timer);
    let input = $(this);
    timer = setTimeout(function () {
        $.ajax({
            url: "{{ route('neetscorecard.remark') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                student_id: input.data('student'),
                remark: input.val()
            }
        });
    }, 500);
    
});

</script>
@endsection
