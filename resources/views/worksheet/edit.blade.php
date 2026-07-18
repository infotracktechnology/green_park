@extends('layouts.app')
@section('title', 'Worksheet')
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
            <form method="post" action="{{ route('worksheet.update', $worksheet->id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Edit Worksheet</h6>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" {{ $worksheet->academic_year == $row->academic_year ? 'selected' : '' }}>{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($worksheet->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($worksheet->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==$worksheet->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$worksheet->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $worksheet->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $worksheet->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $worksheet->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($worksheet->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($worksheet->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($worksheet->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                      <option value="">Select Section</option>
                      <option value="{{ implode(',', $section)}}" @selected(implode(',', $section)==$worksheet->section)>All</option>
                      @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row==$worksheet->section)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                      @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k==$worksheet->students)>{{$k}} - {{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control form-control-sm" required value="{{ $worksheet->title }}">
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Attachment <span class="text-danger">(Only PDF files, max size: 2MB*)</span></label>

                    <input type="file" name="file" id="fileInput" class="form-control form-control-sm">
                    <small id="fileName">
                      @if($worksheet->file_path)
                      {{ basename($worksheet->file_path) }}
                      @endif
                    </small>
                  </div>

                            <div class="form-group col-lg-12">
                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox" name="is_schedule" class="custom-control-input" id="is_schedule" value="1" @checked($worksheet->is_schedule)>
                                  <label class="custom-control-label" for="is_schedule">Is Schedule</label>
                                </div>
                            </div>

                            <div class="col-lg-12 row" id="schedule_fields" style="{{ $worksheet->is_schedule ? '' : 'display: none;' }}">
                                <div class="form-group col-lg-3">
                                    <label>Start Datetime</label>
                                    <input type="text" id="start_at" name="start_at" class="datetime-picker form-control form-control-sm" value="{{ $worksheet->start_at }}" {{ $worksheet->is_schedule ? 'required' : '' }}>
                                </div>   
                            </div>

                  <div class="form-group col-lg-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>

            </form>


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
  document.getElementById('fileInput').addEventListener('change', function() {
      let fileName = this.files[0] ? this.files[0].name : "{{ basename($worksheet->file_path) }}";
      document.getElementById('fileName').innerText = fileName;
  });

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
