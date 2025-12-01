@extends('layouts.app')
@section('title', 'DFC Report')
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
            <h4>DFC Report</h4>
           </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-12 mb-3">
            <form method="GET" action="{{route('fees.report.dfc')}}" id="branchform">
              @csrf
              <div class="row">
                <div class="col-md-3 mb-2">
                  <label class="form-label">Branch</label>
                  <select name="branch_id" class="form-control form-control-sm" required>
                    <option value="">Select Branch</option>
                    @foreach ($branchselect as $id => $branch)
                    <option value="{{$id}}" @selected(request('branch_id') == $id)>{{$branch}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-2">
                  <label class="form-label">Payment Mode</label>
                  <select name="payment_mode" class="form-control form-control-sm">
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
          <div class="col-12">
            <div class="card">
            <div class="card-header">
              <h4>Total Collection</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-striped table-bordered table-sm">
                    {{-- <thead>
                      <tr>
                        <th>Collection Type</th>
                        <th>Amount</th>
                      </tr>
                    </thead> --}}
                    <tbody>
                      <tr>
                        <td>Total Collection</td>
                        <td>&#x20B9; {{ (float)($reports->sum('total')) }}</td>
                      </tr>
                      <tr>
                        <td>Total Online Mode Collection</td>
                        <td>&#x20B9; {{ (float)($reports->where('payment_mode', 'neft')->sum('total')) }}</td>
                      </tr>
                      <tr>
                        <td>Total Cash Mode Collection</td>
                        <td>&#x20B9; {{ (float)($reports->where('payment_mode', 'cash')->sum('total')) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          </div>
          <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="feetable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Receipt No</th>
                        <th>Payment Date</th>
                        <th>Branch</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($reports as $report)
                      <tr @if($report->is_cancelled == 1) class="bg-danger text-white line-through" @endif>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$report->receipt_no}}</td>
                        <td>{{$report->payment_date}}</td>
                        <td>{{optional($report->branch)->name}}</td>
                        <td>{{$report->student_id}}</td>
                        <td>{{optional($report->student)->student_name}}</td>
                        <td>{{$report->payment_mode}}</td>
                        <td>{{$report->total ?? 0}}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
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
    $('#feetable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
                 "<'row'<'col-sm-12 mt-2'B>>",
            buttons: [
                { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-sm btn-outline-secondary' },
                { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-sm btn-outline-success' },
                { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-sm btn-outline-warning' },
                { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape', pageSize: 'A4' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-sm btn-outline-info' }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]
        });
});
</script>
@endsection
