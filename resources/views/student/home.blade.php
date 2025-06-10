@extends('layouts.dashboard')

@section('title', 'Dashboard')


@section("meta")
<meta http-equiv="refresh" content="60">
@endsection

@section('css')
<style>
  .announcement-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #ffffff;
    border-left: 5px solid #007bff;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 15px;
  }
  .announcement-item:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }
  .card-header {
    padding: 15px 20px;
    font-size: 18px;
    font-weight: bold;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    border-radius: 10px 10px 0 0;
  }
  .card-footer {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 0 0 10px 10px;
    text-align: center;
  }
  .card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
  }
  .l-bg-green {
    background: linear-gradient(to right, #56ab2f, #a8e063);
    color: #fff;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
  }
  .l-bg-purple {
    background: linear-gradient(to right, #614385, #516395);
    color: #fff;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
  }
  .l-bg-orange {
    background: linear-gradient(to right, #f09819, #ff512f);
    color: #fff;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
  }
</style>
<style>
  /* ... (Your existing styles) ... */

    /* Scrolling Message */
    .scrolling-text {
        white-space: nowrap;
        overflow: hidden;
        width: 100%;
        display: block;
        font-size: 18px;
        font-weight: bold;
        color: white;
        background: #ff5733; /* Or any color you prefer */
        padding: 10px;
        text-align: center;
        position: fixed; /* Or relative if you don't want it fixed */
        top: 0;
        left: 0;
        z-index: 1000; /* Ensure it's on top */
    }

    /* Timer Styling */
    #exam-timer {
        font-size: 20px;
        font-weight: bold;
        color: red;
        text-align: center;
        margin-top: 10px; /* Adjust as needed */
    }

    .hidden {
        display: none;
    }
  </style>
  <style>
    .scrolling-text {
        white-space: nowrap;
        overflow: hidden;
        width: 100%;
        display: block;
        font-size: 18px;
        font-weight: bold;
        color: white;
        background: #ff5733; /* Or any color you prefer */
        padding: 10px;
        text-align: center;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000; /* Ensure it's on top */
        animation: scrollText 10s linear infinite;
    }

    @keyframes scrollText {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
</style>

<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
    .scrolling-text {
        position: fixed;
        width: 100%;
        top: 10px;
        background-color: red;
        color: white;
        padding: 10px;
        font-size: 18px;
        white-space: nowrap;
        overflow: hidden;
        display: none; /* Hidden by default */
    }
    .scrolling-text span {
        font-weight: bold;
    }
    .scrolling-container {
        width: 100%;
        overflow: hidden;
    }
    .scrolling-content {
        display: inline-block;
        white-space: nowrap;
        animation: scroll-left 10s linear infinite;
    }
    @keyframes scroll-left {
        from { transform: translateX(100%); }
        to { transform: translateX(-100%); }
    }
</style>
@endsection

@section('main')

<div class="main-content">

    <div class="section-body">
        <div class="row">
           
        
            <div class="col-lg-12">
                <div id="exam-alert" class="alert alert-danger alert-dismissible fade show" style="display: none;">
                    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <b>⚠️ If the Exam Link Doesn't Work, Please logout and login again.</b>
                </div>
                @if(session('success'))
                    <div class="alert alert-success"><b>{{ session('success') }}</b></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger"><b>{{ session('error') }}</b></div>
                @endif
            </div>
            
           
            
            {{-- <marquee behavior="scroll" direction="left" id="marquee" style="display: none;">
                Exam starts in <span id="exam-timer"></span>
            </marquee> --}}

            <div class="col-xl-4 col-lg-6">
                <div class="card l-bg-green">
                    <div class="card-statistic-3">
                        <div class="card-icon card-icon-large"><i class="fa fa-user"></i></div>
                        <div class="card-content">
                            <h4 class="card-title">{{ auth()->user()->student_name }}</h4>
                            <span>{{ auth()->user()->dob }}</span>
                            <p class="mb-0 text-sm">
                                <span class="text-nowrap"><strong>Coaching Type:</strong> {{ auth()->user()->coaching_type }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6">
                <div class="card l-bg-purple">
                    <div class="card-statistic-3">
                        <div class="card-icon card-icon-large"><i class="fa fa-building"></i></div>
                        <div class="card-content">
                            <h2 class="card-title">Branch : {{ auth()->user()->branch ? auth()->user()->branch->name : 'No Branch Assigned' }}</h2>
                        </div>
                    </div>
                </div>
            </div>
         

                <div class="col-xl-4 col-lg-6">
             <div class="card l-bg-orange">
            <div class="card-statistic-3">
               <div class="card-icon card-icon-large"><i class="fa fa-calendar-check"></i></div>
                <div class="card-content">
                 <h5 class="card-title">Attendance (Current Month)</h5>
                     <span style="font-size: 17px;"><strong>Working Days:</strong> {{ $totalDays }}</span>

                     <p class="mb-0 text-sm">
                         <span class="text-nowrap">
                        
                      <span style="font-size: 17px;">  <strong>Present Days :</strong> {{ $presentDays }}</span> <span style="font-size: 24px;float: right"> ({{ number_format($attendancePercentage, 2) }}%)
                     </span>
                    </p>
                </div>
                 </div>
                    </div>
                    </div>
            </div>
        {{-- <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-6">
                <!-- Announcements Section -->
                <div class="card">
                    <div class="card-header">
                        Latest Announcements
                    </div>
                    <div class="card-body">
                        @forelse(auth()->user()->announcement()
                        ->where('branch', auth()->user()->campus)
                        ->orWhere('branch', 'All')
                        ->where('coaching_type', auth()->user()->coaching_type)
                        ->orWhere('coaching_type', 'All')
                        ->where('gender', auth()->user()->gender)
                        ->orWhere('gender', 'All')
                        ->latest()->take(5)
                        ->get() as $announcement)
                        
                        @if(
                            ($announcement->branch === auth()->user()->campus || $announcement->branch === 'All') &&
                            ($announcement->coaching_type === auth()->user()->coaching_type || $announcement->coaching_type === 'All') &&
                            ($announcement->gender === auth()->user()->gender || $announcement->gender === 'All') &&
                            ($announcement->coaching_type !== 'Offline' || ($announcement->coaching_type === 'Offline' && ($announcement->category === 'All'|| $announcement->category === null || $announcement->category === auth()->user()->hostel_dayscholar)))
                        )
                        <div class="announcement-item media mb-4">
                            <div class="me-3">
                                <img src="{{ asset('img/announcement.jpg') }}" alt="Announcement Image" class="img-fluid rounded-circle border" width="60">
                            </div>
                            <div class="media-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="text-muted small">Announcement #{{ $announcement->id }}</span>
                                        <h5 class="mt-1 mb-2 text-primary" style="font-weight: bold;">{{ Latest Announcements$announcement->title }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <div class="alert alert-info text-center">
                            <strong>No Announcements</strong>
                        </div>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('student.notification') }}" class="btn btn-primary btn-sm">View All Announcements</a>
                    </div>
                </div>
                <!-- Announcements Section -->
            </div>
        </div> --}}
    </div>
</div>

@endsection
@section('js')

{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        var examStartTime = "{{ $examStartTime ?? '' }}"; // Get exam start time from backend
    
        if (!examStartTime) {
            console.log("No upcoming exams.");
            return;
        }
    
        var examStart = new Date(examStartTime); 
        var timerElement = document.getElementById('exam-timer');
        var marqueeElement = document.getElementById('marquee');
    
        function updateTimer() {
            var now = new Date();
            var timeDiff = examStart - now;
    
            if (timeDiff <= 0) {
                timerElement.innerHTML = "Exam has started!";
                marqueeElement.style.display = "none"; // Hide scrolling message
                clearInterval(timerInterval);
                return;
            }
    
            // Calculate hours, minutes, seconds
            var hours = Math.floor(timeDiff / (1000 * 60 * 60));
            var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
    
            var timeString = (hours > 0 ? hours + "h " : "") +
                             (minutes > 0 ? minutes + "m " : "") +
                             seconds + "s";
    
            timerElement.innerHTML = timeString;
            marqueeElement.innerHTML = " Exam starts in " + timeString + " Get Ready!";
            marqueeElement.style.display = "block"; // Show scrolling message
        }
    
        var timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Run immediately
    });
    </script> --}}


    {{-- <script>
        document.addEventListener("DOMContentLoaded", function () {
    var examStartTime = "{{ $examStartTime ?? '' }}"; // Get exam start time from backend

    if (!examStartTime) {
        console.log("No upcoming exams.");
        return;
    }

    var examStart = new Date(examStartTime).getTime();
    var timerElement = document.getElementById('exam-timer');
    var timerContainer = document.getElementById('exam-timer-container');
    var examAlert = document.getElementById('exam-alert');

    function updateTimer() {
        var now = new Date().getTime();
        var timeDiff = examStart - now;

        if (timeDiff <= 0) {
            timerElement.innerHTML = "Exam has started!";
            timerContainer.style.display = "none"; // Hide text when exam starts
            examAlert.style.display = "none"; // Hide alert when exam starts
            clearInterval(timerInterval);
            return;
        }

        var hours = Math.floor(timeDiff / (1000 * 60 * 60));
        var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

        var timeString = (hours > 0 ? hours + "h " : "") + 
                         (minutes > 0 ? minutes + "m " : "") + 
                         seconds + "s";

        timerElement.innerHTML = timeString;
        timerContainer.style.display = "inline"; // Show text when exam is upcoming
        //examAlert.style.display = "block"; // Show alert before exam
    }

    var timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Run immediately on page load
});

        </script> --}}

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var examStartTime = "{{ $examStartTime ?? '' }}"; // Get exam start time from backend
        
                if (!examStartTime) {
                    console.log("No upcoming exams.");
                    return;
                }
        
                var examStart = new Date(examStartTime).getTime();
                var now = new Date().getTime();
                var timeDiff = examStart - now;
                var examAlert = document.getElementById('exam-alert');
        
                // Show alert if the exam has not yet started
                if (timeDiff > 0) {
                    examAlert.style.display = "block";
                } else {
                    examAlert.style.display = "none";
                }
            });
        </script>
       
        
@endsection


