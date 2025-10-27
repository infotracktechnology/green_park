@extends('layouts.app')

@section('title', 'Edit Chairman Video')
@section('css')
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary" x-data="app">
            <form method="post" id="myForm" action="{{ route('chairmanvideo.update', $chairmanvideo->id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Edit Video</h6>
                  </div>


                  <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class="form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" @selected($row->academic_year == $chairmanvideo->academic_year)>
                        {{ $row->academic_year }}
                      </option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($chairmanvideo->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($chairmanvideo->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==$chairmanvideo->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$chairmanvideo->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $chairmanvideo->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $chairmanvideo->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $chairmanvideo->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($chairmanvideo->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($chairmanvideo->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($chairmanvideo->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                      <option value="">Select Section</option>
                      <option value="{{ implode(',', $section)}}" @selected(implode(',', $section)==$chairmanvideo->section)>All</option>
                      @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row==$chairmanvideo->section)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                      @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k==$chairmanvideo->students)>{{$k}} - {{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control form-control-sm" value="{{ $chairmanvideo->title }}" required>
                  </div>
                  <div class="form-group col-lg-4">
                    <label for="video_id">Video ID</label>
                    <input type="number" name="video_id" id="video_id" class="form-control form-control-sm" value="{{ $chairmanvideo->video_id }}" required>
                  </div>

                  {{-- <div class="form-group col-lg-4">
                                <label for="attachment">Attachment</label>
                                <input type="file" name="attachment" id="attachment" class="form-control form-control-sm">
                                <div class="mt-2">
                                @if(isset($chairmanvideo->attachment))
                                    <a href="/public/{{ $chairmanvideo->attachment }}" target="_blank" rel="noopener noreferrer">
                  <i class="fas fa-paperclip"></i> Attachment
                  </a>
                  @endif
                </div>
              </div> --}}

              <div class="form-group col-lg-12">
                <button type="submit" class="btn btn-primary">Update</button>
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