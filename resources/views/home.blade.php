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
        /* .date-slider {
            background-color: #e9f7fe;
            padding: 15px 0;
            overflow-x: auto;
        } */

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
        /* .date-slider-container {
            display: flex;
            flex-wrap: nowrap;
            padding: 0 10px;
        } */
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
        .card-header {
            background: linear-gradient(45deg, #17a2b8, #138496) !important;
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
            <div class="card-body overflow_scrollbar" style="overflow-y: auto; height: 300px">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center shadow-sm rounded">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:50px;"><i class="fas fa-layer-group"></i></th>
                                <th class="align-middle">📚 Coaching Type</th>
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
                            @foreach($data as $coachingType => $sections)
                                @php
                                    $totalBoys = $sections->sum('boys_count');
                                    $totalGirls = $sections->sum('girls_count');
                                    $totalAll = $sections->sum('total_count');
                                @endphp

                                <tr class="bg-light fw-bold cursor-pointer"
                                    data-toggle="collapse"
                                    data-target="#collapse-{{ Str::slug($coachingType) }}"
                                    aria-expanded="false">
                                    <td>
                                        <i class="fas fa-plus-circle text-success toggle-icon"
                                        id="icon-{{ Str::slug($coachingType) }}"></i>
                                    </td>
                                    <td class="text-start">{{ $coachingType }}</td>
                                    <td class="text-warning">{{ $totalAll }}</td>
                                    <td class="text-primary">{{ $totalBoys }}</td>
                                    <td class="text-pink">{{ $totalGirls }}</td>
                                </tr>

                                <tr class="collapse bg-white" id="collapse-{{ Str::slug($coachingType) }}">
                                    <td colspan="5">
                                        <table class="table table-sm table-striped mb-0 border students-table">
                                            <tbody>
                                                @foreach($sections as $section)
                                                    <tr>
                                                        
                                                        <td width="55%">{{ $section->section == '' ? '-' : $section->section }}</td>
                                                        
                                                        <td class="fw-bold cursor-pointer underline" data-section="{{ $section->section }}" data-type="{{ $coachingType }}" data-gender="all">{{ $section->total_count }}</td>
                                                        <td class="text-primary cursor-pointer underline" data-section="{{ $section->section }}" data-type="{{ $coachingType }}" data-gender="Male">{{ $section->boys_count }}</td>
                                                        <td class="text-pink cursor-pointer underline" data-section="{{ $section->section }}" data-type="{{ $coachingType }}" data-gender="Female">{{ $section->girls_count }}</td>
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
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header text-white rounded-top-3">
      <h4 class="mb-0 fw-bold text-white">Staff Overview</h4>
    </div>

    <div class="card-body p-3 overflow_scrollbar" style="overflow-y: auto; height: 300px;">
      
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

<div class="col-md-12">
                <div class="card announcement-card">
                    <div class="card-header text-white">
                        <h5 class="mb-0"><i class="fas fa-bullhorn mr-2"></i>Activities</h5>
                    </div>
                    
                    <div class="filter-section">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="dateFilter">Date Range</label>
                                <select class="form-control" id="dateFilter">
                                    <option value="7" selected>Last 7 Days</option>
                                    <option value="15">Last 15 Days</option>
                                    <option value="30">Last 30 Days</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                                <div class="custom-range-container" id="customRangeContainer">
                                    <div class="date-range-inputs">
                                        <div class="form-group">
                                            <label for="startDate">Start Date</label>
                                            <input type="date" class="form-control" id="startDate">
                                        </div>
                                        <div class="form-group">
                                            <label for="endDate">End Date</label>
                                            <input type="date" class="form-control" id="endDate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4" id="branchFilterContainer">
                                <label for="branchFilter">Branch</label>
                                <select class="form-control" id="branchFilter">
                                    <option value="all" selected>All Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-start mt-4 pt-1">
                                <button class="btn btn-info w-100" id="applyFilters">
                                    <i class="fas fa-filter mr-1"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Slider -->
                    <div class="date-slider-container">
                        <button class="slider-nav slider-prev" id="sliderPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <div class="date-slider">
                            <div class="date-slider-inner" id="dateSlider">
                                <!-- Dates will be dynamically inserted here -->
                            </div>
                        </div>
                        
                        <button class="slider-nav slider-next" id="sliderNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="announcement-list p-3" id="announcementList">
                        </div>
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
    $('.students-table').on("click", "td", function() {
    
    const section = $(this).data("section") == '' ? '-' : $(this).data("section");
    const type = $(this).data("type");
    const gender = $(this).data("gender");
    
    if (!section || !type || !gender) {
            return;
        }

        $.ajax({
            url: "{{ route('dashboard.gender') }}",
            type: "GET",
            data: { section: section, type: type, gender: gender },
            success: function(response) {

                modalbody.empty();
                modalLabel.text(`${type} - ${section} - ${gender == 'all' ? 'All' : gender}`);
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
<script>
        let announcements = @json($activities);
        const noBranchAllocated = {{ auth()->user()->branch ? 'false' : 'true' }};

        $(document).ready(function() {

            if (noBranchAllocated) {
                $('#branchFilterContainer').show();
            } else {
                $('#branchFilterContainer').hide();
            }
            // Set default dates for custom range
            const today = new Date();
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(today.getDate() - 7);
            
            $('#startDate').val(formatDateForInput(sevenDaysAgo));
            $('#endDate').val(formatDateForInput(today));
            
            // Show/hide custom range inputs
            $('#dateFilter').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#customRangeContainer').slideDown();
                } else {
                    $('#customRangeContainer').slideUp();
                    $('#startDate').val('');
                    $('#endDate').val('');
                }
            });
            
            // Initialize with default 7 days
            generateDateSlider(7);
            displayAnnouncementsForDate(getFormattedDate(new Date()));
            
            // Apply filters button
            $('#applyFilters').on('click', function() {
                const dateRange = $('#dateFilter').val();
                const branch = $('#branchFilter').val();

                if (dateRange === 'custom' && (!$('#startDate').val() || !$('#endDate').val())) {
                    alert('Please select start and end dates for custom range');
                    return;
                }

                // if (dateRange ===)
                
                // Generate date slider based on selected range
                if (dateRange === 'custom') {
                    const startDate = new Date($('#startDate').val());
                    const endDate = new Date($('#endDate').val());
                    
                    // Validate dates
                    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                        alert('Please select valid start and end dates');
                        return;
                    }
                    
                    if (startDate > endDate) {
                        alert('Start date cannot be after end date');
                        return;
                    }
                    
                    $.ajax({
                        url: "{{ route('dashboard.announcement') }}",
                        type: 'GET',
                        data: { startDate: startDate.toISOString(), endDate: endDate.toISOString() },
                        success: function(response) {
                            announcements = response.announcements;
                            generateCustomDateSlider(startDate, endDate);
                        },
                        error: function(xhr) {
                            alert('An error occurred while fetching activities for the selected date range.');
                        }
                    });
                } else {
                    announcements = @json($activities);
                    generateDateSlider(parseInt(dateRange));
                }
                
                // Filter and display announcements
                filterAnnouncements(branch);
            });
        });

        // Generate date slider items for predefined ranges
        function generateDateSlider(days) {
            const slider = $('#dateSlider');
            slider.empty();
            
            const today = new Date();
            let dates = [];
            
            for (let i = days-1; i >= 0; i--) {
                const date = new Date();
                date.setDate(today.getDate() - i);
                dates.push(date);
            }
            
            dates.forEach(date => {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                
                const monthName = monthNames[date.getMonth()];
                const dayName = dayNames[date.getDay()];
                const dateNum = date.getDate();
                const formattedDate = getFormattedDate(date);
                
                const dateItem = $('<div>').addClass('date-item').html(`
                    <div class="date-month">${monthName}</div>
                    <div class="date-day">${dayName}</div>
                    <div class="date-number">${dateNum}</div>
                `);
                
                // Mark today as active
                if (formattedDate === getFormattedDate(today)) {
                    dateItem.addClass('active');
                }
                
                dateItem.on('click', function() {
                    // Remove active class from all items
                    $('.date-item').removeClass('active');
                    
                    // Add active class to clicked item
                    $(this).addClass('active');
                    
                    // Display announcements for selected date
                    displayAnnouncementsForDate(formattedDate);
                });
                
                slider.append(dateItem);
            });
            
            // Update navigation buttons
            setTimeout(updateNavButtons, 100);
        }

        // Generate date slider for custom date range
        function generateCustomDateSlider(startDate, endDate) {
            const slider = $('#dateSlider');
            slider.empty();
            
            let dates = [];
            const currentDate = new Date(startDate);
            
            while (currentDate <= endDate) {
                dates.push(new Date(currentDate));
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            dates.forEach(date => {
                const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const dayName = dayNames[date.getDay()];
                const monthName = monthNames[date.getMonth()];
                const dateNum = date.getDate();
                const formattedDate = getFormattedDate(date);
                
                const dateItem = $('<div>').addClass('date-item').html(`
                    <div class="date-month">${monthName}</div>
                    <div class="date-day">${dayName}</div>
                    <div class="date-number">${dateNum}</div>
                `);
                
                // Mark today as active if it's in the range
                const today = new Date();
                if (formattedDate === getFormattedDate(today)) {
                    dateItem.addClass('active');
                }
                
                dateItem.on('click', function() {
                    // Remove active class from all items
                    $('.date-item').removeClass('active');
                    
                    // Add active class to clicked item
                    $(this).addClass('active');
                    
                    // Display announcements for selected date
                    displayAnnouncementsForDate(formattedDate);
                });
                
                slider.append(dateItem);
            });
            
            // Activate the first date if none is active
            if ($('.date-item.active').length === 0 && dates.length > 0) {
                $('.date-item:first').addClass('active');
                displayAnnouncementsForDate(getFormattedDate(dates[0]));
            }
            setTimeout(updateNavButtons, 100);
        }

        // Format date as YYYY-MM-DD for input fields
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Format date as YYYY-MM-DD
        function getFormattedDate(date) {
            return formatDateForInput(date);
        }

        // Filter announcements based on branch
        function filterAnnouncements(branch) {
            // Get the active date from the slider
            const activeDateItem = $('.date-item.active');
            if (activeDateItem.length) {
                const dateNum = activeDateItem.find('.date-number').text();
                const dayName = activeDateItem.find('.date-day').text();
                
                // In a real application, you would find the actual date from your data
                // For demo purposes, we'll use today's date
                const filteredDate = getFormattedDate(new Date());
                displayAnnouncements(filteredDate, branch);
            } else if ($('.date-item').length > 0) {
                // If no date is active but we have dates, activate the first one
                $('.date-item:first').addClass('active');
                const firstDate = getFormattedDate(new Date());
                displayAnnouncements(firstDate, branch);
            }
        }

        // Display announcements for a specific date
        function displayAnnouncementsForDate(date) {
            const branch = $('#branchFilter').val();
            displayAnnouncements(date, branch);
        }

        // Display announcements with filtering
        function displayAnnouncements(date, branch) {
            const announcementList = $('#announcementList');
            announcementList.empty();
            
            
            // Filter announcements for the selected date and branch
            const filteredAnnouncements = announcements.filter(ann => {
                const annDate = ann.created_at.split(' ')[0];
                const dateMatch = annDate === date;
                
                let branchMatch = true;
                if (branch !== 'all') {
                    
                    // Check if the announcement's branch includes the selected branch
                    branchMatch = ann.branch.split(',').includes(branch);
                }
                
                return dateMatch && branchMatch;
            });
            
            if (filteredAnnouncements.length === 0) {
                announcementList.html(`
                    <div class="text-center py-4">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No activities for this date and branch</p>
                    </div>
                `);
                return;
            }

            announcementList.html(`
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="70%">Activities</th>
                                <th width="30%">Coaching Types</th>
                            </tr>
                        </thead>
                        <tbody id="announcementTableBody"></tbody>
                    </table>
                `);
            
            filteredAnnouncements.forEach(ann => {
                // Format the date
                const createdDate = new Date(ann.created_at);
                const formattedDate = createdDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                // Format time
                const formattedTime = createdDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                const coachingBadges = ann.coaching_type.split(',').map(type => 
                    `<span class="badge badge-info coaching-badge m-1">${type}</span>`
                ).join('');
                
                const attachmentHtml = ann.attachment ? 
                    `<a href="${ann.attachment}" class="attachment-icon" target="_blank">
                        <i class="fas fa-paperclip"></i> Attachment
                    </a>` : '';
                
                // const announcementItem = $(`
                //     <div class="card mb-3 announcement-item">
                //         <div class="card-body">
                //             <div class="d-flex justify-content-between align-items-start">
                //                 <h6 class="card-title mb-1">${ann.title}</h6>
                //                 <small class="text-muted">${formattedTime}</small>
                //             </div>
                //             ${ann.content}
                //             <div class="d-flex justify-content-between align-items-center mt-2">
                //                 <div>
                //                     ${coachingBadges}
                //                     <small class="text-muted">${formattedDate}</small>
                //                 </div>
                //                 ${attachmentHtml}
                //             </div>
                //         </div>
                //     </div>
                // `);

                const announcementItem = $(`
                    <tr class="announcement-item">
                        <td>
                            <div class="d-flex align-items-start">
                                <h6 class="mb-1">${ann.title}</h6>
                            </div>
                            <div class="content-preview">${ann.content}</div>
                            <div class="mt-2">
                                <small class="text-muted mr-2">${formattedDate}</small>
                                <small class="text-muted">${formattedTime}</small>
                            </div>
                        </td>
                        <td>
                            <div class="coaching-types">
                                ${coachingBadges}
                            </div>
                        </td>
                    </tr>
                `);
                
                $('#announcementTableBody').append(announcementItem);
            });
        }

        // Slider navigation functionality
        $('#sliderPrev').on('click', function() {
            const slider = $('.date-slider');
            slider.animate({scrollLeft: slider.scrollLeft() - 300}, 300);
        });

        $('#sliderNext').on('click', function() {
            const slider = $('.date-slider');
            slider.animate({scrollLeft: slider.scrollLeft() + 300}, 300);
        });

        // Update navigation button states based on scroll position
        function updateNavButtons() {
            const slider = $('.date-slider');
            const scrollLeft = slider.scrollLeft();
            const scrollWidth = slider[0].scrollWidth;
            const clientWidth = slider[0].clientWidth;
            
            $('#sliderPrev').prop('disabled', scrollLeft === 0);
            $('#sliderNext').prop('disabled', scrollLeft >= scrollWidth - clientWidth - 5);
        }

        // Call updateNavButtons when slider scrolls
        $('.date-slider').on('scroll', updateNavButtons);
    </script>
    
@endsection