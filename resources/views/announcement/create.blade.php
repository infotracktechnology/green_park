@extends('layouts.app')

@section('title', ' Announcement')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/summernote/summernote-bs4.css')}}">
@endsection
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('announcement.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Add Announcement</h6>
                              </div>

                              <div class="form-group col-lg-4">
                                <label for="branch_id">Branch</label>
                                <select name="branch" class="form-control form-control-sm"  required>
                                    <option value="" disabled selected>Select Campus</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
    


                              <div class="form-group col-lg-4">
                                 <label>Coaching Type</label>
                                 <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required>
                                    <option value="">Select Coaching Type</option>
                                    <option value="Offline">Offline</option>
                                    <option value="Online Recorded">Online Recorded</option>
                                    <option value="Online Live">Online Live</option>
                                 </select>
                              </div>

                              <div class="form-group col-lg-4">
                                <label>Gender</label>
                                <select name="gender" class="form-control form-control-sm" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Category</label>
                                <select name="category" class="form-control form-control-sm">
                                    <option value="">Select Category</option>
                                    <option value="Important">Important</option>
                                    <option value="Normal">Normal</option>
                                </select>
                              </div>

                              <div class="form-group col-lg-4">
                                <label>Type</label>
                                <select name="type" class="form-control form-control-sm" required>
                                    <option value="">Select Type</option>
                                    <option value="General">General</option>
                                    <option value="Chairman Message">Chairman Message</option>
                                </select>
                              </div>

                              <div class="form-group col-lg-4">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" required>
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="content">Content</label>
                                <textarea name="content" id="content" class="summernote-simple"></textarea>
                            </div>
                            
                             

                              <div class="form-group col-lg-12">
                                 <button type="submit" class="btn btn-primary">Submit</button>
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
<script src="{{asset('bundles/summernote/summernote-bs4.js')}}"></script>
@endsection
