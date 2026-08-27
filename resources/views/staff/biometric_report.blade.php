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
                    <select name="branch_id" id="branch" class="form-control form-control-sm" required>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == request('branch_id', auth()->user()->branch ?? ($branches->first()?->id)))>
                        {{ $branch->name }}
                      </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-lg-4">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" value="{{ request('date')  ?? date('Y-m-d') }}" class="form-control form-control-sm" required />
                  </div>

                  <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped" id="biometric-table" style="color: #000;">
                    <thead>
                      <tr>
                        <th>S.NO</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Department</th>
                        <th>Name</th>
                        <th>Biometric No</th>
                        <th>Staff Initial</th>
                        <th>First In</th>
                        <th>Last Out</th>
                        <th>Day</th>
                        <th>Total Hours</th>
                        <th>First Session</th>
                        <th>Second Session</th>
                        <th>Biometric Logs</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($staffs as $staff)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $staff['branch'] }}</td>
                        <td>{{ $staff['date'] }}</td>
                        <td>{{ $staff['department'] }}</td>
                        <td>{{ $staff['name'] }}</td>
                        <td>{{ $staff['biometric_no'] }}</td>
                        <td>{{ $staff['school_initial'] }}</td>
                        <td>{{ $staff['first_in'] }}</td>
                        <td>{{ $staff['last_out'] }}</td>
                        <td>{{ $staff['day'] }}</td>
                        <td>{{ $staff['hours'] }}</td>
                        <td class="{{ $staff['session1'] == 'A' ? 'bg-red' : '' }}">
                          {{ $staff['session1'] }}
                        </td>
                        <td class="{{ $staff['session2'] == 'A' ? 'bg-red' : '' }}">
                          {{ $staff['session2'] }}
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
  $(document).ready(function () {
    $("#biometric-table").DataTable({
      searching: false,
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          customize: function (xlsx) {
            const sheet = xlsx.xl.worksheets['sheet1.xml'];
            const styles = xlsx.xl['styles.xml'];
            
            const fills = $('fills', styles);
            fills.attr('count', +fills.attr('count') + 1)
              .append('<fill><patternFill patternType="solid"><fgColor rgb="FFFF0000"/></patternFill></fill>');
  
            const cellXfs = $('cellXfs', styles);
            const newXfIndex = cellXfs.children().length;
            cellXfs.attr('count', newXfIndex + 1)
              .append('<xf xfId="0" applyFill="1" fillId="' + (fills.children().length - 1) + '"/>');
  
            function colorColumn(col) {
              $('row c[r^="' + col + '"]', sheet).each(function () {
                const val = $('is t', this).text() || $('v', this).text();
                if (val === "A") $(this).attr('s', newXfIndex);
              });
            }
            ["L", "M"].forEach(colorColumn);
          }
        }
      ],
      pageLength: 25,
    });
  });
  
</script>
@endsection