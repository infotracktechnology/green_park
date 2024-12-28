@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('css')
<style>
  .announcement-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .announcement-item:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  }
  .card-header {
    background: linear-gradient(45deg, #007bff, #185ae9);
    color: white;
  }
  .card-footer {
    border-top: 1px solid #ddd;
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
                                <span class="text-nowrap"><strong >Coaching Type :</strong> {{ auth()->user()->coaching_type }}</span>
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
                              <h4 class="card-title">Attedence Count</h4>
                              <span>Total : 270 Days</span>
                              <p class="mb-0 text-sm">
                                <span class="text-nowrap"><strong >Present Days :</strong> 167 Days</span>
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-lg-12 col-xl-6">
                          <!-- Announcements Section -->
                          <div class="card shadow-lg border-0">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                              <h4 class="mb-0 text-white">Latest Announcements</h4>
                              <form class="card-header-form">
                                <!-- Optional: Add search or filter functionality here -->
                              </form>
                            </div>
                            <div class="card-body">
                              @forelse(auth()->user()->announcement()->latest()->get() as $announcement)
                              <div class="announcement-item media mb-4 p-3 border rounded">
                                <div class="me-3">
                                  <img src="{{ asset('img/announcement.jpg') }}" alt="Announcement Image" class="img-fluid rounded-circle border" width="60">
                                </div>
                                <div class="media-body">
                                  <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                      <span class="text-muted small">Announcement #{{ $announcement->id }}</span>
                                      <h5 class="mt-1 mb-2 text-primary" style="font-weight: bold; ">{{ $announcement->title }}</h5>
                                    </div>
                                    <span class="badge rounded-pill" style="background-color: {{ $announcement->category == 'Important' ? '#dc3545' : '#28a745' }}; color: #ffffff; font-weight: bold;">{{ $announcement->category }}</span>
                                  </div>
                                </div>
                              </div>
                              @empty
                              <div class="alert alert-info text-center">
                                <strong>No Announcements</strong>
                              </div>
                              @endforelse
                            </div>
                            <div class="card-footer text-center bg-light">
                              <a href="{{ route('student.notification') }}" class="btn btn-primary btn-sm">View All Announcements</a>
                            </div>
                          </div>
                          <!-- Announcements Section -->
                        </div>
                      </div>
                    </div>
                  </div>
@endsection