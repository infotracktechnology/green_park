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
                        <h3>Personal Details</h3>
                        <fieldset class="row">
                            <div class="form-group col-lg-3">
                                <label>Admission Date</label>
                                 <input type="date" name="admission_date" value="{{$student->admission_date}}" class="form-control form-control-sm" required>
                            
                             </div>
        
                             <div class="form-group col-lg-3">
                                <label for="branch_id">Campus</label>
                                <select name="campus" class="form-control form-control-sm" required >
                                    <option value="" disabled selected>Select Campus</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @if($branch->id == $student->campus) selected @endif>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
        
        
                           <div class="form-group col-lg-3">
                               <label>Coaching Type</label>
                                <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" onchange="hostel(this.value)" required >
                                    <option value="">Select Coaching Type</option>
                                    <option value="Offline" @if($student->coaching_type == 'Offline') selected @endif>Offline</option>
                                    <option value="Online Recorded" @if($student->coaching_type == 'Online Recorded') selected @endif>Online Recorded</option>
                                    <option value="Online Live" @if($student->coaching_type == 'Online Live') selected @endif>Online Live</option>
                             
                                </select>
                            </div>
        
                            <div class="form-group col-lg-3">
                               <label>Hostel/Day Scholar</label>
                                <select name="hostel_dayscholar" id="hostel_dayscholar" class="form-control form-control-sm"  >
                                    <option value="">Select Option</option>
                                    <option value="Hostel" @if($student->hostel_dayscholar == 'Hostel') selected @endif>Hostel</option>
                                    <option value="Day Scholar" @if($student->hostel_dayscholar == 'Day Scholar') selected @endif>Day Scholar</option>
                                </select>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label for="student_name">Student Name</label>
                                <input type="text" name="student_name" id="student_name" value="{{ old('student_name', $student->student_name) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only alphabets and spaces are allowed.
                                </div>
                            </div>
                            
        
                            <div class="form-group col-lg-3">
                                <label for="ph_no1">Mobile No 1</label>
                                <input type="number" name="ph_no1" id="ph_no1" value="{{ old('ph_no1', $student->ph_no1) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only numbers are allowed.
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label for="ph_no2">Mobile No 2</label>
                                <input type="number" name="ph_no2" id="ph_no2" value="{{ old('ph_no2', $student->ph_no2) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only numbers are allowed.
                                </div>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Gender</label>
                                <select name="gender" class="form-control form-control-sm" required >
                                    <option value="">Select Gender</option>
                                    <option value="Male" @if($student->gender == 'Male') selected @endif>Male</option>
                                    <option value="Female" @if($student->gender == 'Female') selected @endif>Female</option>
                                    <option value="Other" @if($student->gender == 'Other') selected @endif>Other</option>
                                </select>
                            </div>
        
        
        
                            <div class="form-group col-lg-3">
                                <label>Date of Birth</label>
                                <input 
                                    type="date" 
                                    name="dob" 
                                    value="{{ $student->dob }}" 
                                    class="form-control form-control-sm" 
                                    required 
                                    id="dobInput"
                                    onchange="calculateAge()">
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Age</label>
                                <input 
                                    type="number" 
                                    name="age" 
                                    value="{{ \Carbon\Carbon::parse($student->dob)->age }}" 
                                    class="form-control form-control-sm" 
                                    required 
                                    id="ageInput" 
                                    readonly>
                            </div>

        
                            <div class="form-group col-lg-3">
                                <label for="father_name">Father Name</label>
                                <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $student->father_name) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only alphabets and spaces are allowed.
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label for="father_ph_no">Father Mobile No</label>
                                <input type="number" name="father_ph_no" id="father_ph_no" value="{{ old('father_ph_no', $student->father_ph_no) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only numbers are allowed.
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label for="mother_name">Mother Name</label>
                                <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $student->mother_name) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only alphabets and spaces are allowed.
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label for="mother_ph_no">Mother Mobile No</label>
                                <input type="number" name="mother_ph_no" id="mother_ph_no" value="{{ old('mother_ph_no', $student->mother_ph_no) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only numbers are allowed.
                                </div>
                            </div>
                            

                
                            {{-- <div class="form-group col-lg-3">
                                <label>Admission Opted For</label>
                                 <select name="admission_opted_for" class="form-control form-control-sm" required >
                                     <option value="">Select Coaching Type</option>
                                     <option value="Online" @if($student->admission_opted_for == 'Online') selected @endif>Online</option>
                                     <option value="Offline" @if($student->admission_opted_for == 'Offline') selected @endif>Offline</option>
                                 </select>
                             </div>
         --}}
                            <div class="form-group col-lg-3" >
                                <label>AC/Non AC</label>
                                 <select name="ac_nonac" class="form-control form-control-sm" required >
                                     <option value="">Select AC/Non AC</option>
                                     <option value="AC" @if($student->ac_nonac == 'AC') selected @endif>AC</option>
                                     <option value="Non AC" @if($student->ac_nonac == 'Non AC') selected @endif>Non AC</option>
                                 </select>
                             </div>
        
        
                             <div class="form-group col-lg-3">
                                <label>Blood Group</label>
                                 <select name="blood_group" class="form-control form-control-sm" required>
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



                         
{{--          
                             <div class="form-group col-lg-3">
                                <label>Age</label>
                                 <input type="number" name="age" value="{{$student->age}}" class="form-control form-control-sm" required>
                            </div> --}}
        
        
                          
