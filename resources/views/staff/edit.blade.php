@extends('layouts.app')
@section('title', 'Admission')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm"  action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data">
                        
                        @csrf
                        @method('PUT')
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple"> Staff</h6>
                        </div>

                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name"  class="form-control form-control-sm text-capitalize" value="{{$staff->name}}" required>
                       </div>
                      
                  
        
                 <div class="form-group col-lg-3">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control form-control-sm text-capitalize" value="{{$staff->designation}}" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control form-control-sm text-capitalize" value="{{$staff->department}}" required>
                 </div>

                 <!-- Contact Details -->
                 <div class="form-group col-lg-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{$staff->email}}" required>
                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Mobile No</label>
                    <input type="text" name="mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" value="{{$staff->mob_no}}" required>
                    @error('mob_no')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>
                 <div class="form-group col-lg-3">
                    <label>Alternate Mobile No</label>
                    <input type="text" name="alternate_mob_no" class="form-control form-control-sm @error('alternate_mob_no') is-invalid @enderror" value="{{$staff->alternate_mob_no}}" required>
                    @error('alternate_mob_no')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>



                 <div class="form-group col-lg-3">
                    <label>Gender</label>
                    <select name="gender" class="form-control form-control-sm text-capitalize" required>
                        <option value="">Select</option>
                        <option value="Male" {{ $staff->gender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $staff->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $staff->gender == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                

                 <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <input type="text" name="branch" class="form-control form-control-sm text-capitalize" value="{{$staff->branch}}" required>
                 </div>
                 <div class="form-group col-lg-3">
                    <label>Address line 1</label>
                    <input type="text" name="address_line_1" class="form-control form-control-sm text-capitalize" value="{{$staff->address_line_1}}" required>
                 </div>
  
                 <div class="form-group col-lg-3">
                    <label>Address line 2</label>
                    <input type="text" name="address_line_2" class="form-control form-control-sm text-capitalize" value= "{{$staff->address_line_2}}" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>City</label>
                     <select name="city" id="city" class="form-control form-control-sm" required >
                       <option value="">Select City</option>
                       @foreach ($districts as $district)
                       <option value="{{$district->District}}" @if($staff->city == $district->District) selected @endif>{{$district->District}}</option>
                       @endforeach
                     </select>
                </div> 

                <div class="form-group col-lg-3">
                    <label>State</label>
                    <select name="state" class="form-control form-control-sm" required>
                      <option value="">Select State</option>
                      @foreach ($states as $state)
                      <option value="{{$state->State}}" @if($staff->state == $state->State) selected @endif>{{$state->State}}</option>
                      @endforeach
                    </select>
               </div>

                 <div class="form-group col-lg-3">
                    <label>Pincode</label>
                    <input type="number" name="pincode" class="form-control form-control-sm" value ="{{$staff->pincode}}" required>
                 </div>
                 <div class="form-group col-lg-12"><h6> Personal Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                 <!-- Personal Details -->
                 <div class="form-group col-lg-3">
                    <label>Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control form-control-sm" value="{{$staff->date_of_joining}}" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Employment Type</label>
                    <select name="employment_type" class="form-control form-control-sm" required>
                        <option value="">Select</option>
                        <option value="Full-Time" {{ $staff->employment_type == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="Part-Time" {{ $staff->employment_type == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="Contract" {{ $staff->employment_type == 'Contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                

                 <div class="form-group col-lg-3">
                    <label>Qualifications</label>
                    <input type="text" name="qualifications" class="form-control form-control-sm text-capitalize" value="{{$staff->qualifications}}" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Blood Group</label>
                    <select name="blood_group" class="form-control form-control-sm">
                        <option value="">Select Blood Group</option>
                        <option value="A+" {{ $staff->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ $staff->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ $staff->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ $staff->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ $staff->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ $staff->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ $staff->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ $staff->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
                

                <div class="form-group col-lg-3">
                    <label>Marital Status</label>
                    <select name="marital_status" class="form-control form-control-sm">
                        <option value="">Select</option>
                        <option value="Single" {{ $staff->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ $staff->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                    </select>
                </div>
                
                 <div class="form-group col-lg-12"><h6> Banking Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                 <!-- Banking Details -->
                 <div class="form-group col-lg-3">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control form-control-sm" value="{{$staff->account_number}}" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control form-control-sm" value="{{$staff->ifsc_code}}" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control form-control-sm text-capitalize"  value="{{$staff->bank_name}}" required>
                 </div>

                 <!-- Upload Fields -->
                 <div class="form-group col-lg-3">
                    <label>Photo</label>
                    @if($staff->photo)
                    <img src="{{ Storage::url($staff->photo) }}" height="200px" width="200px" alt="Photo" class="img-thumbnail">
                    @else
                    <input type="file" name="photo" class="form-control form-control-sm" value="{{$staff->photo}}">
                    @error('photo')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                    @endif
                 </div>
                
                 <div class="form-group col-lg-3">
                    <label> Certificates</label>
                     @if($staff->experience_certificates)
                     <a href="{{ Storage::url($staff->experience_certificates) }}" target="_blank">View Certificates</a>   
                    @else
                    <input type="file" name="experience_certificates" class="form-control form-control-sm" value="{{$staff->experience_certificates}}">
                    @error('experience_certificates')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                    @endif
                   </div>

                 <div class="form-group col-lg-3">
                    <label>ID Proof</label>
                    @if($staff->id_proof) 
                    <img src="{{ Storage::url($staff->id_proof) }}" height="200px" width="200px" alt="ID Proof" class="img-thumbnail">
                    @else
                    <input type="file" name="id_proof" class="form-control form-control-sm" value="{{$staff->id_proof}}">
                    @error('id_proof')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                    @endif
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