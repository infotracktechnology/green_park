@extends('layouts.app')
@section('title', 'Topup')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            
            <div class="row">
                <div class="col-12">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible show fade">
                            <div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>{{ session('success') }}</div>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible show fade">
                            <div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>{{ session('error') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4> Student Topup </h4>
                        </div>
                        
                        <div class="card-body">
                            <form method="GET" action="{{ route('hostel.topup') }}">
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label>Branch</label>
                                        <select class="form-control select2" id="branchid" name="branch_id" required>
                                            <option value="">Choose Branch</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2">
                                        <label>Hostel</label>
                                        <select class="form-control select2" id="hostel" name="hostel_id" required>
                                            <option value="">Choose Hostel</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2">
                                        <label>Room No</label>
                                        <select class="form-control select2" id="room" name="room_no" required>
                                            <option value="">Choose Room</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2">
                                        <label>Amount</label>
                                        <input type="number" name="amount" class="form-control" value="{{ request('amount', 0) }}" min="0" required>
                                    </div>
                                    

                                    <div class="form-group col-lg-1 mt-4">
                                        <button type="submit" name="show" class="btn btn-primary btn-block">  Show </button>
                                    </div>
                                </div>
                            </form>
                            {{-- <hr> --}}

                            @if(request()->has('show'))
                                <form action="{{ route('hostel.topup') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                                    <input type="hidden" name="hostel_id" value="{{ request('hostel_id') }}">
                                    <input type="hidden" name="room_no" value="{{ request('room_no') }}">
                                    <input type="hidden" name="amount" value="{{ request('amount') }}">

                                        <button type="submit" class="btn btn-success mb-3">  Topup  </button>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm " id="studentTable"  >
                                            <thead>
                                                <tr>
                                                    <th >S.No</th>
                                                    <th >
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="checkAll">
                                                            <label class="custom-control-label" for="checkAll"></label>
                                                        </div>
                                                    </th>
                                                    <th>Student ID</th>
                                                    <th>Student Name</th>
                                                    <th>Campus</th>
                                                    <th>Course</th>
                                                    <th>Section</th>
                                                    <th>Avail. Bal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($students as $student)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="student_id[]" value="{{ $student->student_id }}" 
                                                                    class="custom-control-input student-check" id="st-{{ $student->student_id }}">
                                                                <label class="custom-control-label" for="st-{{ $student->student_id }}"></label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $student->student_id }}</td>
                                                        <td>{{ $student->student_name }}</td>
                                                        <td>{{ $student->branch->name }}</td>
                                                        <td>{{ $student->course }}</td>
                                                        <td>{{ $student->section }}</td>
                                                        <td>₹{{ number_format($student->deposit, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center p-4"> No students found for this selection. </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            @endif
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
    $(document).ready(function() {
        const hostelSelect = $('#hostel');
        const roomSelect = $('#room');
        const branchSelect = $('#branchid');
        const selectedHostel = "{{ request('hostel_id') }}";
        const selectedRoom = "{{ request('room_no') }}";

        function fetchHostels(branchId, selectedId = null) {
            if (!branchId) return;
            $.get('{{ route("hostel.topup") }}', { branch: branchId }, function(data) {
                hostelSelect.empty().append('<option value="">Choose Hostel</option>');
                $.each(data, function(key, value) {
                    let selected = (selectedId == value.id) ? 'selected' : '';
                    hostelSelect.append(`<option value="${value.id}" ${selected}>${value.name}</option>`);
                });
                
                if (selectedId) fetchRooms(selectedId, selectedRoom);
            });
        }

        function fetchRooms(hostelId, selectedId = null) {
            if (!hostelId) return;            
            $.get('{{ route("phoneturn.create") }}', { hostel: hostelId }, function(data) {
                roomSelect.empty().append('<option value="">Choose Room</option>');
                $.each(data, function(key, value) {
                    let selected = (selectedId == value) ? 'selected' : '';
                    roomSelect.append(`<option value="${value}" ${selected}>${value}</option>`);
                });
            });
        }

        branchSelect.change(function() {
            hostelSelect.empty().append('<option value="">Choose Hostel</option>');
            roomSelect.empty().append('<option value="">Choose Room</option>');
            fetchHostels($(this).val());
        });

        hostelSelect.change(function() {
            fetchRooms($(this).val());
        });

        if (branchSelect.val()) {
            fetchHostels(branchSelect.val(), selectedHostel);
        }

        $("#checkAll").on('change', function() {
            $(".student-check").prop("checked", $(this).prop("checked"));
        });
    });
</script>
@endsection