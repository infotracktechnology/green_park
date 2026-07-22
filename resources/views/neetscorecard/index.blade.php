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
                                    <th>All-India Rank</th>
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
                                        <td>{{ $student->neetrank }}</td>
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
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-remark-btn" data-toggle="modal" data-target="#remarkModal" data-student="{{ $student->student_id }}" data-remark="{{ $student->neetremark }}">
                                                {{ \Str::limit($student->neetremark, 20) ?: 'Add Remark' }}
                                            </button>
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

    <!-- Remark Modal -->
    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('neetscorecard.remark', [], false) }}" method="POST" id="remarkForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="remarkModalLabel">Edit Remark</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="student_id" id="modal_student_id">
                        <div class="form-group">
                            <label for="modal_remark">Remarks</label>
                            <textarea class="form-control" name="remark" id="modal_remark" rows="3" placeholder="Enter remarks"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

$(document).on('click', '.edit-remark-btn', function () {
    let studentId = $(this).data('student');
    let remark = $(this).attr('data-remark') || '';
    $('#modal_student_id').val(studentId);
    $('#modal_remark').val(remark);
    $('#remarkForm').data('trigger-btn', $(this));
});

$('#remarkForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let submitBtn = form.find('button[type="submit"]');
    let triggerBtn = form.data('trigger-btn');
    let remarkVal = $('#modal_remark').val();

    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: form.attr('action'),
        type: "POST",
        data: form.serialize(),
        success: function (response) {
            if (response.status) {
                triggerBtn.attr('data-remark', remarkVal);
                let displayVal = remarkVal.trim();
                if (displayVal.length > 20) {
                    displayVal = displayVal.substring(0, 20) + '...';
                }
                triggerBtn.text(displayVal !== '' ? displayVal : 'Add Remark');
                $('#remarkModal').modal('hide');
            } else {
                alert('Something went wrong. Please try again.');
            }
        },
        error: function () {
            alert('Error updating remark.');
        },
        complete: function () {
            submitBtn.prop('disabled', false).text('Save Changes');
        }
    });
});

</script>
@endsection
