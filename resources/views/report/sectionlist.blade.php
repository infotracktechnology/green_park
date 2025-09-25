@extends('layouts.app')
@section('title', 'SectionList Report')

@section('css')
<style>
  thead th{
    background-color: #56ade8 !important;
     color: #222 !important;
  }
  table th,table td {
  border: 1px solid #222 !important;
  height: 0px !important;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4>SectionList Report</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <form method="get" id="myForm" action="{{ route('report.sectionlist') }}" enctype="multipart/form-data">
                    <div class="row">
                      <div class="form-group col-lg-3">
                        <label>Course</label>
                        <select name="course" class="form-control form-control-sm" required>
                          <option value="">Select Course</option>
                          @foreach ($course as $row)
                          <option value="{{$row}}" @selected($row==request('course'))>{{$row}}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group col-lg-3">
                        <label>Branch</label>
                        <select name="branch" class="form-control form-control-sm" required>
                          <option value="">Select Branch</option>
                          @foreach ($branches as $branch)
                          <option value="{{ $branch->id }}" @selected($branch->id == request('branch'))>{{ $branch->name }}</option>
                          @endforeach
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
              @if(request('branch') && request('course'))
              <div class="row">
                <div class="col-md-8">
                  @foreach($grouped as $key => $section)
                  <div class="table-responsive mt-3">
                    <table class="table">
                      <thead>
                        <tr>
                          <th colspan="5" class="text-center fw-bold">{{ $key }}</th>
                        </tr>
                        <tr>
                          <th>SECTION</th>
                          <th>BATCH</th>
                          @if($section[0]->hostel_dayscholar == 'HOSTEL')
                          <th>AC</th>
                          <th>NON AC</th>
                          @else
                          <th>CBSE</th>
                          <th>SB</th>
                          @endif
                          <th>TOTAL</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($section as $row)
                        <tr>
                          <td>{{ $row->section }}</td>
                          <td>{{ $row->batch }}</td>
                          @if($row->hostel_dayscholar == 'HOSTEL')
                          <td>{{ $row->ac }}</td>
                          <td>{{ $row->nonac }}</td>
                          @else
                          <td>{{ $row->cbse }}</td>
                          <td>{{ $row->sb }}</td>
                          @endif
                          <td>{{ $row->total }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr class="bg-secondary fw-bold">
                          <td colspan="4">NET TOTAL</td>
                          <td>{{ $section->sum('total') }}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                  @endforeach
                </div>
                <div class="col-md-4">
                  <form method="post" action="{{ route('report.sectionlist') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-lg-12">
                      <label>Sections</label>
                      <input type="hidden" name="branchname" value="{{ $grouped->first()?->first()?->campus }}">
                      <select name="section" class="form-control form-control-sm" required>
                        <option value="">Select Section</option>
                        @foreach($grouped as $key => $section)
                        @foreach($section->sortBy('section') as $row)
                        <option value="{{ $row->section }}">{{ $row->section }}</option>
                        @endforeach
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-lg-12">
                      @foreach(["Attendance List"=>"attendancelist","Phone Number"=>"phonelist","Door List"=>"doorlist","ACINON AC LIST"=>"acnonaclist","SIGN LIST"=>"signlist","Photo List"=>"photolist"] as $key => $link)
                      <button class="btn btn-primary m-t-10 m-r-10" name="view" value="{{ $link }}" type="submit">{{ $key }}</button>
                      @endforeach
                    </div>
                  </form>
                </div>
              </div>
              @endif
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
  $(document).ready(function () {
    $("#myTable").DataTable({
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          footer: true,
        }
      ],
      pageLength: 25,
    });
  });
  
</script>
@endsection