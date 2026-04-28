@extends('layouts.app') 
@section('title', ' Announcement') 

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
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

                  

                  <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class="form-control form-control-sm" required>
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
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control form-control-sm" required />
                  </div>

                  <div class="form-group col-lg-3">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment" class="form-control form-control-sm">             
                  </div>

                  <div class="form-group col-lg-12">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="is_schedule" class="custom-control-input" id="is_schedule" value="1">
                      <label class="custom-control-label" for="is_schedule">Is Schedule</label>
                    </div>
                  </div>

                  <div class="col-lg-12 row" id="schedule_fields" style="display: none;">
                    <div class="form-group col-lg-3">
                        <label>Start Datetime</label>
                        <input type="text" id="start_at" name="start_at" class="datetime-picker form-control form-control-sm">
                    </div>

                    <div class="form-group col-lg-3">
                        <label>End Datetime</label>
                        <input type="text" id="end_at" name="end_at" class="datetime-picker form-control form-control-sm">
                        <div id="end_at_error" class="text-danger"></div>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
  flatpickr(".datetime-picker", {
      enableTime: true,
      allowInput: true,
      dateFormat: "Y-m-d H:i",
      plugins: [
          new confirmDatePlugin({
              confirmText: "OK",
              showAlways: false,
              theme: "light"
          })
      ]
  });

  $('#is_schedule').change(function() {
      if ($(this).is(':checked')) {
          $('#schedule_fields').show();
          $('#start_at').attr('required', true);
          $('#end_at').attr('required', true);
      } else {
          $('#schedule_fields').hide();
          $('#start_at').attr('required', false);
          $('#end_at').attr('required', false);
      }
  });
  
  $('#end_at').change(function() {
      $('#end_at_error').text('');
      const startTime = new Date($('#start_at').val());
      const endTime = new Date($(this).val());
      if (startTime >= endTime) {
          $('#end_at_error').text('End time must be greater than start time.');
          $(this).val('');
      }
  });
</script>
@endsection
