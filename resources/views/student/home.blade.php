@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('css')
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
                            <div class="card-icon card-icon-large"><i class="fa fa-award"></i></div>
                            <div class="card-content">
                              <h4 class="card-title">Section Type : A </h4>
                              <span>Grade A</span>
                             
                              <p class="mb-0 text-sm">
                                
                                <span class="text-nowrap"><strong >Level : </strong> Good</span>
                              </p>
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
                        <!-- Support tickets -->
                        <div class="card">
                          <div class="card-header">
                            <h4>Announcements</h4>
                            <form class="card-header-form">
                             
                            </form>
                          </div>
                          <div class="card-body">
                            @forelse(auth()->user()->announcement()->get() as $announcement)
                            <div class="support-ticket media pb-1 mb-3 d-flex">
                              <div class="flex-1 ms-3">
                                <div class="badge badge-pill badge-success ml-4 float-right">{{ $announcement->category }}</div>
                                <span class="fw-bold">#{{ $announcement->id }}</span>
                                <span>{{ $announcement->title }}</span>
                          </div>
                            </div>
                            @empty
                            <div class="support-ticket media pb-1 mb-3 d-flex">
                              <div class="flex-1 ms-3">
                                <span class="fw-bold">No Announcements</span>
                              </div>
                            </div>
                            @endforelse
                          </div>
                          <a href="{{ route('student.notification') }}" class="card-footer card-link text-center small ">View
                            All</a>
                        </div>
                        <!-- Support tickets -->
                      </div>
                      </div>
                    </div>
                  </div>
@endsection