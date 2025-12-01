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
                    <form action="{{ route('billtype.update', $bill_type->id) }}" id="myForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="name">Bill Type</label>
                                <input type="text" name="name" id="bill_name" class="form-control" value="{{ $bill_type->name }}" required>
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bill_branch_id">Branch</label>
                                    <select name="branch_id" id="bill_branch_id"  class="form-control form-control-sm select2" required>
                                        <option value="">Select Branch</option>
                                        @foreach ($branchselect as $id => $branch)
                                            <option value="{{ $id }}" @selected($bill_type->branch_id == $id)>{{ $branch }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course">Course</label>
                                    <select name="course" id="bill_course" class="form-control form-control-sm select2" required>
                                        <option value="">Select Course</option>
                                        @foreach ($courseselect->where('campus', $bill_type->branch_id) as $course)
                                            <option value="{{ $course->course }}" @selected($bill_type->course == $course->course)>{{ $course->course }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="batch">Batch</label>
                                    <select name="batch" id="bill_batch" class="form-control form-control-sm select2" required>
                                        <option value="">Select Batch</option>
                                        @foreach ($batchselect->where('campus', $bill_type->branch_id)->where('course', $bill_type->course) as $batch)
                                            <option value="{{ $batch->batch }}" @selected($bill_type->batch == $batch->batch)>{{ $batch->batch }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_account">Bank</label>
                                    <select name="bank_accounts_id" id="bank_account"  class="form-control form-control-sm select2" required>
                                        <option value="">Select Bank Account</option>
                                        @foreach ($bank_accounts as $bank_account)
                                            <option value="{{ $bank_account->id }}" @selected($bill_type->bank_accounts_id == $bank_account->id)>{{ $bank_account->account_no . ' - ' . $bank_account->bank_name . ' - ' . $bank_account->branch_name  }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary billsubmit">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {

    let billbranch = $('#bill_branch_id');
    let billcourse = $('#bill_course');
    let billbatch = $('#bill_batch');
    let branchjson = @json($branchselect);
    let coursejson = @json($courseselect);
    let batchjson = @json($batchselect);

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
    })
    billcourse.on('change', function() {
      let branchvalue = billbranch.find('option:selected').val();
      let coursevalue = this.value;
      billbatch.empty();
      billbatch.append('<option value="">Select Batch</option>');
      billbatchsorted = batchjson.sort(function(a, b) {
        return a.batch.localeCompare(b.batch);
      })
      billbatchsorted.forEach(function(batchval) {
        if (batchval.campus == branchvalue && batchval.course == coursevalue) {
          billbatch.append('<option value="' + batchval.batch + '">' + batchval.batch + '</option>');
        }
      });
    });
    setTimeout(function() {
            $(".alert").fadeOut(1500);
        }, 3000);
    });
</script>
@endsection
