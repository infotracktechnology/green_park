@extends('layouts.app')
@section('title', 'Holiday')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css"/>
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
          
            @if($holiday->type == 'Week Of')
                  <div class="card card-primary">
                     <form method="post" id="myForm" action="{{ route('holiday.update', $holiday->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Week of Holiday Details</h6>
                              </div>
                            
                              <div class="form-group col-lg-2">
                                <input type="hidden" name="name" value="Week Of Leave">
                                <input type="hidden" name="type" value="Week Of">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class="form-control form-control-sm" required>
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" @selected($holiday->academic_year == $row->academic_year)>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                              </div>

                              <div class="form-group col-lg-3">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id[]" id="branch_id" class="form-control form-control-sm select2" multiple required>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            @selected(in_array((string)$branch->id, array_map('strval', $branch_ids)))>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                              </div>

                              <div class="form-group col-lg-3">
                                <label for="hostel">Hostel / Dayscholar</label>
                                <select name="hostel[]" id="hostel" class="form-control form-control-sm select2" multiple required>
                                    @foreach (['Hostel', 'Dayscholar'] as $row)
                                        <option value="{{ $row }}"
                                            @selected(in_array($row, $hostels))>
                                            {{ $row }}
                                        </option>
                                    @endforeach
                                </select>
                              </div>

                              <div class="form-group col-lg-2">
                                <label for="gender">Gender</label>
                                <select name="gender[]" id="gender" class="form-control form-control-sm select2" multiple required>
                                    @foreach (['Male', 'Female'] as $row)
                                        <option value="{{ $row }}"
                                            @selected(in_array($row, $genders))>
                                            {{ $row }}
                                        </option>
                                    @endforeach
                                </select>
                              </div>

                              <div class="form-group col-lg-3">
                                <label for="week_sections">Section</label>
                                <select name="section[]" id="week_sections" class="form-control form-control-sm select2" multiple required>
                                    <option value="ALL" @selected(in_array('ALL', $section_ids))>All</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->section }}" @selected(in_array($section->section, $section_ids))>
                                            {{ $section->section }}
                                        </option>
                                    @endforeach
                                </select>
                                </div>

                              <div class="form-group col-lg-2">
                                <label for="start_date">Start Date Time</label>
                                <input type="text" name="start_date" id="start_date" value="{{ $holiday->start_date }}" class="form-control form-control-sm datetime-picker" required>
                              </div>

                              <div class="form-group col-lg-2">
                                <label for="end_date">End Date Time</label>
                                <input type="text" name="end_date" id="end_date" value="{{ $holiday->end_date }}" class="form-control form-control-sm datetime-picker" required>
                                <div id="end_date_error" class="text-danger"></div>
                              </div>

                              <div class="form-group col-lg-2">
                                <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
            @else
                  <div class="card card-info">
                     <form method="post" id="myForm" action="{{ route('holiday.update', $holiday->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Holiday Details</h6>
                              </div>
                              
                              <div class="form-group col-lg-2">
                                 <label for="academic_year">Academic Year</label>
                                 <select name="academic_year" id="academic_year" class="form-control form-control-sm" required>
                                     @foreach ($academicyear as $row)
                                         <option value="{{ $row->academic_year }}" @selected($holiday->academic_year == $row->academic_year)>{{ $row->academic_year }}</option>
                                     @endforeach
                                 </select>
                              </div>
 
                              <div class="form-group col-lg-2">
                                 <label for="branch_id">Branch</label>
                                 <select name="branch_id[]" id="branch_id" class="form-control select2" multiple required>
                                     @foreach ($branches as $branch)
                                         <option value="{{ $branch->id }}"
                                             @selected(in_array((string)$branch->id, array_map('strval', $branch_ids)))>
                                             {{ $branch->name }}
                                         </option>
                                     @endforeach
                                 </select>
                              </div>

                              <div class="form-group col-lg-3">
                                 <label for="hostel">Hostel / Dayscholar</label>
                                 <select name="hostel[]" id="hostel" class="form-control select2" multiple required>
                                     @foreach (['Hostel', 'Dayscholar'] as $row)
                                         <option value="{{ $row }}"
                                             @selected(in_array($row, $hostels))>
                                             {{ $row }}
                                         </option>
                                     @endforeach
                                 </select>
                              </div>
                               
                              <div class="form-group col-lg-3">
                                 <label for="name">Name</label>
                                 <input type="text" name="name" id="name" value="{{ $holiday->name }}" class="form-control form-control-sm">
                              </div>

                              <div class="form-group col-lg-3">
                                 <label for="type">Holiday Type</label>
                                 <input type="text" name="type" id="type" value="{{ $holiday->type }}" class="form-control form-control-sm">
                              </div>

                              <div class="form-group col-lg-2">
                                 <label for="start_date">Start Date Time</label>
                                 <input type="text" name="start_date" id="start_date" value="{{ $holiday->start_date }}" class="form-control form-control-sm datetime-picker" required>
                              </div>

                              <div class="form-group col-lg-2">
                                 <label for="end_date">End Date Time</label>
                                 <input type="text" name="end_date" id="end_date" value="{{ $holiday->end_date }}" class="form-control form-control-sm datetime-picker" required>
                                 <div id="end_date_error" class="text-danger"></div>
                              </div>

                              <div class="form-group col-lg-2">
                                 <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
            @endif
                     
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

    // End date validation (Week Of & Normal Holiday rendukum work aagum)
    function validateDates() {
        $('#end_date_error').text('');
        const startVal = $('#start_date').val();
        const endVal = $('#end_date').val();

        if (startVal && endVal) {
            const startTime = new Date(startVal.replace(' ', 'T'));
            const endTime = new Date(endVal.replace(' ', 'T'));

            if (startTime >= endTime) {
                $('#end_date_error').text('End time must be greater than start time.');
                if ($('#end_date')[0]._flatpickr) {
                    $('#end_date')[0]._flatpickr.clear();
                } else {
                    $('#end_date').val('');
                }
            }
        }
    }

    $('#start_date, #end_date').on('change', validateDates);

    // Week Of form-la mattum Section AJAX run aagum
    if ($('#section').length) {
        const branch = $('#branch_id');
        const hostel = $('#hostel');
        const gender = $('#gender');
        const section = $('#section');

        function getSection() {
            const branchVal = branch.val() || [];
            const hostelVal = hostel.val() || [];
            const genderVal = gender.val() || [];
            const previousSelected = section.val() || [];

            if (branchVal.length === 0) {
                section.empty().trigger('change.select2');
                return;
            }

            $.ajax({
                url: "{{ route('holiday.create') }}",
                type: "GET",
                data: {
                    branch: branchVal,
                    hostel: hostelVal,
                    gender: genderVal
                },
                success: function (data) {
                    section.empty();

                    const isAllSelected = previousSelected.includes('ALL');
                    section.append(new Option('All', 'ALL', false, isAllSelected));

                    $.each(data, function (key, item) {
                        const isSelected = previousSelected.includes(item.section);
                        section.append(new Option(item.section, item.section, false, isSelected));
                    });

                    section.trigger('change.select2');
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        branch.add(hostel).add(gender).on('change', getSection);
    }
</script>
@endsection
