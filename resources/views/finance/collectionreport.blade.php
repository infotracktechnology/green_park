@extends('layouts.app')
@section('title', 'Fee Collection Report')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')

<div class="main-content">
  <section class="section">
    <div class="card card-primary shadow-sm mb-4">
      @if(session()->has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
              </div>
           @endif
           @if(session()->has('error'))
              <div class="alert alert-error alert-dismissible fade show" role="alert">
                  {{ session('error') }}
              </div>
           @endif
           <div class="card-header">
            <h4>Fee Collection Report</h4>
           </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-12 mb-3">
            <form method="GET" action="{{route('fees.report.collection')}}" id="searchform">
              <div class="row">
                <div class="col-md-3 mb-2">
                  <label class="form-label">Branch</label>
                  <select name="branch_id" class="form-control form-control-sm" required>
                    @if(!auth()->user()->branch)
                    <option value="all" @selected(request('branch_id') == 'all')>All</option>
                    @endif
                    @foreach ($branchselect as $id => $branch)
                    <option value="{{$id}}" @selected(request('branch_id') == $id)>{{$branch}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mb-2">
                  <label class="form-label">Category</label>
                  <select name="which_wise" class="form-control form-control-sm" required>
                    <option value="">Select Category</option>
                    <option value="feetypewise" @selected(request('which_wise') == 'feetypewise')>FeeType wise</option>
                    <option value="segmentwise" @selected(request('which_wise') == 'segmentwise')>Segment wise</option>
                  </select>
                </div>

                <div class="col-md-3 mb-2">
                  <label class="form-label">Report Type</label>
                  <select name="report_type" class="form-control form-control-sm" required>
                    <option value="">Select Report</option>
                    <option value="summary" @selected(request('report_type') == 'summary')>Summary</option>
                    <option value="detail" @selected(request('report_type') == 'detail')>Detail</option>
                  </select>
                </div>

                <div class="col-md-3 mb-2">
                  <div class="input-daterange">
                  <label class="form-label">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" required value="{{request('start_date') ?? date('Y-m-d')}}">
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <div class="input-daterange">
                  <label class="form-label">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" required value="{{request('end_date') ?? date('Y-m-d')}}">
                  </div>
                </div>
                <div class="col-md-3 mb-2">
                  <label class="form-label">Payment Mode</label>
                  <select name="payment_mode" class="form-control form-control-sm" required>
                    <option value="all">All</option>
                    <option value="cash" @selected(request('payment_mode') == 'cash')>Cash</option>
                    <option value="neft" @selected(request('payment_mode') == 'neft')>RTGS / NEFT Payments/UPI</option>
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary mt-4">Get</button>
                </div>
              </div>
            </form>
          </div>
          @if(isset($report) && count($report))
          <div class="col-12">
            <div class="table-responsive mt-4">
    <table id="feeTypeTable" class="table table-bordered table-striped w-100">
        <thead>
            <tr>
                <th rowspan="2">S.No</th>
                <th rowspan="2">Payment Date</th>
                @foreach ($feeTypes as $type)
                    <th colspan="2" class="text-center">{{ $type }}</th>
                @endforeach
                <th colspan="2" class="text-center">Total</th>
            </tr>
            <tr>
                @foreach ($feeTypes as $type)
                    <th>Amount</th>
                    <th>No. of Students</th>
                @endforeach
                <th>Amount</th>
                <th>No. of Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->payment_date)->format('Y-m-d') }}</td>
                    @foreach ($feeTypes as $type)
                        <td class="text-right">{{ number_format($row->{$type . '_Amount'} ?? 0, 2) }}</td>
                        <td class="text-right">{{ $row->{$type . '_Students'} ?? 0 }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($row->Total_Amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $row->Total_Students ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th></th>
                @foreach ($feeTypes as $type)
                    <th class="text-right">{{ number_format(array_sum(array_column($report, $type . '_Amount')), 2) }}</th>
                    <th class="text-right">{{ array_sum(array_column($report, $type . '_Students')) }}</th>
                @endforeach
                <th class="text-right">{{ number_format(array_sum(array_column($report, 'Total_Amount')), 2) }}</th>
                <th class="text-right">{{ array_sum(array_column($report, 'Total_Students')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>

          </div>
          @elseif(isset($report) && count($report) == 0)
          <div class="col-12">
        <div class="alert alert-info mt-4">No records found!</div>
          </div>
          @endif

          @if(isset($detailReport) && count($detailReport))
<div class="table-responsive mt-4">
<table id="detailFeeType" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th rowspan="2">S.No</th>
            <th rowspan="2">Payment Date</th>
            <th rowspan="2">Receipt No</th>
            <th rowspan="2">Student</th>
            <th rowspan="2">Register No</th>

            @foreach ($feeTypes as $type)
                <th  class="text-center">{{ $type }}</th>
            @endforeach

            <th  class="text-center">Total</th>
        </tr>

        <tr>
            @foreach ($feeTypes as $type)
                <th class="text-center">Amount</th>
                {{-- <th>Students</th> --}}
            @endforeach
            <th class="text-center">Amount</th>
            {{-- <th>Students</th> --}}
        </tr>
    </thead>

    <tbody>
        @foreach ($detailReport as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->payment_date }}</td>
            <td>{{ $row->receipt_no }}</td>
            <td>{{ $row->student_name }}</td>
            <td>{{ $row->student_id }}</td>

            @foreach ($feeTypes as $type)
                <td class="text-right">{{ number_format($row->{$type . '_Amount'} ?? 0, 2) }}</td>
                {{-- <td>{{ $row->{$type . '_Students'} ?? 0 }}</td> --}}
            @endforeach

            <td class="text-right">{{ number_format($row->Total_Amount ?? 0, 2) }}</td>
            {{-- <td>{{ $row->Total_Students ?? 0 }}</td> --}}
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            @foreach ($feeTypes as $type)
                <th class="text-right">{{ number_format($feeTypeTotals[$type.'_Amount'], 2) }}</th>
                {{-- <th></th> --}}
            @endforeach
            <th class="text-right">{{ number_format($grandTotal ?? 0, 2) }}</th>
            {{-- <th></th> --}}
        </tr>
    </tfoot>
</table>
</div>
@elseif(isset($detailReport) && count($detailReport) == 0)
<div class="col-12">
<div class="alert alert-info mt-4">No records found!</div>
</div>
@endif

@if(isset($segmentreport) && count($segmentreport))
          <div class="col-12">
            <div class="table-responsive mt-4">
    <table id="segmentTypeTable" class="table table-bordered table-striped w-100">
        <thead>
            <tr>
                <th rowspan="2">S.No</th>
                <th rowspan="2">Payment Date</th>
                @foreach ($segmentsList as $type)
                    <th colspan="2" class="text-center">{{ $type }}</th>
                @endforeach
                <th colspan="2" class="text-center">Total</th>
            </tr>
            <tr>
                @foreach ($segmentsList as $type)
                    <th>Amount</th>
                    <th>No. of Students</th>
                @endforeach
                <th>Amount</th>
                <th>No. of Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($segmentreport as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->payment_date)->format('Y-m-d') }}</td>
                    @foreach ($segmentsList as $type)
                        <td class="text-right">{{ number_format($row->{$type . '_Amount'} ?? 0, 2) }}</td>
                        <td class="text-right">{{ $row->{$type . '_Students'} ?? 0 }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($row->Total_Amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $row->Total_Students ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th></th>
                @foreach ($segmentsList as $type)
                    <th class="text-right">{{ number_format(array_sum(array_column($segmentreport, $type . '_Amount')), 2) }}</th>
                    <th class="text-right">{{ array_sum(array_column($segmentreport, $type . '_Students')) }}</th>
                @endforeach
                <th class="text-right">{{ number_format(array_sum(array_column($segmentreport, 'Total_Amount')), 2) }}</th>
                <th class="text-right">{{ array_sum(array_column($segmentreport, 'Total_Students')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>

          </div>
          @elseif(isset($segmentreport) && count($segmentreport) == 0)
          <div class="col-12">
        <div class="alert alert-info mt-4">No records found!</div>
          </div>
          @endif

          @if(isset($segmentdetailReport) && count($segmentdetailReport))
<div class="table-responsive mt-4">
<table id="segmentdetailFeeType" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th rowspan="2">S.No</th>
            <th rowspan="2">Payment Date</th>
            <th rowspan="2">Receipt No</th>
            <th rowspan="2">Student</th>
            <th rowspan="2">Register No</th>

            @foreach ($segmentsList as $type)
                <th  class="text-center">{{ $type }}</th>
            @endforeach

            <th  class="text-center">Total</th>
        </tr>

        <tr>
            @foreach ($segmentsList as $type)
                <th class="text-center">Amount</th>
                {{-- <th>Students</th> --}}
            @endforeach
            <th class="text-center">Amount</th>
            {{-- <th>Students</th> --}}
        </tr>
    </thead>

    <tbody>
        @foreach ($segmentdetailReport as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->payment_date }}</td>
            <td>{{ $row->receipt_no }}</td>
            <td>{{ $row->student_name }}</td>
            <td>{{ $row->student_id }}</td>

            @foreach ($segmentsList as $type)
                <td class="text-right">{{ number_format($row->{$type . '_Amount'} ?? 0, 2) }}</td>
                {{-- <td>{{ $row->{$type . '_Students'} ?? 0 }}</td> --}}
            @endforeach

            <td class="text-right">{{ number_format($row->Total_Amount ?? 0, 2) }}</td>
            {{-- <td>{{ $row->Total_Students ?? 0 }}</td> --}}
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            @foreach ($segmentsList as $type)
                <th class="text-right">{{ number_format($segmentTotals[$type.'_Amount'], 2) }}</th>
                {{-- <th></th> --}}
            @endforeach
            <th class="text-right">{{ number_format($grandTotal ?? 0, 2) }}</th>
            {{-- <th></th> --}}
        </tr>
    </tfoot>
</table>
</div>
@elseif(isset($segmentdetailReport) && count($segmentdetailReport) == 0)
<div class="col-12">
<div class="alert alert-info mt-4">No records found!</div>
</div>
@endif
{{-- @if(isset($segmentreport) && count($segmentreport) && isset($segments))
<div class="col-12">
    <div class="table-responsive mt-4">
        <table id="segmentTable" class="table table-bordered table-striped w-100">
            <thead>
                <tr>
                    <th rowspan="2">S.No</th>
                    <th rowspan="2">Student ID</th>
                    <th rowspan="2">Student Name</th>

                    @foreach ($segments as $segName)
                        <th colspan="2" class="text-center">{{ $segName }}</th>
                    @endforeach

                    <th colspan="2" class="text-center">Total</th>
                </tr>

                <tr>
                    @foreach ($segments as $segId => $segName)
                        <th>Amount</th>
                        <th>No. of Students</th>
                    @endforeach

                    <th>Amount</th>
                    <th>No. of Students</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($segmentreport as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->admno }}</td>
                        <td>{{ $row->student_name }}</td>

                        @foreach ($segments as $segId => $segName)
                            <td>{{ number_format($row->{'SEG_'.$segId.'_Amount'} ?? 0, 2) }}</td>
                            <td>{{ $row->{'SEG_'.$segId.'_Students'} ?? 0 }}</td>
                        @endforeach

                        <td>{{ number_format($row->Total_Amount ?? 0, 2) }}</td>
                        <td>{{ $row->Total_Students ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th></th>

                    @foreach ($segments as $segId => $segName)
                        <th class="text-end">
                            {{ number_format(array_sum(array_column($segmentreport, 'SEG_'.$segId.'_Amount')), 2) }}
                        </th>
                        <th class="text-end">
                            {{ array_sum(array_column($segmentreport, 'SEG_'.$segId.'_Students')) }}
                        </th>
                    @endforeach

                    <th class="text-end">{{ number_format(array_sum(array_column($segmentreport, 'Total_Amount')), 2) }}</th>
                    <th class="text-end">{{ array_sum(array_column($segmentreport, 'Total_Students')) }}</th>
                </tr>
            </tfoot>

        </table>
    </div>
</div>

@elseif(isset($report) && count($report) == 0)
<div class="col-12">
    <div class="alert alert-info mt-4">No records found!</div>
</div>
@endif --}}




        </div>
      </div>
    </div>
  </section>
</div>

@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/pdfmake.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/vfs_fonts.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.print.min.js')}}"></script>
{{-- <script src="{{asset('bundles/datatables/export-tables/buttons.html5.min.js')}}"></script> --}}
<script src="{{asset('js/page/datatables.js')}}"></script>
<script>
   $(document).ready(function () {
    let table = $('#feeTypeTable');

    $('#detailFeeType').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', footer: true },
            { extend: 'csv', footer: true },
            { extend: 'excel', footer: true },
            { extend: 'pdf', footer: true },
            { extend: 'print', footer: true }
        ]

    });
      $('#feeTypeTable').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', footer: true },
            { extend: 'csv', footer: true },
            { extend: 'excel', footer: true },
            { extend: 'pdf', footer: true },
            { extend: 'print', footer: true }
        ]
      });

      $('#segmentTypeTable').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', footer: true },
            { extend: 'csv', footer: true },
            { extend: 'excel', footer: true },
            { extend: 'pdf', footer: true },
            { extend: 'print', footer: true }
        ]
      });

      $('#segmentdetailFeeType').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', footer: true },
            { extend: 'csv', footer: true },
            { extend: 'excel', footer: true },
            { extend: 'pdf', footer: true },
            { extend: 'print', footer: true }
        ]
      });
  });

</script>
@endsection