@extends('layouts.app')
@section('title', 'Hostel Attendance')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css" />
@endsection

@section('main')
<div class="main-content">
  <section class="section">

    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
            
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <b>{{ session('success') }}</b>
          </div>
          @endif

          @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
          </div>
          @endif


          <div class="card card-primary">

            <div class="card-body">

              <div class="row">
                <div class="col-md-10 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Hostel Attendance </h6>
                </div>
              </div>
              <form action="{{ route('hostelattendance') }}" method="get" enctype="multipart/form-data">
                <div class="row">

                  <div class="form-group col-lg-2">
                    <label for="branch">Branch</label>
                    <select name="branch_id" id="branch_id" class="select2" required>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(request('branch_id')==$branch->id)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                   <div class="form-group col-lg-3">
                    <label for="section">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="select2" required>
                      <option value="">Select Hostel</option>
                      @foreach ($hostel as $row)
                      <option value="{{ $row->id }}" @selected(request('hostel_id')==$row->id)>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="section">Section</label>
                    <select name="section" id="sections" class="select2" required>
                      <option value="">Select Section</option>
                      @foreach ($section as $row)
                      <option value="{{ $row }}" @selected(request('section')==$row)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label for="date">Attendance Date</label>
                    <input type="text" value="{{ request('attendance_date') ?? date('Y-m-d')  }}" name="attendance_date" class="form-control form-control-sm date-picker" required>
                  </div>


                  <div class="form-group col-lg-2">
                    <label for="hostel">Attendance Timing</label>
                    <select name="attendance_timing" id="attendance_timing" class="form-control form-control-sm" required>
                      <option value="Morning,Evening" @selected(request('attendance_timing')=='Morning,Evening' )>All Timing</option>
                      @foreach (['Morning','Evening'] as $row)
                      <option value="{{ $row }}" @selected(request('attendance_timing')==$row)>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <button type="submit" name="show" class="btn btn-primary">Show</button>
                  </div>

                </div>
              </form>

              @if(request()->has('show'))
              <form action="{{ route('hostelattendance.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-md-12 col-sm-12 d-flex justify-content-end">
                    <button type="button" id="attendance_del" name="delete" data-timing="{{ request('attendance_timing') }}" class="btn btn-danger mx-2">Delete</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save Attendance</button>
                  </div>

                  <div class="col-md-12 col-sm-12 mb-3">
                    <div class="table-responsive">
                      <input type="hidden" name="attendance_date" value="{{ request('attendance_date') }}">
                      <input type="hidden" name="section" value="{{ request('section') }}">
                      <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                      <input type="hidden" name="hostel_id" value="{{ request('hostel_id') }}">
                      <table class="table table-striped attendance-table table-sm">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Student Id</th>
                            <th>Course</th>
                            <th>Student Name</th>
                            <th>Coaching Type</th>
                            <th>Section</th>
                            <th>Room No</th>
                            @foreach(explode(',', request('attendance_timing')) as $time)
                            <?php 
                            $edit = $attendance->where('timing', $time)->count();
                            ?>
                            <th>
                              {{ $time }}
                              @if($edit)
                              <br><button type="button" data-time="{{ $time }}" class="btn edit_timing"><i class="fa fa-edit col-green font-18"></i></button>
                              @endif
                            </th>
                            @endforeach
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($students as $i => $row)
                          <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->student_id }}</td>
                            <td>{{ $row->course }}</td>
                            <td>{{ $row->student_name }}</td>
                            <td>{{ $row->coaching_type }}</td>
                            <td>{{ $row->section }}</td>
                            <td>{{ $row->room_no }}<input type="hidden" name="room_no[{{$i}}]" value="{{ $row->room_no }}"></td>
                            @foreach(explode(',', request('attendance_timing')) as $time)
                    <?php 
                    $disabled = $attendance->where('timing', $time)->where('student_id', $row->student_id)->count();
                    $attendance_entry = $attendance->where('timing', $time)->where('student_id', $row->student_id)->first();
                    $checked = $attendance_entry ? $attendance_entry->status : 'P';
                    ?>
                            <td class="{{ $time }}">
                              @if($attendance_entry)
                              <input type="hidden" name="attendance_id[{{$i}}][{{$time}}]" value="{{ $attendance_entry->id }}">
                              @endif

                              <input type="hidden" name="student_id[{{$i}}][{{$time}}]" value="{{ $row->student_id }}">
                              <input type="radio" name="status[{{$i}}][{{$time}}]" value="P" class="present" @disabled($disabled) @checked($checked=='P' )> <label>P</label>
                              <input type="radio" name="status[{{$i}}][{{$time}}]" value="A" class="absent" @disabled($disabled) @checked($checked=='A' )> <label>A</label>
                            </td>

                            @endforeach
                          </tr>
                          @endforeach
                        </tbody>
                      </table>


                      </table>
                    </div>
                  </div>


                </div>
              </form>
              @endif

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

  flatpickr(".date-picker", {
     enableTime: false,
     allowInput: true,
     dateFormat: "Y-m-d",
     maxDate: "today",
     plugins: [new confirmDatePlugin({confirmText: "OK"})]
    });
  

  
  $("#attendance_del").click(function(e) {
      var timing = $(this).data('timing');
      var attendance_date = $('input[name="attendance_date"]').val();
      var section = $('input[name="section"]').val();
      var branch_id = $('input[name="branch_id"]').val();
      if(confirm('Are you sure you want to delete?')) {
      $.get(`{{ route('hostelattendance') }}`,{timing: timing, attendance_date: attendance_date, section: section, branch_id: branch_id,delete:1},function(data) {
        alert("Attendance deleted successfully");
        location.reload();
      });
      }
  });
  
  
    $(".edit_timing").click(function(e) {
      var time = $(this).data('time');
      if(confirm('Are you sure you want to edit?')) {
     $(`.${time} input`).prop('disabled', false);
      }
  
  });

  const goToMenu = (params = {}) => {
    const query = new URLSearchParams(params).toString();
    window.location = `{{ route('hostelattendance') }}?${query}`;
     };
  
  $("#branch_id").change(function() {
      if(!this.value) return;
    goToMenu({
      branch_id: this.value,
    });
  });
  
  $("#hostel_id").change(function() {
      if(!this.value) return;
    goToMenu({
      branch_id: $("#branch_id").val(),
      hostel_id: this.value,
    });
  });
</script>
@endsection