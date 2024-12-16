@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('main')

<section class="section">
    <div class="row">
        <div class="col-12">
            <h3><strong>Home</strong></h3>
            <div class="row text-center">
                <!-- Profile Icon -->
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #0cddf8; color: white;">
                        <i class="fas fa-user fa-2x mb-2"></i><br>
                        PROFILE
                    </a>
                </div>
                <!-- Class Videos -->
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #fa0f0f; color: white;">
                        <i class="fas fa-video fa-2x mb-2"></i><br>
                        CLASS VIDEOS
                    </a>
                </div>
                <!-- Exam Portions -->
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #a502c5; color: white;">
                        <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                        EXAM PORTIONS
                    </a>
                </div>
                <!-- Question Paper -->
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #fa6806; color: white;">
                        <i class="fas fa-file fa-2x mb-2"></i><br>
                        QUESTION PAPER
                    </a>
                </div>
                <!-- Answer Key -->
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #fa5103; color: white;">
                        <i class="fas fa-key fa-2x mb-2"></i><br>
                        ANSWER KEY
                    </a>
                </div>
                <!-- Downloads -->
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px; background-color: #0a1af1; color: white;">
                        <i class="fas fa-download fa-2x mb-2"></i><br>
                        DOWNLOADS
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
