@extends('layouts.app')
@section('title', ' NEET MBBS/BDS Counselling')
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary" x-data="app">
            <form method="post" id="myForm" action="{{ route('achievement.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Add MBBS/BDS Counselling</h6>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP">GROUP</option>
                      <option value="INDIVIDUAL">INDIVIDUAL STUDENT</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All">All Gender</option>
                      <option value="MALE">MALE</option>
                      <option value="FEMALE">FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Category</label>
                    <select name="filecategory[]" id="filecategory" class="select2 form-control form-control-sm" multiple="multiple" required>
                      <option value="Video">Video</option>
                      <option value="Image">Image</option>
                      <option value="pdf">PDF</option>
                      <option value="Link">Link</option>
                    </select>
                  </div>

                  <!-- Video Input -->
                  <div class="form-group col-lg-4" id="video-input" style="display: none;">
                    <label for="video">Upload Video <span class="text-danger">(max size: 40MB*)</span></label>
                    <input type="file" name="video" id="video" class="form-control form-control-sm" accept="video/*">
                  </div>

                  <!-- Image Input -->
                  <div class="form-group col-lg-4" id="image-input" style="display: none;">
                    <label for="images">Upload Images <span class="text-danger">(max size: 2MB*)</span></label>
                    <input type="file" name="images[]" id="images" class="form-control form-control-sm" accept="image/*" multiple>
                  </div>

                  <div class="form-group col-lg-4" id="pdf-input" style="display: none;">
                    <label for="pdf">Upload PDF <span class="text-danger">(max size: 2MB*)</span></label>
                    <input type="file" name="pdf" id="pdf" class="form-control form-control-sm" accept="application/pdf">
                  </div>


                  <!-- Link Input -->
                  <div class="form-group col-lg-4" id="link-input" style="display: none;">
                    <label for="link">Enter Link</label>
                    <input type="url" name="link" id="link" class="form-control form-control-sm">
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