<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Mark Sheet</title>
    <style type="text/css">
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }

        .table th, .table thead th, .table td {
            border: 1px solid #000;
            padding: 0.5px 2px;
            text-align: center;
            font-size: 11px;
        }
        
        .table th {
            font-weight: bold;
        }
        
        .page {
            width: 8.2in;
            height: 10.8in;
            margin: 0;
            padding: 0.25in;
            box-sizing: border-box;
            page-break-after: always;
            position: relative;
        }

        tr {
            page-break-inside: avoid;
        }
        
        .page-title {
            text-align: center;
            margin: 0 0 15px 0;
        }
        
        .tables-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: nowrap;
        }
        
        .answer-table {
            width: 23%;
            box-sizing: border-box;
        }
        
        .footer {
            margin-top: 20px;
            position: relative;
        }
        
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .student-name, .exam-name {
            margin: 0;
            font-weight: bold;
            font-size: 13px;
        }
        
        .summary-table {
            width: 90%;
            margin: 0 auto;
        }
        
        .summary-table .bold-row {
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div id="print-container">
        @foreach($formattedData as $answer)
        <?php
         $test_id = $answer[0][0]->test_id;
         ?>
            <div class="page">
                <h3 class="page-title">OMR VALUATION REPORT - {{  $test_id }}</h3>
                
                <div class="tables-container">
                    @foreach($answer as $row)
                        <table class="table answer-table">
                            <tr>
                                <th>QNo</th>
                                <th>Key</th>
                                <th>Ans</th>
                                <th>Res</th>
                            </tr>
                            @foreach($row as $key=>$item)
                            <?php
                            $mark = '';
                            if($item->answer_key == "DEL"){
                                $mark = 'DEL';
                            }
                            else {
                                $mark = $item->mark == 4 ? 'C' : ($item->mark == -1 ? 'W' : 'L');
                            }
                            ?>
                                <tr>
                                    <td>{{ $item->q_no }}</td>
                                    <td>{{ $item->answer_key==null ? 0 : $item->answer_key }}</td>
                                    <td>{{ $item->answer }}</td>
                                    <td>{{ $mark }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
                
                <div class="footer">
                    <?php
                    $sid = $answer[0][0]->student_id;
                    $student_name = $answer[0][0]->student_name;
                    $tot=0;
                    $total=0;
                    $r=0;
                    $w=0;
                    $l=0;
                    $test = DB::select("SELECT sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,(count(q_no)*4)total,subject FROM `exam_answer` where test_id=$test_id and student_id=$sid group by subject");
                    ?>
                    
                    <div class="student-info">
                        <h3 class="student-name">STUDENT NAME : {{ $student_name }}</h3>
                        <h3 class="exam-name">EXAM NAME : {{ $test_name }}</h3>
                    </div>
                    
                    <table class="table summary-table">
                        <tr>
                            <th></th>
                            <th>Right (*4)</th>
                            <th>Wrong (-1)</th>
                            <th>Left (0)</th>
                            <th>Total Marks</th>
                        </tr>
                        @foreach($test as $key)
                        <?php
                        $tot+=$key->tot;
                        $total+=$key->total;
                        $r+=$key->r;
                        $w+=$key->w;
                        $l+=$key->l;
                        ?>
                        <tr>
                            <td>{{ $key->subject }}</td>
                            <td>{{ $key->r }}</td>
                            <td>{{ $key->w }}</td>
                            <td>{{ $key->l }}</td>
                            <td>{{ $key->tot }} </td>
                        </tr>
                        @endforeach
                        <tr class="bold-row">
                            <td>Total Mark</td>
                            <td>{{ $r }}</td>
                            <td>{{ $w }}</td>
                            <td>{{ $l }}</td>
                            <td>{{ $tot }} / {{ $total }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script type="text/javascript">
    window.onload = function() {
            generateAndDownloadPDF();
        };
        function generateAndDownloadPDF() {
        var printContainer = document.getElementById('print-container');
        var opt = {
            margin: [0, 0, 0, 0],
            filename: "{{ $section }} - omr_print.pdf",
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
        };
        html2pdf().set(opt).from(printContainer).save().then(function(){
            alert('Report successfully generated');
            //location.href = "{{ route('report.section_exam') }}";
        });
    }
    </script>
</body>
</html>