@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('main')

<section class="section">
    <div class="row">
        <div class="col-12">
            <marquee behavior="scroll" direction="left" style="background-color: #e61515cb; padding: 10px; color: white; font-weight: bold;">****** Today is {{ date('l, F jS') }} and your upcoming class is :CHEMISTRY *****</marquee>
            <h3><strong>Your Dashboard !</strong></h3>
            <div class="row text-center">
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #0cddf8; color: white;">
                        <i data-feather="user" class="fa-2x mb-2"></i><br>
                        PROFILE
                    </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #fa0f0f; color: white;">
                    <i data-feather="clock" class="fa-2x mb-2"></i><br>
                    TIME TABLE
                </a>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-4">
            <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color:#a502c5; color: white;">
                <i data-feather="check-square" class="fa-2x mb-2"></i><br>
                ATTENDENCE MARKUP
            </a>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-4">
        <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #fa5103; color: white;">
            <i data-feather="book" class="fa-2x mb-2"></i><br>
            NOTES
        </a>
    </div>
        </div>
    </div>
</div>
</section>

@endsection