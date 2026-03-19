@extends('layouts.dashboard')
@section('title', 'Profile')
@section('css')
<style>
    :root {
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --radius: 16px;
    }

    .bg-pastel-purple { background-color: #dcd6f7; color: #5b4e8e; }
    .bg-pastel-card   { background-color: #ffffff; }
    
    .card {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        margin-bottom: 28px;
        overflow: hidden;
    }
    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .label-text {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .value-text {
        color: var(--text-dark);
        font-weight: 500;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    .section-header {
        font-weight: 700;
        color: #5b4e8e;
        border-bottom: 2px solid #f3f4f6;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <div class="row">
        <!-- Left Column: Identity & Contact -->
        <div class="col-lg-4">
            <div class="card bg-pastel-card">
                <div class="bg-pastel-purple" style="height: 80px;"></div>
                <div class="card-body text-center mt-n5">
                    <img src="{{ asset('profilepic/'.auth()->user()->student_id.'.jpg') }}"  onerror="this.style.display='none'" class="rounded-circle profile-img mb-3">
                    
                    <h4 class="fw-bold text-dark mb-1">{{ auth()->user()->student_name }}</h4>
                    <p class="text-muted mb-3">{{ auth()->user()->student_id ?? 'Student' }}</p>
                    
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">{{ auth()->user()->gender }}</span>
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">{{ auth()->user()->dob }}</span>
                    </div>

                    <div class="text-start px-3">
                        <h6 class="section-header">Contact Info</h6>
                        
                        <div class="mb-3">
                            <div class="label-text">Mobile</div>
                            <div class="value-text">{{ auth()->user()->ph_no1 }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="label-text">Email</div>
                            <div class="value-text">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="label-text">Address</div>
                            <div class="value-text">
                                {{ auth()->user()->door_no }}, {{ auth()->user()->street_name }}<br>
                                {{ auth()->user()->city }}, {{ auth()->user()->state }} - {{ auth()->user()->pincode }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Details -->
        <div class="col-lg-8">
            <div class="card bg-pastel-card p-4">
                <!-- Personal Details -->
                <div class="mb-5">
                    <h5 class="section-header">Personal Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="label-text">Father's Name</div>
                            <div class="value-text">{{ auth()->user()->father_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="label-text">Father's Phone</div>
                            <div class="value-text">{{ auth()->user()->father_ph_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="label-text">Mother's Name</div>
                            <div class="value-text">{{ auth()->user()->mother_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="label-text">Mother's Phone</div>
                            <div class="value-text">{{ auth()->user()->mother_ph_no }}</div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->course =='NEET' || auth()->user()->course =='JEE')
                <!-- Academic Details -->
                <div>
                    <h5 class="section-header">Academic Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="label-text">Coaching Type</div>
                            <div class="value-text">{{ auth()->user()->coaching_type }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="label-text">XII Board of Study</div>
                            <div class="value-text">{{ auth()->user()->board_of_study_XII_std }}</div>
                        </div>
                        
                        <!-- Marks Chips -->
                        <div class="col-12 mb-3">
                            <div class="label-text mb-2">Class XII Marks</div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-primary p-2 border">Physics: {{ auth()->user()->S2_obtained_mark }}</span>
                                <span class="badge bg-light text-success p-2 border">Chemistry: {{ auth()->user()->S1_obtained_mark }}</span>
                                <span class="badge bg-light text-danger p-2 border">Biology: {{ auth()->user()->S3_obtained_mark }}</span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="label-text">Accommodation</div>
                            <div class="value-text">
                                {{ auth()->user()->hostel_dayscholar }} <span class="text-muted mx-2">|</span> {{ auth()->user()->ac_nonac }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection