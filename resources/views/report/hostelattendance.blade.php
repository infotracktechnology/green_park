@extends('layouts.app')
@section('title', "Hostel Attendance Report")

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif

          <div class="card card-primary">
            <div class="card-header">
              <h4>Hostel List Reports</h4>
            </div>
            <div class="card-body">
              
              <!-- Tab List Headers -->
              <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link {{ $active_tab == 'section_tab' ? 'active' : '' }}" id="section-wise-tab" data-toggle="tab" href="#section-wise" role="tab" aria-controls="section-wise" aria-selected="{{ $active_tab == 'section_tab' ? 'true' : 'false' }}">Section Wise</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ $active_tab == 'room_tab' ? 'active' : '' }}" id="room-wise-tab" data-toggle="tab" href="#room-wise" role="tab" aria-controls="room-wise" aria-selected="{{ $active_tab == 'room_tab' ? 'true' : 'false' }}">Room Wise</a>
                </li>
              </ul>

              <div class="tab-content" id="myTabContent">
                
                <div class="tab-pane fade {{ $active_tab == 'section_tab' ? 'show active' : '' }}" id="section-wise" role="tabpanel" aria-labelledby="section-wise-tab">
                  <form method="get" action="{{ route('report.hostelattendance') }}">
                    <input type="hidden" name="active_tab" value="section_tab">
                    <div class="row">

                      <div class="form-group col-lg-2">
                        <label for="branch_id">Branch</label>
                        <select name="branch_id" id="branch_id" class="select2" required>
                          <option value="">Select Branch</option>
                          @foreach ($branches as $branch)
                          <option value="{{ $branch->id }}" @selected(request('branch_id')==$branch->id)>{{ $branch->name }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group col-lg-3">
                        <label for="hostel_id">Hostel</label>
                        <select name="hostel_id" id="hostel_id" class="select2" required>
                          <option value="">Select Hostel</option>
                          @foreach ($hostels as $row)
                          <option value="{{ $row->id }}" @selected(request('hostel_id')==$row->id)>{{ $row->name }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group col-lg-2">
                        <label for="sections">Section</label>
                        <select name="section" id="sections" class="select2" required>
                          <option value="">Select Section</option>
                          @foreach ($section as $row)
                          <option value="{{ $row }}" @selected(request('section')==$row)>{{ $row }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-lg-2">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                      </div>

                      <div class="col-lg-2">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                      </div>

                      <div class="form-group col-lg-1">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                      </div>

                    </div>
                  </form>
                </div>

                <div class="tab-pane fade {{ $active_tab == 'room_tab' ? 'show active' : '' }}" id="room-wise" role="tabpanel" aria-labelledby="room-wise-tab">
                  <form method="get" action="{{ route('report.hostelattendance') }}">
                    <input type="hidden" name="active_tab" value="room_tab">
                    <div class="row">

                      <div class="form-group col-lg-2">
                        <label for="room_branch_id">Branch</label>
                        <select name="room_branch_id" id="room_branch_id" class="select2" required>
                          <option value="">Select Branch</option>
                          @foreach ($branches as $branch)
                          <option value="{{ $branch->id }}" @selected(request('room_branch_id')==$branch->id)>{{ $branch->name }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group col-lg-3">
                        <label for="room_hostel_id">Hostel</label>
                        <select name="room_hostel_id" id="room_hostel_id" class="select2" required>
                          <option value="">Select Hostel</option>
                          @foreach ($hostels as $row)
                          <option value="{{ $row->id }}" @selected(request('room_hostel_id')==$row->id)>{{ $row->name }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group col-lg-2">
                        <label for="room_no">Room No</label>
                        <select name="room_no" id="room_no" class="select2" required>
                          <option value="">Select Room</option>
                          @foreach ($rooms as $row)
                          <option value="{{ $row }}" @selected(request('room_no')==$row)>{{ $row }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-lg-2">
                        <label for="room_from_date">From Date</label>
                        <input type="date" name="from_date" id="room_from_date" value="{{ request('from_date') ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                      </div>

                      <div class="col-lg-2">
                        <label for="room_to_date">To Date</label>
                        <input type="date" name="to_date" id="room_to_date" value="{{ request('to_date') ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                      </div>

                      <div class="form-group col-lg-1">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                      </div>

                    </div>
                  </form>
                </div>

              </div>

              @if($attendance->isNotEmpty())
              <div class="row m-t-20">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table class="table table-striped" style="width:100%;">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Student ID</th>
                          <th>Course</th>
                          <th>Coaching Type</th>
                          <th>Name</th>
                          <th>Section</th>
                          <th>Room</th>
                          <th>Date</th> 
                          <th>Morning</th>
                          <th>Evening</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($attendance as $key => $logs)
                        <?php
                          $student = $logs->first()->student;
                          $morning = $logs->firstWhere('timing', 'Morning');
                          $evening = $logs->firstWhere('timing', 'Evening');
                          $logDate = $logs->first()->attendance_date;
                        ?>
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $student->student_id ?? '-' }}</td>
                          <td>{{ $student->course ?? '-' }}</td>
                          <td>{{ $student->coaching_type ?? '-' }}</td>
                          <td>{{ $student->student_name ?? '-' }}</td>
                          <td>{{ $logs->first()->section }}</td>
                          <td>{{ $logs->first()->room_no }}</td>
                          <td class="bold">{{ \Carbon\Carbon::parse($logDate)->format('d-m-Y') }}</td>
                          <td>{{ $morning->status ?? '-' }}</td>
                          <td>{{ $evening->status ?? '-' }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
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
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/buttons.html5.min.js') }}"></script>
<script>
  $('.table').DataTable({
    dom: 'Bfrtip',
    buttons: ['csv', 'excel'],
    searching: false,
    paging: false,
  });
  
  const goToMenu = (params = {}) => {
    const query = new URLSearchParams(params).toString();
    window.location = `{{ route('report.hostelattendance') }}?${query}`;
  };
  
  $("#branch_id").change(function() {
    if(!this.value) return;
    goToMenu({
      active_tab: 'section_tab',
      branch_id: this.value,
    });
  });
  
  $("#hostel_id").change(function() {
    if(!this.value) return;
    goToMenu({
      active_tab: 'section_tab',
      branch_id: $("#branch_id").val(),
      hostel_id: this.value,
    });
  });
  
  $("#room_branch_id").change(function() {
    if(!this.value) return;
    goToMenu({
      active_tab: 'room_tab',
      room_branch_id: this.value,
    });
  });
  
  $("#room_hostel_id").change(function() {
    if(!this.value) return;
    goToMenu({
      active_tab: 'room_tab',
      room_branch_id: $("#room_branch_id").val(),
      room_hostel_id: this.value,
    });
  });

  $(document).ready(function() {
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeAttendanceTab', $(e.target).attr('href'));
    });
    
    var activeTab = localStorage.getItem('activeAttendanceTab');
    if(activeTab){
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    }
  });
</script>
@endsection