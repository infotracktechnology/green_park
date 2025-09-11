@extends('layouts.app')
@section('title', 'announcement')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/summernote/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{asset('bundles/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{route('announcement.update', $announcement->id)}}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="col-deep-purple">Update Announcement</h6>
                            </div>

                            <div class="form-group col-lg-3">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class="form-control form-control-sm" required>
                                    @foreach ($academicyear as $row)
                                    <option value="{{ $row->academic_year }}" @selected($row->academic_year == $announcement->academic_year)>
                                        {{ $row->academic_year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                    <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row == $announcement->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch" id="branch" class="form-control form-control-sm" required>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == $announcement->branch)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required>
                      <option value="">Select Coaching Type</option>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected($row == $announcement->coaching_type)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  
                   <div class="form-group col-lg-4">
                    <label>H/D</label>
                    <select name="category" id="category" class="form-control form-control-sm">
                      <option value="">Select H/D</option>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected($row == $announcement->category)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Batch</label>
                    <select name="batch" id="batch" class="form-control form-control-sm">
                      <option value="">Select Batch</option>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected($row == $announcement->batch)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                   <div class="form-group col-lg-4">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                    <option value="">Select Section</option>
                    <option value="{{ implode(',', $section)}}" @selected(implode(',', $section) == $announcement->section)>All</option>
                    @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row == $announcement->section)>{{$row}}</option>
                    @endforeach
                    </select>
                  </div>
                

                  <div class="form-group col-lg-4">
                    <label>Gender</label>
                    <select name="gender" class="form-control form-control-sm" required>
                      <option value="All" @selected($announcement->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($announcement->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($announcement->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                            
                            <div class="form-group col-lg-4">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" required value="{{ $announcement->title }}">
                            </div>

                            
                            <div class="form-group col-lg-4">
                                <label for="attachment">Attachment</label>
                                <input type="file" name="attachment" id="attachment" class="form-control form-control-sm">
                                @if (!empty($announcement->attachment))
                                    <p class="mt-2">Current File: {{ basename($announcement->attachment) }}</p>
                                @endif
                            </div>


                            <div class="form-group col-lg-12">
                                <label for="content">Content</label>
                                <textarea name="content" id="content" class="summernote-simple">{{  $announcement->content }}</textarea>
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
<script src="{{asset('bundles/select2/dist/js/select2.full.min.js')}}"></script>
<script>
</script>
@endsection
