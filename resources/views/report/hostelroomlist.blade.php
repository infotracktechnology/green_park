@extends('layouts.app')
@section('title', "Hostel Room List Report")

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
              <h4>Hostel List Report</h4>
            </div>
            <div class="card-body">
              <form method="get" action="{{ route('report.hostelroomlist') }}">
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
                      @foreach ($hostel as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('hostel'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                   <div class="form-group col-2">
                    <label>Rooms</label>
                    <select name="room" id="room" class="select2" required>
                      <option value="">Select Room</option>
                      @foreach ($room as $row)
                      <option value="{{ $row }}" @selected($row==request('room'))>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                      <label>Reports</label>
                      <select name="view" class="form-control form-control-sm" required>
                      <option value="">Select Report</option>
                      @foreach(["Phone Number"=>"phonelist","SIGN LIST"=>"signlist"] as $key => $link)                
                        <option value="{{ $link }}" @selected($link == request('view'))>{{ $key }}</option>
                      @endforeach
                      </select>
                    </div>

                    
                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn m-t-25 btn-primary">Submit</button>
                  </div>

                </div>
              </form>

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
    window.location = `{{ route('report.hostelroomlist') }}?${query}`;
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