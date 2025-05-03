@extends('layouts.app')
@section('title', 'Fees Collection / Payment')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

<div class="main-content" x-data="feePaymentScreen()">
  <section class="section">
     <div class="section-header">
        <h1>Fees Collection / Payment</h1>
         
     </div>
     <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
          {{-- <form action="{{ route('your.search.route') }}" method="GET"> --}}
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
                    <option value="student_name" {{ request('student_search_type') == 'student_name' ? 'selected' : '' }}>Student Name</option>
                    <option value="user_name" {{ request('student_search_type') == 'user_name' ? 'selected' : '' }}>User Name</option>
                    <option value="student_id" {{ request('student_search_type') == 'student_id' ? 'selected' : '' }}>Student ID</option>
                    <option value="father_name" {{ request('student_search_type') == 'father_name' ? 'selected' : '' }}>Father Name</option>
                    <option value="mother_name" {{ request('student_search_type') == 'mother_name' ? 'selected' : '' }}>Mother Name</option>
                    <option value="parent_mobile" {{ request('student_search_type') == 'parent_mobile' ? 'selected' : '' }}>Mobile Number</option>
                  </select>
                </div>
              
                <!-- Search Term -->
                <div class="col-md-3 position-relative">
                  <label for="student_query" class="form-label">Search Term</label>
                  <input type="text" name="student_query" id="student_query" class="form-control form-control-sm"
                         placeholder="Enter Search Term..." autocomplete="off" value="{{ request('student_query') }}">
                  <div id="student_suggestions" class="list-group position-absolute w-100" style="z-index: 999;"></div>
                </div>
              
                <!-- Search Button -->
                <div class="col-md-2 text-end">
                  <label class="form-label d-none d-md-block">&nbsp;</label>
                  <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-search me-1"></i> Search
                  </button>
                </div>
              </div>
             
              <div class="col-md-10">
                @if($student)
                  <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-user me-2 text-secondary"></i> {{ $student->student_name }}
                    <small class="text-muted">({{ $student->student_id }})</small>
                  </h5>
                  <div class="row">
                    <div class="col-md-6">
                      <ul class="list-unstyled small">
                        <li><strong>Father Name:</strong> {{ $student->father_name }}</li>
                        <li><strong>Mother Name:</strong> {{ $student->mother_name }}</li>
                        <li><strong>Parent Mobile:</strong> {{ $student->ph_no1 }}</li>
                      </ul>
                    </div>
                    <div class="col-md-6">
                      <ul class="list-unstyled small">
                        <li><strong>Section:</strong> {{ $student->section }}</li>
                        <li><strong>Coaching Type:</strong> {{ ucfirst($student->coaching_type) }}</li>
                        <li><strong>Student Status:</strong> {{ ucfirst($student->student_status) }}</li>
                      </ul>
                    </div>
                  </div>
                
                @endif
              </div>
        
    <div class="card fee-details-area shadow-sm">
        <div class="row no-gutters"> 
            <div class="col-md-3" style="border-right: 1px solid #dee2e6;"> {{-- Add border --}}
                <div class="nav flex-column nav-pills m-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                   
                    <a class="nav-link mb-1 active" id="v-pills-feedetails-tab" data-toggle="pill" href="#v-pills-feedetails" role="tab" aria-controls="v-pills-feedetails" aria-selected="true"><i class="fas fa-list-alt fa-fw mr-2"></i>Fee Details</a>
                    <a class="nav-link mb-1" id="v-pills-transactions-tab" data-toggle="pill" href="#v-pills-transactions" role="tab" aria-controls="v-pills-transactions" aria-selected="false"><i class="fas fa-history fa-fw mr-2"></i>View Transactions</a>
                    <a class="nav-link mb-1" id="v-pills-cheque-tab" data-toggle="pill" href="#v-pills-cheque" role="tab" aria-controls="v-pills-cheque" aria-selected="false"><i class="fas fa-money-check fa-fw mr-2"></i>Cheque Payment</a>
                    <a class="nav-link mb-1" id="v-pills-management-tab" data-toggle="pill" href="#v-pills-management" role="tab" aria-controls="v-pills-management" aria-selected="false"><i class="fas fa-tasks fa-fw mr-2"></i>Fee Management</a>
                    <a class="nav-link mb-1" id="v-pills-cancelled-tab" data-toggle="pill" href="#v-pills-cancelled" role="tab" aria-controls="v-pills-cancelled" aria-selected="false"><i class="fas fa-ban fa-fw mr-2"></i>Cancelled Transactions</a>
                    <a class="nav-link mb-1" id="v-pills-ledger-tab" data-toggle="pill" href="#v-pills-ledger" role="tab" aria-controls="v-pills-ledger" aria-selected="false"><i class="fas fa-book fa-fw mr-2"></i>Fee Ledger</a>
                   
                </div>
            </div>

        
            <div class="col-md-9">
                <div class="tab-content p-4" id="v-pills-tabContent">
               
                    <div class="tab-pane fade" id="v-pills-makepayment" role="tabpanel" aria-labelledby="v-pills-makepayment-tab">
                        <h4>Make Payment</h4>
                        <p>Payment form elements would go here...</p>
                       
                    </div>

                  
                    <div class="tab-pane fade show active" id="v-pills-feedetails" role="tabpanel" aria-labelledby="v-pills-feedetails-tab">
                        <div class="fee-details-tabs">
                            
                             <ul class="nav nav-tabs" id="feeDetailsSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="fee-breakdown-tab" data-toggle="tab" href="#fee-breakdown" role="tab" aria-controls="fee-breakdown" aria-selected="true">1. Fee Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="receipt-details-tab" data-toggle="tab" href="#receipt-details" role="tab" aria-controls="receipt-details" aria-selected="false">2. Receipt Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="print-receipt-tab" data-toggle="tab" href="#print-receipt" role="tab" aria-controls="print-receipt" aria-selected="false">3. Print Receipt</a>
                                </li>
                            </ul>

                       
                            <div class="tab-content pt-3" id="feeDetailsSubTabsContent">
                             
                                <div class="tab-pane fade show active" id="fee-breakdown" role="tabpanel" aria-labelledby="fee-breakdown-tab">
                                    <h5 class="mb-3">Fee Breakdown</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered fee-breakdown-table">
                                            <thead>
                                                <tr>
                                                    <th>Fee Type</th>
                                                    <th>Installment</th>
                                                    <th>Fee Amount ($\$)</th> 
                                                    <th>Concession ($\$)</th>
                                                    <th>Paid Amount ($\$)</th>
                                                    <th>Balance ($\$)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            
                                                <tr>
                                                    <td>Tuition fee</td>
                                                    <td>Quarter 2</td>
                                                    <td class="text-right">14925.00</td>
                                                    <td class="text-right">0.00</td>
                                                    <td class="text-right">0.00</td>
                                                    <td class="text-right">14925.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Late Fee (Tuition fee)</td>
                                                    <td>Quarter 2</td>
                                                    <td class="text-right">100.00</td>
                                                    <td class="text-right">0.00</td>
                                                    <td class="text-right">0.00</td>
                                                    <td class="text-right">100.00</td>
                                                </tr>
                                                 <tr>
                                                    <td>Computer fee</td>
                                                    <td>Quarter 2</td>
                                                    <td class="text-right">390.00</td>
                                                    <td class="text-right">0.00</td>
                                                    <td class="text-right">0.00</td>
                                                    <td class="text-right">390.00</td>
                                                </tr>
                                    
                                              
                                            </tbody>
                                            <tfoot class="font-weight-bold bg-light">
                                                <tr>
                                                    <td colspan="2">Total</td>
                                                    <td class="text-right">40705.00</td> 
                                                    <td class="text-right">0.00</td> 
                                                    <td class="text-right">0.00</td> 
                                                    <td class="text-right">40705.00</td> 
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                
                                <div class="tab-pane fade" id="receipt-details" role="tabpanel" aria-labelledby="receipt-details-tab">
                                    <h5 class="mb-3">Receipt Details / Payment Entry</h5>
                                   
                                    <h6>Fee Summary for Payment</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Fee Type</th>
                                                    <th class="text-right">Amount ($\$)</th>
                                                    <th class="text-right">Balance After Amount paid ($\$)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                <tr>
                                                    <td>Tuition fee - Quarter 2</td>
                                                    <td class="text-right">14925.00</td>
                                                    <td class="text-right">0.00</td> 
                                                </tr>
                                                <tr>
                                                    <td>Late Fee (Tuition fee)</td>
                                                    <td class="text-right">100.00</td>
                                                     <td class="text-right">0.00</td> 
                                                </tr>
                                                <tr>
                                                    <td>Computer fee - Quarter 2</td>
                                                    <td class="text-right">390.00</td>
                                                    <td class="text-right">0.00</td> 
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>

                                   
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row align-items-center mb-2">
                                                <label class="col-sm-2 col-form-label font-weight-bold">Total amount:</label>
                                                <div class="col-sm-10">
                                                    <span class="font-weight-bold h5">15450.00</span> 
                                                </div>
                                            </div>

                                            <div class="form-group row mb-2">
                                                <label class="col-sm-2 col-form-label pt-0">Payment Mode:</label> 
                                                <div class="col-sm-10" x-model="payment_mode"> 
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_mode" id="mode_cash" value="cash">
                                                        <label class="form-check-label" for="mode_cash">Cash</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_mode" id="mode_cheque" value="cheque">
                                                        <label class="form-check-label" for="mode_cheque">Cheque</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_mode" id="mode_neft" value="neft">
                                                        <label class="form-check-label" for="mode_neft">RTGS / NEFT Payments</label>
                                                    </div>
                                                     <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_mode" id="mode_challan" value="challan">
                                                        <label class="form-check-label" for="mode_challan">Challan</label>
                                                    </div>
                                                     <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_mode" id="mode_card" value="card">
                                                        <label class="form-check-label" for="mode_card">Credit / debit card swipe</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                              
                                    <div id="cheque_details_section" x-show="payment_mode === 'cheque'" x-transition class="mt-3 p-3 border rounded bg-light">
                                        <h6>Enter Cheque Details <i class="fas fa-chevron-down small ml-1"></i></h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                 <div class="form-group mb-md-0">
                                                    <label for="cheque_date">Cheque Date</label>
                                                    <div class="input-group input-group-sm">
                                                         <input type="text" class="form-control form-control-sm datepicker" id="cheque_date" name="cheque_date" placeholder="Select Date..." value="25 Jul 2017">
                                                         <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-md-3">
                                                 <div class="form-group mb-md-0">
                                                    <label for="cheque_no">Cheque No</label>
                                                    <input type="text" class="form-control form-control-sm" id="cheque_no" name="cheque_no" value="Cheque No ?">
                                                </div>
                                            </div>
                                             <div class="col-md-3">
                                                 <div class="form-group mb-md-0">
                                                    <label for="search_by">Search By</label>
                                                    <select class="form-control form-control-sm" id="search_by" name="search_by">
                                                        <option value="micr" selected>MICR Code</option>
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                             <div class="col-md-3">
                                                 <div class="form-group mb-md-0">
                                                    <label for="micr_code">MICR Code</label>
                                                    <input type="text" class="form-control form-control-sm" id="micr_code" name="micr_code" placeholder="Enter MICR Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label pt-0">Receipt Type:</label>
                                                <div class="col-sm-8">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="receipt_type" id="type_online" value="online" checked>
                                                        <label class="form-check-label" for="type_online">Online</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="receipt_type" id="type_manual" value="manual">
                                                        <label class="form-check-label" for="type_manual">Manual</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row mb-2">
                                                <label for="payment_date" class="col-sm-4 col-form-label">Date of Payment</label>
                                                 <div class="col-sm-8">
                                                    <div class="input-group input-group-sm">
                                                         <input type="text" class="form-control form-control-sm datepicker" id="payment_date" name="payment_date" placeholder="Select Date..." value="25 Jul 2017"> 
                                                         <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                   
                                     <div class="form-group mt-3">
                                        <label for="remarks">Remarks (Optional)</label>
                                        <textarea name="remarks" id="remarks" rows="2" class="form-control form-control-sm"></textarea>
                                    </div>

                                    <hr>
                                    <div class="text-right mt-3">
                                
                                        <button type="button" class="btn btn-secondary mr-2">Cancel</button> 
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Record Payment</button> 
                                    </div>

                                </div>

                               
                                <div class="tab-pane fade" id="print-receipt" role="tabpanel" aria-labelledby="print-receipt-tab">
                                    <h5 class="mb-3">Print Receipt</h5>
                                     <p>Select a paid transaction or receipt to print.</p>
                                     
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-print mr-1"></i> Print Selected Receipt</button>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                    <div class="tab-pane fade" id="v-pills-transactions" role="tabpanel" aria-labelledby="v-pills-transactions-tab">
                        <h4>View Transactions</h4>
                        <p>Transaction history table would go here...</p>
                    </div>
                    <div class="tab-pane fade" id="v-pills-cheque" role="tabpanel" aria-labelledby="v-pills-cheque-tab">
                        <h4>Cheque Payment Details</h4>
                        <p>Details related to cheque payments (e.g., clearance status) would go here...</p>
                   </div>
                    <div class="tab-pane fade" id="v-pills-management" role="tabpanel" aria-labelledby="v-pills-management-tab">
                       <h4>Fee Management</h4>
                       <p>Tools for managing fee structures, assignments, concessions, etc...</p>
                   </div>
                    <div class="tab-pane fade" id="v-pills-cancelled" role="tabpanel" aria-labelledby="v-pills-cancelled-tab">
                       <h4>Cancelled Transactions</h4>
                       <p>List of cancelled fee receipts/transactions...</p>
                   </div>
                    <div class="tab-pane fade" id="v-pills-ledger" role="tabpanel" aria-labelledby="v-pills-ledger-tab">
                       <h4>Fee Ledger</h4>
                       <p>Student's complete fee ledger report...</p>
                   </div>
                </div>
            </div>
        </div>
    </div>
  
  </section>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const students = @json($students);
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const input = document.getElementById('student_query');
      const suggestionsBox = document.getElementById('student_suggestions');
      const typeSelector = document.getElementById('student_search_type');
  
      input.addEventListener('keyup', function () {
        const query = input.value.toLowerCase();
        const field = typeSelector.value;
        suggestionsBox.innerHTML = '';
  
        if (query.length < 2) return;
  
        const matches = students
          .map(student => student[field])
          .filter(val => val && val.toLowerCase().includes(query))
          .filter((v, i, a) => a.indexOf(v) === i) // unique
          .slice(0, 10);
  
        matches.forEach(match => {
          const item = document.createElement('a');
          item.href = '#';
          item.className = 'list-group-item list-group-item-action';
          item.textContent = match;
          item.addEventListener('click', function (e) {
            e.preventDefault();
            input.value = match;
            suggestionsBox.innerHTML = '';
          });
          suggestionsBox.appendChild(item);
        });
      });
  
      document.addEventListener('click', function (e) {
        if (!suggestionsBox.contains(e.target) && e.target !== input) {
          suggestionsBox.innerHTML = '';
        }
      });
    });
  </script>
  
@endsection