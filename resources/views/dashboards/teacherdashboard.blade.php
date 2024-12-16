@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('main')

<section class="section">
    <div class="row">
        <div class="col-12">
            <h3><strong>Home</strong></h3>
            <div class="row text-center">
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #0cddf8; color: white;">
                        <i data-feather="user" class="fa-2x mb-2"></i><br>
                        PROFILE
                    </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #0cddf8; color: white;">
                    <i data-feather="clock" class="fa-2x mb-2"></i><br>
                    TIME TABLE
                </a>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-4">
            <a href="#" class="btn btn-lg  w-100" style="padding: 20px;background-color: #0cddf8; color: white;">
                <i data-feather="check-square" class="fa-2x mb-2"></i><br>
                ATTENDENCE MARKUP
            </a>
    </div>
        </div>
    </div>
</div>
</section>

@endsection