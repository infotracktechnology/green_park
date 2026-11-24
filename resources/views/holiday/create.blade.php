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

          {{-- Form 1: Week Of Holiday --}}
          <div class="card card-primary">
            <form method="post" id="weekForm" action="{{ route('holiday.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Week of Holiday Details</h6>
                  </div>

                  <div class="form-group col-lg-2">
                    <input type="hidden" name="name" value="Week Of Leave">
                    <input type="hidden" name="type" value="Week Of">
                    <label for="week_academic_year">Academic Year</label>
                    <select name="academic_year" id="week_academic_year" class="form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="week_branch_id">Branch</label>
                    <select name="branch_id[]" id="week_branch_id" class="form-control form-control-sm select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="week_hostels">Hostel / Dayscholar</label>
                    <select name="hostel[]" id="week_hostels" class="form-control form-control-sm select2" multiple required>
                      @foreach (['Hostel','Dayscholar'] as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="week_genders">Gender</label>
                    <select name="gender[]" id="week_genders" class="form-control form-control-sm select2" multiple required>
                      @foreach (['Male','Female'] as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="week_sections">Section</label>
                    <select name="section[]" id="week_sections" class="form-control form-control-sm select2" multiple required>
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="week_start_date">Start Date Time</label>
                    <input type="text" name="start_date" id="week_start_date" class="form-control form-control-sm datetime-picker" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="week_end_date">End Date Time</label>
                    <input type="text" name="end_date" id="week_end_date" class="form-control form-control-sm datetime-picker" required>
                    <div class="text-danger end-date-error"></div>
                  </div>

                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>

          {{-- Form 2: Normal Holiday --}}
          <div class="card card-info">
            <form method="post" id="holidayForm" action="{{ route('holiday.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Holiday Details</h6>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="holiday_academic_year">Academic Year</label>
                    <select name="academic_year" id="holiday_academic_year" class="form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="holiday_branch_id">Branch</label>
                    <select name="branch_id[]" id="holiday_branch_id" class="form-control form-control-sm select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="holiday_hostels">Hostel / Dayscholar</label>
                    <select name="hostel[]" id="holiday_hostels" class="form-control form-control-sm select2" multiple required>
                      @foreach (['Hostel','Dayscholar'] as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="holiday_name">Name</label>
                    <input type="text" name="name" id="holiday_name" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="holiday_type">Holiday Type</label>
                    <input type="text" name="type" id="holiday_type" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="holiday_start_date">Start Date Time</label>
                    <input type="text" name="start_date" id="holiday_start_date" class="form-control form-control-sm datetime-picker" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="holiday_end_date">End Date Time</label>
                    <input type="text" name="end_date" id="holiday_end_date" class="form-control form-control-sm datetime-picker" required>
                    <div class="text-danger end-date-error"></div>
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
      })
    ]
  });

  flatpickr(".date-picker", {
    enableTime: false,
    allowInput: true,
    dateFormat: "Y-m-d",
    plugins: [
      new confirmDatePlugin({
        confirmText: "OK",
        showAlways: false,
      })
    ]
  });

  $('form').each(function () {
    const form = $(this);
    const startInput = form.find('[name="start_date"]');
    const endInput = form.find('[name="end_date"]');
    const errorBox = form.find('.end-date-error');

    // Date validation per form
    startInput.add(endInput).on('change', function () {
      errorBox.text('');
      const startVal = startInput.val();
      const endVal = endInput.val();

      if (startVal && endVal) {
        const startTime = new Date(startVal.replace(' ', 'T'));
        const endTime = new Date(endVal.replace(' ', 'T'));

        if (startTime >= endTime) {
          errorBox.text('End time must be greater than start time.');
          if (endInput[0]._flatpickr) {
            endInput[0]._flatpickr.clear();
          } else {
            endInput.val('');
          }
        }
      }
    });

    // Section AJAX (only for Week Of form)
    const branchSelect = form.find('[name="branch_id[]"]');
    const hostelSelect = form.find('[name="hostel[]"]');
    const genderSelect = form.find('[name="gender[]"]');
    const sectionSelect = form.find('[name="section[]"]');

    if (!sectionSelect.length) {
      return;
    }

  function getSection() {
        let branch = branchSelect.val() || [];
        let hostel = hostelSelect.val() || [];
        let gender = genderSelect.val() || [];

        if (branch.length === 0) {
          sectionSelect.empty().trigger('change.select2');
          return;
        }

        $.ajax({
          url: "{{ route('holiday.create') }}",
          type: "GET",
          data: {
            branch: branch,
            hostel: hostel,
            gender: gender
          },
          success: function (data) {
            sectionSelect.empty();
            sectionSelect.append('<option value="ALL">All</option>');
            $.each(data, function (key, item) {
              sectionSelect.append(`<option value="${item.section}">${item.section}</option>`);
            });
            sectionSelect.trigger('change.select2');
          },
          error: function (xhr) {
            console.log(xhr.responseText);
          }
        });
      }

    branchSelect.add(hostelSelect).add(genderSelect).on('change', getSection);
  });
</script>
@endsection