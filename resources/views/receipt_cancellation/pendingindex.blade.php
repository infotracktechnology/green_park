@extends('layouts.app')
@section('title', 'Receipts List (Approve / Reject Cancellation)')
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
              @if(session()->has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
              </div>
           @endif
           @if(session()->has('error'))
              <div class="alert alert-error alert-dismissible fade show" style="background-color:red ! important" role="alert">
                  {{ session('error') }}
              </div>
           @endif
           @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
           @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-header">
                        <h4>Pending Receipt Cancellation Requests</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Receipt No.</th>
                                            <th>Amount</th>
                                            <th>Requested By</th>
                                            <th>Reason</th>
                                            <th>Requested At</th>
                                            <th>Attachment</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($pendingreceipts as $pendingreceipt)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pendingreceipt->receipt->receipt_no }}</td>
                                            <td>{{ number_format($pendingreceipt->receipt->total, 2) }}</td>
                                            <td>{{ $pendingreceipt->requestedBy->username }}</td>
                                            <td>{{ $pendingreceipt->cancel_reason }}</td>
                                            <td>{{ $pendingreceipt->created_at->format('d-m-Y h:i A') }}</td>

                                            <td>
                                                <button class="btn btn-info btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#receiptDetailModal"
                                                    data-id="{{ $pendingreceipt->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($pendingreceipt->attachment)
                                                <a href="{{ Storage::disk('public')->url($pendingreceipt->attachment) }}" 
                                                    target="_blank" 
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                                @else
                                                -
                                                @endif
                                            </td>


                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="false">
                                                        Approve / Reject
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <li>
                                                            <form action="{{ route('receipt.cancel.approve', $pendingreceipt->id) }}" method="post" onsubmit="return confirm('Are you sure you want to Approve the cancel request for Receipt No: {{ $pendingreceipt->receipt->receipt_no }}?')">
                                                                @csrf 
                                                                @method('put')
                                                                <button type="submit" class="dropdown-item btn btn-success btn-sm">
                                                                    Approve
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('receipt.cancel.reject', $pendingreceipt->id) }}" method="post" onsubmit="return confirm('Are you sure you want to Reject the cancel request for Receipt No: {{ $pendingreceipt->receipt->receipt_no }}?')">
                                                                @csrf 
                                                                @method('put')
                                                                <button type="submit" class="dropdown-item btn btn-danger btn-sm">
                                                                    Reject
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
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
</section>
</div>

<div class="modal fade" id="receiptDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="btn btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm text-dark" data-dismiss="modal">Close</button>
        </div>
        </div>

  </div>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{asset('js/page/datatables.js')}}"></script>
<script>
  const table = $('#myTable').DataTable({
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  });

  pendingreceipts = @json($pendingreceipts);
  

  $('#receiptDetailModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var receipt = pendingreceipts.find(receipt => receipt.id == id);
    var modal = $(this);
    modal.find('.modal-title').text('Receipt Details');
    modal.find('.modal-body').html(`
        <p>Receipt No. : ${receipt.receipt.receipt_no}</p>
        <p>Amount : ${receipt.receipt.total}</p>
        <p>Requested By : ${receipt.requested_by.username}</p>
        <p>Reason : ${receipt.cancel_reason}</p>
        <p>Requested At : ${receipt.created_at}</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fee Type</th>
                        <th>Fee Plan</th>
                        <th>Instalment</th>
                        <th>Concession Amount</th>
                        <th>Paid Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${receipt.receipt.item.map((item, index) => `
                    <tr>
                            <td>${index + 1}</td>
                            <td>${item.feeplanitem.fee_type}</td>
                            <td>${item.feeplanitem.feeplanmaster.name}</td>
                            <td>${item.feeplanitem.instalment}</td>
                            <td>${item.concession_amount}</td>
                            <td>${item.payamount}</td>
                        </tr>`).join('')}
                </tbody>
            </table>
        </div>
    `);
  });
</script>
@endsection