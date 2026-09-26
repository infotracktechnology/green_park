@extends('layouts.app')
@section('title', 'User Login Report')

@section('css')
    <link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <style>
        .nav-tabs .nav-link {
            font-weight: 600;
        }
        .nav-tabs .nav-link.active {
            color: #5864c9;
            border-color: #5864c9 #5864c9 #fff;
        }
        .table th {
            white-space: nowrap;
        }
    </style>
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-12 col-sm-12">

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible show fade">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="card card-primary">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="loginReportTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ request('tab', 'student') === 'student' ? 'active' : '' }}" id="student-tab" data-toggle="tab" href="#studentLogin" role="tab">
                                        <i class="fas fa-user-graduate"></i> Student Login
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request('tab') === 'admin' ? 'active' : '' }}" id="admin-tab" data-toggle="tab" href="#adminLogin" role="tab">
                                        <i class="fas fa-user-shield"></i> Admin Login
                                    </a>
                                </li>
                            </ul>
                          
                            <div class="tab-content mt-4">

                                <div class="tab-pane fade {{ request('tab', 'student') === 'student' ? 'show active' : '' }}" id="studentLogin" role="tabpanel">
                                    <input type="hidden" name="tab" value="student">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h6 class="col-deep-purple">Student Login Report</h6>
                                        </div>
                                    </div>

                                    
                                    <div class="row my-3">
                                        
                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-primary text-white">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-primary font-weight-bold">Total Students</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $totalStudents }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
      
                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-success text-white">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-success font-weight-bold">Today's Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $todayLogin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-info text-white">
                                                    <i class="fas fa-desktop"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-info font-weight-bold">Web Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $webLogin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-warning text-white">
                                                    <i class="fab fa-android"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-warning font-weight-bold">Android Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $androidLogin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-danger text-white">
                                                    <i class="fab fa-apple"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-danger font-weight-bold">IOS Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $iosLogin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ url()->current() }}" method="get" id="filterForm">
                                        <div class="row">
                                            @if(!auth()->user()->branch)
                                                <div class="col-md-2 col-sm-6 form-group">
                                                    <label>Branch</label>
                                                    <select class="form-control form-control-sm" name="student_branch" onchange="document.getElementById('filterForm').submit();">
                                                        <option value="">All Branches</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}" @selected(request('student_branch') == $branch->id)>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>Course</label>
                                                <select class="form-control form-control-sm" name="course" onchange="document.getElementById('filterForm').submit();">
                                                    <option value="">All Courses</option>
                                                    @foreach($courses as $row)
                                                        <option value="{{ $row }}" @selected(request('course') == $row)>
                                                            {{ $row }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>H/D</label>
                                                <select class="form-control form-control-sm" name="hostel_dayscholar" onchange="document.getElementById('filterForm').submit();">
                                                    <option value="">All H/D</option>
                                                    @foreach($hosteldayscolor as $row)
                                                        <option value="{{ $row }}" @selected(request('hostel_dayscholar') == $row)>
                                                            {{ $row }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>Coaching Type</label>
                                                <select class="form-control form-control-sm" name="coaching_type" onchange="document.getElementById('filterForm').submit();">
                                                    <option value="">All Coaching Type</option>
                                                    @foreach($coaching_type as $row)
                                                        <option value="{{ $row->coaching_type }}" @selected(request('coaching_type') == $row->coaching_type)>
                                                            {{ $row->coaching_type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>Device</label>
                                                <select class="form-control form-control-sm" name="device" onchange="document.getElementById('filterForm').submit();">
                                                    <option value="">All Devices</option>
                                                    <option value="Web" @selected(request('device') == 'Web')>Web</option>
                                                    <option value="Android" @selected(request('device') == 'Android')>Android</option>
                                                    <option value="IOS" @selected(request('device') == 'IOS')>IOS</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>Status</label>
                                                <select class="form-control form-control-sm" name="status" onchange="document.getElementById('filterForm').submit();">
                                                    <option value="">All Status</option>
                                                    <option value="login successful" @selected(request('status') === 'login successful')>Active</option>
                                                    <option value="logout successful" @selected(request('status') === 'logout successful')>Inactive</option>
                                                    <option value="not_accessed" @selected(request('status') === 'not_accessed')>Not Accessed</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>From Date</label>
                                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" onchange="document.getElementById('filterForm').submit();">
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group mb-4">
                                                <label>To Date</label>
                                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" onchange="document.getElementById('filterForm').submit();">
                                            </div>
                                        </div>
                                    </form>

                                    <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm" id="myTable">

                                            <thead>
                                                <tr role="row">
                                                    <th>#</th>
                                                    <th>Student ID</th>
                                                    <th>Student Name</th>
                                                    <th>Campus</th>
                                                    <th>Coaching Type</th>
                                                    <th>Course</th>
                                                    <th>Section</th>
                                                    <th>H/D</th>
                                                    {{-- <th>Father Number</th>
                                                    <th>Mother Number</th> --}}
                                                    <th>Device</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($students as $student)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $student->student_id }}</td>
                                                        <td>{{ $student->student_name }}</td>
                                                        <td>{{ $student->branch->name ?? $student->campus }}</td>
                                                        <td>{{ $student->coaching_type }}</td>
                                                        <td>{{ $student->course }}</td>
                                                        <td>{{ $student->section }}</td>
                                                        <td>{{ $student->hostel_dayscholar }}</td>
                                                        {{-- <td>{{ $student->father_ph_no }}</td> --}}
                                                        {{-- <td>{{ $student->mother_ph_no }}</td> --}}
                                                        <td>{{ $student->device ?? 'N/A' }}</td>
                                                        <td>
                                                            @if(str_contains(strtolower($student->action), 'login successful'))
                                                                <span class="badge badge-success">Login</span>
                                                            @elseif(str_contains(strtolower($student->action), 'logout successful'))
                                                                <span class="badge badge-danger">Logout</span>
                                                            @else
                                                                <span class="badge badge-secondary">Not Accessed</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                          @if($student->login_time)
                                                            {{ date('d-M-Y', strtotime($student->login_time)) }}
                                                          @else
                                                            -
                                                          @endif
                                                        </td>
                                                        <td>
                                                          @if($student->login_time)
                                                            {{ date('h:i A', strtotime($student->login_time)) }}
                                                          @else
                                                            -
                                                          @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="13" class="text-center">No login reports found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>

                                {{-- ================= Admin Login Report ================= --}}
                                <div class="tab-pane fade {{ request('tab') === 'admin' ? 'show active' : '' }}" id="adminLogin" role="tabpanel">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h6 class="col-deep-purple">Admin Login Report</h6>
                                        </div>
                                    </div>

                                    <div class="row my-3">
                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-primary text-white">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-primary font-weight-bold">Total Admin's</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $adminbranchtotal }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-success text-white">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-success font-weight-bold">Today's Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $adminTodayLogin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-info text-white">
                                                    <i class="fas fa-desktop"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-info font-weight-bold">Web Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $webAdminTodayLogin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-warning text-white">
                                                    <i class="fab fa-android"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-warning font-weight-bold">Android Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $andriodadmin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        <div class="col">
                                            <div class="card card-statistic-1 shadow-sm border">
                                                <div class="card-icon bg-danger text-white">
                                                    <i class="fab fa-apple"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="card-header">
                                                        <h4 class="text-danger font-weight-bold">IOS Login</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $iosadmin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ url()->current() }}" method="get" id="adminfilterForm">
                                        <input type="hidden" name="tab" value="admin">
                                        <div class="row">
                                            @if(!auth()->user()->branch)
                                                <div class="col-md-2 col-sm-6 form-group">
                                                    <label>Branch</label>
                                                    <select class="form-control form-control-sm" name="admin_branch" onchange="document.getElementById('adminfilterForm').submit();">
                                                        <option value="">All Branches</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}" @selected(request('admin_branch') == $branch->id)>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>From Date</label>
                                                <input type="date"
                                                    name="admin_from_date"
                                                    class="form-control form-control-sm"
                                                    value="{{ request('admin_from_date') }}"
                                                    onchange="document.getElementById('adminfilterForm').submit();">
                                            </div>

                                            <div class="col-md-2 col-sm-6 form-group">
                                                <label>To Date</label>
                                                <input type="date"
                                                    name="admin_to_date"
                                                    class="form-control form-control-sm"
                                                    value="{{ request('admin_to_date') }}"
                                                    onchange="document.getElementById('adminfilterForm').submit();">
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm" id="adminLoginTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Branch</th>
                                                    <th>User Name</th>
                                                    <th>Role</th>
                                                    <th>Action</th>
                                                    <th>Device</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($adminLogs as $log)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $log->user_branch ?? 'ADMIN' }}</td>
                                                        <td>{{ $log->user_name ?? '-' }}</td>
                                                        <td>{{ $log->role }}</td>
                                                        <td>
                                                            @if(strtolower($log->action) == 'login successful')
                                                                <span class="badge badge-success">Login</span>
                                                            @else
                                                                <span class="badge badge-danger">Logout</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $log->device ?? 'N/A' }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($log->created_at)) }}</td>
                                                        <td>{{ date('h:i A', strtotime($log->created_at)) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No admin login reports found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
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
        $('#myTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{extend: 'excel',title: 'Student_Login_Report'}],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 15,
            lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, "All"]]
        });

        $('#adminLoginTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{extend: 'excel', title: 'Admin_Login_Report'}],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 15,
            lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, "All"]]
        });
    });
    </script>
@endsection