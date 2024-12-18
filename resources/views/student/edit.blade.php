@extends('layouts.app')
@section('title', 'Student Edit')
@section('css')
<style>[x-cloak] { display: none !important; }</style>
@endsection
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
         <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    
                    <form id="wizard_with_validation" method="POST" novalidate="novalidate" action="{{ route('student.update', $student->id) }}" class="my-4" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <h3>Pesronal Details</h3>
                        <fieldset class="row">
                            <div class="form-group col-lg-3">
                                <label>Admission Date</label>
                                 <input type="date" name="admission_date" value="{{$student->admission_date}}" class="form-control form-control-sm">
                            
                             </div>
        
                             <div class="form-group col-lg-3">
                                <label for="branch_id">Campus</label>
                                <select name="campus" class="form-control form-control-sm" >
                                    <option value="" disabled selected>Select Campus</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @if($branch->id == $student->campus) selected @endif>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
        
        
                           <div class="form-group col-lg-3">
                               <label>Coaching Type</label>
                                <select name="coaching_type" class="form-control form-control-sm">
                                    <option value="">Select Coaching Type</option>
                                    <option value="Online" @if($student->coaching_type == 'Online') selected @endif>Online</option>
                                    <option value="Offline" @if($student->coaching_type == 'Offline') selected @endif>Offline</option>
                                </select>
                            </div>
        
                            <div class="form-group col-lg-3">
                               <label>Hostel/Day Scholar</label>
                                <select name="hostel_dayscholar" class="form-control form-control-sm">
                                    <option value="">Select Option</option>
                                    <option value="Hostel" @if($student->hostel_dayscholar == 'Hostel') selected @endif>Hostel</option>
                                    <option value="Day Scholar" @if($student->hostel_dayscholar == 'Day Scholar') selected @endif>Day Scholar</option>
                                </select>
                            </div>
        
                            <div class="form-group col-lg-3">
                               <label>Student Name</label>
                                <input type="text" name="student_name" value="{{$student->student_name}}" class="form-control form-control-sm">
                           </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mobile No 1</label>
                                 <input type="text" name="ph_no1" value="{{$student->ph_no1}}" class="form-control form-control-sm">
                                
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mobile No 2</label>
                                 <input type="text" name="ph_no2" value="{{$student->ph_no2}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Gender</label>
                                <select name="gender" class="form-control form-control-sm">
                                    <option value="">Select Gender</option>
                                    <option value="Male" @if($student->gender == 'Male') selected @endif>Male</option>
                                    <option value="Female" @if($student->gender == 'Female') selected @endif>Female</option>
                                    <option value="Other" @if($student->gender == 'Other') selected @endif>Other</option>
                                </select>
                            </div>
        
        
        
                            <div class="form-group col-lg-3">
                                <label>Date of Birth</label>
                                 <input type="date" name="dob" value="{{$student->dob}}" class="form-control form-control-sm">
                            </div>
        
        
                            <div class="form-group col-lg-3">
                                <label>Father Name</label>
                                 <input type="text" name="father_name" value="{{$student->father_name}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Father Mobile No</label>
                                 <input type="text" name="father_ph_no" value="{{$student->father_ph_no}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mother Name</label>
                                 <input type="text" name="mother_name" value="{{$student->mother_name}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mother Mobile No</label>
                                 <input type="text" name="mother_ph_no" value="{{$student->mother_ph_no}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Admission Opted For</label>
                                 <select name="admission_opted_for" class="form-control form-control-sm">
                                     <option value="">Select Coaching Type</option>
                                     <option value="Online" @if($student->admission_opted_for == 'Online') selected @endif>Online</option>
                                     <option value="Offline" @if($student->admission_opted_for == 'Offline') selected @endif>Offline</option>
                                 </select>
                             </div>
        
                            <div class="form-group col-lg-3">
                                <label>AC/Non AC</label>
                                 <select name="ac_nonac" class="form-control form-control-sm">
                                     <option value="">Select AC/Non AC</option>
                                     <option value="AC" @if($student->ac_nonac == 'AC') selected @endif>AC</option>
                                     <option value="Non AC" @if($student->ac_nonac == 'Non AC') selected @endif>Non AC</option>
                                 </select>
                             </div>
        
        
                             <div class="form-group col-lg-3">
                                <label>Blood Group</label>
                                 <select name="blood_group" class="form-control form-control-sm">
                                     <option value="">Select Blood Group</option>
                                     <option value="A+" @if($student->blood_group == 'A+') selected @endif>A+</option>
                                     <option value="A-" @if($student->blood_group == 'A-') selected @endif>A-</option>
                                     <option value="B+" @if($student->blood_group == 'B+') selected @endif>B+</option>
                                     <option value="B-" @if($student->blood_group == 'B-') selected @endif>B-</option>
                                     <option value="AB+" @if($student->blood_group == 'AB+') selected @endif>AB+</option>
                                     <option value="AB-" @if($student->blood_group == 'AB-') selected @endif>AB-</option>
                                     <option value="O+" @if($student->blood_group == 'O+') selected @endif>O+</option>
                                     <option value="O-" @if($student->blood_group == 'O-') selected @endif>O-</option>
                                 </select>
                             </div>
                         
         
                             <div class="form-group col-lg-3">
                                <label>Age</label>
                                 <input type="number" name="age" value="{{$student->age}}" class="form-control form-control-sm" required>
                            </div>
        
        
        
                            <div class="form-group col-lg-3">
                                <label>Aadhar Card No</label>
                                 <input type="number" name="aadhar_card_no" value="{{$student->aadhar_card_no}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Nationality</label>
                                 <input type="text" name="nationality" value="{{$student->nationality}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Religion</label>
                                 <input type="text" name="religion" value="{{$student->religion}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Community</label>
                                 <input type="text" name="community" value="{{$student->community}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Caste</label>
                                 <input type="text" name="caste" value="{{$student->caste}}" class="form-control form-control-sm">
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Student WhatsApp No</label>
                                 <input type="number" name="student_whatsapp_no" value="{{$student->student_whatsapp_no}}" class="form-control form-control-sm">
                            </div>        
                        </fieldset>
                        <h3>Profile Information</h3>
                        <fieldset>
                          <div class="form-group form-float">
                            <div class="form-line">
                              <label class="form-label">First Name*</label>
                              <input type="text" name="name" class="form-control" required>
                            </div>
                          </div>
                          <div class="form-group form-float">
                            <div class="form-line">
                              <label class="form-label">Last Name*</label>
                              <input type="text" name="surname" class="form-control" required>
                            </div>
                          </div>
                          <div class="form-group form-float">
                            <div class="form-line">
                              <label class="form-label">Email*</label>
                              <input type="email" name="email" class="form-control" required>
                            </div>
                          </div>
                          <div class="form-group form-float">
                            <div class="form-line">
                              <label class="form-label">Address*</label>
                              <textarea name="address" cols="30" rows="3" class="form-control no-resize"
                                required></textarea>
                            </div>
                          </div>
                          <div class="form-group form-float">
                            <div class="form-line">
                              <label class="form-label">Age*</label>
                              <input min="18" type="number" name="age" class="form-control" required>
                            </div>
                            <div class="help-info">The warning step will show up if age is less than 18</div>
                          </div>
                        </fieldset>
                        <h3>Terms &amp; Conditions - Finish</h3>
                        <fieldset>
                          <input id="acceptTerms-2" name="acceptTerms" type="checkbox" required>
                          <label for="acceptTerms-2">I agree with the Terms and Conditions.</label>
                        </fieldset>


                        <h3>Mark Information</h3>
                        <fieldset>
                        </fieldset>

                      </form>
                </div>
            </div>
         </div>        
      </div>     
   </section>
</div>

@endsection

@section('js')

<script src="{{asset('bundles/jquery-validation/dist/jquery.validate.min.js')}}"></script>
<script src="{{asset('bundles/jquery-steps/jquery.steps.min.js')}}"></script>
<script src="{{ asset('js/page/form-wizard.js') }}"></script>

{{-- <script>
     document.addEventListener('alpine:init', () => {
        Alpine.data('app', () => ({
            formStep: 1,
            errors: {},
            updateFormStep() {
            },
            init() {
                
            }
        }));
    });
</script> --}}

@endsection

