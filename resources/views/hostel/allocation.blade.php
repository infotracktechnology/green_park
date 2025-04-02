@extends('layouts.app')
@section('title', 'Hostel Allocation')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="card card-primary">
                        <form method="post" id="myForm" action="{{ route('allocation.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Hostel Allocation</h6>
                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-3 text-right">
                                        <a href="{{ env('APP_URL').'template/hostelallocation.csv'}}" class="btn btn-primary">
                                            <i class="fa fa-download"></i> Hostel Allocation Upload Template (Format)
                                        </a>
                                    </div>
                              
                                        
                                      

                                        <div class="col-md-12">

                                            <div class="row">
                                                <div class="form-group col-lg-3">
                                                    <label for="academic_year">Academic Year</label>
                                                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                                        <option value="">Select Academic Year</option>
                                                        @foreach ($academicyear as $row)
                                                            <option value="{{ $row->academic_year }}" @selected($row->academic_year == request('academic_year'))>{{ $row->academic_year }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class=" form-group col-3">
                                                    <label>Branch</label>
                                                    <select name="branch" id="branch" class="form-control form-control-sm" onchange="location.href = `{{ route('allocation.hostel') }}?branch=${this.value}&academic_year=${document.getElementById('academic_year').value}`" required>
                                                        <option value="" disabled selected>Choose Branch</option>
                                                        @foreach ($branches as $row)
                                                            <option value="{{ $row->id }}" @selected($row->id == request('branch'))>{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                
                                                <div class="form-group  col-3">
                                                    <label>Hostel</label>
                                                    <select name="hostel" id="hostel" class="form-control form-control-sm" onchange="updateTotalRooms(this.value)" required>
                                                        <option value="">Select </option>
                                                        @foreach ($hostels as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="form-group col-3">
                                                    <label>Total Rooms</label>
                                                    <input type="text" id="total_rooms" class="form-control form-control-sm" readonly>
                                                </div>


                                                <div class="form-group col-3">
                                                    <label>Upload File</label>
                                                    <input type="file" name="file" id="file" accept=".csv" class="form-control form-control-sm" required>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>

                                            </div>
                                        </div>
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
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
    // Initialize DataTables
    const table = $('#myTable').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    function updateTotalRooms(hostel_id) {
        $.ajax({
            url: "{{ route('allocation.hostel') }}",
            type: 'GET',
            data: { hostel_id: hostel_id },
            success: function(data) {
                $('#total_rooms').val(data.length); // Display the number of rooms in readonly input
            }
        });
    }

    $(document).ready(function() {
        var hostel_id = $('#hostel').val();
        if (hostel_id) {
            updateTotalRooms(hostel_id);
        }
    });

    $('#hostel').on('change', function() {
        updateTotalRooms(this.value);
    });

    $('#file').on('change', function() {
        var file = this.files[0];
        if (file.type !== 'text/csv') {
            alert('Please select a CSV file.');
            this.value = '';
        }
    });
</script>

@endsection