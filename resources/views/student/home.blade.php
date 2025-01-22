@extends('layouts.dashboard')

@section('title', 'Dashboard')

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
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-xl-4 col-lg-6">
                <div class="card l-bg-green">
                    <div class="card-statistic-3">
                        <div class="card-icon card-icon-large"><i class="fa fa-user"></i></div>
                        <div class="card-content">
                            <h4 class="card-title">{{ auth()->user()->student_name }}</h4>
                            <span>{{ auth()->user()->dob }}</span>
                            <p class="mb-0 text-sm">
                                <span class="text-nowrap"><strong>Coaching Type :</strong> {{ auth()->user()->coaching_type }}</span>
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
                            <h4 class="card-title">Attendance Count</h4>
                            <span>Total : 270 Days</span>
                            <p class="mb-0 text-sm">
                                <span class="text-nowrap"><strong>Present Days :</strong> 167 Days</span>
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
