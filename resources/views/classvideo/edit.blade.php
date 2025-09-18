@extends('layouts.app')
@section('title', 'Edit Class Video')


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
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
          @endif

          @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissible show fade">{{ session('error') }}</div>
          @endif

          <div class="card card-primary" x-data="app">
            <form method="post" id="myForm" action="{{ route('classvideo.update', $classvideo->id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Edit Class Video</h6>
                  </div>



                  <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>

                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" {{ $classvideo->academic_year == $row->academic_year ? 'selected' : '' }}>{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($classvideo->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($classvideo->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==$classvideo->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$classvideo->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $classvideo->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $classvideo->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $classvideo->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($classvideo->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($classvideo->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($classvideo->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                      <option value="">Select Section</option>
                      <option value="{{ implode(',', $section)}}" @selected(implode(',', $section)==$classvideo->section)>All</option>
                      @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row==$classvideo->section)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                      @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k==$classvideo->students)>{{$k}} - {{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Subject</label>
                    <select name="subject" class="form-control form-control-sm" required>
                      <option value="">Select Subject</option>
                      <option value="physics" {{ $classvideo->subject == 'physics' ? 'selected' : '' }}>Physics</option>
                      <option value="chemistry" {{ $classvideo->subject == 'chemistry' ? 'selected' : '' }}>Chemistry</option>
                      <option value="zoology" {{ $classvideo->subject == 'zoology' ? 'selected' : '' }}>Zoology</option>
                      <option value="botany" {{ $classvideo->subject == 'botany' ? 'selected' : '' }}>Botany</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Chapter</label>
                    <input type="text" name="chapter" class="form-control form-control-sm" value="{{ $classvideo->chapter }}" required>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Period</label>
                    <select name="period" class="form-control form-control-sm" required>
                      <option value="">Select Period</option>
                      @for($i = 1; $i <= 6; $i++) <option value="{{ $i }}" {{ $classvideo->period == $i ? 'selected' : '' }}>Period {{ $i }}</option>
                        @endfor
                    </select>
                  </div>

                  <div class="form-group col-lg-4">
                    <label for="video_id">Video ID</label>
                    <input type="text" name="video_id" class="form-control form-control-sm" value="{{ $classvideo->video_id }}" required>
                  </div>



                  <div class="form-group col-lg-3">
                    <label>Start Datetime</label>
                    <input type="text" id="start_at" name="start_at" class="datetime-picker form-control form-control-sm" value="{{ $classvideo->start_at }}" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>End Datetime</label>
                    <input type="text" id="end_at" name="end_at" class="datetime-picker form-control form-control-sm" value="{{ $classvideo->end_at }}" required>
                    <div id="end_at_error" class="text-danger"></div>
                  </div>

                  <div class="form-group col-lg-12">
                    <button type="submit" class="btn btn-primary">Update Video</button>
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