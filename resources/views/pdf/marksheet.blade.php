<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Mark Sheet</title>
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 0px 10px 10px 10px !important; 
        }
        
        h2, h4 {
            text-align: center;
            margin: 2px 5px;
        }
        
        table {
            border-collapse: collapse;
            margin: 0 auto; 
            width: 100%;
        }
        
        .table th, .table thead th, .table td {
            border: 1px solid #2e2e2e;
            padding: 1px !important;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
     
        /* Centered Student Info Table */
        .student-info {
            font-size: 13px;
            width: 80% !important;
            margin: 10px 10% 15px 10% !important;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .student-info td {
            border: 1px solid #2e2e2e;
            padding: 5px 10px;
            width: 50%;
            text-align: left;
            vertical-align: middle;
        }

        .answers-wrapper-table {
            margin: 0 auto !important;
            width: auto;
        }
    </style>
</head>
<body>
    <h2>GREEN PARK COACHING CENTRE, NAMAKKAL</h2>
    <h4>CHECK THE ANSWERS THAT YOU MARKED</h4>
    
    <!-- Student Info Table (Centered on Page) -->
    <table class="student-info" align="center">
        <tr>
            <td><strong>Student Name:</strong> <?php echo auth()->user()->student_name; ?></td>
            <td><strong>Exam:</strong> <?php echo !empty($exam->exam_date) ? date('d/m/Y', strtotime($exam->exam_date)) : ''; ?></td>
        </tr>
        <tr>
            <td><strong>Subject:</strong> <?php echo $exam->name; ?></td>
            <td><strong>User Name:</strong> <?php echo auth()->user()->user_name; ?></td>
        </tr>
        <tr>
            <td><strong>Test ID:</strong> {{ $testId }}</td>
            <td><strong>Question Order:</strong> <?php echo $qorder->qorder; ?></td>
        </tr>
    </table>
    
    <table class="answers-wrapper-table">
        <tr>
        @foreach($answers as $answer)
        <td style="padding: 0px 10px; vertical-align: top; text-align: center;">
            <table class="table" style="width: {{ 25 * count($answers) }}%; margin: 0 auto;">
                <tr>
                    <th width="40px">QNo</th>
                    <th width="40px">Key</th>
                    <th width="40px">Ans</th>
                    <th width="40px">Res</th>
                </tr>
                @foreach($answer as $key=>$item)
                <tr>
                    <?php
                    $mark = '';
                    if($item->answer_key == "DEL"){
                        $mark = 'DEL';
                    }
                    else {
                        $mark = $item->mark == 4 ? 'C' : ($item->mark == -1 ? 'W' : 'L');
                    }
                    ?>
                    <td>{{ $item->q_no }}</td>
                    <td>{{ $item->answer_key==null ? 0 : $item->answer_key }}</td>
                    <td>{{ $item->answer }}</td>
                    <td>{{ $mark }}</td>
                </tr>
                @endforeach
            </table>
        </td>
        @endforeach
        </tr>
    </table>
</body>
</html>