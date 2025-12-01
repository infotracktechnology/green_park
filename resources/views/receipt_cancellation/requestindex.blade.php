@extends('layouts.app')
@section('title', 'Receipts List (For Cancellation)')
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
                        <h4>Receipts List (For Cancellation)</h4>
                    </div>
  
                    <div class="card-body">


    <table class="table table-bordered table-striped" id="myTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Receipt No</th>
                <th>Date</th>
                <th>Student</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th width="120">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($receipts as $receipt)
                
                @php
                    $request = $receipt->cancelRequest;  
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $receipt->receipt_no ?? 'N/A' }}</td>

                    <td>
                        {{ $receipt->payment_date 
                            ? \Carbon\Carbon::parse($receipt->payment_date)->format('d-m-Y')
                            : 'N/A'
                        }}
                    </td>

                    <td>{{ $receipt->student->student_name ?? 'N/A' }}</td>

                    <td>{{ number_format($receipt->total, 2) }}</td>

                    <td class="text-white">
                        @if($request)
                            @if($request->status === 'pending')
                                <span class="badge bg-warning">Cancel Requested</span>

                            @elseif($request->status === 'approved')
                                <span class="badge bg-danger">Cancelled</span>

                            @else
                                <span class="badge bg-secondary text-danger font-weight-bold">Rejected</span>
                            @endif
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </td>

                    <td>
                        @if(!$request)
                            <button class="btn btn-danger btn-sm"
                                data-toggle="modal"
                                data-target="#cancelModal"
                                data-id="{{ $receipt->id }}">
                                Request Cancel
                            </button>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled>
                                Requested
                            </button>
                        @endif
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
</section>
</div>

{{-- ===================== CANCEL MODAL ===================== --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('receipt.request.cancel') }}" 
          enctype="multipart/form-data" class="modal-content">

        @csrf
        <input type="hidden" name="receipt_id" id="receipt_id">

        <div class="modal-header">
            <h5 class="modal-title">Cancellation Request</h5>
            <button type="button" class="btn btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">

            <div class="mb-3">
                <label class="form-label">Reason for Cancellation</label>
                <textarea name="cancel_reason" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (Optional) — PDF, JPG, PNG (max 2MB)</label>
                <input type="file" name="attachment" class="form-control" accept="image/jpeg, image/png, application/pdf">
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Submit Request</button>
        </div>

    </form>
  </div>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
$(document).ready(function() {
    $('#myTable').DataTable();
    $('#cancelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#receipt_id').val(id);
    });
});
</script>

@endsection