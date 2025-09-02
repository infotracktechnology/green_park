@extends('layouts.app')
@section('title', 'Attendance Daily Report')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <form method="get" action="{{ route('report.attendance') }}">
                            <div class="card-header"><h4>Daily Attendance Report</h4></div>
                            <div class="card-body">
                               
                                <div class="row mb-3">
                                    <div class="col-lg-4">
                                        <label for="branch">Branch</label>
                                        <select name="branch_id" id="branch" class="form-control form-control-sm" required>
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
                                        <input type="date" name="date" id="date" value="{{ request('date')  ?? date('Y-m-d') }}" 
                                               class="form-control form-control-sm" required />
                                    </div>
                                    
                                    <div class="col-lg-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                                    </div>
                                </div>
                                @if(count($attendances))
                                <div class="table-responsive">
                                    <table class="table table-striped" id="attendance-table">
                                        <thead>
                                            <tr>
                                                <th>S.NO</th>
                                                <th>Section Name</th>
                                                <th>Boys</th>
                                                <th>Girls</th>
                                                <th>Total</th>
                                                <th>Present</th>
                                                <th>Absent</th>
                                                <th>Present (%)</th>
                                                <th>Absent (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $boys_total=$girls_total=$total=$present=$absent = 0;
                                            ?>
                                            @foreach($attendances as $row)
                                            <?php
                                            $boys_total += $row['boys'];
                                            $girls_total += $row['girls'];
                                            $total += $row['total'];
                                            $present += $row['present'];
                                            $absent += $row['absent'];
                                            ?>
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $row['section'] }}</td>
                                                <td>{{ $row['boys'] }}</td>
                                                <td>{{ $row['girls'] }}</td>
                                                <td>{{ $row['total'] }}</td>
                                                <td>{{ $row['present'] }}</td>
                                                <td>{{ $row['absent'] }}</td>
                                                <td>{{ round($row['present'] * 100 / $row['total'], 2) }}</td>
                                                <td>{{ round($row['absent'] * 100 / $row['total'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="font-16 fw-bold text-center">Total</td>
                                                <td>{{ $boys_total }}</td>
                                                <td>{{ $girls_total }}</td>
                                                <td>{{ $total }}</td>
                                                <td>{{ $present }}</td>
                                                <td>{{ $absent }}</td>
                                                <td>{{ round($present * 100 / $total, 2) }}</td>
                                                <td>{{ round($absent * 100 / $total, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @endif
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
  $("#attendance-table").DataTable({
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
