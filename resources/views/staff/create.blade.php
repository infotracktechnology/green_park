@extends('layouts.app')
@section('title', 'Admission')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm"  action="{{ route('staff.store') }}" enctype="multipart/form-data">
                        @csrf
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple"> Staff</h6>
                        </div>

                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name"  class="form-control form-control-sm" required>
                       </div>
                      
                  
        
                 <div class="form-group col-lg-3">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control form-control-sm" required>
                 </div>

                 <!-- Contact Details -->
                 <div class="form-group col-lg-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" required>
                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Mobile No</label>
                    <input type="text" name="mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" required>
                    @error('mob_no')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <input type="text" name="branch" class="form-control form-control-sm" required>
                 </div>

                 <!-- Address -->
                 <div class="form-group col-lg-3">
                    <label>City</label>
                    <input type="text" name="city" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>State</label>
                    <input type="text" name="state" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Pincode</label>
                    <input type="number" name="pincode" class="form-control form-control-sm" required>
                 </div>

                 <!-- Personal Details -->
                 <div class="form-group col-lg-3">
                    <label>Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Employment Type</label>
                    <select name="employment_type" class="form-control form-control-sm" required>
                       <option value="Full-Time">Full-Time</option>
                       <option value="Part-Time">Part-Time</option>
                       <option value="Contract">Contract</option>
                    </select>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Qualifications</label>
                    <input type="text" name="qualifications" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Blood Group</label>
                    <input type="text" name="blood_group" class="form-control form-control-sm">
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Marital Status</label>
                    <select name="marital_status" class="form-control form-control-sm">
                       <option value="Single">Single</option>
                       <option value="Married">Married</option>
                    </select>
                 </div>

                 <!-- Banking Details -->
                 <div class="form-group col-lg-3">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control form-control-sm" required>
                 </div>

                 <!-- Upload Fields -->
                 <div class="form-group col-lg-3">
                    <label>Photo</label>
                    <input type="file" name="photo" class="form-control form-control-sm">
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Experience Certificates</label>
                    <input type="file" name="experience_certificates[]" class="form-control form-control-sm" multiple>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>ID Proof</label>
                    <input type="file" name="id_proof" class="form-control form-control-sm">
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


@endsection