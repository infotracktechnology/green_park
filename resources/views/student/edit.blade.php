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
                               <label>Student Name</label>
                                <input type="text" name="student_name" value="{{$student->student_name}}" class="form-control form-control-sm" required>
                           </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mobile No 1</label>
                                 <input type="text" name="ph_no1" value="{{$student->ph_no1}}" class="form-control form-control-sm" required>
                                
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mobile No 2</label>
                                 <input type="text" name="ph_no2" value="{{$student->ph_no2}}" class="form-control form-control-sm" required>
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
                                 <input type="date" name="dob" value="{{$student->dob}}" class="form-control form-control-sm" required >
                            </div>
        
        
                            <div class="form-group col-lg-3">
                                <label>Father Name</label>
                                 <input type="text" name="father_name" value="{{$student->father_name}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Father Mobile No</label>
                                 <input type="text" name="father_ph_no" value="{{$student->father_ph_no}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mother Name</label>
                                 <input type="text" name="mother_name" value="{{$student->mother_name}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Mother Mobile No</label>
                                 <input type="text" name="mother_ph_no" value="{{$student->mother_ph_no}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Admission Opted For</label>
                                 <select name="admission_opted_for" class="form-control form-control-sm" required >
                                     <option value="">Select Coaching Type</option>
                                     <option value="Online" @if($student->admission_opted_for == 'Online') selected @endif>Online</option>
                                     <option value="Offline" @if($student->admission_opted_for == 'Offline') selected @endif>Offline</option>
                                 </select>
                             </div>
        
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
                                 <input type="text" name="nationality" value="{{$student->nationality}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Religion</label>
                                 <input type="text" name="religion" value="{{$student->religion}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Community</label>
                                 <input type="text" name="community" value="{{$student->community}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Caste</label>
                                 <input type="text" name="caste" value="{{$student->caste}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Student WhatsApp No</label>
                                 <input type="number" name="student_whatsapp_no" value="{{$student->student_whatsapp_no}}" class="form-control form-control-sm" required>
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
                                <select name="state" class="form-control form-control-sm" required>
                                  <option value="">Select State</option>
                                  @foreach ($states as $state)
                                  <option value="{{$state->State}}" @if($student->state == $state->State) selected @endif>{{$state->State}}</option>
                                  @endforeach
                                </select>
                           </div>
        

        
                            <div class="form-group col-lg-3">
                                <label>City</label>
                                 <select name="city" id="city" class="form-control form-control-sm" required>
                                   <option value="">Select City</option>
                                   @foreach ($districts as $district)
                                   <option value="{{$district->District}}" @if($student->city == $district->District) selected @endif>{{$district->District}}</option>
                                   @endforeach
                                 </select>
                            </div>
                
                    
        
                            <div class="form-group col-lg-3">
                                <label>Pincode</label>
                                 <input type="number" name="pincode" value="{{$student->pincode}}" class="form-control form-control-sm" required>
                            </div>
        
                            <div class="form-group col-lg-3">
                                <label>Parent WhatsApp No</label>
                                 <input type="number" name="parent_whatsapp_no" value="{{$student->parent_whatsapp_no}}" class="form-control form-control-sm" required>
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
                                <input type="text" name="board_of_study_X_std" value="{{$student->board_of_study_X_std}}" class="form-control form-control-sm" required>
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
                                <input type="text" name="board_of_study_XII_std" value="{{$student->board_of_study_XII_std}}" class="form-control form-control-sm" required>
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
                            
                           <div class="form-group col-lg-4">
                            <label>Subject 1</label>
                            <input type="text" name="S1" value="{{$student->S1}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S1</label>
                            <input type="number" name="S1_max_marks" value="{{$student->S1_max_marks}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Marks Obtained in S1</label>
                            <input type="number" name="S1_obtained_mark" value="{{$student->S1_obtained_mark}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Subject 2</label>
                            <input type="text" name="S2" value="{{$student->S2}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S2</label>
                            <input type="number" name="S2_max_marks" value="{{$student->S2_max_marks}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Marks Obtained in S2</label>
                            <input type="number" name="S2_obtained_mark" value="{{$student->S2_obtained_mark}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Subject 3</label>
                            <input type="text" name="S3" value="{{$student->S3}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S3</label>
                            <input type="number" name="S3_max_marks" value="{{$student->S3_max_marks}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Marks Obtained in S3</label>
                            <input type="number" name="S3_obtained_mark" value="{{$student->S3_obtained_mark}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Subject 4</label>
                            <input type="text" name="S4" value="{{$student->S4}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Maximum Marks of S4</label>
                            <input type="number" name="S4_max_marks" value="{{$student->S4_max_marks}}" class="form-control form-control-sm" required>
                        </div>

                        <div class="form-group col-lg-4">
                            <label>Marks Obtained in S4</label>
                            <input type="number" name="S4_obtained_mark" value="{{$student->S4_obtained_mark}}" class="form-control form-control-sm" required>
                        </div>


                        <div class="form-group col-lg-4">
                            <label>Total Marks</label>
                            <input type="number" name="total_marks" value="{{$student->total_marks}}" class="form-control form-control-sm" required>
                        </div>
                        


                        </fieldset>

                        <h3> Neet Details</h3>
                        <fieldset class="row">
                            <div class="form-group col-lg-3">
                                <label>Total No of Attempts in NEET</label>
                                <input type="number" name="total_attempts_neet" value="{{$student->total_attempts_neet}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>NEET Score - 2023</label>
                                <input type="number" name="neet_score_2023" value="{{$student->neet_score_2023}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>NEET Score - 2024</label>
                                <input type="number" name="neet_score_2024" value="{{$student->neet_score_2024}}" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Are you Repeater or Re-repeater</label>
                                <select name="repeater_re_repeater" class="form-control form-control-sm" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="repeater" {{ $student->repeater_re_repeater == 'repeater' ? 'selected' : '' }}>Repeater</option>
                                    <option value="re-repeater" {{ $student->repeater_re_repeater == 're-repeater' ? 'selected' : '' }}>Re-repeater</option>
                                </select>
                            </div>
                            
                            
                            <div class="form-group col-lg-3">
                                <label>Previous Course Studied for NEET</label>
                                <input type="text" name="previous_course_studied_neet" value="{{$student->previous_course_studied_neet}}" class="form-control form-control-sm" required>
                            </div>
        
        <div class="form-group col-lg-3">
            <label>Name of the Institution Studied</label>
            <input type="text" name="institution_studied_name" value="{{$student->institution_studied_name}}" class="form-control form-control-sm" required>
        </div>
        
        
        <div class="form-group col-lg-3">
            <label>Institution Bill Type</label>
            <input type="text" name="institution_bill_type" value="{{$student->institution_bill_type}}" class="form-control form-control-sm" required>
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
<script src="{{ asset('js/page/form-wizard.js') }}"></script>
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

    function City(state) {
      if(!state) return;
      $.get("{{ route('staff.create') }}", {state: state}, function(data) {
          var html = '<option value="">Select City</option>';
          $.each(data, function(key, value) {
              html += '<option value="' + value.District + '">' + value.District + '</option>';
          });
          $('#city').html(html);
      });
   }


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

