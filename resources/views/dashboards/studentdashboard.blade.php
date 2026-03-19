@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section("meta")
<meta http-equiv="refresh" content="60">
@endsection

@section('main')
@php
    $user = auth()->user();
    $exam = $user->GetExam();
    $isOffline = $user->coaching_type == 'OFFLINE';

    $isNeetJeeOffline = in_array($user->course, ['NEET', 'JEE']) && $user->coaching_type != 'OFFLINE';
    $isObCourse = in_array($user->course, ['XI-OB', 'XII-OB']);
    $showUploads = $isNeetJeeOffline || $isObCourse;
    $needsPan = empty($user->neet_confirmationpan);
    $needsPhoto = empty($user->neet_photo);
@endphp

<div class="main-content">
  <div class="section-body">
    <div class="row">

      <!-- Alerts Section -->
      <div class="col-lg-12">
        @if($exam && $exam->end_at >= now())
          <div class="alert alert-warning">
            <b>⚠️ If the Exam Link Doesn't Work, Please Click <a class="font-weight-bold" href="{{ route('student.instruction', base64_encode($exam->id)) }}">here</a></b>
          </div>
        @endif

        @if(session('success')) 
        <div class="alert alert-success">
          <b>{{ session('success') }}</b>
        </div> 
        @endif

        @if(session('error')) 
        <div class="alert alert-danger">
          <b>{{ session('error') }}</b>
        </div> 
        @endif
      </div>

      <!-- Student Profile Card -->
      <div class="col-xl-4 col-lg-6">
        <div class="card l-bg-green">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large"><i class="fa fa-user"></i></div>
            <div class="card-content">
              <h4 class="card-title">{{ $user->student_name }}</h4>
              <span>{{ $user->dob }}</span>
              <p class="mb-0 text-sm">
                <span class="text-nowrap"><strong>Coaching Type:</strong> {{ $user->coaching_type }}</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Branch & Course Card -->
      <div class="col-xl-4 col-lg-6">
        <div class="card l-bg-purple">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large"><i class="fa fa-building"></i></div>
            <div class="card-content">
              <h2 class="card-title">Branch : {{ $user->branch?->name }}</h2>
              <p class="mb-0 text-sm"><span class="text-nowrap"><strong>Course:</strong> {{ $user->course }}</span></p>
              <p class="mb-0 text-sm"><span class="text-nowrap"><strong>Section:</strong> {{ $user->section }}</span></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Attendance Card (Offline Only) -->
      @if($isOffline)
      <div class="col-xl-4 col-lg-6">
        <div class="card l-bg-orange">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large"><i class="fa fa-calendar-check"></i></div>
            <div class="card-content">
              <h5 class="card-title">Attendance (Current Month)</h5>
              <span style="font-size: 17px;"><strong>Working Days:</strong> {{ $totalDaysInMonth }}</span>
              <p class="mb-0 text-sm text-nowrap">
                <span style="font-size: 17px;"><strong>Present Days :</strong> {{ $presentDaysInMonth }}</span> 
                <span style="font-size: 24px; float: right;">({{ number_format($percentage, 2) }}%)</span>
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
