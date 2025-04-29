@extends('layouts.dashboard')

@section('title', 'Attendance')

@section('css')
<style>
    .card-header {
        background: linear-gradient(to right, #007bff, #0056b3);
        color: #fff;
        font-weight: 600;
        font-size: 1.3rem;
        text-align: center;
    }

    .table thead th {
        background-color: #343a40;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 1px;
    }

    .table tbody td {
        font-size: 0.95rem;
        font-weight: 500;
        color: #333;
        vertical-align: middle;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: #e9f5ff;
    }

    .summary-box {
        font-size: 1.05rem;
        font-weight: 500;
        background-color: #eef5ff;
        border: 1px solid #d0e3ff;
        color: #333;
    }

    .summary-box span {
        color: #007bff;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.85rem;
        }

        .summary-box {
            font-size: 0.95rem;
        }
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="card shadow-sm rounded">
                        <div class="card-header">
                            Attendance Summary
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Present Days</th>
                                            <th>Percentage (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendance as $data)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $data->month)->format('F Y') }}</td>
                                                <td>
                                                    {{ fmod($data->present_days, 1) == 0 ? (int) $data->present_days : number_format($data->present_days, 1) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $monthPercentage = $data->total_days > 0 ? ($data->present_days / $data->total_days) * 100 : 0;
                                                    @endphp
                                                    {{ fmod($monthPercentage, 1) == 0 ? (int) $monthPercentage : number_format($monthPercentage, 2) }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                            </div>
                        
                            <div class="mt-3 p-3 rounded summary-box text-center">
                                <strong>Present / Working Days:</strong>
                                <span>{{ $total_present }} / {{ $total_days }}</span> &nbsp;&nbsp;
                                ( <span>{{ $percentage }}%</span> )
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
