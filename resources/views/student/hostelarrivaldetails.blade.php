
@extends('layouts.dashboard')

@section('title', 'Hostel Arrival Details')

@section('css')
<style>
    .hostel-arrival-card {
        border-radius: 8px;
        overflow: hidden;
    }

    .hostel-arrival-card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .hostel-arrival-card .card-header h4 {
        margin: 0;
    }

    .hostel-heading {
        text-align: center;
        margin-bottom: 30px;
    }

    .hostel-heading h4 {
        font-weight: 700;
        color: #111827;
        margin-bottom: 10px;
    }

    .hostel-heading h6 {
        font-weight: 700;
        color: #374151;
        margin-bottom: 0;
    }

    .hostel-details-table th {
        width: 38%;
        background-color: #f8fafc;
        font-weight: 600;
        vertical-align: middle;
    }

    .hostel-details-table td {
        vertical-align: middle;
    }

    .hostel-details-table .colon-column {
        width: 35px;
        text-align: center;
        font-weight: 700;
    }

    .hostel-note {
        margin-top: 25px;
        padding: 15px;
        border: 1px solid #9ca3af;
        color: #111827;
        line-height: 1.8;
        font-size: 13px;
    }

    .hostel-note p {
        margin: 5px 0 0 20px;
        font-weight: 600;
    }

    .arrival-update-message {
        padding: 50px 20px;
        text-align: center;
    }

    .classroom-heading {
        text-align: center;
        font-weight: 700;
        margin: 65px 0 8px;
        color: #111827;
    }

    .classroom-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .classroom-table th,
    .classroom-table td {
        border: 1px solid #333 !important;
        text-align: center;
        vertical-align: middle;
        padding: 8px;
        color: #000;
    }

    .classroom-table thead th {
        background-color: #cbd2d5;
        font-weight: 700;
        font-size: 12px;
    }

    .classroom-table tbody td {
        font-weight: 700;
    }

    .chairman-signature {
        margin-top: 65px;
        text-align: right;
        padding-right: 30px;
        color: #000;
    }

    .chairman-signature img {
        max-width: 110px;
        max-height: 55px;
        object-fit: contain;
        display: block;
        margin-left: auto;
        margin-bottom: 3px;
    }

    .chairman-signature strong {
        display: block;
        font-size: 14px;
    }

    @media print {
        .no-print,
        .card-header {
            display: none !important;
        }

        .card,
        .card-body {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card answer-key-card hostel-arrival-card">
                    <div class="card-header no-print">
                        <h4><i class="fas fa-calendar-check" style="font-size: 28px; margin-right: 8px;"></i> Arrival Details </h4>
                    </div>

                    @if(!$student)
                        <div class="card-body">
                            <div class="arrival-update-message">
                                <i class="fas fa-info-circle text-warning" style="font-size: 45px;"></i>
                                <h4 class="mt-3">Arrival Details</h4>
                                <p class="text-muted mb-0">
                                    We will update soon.
                                </p>
                            </div>
                        </div>
                    @else
                        @php
                            $studentType = strtoupper(trim($student->hostel_dayscholar ?? ''));
                            $isHosteler = in_array( $studentType, ['HOSTEL', 'HOSTELER', 'HOSTELLER']);
                            $academicYear = $student->academic_year ?? '2026-2027';

                            $details = [
                                "STUDENT'S NAME" => $student->student_name ?? '-',
                                "GENDER" => $student->gender ?? '-',
                                "CLASS" => $student->course ?? 'LONGTERM',
                                "BATCH" => $student->batch ?? '-',
                                "SECTION" => $student->section ?? '-',
                                "USER ID" => $student->student_id ?? '-',
                                "FATHER'S NAME" => $student->father_name ?? '-',
                                "FATHER'S MOBILE NO" => $student->father_ph_no ?? '-',
                                "MOTHER'S NAME" => $student->mother_name ?? '-',
                                "MOTHER'S MOBILE NO" => $student->mother_ph_no ?? '-',
                                "BOARD STUDIED IN XII STD" => $student->board_of_study_XII_std ?? '-',
                            ];

                            if ($isHosteler) {
                                $details["AC/NONAC"] = $student->ac_nonac ?? '-';
                                $details["DATE OF ARRIVAL"] = $student->date_of_arrival ?? '-';
                                $details["TIME OF ARRIVAL"] = $student->time_of_arrival ?? '-';
                            } else {
                                $details["DATE OF ARRIVAL"] = $student->date_of_arrival ?? '-';
                                $details["CLASS TIMING"] = $student->class_timing ?? '-';
                            }

                            $branchName = $student->branch?->name ?? '';
                            $parts = explode(',', $branchName, 2);

                            $city = trim($parts[1] ?? $parts[0]);

                            if (strtoupper($city) === 'NKL') {
                                $city = 'NAMAKKAL';
                            }
                        @endphp

                        <div class="card-body">
                            <div class="hostel-heading">
                               @if($student->campus == 1 && $student->gender == 'MALE')
                                    <div class="college-title">GREEN PARK COACHING CENTRE, NAMAKKAL</div>
                                @elseif($student->campus == 1 && $student->gender == 'FEMALE')
                                    <div class="college-title">GREEN PARK INSTITUTE, NAMAKKAL</div>
                                @else
                                <div class="college-title">GREEN PARK CAREER INSTITUTE, {{ $city }}</div>
                                @endif
                                <h6>
                                    @if($isHosteler)
                                        HOSTEL ARRIVAL DETAILS
                                    @else
                                        DAYSCHOLAR - CLASS ROOM ALLOTMENT DETAILS
                                    @endif
                                    ({{ $academicYear }})
                                </h6>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered hostel-details-table">
                                    <tbody>
                                        @foreach($details as $label => $value)
                                            <tr>
                                                <th>{{ $label }}</th>
                                                <td class="colon-column">:</td>
                                                <td>{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($isHosteler)
                                <div class="hostel-note">
                                    <strong>Note:</strong>
                                    <p> Hostel Allotment Letter will be issued to the students only after submitting DD for First term hostel fee and First term Coaching fee. </p>
                                </div>
                                <div class="text-center mt-4 no-print">
                                    <a href="{{ route('student.hostel-arrival-details', ['download' => 1]) }}" class="btn btn-primary"> <i class="fas fa-file-download"></i> Download PDF </a>
                                </div>

                            @else
                                <h6 class="classroom-heading"> CLASS ROOM DETAILS </h6>
                                <div class="table-responsive">
                                    <table class="table classroom-table">
                                        <thead>
                                            <tr>
                                                <th>Block</th>
                                                <th>Floor</th>
                                                <th>Class Room No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td> {{ $student->block ?? '-' }} </td>
                                                <td> {{ $student->floor ?? '-' }} </td>
                                                <td> {{ $student->class_room_no ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-4 no-print">
                                    <a href="{{ route('student.hostel-arrival-details', ['download' => 1]) }}" class="btn btn-primary"> <i class="fas fa-file-pdf"></i> Download PDF </a>
                                </div>

                            @endif

                        </div>

                    @endif

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
