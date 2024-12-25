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
                            <div class="support-ticket media pb-1 mb-3 d-flex">
                             
                              <div class="flex-1 ms-3">
                                
                                <span class="fw-bold">#89754</span>
                                <a href="javascript:void(0)">Please add advance table</a>
                                <p class="my-1">Hi, can you please add new table for advan...</p>
                                
                              </div>
                            </div>
                            <div class="support-ticket media pb-1 mb-3 d-flex">
                             
                              <div class="flex-1 ms-3">
                              
                                <span class="fw-bold">#85784</span>
                                <a href="javascript:void(0)">Are you provide template in Angular?</a>
                                <p class="my-1">can you provide template in latest angular 8.</p>
                          
                              </div>
                            </div>
                            <div class="support-ticket media pb-1 mb-3 d-flex">
                              
                              <div class="flex-1 ms-3">
                                
                                <span class="fw-bold">#25874</span>
                                <a href="javascript:void(0)">About template page load speed</a>
                                <p class="my-1">Hi, John, can you work on increase page speed of template...</p>
                               
                              </div>
                            </div>
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