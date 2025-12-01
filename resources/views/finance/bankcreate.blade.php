@extends('layouts.app')
@section('title', 'Banks / Bill Types')
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
                 
        
                <div class="card card-primary">
                    
                  @if(session()->has('success'))
                      <div class="row">
                        <div class="col-12">
                          <div class="alert alert-success bg-success alert-dismissible fade show" role="alert">
                          {{ session('success') }}
                      </div>
                        </div>
                      </div>
                  @endif
                  @if(session()->has('error'))
                      <div class="row">
                        <div class="col-12">
                          <div class="alert alert-error bg-danger alert-dismissible fade show" role="alert">
                          {{ session('error') }}
                      </div>
                        </div>
                      </div>
                  @endif
                  @if ($errors->any())
                      <div class="alert alert-danger">
                          @foreach ($errors->all() as $error)
                              {{ $error }}
                          @endforeach
                      </div>
                  @endif

                    <div class="card-header">
                        <h4>Banks / Bill Types</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="bank-tab" data-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="true">Banks</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="bill-tab" data-toggle="tab" href="#bill" role="tab" aria-controls="bill" aria-selected="false">Bill Types</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="segment-tab" data-toggle="tab" href="#segment" role="tab" aria-controls="segment" aria-selected="false">Segment</a>
                            </li>

                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="concession-tab" data-toggle="tab" href="#concession" role="tab" aria-controls="concession" aria-selected="false">Concession</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                                <div class="clearfix">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#bankModal">Create New Bank</button>

                                </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered" id="bankTable">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Bank Name</th>
                                            <th>Branch</th>
                                            <th>Account Number</th>
                                            <th>IFSC Code</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bank_accounts as $bank)
                                            <tr>
                                                <td>{{ $bank->account_name }}</td>
                                                <td>{{ $bank->bank_name }}</td>
                                                <td>{{ $bank->branch_name }}</td>
                                                <td>{{ $bank->account_no }}</td>
                                                <td>{{ $bank->ifsc_code }}</td>
                                                <td>
                                                    <a href="{{ route('bank.edit', $bank->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            <div class="tab-pane fade" id="bill" role="tabpanel" aria-labelledby="bill-tab">
                                <div class="clearfix">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#billTypeModal">Create New Bill Type</button>

                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-bordered" id="billTable">
                                        <thead>
                                            <tr>
                                                <th>Bill Type</th>
                                                <th>Branch</th>
                                                {{-- <th>Course</th> --}}
                                                {{-- <th>Batch</th> --}}
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bill_types as $bill)
                                                <tr>
                                                    <td>{{ $bill->name }}</td>
                                                    <td>{{ $bill->branch->name }}</td>
                                                    {{-- <td>{{ $bill->course }}</td> --}}
                                                    {{-- <td>{{ $bill->batch }}</td> --}}
                                                    <td>
                                                        <a href="{{ route('billtype.edit', $bill->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
                            <div class="tab-pane fade" id="segment" role="tabpanel" aria-labelledby="segment-tab">
                                <div class="clearfix">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#segmentsModal">Create Segment</button>

                                </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered" id="segmentTable">
                                    <thead>
                                        <tr>
                                            <th>Segment Name</th>
                                            <th>Branch</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($segments as $segment)
                                            <tr>
                                                <td>{{ $segment->name }}</td>
                                                <td>{{ optional($segment->branch)->name }}</td>
                                                <td>
                                                    @if ($segment->is_active)
                                                        <span class="badge rounded-pill bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger text-white">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('segment.edit', $segment->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            <div class="tab-pane fade" id="concession" role="tabpanel" aria-labelledby="concession-tab">
                                  <div class="clearfix">
                                  <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#concessionModal">Create Concession</button>

                                  </div>
                                  <div class="table-responsive mt-3">
                                      <table class="table table-striped table-bordered" id="billTable">
                                          <thead>
                                              <tr>
                                                  <th>Concession Name</th>
                                                  <th>Type</th>
                                                  <th>Value</th>
                                                  <th>Status</th>
                                                  <th>Action</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @foreach ($concessions as $concession)
                                                  <tr>
                                                      <td>{{ $concession->name }}</td>
                                                      <td style="text-transform: capitalize">{{ $concession->type }}</td>
                                                      <td>{{ $concession->value }}</td>
                                                      <td>
                                                          @if ($concession->is_active)
                                                              <span class="badge rounded-pill bg-success text-white">Active</span>
                                                          @else
                                                              <span class="badge rounded-pill bg-danger text-white">Inactive</span>
                                                          @endif
                                                      </td>
                                                      
                                                      <td>
                                                          <a href="{{ route('concession.edit', $concession->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
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
    </div>
</section>
</div>

<!-- Modal for creating a bank account -->
<div class="modal fade" id="bankModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Bank Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('bank.store') }}" method="POST">
        @csrf
      <div class="modal-body">
        
        <div class="row">
          <div class="col-md-6 mb-2">
            <label class="form-label">Bank Name</label>
            <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="Bank Name" required>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">Branch</label>
            <input type="text" name="branch_name" class="form-control form-control-sm" placeholder="Branch" required>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">IFSC code</label>
            <input type="text" name="ifsc_code" class="form-control form-control-sm" placeholder="IFSC" required>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">MICR code</label>
            <input type="text" name="micr_code" class="form-control form-control-sm" placeholder="MICR">
          </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6 mb-2">
            <label class="form-label">Account Name</label>
            <input type="text" name="account_name" class="form-control form-control-sm" placeholder="Account Name" required>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">Account Number</label>
            <input type="text" name="account_no" class="form-control form-control-sm" placeholder="Account Number" required>
          </div>
          {{-- <div class="col-md-6 mb-2">
            <label class="form-label">Account Type</label>
            <select name="account_type" class="form-control form-control-sm">
              <option value="">Select Account Type</option>
              <option value="Savings">Savings</option>
              <option value="Current">Current</option>
            </select>
          </div> --}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
        </form>

    </div>
  </div>
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
        <form action="{{ route('billtype.store') }}" method="POST">
        @csrf
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
          {{-- <div class="form-group">
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
          </div> --}}

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
            <button type="submit" class="btn btn-primary billsubmit">Create</button>
          </div>
        </form>
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
            <button type="submit" class="btn btn-primary segmentsubmit">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal for creating a Concession -->
<div class="modal fade" id="concessionModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Concession</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('concession.store') }}" method="POST" id="concessionForm">
          @csrf
          <div class="form-group">
            <label for="concession_name">Concession Name</label>
            <input type="text" name="name" id="concession_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type"  class="form-control form-control-sm select2" required>
                <option value="">Select Type</option>
                  @foreach ($typeselect as $type)
                      <option value="{{ $type }}">{{ $type }}</option>
                  @endforeach
              </select>
          </div>

          <div class="form-group">
            <label for="value">Value</label>
            <input type="number" name="value" id="value" class="form-control form-control-sm" required>
          </div>

          <div class="form-group">
            <label for="is_active">Active</label>
            <select name="is_active" id="concessionstatus"  class="form-control form-control-sm select2" required>
              <option value="">Select Status</option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary concessionsubmit">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#bankTable, #billTable,#segmentTable').DataTable(
            {
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            }
        );

    // let billbranch = $('#bill_branch_id');
    // let billcourse = $('#bill_course');
    // let billbatch = $('#bill_batch');
    // let branchjson = @json($branchselect);
    // let coursejson = @json($courseselect);
    // let batchjson = @json($batchselect);

    // billbranch.on('change', function() {
    //   let branchId = this.value;
    //   billcourse.empty();
    //   billbatch.empty();
    //   billcourse.append('<option value="">Select Course</option>');
    //   billbatch.append('<option value="">Select Batch</option>');
    //   billcoursesorted = coursejson.sort(function(a, b) {
    //     return a.course.localeCompare(b.course);
    //   });
    //   billcoursesorted.forEach(function(courseval) {
    //     if (courseval.campus == branchId) {
    //       billcourse.append('<option value="' + courseval.course + '">' + courseval.course + '</option>');
    //     }
    //   });
    // })
    // billcourse.on('change', function() {
    //   let branchvalue = billbranch.find('option:selected').val();
    //   let coursevalue = this.value;
    //   billbatch.empty();
    //   billbatch.append('<option value="">Select Batch</option>');
    //   billbatchsorted = batchjson.sort(function(a, b) {
    //     return a.batch.localeCompare(b.batch);
    //   })
    //   billbatchsorted.forEach(function(batchval) {
    //     if (batchval.campus == branchvalue && batchval.course == coursevalue) {
    //       billbatch.append('<option value="' + batchval.batch + '">' + batchval.batch + '</option>');
    //     }
    //   });
    // });


    let valinput = $('#value');
    let typeinput = $('#type');

    typeinput.on('change', function() {
      valinput.val('');
    });
    
    valinput.on('input', function() {
      let value = this.value;
      if(typeinput.val() == 'percentage') {
        if (value > 100) {
          alert('Value cannot be greater than 100%');
          this.value = 100;
        }
      } 
    });
    setTimeout(function() {
            $(".alert").fadeOut(1500);
        }, 3000);
    });
</script>
@endsection