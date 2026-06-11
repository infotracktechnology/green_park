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
                  <input type="date" name="admission_date" value="{{date('Y-m-d', strtotime($Student->admission_date))}}" class="form-control form-control-sm">
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
                  <select name="course" id="course" class="form-control form-control-sm" required>
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
                  <select name="coaching_type" class="form-control form-control-sm type">
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
                  <label>Batch</label>
                  <input type="text" name="batch" value="{{$Student->batch}}" class="form-control form-control-sm">
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
                  <input type="date" name="dob" value="{{date('Y-m-d', strtotime($Student->dob))}}" class="form-control form-control-sm" id="dobInput">
                </div>

                <div class="form-group col-lg-3">
                  <label>Age</label>
                  <input type="text" name="age" value="{{ $Student->age }}" class="form-control form-control-sm" id="ageInput" readonly>
                </div>
                 
                <div class="form-group col-lg-3">
                  <label for="aadhar_card_no">Aadhar Card No</label>
                  <input type="number" name="aadhar_card_no" id="aadhar_card_no" value="{{ old('aadhar_card_no', $Student->aadhar_card_no) }}" class="form-control form-control-sm">
                  <div class="invalid-feedback">
                    Aadhar card number should be exactly 12 digits.
                  </div>
                </div>

                <div class="form-group col-lg-3">
                  <label>Nationality</label>
                  <input type="text" name="nationality" value="{{ $Student->nationality }}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Religion</label>
                  <input type="text" name="religion" value="{{ $Student->religion }}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Community</label>
                  <select name="community" class="form-control form-control-sm">
                    <option value="">Select Community</option>
                    <option value="OC" @if($Student->community == 'OC') selected @endif>OC</option>
                    <option value="BC" @if($Student->community == 'BC') selected @endif>BC</option>
                    <option value="BCM" @if($Student->community == 'BCM') selected @endif>BCM</option>
                    <option value="MBC/DNC" @if($Student->community == 'MBC/DNC') selected @endif>MBC/DNC</option>
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
                  <label for="student_whatsapp_no">Student WhatsApp No</label>
                  <input type="number" name="student_whatsapp_no" id="student_whatsapp_no" value="{{ old('student_whatsapp_no', $Student->student_whatsapp_no) }}" class="form-control form-control-sm digits">
                </div>

                <div class="form-group col-lg-3">
                  <label>Institution Bill Type</label>
                  <select name="institution_bill_type" id="bill_type" class="form-control form-control-sm">
                    <option value="">Select Bill Type </option>
                    <option value="GPI,NKL" @selected($Student->institution_bill_type == 'GPI,NKL')>GPI,NKL</option>
                    <option value="GPCC,NKL" @selected($Student->institution_bill_type == 'GPCC,NKL')>GPCC,NKL</option>
                    <option value="GPCI,NKL" @selected($Student->institution_bill_type == 'GPCI,NKL')>GPCI,NKL</option>
                    <option value="GPCA,NKL" @selected($Student->institution_bill_type == 'GPCA,NKL')>GPCA,NKL</option>
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
                  <select name="state" id="state" class="form-control form-control-sm">
                    <option value="">Select State</option>
                    @foreach ($states as $state)
                    <option value="{{$state->State}}" @if($Student->state == $state->State) selected @endif>{{$state->State}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-lg-3">
                  <label>District</label>
                  <select name="district" id="city" class="form-control form-control-sm">
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

                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>

              </fieldset>

              <h3>Parent Details</h3>
              <fieldset class="row">

                <div class="form-group col-lg-3">
                  <label for="father_name">Father Name</label>
                  <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $Student->father_name) }}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label for="father_ph_no">Father Mobile No</label>
                  <input type="number" name="father_ph_no" id="father_ph_no" value="{{ old('father_ph_no', $Student->father_ph_no) }}" class="form-control form-control-sm">
                </div>

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
                  <label for="mother_name">Mother Name</label>
                  <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $Student->mother_name) }}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label for="mother_ph_no">Mother Mobile No</label>
                  <input type="number" name="mother_ph_no" id="mother_ph_no" value="{{ old('mother_ph_no', $Student->mother_ph_no) }}" class="form-control form-control-sm">
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
                  <label>Year of Passing (X std)</label>
                  <input type="text" name="year_of_passing_X_std" value="{{$Student->year_of_passing_X_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Board of Study (XII std)</label>
                  <select name="board_of_study_XII_std" id="board_of_study_XII_std" class="form-control form-control-sm">
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
                  <label>Year of Passing (XII std)</label>
                  <input type="text" name="year_of_passing_XII_std" value="{{$Student->year_of_passing_XII_std}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>

              </fieldset>


              <h3>Mark Details</h3>
              <fieldset class="row">
                <div class="col-lg-6 offset-lg-3">
                  <table class="table table-bordered">
                    <thead>
                      <tr class="text-center">
                        <th>SUBJECTS</th>
                        <th id="xii_max_marks"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>MARKS OBTAINED </td>
                        <td>
                          <input type="number" id="total_marks_XII_std" name="total_marks_XII_std" value="{{$Student->total_marks_XII_std}}" class="form-control form-control-sm">
                        </td>
                      </tr>
                      <tr>
                        <td>PHYSICS</td>
                        <td>
                          <input type="hidden" name="S1" value="PHYSICS">
                          <input type="hidden" name="S1_max_marks" value="100">
                          <input type="number" id="S1_obtained_mark" name="S1_obtained_mark" value="{{$Student->S1_obtained_mark}}" class="form-control form-control-sm">
                        </td>
                      </tr>
                      <tr>
                        <td>CHEMISTRY</td>
                        <td>
                          <input type="hidden" name="S2" value="CHEMISTRY">
                          <input type="hidden" name="S2_max_marks" value="100">
                          <input type="number" id="S2_obtained_mark" name="S2_obtained_mark" value="{{$Student->S2_obtained_mark}}" class="form-control form-control-sm">
                        </td>
                      </tr>
                      <tr>
                        <td>BIOLOGY</td>
                        <td>
                          <input type="hidden" name="S3" value="BIOLOGY">
                          <input type="hidden" name="S3_max_marks" value="100">
                          <input type="number" id="S3_obtained_mark" name="S3_obtained_mark" value="{{$Student->S3_obtained_mark}}" class="form-control form-control-sm">
                        </td>
                      </tr>
                      <tr class="font-weight-bold">
                        <td class="text-right">TOTAL</td>
                        <td>
                          <input type="number" id="total_marks" name="total_marks" value="{{$Student->total_marks}}" class="form-control form-control-sm" readonly>
                        </td>
                      </tr>
                    </tbody>
                  </table>
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
                  <label>NEET Score - 2026</label>
                  <input type="number" name="neet_score_2026" value="{{$Student->neet_score_2026}}" class="form-control form-control-sm">
                </div>

                <div class="form-group col-lg-3">
                  <label>Are you Repeater or Re-repeater</label>
                  <select name="repeater_re_repeater" class="form-control form-control-sm">
                    <option value="" disabled selected>Select an option</option>
                    <option value="repeater" {{ $Student->repeater_re_repeater == 'repeater' ? 'selected' : '' }}>Repeater</option>
                    <option value="re-repeater" {{ $Student->repeater_re_repeater == 're-repeater' ? 'selected' : '' }}>Re-repeater</option>


                  </select>
                </div>


                <div class="form-group col-lg-3">
                  <label>Previous Course Studied for NEET</label>
                  <select name="previous_course_studied_neet" id="previous_course_studied_neet" class="form-control form-control-sm">
                    <option value="">Select Course</option>
                    <option value="Integrated" {{ $Student->previous_course_studied_neet == 'Integrated' ? 'selected' : '' }}>Integrated</option>
                    <option value="Crash course" {{ $Student->previous_course_studied_neet == 'Crash course' ? 'selected' : '' }}>Crash course</option>
                    <option value="Long term" {{ $Student->previous_course_studied_neet == 'Long term' ? 'selected' : '' }}>Long term</option>
                    <option value="other" {{ $Student->previous_course_studied_neet == 'other' ? 'selected' : '' }}>Other</option>
                  </select>
                </div>

                <div class="form-group col-lg-3" id="other_course_div" style="display:none;">
                  <label>Specify </label>
                  <input type="text"name="other_initution" id="other_initution" class="form-control form-control-sm" value="{{ $Student->other_initution ?? '' }}">
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
  // City and Pincode AJAX
  $('#state').on('change', function () {
    const state = $(this).val();
    if (!state) return;
    $.get("{{ route('staff.create') }}", { state: state }, function (data) {
      let html = '<option value="">Select City</option>';
      $.each(data, function (key, value) {
        html += `<option value="${value.District}">${value.District}</option>`;
      });
      $('#city').html(html);
    });
  });
    
  $('#city').on('change', function () {
    const city = $(this).val();
    $.get("{{ route('student.create') }}", { city: city }, function (data) {
      let html = '';
      $('#pincode').val('');
      $.each(data, function (key, value) {
        html += `<option value="${value.Pincode}">${value.Pincode}</option>`;
      });
      $('#pincode_list').html(html);
    });
  });
    
  // Age Calculation
  $('#dobInput').on('change', function () {
    const dob = new Date($(this).val());
    const today = new Date();
    if (!isNaN(dob.getTime())) {
      let age = today.getFullYear() - dob.getFullYear();
      const monthDiff = today.getMonth() - dob.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
      }
      $('#ageInput').val(age);
    } else {
      $('#ageInput').val('');
    }
  });
    
  // Total Marks Calculation
  $('#S1_obtained_mark, #S2_obtained_mark, #S3_obtained_mark').on('input', function () {
    const s1 = parseInt($('#S1_obtained_mark').val()) || 0;
    const s2 = parseInt($('#S2_obtained_mark').val()) || 0;
    const s3 = parseInt($('#S3_obtained_mark').val()) || 0;
    $('#total_marks').val(s1 + s2 + s3);
  });
    
  // Campus and AC/Non AC logic
  $('#campus-select').on('change', function () {
    const selectedCampusName = $(this).find('option:selected').text().trim();
    const acNonAcSelect = $('#ac-nonac-select');
    if (selectedCampusName.startsWith('GPCC')) {
      acNonAcSelect.prop('disabled', false);
    } else {
      acNonAcSelect.prop('disabled', true).val('');
    }
  });
    
  // coaching type 
  $('#coaching_type').on('change', function() {
      const type = $(this).val();
      $('#hostel_dayscholar').prop('disabled', type !== 'OFFLINE').val(type !== 'OFFLINE' ? '' : $('#hostel_dayscholar').val());
      if (typeof hostel === 'function') hostel(type);
  });
  $('.steps ul li').addClass('done').removeClass('disabled');
  
  
    // Board Max Marks logic
  function updateMaxMarks() {
    var boardVal = $('#board_of_study_XII_std').val() || '';
    var maxMarks = (boardVal.replace(/\s/g, '') == 'STATEBOARD') ? 600 : 500;
    $('#xii_max_marks').text(boardVal ? boardVal + ' / ' + maxMarks : maxMarks);
  }
  
  $('#board_of_study_XII_std').on('change', updateMaxMarks);
  updateMaxMarks();

  // Previous Course Studied for NEET logic
  function toggleOtherCourse() {
    if ($('#previous_course_studied_neet').val() == 'other') {
        $('#other_course_div').show();
    } else {
        $('#other_course_div').hide();
        $('input[name="other_course"]').val('');
    }
}
$('#previous_course_studied_neet').on('change', function () {
    toggleOtherCourse();
});

toggleOtherCourse();
</script>

@endsection