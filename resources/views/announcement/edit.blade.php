@extends('layouts.app')
@section('title', 'announcement')

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
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($announcement->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($announcement->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
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
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$announcement->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $announcement->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  
                   <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $announcement->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $announcement->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  
                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($announcement->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($announcement->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($announcement->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                   <div class="form-group col-lg-2">
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
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                    @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k == $announcement->students)>{{$k}} - {{$row}}</option>
                    @endforeach
                    </select>
                  </div>


                            
                            <div class="form-group col-lg-4">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" required value="{{ $announcement->title }}">
                            </div>

                            
                            <div class="form-group col-lg-3">
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


