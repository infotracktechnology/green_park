@extends('layouts.dashboard')

@section('title', 'Student')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <div class="section-body">
        <marquee behavior="scroll" direction="left" style="background-color: #e61515cb; padding: 10px; color: white; font-weight: bold;">****** Today is {{ date('l, F jS') }} and your upcoming class is :CHEMISTRY *****</marquee>
        <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                    <div class="card-body">
                        <div class="author-box-center">
                            <img alt="image" src="{{ asset('img/avather.png') }}" class="rounded-circle author-box-picture">
                            <div class="clearfix"></div>
                            <div class="author-box-job">
                                <h4 style="color: #2196f3;">{{ auth()->user()->student_name }}</h4>

                            </div>
                            <div class="author-box-job">
                                <h4 >{{ auth()->user()->dob }}</h4>
                                <h4 >{{ auth()->user()->gender }}</h4>
                            </div>
                           
                    </div>
                    <div class="text-center">
                    <a href="#" class="btn btn-primary mr-1 btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                      </a>
                      <a href="#" class="btn col-white mr-1 btn-info">
                        <i class="fab fa-twitter"></i>
                      </a>
                      <a href="#" class="btn col-white mr-1" style="background-color: rgb(247, 0, 185);">
                        <i class="fab fa-instagram"></i>
                      </a>
                    </div>
                </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <div class="card">
                            <div class="padding-20">
                              <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                
                                
                              </ul>
                              <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="home-tab2">
                                  <div class="row">
                                    
                                    <div style="color: #2196f3;" class="col-md-3 col-6 b-r">
                                      <strong>Mobile</strong>
                                      <br>
                                      <p class="text-muted"> {{ auth()->user()->ph_no1 }}</p>
                                    </div>
                                    <div style="color: #2196f3;" class="col-md-4 col-6 b-r">
                                      <strong>Email</strong>
                                      <br>
                                      <p class="text-muted">johndeo@example.com</p>
                                    </div>
                                    <div style="color: #2196f3;" class="col-md-3 col-6">
                                      <strong>Address</strong>
                                      <br>
                                      <p class="text-muted"> {{ auth()->user()->city }}</p>
                                    </div>
                                  </div>
                                  <div class="section-title"><h5 style="color: #2196f3;">Personal Details</h5></div>
                                  <ul>
                                    <li>Father Name : &nbsp; &nbsp;{{ auth()->user()->father_name }}</li>
                                    <li>Father Phone : &nbsp; &nbsp;{{ auth()->user()->father_ph_no }}</li>
                                    <li>Mother Name :  &nbsp; &nbsp;{{ auth()->user()->mother_name }}</li>
                                    <li>Mother Phone : &nbsp; &nbsp;{{ auth()->user()->mother_ph_no }}</li>
                                  </ul>
                                  <div class="section-title"><h5 style="color: #2196f3;">Academic Details</h5></div>
                                  <ul>
                                    <li>Coaching Type : &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; {{ auth()->user()->coaching_type }}</li>
                                    <li>Board of Study in XII :&nbsp; &nbsp; {{ auth()->user()->board_of_study_XII_std }}</li>
                                    <li>XII - Physics Marks : &nbsp; &nbsp;&nbsp; &nbsp; {{ auth()->user()->S2_obtained_mark }}</li>
                                    <li>XII - Chemistry Mark : &nbsp; &nbsp; {{ auth()->user()->S1_obtained_mark }}</li>
                                    <li>XII - Biology Mark : &nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; {{ auth()->user()->S3_obtained_mark }}</li>
                                  </ul>
                                </div>
                                
                              </div>
                            </div>
                          </div>
                         </div>
            </div>
        </div>
    </div>
</div>
@endsection

