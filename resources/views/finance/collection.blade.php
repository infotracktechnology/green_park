@extends('layouts.app')
@section('title', 'Fees Collection / Payment')
@section('css')
<style>
  .student-info-section {
    background: #f8f9fa;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    margin-bottom: 1.5rem;
  }
  .student-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    margin: 0 auto 10px;
    display: block;
  }
  .student-info-details dl { margin-bottom: 0.5rem; font-size: 0.875rem; }
  .student-info-details dt {
    font-weight: 600;
    width: 120px;
    color: #6c757d;
    padding-right: 5px;
    float: left;
    clear: left;
  }
  .student-info-details dd {
    margin-left: 125px;
    margin-bottom: 0.3rem;
    display: block;
  }
  .fee-details-area .nav-pills .nav-link {
    border-radius: 0;
    margin-bottom: 2px;
    font-size: 0.9rem;
    text-align: left;
    background: #f8f9fa;
    color: #495057;
    border: 1px solid transparent;
    padding: 0.75rem 1rem;
  }
  .fee-details-area .nav-pills .nav-link.active {
    background: #e9ecef;
    color: #007bff;
    border-left: 3px solid #007bff;
  }
  .fee-details-tabs .nav-tabs {
    margin-bottom: 1rem;
  }
  .fee-details-tabs .nav-tabs .nav-link {
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    font-weight: 500;
    color: #6c757d;
    padding: 0.5rem 1rem;
  }
  .fee-details-tabs .nav-tabs .nav-link.active {
    color: #495057;
    background: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
  }
  .fee-breakdown-table th,
  #receipt-details .table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 0.85rem;
  }
  .fee-breakdown-table td, .fee-breakdown-table th,
  #receipt-details .table td, #receipt-details .table th {
    padding: 0.5rem;
    vertical-align: middle;
  }
  .address-icon {
    color: #007bff;
    margin-left: 5px;
  }
</style>
@endsection

@section('main')

