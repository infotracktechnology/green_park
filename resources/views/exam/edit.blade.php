@extends('layouts.app')
@section('title', 'Examinations')
@section('css')
<style>
  .photo-item:hover {
     transform: scale(1.05);
   }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css" />
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('exam.update', $exam->id) }}" enctype="multipart/form-data">
              @method('PUT')
              @csrf
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Edit Test</h6>
                  </div>


                  <div class="form-group col-lg-3">
                    <label for="branch">Select Academic Year:</label>
                    <select name="academic_year" id="academic_year" class="form-control form-control-sm">
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" @selected($row->academic_year == $exam->academic_year)>{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($exam->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($exam->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==$exam->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$exam->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($type as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $exam->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $exam->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $exam->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($exam->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($exam->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($exam->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                      <option value="">Select Section</option>
                      <option value="{{ implode(',', $section)}}" @selected(implode(',', $section)==$exam->section)>All</option>
                      @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row==$exam->section)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                      @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k==$exam->students)>{{$k}} - {{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Test Category</label>
                    <select name="testcategory" class="form-control form-control-sm" required>
                      <option value="">Select Test Category</option>
                      @foreach ($testcategory as $row)
                      <option value="{{$row}}" @selected($row==$exam->testcategory)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Test ID <span class="text-danger">(should be unique*)</span></label>
                    <input type="number" name="id" value="{{ $exam->testid }}" id="id" class="form-control form-control-sm numberk" disabled>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Test Name</label>
                    <input type="text" name="name" value="{{ $exam->name }}" id="name" class="form-control form-control-sm" readonly>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Exam Date</label>
                    <input type="date" value="{{ $exam->exam_date }}" name="exam_date" class="form-control form-control-sm">
                  </div>


                  <div class="form-group col-lg-4">
                    <label>Start Datetime</label>
                    <input type="text" id="start_at" value="{{ $exam->start_at }}" name="start_at" class="datetime-picker form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-4">
                    <label>End Datetime</label>
                    <input type="text" id="end_at" value="{{ $exam->end_at }}" name="end_at" class="datetime-picker form-control form-control-sm">
                    <div id="end_at_error" class="text-danger"></div>
                  </div>


                  <div class="form-group col-lg-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>

                </div>
              </div>
            </form>
          </div>

          <div class="card card-primary">
            <div class="card-header">
              <h4>Questions Replace Form</h4>
            </div>
            <div class="card-body">
               <button type="button" class="btn btn-primary m-b-10" data-toggle="modal" data-target="#replaceModal">Add Replace Images</button>
              <div class="row">
               

                @foreach ($exam->questions as $row)
                <div class="col-lg-1 photo-item">
                  <div class="card">
                    <img src="{{ env('APP_URL').$row['image'] }}" class="card-img-top" alt="Photo" style="height: 100px; object-fit: cover;">
                    <div class="card-footer p-2 text-center">
                      <small class="text-muted d-block">{{ basename($row['image']) }}</small>
                      <a href="{{ env('APP_URL').$row['image'] }}" download class="btn btn-sm btn-primary"> Download</a>
                    </div>
                  </div>
                </div>
                @endforeach

                </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <div class="modal fade" id="replaceModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Replace Images</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('exam.index') }}" method="get" enctype="multipart/form-data">
          <div class="row">
            <div class="form-group col-12">
              <label>Images</label>
              <input type="hidden" name="id" value="{{ $exam->id }}">
              <input type="file" name="images[]" accept="image/*" multiple class="form-control form-control-sm" required>
            </div>

            <div class="form-group col-12">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
@section('js')
<script>
  flatpickr(".datetime-picker", {
     enableTime: true,
     allowInput: true,
     dateFormat: "Y-m-d H:i",
     plugins: [
         new confirmDatePlugin({
             confirmText: "OK",
             showAlways: false,
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