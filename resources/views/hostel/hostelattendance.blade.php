@extends('layouts.app')
@section('title', 'Hostel Attendance')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css"/>
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
                    <h6 class="col-deep-purple">Attendance </h6>
                    </div>
                    </div>
                    <form action="{{ route('hostelattendance') }}" method="get" enctype="multipart/form-data">
                    <div class="row">
                        {{-- <div class="form-group col-lg-2">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required> --}}
                                    {{-- <option value="">Select Academic Year</option> --}}
                                    {{-- @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" @selected(request('academic_year') == $row->academic_year)>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                          </div> --}}

                        <div class="form-group col-lg-2">
                            <label for="date">Attendance Date</label>
                            <input type="text" value="{{ request('attendance_date') ?? date('Y-m-d')  }}" name="attendance_date" class="form-control form-control-sm date-picker" required>
                          </div>
                          
                          
                        <div class="form-group col-lg-2">
                            <label for="hostel">Attendance Timing</label>
                            <select name="attendance_timing" id="attendance_timing"  class="form-control form-control-sm" required>
                                <option value="">Select Option</option>
                                @foreach (['Morning','Evening'] as $row)
                                    <option value="{{ $row }}" @selected(request('attendance_timing') == $row)>{{ $row }}</option>
                                @endforeach
                              </select>
                          </div>

                      
                        <div class="form-group col-lg-3">
                          <label for="branch">Branch</label>
                          <select name="branch_id" id="branch_id"  class="form-control form-control-sm"  required>
                              <option value="">Select  Branch</option>
                              @foreach ($branches as $branch)
                                  <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                              @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="hostel">Hostel</label>
                            <select name="hostel" id="hostel" class="form-control form-control-sm" required>
                                <option value="">Select Hostel</option>
                                @foreach($hostels->where('branch_id', request('branch_id')) as $h)
                                    <option value="{{ $h->id }}" @selected(request('hostel') == $h->id)>{{ $h->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="form-group col-lg-2">
                            <label for="room_no">Room No</label>
                            <select name="room_no" id="room_no" class="form-control form-control-sm" required>
                                <option value="">Select Room No</option>
                                @foreach($rooms->where('hostel_id', request('hostel')) as $room)
                                    @if($room->room_no)
                                        <option value="{{ $room->room_no }}" @selected(request('room_no') == $room->room_no)>{{ $room->room_no }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-2">
                            <button type="submit" name="show" class="btn btn-primary m-t-25">Show</button>
                    </div>
                    
                     </div>
                    </form>
                   
                     @if(request()->has('show'))
                     <form action="{{ route('hostelattendance.store') }}" method="post" enctype="multipart/form-data">
                     @csrf
                    <div class="row">
                       

            <div class="col-md-12 col-sm-12 mb-3">
                    <div class="table-responsive">
<input type="hidden" name="attendance_date" value="{{ request('attendance_date') }}">
<input type="hidden" name="timing" value="{{ request('attendance_timing') }}">
{{-- <input type="hidden" name="academic_year" value="{{ request('academic_year') }}"> --}}
<input type="hidden" name="hostel" value="{{ request('hostel') }}">
<input type="hidden" name="room_no" value="{{ request('room_no') }}">
<input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
      <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    {{-- <th>Academic Year</th> --}}
                    <th>Student Id</th>
                    <th>Name</th>
                    <th>Coaching Type</th>
                    
                    <th>Present/Absent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $row)
               <tr>
                <td>{{ $loop->iteration }}</td>
                <input type="hidden" name="student_id[{{ $loop->iteration }}]" value="{{ $row->student_id }}">

                   {{-- <td>{{ $row->academic_year }}</td> --}}
                   <td>{{ $row->student_id }}</td>
                   <td>{{ \Str::limit($row->student_name, 20) }}</td>
                   <td>{{ $row->coaching_type }}</td>
                 
                   <td>
                    <input type="radio" name="status[{{ $loop->iteration }}]" value="P" class="present" required checked> <label>P</label>
                    <input type="radio" name="status[{{ $loop->iteration }}]" value="A" class="absent" required> <label>A</label>
                   </td>
               </tr>
                @endforeach
            </tbody>
        </table>

        
  
                </div>
            </div>
            <div class="col-md-6 offset-md-4 col-sm-12 mt-3">
                <button type="submit" name="submit" class="btn btn-primary m-t-10">Save Attendance</button>
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
        plugins: [
            new confirmDatePlugin({
                confirmText: "OK",
                showAlways: false,
            })
        ]
    });

    // Present all checkbox
   
</script>

<script>
    const allHostels = @json($hostels);
    const allRooms = @json($rooms);

    const branch = $('#branch_id');
    const hostel = $('#hostel');
    const roomNo = $('#room_no');

    branch.change(function () {
        const branchId = $(this).val();
        const filteredHostels = allHostels.filter(item => item.branch_id == branchId);
        let hostelOptions = '<option value="">Select Hostel</option>';
        filteredHostels.forEach(item => {
            hostelOptions += `<option value="${item.id}">${item.name}</option>`;
        });
        hostel.html(hostelOptions);
        roomNo.html('<option value="">Select Room No</option>');
    });

    hostel.change(function () {
        const hostelId = $(this).val();
        const filteredRooms = allRooms.filter(item => item.hostel_id == hostelId);
        let roomOptions = '<option value="">Select Room No</option>';
        filteredRooms.forEach(item => {
            if (item.room_no) {
                roomOptions += `<option value="${item.room_no}">${item.room_no}</option>`;
            }
        });
        roomNo.html(roomOptions);
    });
</script>
@endsection

