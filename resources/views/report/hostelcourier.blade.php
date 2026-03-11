@extends('layouts.app')
@section('title', "Hostel Courier Entry Report")

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
              <h4>Hostel Courier Entry Report</h4>
            </div>
            <div class="card-body">
              <form method="get" action="{{ route('report.hostelcourier') }}">
                <div class="row">

                  <div class=" form-group col-2">
                    <label>Branch</label>
                    <select name="branch" id="branchid" class="select2" required>
                      <option value="">Choose Branch</option>
                      @foreach ($branches as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('branch'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group  col-3">
                    <label>Hostel</label>
                    <select name="hostel" id="hostel" class="select2" required>
                      <option value="">Select Hostel</option>
                      @foreach ($hostels as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('hostel'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-2">
                    <label>Rooms</label>
                    <select name="room" id="room" class="select2" required>
                      <option value="">Select Room</option>
                      <option value="all" @selected(request('room')=='all' )>All Rooms</option>
                      @foreach ($room as $row)
                      <option value="{{ $row }}" @selected($row==request('room'))>{{ $row }}</option>
                      @endforeach
                    </select>
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
                          <th>#</th>
                          <th>Room No</th>
                          <th>Student ID</th>
                          <th>Name</th>
                          <th>Coaching Type</th>
                          <th>Section</th>
                          <th>Date & Time of Arrival</th>
                          <th>Courier Company Name</th>
                          <th>Sender information</th>
                          <th>Details of Courier</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($hostel_courier as $key => $row)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $row->room_no }}</td>
                          <td>{{ $row->student_id }}</td>
                          <td>{{ $row->student?->student_name }}</td>
                          <td>{{ $row->student?->coaching_type }}</td>
                          <td>{{ $row->student?->section }}</td>
                          <td>{{ $row->datetime_arrival->format('d/m/Y h:i A') }}</td>
                          <td>{{ $row->courier_company }}</td>
                          <td>{{ $row->sender_info }}</td>
                          <td>{{ $row->courier_details }}</td>
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
    window.location = `{{ route('report.hostelcourier') }}?${query}`;
  };
  
  $("#branchid").change(function() {
      if(!this.value) return;
    goToMenu({
      branch: this.value,
    });
  });
  
  $("#hostel").change(function() {
      if(!this.value) return;
    goToMenu({
      branch: $("#branchid").val(),
      hostel: this.value,
    });
  });
  
</script>
@endsection