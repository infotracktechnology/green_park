@extends('layouts.app')
@section('title', 'Edit NEET Achievement')
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <form id="myForm" action="{{ route('achievement.update', $achievement->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card card-primary">
              <div class="card-header">

                <h6 class="col-deep-purple">Edit NEET Achievement</h6>
              </div>
              <div class="card-body row">

               <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" @selected($achievement->academic_year == $row->academic_year)>{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($achievement->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($achievement->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==$achievement->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$achievement->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $achievement->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $achievement->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $achievement->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($achievement->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($achievement->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($achievement->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                      <option value="">Select Section</option>
                      <option value="{{ implode(',', $section)}}" @selected(implode(',', $section)==$achievement->section)>All</option>
                      @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row==$achievement->section)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                      @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k==$achievement->students)>{{$k}} - {{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                <div class="form-group col-lg-4">
                  <label>Category</label>
                  <select name="filecategory[]" id="filecategory" class="select2 form-control form-control-sm" multiple required>
                    @foreach (['Video', 'Image', 'pdf', 'Link'] as $row)
                    <option value="{{ $row }}" @selected(in_array($row, explode(',', $achievement->filecategory)))>{{ $row }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Video Input -->
                <div class="form-group col-lg-4" id="video-input" style="{{ !in_array('Video', explode(',', $achievement->filecategory)) ? 'display: none;' : '' }}">
                  <label for="video">Upload Video <span class="text-danger">(max size: 40MB*)</span></label>
                  <input type="file" name="video" id="video" class="form-control form-control-sm" accept="video/*">
                  <small class="text-muted d-block mt-1">Current: {{ basename($achievement->video) }}</small>
                </div>

                <!-- Image Input -->
                <div class="form-group col-lg-4" id="image-input" style="{{ !in_array('Image', explode(',', $achievement->filecategory)) ? 'display: none;' : '' }}">
                  <label for="images">Upload Images <span class="text-danger">(max size: 2MB*)</span></label>
                  <input type="file" name="images[]" id="images" class="form-control form-control-sm" accept="image/*" multiple>
                  <div class="mt-2 text-muted">
                    @foreach ($achievement->images as $img)
                    <div>Image: {{ basename($img) }}</div>
                    @endforeach
                  </div>
                </div>

                <!-- PDF Input -->
                <div class="form-group col-lg-4" id="pdf-input" style="{{ !in_array('pdf', explode(',', $achievement->filecategory)) ? 'display: none;' : '' }}">
                  <label for="pdf">Upload PDF <span class="text-danger">(max size: 3MB*)</span></label>
                  <input type="file" name="pdf" id="pdf" class="form-control form-control-sm" accept="application/pdf">
                  <small class="text-muted d-block mt-1">Current: {{ basename($achievement->pdf) }}</small>
                </div>

                <!-- Link Input -->
                <div class="form-group col-lg-4" id="link-input" style="{{ !in_array('Link', explode(',', $achievement->filecategory)) ? 'display: none;' : '' }}">
                  <label for="link">Enter Link</label>
                  <input type="url" name="link" id="link" class="form-control form-control-sm" value="{{ $achievement->link }}">
                </div>





                <!-- Content -->
                <div class="form-group col-lg-12">
                  <label>Content</label>
                  <textarea name="content" class="summernote-simple">{{ $achievement->content }}</textarea>
                </div>

                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script>
  document.getElementById('myForm').addEventListener('submit', function (e) {
    const videoInput = document.getElementById('video');
    if (videoInput.files[0] && videoInput.files[0].size > 40 * 1024 * 1024) { // 40MB
   e.preventDefault();
   alert('The uploaded video exceeds the maximum allowed size of 40MB.');
    }
    });
     
   $('#filecategory').on('change', function() {
       const selectedOptions = $(this).val();
       $('#video-input').toggle(selectedOptions.includes('Video'));
       $('#image-input').toggle(selectedOptions.includes('Image'));
       $('#pdf-input').toggle(selectedOptions.includes('pdf'));
       $('#link-input').toggle(selectedOptions.includes('Link'));
   });
</script>
@endsection