@extends('layouts.app')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">

           @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif

          <div class="card card-primary">

            <form id="wizard_with_validation" method="POST" novalidate="novalidate" action="{{ route('student.update', $Student->id) }}" class="my-4" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <h3>Personal Details</h3>
              <fieldset class="row">
                <div class="form-group col-lg-3">
                  <label>Admission Date</label>
                  <input type="date" name="admission_date" value="{{$Student->admission_date}}" class="form-control form-control-sm">

                </div>
                <div class="form-group col-lg-3">
                  <label for="branch_id">Campus</label>
                  <select name="campus" class="form-control form-control-sm" id="campus-select">
                    <option value="" disabled selected>Select Campus</option>
                    @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @if($branch->id == $Student->campus) selected @endif>
                      {{ $branch->name }}
                    </option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>Course</label>
                  <select name="course" id="course" class="form-control form-control-sm" onchange="handleType(this);" required>
                    <option value="">Select Course</option>
                    @foreach ($course as $row)
                    <option value="{{$row}}" @selected($row==$Student->course)>{{$row}}</option>
                    @endforeach
                  </select>
                </div>


                <div class="form-group col-lg-3">
                  <label for="student_name">Student Name</label>
                  <input type="text" name="student_name" id="student_name" value="{{ old('student_name', $Student->student_name) }}" class="form-control form-control-sm alphabetsOnly">

                </div>


                <div class="form-group col-lg-3">
                  <label>Password</label>
                  <input type="text" name="password" value="{{$Student->password}}" class="form-control form-control-sm">
                </div>



                <div class="form-group col-lg-3">
                  <label>Coaching Type</label>
                  <select name="coaching_type" id="coaching_type" class="form-control form-control-sm type" onchange="hostel(this.value)">
                    <option value="">Select Coaching Type</option>
                    @foreach ($coachingtype as $row)
                    <option value="{{$row}}" @selected($row==$Student->coaching_type)>{{$row}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>Hostel/Day Scholar</label>
                  <select name="hostel_dayscholar" id="hostel_dayscholar" class="form-control form-control-sm">
                    <option value="">Select Option</option>
                    @foreach ($hostel as $row)
                    <option value="{{$row}}" @selected($row==$Student->hostel_dayscholar)>{{$row}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>AC/Non AC</label>
                  <select name="ac_nonac" class="form-control form-control-sm" id="ac-nonac-select">
                    <option value="">Select AC/Non AC</option>
                    <option value="AC" @if($Student->ac_nonac == 'AC') selected @endif>AC</option>
                    <option value="NON AC" @if($Student->ac_nonac == 'NON AC') selected @endif>NON AC</option>
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>Section</label>
                  <input type="text" name="section" value="{{$Student->section}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Gender</label>
                  <select name="gender" class="form-control form-control-sm">
                    <option value="">Select Gender</option>
                    <option value="MALE" @if($Student->gender == 'MALE') selected @endif>MALE</option>
                    <option value="FEMALE" @if($Student->gender == 'FEMALE') selected @endif>FEMALE</option>
                    <option value="Other" @if($Student->gender == 'Other') selected @endif>Other</option>
                  </select>
                </div>



                <div class="form-group col-lg-3">
                  <label>Date of Birth</label>
                  <input type="date" name="dob" value="{{ $Student->dob }}" class="form-control form-control-sm" id="dobInput" onchange="calculateAge()">
                </div>

                <div class="form-group col-lg-3">
                  <label>Age</label>
                  <input type="number" name="age" value="{{ \Carbon\Carbon::parse($Student->dob)->age }}" class="form-control form-control-sm" id="ageInput" readonly>
                </div>


                <div class="form-group col-lg-3">
                  <label for="father_name">Father Name</label>
                  <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $Student->father_name) }}" class="form-control form-control-sm alphabetsOnly">

                </div>

                <div class="form-group col-lg-3">
                  <label for="father_ph_no">Father Mobile No</label>
                  <input type="number" name="father_ph_no" id="father_ph_no" value="{{ old('father_ph_no', $Student->father_ph_no) }}" class="form-control form-control-sm digits">

                </div>

                <div class="form-group col-lg-3">
                  <label for="mother_name">Mother Name</label>
                  <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $Student->mother_name) }}" class="form-control form-control-sm alphabetsOnly">

                </div>

                <div class="form-group col-lg-3">
                  <label for="mother_ph_no">Mother Mobile No</label>
                  <input type="number" name="mother_ph_no" id="mother_ph_no" value="{{ old('mother_ph_no', $Student->mother_ph_no) }}" class="form-control form-control-sm digits">

                </div>





                <div class="form-group col-lg-3">
                  <label>Blood Group</label>
                  <select name="blood_group" class="form-control form-control-sm">
                    <option value="">Select Blood Group</option>
                    <option value="A+" @if($Student->blood_group == 'A+') selected @endif>A+</option>
                    <option value="A-" @if($Student->blood_group == 'A-') selected @endif>A-</option>
                    <option value="B+" @if($Student->blood_group == 'B+') selected @endif>B+</option>
                    <option value="B-" @if($Student->blood_group == 'B-') selected @endif>B-</option>
                    <option value="AB+" @if($Student->blood_group == 'AB+') selected @endif>AB+</option>
                    <option value="AB-" @if($Student->blood_group == 'AB-') selected @endif>AB-</option>
                    <option value="O+" @if($Student->blood_group == 'O+') selected @endif>O+</option>
                    <option value="O-" @if($Student->blood_group == 'O-') selected @endif>O-</option>
                  </select>
                </div>





                <div class="form-group col-lg-3">
                  <label for="aadhar_card_no">Aadhar Card No</label>
                  <input type="number" name="aadhar_card_no" id="aadhar_card_no" value="{{ old('aadhar_card_no', $Student->aadhar_card_no) }}" class="form-control form-control-sm" pattern="^[0-9]{12}$">
                  <div class="invalid-feedback">
                    Aadhar card number should be exactly 12 digits.
                  </div>
                </div>





                <div class="form-group col-lg-3">
                  <label>Nationality</label>
                  <select name="nationality" class="form-control form-control-sm">
                    <option value="">Select Nationality</option>
                    <option value="Indian" @if($Student->nationality == 'Indian') selected @endif>Indian</option>
                    <option value="Foreign" @if($Student->nationality == 'Foreign') selected @endif>Foreign</option>
                    <option value="NRI" @if($Student->nationality == 'NRI') selected @endif>NRI</option>
                  </select>
                </div>



                <div class="form-group col-lg-3">
                  <label>Religion</label>
                  <select name="religion" class="form-control form-control-sm">
                    <option value="">Select Religion</option>
                    <option value="Hindu" @if($Student->religion == 'Hindu') selected @endif>Hindu</option>
                    <option value="Christian" @if($Student->religion == 'Christian') selected @endif>Christian</option>
                    <option value="Muslim" @if($Student->religion == 'Muslim') selected @endif>Muslim</option>
                  </select>
                </div>


                <div class="form-group col-lg-3">
                  <label>Community</label>
                  <select name="community" class="form-control form-control-sm">
                    <option value="">Select Community</option>
                    <option value="OC" @if($Student->community == 'OC') selected @endif>OC</option>
                    <option value="BC" @if($Student->community == 'BC') selected @endif>BC</option>
                    <option value="BCM" @if($Student->community == 'BCM') selected @endif>BCM</option>
                    <option value="MBC / DNC" @if($Student->community == 'MBC / DNC') selected @endif>MBC / DNC</option>
                    <option value="SC" @if($Student->community == 'SC') selected @endif>SC</option>
                    <option value="SCA" @if($Student->community == 'SCA') selected @endif>SCA</option>
                    <option value="ST" @if($Student->community == 'ST') selected @endif>ST</option>
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>Caste</label>
                  <input type="text" name="caste" value="{{$Student->caste}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label for="student_whatsapp_no">Student WhatsApp No</label>
                  <input type="number" name="student_whatsapp_no" id="student_whatsapp_no" value="{{ old('student_whatsapp_no', $Student->student_whatsapp_no) }}" class="form-control form-control-sm digits">

                </div>


                <div class="form-group col-lg-3">
                  <label>Institution Bill Type</label>
                  <select name="institution_bill_type" id="bill_type" class="form-control form-control-sm" required>
                    <option value="">Select Bill Type</option>
                    <option value="GPCC,NKL" @selected($Student->institution_bill_type == 'GPCC,NKL')>GPCC,NKL</option>
                    <option value="GPI,NKL" @selected($Student->institution_bill_type == 'GPI,NKL')>GPI,NKL</option>
                    <option value="GPCI,NKL" @selected($Student->institution_bill_type == 'GPCI,NKL')>GPCI,NKL</option>
                    <option value="GPCI,KARUR" @selected($Student->institution_bill_type == 'GPCI,KARUR')>GPCI,KARUR</option>
                    <option value="GPCI,ERODE" @selected($Student->institution_bill_type == 'GPCI,ERODE')>GPCI,ERODE</option>
                    <option value="GPCA,COIMBATORE" @selected($Student->institution_bill_type == 'GPCA,COIMBATORE')>GPCA,COIMBATORE</option>
                    <option value="GPA,CHENNAI" @selected($Student->institution_bill_type == 'GPA,CHENNAI')>GPA,CHENNAI</option>
                  </select>
                </div>



                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>

              </fieldset>

              <h3> Address</h3>
              <fieldset class="row">
                <div class="form-group col-lg-3">
                  <label>Door No</label>
                  <input type="text" name="door_no" value="{{$Student->door_no}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Street Name</label>
                  <input type="text" name="street_name" value="{{$Student->street_name}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>City</label>
                  <input type="text" name="city" value="{{$Student->city}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>State</label>
                  <select name="state" id="state" onchange="City(this.value);" class="form-control form-control-sm">
                    <option value="">Select State</option>
                    @foreach ($states as $state)
                    <option value="{{$state->State}}" @if($Student->state == $state->State) selected @endif>{{$state->State}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>District</label>
                  <select name="district" id="city" onchange="Pincode(this.value);" class="form-control form-control-sm">
                    <option value="">Select City</option>
                    @foreach ($districts as $city)
                    <option value="{{ $city->District }}" @if($Student->district == $city->District) selected @endif>{{ $city->District }}</option>
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
                  <input type="text" id="pincode" list="pincode_list" name="pincode" value="{{$Student->pincode}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label for="parent_whatsapp_no">Parent WhatsApp No</label>
                  <input type="number" name="parent_whatsapp_no" id="parent_whatsapp_no" value="{{ old('parent_whatsapp_no', $Student->parent_whatsapp_no) }}" class="form-control form-control-sm digits">

                </div>

                <div class="form-group col-lg-3">
                  <label>Email ID</label>
                  <input type="email" name="email" value="{{$Student->email}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>

              </fieldset>
              <h3>Parent Details</h3>
              <fieldset class="row">
                <div class="form-group col-lg-3">
                  <label>Father Qualification</label>
                  <input type="text" name="father_qualification" value="{{$Student->father_qualification}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Father Occupation</label>
                  <input type="text" name="father_occupation" value="{{$Student->father_occupation}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Father Annual Income</label>
                  <input type="number" name="father_annual_income" value="{{$Student->father_annual_income}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Father Designation</label>
                  <input type="text" name="father_designation" value="{{$Student->father_designation}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Father's Place of Work</label>
                  <input type="text" name="fathers_place_of_work" value="{{$Student->fathers_place_of_work}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Mother Qualification</label>
                  <input type="text" name="mother_qualification" value="{{$Student->mother_qualification}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Mother Occupation</label>
                  <input type="text" name="mother_occupation" value="{{$Student->mother_occupation}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Mother Annual Income</label>
                  <input type="number" name="mother_annual_income" value="{{$Student->mother_annual_income}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Mother Designation</label>
                  <input type="text" name="mother_designation" value="{{$Student->mother_designation}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Mother's Place of Work</label>
                  <input type="text" name="mother_place_of_work" value="{{$Student->mother_place_of_work}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>


              </fieldset>


              <h3>Academic Details</h3>
              <fieldset class="row">

                <div class="form-group col-lg-3">
                  <label>Board of Study (X std)</label>
                  <select name="board_of_study_X_std" class="form-control form-control-sm">
                    <option value="">Select Board</option>
                    <option value="STATE BOARD" {{ $Student->board_of_study_X_std == 'STATE BOARD' ? 'selected' : '' }}>STATE BOARD</option>
                    <option value="CBSE" {{ $Student->board_of_study_X_std == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                    <option value="ICSE" {{ $Student->board_of_study_X_std == 'ICSE' ? 'selected' : '' }}>ICSE</option>
                    <option value="IGCSE" {{ $Student->board_of_study_X_std == 'IGCSE' ? 'selected' : '' }}>IGCSE</option>
                  </select>
                </div>



                <div class="form-group col-lg-3">
                  <label>Name of School (X std)</label>
                  <input type="text" name="school_name_X_std" value="{{$Student->school_name_X_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>District Name of the School (X std)</label>
                  <input type="text" name="district_name_school_X_std" value="{{$Student->district_name_school_X_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Total Marks Obtained in X std</label>
                  <input type="number" name="total_marks_X_std" value="{{$Student->total_marks_X_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Board of Study (XII std)</label>
                  <select name="board_of_study_XII_std" class="form-control form-control-sm">
                    <option value="">Select Board</option>
                    <option value="STATEBOARD" {{ $Student->board_of_study_XII_std == 'STATEBOARD' ? 'selected' : '' }}>STATEBOARD</option>
                    <option value="CBSE" {{ $Student->board_of_study_XII_std == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                    <option value="ICSE" {{ $Student->board_of_study_XII_std == 'ICSE' ? 'selected' : '' }}>ICSE</option>
                    <option value="IGCSE" {{ $Student->board_of_study_XII_std == 'IGCSE' ? 'selected' : '' }}>IGCSE</option>
                  </select>
                </div>


                <div class="form-group col-lg-3">
                  <label>Name of School (XII std)</label>
                  <input type="text" name="school_name_XII_std" value="{{$Student->school_name_XII_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>District Name of the School (XII std)</label>
                  <input type="text" name="district_name_school_XII_std" value="{{$Student->district_name_school_XII_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Total Marks Obtained in XII std</label>
                  <input type="number" name="total_marks_XII_std" value="{{$Student->total_marks_XII_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>

              </fieldset>


              <h3>Mark Details</h3>
              <fieldset class="row">



                <div class="form-group col-lg-4">
                  <label>Subject 1</label>
                  <input type="text" name="S1" value="{{ $Student->S1 ?? 'English' }}" class="form-control form-control-sm" readonly>
                </div>




                <div class="form-group col-lg-4">
                  <label>Maximum Marks of S1</label>
                  <input type="number" name="S1_max_marks" value="{{$Student->S1_max_marks}}" max="100" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-4">
                  <label>Marks Obtained in S1</label>
                  <input type="number" id="S1_obtained_mark" name="S1_obtained_mark" value="{{$Student->S1_obtained_mark}}" class="form-control form-control-sm" oninput="calculateTotal()">
                </div>



                <div class="form-group col-lg-4">
                  <label>Subject 2</label>
                  <input type="text" name="S2" value="{{ $Student->S2 ?? 'Physics' }}" class="form-control form-control-sm" readonly>
                </div>

                <div class="form-group col-lg-4">
                  <label>Maximum Marks of S2</label>
                  <input type="number" name="S2_max_marks" value="{{$Student->S2_max_marks}}" max="100" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-4">
                  <label>Marks Obtained in S2</label>
                  <input type="number" id="S2_obtained_mark" name="S2_obtained_mark" value="{{$Student->S2_obtained_mark}}" class="form-control form-control-sm" oninput="calculateTotal()">
                </div>

                <div class="form-group col-lg-4">
                  <label>Subject 3</label>
                  <input type="text" name="S3" value="{{ $Student->S3 ?? 'Chemistry' }}" class="form-control form-control-sm" readonly>
                </div>


                <div class="form-group col-lg-4">
                  <label>Maximum Marks of S3</label>
                  <input type="number" name="S3_max_marks" value="{{$Student->S3_max_marks}}" max="100" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-4">
                  <label>Marks Obtained in S3</label>
                  <input type="number" id="S3_obtained_mark" name="S3_obtained_mark" value="{{$Student->S3_obtained_mark}}" class="form-control form-control-sm" oninput="calculateTotal()">
                </div>



                <div class="form-group col-lg-4">
                  <label>Subject 4</label>
                  <input type="text" name="S4" value="{{ $Student->S4
                             ?? 'Biology' }}" class="form-control form-control-sm" readonly>
                </div>

                <div class="form-group col-lg-4">
                  <label>Maximum Marks of S4</label>
                  <input type="number" name="S4_max_marks" value="{{$Student->S4_max_marks}}" max="100" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-4">
                  <label>Marks Obtained in S4</label>
                  <input type="number" id="S4_obtained_mark" name="S4_obtained_mark" value="{{$Student->S4_obtained_mark}}" class="form-control form-control-sm" oninput="calculateTotal()">
                </div>


                <div class="form-group col-lg-4">
                  <label>Total Marks</label>
                  <input type="number" id="total_marks" name="total_marks" value="{{$Student->total_marks}}" class="form-control form-control-sm" readonly>
                </div>
                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>


              </fieldset>

              <h3> Neet Details</h3>
              <fieldset class="row">
                <div class="form-group col-lg-3">
                  <label>Total No of Attempts in NEET</label>
                  <input type="number" name="total_attempts_neet" value="{{$Student->total_attempts_neet}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>NEET Score - 2024</label>
                  <input type="number" name="neet_score_2024" value="{{$Student->neet_score_2024}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>NEET Score - 2025</label>
                  <input type="number" name="neet_score_2025" value="{{$Student->neet_score_2025}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Are you Repeater or Re-repeater</label>
                  <select name="repeater_re_repeater" class="form-control form-control-sm">
                    <option value="" disabled selected>Select an option</option>
                    <option value="repeater" {{ $Student->repeater_re_repeater == 'repeater' ? 'selected' : '' }}>Repeater</option>
                    <option value="re-repeater" {{ $Student->repeater_re_repeater == 're-repeater' ? 'selected' : '' }}>Re-repeater</option>
                    <option value="Regular" {{ $Student->repeater_re_repeater == 'Regular' ? 'selected' : '' }}>Regular</option>

                  </select>
                </div>


                <div class="form-group col-lg-3">
                  <label>Previous Course Studied for NEET</label>
                  <select name="previous_course_studied_neet" class="form-control form-control-sm">
                    <option value="">Select Course</option>
                    <option value="Integrated" {{ $Student->previous_course_studied_neet == 'Integrated' ? 'selected' : '' }}>Integrated</option>
                    <option value="Crash course" {{ $Student->previous_course_studied_neet == 'Crash course' ? 'selected' : '' }}>Crash course</option>
                    <option value="Long term" {{ $Student->previous_course_studied_neet == 'Long term' ? 'selected' : '' }}>Long term</option>
                  </select>
                </div>


                <div class="form-group col-lg-3">
                  <label>Name of the Institution Studied</label>
                  <input type="text" name="institution_studied_name" value="{{$Student->institution_studied_name}}" class="form-control form-control-sm">
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


<script>
  // function hostel(type){
  //     document.getElementById('hostel_dayscholar').value = '';
  //     if(type == 'OFFLINE'){
  //         document.getElementById('hostel_dayscholar').disabled = false;
  //     }else{
  //         document.getElementById('hostel_dayscholar').disabled = true;
  //     }
  // }
  
  // window.onload = function() {
  //     var selectedCoachingType = document.getElementById('coaching_type').value;
  //     var hostelSelect = document.getElementById('hostel_dayscholar');
      
  //     if (selectedCoachingType !== 'OFFLINE') {
  //         hostelSelect.disabled = true;  
  //     }
  // };
  
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
  const acNonAcSelect = $('#ac-nonac-select');
  
  $('#campus-select').on('change', function () {
      const selectedCampusName = $(this).find('option:selected').text().trim();
  
      if (selectedCampusName.startsWith('GPCC')) {
          acNonAcSelect.prop('disabled', false); // Enable AC/Non AC field
      } else {
          acNonAcSelect.prop('disabled', true); // Disable AC/Non AC field
          acNonAcSelect.val(''); // Reset value to null when campus is not GPCC
      }
  });
  
  // Initialize AC/Non AC field state on page load
  const initialCampusName = $('#campus-select').find('option:selected').text().trim();
  if (!initialCampusName.startsWith('GPCC')) {
      acNonAcSelect.prop('disabled', true);
      acNonAcSelect.val(''); // Reset value to null when campus is not GPCC on page load
  }
  });
  $('.steps ul li').addClass('done').removeClass('disabled');  
</script>

@endsection