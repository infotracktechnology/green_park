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
                                <h4>{{ auth()->user()->student_name }}</h4>

                            </div>
                            <div class="author-box-job">
                                <h4>{{ auth()->user()->dob }}</h4>
                                <h4>{{ auth()->user()->gender }}</h4>
                            </div>
                           
                    </div>
                    <a href="#" class="btn btn-social-icon mr-1 btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                      </a>
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
                                    
                                    <div class="col-md-3 col-6 b-r">
                                      <strong>Mobile</strong>
                                      <br>
                                      <p class="text-muted"> {{ auth()->user()->ph_no1 }}</p>
                                    </div>
                                    <div class="col-md-4 col-6 b-r">
                                      <strong>Email</strong>
                                      <br>
                                      <p class="text-muted">johndeo@example.com</p>
                                    </div>
                                    <div class="col-md-3 col-6">
                                      <strong>Address</strong>
                                      <br>
                                      <p class="text-muted"> {{ auth()->user()->city }}</p>
                                    </div>
                                  </div>
                                  <div class="section-title"><h5>Personal Details</h5></div>
                                  <ul>
                                   <li>Father Name :{{ auth()->user()->father_name }}</li>
                                <li>Father Phone :{{ auth()->user()->father_ph_no }}</li>
                                    <li>Mother Name :{{ auth()->user()->mother_name }}</li>
                                         <li>Mother Phone :{{ auth()->user()->mother_ph_no }}</li>
                                  </ul>
                                  <div class="section-title"><h5>Academic Details</h5></div>
                                  <ul>
                                <li>Coaching Type :{{ auth()->user()->coaching_type }}</li>
                                <li>Board of Study in XII :{{ auth()->user()->board_of_study_XII_std }}</li>
                                <li>XII - Physics Marks :{{ auth()->user()->S2_obtained_mark }}</li>
                                 <li>XII - Chemistry Mark :{{auth()->user()->S1_obtained_mark}}	</li>
                                 <li>XII - Biology Mark :{{auth()->user()->S3_obtained_mark}}</li>
                                  </ul>
                                </div>
                                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="profile-tab2">
                                  <form method="post" class="needs-validation">
                                    <div class="card-header">
                                      <h4>Edit Profile</h4>
                                    </div>
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="form-group col-md-6 col-12">
                                          <label>First Name</label>
                                          <input type="text" class="form-control" value="John">
                                          <div class="invalid-feedback">
                                            Please fill in the first name
                                          </div>
                                        </div>
                                        <div class="form-group col-md-6 col-12">
                                          <label>Last Name</label>
                                          <input type="text" class="form-control" value="Deo">
                                          <div class="invalid-feedback">
                                            Please fill in the last name
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="form-group col-md-7 col-12" style="position: relative;">
                                          <label>Email</label>
                                          <input type="email" class="form-control" value="test@example.com">
                                          <div class="invalid-feedback">
                                            Please fill in the email
                                          </div>
                                        <div data-v-238851cb=""></div></div>
                                        <div class="form-group col-md-5 col-12">
                                          <label>Phone</label>
                                          <input type="tel" class="form-control" value="">
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="form-group col-12">
                                          <label>Bio</label>
                                          <textarea class="form-control summernote-simple" style="display: none;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur voluptatum alias molestias minus quod dignissimos.</textarea><div class="note-editor note-frame card"><div class="note-dropzone">  <div class="note-dropzone-message"></div></div><div class="note-toolbar-wrapper" style="height: 0px;"><div class="note-toolbar card-header" style="position: relative; top: 0px; width: 100%;"><div class="note-btn-group btn-group note-style"><button type="button" class="note-btn btn btn-light btn-sm note-btn-bold" tabindex="-1" aria-label="Bold (CTRL+B)" data-bs-original-title="Bold (CTRL+B)"><i class="note-icon-bold"></i></button><button type="button" class="note-btn btn btn-light btn-sm note-btn-italic" tabindex="-1" aria-label="Italic (CTRL+I)" data-bs-original-title="Italic (CTRL+I)"><i class="note-icon-italic"></i></button><button type="button" class="note-btn btn btn-light btn-sm note-btn-underline" tabindex="-1" aria-label="Underline (CTRL+U)" data-bs-original-title="Underline (CTRL+U)"><i class="note-icon-underline"></i></button><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Remove Font Style (CTRL+\)" data-bs-original-title="Remove Font Style (CTRL+\)"><i class="note-icon-eraser"></i></button></div><div class="note-btn-group btn-group note-font"><button type="button" class="note-btn btn btn-light btn-sm note-btn-strikethrough" tabindex="-1" aria-label="Strikethrough (CTRL+SHIFT+S)" data-bs-original-title="Strikethrough (CTRL+SHIFT+S)"><i class="note-icon-strikethrough"></i></button></div><div class="note-btn-group btn-group note-para"><div class="note-btn-group btn-group"><button type="button" class="note-btn btn btn-light btn-sm dropdown-toggle" tabindex="-1" data-toggle="dropdown" aria-label="Paragraph" data-bs-original-title="Paragraph"><i class="note-icon-align-left"></i></button><div class="dropdown-menu"><div class="note-btn-group btn-group note-align"><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Align left (CTRL+SHIFT+L)" data-bs-original-title="Align left (CTRL+SHIFT+L)"><i class="note-icon-align-left"></i></button><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Align center (CTRL+SHIFT+E)" data-bs-original-title="Align center (CTRL+SHIFT+E)"><i class="note-icon-align-center"></i></button><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Align right (CTRL+SHIFT+R)" data-bs-original-title="Align right (CTRL+SHIFT+R)"><i class="note-icon-align-right"></i></button><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Justify full (CTRL+SHIFT+J)" data-bs-original-title="Justify full (CTRL+SHIFT+J)"><i class="note-icon-align-justify"></i></button></div><div class="note-btn-group btn-group note-list"><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Outdent (CTRL+[)" data-bs-original-title="Outdent (CTRL+[)"><i class="note-icon-align-outdent"></i></button><button type="button" class="note-btn btn btn-light btn-sm" tabindex="-1" aria-label="Indent (CTRL+])" data-bs-original-title="Indent (CTRL+])"><i class="note-icon-align-indent"></i></button></div></div></div></div></div></div><div class="note-editing-area"><div class="note-handle"><div class="note-control-selection"><div class="note-control-selection-bg"></div><div class="note-control-holder note-control-nw"></div><div class="note-control-holder note-control-ne"></div><div class="note-control-holder note-control-sw"></div><div class="note-control-sizing note-control-se"></div><div class="note-control-selection-info"></div></div></div><textarea class="note-codable"></textarea><div class="note-editable card-block" contenteditable="true" style="min-height: 150px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur voluptatum alias molestias minus quod dignissimos.</div></div><div class="note-statusbar">  <div class="note-resizebar">    <div class="note-icon-bar">    <div class="note-icon-bar">    <div class="note-icon-bar">  </div></div></div></div></div></div>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="form-group mb-0 col-12">
                                          <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="remember" class="custom-control-input" id="newsletter">
                                            <label class="custom-control-label" for="newsletter">Subscribe to newsletter</label>
                                            <div class="text-muted form-text">
                                              You will get new information about products, offers and promotions
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="card-footer text-end">
                                      <button class="btn btn-primary">Save Changes</button>
                                    </div>
                                  </form>
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

