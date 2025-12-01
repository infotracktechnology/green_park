@extends('layouts.app')

@section('title', 'Fees Plan')
@section('css')

<!-- Additional CSS to fix Select2 styling -->
<style>
.select2-container {
    width: 100% !important;
}
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #e4e6fc !important;
    border-radius: 4px !important;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
    color: #495057 !important;
}
.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 10px !important;
}
.select2-container .select2-selection--multiple {
    min-height: 38px !important;
    border: 1px solid #e4e6fc !important;
    border-radius: 4px !important;
}
.select2-dropdown {
    border: 1px solid #e4e6fc !important;
    border-radius: 4px !important;
}
</style>
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="feesplan()">
                     <form method="post" id="myForm" action="{{ route('feesplan.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3 d-flex align-items-center justify-content-between">
                                 <h6 class="col-deep-purple">Add Fees Plan</h6>
                                 <a href="{{ route('feesplan.index') }}" class="btn btn-secondary btn-sm float-right"><i class="fa fa-arrow-left"></i> Back</a>
                              </div>
                           <div class="row">
                              
                            <div class="form-group col-lg-4">
                                    <label for="academic_year">Student Coaching Type</label>
                                    <select name="coaching_type"  class="form-control form-control-sm select2" required>
                                      <option value="">Select  Coaching Type</option>
                                        @foreach ($coaching_type as $row)
                                            <option value="{{ $row->coaching_type }}">{{ $row->coaching_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-4">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" id="branch_id"  class="form-control form-control-sm select2 branch" required>
                                      <option value="">Select Branch</option>
                                        @foreach ($branchselect as $id => $branch)
                                            <option value="{{ $id }}">{{ $branch }}</option>
                                        @endforeach
                                    </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="courseselect">Course</label>
                                <select name="course" id="courseselect"  class="form-control form-control-sm select2 course" required>
                                      <option value="">Select Course</option>
                                        {{-- @foreach ($courseselect as $course)
                                            <option value="{{ $course->course }}">{{ $course->course }}</option>
                                        @endforeach --}}
                                    </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="batchselect">Batch</label>
                                <select name="batch[]" id="batchselect"  class="form-control form-control-sm select2 batch" required multiple>
                                      <option value="">Select Batch</option>
                                        {{-- @foreach ($batchselect as $batch)
                                            <option value="{{ $batch->batch }}">{{ $batch->batch }}</option>
                                        @endforeach --}}
                                    </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="bill_type_id">Bill Type</label><small  class="ml-2 text-primary" data-toggle="modal" data-target="#billTypeModal" style="cursor: pointer;"><i class="fa fa-plus"></i> Add</small>
                                <select name="bill_type_id" id="bill_type_id"  class="form-control form-control-sm select2" required>
                                      <option value="">Select Bill Type</option>
                                        @foreach ($bill_types as $id => $bill_type)
                                            <option value="{{ $id }}">{{ $bill_type }}</option>
                                        @endforeach
                                    </select>
                            </div>

                              <div class="form-group col-lg-4">
                                <label for="hostel">Hostel Plan ?</label>
                                <select name="is_hostel" id="hostel"  class="form-control form-control-sm select2" required>
                                      <option value="">Select Yes/No</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                              </div>

                              <div class="form-group col-lg-4">
                                <label for="name">Fees Plan Name</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="segment">Segment</label><small  class="ml-2 text-primary" data-toggle="modal" data-target="#segmentsModal" style="cursor: pointer;"><i class="fa fa-plus"></i> Add</small>
                                <select name="segment_id" id="segment"  class="form-control form-control-sm select2" required>
                                    <option value="">Select Segment</option>
                                    {{-- @foreach ($segments as $segment)
                                        <option value="{{ $segment->id }}">{{ $segment->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="is_active">Status</label>
                                <select name="is_active" id="is_active"  class="form-control form-control-sm select2" required>
                                      <option value="">Select </option>
                                        <option value="1">Active</option>
                                        <option value="0">InActive</option>
                                    </select>
                              </div>
                           </div>

                              

                               
                           <div class="form-group col-lg-4">
                           <label>Fee Type ()</label>
                           <select x-model="feetype" x-on:change="removerows()" name="fee_type"  class="form-control form-control-sm">
                             <option value="">Select Fee Type</option>
                             @foreach ($feetype as $row)
                            <option value="{{$row['feetype']}}">{{$row['feetype']}}</option>
                             @endforeach
                           </select>
                            </div>

                              <div class="form-group col-lg-3">
                                 <label>Amount</label>
                                 <input type="text" x-model.number="amount" class="form-control form-control-sm">
                               </div>

                               <div class="form-group col-lg-3">
                                <label>No of Instalments</label>
                                <input type="text" x-model.number="instalment" class="form-control form-control-sm numberk">
                              </div>


                               <div class="form-group col-lg-2">
                                 <button type="button" @click="addRow" class="btn btn-primary m-t-20">Add Fee Type</button>
                              </div>

                               <div class="col-lg-12">
                                 <div class="table-responsive">
                                    <table class="table table-bordered">
                                      <thead>
                                        <tr>
                                          <th>Instalment Name</th>
                                          <th>Amount</th>
                                          <th>Invoice Date</th>
                                          <th>Due Date</th>
                                          <th width="30"></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <template x-for="(row, index) in structure" :key="index">
                                          <tr>
                                            <td>
                                              <input type="text" class="form-control form-control-sm" x-model="row.name" :name="`item[${index}][instalment]`" required>
                                              <input type="hidden" x-model="row.fee_type" :name="`item[${index}][fee_type]`">
                                            </td>
                                            <td>
                                              <input  type="text" class="form-control form-control-sm" x-model="row.amount" :name="`item[${index}][amount]`" required>
                                            </td>
                                            <td>
                                                <input  type="date" class="form-control form-control-sm" :name="`item[${index}][invoice_date]`" required>
                                            </td>
                                            <td>
                                                <input  type="date" class="form-control form-control-sm" :name="`item[${index}][due_date]`" required>
                                            </td>
                                            <td></td>
                                            {{-- <td>
                                              <button   class="btn btn-danger"  @click="removeRow(index)">
                                                <i class="fas fa-trash"></i>
                                            </td> --}}
                                          </tr>
                                        </template>
                                      </tbody>
                                    </table>
                                  </div>
                               </div>

                              

                              <div class="form-group col-lg-12">
                                 <button type="submit" class="btn btn-primary">Submit</button>
                              </div>

                           </div>
                        </div>
                     </form>
                  </div>
              </div>
          </div>
      </div>
   </section>
</div>
<!-- Modal for creating a bill type -->
<div class="modal fade" id="billTypeModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Bill Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
          <div class="form-group">
            <label for="name">Bill Type</label>
            <input type="text" name="name" id="bill_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="bill_branch_id">Branch</label>
            <select name="branch_id" id="bill_branch_id"  class="form-control form-control-sm select2" required>
                <option value="">Select Branch</option>
                  @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group">
            <label for="course">Course</label>
            <select name="course" id="bill_course" class="form-control form-control-sm select2" required>
              <option value="">Select Course</option>
            </select>
          </div>
          <div class="form-group">
            <label for="batch">Batch</label>
            <select name="batch" id="bill_batch" class="form-control form-control-sm select2" required>
              <option value="">Select Batch</option>
            </select>
          </div>

          <div class="form-group">
            <label for="bank_account">Bank</label>
            <select name="bank_accounts_id" id="bank_account"  class="form-control form-control-sm select2" required>
                <option value="">Select Bank Account</option>
                  @foreach ($bank_accounts as $bank_account)
                      <option value="{{ $bank_account->id }}">{{ $bank_account->account_no . ' - ' . $bank_account->bank_name . ' - ' . $bank_account->branch_name  }}</option>
                  @endforeach
              </select>
          </div>

          <div class="form-group">
            <button type="button" class="btn btn-primary billsubmit">Create</button>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for creating a segment -->
<div class="modal fade" id="segmentsModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Segment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('segment.store') }}" method="POST" id="segmentForm">
          @csrf
          <div class="form-group">
            <label for="name">Segment Name</label>
            <input type="text" name="name" id="segment_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="segment_branch_id">Branch</label>
            <select name="branch_id" id="segment_branch_id"  class="form-control form-control-sm select2" required>
                <option value="">Select Branch</option>
                  @foreach ($branchselect as $id => $branch)
                      <option value="{{ $id }}">{{ $branch }}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group">
            <label for="is_active">Status</label>
            <select name="is_active" id="segmentstatus"  class="form-control form-control-sm select2" required>
              <option value="">Select Status</option>
              <option value="1">Active</option>
              <option value="0">InActive</option>
              </select>
          </div>
          {{-- <div class="form-group">
            <label for="course">Course</label>
            <select name="course" id="segment_course" class="form-control form-control-sm select2" required>
              <option value="">Select Course</option>
            </select>
          </div>
          <div class="form-group">
            <label for="batch">Batch</label>
            <select name="batch" id="segment_batch" class="form-control form-control-sm select2" required>
              <option value="">Select Batch</option>
            </select>
          </div> --}}

          {{-- <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="segment_amount" class="form-control form-control-sm" required>
          </div> --}}

          <div class="form-group">
            <button type="button" class="btn btn-primary segmentsubmit">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<!-- Add Select2 JavaScript (since it's missing from layout) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function feesplan() {
  return {
    structure: [],
    feetype: '',
    amount: 0,
    instalment: 0,
    addRow() {
        if(this.instalment == 0 || this.feetype == '' || this.amount <= 0) {
            alert('Please fill all the fields');
            return;
        }

        for(var i = 1; i <= this.instalment; i++) {
            this.structure.push({
                name: `${this.feetype}-${i}`,
                fee_type: this.feetype,
                amount: (this.amount/this.instalment),
            });
        }
        this.instalment = 0;
        this.amount = 0;
    },
    removerows() {
        this.structure = [];
    }
  };
}
</script>

<script>
$(document).ready(function() {
    // Wait for TomSelect to finish initializing first
    setTimeout(function() {
        
        // Step 1: Destroy any existing TomSelect instances on select2 elements
        $('.select2').each(function() {
            const element = this;
            
            // Check if TomSelect is attached and destroy it
            if (element.tomselect) {
                element.tomselect.destroy();
            }
            
            // Check if Select2 is already attached and destroy it
            if ($(element).hasClass('select2-hidden-accessible')) {
                $(element).select2('destroy');
            }
        });
        
        // Step 2: Initialize Select2 with proper settings
        $('.select2').select2({
            theme: 'default',
            width: '100%',
            placeholder: function() {
                return $(this).find('option:first').text();
            }
        });
        
        // Step 3: Special handling for modal selects
        $('#bill_branch_id, #bill_course, #bill_batch, #bank_account').each(function() {
            const element = this;
            
            // Destroy TomSelect if attached
            if (element.tomselect) {
                element.tomselect.destroy();
            }
            
            // Destroy Select2 if already attached
            if ($(element).hasClass('select2-hidden-accessible')) {
                $(element).select2('destroy');
            }
        });
        
        // Initialize modal selects with dropdownParent
        $('#bill_branch_id, #bill_course, #bill_batch, #bank_account').select2({
            theme: 'default',
            width: '100%',
            dropdownParent: $('#billTypeModal'),
            placeholder: function() {
                return $(this).find('option:first').text();
            }
        });
        
        // Step 4: Your existing dynamic functionality
        let branchjson = @json($branchselect);
        let coursejson = @json($courseselect);
        let batchjson = @json($batchselect);
        let segmentjson = @json($segments);
        let branchselect = $('.branch');
        let courseselect = $('.course');
        let batchselect = $('.batch');
        let segmentselect = $('#segment');
        let billbranch = $('#bill_branch_id');
        let billcourse = $('#bill_course');
        let billbatch = $('#bill_batch');
        let billbtn = $('.billsubmit');
        
        // Function to safely reinitialize Select2
        function reinitializeSelect2(element, isModal = false) {
            // Destroy existing instances
            if (element[0].tomselect) {
                element[0].tomselect.destroy();
            }
            if (element.hasClass('select2-hidden-accessible')) {
                element.select2('destroy');
            }
            
            // Reinitialize Select2
            const config = {
                theme: 'default',
                width: '100%',
                placeholder: element.find('option:first').text()
            };
            
            if (isModal) {
                config.dropdownParent = $('#billTypeModal');
            }
            
            element.select2(config);
        }
        
        // Main form branch change
        branchselect.on('change', function() {
            let branchId = this.value;
            courseselect.empty();
            batchselect.empty();
            segmentselect.empty();
            courseselect.append('<option value="">Select Course</option>');
            segmentselect.append('<option value="">Select Segment</option>');
            
            coursesorted = coursejson.sort(function(a, b) {
                return a.course.localeCompare(b.course);
            });
            
            coursesorted.forEach(function(courseval) {
                if (courseval.campus == branchId) {
                    courseselect.append('<option value="' + courseval.course + '">' + courseval.course + '</option>');
                }
            });
            
            segmentsorted = segmentjson.sort(function(a, b) {
                return a.name.localeCompare(b.name);
            });
            
            segmentsorted.forEach(function(segmentval) {
                if (segmentval.branch_id == branchId) {
                    segmentselect.append('<option value="' + segmentval.id + '">' + segmentval.name + '</option>');
                }
            });
            
            // Reinitialize Select2 after adding options
            reinitializeSelect2(courseselect);
            reinitializeSelect2(batchselect);
            reinitializeSelect2(segmentselect);
        });
        
        // Main form course change
        courseselect.on('change', function() {
            let branchvalue = branchselect.find('option:selected').val();
            let coursevalue = this.value;
            batchselect.empty();
            batchselect.append('<option value="">Select Batch</option>');
            
            batchsorted = batchjson.sort(function(a, b) {
                return a.batch.localeCompare(b.batch);
            });
            
            batchsorted.forEach(function(batchval) {
                if (batchval.campus == branchvalue && batchval.course == coursevalue) {
                    batchselect.append('<option value="' + batchval.batch + '">' + batchval.batch + '</option>');
                }
            });
            
            // Reinitialize Select2 after adding options
            reinitializeSelect2(batchselect);
        });

        // Modal branch change
        billbranch.on('change', function() {
            let branchId = this.value;
            billcourse.empty();
            billbatch.empty();
            billcourse.append('<option value="">Select Course</option>');
            billbatch.append('<option value="">Select Batch</option>');
            
            billcoursesorted = coursejson.sort(function(a, b) {
                return a.course.localeCompare(b.course);
            });
            
            billcoursesorted.forEach(function(courseval) {
                if (courseval.campus == branchId) {
                    billcourse.append('<option value="' + courseval.course + '">' + courseval.course + '</option>');
                }
            });
            
            // Reinitialize Select2 for modal selects
            reinitializeSelect2(billcourse, true);
            reinitializeSelect2(billbatch, true);
        });
        
        // Modal course change
        billcourse.on('change', function() {
            let branchvalue = billbranch.find('option:selected').val();
            let coursevalue = this.value;
            billbatch.empty();
            billbatch.append('<option value="">Select Batch</option>');
            
            billbatchsorted = batchjson.sort(function(a, b) {
                return a.batch.localeCompare(b.batch);
            });
            
            billbatchsorted.forEach(function(batchval) {
                if (batchval.campus == branchvalue && batchval.course == coursevalue) {
                    billbatch.append('<option value="' + batchval.batch + '">' + batchval.batch + '</option>');
                }
            });
            
            // Reinitialize Select2 after adding options
            reinitializeSelect2(billbatch, true);
        });

        // Bill creation
        billbtn.on('click', function() {
            let branchvalue = billbranch.find('option:selected').val();
            let coursevalue = billcourse.find('option:selected').val();
            let batchvalue = billbatch.find('option:selected').val();
            let bank_account = $('#bank_account').val();
            let billname = $('#bill_name').val();
            
            if (branchvalue == '' || coursevalue == '' || batchvalue == '' || billname == '' || bank_account == '') {
                alert('Please fill all the fields');
            } else {
                const csrfToken = "{{ csrf_token() }}";
                $.ajax({
                    url: "{{ route('billtype.store') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    data: {
                        name: billname,
                        branch_id: branchvalue,
                        course: coursevalue,
                        batch: batchvalue,
                        bank_accounts_id: bank_account,
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#bill_type_id option:first').after('<option value="' + response.data.id + '">' + response.data.name + '</option>');
                            
                            // Reinitialize the main form bill_type select
                            reinitializeSelect2($('#bill_type_id'));
                            
                            $('#billTypeModal').modal('hide');
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });
        $('#segmentForm').on('submit', function(e) {
            e.preventDefault();
        });
        $('.segmentsubmit').on('click', function() {
          let segmentname = $('#segment_name').val();
          let segmentbranch = $('#segment_branch_id').find('option:selected').val();
          let segmentstatus = $('#segmentstatus').val();
          

          if (segmentname == '' || segmentbranch == '' || segmentstatus == '') {
            alert('Please fill all the fields');
          } else {
            const csrfToken = "{{ csrf_token() }}";
            $.ajax({
                url: "{{ route('segment.store') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                data: {
                    name: segmentname,
                    branch_id: segmentbranch,
                    is_active: segmentstatus
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#segment option:first').after('<option value="' + response.data.id + '">' + response.data.name + '</option>');
                        reinitializeSelect2($('#segment'));
                        $('#segmentsModal').modal('hide');
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.errors.name[0]);
                }
            });
          }
        });

        // Auto-hide alerts
        setTimeout(function() {
            $(".alert").fadeOut(1500);
        }, 3000);
        
    }, 500); // Wait 500ms for TomSelect to finish initializing
});

// Handle modal events to reinitialize Select2 when modal opens
$('#billTypeModal').on('shown.bs.modal', function() {
    // Reinitialize modal selects when modal is shown
    $('#bill_branch_id, #bill_course, #bill_batch, #bank_account').each(function() {
        const element = $(this);
        
        // Destroy existing instances
        if (this.tomselect) {
            this.tomselect.destroy();
        }
        if (element.hasClass('select2-hidden-accessible')) {
            element.select2('destroy');
        }
        
        // Reinitialize with modal parent
        element.select2({
            theme: 'default',
            width: '100%',
            dropdownParent: $('#billTypeModal'),
            placeholder: element.find('option:first').text()
        });
    });
});
</script>

@endsection
