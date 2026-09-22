<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hostel Allotment Letter</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 15mm; 
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            color: #000;
            font-weight: bold;
            background: #fff;
            font-size: 11.5px;
            margin: 50px;
        }

        .page-container {
            width: 100%;
            padding: 10px 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .logo-cell {
            width: 75px;
            vertical-align: middle;
            text-align: left;
        }

        .logo-cell img {
            width: 70px;
            height: auto;
            display: block;
        }

        .title-cell {
            text-align: center;
            vertical-align: middle;
            padding-right: 75px; 
        }

        .college-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .report-subtitle {
            font-size: 12.5px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .particulars-table {
            width: 96%;
            margin: 0 auto 18px auto;
            border-collapse: collapse;
        }

        .particulars-table td {
            padding: 3.2px 0;
            vertical-align: middle;
            font-size: 11.5px;
            font-weight: bold;
        }

        .lbl-col {
            width: 38%;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .colon-col {
            width: 4%;
            text-align: center;
        }

        .val-col {
            width: 58%;
            text-transform: uppercase;
        }

        .section-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .room-table {
            width: 96%;
            margin: 0 auto 22px auto;
            border-collapse: collapse;
            text-align: center;
        }

        .room-table th {
            background-color: #cfd8dc;
            border: 1.2px solid #000;
            padding: 5px;
            font-size: 11px;
            font-weight: bold;
            width: 33.33%;
        }

        .room-table td {
            border: 1.2px solid #000;
            padding: 7px 5px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .verify-table {
            width: 96%;
            margin: 0 auto 20px auto;
            border-collapse: collapse;
        }

        .verify-table td {
            vertical-align: middle;
            font-size: 11.5px;
            font-weight: bold;
        }

        .chk-box {
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 1.2px solid #000;
            vertical-align: middle;
            margin-left: 8px;
        }

        /* Remarks */
        .remarks-wrap {
            width: 96%;
            margin: 0 auto 40px auto;
            font-size: 11.5px;
            font-weight: bold;
        }

        .underline-dots {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #000;
            margin-left: 5px;
        }

        /* Footer */
        .footer-table {
            width: 96%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .footer-table td {
            font-size: 13px;
            font-weight: bold;
            vertical-align: bottom;
        }
    </style>
</head>
<body>

<div class="page-container">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}" alt="Logo">
            </td>
            <td class="title-cell">
                <div class="college-title">GREEN PARK INSTITUTE, NAMAKKAL</div>
                <div class="report-subtitle">HOSTEL ALLOTMENT LETTER (LONGTERM - {{ $student->academic_year ?? '2026-2027' }})</div>
            </td>
        </tr>
    </table>

    {{-- STUDENT & ALLOTMENT PARTICULARS --}}
    <table class="particulars-table">
        <tr>
            <td class="lbl-col">STUDENT'S NAME</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->student_name }}</td>
        </tr>
        <tr>
            <td class="lbl-col">GENDER</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->gender }}</td>
        </tr>
        <tr>
            <td class="lbl-col">CLASS</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->class_name ?? 'LONGTERM' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">BATCH</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->batch ?? 'A' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">SECTION</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->section ?? 'RSH11' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">USER ID</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->user_name }}</td>
        </tr>
        <tr>
            <td class="lbl-col">FATHER'S NAME</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->father_name }}</td>
        </tr>
        <tr>
            <td class="lbl-col">FATHER'S MOBILE NO</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->father_ph_no }}</td>
        </tr>
        <tr>
            <td class="lbl-col">MOTHER'S NAME</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->mother_name }}</td>
        </tr>
        <tr>
            <td class="lbl-col">MOTHER'S MOBILE NO</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->mother_ph_no }}</td>
        </tr>
        <tr>
            <td class="lbl-col">BOARD STUDIED IN XII STD</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->XII_BOARD ?? 'SB' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">AC/NONAC</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->AC_NON_AC ?? 'NON AC' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">DATE OF ARRIVAL</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->date_of_arrival ?? '23-08-2026 (SUNDAY)' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">TIME OF ARRIVAL</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->time_of_arrival ?? '02.00 PM TO 04.30 PM' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">HOSTEL NAME</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $student->hostel_name ?? 'GIRLS HOSTEL BLOCK - 2' }}</td>
        </tr>
    </table>

    {{-- ROOM DETAILS --}}
    <div class="section-title">ROOM DETAILS</div>

    <table class="room-table">
        <thead>
            <tr>
                <th>Floor</th>
                <th>Room No</th>
                <th>Cot No</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $student->floor ?? 'FIRST' }}</td>
                <td>{{ $student->room_no ?? '223' }}</td>
                <td>{{ $student->cot_no ?? 'C-2' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- VERIFICATION STATUS --}}
    <table class="verify-table">
        <tr>
            <td style="width: 60%; text-transform: uppercase;">VERIFICATION FORM SUBMITTED</td>
            <td style="width: 20%; text-align: right;">
                YES <span class="chk-box"></span>
            </td>
            <td style="width: 20%; text-align: right;">
                NO <span class="chk-box"></span>
            </td>
        </tr>
    </table>

    {{-- REMARKS --}}
    <div class="remarks-wrap">
        REMARKS : <span class="underline-dots"></span>
    </div>

    {{-- FOOTER SEALS --}}
    <table class="footer-table">
        <tr>
            <td style="text-align: left; width: 50%;">Seal</td>
            <td style="text-align: right; width: 50%;">Incharge</td>
        </tr>
    </table>

</div>

</body>
</html>