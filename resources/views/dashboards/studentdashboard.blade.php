@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('css')
<style>
  .exam-card {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      border: 2px solid #ffc107;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      transition: transform 0.3s ease;
  }
  .exam-card:hover {
      transform: translateY(-5px);
  }
  .timer-box {
      background: rgba(0, 0, 0, 0.4);
      border-radius: 12px;
      padding: 15px 30px;
      display: inline-block;
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
  }
  .timer-text {
      font-size: 36px;
      font-weight: 900;
      color: #ffc107;
      letter-spacing: 3px;
      font-family: 'Courier New', Courier, monospace;
      text-shadow: 0 2px 4px rgba(0,0,0,0.6);
  }
  .timer-separator {
      font-size: 30px;
      color: #ffffff;
      vertical-align: text-top;
      margin: 0 5px;
  }
  .pulse-icon {
      animation: pulse 1.5s infinite;
      display: inline-block;
  }
  @keyframes pulse {
      0% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.5; transform: scale(1.3); }
      100% { opacity: 1; transform: scale(1); }
  }
  
  
  .timer-banner {
    background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
    color: white; 
    padding: 12px 15px; 
    text-align: center; 
    font-size: 16px; 
    font-weight: 600; 
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    letter-spacing: 0.5px;
  }
  .timer-banner .time-highlight {
    color: #ffc107; 
    font-size: 19px;
    font-weight: 800;
    margin-left: 8px;
    font-family: 'Courier New', Courier, monospace;
    background: rgba(0,0,0,0.2);
    padding: 2px 8px;
    border-radius: 4px;
  }
  
  @media (max-width: 767px) {
      .timer-text { font-size: 28px; }
      .timer-box { padding: 10px 20px; }
      .timer-banner { font-size: 14px; padding: 10px; }
      .timer-banner .time-highlight { font-size: 16px; }
  }
</style>
@endsection

@section('main')
@php
$user = auth()->user();
$exam = $user->GetExam();
$mockTest = $user->GetMockTest();
$isOffline = in_array($user->coaching_type, ['OFFLINE', 'ONLINE LIVE']);

