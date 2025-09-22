<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Mark Sheet</title>
    <style type="text/css">
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            margin: 5px;
            padding: 0;
        }
        
        h2, h4 {
            text-align: center;
            margin: 5px;
        }
        
        table {
            border-collapse: collapse;
            margin: 0px;
            width: 100%;
        }
        
        .table th, .table thead th, .table td {
            border: 1px solid #2e2e2e;
            padding: 3px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
     
        .student-info {
            font-size: 14px;
            margin: 10px 0px;
        }
       
      
    </style>
</head>
<body>
    <h2>GREEN PARK COACHING CENTRE, NAMAKKAL</h2>
    <h4>CHECK THE ANSWERS THAT YOU MARKED</h4>
    
    <table class="student-info">
        <tr>
            <td width="50%">Student Name: <?php echo auth()->user()->student_name; ?></td>
            <td width="50%">Exam Date: <?php echo $exam->name; ?></td>
        </tr>
        <tr>
            <td width="50%">Subject: <?php echo $exam->name; ?></td>
            <td width="50%">User Name: <?php echo auth()->user()->user_name; ?></td>
        </tr>
        <tr>
            <td colspan="2">Test ID: <?php echo $exam->id; ?></td>
        </tr>
    </table>
    
    <table style="width: 100%;">
        <tr>
        @foreach($answers as $answer)
        <td style="padding: 0px 10px;">
            <table class="table">
                <tr>
                    <th>QNo</th>
                    <th>Key</th>
                    <th>Ans</th>
                    <th>Res</th>
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
</htm