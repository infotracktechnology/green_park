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
  #studentList {
  max-height: 200px;
  overflow-y: auto;
}
</style>
@endsection

@section('main')

<div class="main-content">
  <section class="section">

    <div class="card card-primary shadow-sm mb-4">
      @if(session()->has('success'))
              <div class="row">
                <div class="col-12">
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  </div>
                </div>
              </div>
           @endif
           @if(session()->has('error'))
              <div class="row">
                <div class="col-12">
                  <div class="alert alert-error alert-dismissible fade show" role="alert">
                  {{ session('error') }}
                  </div>
                </div>
              </div>
           @endif
           <div class="card-header">
            <h4>Fees Collection / Payment</h4>
           </div>
      <div class="card-body p-3">
        <form method="GET" action="{{ route('fees.collection') }}" id="searchform">
          <div class="row align-items-end gy-2">

    
            <div class="col-md-3">
              <label for="branch_filter" class="form-label">Branch</label>
              <select name="branch" id="branch_filter" class="form-control form-control-sm">
                @if (!auth()->user()->branch)
                  <option value="all">All Branches</option>
                @endif
                @foreach ($branches as $id => $branch)
                  <option value="{{ $id }}" {{ request('branch') == $id ? 'selected' : '' }}>{{ $branch }}</option>
                @endforeach
              </select>
            </div>

            <!-- Coaching Type -->
            <div class="col-md-2">
              <label for="coaching_type" class="form-label">Coaching Type</label>
              <select name="coaching_type" id="coaching_type" class="form-control form-control-sm">
                <option value="all">All Types</option>
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
                <option value="ph_no1" @selected(request('student_search_type') == 'ph_no1')>Mobile No</option>
              </select>
            </div>

            <div class="col-md-3 position-relative">
              <label for="student_query" class="form-label">Search Term</label>
              <input type="text" name="student_query" id="student_query" class="form-control form-control-sm"
                    placeholder="Enter Search Term..." autocomplete="off" value="{{ request('student_query') }}">

              <!-- Dropdown container -->
              <div id="studentList" class="list-group position-absolute w-100" style="z-index:1000;"></div>
            </div>

            <!-- Search Button -->
            <div class="col-md-2 text-end">
              <input type="hidden" name="student_id" value="{{ request('student_id') }}" id="student_id">
              <label class="form-label d-none d-md-block">&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-sm w-100">
                Get
              </button>
            </div>

          </div>
        </form>

        <!-- Student Profile Card -->
        @if($student)
        <div class="student-info-section mt-4">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm table-borderless">
                <tbody>
                  <tr>
                    <td><strong>Student Name:</strong></td>
                    <td>{{ $student->student_name }}</td>
                  </tr>
                  <tr>
                    <td><strong>Student ID:</strong></td>
                    <td>{{ $student->student_id }}</td>
                  </tr>
                  <tr>
                    <td><strong>Father Name:</strong></td>
                    <td>{{ $student->father_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Mother Name:</strong></td>
                    <td>{{ $student->mother_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Mobile No:</strong></td>
                    <td>{{ $student->ph_no1 ?? '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm table-borderless">
                <tbody>
                  <tr>
                    <td><strong>Coaching Type:</strong></td>
                    <td>{{ $student->coaching_type ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Branch:</strong></td>
                    <td>{{ $student->branch->name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Course:</strong></td>
                    <td>{{ $student->course ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Batch:</strong></td>
                    <td>{{ $student->batch ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Section:</strong></td>
                    <td>{{ $student->section ?? '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <form method="POST" action="{{ route('fees.collection') }}" id="feesForm">
            @csrf
           <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Fee List</h5>
            <div>
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#concessionModal">
                  Apply Concession
              </button>

              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentHistoryModal">
                Payment History
              </button>
            </div>
            {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#concessionModal">
              Concession
            </button> --}}
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-bordered fee-breakdown-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Installment</th>
                  <th>Fee Amount</th> 
                  <th>Balance</th>
                  <th>Pay Amount</th>
                </tr>
              </thead>
              <tbody>
                @foreach($student->fees() as $fee)
                  <tr>
                    <td>
                      <input type="checkbox" class="fee-check" name="fees[{{$loop->index}}][feeplan_item_id]" value="{{$fee['id']}}">
                    </td>
                    <td>{{ $fee['instalment'] }}
                      <input type="hidden" class="bill_type_id" value="{{ $fee['bill_type_id'] }}">
                      <input type="hidden" name="studentID" value="{{$student->id}}">
                      <input type="hidden" class="concession_id_{{$fee['id']}}" name="fees[{{$loop->index}}][concession_id]">
                      <input type="hidden" class="concession_amt_{{$fee['id']}}" name="fees[{{$loop->index}}][concession_amount]">
                    </td>
                    <td class="feeamount fee_amount_{{$fee['id']}}">{{ $fee['amount'] }}</td>
                    <td><input type="text" class="form-control form-control-sm numberk balance" readonly></td>
                    <td>
                      <input type="text" class="form-control form-control-sm numberk payamount"
                            name="fees[{{$loop->index}}][payamount]" readonly>
                    </td>
                  </tr>
                @endforeach

              </tbody>
            </table>
          </div>

          <div class="row">
            <label class="col-lg-10 col-form-label font-weight-bold">Total amount:</label>
            <div class="col-lg-2">
              <span class="font-weight-bold h5 totalspan"></span> 
              <input type="hidden" name="total" id="total">
            </div>
          </div>

<div class="mt-3">
  <div class="form-group row mb-2">
    <label class="col-lg-2 col-form-label pt-0">Payment Mode:</label>
    <div class="col-lg-10">
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="payment_mode" id="mode_cash" value="cash" checked>
        <label class="form-check-label" for="mode_cash">Cash</label>
      </div>

      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="payment_mode" id="mode_neft" value="neft">
        <label class="form-check-label" for="mode_neft">RTGS / NEFT Payments/UPI</label>
      </div>
    </div>
  </div>

  <!-- Online Bank Transfer Fields -->
  <div class="row" id="neftDiv">
    <div class="col-md-3 mb-2">
  <label class="form-label">Transfer Date</label>
  <input type="date" name="bank_transfer_date" class="form-control form-control-sm">
</div>
  <div class="col-md-3 mb-2">
  <label class="form-label">Transfer Mode</label>
  <select name="bank_transfer_mode" class="form-control form-control-sm select2">
    <option value="">Select Mode</option>
    <option value="phonepe">PhonePe</option>
    <option value="gpay">GPay</option>
    <option value="paytm">PayTM</option>
    <option value="amazonpay">Amazon Pay</option>
    <option value="neft">NEFT</option>
    <option value="rtgs">RTGS</option>

    <!-- Bank Options -->
    <option value="sbi">State Bank of India</option>
    <option value="hdfc">HDFC Bank</option>
    <option value="icici">ICICI Bank</option>
    <option value="axis">Axis Bank</option>
    <option value="kotak">Kotak Mahindra Bank</option>
    <option value="indusind">IndusInd Bank</option>
    <option value="iob">Indian Overseas Bank</option>
    <option value="indianbank">Indian Bank</option>
    <option value="karurvysya">Karur Vysya Bank</option>
    <option value="cubi">City Union Bank</option>
    <option value="tmbl">Tamilnad Mercantile Bank</option>
  </select>
</div>

    <div class="col-md-3 mb-2">
      <label class="form-label">Bank Name</label>
      <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="Bank Name">
    </div>
    <div class="col-md-3 mb-2">
      <label class="form-label">Transaction ID</label>
      <input type="text" name="transaction_id" class="form-control form-control-sm" placeholder="Transaction ID">
    </div>
  </div>


  <div class="row mt-3">
    <div class="col-md-3 mb-2">
      <label class="form-label">Payment Date</label>
      <input type="date" class="form-control form-control-sm" name="payment_date" required 
             value="{{ date('Y-m-d') }}">
    </div>
    <div class="col-md-3 mb-2">
      <label class="form-label">Attachment</label>
      <input type="file" class="form-control form-control-sm" name="payment_attachment">
    </div>
  </div>

  <div class="col-md-12 mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
  </div>
</div>
          </form>
</div>

      @endif

    </div>
  </div>
</section>
</div>



@if($student)

<!-- Modal to view the payment History -->
<div class="modal fade" id="paymentHistoryModal" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentHistoryModalLabel">Payment History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive mb-3">
          <table class="table table-bordered table-sm align-middle">
            <thead>
              <tr>
                <th>Payment Date</th>
                <th>Receipt No</th>
                <th>Mode</th>
                {{-- <th>Installment</th> --}}
                <th>Amount</th>
                <th>Print</th>
              </tr>
            </thead>
            <tbody id="paymentHistoryModalBody">
              @foreach ($student->feespaidhistory as $feecollection)
                {{-- @foreach ($feecollection->item as $feecollectionitem)
                  <tr @if ($feecollection->is_cancelled) class="table-danger" style="text-decoration: line-through; color: #6c757d;" @endif>
                    <td>{{ $feecollection->payment_date }}</td>
                    <td>{{ $feecollection->receipt_no }}</td>
                    <td>{{ $feecollection->payment_mode }}</td>
                    <td>{{ $feecollectionitem->feeplanitem->instalment }}</td>
                    <td>{{ $feecollectionitem->payamount }}</td>
                    <td>
                      <button class="btn btn-primary btn-sm" target="_blank" onclick="{{ route('receipt.print', $feecollection->id) }}">
                        <i class="fas fa-print"></i>
                      </button>
                    </td>
                  </tr>
                @endforeach --}}

                <tr @if ($feecollection->is_cancelled) class="table-danger" style="text-decoration: line-through; color: #6c757d;" @endif>
                  <td>{{ $feecollection->payment_date }}</td>
                  <td>{{ $feecollection->receipt_no }}</td>
                  <td>{{ $feecollection->payment_mode }}</td>
                  {{-- <td>{{ $feecollectionitem->feeplanitem->instalment }}</td> --}}
                  <td>{{ $feecollection->total ?? 0 }}</td>
                  <td>
                    <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('fees.receipt', [$feecollection->id, 'copy' => 1]) }}">
                      <i class="fas fa-print"></i>
                    </a>
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


<!-- Modal -->
<div class="modal fade" id="concessionModal" tabindex="-1" aria-labelledby="concessionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="concessionForm">
        <div class="modal-header">
          <h5 class="modal-title" id="concessionModalLabel">Apply Concession</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>Installment</th>
                  <th>Fee Amount</th>
                  <th>Concession Type</th>
                  <th>Concession Amount</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>
                @foreach($student->fees() as $fee)
                  {{-- <tr @if($fee['concession'] > 0) class="table-danger" style="text-decoration: line-through; color: #6c757d;" @endif> --}}
                    <tr>
                    <td>{{ $fee['instalment'] }}</td>
                    <td class="feeamount">{{ $fee['amount'] }}
                      <input type="hidden" class="feeplan_{{$fee['id']}}" value="{{$fee['id']}}">
                      <input type="hidden" class="feeamount_{{$fee['id']}}" value="{{ $fee['amount'] }}">
                    </td>
                    {{-- <td @if($fee['concession'] > 0) style="pointer-events: none;" @endif> --}}
                    <td>
                      <select class="form-control form-control-sm concession_type">
                        <option value="">Select Concession Type</option>
                        @foreach($concessions as $concession)
                          <option value="{{ $concession->id }}" data-con_type="{{ $concession->type }}" data-con_value="{{ $concession->value }}">{{ $concession->name }}</option>
                        @endforeach
                        <option value="0" data-con_type="manual" data-con_value="0">Manual</option>
                      </select>
                    </td>
                    <td>
                      <input type="number" class="form-control form-control-sm concession_amount" value="0" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control form-control-sm concession_value" value="{{ $fee['amount'] }}" readonly>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary applyconcessionbtn">Apply</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endif
@endsection



@section('js')

<script>
$(document).ready(function(){
  const students = @json($students);
  $('#neftDiv').hide();

  $('#branch_filter, #coaching_type, #student_search_type').on('change', function(){
    $('#student_query').val('');
    $('#student_id').val('');
  });

  $('#student_query').on('input', function(){
    let query = $(this).val();

    if(query.length > 1){ 
      branch_filtered = [];
      coaching_filtered = [];
      students_filtered = [];
      data = '';
        branch_id = $('#branch_filter').val();
        coaching_type = $('#coaching_type').val();
        student_search_type = $('#student_search_type').val();
        if(branch_id == '' || coaching_type == '' || student_search_type == '') {
          return;
        }
        if(branch_id == 'all') {
          branch_filtered = students;
        } else {
          branch_filtered = students.filter(student => student.campus == branch_id);
        }
        if(coaching_type == 'all') {
          coaching_filtered = branch_filtered;
        } else{
          coaching_filtered = branch_filtered.filter(student => student.coaching_type == coaching_type);
        }
        if(student_search_type == 'student_name') {
          students_filtered = coaching_filtered.filter(student => (student.student_name || '').toLowerCase().includes(query.toLowerCase()));
        } else if(student_search_type == 'student_id') {
          students_filtered = coaching_filtered.filter(student => (student.student_id || '').toLowerCase().includes(query.toLowerCase()));
        } else if(student_search_type == 'father_name') {
          students_filtered = coaching_filtered.filter(student => (student.father_name || '').toLowerCase().includes(query.toLowerCase()));
        } else if(student_search_type == 'mother_name') {
          students_filtered = coaching_filtered.filter(student => (student.mother_name || '').toLowerCase().includes(query.toLowerCase()));
        } else if(student_search_type == 'ph_no1') {
          students_filtered = coaching_filtered.filter(student => (student.ph_no1 || '').toLowerCase().includes(query.toLowerCase()));
        }
        
        students_filtered.forEach(student => {
          data += '<a href="javascript:void(0);" class="list-group-item list-group-item-action student-item" data-student_id="' + student.id + '">' + student.student_name + '</a>'
        });
          $('#studentList').fadeIn().html(data);
    } else {
      $('#studentList').fadeOut();
    }
  });

  // When user clicks on a suggestion
  $(document).on('click', '.student-item', function(){
    $('#student_query').val($(this).text());
    $('#student_id').val($(this).data('student_id'));
    $('#studentList').fadeOut();
  });

  // Hide dropdown if clicked outside
  $(document).click(function(e){
    if(!$(e.target).closest('#student_query, #studentList').length){
      $('#studentList').fadeOut();
    }
  });
  $('#searchform').on('submit', function(e){
    e.preventDefault();
    if($('#student_id').val() == '') {
      alert('Please select a student');
      return;
    }
    $('#studentList').fadeOut();
    this.submit();
  });

  setTimeout(function() {
            $(".alert").fadeOut(1500);
        }, 3000);

  @if($student)
    $('.fee-check').on('change', function() {
        let $payAmountInput = $(this).closest('tr').find('.payamount');
        
        if ($(this).is(':checked')) {
            $payAmountInput.prop('readonly', false);
            $payAmountInput.removeClass('readonly-style');
            $payAmountInput.focus();
        } else {
            $payAmountInput.prop('readonly', true);
            $payAmountInput.val('');
            $payAmountInput.addClass('readonly-style');
        }
      let row = $(this).closest('tr');
        calculatetotal(row);
      calculatebalance(row);
    });
    
    $('.payamount').prop('readonly', true);

    $('.payamount').on('input', function() {
      let row = $(this).closest('tr');
      calculatetotal(row);
      calculatebalance(row);
      if($(this).val() <= 0) {
        $(this).val('');
      }

      let rowfeeamount = parseFloat(row.find('.feeamount').text().trim());

      if($(this).val() > rowfeeamount) {
        $(this).val(rowfeeamount).trigger('input');
      }
      
    });

    function calculatebalance(row){
      let feeamt = parseFloat(row.find('.feeamount').text().trim());
      
      let amt = parseFloat(row.find('.payamount').val()) || 0;
      row.find('.balance').val(feeamt - amt);

      
      if((feeamt - amt) <= 0) {
        row.find('.balance').val('');
      }

      if(!row.find('.fee-check').is(':checked')) {
        row.find('.balance').val('');
      }
    }

    function calculatetotal(row){
      let total = 0;
      $('.payamount').each(function() {
        let val = parseFloat($(this).val());
        if (val) {
          total += val;
        }
      });
      
      $('.totalspan').text(total.toFixed(2));
      $('#total').val(total.toFixed(2));
    }

    $('#feesForm').on('submit', function(e) {
      e.preventDefault();

      if (!this.checkValidity()) {
          return;
      }
      
      if($('#total').val() <= 0 || $('#total').val() == null) {
        alert('There is no valid amount to proceed');
        return;
      }
      
      if($('input[name="payment_mode"]:checked').val() == 'neft') {
        
       let firstBillType = null;
       let hasError = false;
    
    $('.fee-breakdown-table').find('.fee-check:checked').each(function() {
        let bill_type_id = $(this).closest('tr').find('.bill_type_id').val();
        
        if (firstBillType === null) {
            firstBillType = bill_type_id;
        } else if (firstBillType !== bill_type_id) {
            alert('Bill type differs');
            hasError = true;
            return false;
        }
    });
    
    if (hasError) return;

    if($('input[name="bank_transfer_mode"]').val == '' || $('input[name="bank_name"]').val == '' || $('input[name="transaction_id"]').val == '' || $('input[name="bank_transfer_date"]').val == '') {
      alert('Please fill all the NEFT details');
      return;
    }
   
      } 


    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).text('Please wait...');
      this.submit();
    });

    $('#mode_neft').on('change', function() {
      
      if($('input[name="payment_mode"]:checked').val() == 'neft') {
        $('input[name="bank_transfer_date"]').prop("required", true);
        $('select[name="bank_transfer_mode"]').prop("required", true);
        $('input[name="bank_name"]').prop("required", true);
        $('input[name="transaction_id"]').prop("required", true);
        $('#neftDiv').show();

      } else {
        $('#neftDiv').hide();
      }
    });
    $('#mode_cash').on('change', function() {
      
      if($('input[name="payment_mode"]:checked').val() == 'cash') {
        $('input[name="bank_transfer_date"]').prop("required", false).val('');
        $('select[name="bank_transfer_mode"]').prop("required", false).val('').trigger('change');
        $('input[name="bank_name"]').prop("required", false).val('');
        $('input[name="transaction_id"]').prop("required", false).val('');
        $('#neftDiv').hide();
      } else {
        $('#neftDiv').show();
      }
    });

    $(document).on('change', '.concession_type', function() {

      let row = $(this).closest('tr');

      let feeID = row.find('input[class^="feeplan_"]').val();
      let originalAmount = parseFloat(row.find('input[class^="feeamount_"]').val());

      let selected = $(this).find('option:selected');

      let conType = selected.data('con_type');   // percentage or fixed
      let conValue = parseFloat(selected.data('con_value')) || 0;
      

      let newAmount = originalAmount;

      if (conType === "percentage") {
          newAmount = originalAmount - (originalAmount * (conValue / 100));
          if (newAmount < 0) {
              newAmount = 0;
          }
      }
      else if (conType === "fixed") {
          newAmount = originalAmount - conValue;
          if (newAmount < 0) {
              newAmount = 0;
          }
      } else if (conType === "manual") {
        // Enable user to type new amount
        row.find('.concession_amount').val("").prop("readonly", false);
        row.find('.concession_value').val("");
        return; // IMPORTANT — keep existing flow untouched
    }
      concession_amount = originalAmount - newAmount;
      // Update the concession value input
      row.find('.concession_value').prop("readonly", true).val(newAmount);
      row.find('.concession_amount').prop("readonly", true).val(concession_amount);

  });

  $(document).on("keyup", ".concession_amount", function() {

      let row = $(this).closest("tr");

      // Only if manual selected
      if (row.find(".concession_type option:selected").data("con_type") !== "manual") return;

      let originalAmount = parseFloat(row.find('input[class^="feeamount_"]').val());

      conValue = parseFloat($(this).val()) || 0;

      newAmount = originalAmount - conValue;
      if (newAmount < 0) {
          newAmount = 0;
      }
      // let newAmount = parseFloat($(this).val()) || 0;

      // // Boundaries
      // if (newAmount < 0) newAmount = 0;
      // if (newAmount > originalAmount) newAmount = originalAmount;

      // $(this).val(newAmount);

      row.find('.concession_value').prop("readonly", true).val(newAmount);

      // let concession_amount = originalAmount - newAmount;

      // row.find('.concession_amount').val(concession_amount.toFixed(2));

      // // Update the concession value input
      // row.find('.concession_value').val(newAmount.toFixed(2));
  });


  $(document).on('click', '.applyconcessionbtn', function () {

    $('#concessionModal tbody tr').each(function () {

        let row = $(this);

        let feeID = row.find('input[class^="feeplan_"]').val();
        let revisedAmount = parseFloat(row.find('.concession_value').val());
        let concession_amount = parseFloat(row.find('.concession_amount').val() || 0);
        let concession_id = row.find('.concession_type').val();

        if (!feeID || isNaN(revisedAmount)) return;

        // Update main table fee amount
        $('.fee_amount_' + feeID).text(revisedAmount.toFixed(2));
        $('.concession_id_' + feeID).val(concession_id);
        // Update main table concession amount
        $('.concession_amt_' + feeID).val(concession_amount.toFixed(2));

        // Update main table payamount input
        // $('.fee_pay_' + feeID).val(revisedAmount.toFixed(2));
    });

    $('#concessionModal').modal('hide');
});


  @endif

  // $('#feesForm').on('submit', function () {
  //       // If form is NOT valid → prevent disable
  //       if (!this.checkValidity()) {
  //           return; // Let browser show validation errors
  //       }
  //       const submitBtn = $(this).find('button[type="submit"]');
  //       submitBtn.prop('disabled', true).text('Please wait...');
  //   });
  });
</script>
<script>
@if(session('last_receipt_id'))
    // Open receipt in a new tab and trigger print
    let receiptUrl = "{{ route('fees.receipt', session('last_receipt_id')) }}";
    let win = window.open(receiptUrl, '_blank');
    // // Optional: print after tab loads
    // win.onload = function() {
    //     win.print();
    // };
@endif
</script>
@endsection