<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam Log Report</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 15px;
            background-color: #ffffff;
            color: #000000;
        }

        /* Main Container Table */
        .exam-log-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000000;
        }

        .exam-log-table th, 
        .exam-log-table td {
            border: 1px solid #000000;
            padding: 6px 10px;
            font-size: 13px;
        }

        /* Header Red Titles */
        .title-header {
            color: #d90429;
            font-weight: bold;
            text-align: center;
            font-size: 16px;
            background-color: #ffffff;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #000000;
            padding: 8px;
        }

        .student-header {
            color: #d90429;
            font-weight: bold;
            text-align: center;
            font-size: 15px;
            background-color: #ffffff;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #000000;
            padding: 6px;
        }

        /* Column Headers (Gray) */
        .table-head {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 13px;
            color: #000000;
            text-transform: uppercase;
        }

        .table-head th {
            padding: 8px;
        }

        /* Alignment Helpers */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;color:#d90429;">
    ONLINE EXAM LOG REPORT
</h2>

<table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
    <tr>
        <td><strong>Exam Name :</strong></td>
        <td>{{ $exam->name }}</td>
    </tr>
    <tr>
        <td><strong>Student Name :</strong></td>
        <td>{{ strtoupper($student->student_name ?? '') }} </td>
    </tr>
    <tr>
        <td><strong>Student ID :</strong></td>
        <td>{{ $student->student_id ?? '' }} </td>
    </tr>

</table>

<table class="exam-log-table">
    <thead>
        <tr class="table-head">
            <th style="width: 6%;">S.NO</th>
            <th style="width: 14%;">STUDENT ID</th>
            <th style="width: 60%;">QUESTION / ANSWER / EXAM NAME</th>
            <th style="width: 20%;">UPDATED TIME</th>
        </tr>
    </thead>

    <tbody>
        @foreach($logs as $key => $log)
        <tr>
            <td class="text-center">{{ $key + 1 }}</td>
            <td class="text-center">{{ $student->student_id }}</td>
            <td class="text-left">{{ $log->action }}</td>
            <td class="text-center">{{ $log->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>