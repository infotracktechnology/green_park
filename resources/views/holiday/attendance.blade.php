@extends('layouts.app')
@section('title', 'Attendance')
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
                    <form action="{{ route('attendance') }}" method="get" enctype="multipart/form-data">
                    <div class="row">
                        
                       
                        <div class="form-group col-lg-2">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" @selected(request('academic_year') == $row->academic_year)>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                          </div>

                        <div class="form-group col-lg-2">
                            <label for="date">Attendance Date</label>
                            <input type="text" value="{{ request('attendance_date') ?? date('Y-m-d')  }}" name="attendance_date" class="form-control form-control-sm date-picker" required>
                          </div>

                          
                        <div class="form-group col-lg-2">
                            <label for="hostel">Attendance Timing</label>
                            <select name="attendance_timing" id="attendance_timing"  class="form-control form-control-sm" required>
                                <option value="">Select Option</option>
                                <option value="Morning,Afternoon" @selected(request('attendance_timing') == 'Morning,Afternoon')>All Timing</option>
                                @foreach (['Morning','Afternoon'] as $row)
                                    <option value="{{ $row }}" @selected(request('attendance_timing') == $row)>{{ $row }}</option>
                                @endforeach
                              </select>
                          </div>

                      
                          <div class="form-group col-lg-3">
                            <label for="branch">Branch</label>
                            <select name="branch_id" id="branch_id" class="form-control form-control-sm" required>
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="form-group col-lg-2">
                            <label for="section">Section</label>
                            <select name="section" id="section" class="form-control form-control-sm" required>
                                <option value="">Select Section</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->section }}" @selected(request('section') == $section->section)>{{ $section->section }}</option>
                                @endforeach
                            </select>
                        </div>



                          <div class="form-group col-lg-2">
                                  <button type="submit" name="show" class="btn btn-primary m-t-25">Show</button>
                          </div>
                         
                     </div>
                    </form>

                     @if(request()->has('show'))
                     <form action="{{ route('attendance.store') }}" method="post" enctype="multipart/form-data">
                     @csrf
                    <div class="row">
                      
            <div class="col-md-12 col-sm-12 mb-3">
<div class="table-responsive">
<input type="hidden" name="attendance_date" value="{{ request('attendance_date') }}">
<input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
<input type="hidden" name="section" value="{{ request('section') }}">
<input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
      <table class="table table-striped attendance-table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Academic Year</th>
                    <th>Student Id</th>
                    <th>Name</th>
                    <th>Coaching Type</th>
                    <th>Section</th>
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
                   <td>{{ $row->academic_year }}</td>
                   <td>{{ $row->student_id }}</td>
                   <td>{{ \Str::limit($row->student_name, 20) }}</td>
                   <td>{{ $row->coaching_type }}</td>
                   <td>{{ $row->section }}</td>
                   @foreach(explode(',', request('attendance_timing')) as  $time)
                   <?php 
                    $disabled = $attendance->where('timing', $time)->where('student_id', $row->student_id)->count();
                    $checked = $attendance->where('timing', $time)->where('student_id', $row->student_id)->first()->status ?? 'P';
                    ?>
                   <td class="{{ $time }}">
                    <input type="hidden" name="student_id[{{$i}}][{{$time}}]" value="{{ $row->student_id }}">
                    <input type="radio" name="status[{{$i}}][{{$time}}]" value="P" class="present" @disabled($disabled) @checked($checked == 'P')> <label>P</label>
                    <input type="radio" name="status[{{$i}}][{{$time}}]" value="A" class="absent" @disabled($disabled) @checked($checked == 'A')> <label>A</label>
                   </td>
                   @endforeach
               </tr>
                @endforeach
            </tbody>
        </table>

        
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

    const branch = $('#branch_id');

branch.change(function() {
    $('#section').html('<option value="">Select Section</option>');
    if(branch.val() != "") {
        getSection();
    }
})

function getSection() {
    $.get(`{{ route('holiday.create') }}?branch=${branch.val()}`, function(data) {
        var html = '<option value="">Select Section</option>';
        $.each(data, function(key, item) {
            html += '<option value="' + item.section + '">' + item.section + '</option>';
        });
        $('#section').html(html);
    });
}


  $(".edit_timing").click(function(e) {
    var time = $(this).data('time');
    if(confirm('Are you sure you want to edit?')) {
        $(`.${time} input`).prop('disabled', false);
    }

});
</script>
@endsection