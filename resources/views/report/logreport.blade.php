@extends('layouts.app') 

@section('title', 'Log Report') 

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" />
@endsection

 @section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                @if(session('error'))
                <div class="col-md-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                  </div>
                </div>
                @endif

                <div class="col-md-10 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Log Report</h6>
                </div>
              </div>

              <div class="col-md-12">
                <form method="get" id="myForm" action="{{ route('report.log') }}" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-lg-4">
                      <label>Branch</label>
                      <select name="branch" id="branch" class="form-control form-control-sm" required>
                        <option value="">Select Branch</option>
                        @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branch->id == request('branch'))>
                          {{ $branch->name }}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group col-lg-4">
                      <label>Coaching Type</label>
                      <select name="coaching_type" class="form-control form-control-sm" required>
                        <option value="">Select Type</option>
                        <option value="Offline" @selected('Offline' == request('coaching_type'))>Offline</option>
                        <option value="Online Recorded" @selected('Online Recorded' == request('coaching_type'))>Online Recorded</option>
                        <option value="Online Live" @selected('Online Live' == request('coaching_type'))>Online Live</option>
                        <option value="Test Series" @selected('Test Series' == request('coaching_type'))>Test Series</option>
                        <option value="11 to XI - OB" @selected('11 to XI - OB' == request('coaching_type'))>11 to XI - OB</option>
                        <option value="12 TO XII - OB" @selected('12 TO XII - OB' == request('coaching_type'))>12 TO XII - OB</option>
                      </select>
                    </div>

                    <div class="form-group col-lg-2">
                      <label>&nbsp;</label>
                      <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

              @if(request()->has('branch'))
              <div class="row">
            
            <div class="col-md-6">
              <div class="card card-primary"  style="overflow-y: auto; height: 350px;">
                  <div class="card-header">
                    <h4>Student Logs Details</h4>
                  </div>
                  <div class="card-body">
                    <ul class="nav nav-pills">
                      <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-toggle="tab" href="#loggedin" role="tab">Logged In ({{ $students->where('active', 1)->count() }})</a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#notloggedin" role="tab">Not Logged In ({{ $students->where('active', 0)->count() }})</a>
                      </li>
                    </ul>
                    <div class="tab-content" id="myTabContent2">
                      <div class="tab-pane fade active show" id="loggedin" role="tabpanel">
                       <table class="table table-sm table-striped">
                        <thead>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Section</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Campus</th>
                        </thead>
                        <tbody>
                            @foreach ($students->where('active', 1) as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->student_name }}</td>
                                <td>{{ $student->section }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>{{ $student->branch->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                       </table>
                      </div>
                      <div class="tab-pane fade" id="notloggedin" role="tabpanel">
                        <table class="table table-striped table-sm">
                        <thead>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Section</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Campus</th>
                        </thead>
                        <tbody>
                            @foreach ($students->where('active', 0) as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->student_name }}</td>
                                <td>{{ $student->section }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>{{ $student->branch->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                       </table>
                      </div>
                    </div>
                  </div>
                </div>
                </div>

                <div class="col-md-6">
                <div class="card card-primary" style="overflow-y: auto; height: 350px;">
                  <div class="card-header">
                    <h4>Announcement Logs</h4>
                  </div>
                  <div class="card-body">
                <table class="table table-striped">
                <tr>
                    <th>Announcement</th>
                    <th>Seen</th>
                    <th>Unseen</th>
                </tr>
                 @foreach($announcements as $key => $row)
                <tr>
                    <td>{{ $row['title'] }}</td>
                    <td><a href="javascript:void(0);">  {{ $row['seen'] }}</a></td>
                    <td><a href="javascript:void(0);">  {{ $row['unseen'] }}</a></td>
                </tr>
                @endforeach
                </table>
                </div>
                </div>
              </div>

               <div class="col-md-6">
                <div class="card card-primary" style="overflow-y: auto; height: 350px;">
                  <div class="card-header">
                    <h4>Exam Logs</h4>
                  </div>
                  <div class="card-body">
                <table class="table  table-striped">
                <tr>
                    <th>Exam</th>
                    <th>Written</th>
                    <th>Not Written</th>
                </tr>
                @foreach($exams as $key => $row)
                <tr>
                    <td>{{ $row['title'] }}</td>
                    <td><a href="javascript:void(0);">  {{ $row['seen'] }}</a></td>
                    <td><a href="javascript:void(0);">  {{ $row['unseen'] }}</a></td>
                </tr>
                @endforeach
                </table>
                </div>
                </div>
              </div>


              


              </div>
              @endif
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
  const table = $(".datatable").DataTable({
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  });
</script>
@endsection
