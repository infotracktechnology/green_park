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
        }

        table {
            border-collapse: collapse;
        }

        .table th, .table thead th, .table td {
            border: 1px solid #000;
            padding: 5px 0px 5px 0px;
            font-size: 12px;
        }
        .table th{
            font-weight: bold;
            text-align: center;
        }
        .page {
            width: 8.2in; 
            min-height: 11in;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <div id="print-container">
                <h3>GREEN PARK COACHING CENTRE, NAMAKKAL</h3>
                <h4>{{ $test_name }}</h4>
                <h4>Sec: {{ $section }}</h4>
                <table class="table" style="margin:5px 0px;width: 800px;">
                    <thead>
                        <tr>
                            <th>S.no</th><th>Roll No</th><th>Q Type</th><th width="20%">Name</th>
                        <?php
                        $subjects = explode(',', $results[0]->subjects);
                        ?>
                        @foreach($subjects as $subject)
                            <th colspan="4">{{$subject}}</th>
                        @endforeach
                        <th>Total</th></tr>
                        <tr><th></th><th></th><th></th><th></th>
                        @foreach($subjects as $subject)
                        <th>R</th><th>W</th><th>L</th><th>T</th>
                        @endforeach
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($results as $i => $result)
                    <tr>
                        <td style="text-align: center;">{{ $i+1 }} </td><td style="text-align: center;">{{ $result->student_id}}</td><td style="text-align: center;">{{ $result->test_id}}</td><td>{{ $result->student_name}}</td>
                    @foreach($subjects as $subject)
                    <?php
                    $marks = DB::select("SELECT sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,subject FROM `exam_answer` where test_id in($testids) and student_id=$result->student_id and subject='$subject'");
                    ?>
                    <td style="text-align: center;">{{ $marks[0]->r }}</td>
                    <td style="text-align: center;">{{ $marks[0]->w }}</td>
                    <td style="text-align: center;">{{ $marks[0]->l }}</td>
                    <td style="text-align: center;">{{ $marks[0]->tot }}</td>
                    @endforeach
                    <td style="text-align: center;">{{ $result->mark }}</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script type="text/javascript">
        var printContainer = document.getElementById('print-container');
        var opt = {
            margin: [0, 0.1, 0, 0],
            filename: "{{ $section }} - overall_print.pdf",
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 }, 
            pagebreak: { 
            mode: ['css', 'legacy'],
            },
        };
        html2pdf().set(opt).from(printContainer).save().then(function(){
            alert('Report successfully generated');
            //location.href = "{{ route('report.section_exam') }}";
        });
    </script>
</body>
</html>