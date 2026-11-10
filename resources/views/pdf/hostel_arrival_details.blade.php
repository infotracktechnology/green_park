<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hostel Allotment Details</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 45px 50px 45px 50px;
        }

        body {
            font-family: Arial, "DejaVu Sans", sans-serif;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h2 {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
        }

        .header h3 {
            font-size: 12.5px;
            font-weight: bold;
            letter-spacing: 0.3px;
            margin: 0;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            padding: 6.5px 0;
            font-size: 12px;
            font-weight: bold;
            vertical-align: top;
        }

        .details .label {
            width: 43%;
            letter-spacing: 0.2px;
        }

        .details .colon {
            width: 3%;
            text-align: left;
        }

        .details .value {
            width: 54%;
            letter-spacing: 0.2px;
        }

        /* Note Box at Bottom */
        .note-box {
            margin-top: 55px;
            border: 1px solid #000;
            padding: 12px 18px 14px 18px;
        }

        .note-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .note-content {
            margin-left: 35px;
            font-size: 11.5px;
            font-weight: bold;
            line-height: 1.6;
        }

        .note-content p {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>GREEN PARK COACHING CENTRE, NAMAKKAL</h2>
        <h3>HOSTEL ALLOTMENT DETAILS (LONGTERM - {{ $student->academic_year ?? '-' }})</h3>
    </div>

    <table class="details">

        <tr>
            <td class="label">STUDENT'S NAME</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->student_name ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">GENDER</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->gender ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">CLASS</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->course ?? 'LONGTERM') }}</td>
        </tr>

        <tr>
            <td class="label">BATCH</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->batch ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">SECTION</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->section ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">USER ID</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->student_id ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">FATHER'S NAME</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->father_name ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">FATHER'S MOBILE NO</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->father_ph_no ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">MOTHER'S NAME</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->mother_name ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">MOTHER'S MOBILE NO</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->mother_ph_no ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">BOARD STUDIED IN XII STD</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->board_of_study_XII_std ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">AC/NONAC</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->ac_nonac ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">DATE OF ARRIVAL</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->date_of_arrival ?? '-') }}</td>
        </tr>

        <tr>
            <td class="label">TIME OF ARRIVAL</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($student->time_of_arrival ?? '-') }}</td>
        </tr>

    </table>

    <div class="note-box">
        <div class="note-title">Note :</div>
        <div class="note-content">
            <p>Hostel Allotment Letter will be issued to the students only after submitting</p>
            <p>DD for First term hostel fee and First term Coaching fee.</p>
        </div>
    </div>

</body>
</html>