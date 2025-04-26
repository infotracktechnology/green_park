@extends('layouts.app')

@section('title', 'Fees Plan')

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

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Add Fees Plan</h6>
                              </div>

                              <div class="form-group col-lg-6">
                                <label for="academic_year">Fees Plan Name</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                            </div>

                              <div class="form-group col-lg-6">
                                    <label for="academic_year">Student Coaching Type</label>
                                    <select name="coaching_type[]"  class="form-control form-control-sm select2" multiple required>
                                        @foreach ($coaching_type as $row)
                                            <option value="{{ $row->coaching_type }}">{{ $row->coaching_type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                               

                           <div class="form-group col-lg-4">
                           <label>Fee Type ()</label>
                           <select x-model="feetype" x-on:change="getAmount($event.target)"   class="form-control form-control-sm">
                             <option value="">Select Fee Type</option>
                             @foreach ($feetype as $row)
                            <option value="{{$row['feetype']}}" data-amount="{{ $row['amount'] }}">{{$row['feetype']}}</option>
                             @endforeach
                           </select>
                            </div>

                              <div class="form-group col-lg-3">
                                 <label>Amount</label>
                                 <input type="text" x-model.number="amount" class="form-control form-control-sm" readonly>
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
@endsection

@section('js')
<script>
function feesplan() {
  return {
    structure: [],
    feetype: '',
    amount: 0,
    instalment: 0,
    addRow() {
        if(this.instalment == 0) {
            alert('Please enter number of Instalments');
            return;
        }
        for(var i = 1; i <= this.instalment; i++) {
            this.structure.push({
                name: `${this.feetype}-${i}`,
                amount: (this.amount/this.instalment),
            });
        }
        this.instalment = 0;
        this.feetype = '';
        this.amount = 0;
    },
    removeRow(index) {
      this.structure.splice(index, 1);
    },
    getAmount(index) {
        var amount = $(index).find(':selected').data('amount');
        this.amount = amount;
    },
  };
}

// $("#myForm").submit(function(e) {
//     e.preventDefault();
//     if($('.days:checked').length == 0) {
//         alert("Please select at least one day");
//     } else {
//         this.submit();
//     }
// });
</script>
@endsection
