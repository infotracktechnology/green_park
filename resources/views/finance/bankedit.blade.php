@extends('layouts.app')

@section('title', 'Edit Banks')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
                  <div class="card card-primary">
                     <div class="card-header d-flex justify-content-between align-items-center">
                                 <h4>Edit Bank</h4>
                                 <a href="{{ route('bank.create') }}" class="btn btn-secondary btn-sm float-right"><i class="fa fa-arrow-left"></i> Back</a>
                              </div>
                     
                        <div class="card-body">
                            <form method="post" id="myForm" action="{{ route('bank.update', $bank_accounts->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                           <div class="row">
                              <div class="col-md-6 mb-2">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="Bank Name" value="{{ $bank_accounts->bank_name }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Branch</label>
                                <input type="text" name="branch_name" class="form-control form-control-sm" placeholder="Branch" value="{{ $bank_accounts->branch_name }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">IFSC code</label>
                                <input type="text" name="ifsc_code" class="form-control form-control-sm" placeholder="IFSC" value="{{ $bank_accounts->ifsc_code }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">MICR code</label>
                                <input type="text" name="micr_code" class="form-control form-control-sm" placeholder="MICR" value="{{ $bank_accounts->micr_code }}">
                            </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Account Name</label>
                                    <input type="text" name="account_name" class="form-control form-control-sm" placeholder="Account Name" value="{{ $bank_accounts->account_name }}" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_no" class="form-control form-control-sm" placeholder="Account Number" value="{{ $bank_accounts->account_no }}" required>
                                </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">Update</button>
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