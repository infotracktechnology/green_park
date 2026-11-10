<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Room Allotment Details</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 10mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            position: relative;
            width: 100%;
            height: 65px;
            text-align: center;
        }

        .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 75px;
            height: 75px;
        }

        .college-name {
            margin: 7px 0 10px 35px;
            font-size: 21px;
            font-weight: bold;
            white-space: nowrap;
        }

        .report-title {
            margin-left: 35px;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .student-details {
            width: 100%;
            margin-top: 48px;
            border-collapse: collapse;
        }

        .student-details td {
            padding-top: 7px;
            padding-bottom: 7px;
            font-size: 12px;
            font-weight: bold;
            vertical-align: middle;
        }

        .student-details .label {
            width: 39%;
            padding-left: 10px;
        }

        .student-details .colon {
            width: 20px;
            text-align: center;
        }

        .student-details .value {
            width: 58%;
            padding-left: 4px;
        }

        .classroom-section {
            margin-top: 75px;
        }

        .classroom-title {
            margin-bottom: 8px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        .classroom-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .classroom-table th {
            padding: 7px 5px;
            border: 1px solid #333;
            background-color: #cbd2d5;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        .classroom-table td {
            height: 35px;
            padding: 9px 5px;
            border: 1px solid #333;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .chairman {
            margin-top: 72px;
            margin-right: 25px;
            text-align: right;
            page-break-inside: avoid;
        }

        .signature {
            display: block;
            width: 100px;
            height: 35px;
            margin-left: auto;
            margin-bottom: 1px;
        }

        .chairman-name {
            margin-right: 2px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>

<body>

@php
    $academicYear = $student->academic_year ?? '2026-2027';

    $yearParts = explode('-', $academicYear);

    $displayYear = count($yearParts) > 1
        ? trim($yearParts[1])
        : $academicYear;

    $course = strtoupper(trim($student->course ?? 'LONGTERM'));

    $courseLabel = $course === 'LONGTERM'
        ? 'LT'
        : $course;

    $reportTitle = 'DAYSCHOLAR - CLASS ROOM ALLOTMENT DETAILS ('
        . $courseLabel . ' - ' . $displayYear . ')';

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
        "BOARD STUDIED IN XII STD" =>
            $student->board_of_study_XII_std ?? '-',
        "DATE OF ARRIVAL" => !empty($student->date_of_arrival)
            ? \Carbon\Carbon::parse($student->date_of_arrival)
                ->format('d-m-Y (l)')
            : '-',
        "CLASS TIMING" => '08.45 am To 05.00 pm',
    ];
@endphp

<div class="header">
    <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}">
    <div class="college-name"> GREEN PARK COACHING CENTRE, NAMAKKAL </div>
    <div class="report-title"> {{ $reportTitle }} </div>
</div>

<table class="student-details">
    <tbody>
        @foreach($details as $label => $value)
            <tr>
                <td class="label">{{ $label }}</td>
                <td class="colon">:</td>
                <td class="value">{{ $value }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="classroom-section">
    <div class="classroom-title"> CLASS ROOM DETAILS </div>
    <table class="classroom-table">
        <thead>
            <tr>
                <th>Block</th>
                <th>Floor</th>
                <th>Class Room No</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $student->block ?? '-' }}</td>
                <td>{{ $student->floor ?? '-' }}</td>
                <td>{{ $student->class_room_no ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="chairman">
    <img class="signature" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/chairman_sign.jpeg'))) }}" style="width: 80px;">
    <div class="chairman-name"> CHAIRMAN </div>
</div>

</body>
</html>
