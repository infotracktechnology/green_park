@extends('layouts.app')
@section('title', 'Holiday')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css" />
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
          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('holiday.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Week of Holiday Details</h6>
                  </div>

                  <div class="form-group col-lg-2">
                    <input type="hidden" name="name" value="Week Of Leave">
                    <input type="hidden" name="type" value="Week Of">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                      {{-- <option value="">Select Academic Year</option> --}}
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label for="branch">Branch</label>

                    <select name="branch_id[]" id="branch_id" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label for="hostel">Hostel / Dayscholar</label>
                    <select name="hostel" id="hostels" onchange="getSection();" class="form-control form-control-sm" required>
                      <option value="">Select Option</option>
                      @foreach (['Hostel','Dayscholar'] as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-2">
                    <label for="gender">Gender</label>
                    <select name="gender" id="genders" onchange="getSection();" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      @foreach (['Male','Female'] as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="section">Section</label>
                    <select name="section[]" class="select2" id="sections" multiple required>
                      <option value="">Select Section</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="date">Date</label>
                    <input type="text" name="holiday_date" class="form-control form-control-sm date-picker" required>
                  </div>



                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                  </div>
                </div>
              </div>
            </form>


          </div>



          <div class="card card-info">
            <form method="post" id="myForm" action="{{ route('holiday.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Holiday Details</h6>
                  </div>


                  <div class="form-group col-lg-2">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                      {{-- <option value="">Select Academic Year</option> --}}
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-3">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="holiday_type">Holiday Type</label>
                    <select name="type" id="type" class="form-control form-control-sm" required>
                      <option value="">Select Holiday Type</option>
                      @foreach(['Public Holiday','Vacation','Events','Other'] as $holiday_type)
                      <option value="{{ $holiday_type }}">{{ $holiday_type }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label for="month">Start Date Time</label>
                    <input type="text" name="start_date" id="start_date" class="form-control form-control-sm datetime-picker" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="month">End Date Time</label>
                    <input type="text" name="end_date" id="end_date" class="form-control form-control-sm datetime-picker" required>
                    <div id="end_date_error" class="text-danger"></div>
                  </div>



                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                  </div>
                </div>
              </div>

            </form>


          </div>

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
     minDate: "today",
     plugins: [
         new confirmDatePlugin({
             confirmText: "OK",
             showAlways: false,
         })
     ]
      });
  
  flatpickr(".date-picker", {
     enableTime: false,
     allowInput: true,
     dateFormat: "Y-m-d",
     minDate: "today",
     plugins: [
         new confirmDatePlugin({
             confirmText: "OK",
             showAlways: false,
         })
     ]
      });
    
    $('#end_date').change(function() {
     $('#end_at_error').text('');
     const startTime = new Date($('#start_date').val());
     const endTime = new Date($(this).val());
     if (startTime >= endTime) {
         $('#end_at_error').text('End time must be greater than start time.');
         $(this).val('');
     }
      });
  
  $('#branch_id').change(function() {
   $('#hostels').val('');
   $('#genders').val('');

  });

  function getSection() {
   $.get(`{{ route('holiday.create') }}?gender=${$('#genders').val()}&branch=${$('#branch_id').val()}&hostel=${$('#hostels').val()}`, function(data) {
     $('#sections').empty();
     $.each(data, function(key, item) {
       $('#sections').append(`<option value="${item.section}">${item.section}</option>`);
     });
   });
}
</script>
@endsection