@extends('layouts.app')
@section('title', 'Student Export')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection
<?php
$details = [
    'Personal Details' => 'student_id,admission_date,academic_year,course,coaching_type,hostel_dayscholar,section,batch,student_name,ph_no1,ph_no2,gender,dob,father_name,father_ph_no,mother_name,mother_ph_no,admission_opted_for,ac_nonac,blood_group,age,aadhar_card_no,nationality,religion,community,caste,student_whatsapp_no',
    'Address Details' => 'door_no,street_name,city,state,pincode,parent_whatsapp_no,email',
    'Parent Details' => 'father_qualification,father_occupation,father_designation,fathers_place_of_work,mother_qualification,mother_occupation,mother_annual_income,mother_designation,mother_place_of_work',
    'Academic Details' => 'board_of_study_X_std,school_name_X_std,district_name_school_X_std,total_marks_X_std,board_of_study_XII_std,school_name_XII_std,district_name_school_XII_std,total_marks_XII_std',
    'Mark Details' => 'S1,S1_max_marks,S1_obtained_mark,S2,S2_max_marks,S2_obtained_mark,S3,S3_max_marks,S3_obtained_mark,S4,S4_max_marks,S4_obtained_mark,total_marks'
];
?>
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
     
                        @foreach ($details as $key => $value)
                        <div class="row">
                        <div class="form-group col-lg-12">
                            <h4>{{ $key }}</h4>
                        </div>
                        @foreach(explode(',', $value) as $field)
                        <div class="form-group col-lg-3">
                            <input type="checkbox" name="fields[]" value="{{ $field }}"> {{ $field }}
                        </div>
                        @endforeach
                        </div>
                        @endforeach
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