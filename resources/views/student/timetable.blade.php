@extends('layouts.dashboard')
@section('title', 'Timetable')  
@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
   
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #007bff;
        color: #fff;
        border-radius: 15px 15px 0 0;
    }

    .card-header h4 {
        font-size: 1.5rem;
    }

    .nav-pills .nav-link {
        border-radius: 10px;
        margin: 0 5px;
        font-weight: bold;
        color: #007bff;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: #fff;
    }

    .nav-pills .nav-link:hover {
        background-color: #0056b3;
        color: #fff;
    }

    .table th, .table td {
        vertical-align: middle;
        padding: 15px;
        text-align: center;
    }

    .table th {
        background-color: #007bff;
        color: #fff;
    }

    .table thead tr {
        border-bottom: 2px solid #007bff;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

    .table tbody tr:hover {
        background-color: #e0e0e0;
    }

    .table td {
        font-size: 1.1rem;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .section-body {
       
        padding: 20px;
        border-radius: 10px;
    }

    .form-group {
        font-size: 1.1rem;
    }

    .font-weight-bold {
        font-weight: bold;
    }

    .text-primary {
        color: #007bff;
    }

    .text-center {
        text-align: center;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card"> <!-- Only one card here -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Class Timetable</h4>
                            <div class="form-group mb-0">
                                <label class="mr-2 mb-0 font-weight-bold text-dark">Section:</label>
                                <span class="font-weight-bold text-primary" style="font-size: 1.1rem;">
                                    {{ Auth::user()->section }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="px-4">
                                <ul class="nav nav-pills mb-3" id="dayTabs" role="tablist">
                                    @foreach($days as $day)
                                        <li class="nav-item">
                                            <a class="nav-link @if($day == $currentDay) active @endif" 
                                               id="tab-{{ strtolower($day) }}" 
                                               data-toggle="tab" 
                                               href="#{{ strtolower($day) }}" 
                                               role="tab" 
                                               aria-controls="{{ strtolower($day) }}" 
                                               aria-selected="{{ $day == $currentDay ? 'true' : 'false' }}">
                                                {{ $day }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content" id="dayTabContent">
                                    @foreach($days as $day)
                                        <div class="tab-pane fade @if($day == $currentDay) show active @endif mb-4" 
                                             id="{{ strtolower($day) }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $day }}">
                                            <div class="table-responsive mt-3">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>Subject</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($timetable[$day] ?? [] as $session)
                                                            <tr>
                                                                <td>{{ $session['period'] ?? '-' }}</td>
                                                                <td>{{ $session['subject'] ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if(empty($timetable[$day]))
                                                            <tr>
                                                                <td colspan="2" class="text-center">No timetable available for {{ $day }}</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
