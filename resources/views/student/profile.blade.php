@extends('layouts.dashboard')

@section('title', 'Profile')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
  .details-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
  }
  .details-table td {
    padding: 5px !important;
    border: none !important;
  }
  .details-label {
    font-weight: bold;
    color: #333;
    width: 30%;
  }
  .details-value {
    color: #555;
  }
  .section-title {
    margin: 20px 0 10px;
    font-size: 1.2em;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    {{-- <marquee behavior="scroll" direction="left" style="background-color: #e61515cb; padding: 10px; color: white; font-weight: bold;">****** Today is {{ date('l, F jS') }} and your upcoming class is :CHEMISTRY *****</marquee> --}}
    <div class="row">
      <div class="col-md-6">
        <div class="card author-box">
          <div class="card-body">
            <div class="author-box-center">

              <img alt="image" src="{{ asset("profilepic/".auth()->user()->student_id.".jpg") }}" onerror="this.style.display='none'" class="rounded-circle author-box-picture">
              
              <div class="clearfix"></div>
              <div class="author-box-job">
                <h4 style="color: #2196f3;">{{ auth()->user()->student_name }}</h4>
              </div>
              <div class="author-box-job">
                <h4>{{ auth()->user()->dob }}</h4>
                <h4>{{ auth()->user()->gender }}</h4>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="col-md-6">
        <div class="card  author-box">
          <div class="card-body">
            <div class="author-box-center">
              <div class="clearfix"></div>
              <div style="font-size: 13px" class="author-box-job">
                <p style="margin-bottom: 0px;"><strong style="color: #2196f3;">Mobile :</strong> {{ auth()->user()->ph_no1 }}</p>
                <p style="margin-bottom: 0px;"><strong style="color: #2196f3;">Email :</strong> {{ auth()->user()->email }}</p>
                <p style="color: #2196f3;padding-top: 0px;margin-bottom: 0px;"><strong>Address :</strong></p>
                <ul style="list-style: none; padding-left: 0;margin-bottom: 0px;">
                  <li>{{ auth()->user()->door_no }}, {{ auth()->user()->street_name }}, {{ auth()->user()->city }}, {{ auth()->user()->state }} - {{ auth()->user()->pincode }}</li>
                </ul>

              </div>

            </div>


          </div>

        </div>
      </div>



    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h6 class="col-blue">Personal Details</h4>
            <div class="row">
              <div class="col-lg-6">
                <table class="details-table">
                  <tr>
                    <td class="details-label">Father Name</td>
                    <td class="details-value">: {{ auth()->user()->father_name }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">Father Phone</td>
                    <td class="details-value">: {{ auth()->user()->father_ph_no }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">Mother Name</td>
                    <td class="details-value">: {{ auth()->user()->mother_name }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">Mother Phone</td>
                    <td class="details-value">: {{ auth()->user()->mother_ph_no }}</td>
                  </tr>
                </table>
              </div>

              <div class="col-lg-6">
                <h6 class="col-blue">Academic Details</h6>
                <table class="details-table">
                  <tr>
                    <td class="details-label">Coaching Type</td>
                    <td class="details-value">: {{ auth()->user()->coaching_type }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">Board of Study in XII</td>
                    <td class="details-value">: {{ auth()->user()->board_of_study_XII_std }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">XII - Physics Marks</td>
                    <td class="details-value">: {{ auth()->user()->S2_obtained_mark }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">XII - Chemistry Mark</td>
                    <td class="details-value">: {{ auth()->user()->S1_obtained_mark }}</td>
                  </tr>
                  <tr>
                    <td class="details-label">XII - Biology Mark</td>
                    <td class="details-value">: {{ auth()->user()->S3_obtained_mark }}</td>
                  </tr>
                </table>
                <p>
                <h6 class="col-blue">Hostel Type :</h6> {{ auth()->user()->hostel_dayscholar }} - {{ auth()->user()->ac_nonac }}
                </p>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection