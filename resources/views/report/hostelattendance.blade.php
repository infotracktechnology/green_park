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
        <div class="col-md-12 col-sm-12">

          <div class="card card-primary">
            <div class="card-header">
              <h4>Hostel Attendance Report</h4>
            </div>
            <div class="card-body">
              <form method="get" action="{{ route('report.hostelattendance') }}">
                <div class="row">

                  <div class="form-group col-lg-2">
                    <label for="branch">Branch</label>
                    <select name="branch_id" id="branch_id" class="select2" required>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(request('branch_id')==$branch->id)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="section">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="select2" required>
                      <option value="">Select Hostel</option>
                      @foreach ($hostels as $row)
                      <option value="{{ $row->id }}" @selected(request('hostel_id')==$row->id)>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="section">Section</label>
                    <select name="section" id="sections" class="select2" required>
                      <option value="">Select Section</option>
                      @foreach ($section as $row)
                      <option value="{{ $row }}" @selected(request('section')==$row)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-lg-2">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" value="{{ request('date')  ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                  </div>


                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn m-t-25 btn-primary">Submit</button>
                  </div>

                </div>
              </form>


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
                        ?>
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $student->student_id ?? '-' }}</td>
                          <td>{{ $student->course ?? '-' }}</td>
                          <td>{{ $student->coaching_type ?? '-' }}</td>
                          <td>{{ $student->student_name ?? '-' }}</td>
                          <td>{{ $logs->first()->section }}</td>
                          <td>{{ $logs->first()->room_no }}</td>
                          <td>{{ $morning->status ?? '-' }}</td>
                          <td>{{ $evening->status ?? '-' }}</td>
                        </tr>
                        @endforeach
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
      branch_id: this.value,
    });
  });
  
  $("#hostel_id").change(function() {
      if(!this.value) return;
    goToMenu({
      branch_id: $("#branch_id").val(),
      hostel_id: this.value,
    });
  });
  
</script>
@endsection