<div class="main-content" x-data="feecollection()">
  <section class="section">
    <div class="section-header">
      <h1>Fees Collection / Payment</h1>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body p-3">
        <form method="GET" action="{{ route('fees.collection') }}">
          <div class="row align-items-end gy-2">

            <!-- Branch Input -->
            <div class="col-md-3">
              <label for="branch_filter" class="form-label">Branch</label>
              <select name="branch" id="branch_filter" class="form-control form-control-sm">
                <option value="">All Branches</option>
                @foreach ($branches as $id => $branch)
                  <option value="{{ $id }}" {{ request('branch') == $id ? 'selected' : '' }}>{{ $branch }}</option>
                @endforeach
              </select>
            </div>

            <!-- Coaching Type -->
            <div class="col-md-2">
              <label for="coaching_type" class="form-label">Coaching Type</label>
              <select name="coaching_type" id="coaching_type" class="form-control form-control-sm">
                <option value="">All Types</option>
                @foreach ($coachingTypes as $coachingType)
                  <option value="{{ $coachingType->coaching_type }}" {{ request('coaching_type') == $coachingType->coaching_type ? 'selected' : '' }}>
                    {{ ucfirst($coachingType->coaching_type) }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Search By -->
            <div class="col-md-2">
              <label for="student_search_type" class="form-label">Search By</label>
              <select name="student_search_type" id="student_search_type" class="form-control form-control-sm">
                <option value="student_name" @selected(request('student_search_type') == 'student_name')>Student Name</option>
                <option value="student_id" @selected(request('student_search_type') == 'student_id')>Student ID</option>
                <option value="father_name" @selected(request('student_search_type') == 'father_name')>Father Name</option>
                <option value="mother_name" @selected(request('student_search_type') == 'mother_name')>Mother Name</option>
                <option value="parent_mobile" @selected(request('student_search_type') == 'parent_mobile')>Parent Mobile No</option>
              </select>
            </div>

            <!-- Search Term -->
            <div class="col-md-3 position-relative">
              <label for="student_query" class="form-label">Search Term</label>
              <input type="text" name="student_query" id="student_query" class="form-control form-control-sm"
                     placeholder="Enter Search Term..." value="{{ request('student_query') }}">
            </div>

            <!-- Search Button -->
            <div class="col-md-2 text-end">
              <label class="form-label d-none d-md-block">&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fas fa-search me-1"></i> Search
              </button>
            </div>

          </div>
        </form>

        <!-- Student Profile Card -->
        @if($student)
        <div class="student-info-section mt-4">
          {{-- <h5 class="fw-bold text-primary mb-3">
            <i class="fas fa-user me-2 text-secondary"></i> {{ $student->student_name }}
            <small class="text-muted">({{ $student->student_id }})</small>
          </h5> --}}
          <div class="row">
            <div class="col-md-6">
              <ul class="list-unstyled">
                <li><strong>Father Name:</strong> {{ $student->father_name }}</li>
                <li><strong>Mother Name:</strong> {{ $student->mother_name }}</li>
                <li><strong>Parent Mobile:</strong> {{ $student->ph_no1 }}</li>
              </ul>
            </div>
            <div class="col-md-6">
              <ul class="list-unstyled">
                <li><strong>Student Name:</strong> {{ $student->student_name }}</li>
                <li><strong>Student ID:</strong> {{ $student->student_id }}</li>
                <li><strong>Section:</strong> {{ $student->section }}</li>
                {{-- <li><strong>Coaching Type:</strong> {{ ucfirst($student->coaching_type) }}</li> --}}
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <form method="POST" action="{{ route('fees.collection') }}">
            @csrf
          <h5 class="mb-3">Fee List</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered fee-breakdown-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Installment</th>
                                                    <th>Fee Amount</th> 
                                                    <th>Concession</th>
                                                    <th>Pay Amount</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                             <template x-for="(fee, index) in fees" :key="index">
                                                <tr>
                                                  <td><input type="checkbox" x-model="fee.check" :value="fee.id" :name="`fees[${index}][fee_id]`"></td>
                                                  <td x-text="fee.instalment"></td>
                                                  <td x-text="fee.amount"></td>
                                                  <td></td>
                                                  <td><input type="text" class="form-control form-control-sm numberk" :name="`fees[${index}][payamount]`" x-model="fee.payamount" x-on:change="balance(fee)" :readonly="!fee.check"></td>
                                                  <td><input type="text" class="form-control form-control-sm" :name="`fees[${index}][balance]`" x-model="fee.balance" readonly></td>
                                                </tr>
                                             </template>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row">
                                          <label class="col-lg-10 col-form-label font-weight-bold">Total amount:</label>
                                          <div class="col-lg-2">
                                              <span class="font-weight-bold h5" x-text="total"></span> 
                                              <input type="hidden" name="total" x-model="total">
                                          </div>
                                    </div>


                                    <div class="form-group row mb-2">
                                      <label class="col-lg-2 col-form-label pt-0">Payment Mode:</label> 
                                      <div class="col-lg-10"> 
                                          <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" x-model="payment_mode" name="payment_mode" id="mode_cash" value="cash" checked>
                                              <label class="form-check-label" for="mode_cash">Cash</label>
                                          </div>
                                          <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" x-model="payment_mode" name="payment_mode" id="mode_cheque" value="cheque">
                                              <label class="form-check-label" for="mode_cheque">Cheque</label>
                                          </div>
                                          <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" x-model="payment_mode" name="payment_mode" id="mode_neft"  value="neft">
                                              <label class="form-check-label" for="mode_neft">RTGS / NEFT Payments</label>
                                          </div>

                                          <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" x-model="payment_mode" name="payment_mode" id="mode_bank"  value="bank/upi">
                                            <label class="form-check-label" for="mode_bank">Bank / UPI</label>
                                        </div>

                                      </div>

                                      <div class="col-lg-12"> 
                                        <button type="submit" class="btn btn-primary"> Save</button>
                                      </div>

                                    </div>
          </form>                           
        </div>
        @endif

      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script>
  function feecollection() {
    return {
      payment_mode: 'cash',
      total: 0.00,
      fees: @json($student ? $student->fees() : []),
      balance(fee) {
        if(parseFloat(fee.payamount) > parseFloat(fee.amount)) {
          alert('Amount cannot be greater than fee amount');
          return;
        }
        fee.balance = (parseFloat(fee.amount) - parseFloat(fee.payamount)).toFixed(2);
        this.total = this.fees.reduce((sum, item) => {
        let pay = parseFloat(item.payamount);
        return sum + (isNaN(pay) ? 0 : pay);
      }, 0).toFixed(2);
      },
      init() {
        
      }
    }
  }
</script>
@endsection
