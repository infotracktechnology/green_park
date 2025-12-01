@extends('layouts.app')
@section('title', 'Fees Plan')
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
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
                        <form action="{{ route('receipt.request.cancel', $receipt->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Cancel Reason <span class="text-danger">*</span></label>
                                <textarea name="cancel_reason" class="form-control" required></textarea>
                            </div>

                            <div class="form-group mt-3">
                                <label>Attachment (optional) — PDF, JPG, PNG (max 2MB)</label>
                                <input type="file" name="attachment" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-danger mt-3">
                                Submit Cancellation Request
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection