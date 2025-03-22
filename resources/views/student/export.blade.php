@extends('layouts.app')
@section('title', 'Student Export')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="get" id="myForm" target="_blank"  action="{{ route('export.student') }}" enctype="multipart/form-data">
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple">Export Students</h6>
                        </div>
     
 
                        <div class="form-group col-lg-3">
                            <label for="branch_id">Category</label>
                            <select name="category" class="form-control form-control-sm"  required>
                                <option value="" disabled selected>Select Category</option>

                                <option value="student_id,admission_date,coaching_type,user_name,password_1,hostel_dayscholar,student_name,ph_no1,ph_no2,gender,dob,father_name,father_ph_no,mother_name,mother_ph_no,admission_opted_for,ac_nonac,blood_group,age,aadhar_card_no,nationality,religion,community,caste,student_whatsapp_no,door_no,street_name,city,district,state,pincode,parent_whatsapp_no,email,father_qualification,father_occupation,father_annual_income,father_designation,fathers_place_of_work,mother_qualification,mother_occupation,mother_annual_income,mother_designation,mother_place_of_work,board_of_study_X_std,school_name_X_std,district_name_school_X_std,total_marks_X_std,board_of_study_XII_std,school_name_XII_std,district_name_school_XII_std,total_marks_XII_std,S1,S1_max_marks,S1_obtained_mark,S2,S2_max_marks,S2_obtained_mark,S3,S3_max_marks,S3_obtained_mark,S4,S4_max_marks,S4_obtained_mark,total_marks,total_attempts_neet,neet_score_2025,neet_score_2024,repeater_re_repeater,section,previous_course_studied_neet,institution_studied_name,institution_bill_type">All Details</option>

                                <option value="id,admission_date,coaching_type,hostel_dayscholar,student_name,ph_no1,ph_no2,gender,dob,father_name,father_ph_no,mother_name,mother_ph_no,admission_opted_for,ac_nonac,blood_group,age,aadhar_card_no,nationality,religion,community,caste,student_whatsapp_no">Personal Details</option>

                                <option value="id,door_no,street_name,city,state,pincode,parent_whatsapp_no,email">Address Details</option>

                                <option value="id,father_qualification,father_occupation,father_designation,fathers_place_of_work,mother_qualification,mother_occupation,mother_annual_income,mother_designation,mother_place_of_work">Parent Details</option>

                                <option value="id,board_of_study_X_std,school_name_X_std,district_name_school_X_std,total_marks_X_std,board_of_study_XII_std,school_name_XII_std,district_name_school_XII_std,total_marks_XII_std">Academic Details</option>

                                <option value="id,S1,S1_max_marks,S1_obtained_mark,S2,S2_max_marks,S2_obtained_mark,S3,S3_max_marks,S3_obtained_mark,S4,S4_max_marks,S4_obtained_mark,total_marks">Mark Details</option>

                                <option value="id,total_attempts_neet,neet_score_2023,neet_score_2024,repeater_re_repeater,previous_course_studied_neet,institution_studied_name,institution_bill_type">Neet Details</option>

                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <button type="submit" class="btn btn-primary mt-4">Export</button>
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
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
{{-- <script>
  const table = $('#myTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv',
    ],
  });

</script> --}}
@endsection