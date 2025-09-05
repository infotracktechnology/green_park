@extends('layouts.app')
 @section('title', 'Dashboard')
 @section('css')
 <style>
    .cursor-pointer{
        cursor: pointer;
    }
    .underline{
        text-decoration: underline;
    }

    .card-header {
            background: #277bff !important;
        }

    
 </style>
 <style>
        .announcement-card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            height: 100%;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
    

        .date-slider-container {
            display: flex;
            align-items: center;
            background-color: #e9f7fe;
            padding: 10px 5px;
        }

        .date-slider {
            flex: 1;
            overflow-x: auto;
            scroll-behavior: smooth;
            -ms-overflow-style: none;  /* Hide scrollbar for IE and Edge */
            scrollbar-width: none;  /* Hide scrollbar for Firefox */
        }

        .date-slider::-webkit-scrollbar {
            display: none; /* Hide scrollbar for Chrome, Safari and Opera */
        }

        .date-slider-inner {
            display: flex;
            flex-wrap: nowrap;
            padding: 0 10px;
        }

        .slider-nav {
            background: #17a2b8;
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
        }

        .slider-nav:hover {
            background: #138496;
            transform: scale(1.1);
        }

        .slider-nav:disabled {
            background: #b8d4da;
            cursor: not-allowed;
            transform: scale(1);
}
        .date-item {
            flex: 0 0 auto;
            width: 70px;
            text-align: center;
            padding: 10px 5px;
            margin: 0 5px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .date-item:hover {
            background-color: #d1ecf1;
        }
        .date-item.active {
            background-color: #17a2b8;
            color: white;
        }
        .date-month {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .date-day {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .date-number {
            font-size: 20px;
            font-weight: 700;
        }
        .announcement-list {
            height: 300px;
            overflow-y: auto;
        }
        .announcement-item {
            border-left: 4px solid #17a2b8;
            transition: all 0.3s;
        }
        .announcement-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .attachment-icon {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .coaching-badge {
            font-size: 0.75rem;
        }
      
        .announcement-list::-webkit-scrollbar {
            width: 6px;
        }
        .announcement-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .announcement-list::-webkit-scrollbar-thumb {
            background: #aaa;
            border-radius: 10px;
        }
        
        .custom-range-container {
            display: none;
            margin-top: 10px;
        }
        .date-range-inputs {
            display: flex;
            gap: 10px;
        }
        .date-range-inputs .form-group {
            flex: 1;
        }
        .overflow_scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .overflow_scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .overflow_scrollbar::-webkit-scrollbar-thumb {
            background: #aaa;
            border-radius: 10px;
        }
        
    </style>
 @endsection
 @section('main')
<div class="main-content">
  <section class="section">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="text-white">Students</h4>
            </div>
            <div class="card-body overflow_scrollbar" style="overflow-y: auto; height: 350px">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center shadow-sm rounded">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:50px;"><i class="fas fa-layer-group"></i></th>
                                <th class="align-middle">📚 Branch</th>
                                <th class="align-middle">
                                    Total <br>
                                    <span class="badge badge-info p-2">{{$total}}</span>
                                </th>
                                <th class="align-middle">👦 <br>
                                    <span class="badge badge-primary p-2">{{$boys}}</span>
                                </th>
                                <th class="align-middle">👧 <br>
                                    <span class="badge bg-pink p-2">{{$girls}}</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="collapse-tbody" class="collapse show">
                            @foreach($data as $key => $branchs)
                               
                                <tr class="bg-light fw-bold cursor-pointer" data-toggle="collapse" data-target="#collapse-{{ Str::slug($branchs->name) }}" aria-expanded="false">
                                    <td>
                                        <i class="fas fa-plus-circle text-success toggle-icon"></i>
                                    </td>
                                    <td class="text-start">{{ $branchs->name }}</td>
                                    <td class="text-warning">{{ $branchs->student->count() }}</td>
                                    <td class="text-primary">{{ $branchs->student->where('gender', 'MALE')->count() }}</td>
                                    <td class="col-pink">{{ $branchs->student->where('gender', 'FEMALE')->count() }}</td>
                                </tr>

                                <tr class="collapse" id="collapse-{{ Str::slug($branchs->name) }}">
                                    <td colspan="5">
                                        <table class="table table-sm mb-0 table-striped students-table">
                                            <tbody>
                                                @foreach($branchs->student->groupBy('section') as $key => $section)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $key == '' ? '-' : $key }}</td>
                                                        
                                                        <td class="fw-bold cursor-pointer underline" data-section="{{ $key }}" data-campus="{{ $branchs->id }}" data-gender="all">{{ $section->count() }}</td>
                                                        <td class="text-primary cursor-pointer underline" data-section="{{ $key }}" data-campus="{{ $branchs->id }}" data-gender="MALE">{{ $section->where('gender', 'MALE')->count() }}</td>
                                                        <td class="text-pink cursor-pointer underline" data-section="{{ $key }}" data-campus="{{ $branchs->id }}" data-gender="FEMALE">{{ $section->where('gender', 'FEMALE')->count() }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="text-white">Attendance</h4>
            </div>
            <div class="card-body overflow_scrollbar" style="overflow-y: auto; height: 350px">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center shadow-sm rounded">
                       
                        <thead class="thead-light">
                            <tr>
                                <th style="width:50px;"><i class="fas fa-layer-group"></i></th>
                                <th class="align-middle">📚 Branch</th>
                                <th class="align-middle">
                                    Total <br>
                                    <span class="badge badge-info p-2">{{$total}}</span>
                                </th>
                                <th class="align-middle">Prsent <br>
                                    <span class="badge badge-primary p-2">{{ $present }}</span>
                                </th>
                                <th class="align-middle">Absent <br>
                                    <span class="badge bg-pink p-2">{{$total - $present}}</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="collapse-tbody" class="collapse show">
                            @foreach($data as $key => $branchs)
                        <?php
                        $branch_present = $branchs->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->unique('student_id')->count();
                        
                        ?>
                                <tr class="bg-light fw-bold cursor-pointer" data-toggle="collapse" data-target="#attendance-{{ Str::slug($branchs->name) }}" aria-expanded="false">
                                    <td>
                                        <i class="fas fa-plus-circle text-success toggle-icon"></i>
                                    </td>
                                    <td class="text-start">{{ $branchs->name }}</td>
                                    <td class="text-warning">{{ $branchs->student->count() }}</td>
                                    <td class="text-primary">{{ $branch_present }}</td>
                                    <td class="col-pink">{{ $branchs->student->count() - $branch_present }}</td>
                                </tr>

                                <tr class="collapse" id="attendance-{{ Str::slug($branchs->name) }}">
                                    <td colspan="5">
                                        <table class="table table-sm mb-0 table-striped attendance-table">
                                            <tbody>
                                                @foreach($branchs->student->groupBy('section') as $key => $section)
                        <?php
                        $section_present = $branchs->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->where('section', $key)->unique('student_id')->count();
                        ?>
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $key == '' ? '-' : $key }}</td>
                                                        
                                                        <td class="fw-bold cursor-pointer underline" data-section="{{ $key }}" data-campus="{{ $branchs->id }}" data-gender="all">{{ $section->count() }}</td>
                                                        <td class="text-primary cursor-pointer underline" data-section="{{ $key }}" data-campus="{{ $branchs->id }}" data-gender="MALE">{{ $section_present }}</td>
                                                        <td class="text-pink cursor-pointer underline" data-section="{{ $key }}" data-campus="{{ $branchs->id }}" data-gender="FEMALE">{{ $section->count() - $section_present }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
      </div>



<div class="col-md-4">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header text-white rounded-top-3">
      <h4 class="mb-0 fw-bold text-white">Staff Overview</h4>
    </div>

    <div class="card-body p-3 overflow_scrollbar" style="overflow-y: auto; height: 350px;">
      
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2 px-3 bg-light rounded d-flex align-items-center justify-content-between"
             data-toggle="collapse" 
             data-target="#collapse-TeachingStaff" 
             aria-expanded="false" 
             style="cursor: pointer;">
          <h5 class="mb-0 fw-bold text-success"><img src="{{ asset('img/icon/teacher.png') }}" class="mr-2" height="30px" width="30px" alt="">Teaching Staff</h5>
          <span class="badge bg-success rounded-pill px-3 py-2 text-white fw-bold">
            {{ $staffs->except('Others')->map(fn($staff) => $staff->count())->sum() }}
          </span>
        </div>
        <div id="collapse-TeachingStaff" class="collapse mt-2">
          <table class="table table-hover table-sm mb-0 text-center" id="teachingStaffTable">
            <tbody>
              @foreach($staffs->except('Others') as $department => $staff)
              <tr>
                <td class="fw-semibold" style="font-weight: bold; color: #f5ab0b; font-size: 16px; width: 75%">{{ $department }}</td>
                <td class="text-end fw-bold cursor-pointer underline" style="font-weight: bold; color: #28a745; font-size: 16px; width: 25%" data-department="{{ $department }}">{{ $staff->count() }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3 bg-light rounded d-flex align-items-center justify-content-between" data-toggle="collapse" data-target="#collapse-NonTeachingStaff" aria-expanded="false" style="cursor: pointer;">
            <div class="d-flex align-items-center justify-content-center">
                <div>
                    <img src="{{ asset('img/icon/cleaning-staff.png') }}" class="mr-2" height="30px" width="30px" alt="">
                </div>
                <h5 class="mb-0 fw-bold text-primary">Non-Teaching Staff</h5>
            </div>
          <span class="badge bg-primary rounded-pill px-3 py-2 text-white fw-bold">
            {{ $staffs->only('Others')->map->count()->sum() }}
          </span>
        </div>
        <div id="collapse-NonTeachingStaff" class="collapse mt-2">
          <table class="table table-hover table-sm text-center mb-0" id="nonTeachingStaffTable">
            <tbody>
              @foreach($staffs->only('Others') as $department => $staff)
              <tr>
                <td class="fw-semibold" style="font-weight: bold; color: #f5ab0b; font-size: 16px; width: 75%">
                  {{ $department == 'Others' ? 'Non-Teaching Staff' : $department }}
                </td>
                <td class="text-end fw-bold text-primary cursor-pointer underline" style="font-weight: bold; font-size: 16px; width: 25%" data-department="{{ $department }}">{{ $staff->count() }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="col-md-4">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header text-white rounded-top-3">
      <h4 class="mb-0 fw-bold text-white">Concerns</h4>
    </div>
 
  <div class="card-body overflow_scrollbar" style="overflow-y: auto; height: 350px">
    <table class="table table-striped">
        <tr>
            <th>#</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Open Concerns</td>
            <td><a href="{{ route('parent_concern') }}">  {{ $concerns->count() }}</a></td>
        </tr>
        <tr>
            <td>In Progress Concerns</td>
            <td><a href="{{ route('parent_concern') }}"> {{ $concerns->where('status', 'In Progress')->count() }}</a></td>
        </tr>
        <tr>
            <td>Closed Concerns</td>
            <td><a href="{{ route('parent_concern') }}">  {{ $concerns->where('status', 'Closed')->count() }}</a></td>
        </tr>
    </table>
  </div>
</div>
</div>



<div class="col-md-4">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header text-white rounded-top-3">
      <h4 class="mb-0 fw-bold text-white">Announcement Overview</h4>
    </div>
 
  <div class="card-body overflow_scrollbar" style="overflow-y: auto; height: 350px">
    <table class="table table-striped">
        <tr>
            <th>Branch</th>
            <th>Count</th>
        </tr>
        @foreach($announcement as $key => $row)
        <tr>
            <td>{{ $row['branch'] }}</td>
            <td><a href="{{ route('announcement.index') }}">  {{ $row['count'] }}</a></td>
        </tr>
        @endforeach
        
    </table>
  </div>
</div>
</div>

<div class="col-md-4">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header text-white rounded-top-3">
      <h4 class="mb-0 fw-bold text-white">Chairmanvideo Overview</h4>
    </div>
 
  <div class="card-body overflow_scrollbar" style="overflow-y: auto; height: 350px">
    <table class="table table-striped">
        <tr>
            <th>Branch</th>
            <th>Count</th>
        </tr>
         @foreach($chairman as $key => $row)
        <tr>
            <td>{{ $row['branch'] }}</td>
            <td><a href="{{ route('chairmanvideo.index') }}">  {{ $row['count'] }}</a></td>
        </tr>
        @endforeach
        
    </table>
  </div>
</div>
</div>





    </div>
  </section>
  <div class="modal fade" id="studentInfoModal" tabindex="-1" aria-labelledby="studentInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content bg-white rounded shadow">
        <div class="modal-header bg-success text-white py-2">
          <h5 class="modal-title" id="studentInfoModalLabel">Student Information</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="studentInfoModalBody">
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')

<script>

$(document).ready(function() {
    let modal = $('#studentInfoModal');
    let modalbody = $('#studentInfoModalBody');
    let modalLabel = $('#studentInfoModalLabel');
    $('#teachingStaffTable, #nonTeachingStaffTable').on('click', 'td', function() {
        const department = $(this).data('department');
        if (!department) {
            return;
        }
        if (department) {
            $.ajax({
                url: "{{ route('dashboard.staff') }}",
                type: 'GET',
                data: { department: department },
                success: function(response) {
                    modalbody.empty();
                    modalLabel.text(`${department}`);
                    const table = $('<table class="table table-striped table-bordered table-hover"></table>');
                    const thead = $('<thead class="thead-dark"></thead>');
                    const tbody = $('<tbody></tbody>');
                    const trHead = $('<tr></tr>');
                    trHead.append('<th scope="col">#</th>');
                    trHead.append('<th scope="col">Name</th>');
                    trHead.append('<th scope="col">Gender</th>');
                    trHead.append('<th scope="col">Designation</th>');
                    trHead.append('<th scope="col">Department</th>');
                    trHead.append('<th scope="col">Branch</th>');
                    thead.append(trHead);
                    response.staffs.forEach((staff, index) => {
                        const tr = $('<tr></tr>');
                        tr.append(`<td>${index + 1 || '-'}</td>`);
                        tr.append(`<td>${staff.name || '-'}</td>`);
                        tr.append(`<td>${staff.gender || '-'}</td>`);
                        tr.append(`<td>${staff.designation || '-'}</td>`);
                        tr.append(`<td>${staff.department || '-'}</td>`);
                        tr.append(`<td>${staff.branch.name || '-'}</td>`);
                        tbody.append(tr);
                    });
                    table.append(thead);
                    table.append(tbody);
                    modalbody.append(table);
                    modal.modal('show');
                },
                error: function(xhr) {
                    alert('An error occurred while fetching teaching staff information.');
                }
            })
        }
    });
    $('.students-table').on("click", "td", function(){
    
    const section = $(this).data("section") == '' ? '-' : $(this).data("section");
    const campus = $(this).data("campus");
    const gender = $(this).data("gender");
    
    if (!section || !campus || !gender) {
            return;
        }

        $.ajax({
            url: "{{ route('dashboard.gender') }}",
            type: "GET",
            data: { section: section, campus: campus, gender: gender },
            success: function(response) {

                modalbody.empty();
                modalLabel.text(`${section} - ${gender == 'all' ? 'All' : gender}`);
                    const table = $('<table class="table table-striped table-bordered table-hover"></table>');
                    const thead = $('<thead class="thead-dark"></thead>');
                    const tbody = $('<tbody></tbody>');
                    const trHead = $('<tr></tr>');
                    trHead.append('<th scope="col">#</th>');
                    trHead.append('<th scope="col">ID</th>');
                    trHead.append('<th scope="col">Name</th>');
                    trHead.append('<th scope="col">Section</th>');
                    trHead.append('<th scope="col">Type</th>');
                    trHead.append('<th scope="col">Gender</th>');
                    trHead.append('<th scope="col">Campus</th>');
                    thead.append(trHead);
                    response.students.forEach((student, index) => {
                        const tr = $('<tr></tr>');
                        tr.append(`<td>${index + 1 || '-'}</td>`);
                        tr.append(`<td>${student.student_id || '-'}</td>`);
                        tr.append(`<td>${student.student_name || '-'}</td>`);
                        tr.append(`<td>${student.section || '-'}</td>`);
                        tr.append(`<td>${student.coaching_type || '-'}</td>`);
                        tr.append(`<td>${student.gender || '-'}</td>`);
                        tr.append(`<td>${student.branch.name || '-'}</td>`);
                        tbody.append(tr);
                    });
                    table.append(thead);
                    table.append(tbody);
                    modalbody.append(table);
                    modal.modal('show');
            },
            error: function(xhr) {
                alert('An error occurred while fetching student information.');
            }
        });
    });
});
</script>

@endsection