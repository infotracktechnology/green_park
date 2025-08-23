@extends('layouts.app')
@section('title', 'Biometric Daily Report')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible show fade"> {{ session('error') }} </div>
                @endif

                    <div class="card card-primary">
                        <form method="get" action="{{ route('biometric.report') }}">
                            <div class="card-body">
                                <h6>Biometric Report</h6>
                                
                                <div class="row mb-3">
                                    <div class="col-lg-4">
                                        <label for="branch">Branch</label>
                                        <select name="branch_id" id="branch" class="form-control" required>
                                            <option value="">Select Branch</option>
                                            @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @selected($branch->id == request('branch_id'))>
                                                {{ $branch->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <label for="date">Date</label>
                                        <input type="date" name="date" id="date" value="{{ request('date') }}" 
                                               class="form-control" required />
                                    </div>
                                    
                                    <div class="col-lg-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="biometric-table">
                                        <thead>
                                            <tr>
                                                <th>S.NO</th>
                                                <th>Department</th>
                                                <th>Name</th>
                                                <th>Biometric No</th>
                                                <th>First In</th>
                                                <th>Last Out</th>
                                                <th>Status</th>
                                                <th>Time Logs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($staffs as $staff)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $staff['department'] }}</td>
                                                <td>{{ $staff['name'] }}</td>
                                                <td>{{ $staff['biometric_no'] }}</td>
                                                <td>{{ $staff['first_in'] }}</td>
                                                <td>{{ $staff['last_out'] }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $staff['status'] == 'P' ? 'success' : ($staff['status'] == 'L' ? 'warning' : 'danger') }}">
                                                        {{ $staff['status'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $staff['time_logs'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.print.min.js')}}"></script>

<script>
$(document).ready(function() {
    $("#biometric-table").DataTable({
        dom: "Bfrtip",
        buttons: ["excel"],
        pageLength: 25,
        responsive: true
    });
});
</script>
@endsection
