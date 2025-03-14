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
            line-height: 25px;
            margin: 15px;
            padding: 0;
        }
        
        h1, h3 {
            text-align: center;
            margin: 15px 0;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .table th, .table thead th, .table td {
            border: 1px solid #2e2e2e;
            padding: 2px 5px;
            font-size: 12px;
            text-align: center;
        }
        
        .break:before, .break:after {
            display: block!important;
        }
        
        .break {
            page-break-after: always;
        }
        
        tr {
            page-break-inside: avoid;
        }
        
        .student-info {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .student-info td {
            padding: 5px;
        }
        
        .tables-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 0 -5px;
        }
        
        .answer-table {
            width: calc(25% - 10px);
            margin: 5px;
            box-sizing: border-box;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .tables-container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <h1>GREEN PARK COACHING CENTRE, NAMAKKAL</h1>
    <h3>CHECK THE ANSWERS THAT YOU MARKED</h3>
    
    <table class="student-info">
        <tr>
            <td width="50%">Student Name: <?php echo auth()->user()->student_name; ?></td>
            <td width="50%">Exam Date: <?php echo $exam->exam_date; ?></td>
        </tr>
        <tr>
            <td width="50%">Subject: <?php echo $exam->name; ?></td>
            <td width="50%">User Name: <?php echo auth()->user()->user_name; ?></td>
        </tr>
        <tr>
            <td colspan="2">Test ID: <?php echo $exam->id; ?></td>
        </tr>
    </table>
    
    <div class="tables-container">
        @foreach($answers as $answer)
        <div class="answer-table">
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
        </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script type="text/javascript">
        var opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: "{{ $exam->name }}-{{ $exam->exam_date }}.pdf",
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
        };
        
        html2pdf()
            .set(opt)
            .from(document.body)
            .save()
            .then(function(){
                window.location.href="{{ route('student.marksheet') }}";
            });
    </script>
</body>
</htm