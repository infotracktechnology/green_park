@extends('layouts.app')
@section('title', 'announcement')
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
                     <form method="post" id="myForm"  action="{{route('announcement.update', $announcement->id)}}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                      <div class="card-body">


                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple">Update Announcement</h6>
                        </div>


                        <div class="form-group col-lg-4">
                            <label>Branch</label>
                        <select name="branch" class="form-control form-control-sm" id="campus-select" required>
                            <option value="" disabled selected>Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @if($branch->id == $announcement->branch) selected @endif>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                        

                        <div class="form-group col-lg-4">
                            <label>Coaching Type</label>
                             <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required >
                                <option value="">Select Coaching Type</option>
                                <option value="Offline" {{ $announcement->coaching_type == 'Offline' ? 'selected' : '' }}>Offline</option>
                                <option value="Online Recorded" {{ $announcement->coaching_type == 'Online Recorded' ? 'selected' : '' }}>Online Recorded</option>
                                <option value="Online Live" {{ $announcement->coaching_type == 'Online Live' ? 'selected' : '' }}>Online Live</option>
                                <option value="Test Series" {{ $announcement->coaching_type == 'Test Series' ? 'selected' : '' }}>Test Series</option>
                                <option value="11" {{ $announcement->coaching_type == '11' ? 'selected' : '' }}>11</option>
                                <option value="12" {{ $announcement->coaching_type == '12' ? 'selected' : '' }}>12</option>
                             </select>
                         </div>
                  
                         <div class="form-group col-lg-4">
                            <label>Gender</label>
                            <select class="form-control form-control-sm" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ $announcement->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $announcement->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $announcement->gender == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>


                        <div class="form-group col-lg-4">
                            <label>Category</label>
                            <select name="category" class="form-control form-control-sm">
                                <option value="">Select Category</option>
                                <option value="Important" {{ $announcement->category == 'Important' ? 'selected' : '' }}>Important</option>
                                <option value="Normal" {{ $announcement->category == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                          </div>

                          <div class="form-group col-lg-4">
                            <label>Type</label>
                            <select name="type" class="form-control form-control-sm" required>
                                <option value="">Select Type</option>
                                <option value="General" {{ $announcement->type == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Chairman Message" {{ $announcement->type == 'Chairman Message' ? 'selected' : '' }}>Chairman Message</option>
                            </select>
                          </div>


                    
                        <div class="form-group col-lg-4">
                            <label>Title</label>
                            <input type="text" class="form-control form-control-sm" name="title" value="{{$announcement->title}}" required>
                        </div>

                        <div class="form-group col-lg-12">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="summernote-simple">{{ $announcement->content }}</textarea>
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

