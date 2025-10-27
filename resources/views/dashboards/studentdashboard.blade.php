@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section("meta")
<meta http-equiv="refresh" content="60">
@endsection

@section('css')
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">

      <div class="col-lg-12">
       @if(auth()->user()->GetExam())
        <div class="alert alert-danger">
          <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <b>⚠️ If the Exam Link Doesn't Work, Please logout and login again.</b>
        </div>
        @endif
        
        @if(session('success'))
        <div class="alert alert-success"><b>{{ session('success') }}</b></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger"><b>{{ session('error') }}</b></div>
        @endif
      </div>



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
              <span style="font-size: 17px;"><strong>Working Days:</strong> {{ $totalDaysInMonth }}</span>

              <p class="mb-0 text-sm">
                <span class="text-nowrap">

                  <span style="font-size: 17px;"> <strong>Present Days :</strong> {{ $presentDaysInMonth }}</span> <span style="font-size: 24px;float: right"> ({{ number_format($percentage, 2) }}%)
                  </span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
@section('js')
@endsection