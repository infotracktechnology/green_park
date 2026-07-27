@extends('layouts.app')
@section('title', 'User Login Report')

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
          <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
          @endif

          <div class="card card-primary">
            <div class="card-body">
              
              <div class="row mb-3">
                <div class="col-md-12">
                  <h6 class="col-deep-purple">User Login Report</h6>
                </div>
              </div>

               <!-- New Styled Summary Cards -->
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
                    <select class="form-control form-control-sm" name="branch" onchange="document.getElementById('filterForm').submit();">
                      <option value="">All Branches</option>
                      @foreach($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(request('branch') == $branch->id)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  @endif

                  <div class="col-md-2 col-sm-6 form-group">
                    <label>Course</label>
                    <select class="form-control form-control-sm" name="course" onchange="document.getElementById('filterForm').submit();">
                      <option value="">All Courses</option>
                      @foreach($courses as $row)
                      <option value="{{ $row }}" @selected(request('course') == $row)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2 col-sm-6 form-group">
                    <label>Section</label>
                    <select class="form-control form-control-sm" name="section" onchange="document.getElementById('filterForm').submit();">
                      <option value="">All Sections</option>
                      @foreach($sections as $row)
                      <option value="{{ $row }}" @selected(request('section') == $row)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2 col-sm-6 form-group">
                    <label>Device</label>
                    <select class="form-control form-control-sm" name="device" onchange="document.getElementById('filterForm').submit();">
                      <option value="">All Devices</option>
                      <option value="web" @selected(request('device') == 'web')>web</option>
                      <option value="Android" @selected(request('device') == 'Android')>Android</option>
                      <option value="ios" @selected(request('device') == 'ios')>ios</option>
                    </select>
                  </div>

                  <div class="col-md-2 col-sm-6 form-group">
                    <label>Status</label>
                    <select class="form-control form-control-sm" name="status" onchange="document.getElementById('filterForm').submit();">
                      <option value="">All Status</option>
                      <option value="1" @selected(request('status') === '1')>Active</option>
                      <option value="0" @selected(request('status') === '0')>Inactive</option>
                      <option value="not_accessed" @selected(request('status') === 'not_accessed')>Not Accessed</option>
                    </select>
                  </div>

                  {{-- <div class="col-md-2 col-sm-7 form-group">
                    <label>Global Search</label>
                    <div class="input-group">
                      <input type="text" name="search" id="globalSearch" class="form-control form-control-sm" placeholder="User ID,User Name,..." value="{{ request('search') }}">
                    </div>
                  </div> --}}

                  <div class="col-md-2 col-sm-6 form-group">
                      <label>From Date</label>
                      <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" onchange="document.getElementById('filterForm').submit();">
                  </div>

                  <div class="col-md-2 col-sm-6 form-group">
                      <label>To Date</label>
                      <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" onchange="document.getElementById('filterForm').submit();">
                  </div>

                </div>
              </form>

             

              <!-- Table Section -->
              <div class="col-12 mt-3">
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="reportTable">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Campus</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Device</th>
                        <th>Status</th>
                        <th>Last Login</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($students as $student)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->branch->name ?? $student->campus }}</td>
                        <td>{{ $student->course }}</td>
                        <td>{{ $student->section }}</td>
                        <td>{{ $student->device ?? 'N/A' }}</td>
                       <td>
                            @if(is_null($student->last_login))
                                <span class="badge badge-secondary">Not Accessed</span>
                            @elseif($student->active == 1)
                                <span class="badge badge-success">Login</span>
                            @else
                                <span class="badge badge-danger">Logout</span>
                            @endif
                        </td>
                        <td>{{ $student->last_login ? date('d-M-Y h:i A', strtotime($student->last_login)) : 'Not Accessed' }}</td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="9" class="text-center">No login reports found.</td>
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

  </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
 $(document).ready(function () {
    $('#reportTable').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excel',
            title: 'User_Login_Report'
        }],
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 15,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ]
    });
});

// let timer;

// $('#globalSearch').on('keyup', function () {
//     clearTimeout(timer);
//     timer = setTimeout(() => {
//         $('#filterForm').submit();
//     }, 2000);

// });
</script>
@endsection