$isExamActive = $exam && $exam->start_at <= now() && $exam->end_at >= now();
  $isExamUpcoming = $exam && $exam->start_at > now();

  $upcomingTestTime = null;
  $upcomingTestName = '';

  if ($isExamUpcoming) {
  $upcomingTestTime = $exam->start_at->toIso8601String();
  $upcomingTestName = 'Exam';
  } elseif ($mockTest && $mockTest->start_at > now()) {
  $upcomingTestTime = $mockTest->start_at->toIso8601String();
  $upcomingTestName = 'Mock Test';
  }
  @endphp

  <div class="main-content">

    <!-- ============================================== -->
    <!-- COMPACT UPCOMING BANNER -->
    <!-- ============================================== -->
    @if($upcomingTestTime)
    <div x-data="dashboardTimer('{{ $upcomingTestTime }}', '{{ $upcomingTestName }}')" x-init="initTimer()" x-show="isActive" x-cloak class="timer-banner mb-4">
      <span style="font-size: 18px; margin-right: 5px;">⏳</span>
      Upcoming <span x-text="testName" class="text-warning font-weight-bold"></span> starts in <span class="time-highlight" x-text="timeLeft"></span>
    </div>
    @endif

    <div class="section-body">
      <div class="row">

        <!-- Alerts Section -->
        <div class="col-lg-12">
          @if(session('success'))
          <div class="alert alert-success alert-dismissible show fade">
            <div class="alert-body">
              <button class="close" data-dismiss="alert"><span>&times;</span></button>
              <b>{{ session('success') }}</b>
            </div>
          </div>
          @endif

          @if(session('error'))
          <div class="alert alert-danger alert-dismissible show fade">
            <div class="alert-body">
              <button class="close" data-dismiss="alert"><span>&times;</span></button>
              <b>{{ session('error') }}</b>
            </div>
          </div>
          @endif
        </div>

        <!-- ============================================== -->
        <!-- HIGHLIGHTED EXAM TIMER CARDS -->
        <!-- ============================================== -->

        @if($isExamActive)
        <!-- ACTIVE EXAM CARD -->
        <div class="col-lg-12 mb-4">
          <div class="card exam-card">
            <div class="card-body text-center text-white py-5">
              <h2 class="mb-2 font-weight-bold text-uppercase">
                <span class="pulse-icon text-danger mr-2">🔴</span> Live Exam Active
              </h2>
              <h5 class="mb-4 text-light">{{ $exam->name ?? 'Online Assessment' }}</h5>

              <!-- Alpine Timer Component -->
              <div x-data="dashboardTimer('{{ $exam->end_at->toIso8601String() }}')" x-init="initTimer()" class="mb-4">
                <p class="mb-2 text-uppercase" style="font-size: 14px; letter-spacing: 1.5px; color: #d1d5db;">Time Remaining To Submit</p>
                <div class="timer-box">
                  <span class="timer-text" x-text="time.hours">00</span><span class="timer-separator">:</span><span class="timer-text" x-text="time.minutes">00</span><span class="timer-separator">:</span><span class="timer-text" x-text="time.seconds">00</span>
                </div>
              </div>

              <a href="{{ route('student.exam', base64_encode($exam->id)) }}" class="btn btn-primary px-5 mt-2" style="font-size: 20px; font-weight: bold; border-radius: 50px; text-transform: uppercase;">
                Enter Exam <i class="fas fa-arrow-circle-right ml-2"></i>
              </a>
            </div>
          </div>
        </div>

        @elseif($isExamUpcoming)
        <!-- UPCOMING EXAM CARD -->
        <div class="col-lg-12 mb-4">
          <div class="card" style="background: #343a40; border-radius: 15px; border-left: 5px solid #17a2b8; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="card-body text-center text-white py-4">
              <h4 class="mb-2"><i class="fas fa-clock text-info mr-2"></i> Upcoming Exam</h4>
              <h6 class="mb-4 text-light">{{ $exam->name ?? 'Online Assessment' }}</h6>

              <div x-data="dashboardTimer('{{ $exam->start_at->toIso8601String() }}')" x-init="initTimer()" class="mb-2">
                <p class="mb-1 text-uppercase text-muted" style="font-size: 13px; letter-spacing: 1px;">Starts In</p>
                <h2 class="font-weight-bold text-info" style="letter-spacing: 2px;">
                  <span x-text="time.hours">00</span> : <span x-text="time.minutes">00</span> : <span x-text="time.seconds">00</span>
                </h2>
              </div>
            </div>
          </div>
        </div>
        @endif

        <div class="col-xl-4 col-lg-6">
          <div class="card l-bg-green" style="border-radius: 12px;">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large"><i class="fa fa-user"></i></div>
              <div class="card-content">
                <h4 class="card-title">{{ $user->student_name }}</h4>
                <span>{{ $user->dob }}</span>
                <p class="mb-0 text-sm mt-2">
                  <span class="text-nowrap"><strong>Coaching Type:</strong> {{ $user->coaching_type }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Branch & Course Card -->
        <div class="col-xl-4 col-lg-6">
          <div class="card l-bg-purple" style="border-radius: 12px;">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large"><i class="fa fa-building"></i></div>
              <div class="card-content">
                <h4 class="card-title">Branch : {{ $user->branch?->name }}</h4>
                <p class="mb-0 text-sm mt-2"><span class="text-nowrap"><strong>Course:</strong> {{ $user->course }}</span></p>
                <p class="mb-0 text-sm"><span class="text-nowrap"><strong>Section:</strong> {{ $user->section }}</span></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Attendance Card (Offline Only) -->
        @if($isOffline)
        <div class="col-xl-4 col-lg-6">
          <div class="card l-bg-orange" style="border-radius: 12px;">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large"><i class="fa fa-calendar-check"></i></div>
              <div class="card-content">
                <h5 class="card-title">Attendance (Current Month)</h5>
                <span style="font-size: 16px;"><strong>Working Days:</strong> {{ $totalDaysInMonth }}</span>
                <p class="mb-0 text-sm text-nowrap mt-1">
                  <span style="font-size: 16px;"><strong>Present Days :</strong> {{ $presentDaysInMonth }}</span>
                  <span style="font-size: 22px; float: right; font-weight: bold;">{{ number_format($percentage, 2) }}%</span>
                </p>
                <p class="mb-0 text-sm text-nowrap mt-0">
                  <span style="font-size: 16px;"><strong>Absent Days :</strong> {{ $absentDaysInMonth }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Microsoft Teams Card (Online Only) -->
        @if(auth()->user()->coaching_type == 'ONLINE LIVE')
        <div class="col-xl-4 col-lg-6">
          <div class="card" style="background:linear-gradient(135deg,#36a1f2,#016bbc);color:white;border-radius:12px;">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large"><i class="fa fa-users"></i></div>
              <div class="card-content">
                <h5 class="card-title">Microsoft Teams Details</h5>
                <span style="font-size: 16px;"><strong>Teams ID:</strong> {{ $user->teams_id }}</span>
                <p class="mb-0 text-sm text-nowrap mt-1">
                  <span style="font-size: 16px;"><strong>Teams Password:</strong> {{ $user->teams_password }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  @endsection

  @section('js')
  <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardTimer', (targetTime, testName = 'Test') => ({
            isActive: false,
            time: { hours: '00', minutes: '00', seconds: '00' },
            timeLeft: '',
            testName: testName,
            
            initTimer() {
                if (!targetTime) return;
                
                const targetDate = new Date(targetTime).getTime();
                if (isNaN(targetDate)) return;
                
                const updateTime = () => {
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    if (distance <= 0) {
                        this.isActive = false;
                        this.time = { hours: '00', minutes: '00', seconds: '00' };
                        this.timeLeft = '00h 00m 00s';
                        setTimeout(() => location.reload(), 1000); 
                        return true; 
                    }
                    const h = Math.floor(distance / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);
                    const pad = n => String(n).padStart(2, '0');
                    this.time.hours = pad(h);
                    this.time.minutes = pad(m);
                    this.time.seconds = pad(s);
                    this.timeLeft = `${h > 0 ? h + 'h ' : ''}${pad(m)}m ${pad(s)}s`;
                    this.isActive = true;
                    
                    return false;
                };
                if (!updateTime()) {
                    const timerInterval = setInterval(() => {
                        if (updateTime()) clearInterval(timerInterval);
                    }, 1000);
                }
            }
        }));
    });
  </script>
  @endsection