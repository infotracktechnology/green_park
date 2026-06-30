<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $testname }} - OMR Sheet</title>
    <style>
        @page {
            margin: 5px 10px; /* Slight margins to fit 45 rows comfortably */
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
            margin-top: 10px;
        }
        .header-subtitle {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 35px;
        }
        
        /* Top Student Information Table */
        .info-table {
            width: 100%;
            margin-bottom: 35px;
            font-size: 12px;
            font-weight: bold;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }

        /* 4-Column Layout Setup */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }
        .layout-table > tbody > tr > td {
            vertical-align: top;
            width: 22.5%; /* 4 columns */
        }
        .spacer {
            width: 3.33%; /* Gap between the columns */
        }

        /* Inner OMR Data Tables */
        .omr-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000; /* Thick outer border matching the image */
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
        .omr-table th {
            background-color: #d1d5db; /* Grey header */
            border: 1px solid #000;
            padding: 2px 0px;
            font-size: 12px;
        }
        .omr-table td {
            border: 1px solid #000;
            padding: 2px 0px;
        }
        .col-q {
            width: 40%;
        }
        .col-res {
            width: 60%;
        }
    </style>
</head>
<body>

    <div class="header-title">GREEN PARK COACHING CENTRE, NAMAKKAL</div>
    <div class="header-subtitle">CHECK THE ANSWERS THAT YOU MARKED</div>

    <!-- Student & Exam Information -->
    <table class="info-table">
        <tr>
            <!-- Using strtoUpper to match the image casing -->
            <td>STUDENT NAME : {{ strtoupper($student->student_name ?? 'STUDENT NAME') }}</td>
            <td class="text-right">Exam Date: {{ \Carbon\Carbon::parse($exam->exam_date ?? now())->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>SUBJECT: {{ strtoupper($testname) }}</td>
            <td class="text-right">User Name: {{ $student->student_id }}</td>
        </tr>
        <tr>
            <td>Coaching Type : &nbsp; {{ $student->coaching_type }}</td>
            <!-- You can fetch actual submission time from ExamAnswer table if needed, otherwise 'now' works -->
            <td class="text-right">Time of Submission: {{ now()->format('d-m-Y H:i A') }}</td>
        </tr>
    </table>

    <!-- OMR Grid (4 Columns, 45 Rows Each = 180 Total) -->
    <table class="layout-table">
        <tr>
            @for ($col = 0; $col < 5; $col++)
                <!-- Data Column -->
                <?php $rowsize = (int)$exam->total_questions/5; ?>
                <td>
                    <table class="omr-table">
                        <thead>
                            <tr>
                                <th class="col-q">Q.No</th>
                                <th class="col-res">Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($row = 1; $row <= $rowsize; $row++)
                                @php
                                    $qNum = $row + ($col * $rowsize);
                                @endphp
                                <tr>
                                    <td>{{ $qNum }}.</td>
                                    <td>{{ $answers[$qNum] ?? '' }}</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </td>
            @endfor
        </tr>
    </table>
</body>
</html>