<div class="form-group col-lg-3">
    <label for="aadhar_card_no">Aadhar Card No</label>
    <input type="number" name="aadhar_card_no" id="aadhar_card_no" value="{{ old('aadhar_card_no', $student->aadhar_card_no) }}" class="form-control form-control-sm" required pattern="^[0-9]{12}$">
    <div class="invalid-feedback">
        Aadhar card number should be exactly 12 digits.
    </div>
</div>

                             {{-- @error('aadhar_card_no')
                             <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                             @enderror --}}

        
                          
                                <div class="form-group col-lg-3">
                                <label>Nationality</label>
                                <select name="nationality" class="form-control form-control-sm" required>
                                    <option value="">Select Nationality</option>
                                    <option value="Indian" @if($student->nationality == 'Indian') selected @endif>Indian</option>
                                    <option value="Foreign" @if($student->nationality == 'Foreign') selected @endif>Foreign</option>
                                    <option value="NRI" @if($student->nationality == 'NRI') selected @endif>NRI</option>
                                </select>
                            </div>
                               
        
                     
                            <div class="form-group col-lg-3">
                                <label>Religion</label>
                                <select name="religion" class="form-control form-control-sm" required>
                                    <option value="">Select Religion</option>
                                    <option value="Hindu" @if($student->religion == 'Hindu') selected @endif>Hindu</option>
                                    <option value="Christian" @if($student->religion == 'Christian') selected @endif>Christian</option>
                                    <option value="Muslim" @if($student->religion == 'Muslim') selected @endif>Muslim</option>
                                </select>
                            </div>
        
                
                            <div class="form-group col-lg-3">
                                <label>Community</label>
                                <select name="community" class="form-control form-control-sm" required>
                                    <option value="">Select Community</option>
                                    <option value="OC" @if($student->community == 'OC') selected @endif>OC</option>
                                    <option value="BC" @if($student->community == 'BC') selected @endif>BC</option>
                                    <option value="BCM" @if($student->community == 'BCM') selected @endif>BCM</option>
                                    <option value="MBC / DNC" @if($student->community == 'MBC / DNC') selected @endif>MBC / DNC</option>
                                    <option value="SC" @if($student->community == 'SC') selected @endif>SC</option>
                                    <option value="SCA" @if($student->community == 'SCA') selected @endif>SCA</option>
                                    <option value="ST" @if($student->community == 'ST') selected @endif>ST</option>
                                </select>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Caste</label>
                                 <input type="text" name="caste" value="{{$student->caste}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label for="student_whatsapp_no">Student WhatsApp No</label>
                                <input type="number" name="student_whatsapp_no" id="student_whatsapp_no" value="{{ old('student_whatsapp_no', $student->student_whatsapp_no) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only numbers are allowed.
                                </div>
                            </div>     
                        </fieldset>

                        <h3> Address</h3>
                        <fieldset class="row">
                            <div class="form-group col-lg-3">
                                <label>Door No</label>
                                 <input type="text" name="door_no" value="{{$student->door_no}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Street Name</label>
                                 <input type="text" name="street_name" value="{{$student->street_name}}" class="form-control form-control-sm" required>
                            </div>


                            <div class="form-group col-lg-3">
                                <label>State</label>
                               <select name="state" id="state" onchange="City(this.value);" class="form-control form-control-sm" required>
                             <option value="">Select State</option>
                             @foreach ($states as $state)
                             <option value="{{$state->State}}" @if($student->state == $state->State) selected @endif>{{$state->State}}</option>
                             @endforeach
                           </select>
                             </div>

                             <div class="form-group col-lg-3">
                                <label>City</label>
                                <select name="city" id="city" onchange="Pincode(this.value);" class="form-control form-control-sm" required>
                                 <option value="">Select City</option>
                                 @foreach ($districts as $city)
                                 <option value="{{$city->District}}" @if($student->city == $city->District) selected @endif>{{$city->District}}</option>
                                 @endforeach
                               </select>
                             </div>

                    <datalist id="pincode_list">
                        @foreach ($pincodes as $pin)
                        <option value="{{$pin->Pincode}}">{{$pin->Pincode}}</option>
                        @endforeach
                    </datalist>
        
                            <div class="form-group col-lg-3">
                                <label>Pincode</label>
                                 <input type="text" id="pincode" list="pincode_list" name="pincode" value="{{$student->pincode}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label for="parent_whatsapp_no">Parent WhatsApp No</label>
                                <input type="number" name="parent_whatsapp_no" id="parent_whatsapp_no" value="{{ old('parent_whatsapp_no', $student->parent_whatsapp_no) }}" class="form-control form-control-sm" required>
                                <div class="invalid-feedback">
                                    Only numbers are allowed.
                                </div>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Email ID</label>
                                 <input type="email" name="email" value="{{$student->email}}" class="form-control form-control-sm" required>
                            </div>
                        </fieldset>
                        <h3>Parent Details</h3>
                        <fieldset class="row">
                            <div class="form-group col-lg-3">
                                <label>Father Qualification</label>
                                <input type="text" name="father_qualification" value="{{$student->father_qualification}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Father Occupation</label>
                                <input type="text" name="father_occupation" value="{{$student->father_occupation}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Father Annual Income</label>
                                <input type="number" name="father_annual_income" value="{{$student->father_annual_income}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Father Designation</label>
                                <input type="text" name="father_designation" value="{{$student->father_designation}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Father's Place of Work</label>
                                <input type="text" name="fathers_place_of_work" value="{{$student->fathers_place_of_work}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Mother Qualification</label>
                                <input type="text" name="mother_qualification" value="{{$student->mother_qualification}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Mother Occupation</label>
                                <input type="text" name="mother_occupation" value="{{$student->mother_occupation}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Mother Annual Income</label>
                                <input type="number" name="mother_annual_income" value="{{$student->mother_annual_income}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Mother Designation</label>
                                <input type="text" name="mother_designation" value="{{$student->mother_designation}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Mother's Place of Work</label>
                                <input type="text" name="mother_place_of_work" value="{{$student->mother_place_of_work}}" class="form-control form-control-sm" required>
                            </div>
                            
                            
                        </fieldset>


                        <h3>Academic Details</h3>
                        <fieldset class="row">
                        
                                <div class="form-group col-lg-3">
                                    <label>Board of Study (X std)</label>
                                    <select name="board_of_study_X_std" class="form-control form-control-sm" required>
                                        <option value="">Select Board</option>
                                        <option value="STATEBOARD" {{ $student->board_of_study_X_std == 'STATEBOARD' ? 'selected' : '' }}>STATEBOARD</option>
                                        <option value="CBSE" {{ $student->board_of_study_X_std == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                                        <option value="ICSE" {{ $student->board_of_study_X_std == 'ICSE' ? 'selected' : '' }}>ICSE</option>
                                        <option value="IGCSE" {{ $student->board_of_study_X_std == 'IGCSE' ? 'selected' : '' }}>IGCSE</option>
                                    </select>
                                </div>
                           
                            
    
                            <div class="form-group col-lg-3">
                                <label>Name of School (X std)</label>
                                <input type="text" name="school_name_X_std" value="{{$student->school_name_X_std}}" class="form-control form-control-sm" required>
                            </div>
    
                            <div class="form-group col-lg-3">
                                <label>District Name of the School (X std)</label>
                                <input type="text" name="district_name_school_X_std" value="{{$student->district_name_school_X_std}}" class="form-control form-control-sm" required>
                            </div>
    
                            <div class="form-group col-lg-3">
                                <label>Total Marks Obtained in X std</label>
                                <input type="number" name="total_marks_X_std" value="{{$student->total_marks_X_std}}" class="form-control form-control-sm" required>
                            </div>
    
                            <div class="form-group col-lg-3">
                                <label>Board of Study (XII std)</label>
                                <select name="board_of_study_XII_std" class="form-control form-control-sm" required>
                                    <option value="">Select Board</option>
                                    <option value="STATEBOARD" {{ $student->board_of_study_XII_std == 'STATEBOARD' ? 'selected' : '' }}>STATEBOARD</option>
                                    <option value="CBSE" {{ $student->board_of_study_XII_std == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                                    <option value="ICSE" {{ $student->board_of_study_XII_std == 'ICSE' ? 'selected' : '' }}>ICSE</option>
                                    <option value="IGCSE" {{ $student->board_of_study_XII_std == 'IGCSE' ? 'selected' : '' }}>IGCSE</option>
                                </select>
                            </div>
                            
    
                            <div class="form-group col-lg-3">
                                <label>Name of School (XII std)</label>
                                <input type="text" name="school_name_XII_std" value="{{$student->school_name_XII_std}}" class="form-control form-control-sm" required>
                            </div>
    
                            <div class="form-group col-lg-3">
                                <label>District Name of the School (XII std)</label>
                                <input type="text" name="district_name_school_XII_std" value="{{$student->district_name_school_XII_std}}" class="form-control form-control-sm" required>
                            </div>
    
                            <div class="form-group col-lg-3">
                                <label>Total Marks Obtained in XII std</label>
                                <input type="number" name="total_marks_XII_std" value="{{$student->total_marks_XII_std}}" class="form-control form-control-sm" required>
                            </div>

                        </fieldset>


                        <h3>Mark Details</h3>
                        <fieldset class="row">
                            
                            {{-- <div class="form-group col-lg-4">
                                <label>Subject 1</label>
                                <select name="S1" class="form-control form-control-sm" required>
                                    <option value="">Select Subject</option>
                                    <option value="ENGLISH" {{ $student->S1 == 'ENGLISH' ? 'selected' : '' }}>English</option>
                                    <option value="PHYSICS" {{ $student->S1 == 'PHYSICS' ? 'selected' : '' }}>Physics</option>
                                    <option value="CHEMISTRY" {{ $student->S1 == 'CHEMISTRY' ? 'selected' : '' }}>Chemistry</option>
                                    <option value="BIOLOGY" {{ $student->S1 == 'BIOLOGY' ? 'selected' : '' }}>Biology</option>
                                </select>
                            
                            </div> --}}


                            <div class="form-group col-lg-4">
                                <label>Subject 1</label>
                                <input type="text" name="S1" 
                                       value="{{ $student->S1 ?? 'English' }}" 
                                       class="form-control form-control-sm" 
                                       required readonly>
                            </div>
                            
                            
                            

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S1</label>
                            <input type="number" name="S1_max_marks" value="{{$student->S1_max_marks}}"  max="100" class="form-control form-control-sm" required>
                        </div>

                        {{-- <div class="form-group col-lg-4">
                            <label>Maximum Marks of S1</label>
                            <input type="number" name="S1_max_marks" value="{{$student->S1_max_marks}}" class="form-control form-control-sm" required>
                        </div> --}}

      <div class="form-group col-lg-4">
    <label>Marks Obtained in S1</label>
    <input type="number" id="S1_obtained_mark" name="S1_obtained_mark" value="{{$student->S1_obtained_mark}}" class="form-control form-control-sm" required oninput="calculateTotal()">
</div>

                        {{-- <div class="form-group col-lg-4">
                            <label>Subject 2</label>
                            <select name="S2" class="form-control form-control-sm" required>
                                <option value="">Select Subject</option>
                                <option value="ENGLISH" {{ $student->S2 == 'ENGLISH' ? 'selected' : '' }}>English</option>
                                <option value="PHYSICS" {{ $student->S2 == 'PHYSICS' ? 'selected' : '' }}>Physics</option>
                                <option value="CHEMISTRY" {{ $student->S2 == 'CHEMISTRY' ? 'selected' : '' }}>Chemistry</option>
                                <option value="BIOLOGY" {{ $student->S2 == 'BIOLOGY' ? 'selected' : '' }}>Biology</option>
                            </select>
                        </div> --}}



                        <div class="form-group col-lg-4">
                            <label>Subject 2</label>
                            <input type="text" name="S2" value="{{ $student->S2 ?? 'Physics' }}" class="form-control form-control-sm" required readonly>
                            </div>

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S2</label>
                            <input type="number" name="S2_max_marks" value="{{$student->S2_max_marks}}" max="100" class="form-control form-control-sm" required>
                        </div>

             <div class="form-group col-lg-4">
    <label>Marks Obtained in S2</label>
    <input type="number" id="S2_obtained_mark" name="S2_obtained_mark" value="{{$student->S2_obtained_mark}}" class="form-control form-control-sm" required oninput="calculateTotal()">
</div>

                        {{-- <div class="form-group col-lg-4">
                            <label>Subject 3</label>
                            <select name="S3" class="form-control form-control-sm" required>
                                <option value="">Select Subject</option>
                                <option value="ENGLISH" {{ $student->S3 == 'ENGLISH' ? 'selected' : '' }}>English</option>
                                <option value="PHYSICS" {{ $student->S3 == 'PHYSICS' ? 'selected' : '' }}>Physics</option>
                                <option value="CHEMISTRY" {{ $student->S3 == 'CHEMISTRY' ? 'selected' : '' }}>Chemistry</option>
                                <option value="BIOLOGY" {{ $student->S3 == 'BIOLOGY' ? 'selected' : '' }}>Biology</option>
                            </select>
                        </div> --}}



                        <div class="form-group col-lg-4">
                            <label>Subject 3</label>
                            <input type="text" name="S3" value="{{ $student->S3 ?? 'Chemistry' }}" class="form-control form-control-sm" required readonly>
                            </div>
                  



                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S3</label>
                            <input type="number" name="S3_max_marks" value="{{$student->S3_max_marks}}"  max="100" class="form-control form-control-sm" required>
                        </div>

            <div class="form-group col-lg-4">
    <label>Marks Obtained in S3</label>
    <input type="number" id="S3_obtained_mark" name="S3_obtained_mark" value="{{$student->S3_obtained_mark}}" class="form-control form-control-sm" required oninput="calculateTotal()">
</div>


                        {{-- <div class="form-group col-lg-4">
                            <label>Subject 4</label>
                            <select name="S4" class="form-control form-control-sm" required>
                                <option value="">Select Subject</option>
                                <option value="ENGLISH" {{ $student->S4 == 'ENGLISH' ? 'selected' : '' }}>English</option>
                                <option value="PHYSICS" {{ $student->S4 == 'PHYSICS' ? 'selected' : '' }}>Physics</option>
                                <option value="CHEMISTRY" {{ $student->S4 == 'CHEMISTRY' ? 'selected' : '' }}>Chemistry</option>
                                <option value="BIOLOGY" {{ $student->S4 == 'BIOLOGY' ? 'selected' : '' }}>Biology</option>
                            </select>
                        </div> --}}


                        <div class="form-group col-lg-4">
                            <label>Subject 4</label>
                            <input type="text" name="S4" value="{{ $student->S4
                             ?? 'Biology' }}" class="form-control form-control-sm" required readonly>
                            </div>

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S4</label>
                            <input type="number" name="S4_max_marks" value="{{$student->S4_max_marks}}" max="100" class="form-control form-control-sm" required>
                        </div>

                    <div class="form-group col-lg-4">
    <label>Marks Obtained in S4</label>
    <input type="number" id="S4_obtained_mark" name="S4_obtained_mark" value="{{$student->S4_obtained_mark}}" class="form-control form-control-sm" required oninput="calculateTotal()">
</div>


                  <div class="form-group col-lg-4">
    <label>Total Marks</label>
    <input type="number" id="total_marks" name="total_marks" value="{{$student->total_marks}}" class="form-control form-control-sm" readonly>
</div>
                        


                        </fieldset>

                        <h3> Neet Details</h3>
                        <fieldset class="row">
                            <div class="form-group col-lg-3">
                                <label>Total No of Attempts in NEET</label>
                                <input type="number" name="total_attempts_neet" value="{{$student->total_attempts_neet}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>NEET Score - 2024</label>
                                <input type="number" name="neet_score_2024" value="{{$student->neet_score_2024}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>NEET Score - 2025</label>
                                <input type="number" name="neet_score_2025" value="{{$student->neet_score_2025}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Are you Repeater or Re-repeater</label>
                                <select name="repeater_re_repeater" class="form-control form-control-sm" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="repeater" {{ $student->repeater_re_repeater == 'repeater' ? 'selected' : '' }}>Repeater</option>
                                    <option value="re-repeater" {{ $student->repeater_re_repeater == 're-repeater' ? 'selected' : '' }}>Re-repeater</option>
                                    <option value="Regular" {{ $student->repeater_re_repeater == 'Regular' ? 'selected' : '' }}>Regular</option>

                                </select>
                            </div>
                            
                            
                            <div class="form-group col-lg-3">
                                <label>Previous Course Studied for NEET</label>
                                <select name="previous_course_studied_neet" class="form-control form-control-sm" required>
                                    <option value="">Select Course</option>
                                    <option value="Integrated" {{ $student->previous_course_studied_neet == 'Integrated' ? 'selected' : '' }}>Integrated</option>
                                    <option value="Crash course" {{ $student->previous_course_studied_neet == 'Crash course' ? 'selected' : '' }}>Crash course</option>
                                    <option value="Long term" {{ $student->previous_course_studied_neet == 'Long term' ? 'selected' : '' }}>Long term</option>
                                </select>
                            </div>
                            
        
        <div class="form-group col-lg-3">
            <label>Name of the Institution Studied</label>
            <input type="text" name="institution_studied_name" value="{{$student->institution_studied_name}}" class="form-control form-control-sm" required>
        </div>
        
        
        <div class="form-group col-lg-3">
            <label>Institution Bill Type</label>
            <select name="institution_bill_type" class="form-control form-control-sm" required>
                <option value="">Select Bill Type</option>
                <option value="GPCC, Namakkal" {{ $student->institution_bill_type == 'GPCC, Namakkal' ? 'selected' : '' }}>GPCC, Namakkal</option>
                <option value="GPI, Namakkal" {{ $student->institution_bill_type == 'GPI, Namakkal' ? 'selected' : '' }}>GPI, Namakkal</option>
                <option value="GPCI, Erode" {{ $student->institution_bill_type == 'GPCI, Erode' ? 'selected' : '' }}>GPCI, Erode</option>
                <option value="GPCI, Karur" {{ $student->institution_bill_type == 'GPCI, Karur' ? 'selected' : '' }}>GPCI, Karur</option>
                <option value="GPCA, Coimbatore" {{ $student->institution_bill_type == 'GPCA, Coimbatore' ? 'selected' : '' }}>GPCA, Coimbatore</option>
                <option value="GPA, Chennai" {{ $student->institution_bill_type == 'GPA, Chennai' ? 'selected' : '' }}>GPA, Chennai</option>
                <option value="Others" {{ $student->institution_bill_type == 'Others' ? 'selected' : '' }}>Others</option>
            </select>
        </div>
        
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
<script src="{{asset('js/page/form-wizard.js') }}"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/8H2L9eO5g5y5V3K5Hg5T5XH3KZ9r5X5K5Z9r5T" crossorigin="anonymous"></script> --}}

<script>


    function hostel(type){
        document.getElementById('hostel_dayscholar').value = '';
        if(type == 'Offline'){
            document.getElementById('hostel_dayscholar').disabled = false;
        }else{
            document.getElementById('hostel_dayscholar').disabled = true;
        }
    }
    // Initial check if the page loads with a predefined value
    window.onload = function() {
        var selectedCoachingType = document.getElementById('coaching_type').value;
        var hostelSelect = document.getElementById('hostel_dayscholar');
        
        if (selectedCoachingType !== 'Offline') {
            hostelSelect.disabled = true;  // Disable if the value is not "Offline"
        }
    };

    function  City(state) {
      if(!state) return;
      $.get("{{ route('staff.create') }}", {state: state}, function(data) {
          var html = '<option value="">Select City</option>';
          $.each(data, function(key, value) {
              html += '<option value="' + value.District + '">' + value.District + '</option>';
          });
          $('#city').html(html);
      });
   }


   function Pincode(city){
    $.get("{{ route('student.create') }}", {city: city}, function(data) {
            var html = '';
            $('#pincode').val('');
          $.each(data, function(key, value) {
              html += '<option value="' + value.Pincode + '">' + value.Pincode + '</option>';
          });
          $('#pincode_list').html(html);
      });
   }


    function calculateAge() {
        const dobInput = document.getElementById('dobInput');
        const ageInput = document.getElementById('ageInput');

        const dob = new Date(dobInput.value);
        const today = new Date();

        if (!isNaN(dob.getTime())) { 
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age; 
        } else {
            ageInput.value = ''; 
        }
    }

    function calculateTotal() {
        const s1 = parseInt(document.getElementById('S1_obtained_mark').value) || 0;
        const s2 = parseInt(document.getElementById('S2_obtained_mark').value) || 0;
        const s3 = parseInt(document.getElementById('S3_obtained_mark').value) || 0;
        const s4 = parseInt(document.getElementById('S4_obtained_mark').value) || 0;

        const total = s1 + s2 + s3 + s4;
        document.getElementById('total_marks').value = total;
    }

    $(document).ready(function () {
    $('#student_name, #ph_no1, #ph_no2, #father_name, #father_ph_no, #mother_name, #mother_ph_no, #aadhar_card_no, #student_whatsapp_no, #parent_whatsapp_no').on('input', function () {
        let input = $(this).val();
        let nameRegex = /^[a-zA-Z .]*$/;  // Allow alphabets, spaces, and dots
        let phoneRegex = /^[0-9]{10}$/;  // Only 10 digits allowed for phone numbers
        let aadharRegex = /^[0-9]{12}$/;  // 12 digits for Aadhar card

        if ($(this).attr('id') == 'student_name' || $(this).attr('id') == 'father_name' || $(this).attr('id') == 'mother_name') {
            if (!nameRegex.test(input)) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').text('Only alphabets, spaces, and dots are allowed');
            } else {
                $(this).removeClass('is-invalid');
            }
        }

        // For phone numbers: only 10 digits allowed
        if ($(this).attr('id') == 'ph_no1' || $(this).attr('id') == 'ph_no2' || $(this).attr('id') == 'father_ph_no' || $(this).attr('id') == 'mother_ph_no' || $(this).attr('id') == 'student_whatsapp_no' || $(this).attr('id') == 'parent_whatsapp_no') {
            if (!phoneRegex.test(input)) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').text('Phone numbers must be exactly 10 digits');
            } else {
                $(this).removeClass('is-invalid');
            }
        }

        if ($(this).attr('id') == 'aadhar_card_no') {
            if (!aadharRegex.test(input)) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').text('Aadhar card number should be exactly 12 digits');
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });
});

</script>




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
