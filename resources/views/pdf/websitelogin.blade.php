<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Login Details</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
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
            margin: 30px;
        }

        .card-container {
            width: 100%;
            padding: 15px 25px 25px 25px;
            background: #fff;
            padding-bottom: 40px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .header-logo-col {
            width: 80px;
            vertical-align: middle;
            text-align: left;
        }

        .header-logo-col img {
            width: 75px;
            height: auto;
            display: block;
        }

        .header-title-col {
            text-align: center;
            vertical-align: middle;
            padding-right: 75px;
        }

        .college-title {
            font-size: 19px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .report-subtitle {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .details-table td {
            padding: 5.5px 2px;
            vertical-align: middle;
            font-size: 12px;
            font-weight: bold;
        }

        .lbl-left {
            width: 18%;
            white-space: nowrap;
        }

        .colon {
            width: 2%;
            text-align: center;
        }

        .val-left {
            width: 33%;
            text-transform: uppercase;
            padding-right: 15px;
        }

        .lbl-right {
            width: 16%;
            white-space: nowrap;
        }

        .val-right {
            width: 29%;
            text-transform: uppercase;
        }

        .login-box-table {
            width: 52%;
            margin: 15px auto 0 auto;
            border: 1.2px solid #000;
            border-collapse: collapse;
            text-align: center;
        }

        .website-title {
            font-size: 12.5px;
            font-weight: bold;
            text-align: center;
            padding-top: 15px;
            padding-bottom: 12px;
        }

        .cred-table {
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .cred-table td {
            padding: 4px 6px;
            font-size: 16px;
            font-weight: bold;
            vertical-align: middle;
        }

        .cred-lbl {
            text-align: left;
            white-space: nowrap;
        }

        .cred-colon {
            text-align: center;
            padding: 0 5px;
        }

        .cred-val {
            text-align: left;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
    </style>
</head>
<body>

<div class="card-container">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="header-logo-col">
                <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}" alt="Logo">
            </td>
            <td class="header-title-col">
                <div class="college-title">GREEN PARK COACHING CENTRE, NAMAKKAL</div>
                <div class="report-subtitle">LONGTERM - WEBSITE LOGIN DETAILS - {{ $student->academic_year ?? '2026-2027' }}</div>
            </td>
        </tr>
    </table>

    {{-- STUDENT DETAILS --}}
    <table class="details-table">
        <tr>
            <td class="lbl-left">STUDENT NAME</td>
            <td class="colon">:</td>
            <td class="val-left">{{ $student->student_name }}</td>

            <td class="lbl-right">GENDER</td>
            <td class="colon">:</td>
            <td class="val-right">{{ $student->gender }}</td>
        </tr>
        <tr>
            <td class="lbl-left">FATHER NAME</td>
            <td class="colon">:</td>
            <td class="val-left">{{ $student->father_name }}</td>

            <td class="lbl-right">PHONE NO 1</td>
            <td class="colon">:</td>
            <td class="val-right">{{ $student->father_ph_no }}</td>
        </tr>
        <tr>
            <td class="lbl-left">MOTHER NAME</td>
            <td class="colon">:</td>
            <td class="val-left">{{ $student->mother_name }}</td>

            <td class="lbl-right">PHONE NO 2</td>
            <td class="colon">:</td>
            <td class="val-right">{{ $student->mother_ph_no }}</td>
        </tr>
        <tr>
            <td class="lbl-left">XII BOARD</td>
            <td class="colon">:</td>
            <td class="val-left">{{ $student->XII_BOARD }}</td>

            <td class="lbl-right">HOS/DAY</td>
            <td class="colon">:</td>
            <td class="val-right">{{ $student->hostel_dayscholar }}</td>
        </tr>
        @if(strtoupper(trim($student->hostel_dayscholar ?? 'HOSTEL')) === 'HOSTEL')
        <tr>
            <td class="lbl-left">AC / NON AC</td>
            <td class="colon">:</td>
            <td class="val-left">{{ $student->AC_NON_AC ?? 'NON AC' }}</td>

            <td class="lbl-right"></td>
            <td class="colon"></td>
            <td class="val-right"></td>
        </tr>
        @endif
    </table>

    {{-- LOGIN BOX --}}
    <table class="login-box-table">
        <tr>
            <td>
                <div class="website-title">
                    Website URL : www.gpccnamakkal.com
                </div>

                <table class="cred-table">
                    <tr>
                        <td class="cred-lbl">User ID</td>
                        <td class="cred-colon">:</td>
                        <td class="cred-val">{{ $student->user_name }}</td>
                    </tr>
                    <tr>
                        <td class="cred-lbl">Password</td>
                        <td class="cred-colon">:</td>
                        <td class="cred-val">{{ $student->password }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>

</body>
</html>