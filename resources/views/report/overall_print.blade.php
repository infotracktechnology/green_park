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
            width: 100%;
            margin-bottom: 10px;
        }

        .table th, .table thead th, .table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .table th {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        
        .container {
            width: 8in; /* Smaller than page width to ensure margins */
            margin: 0 auto;
            padding: 0.25in;
            box-sizing: border-box;
        }

        /* Explicit page breaks after certain number of rows */
        .page-break {
            page-break-after: always;
        }
        
        /* Control how many rows per page */
        .table tbody tr:nth-child(28n) {
            page-break-after: always;
        }
        
        /* Headers */
        .center-header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        /* For student name column alignment */
        .name-cell {
            text-align: left !important;
            padding-left: 5px !important;
        }
        
        /* For repeated header on new pages */
        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }
    </style>
</head>
<body>

    <div id="print-container" class="container">
        <div class="center-header">
            <h3 style="margin-bottom: 5px;">GREEN PARK COACHING CENTRE, NAMAKKAL</h3>
            <h4 style="margin-top: 5px; margin-bottom: 5px;">{{ $test_name }}</h4>
            <h4 style="margin-top: 5px; margin-bottom: 10px;">Sec: {{ $section }}</h4>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40px;">S.no</th>
                    <th style="width: 60px;">Roll No</th>
                    <th style="width: 50px;">Q Type</th>
                    <th style="width: 20%;" class="name-cell">Name</th>
                    <?php
                    $subjects = explode(',', $results[0]->subjects);
                    ?>
                    @foreach($subjects as $subject)
                        <th colspan="4">{{$subject}}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="name-cell"></th>
                    @foreach($subjects as $subject)
                    <th>R</th>
                    <th>W</th>
                    <th>L</th>
                    <th>T</th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $i => $result)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $result->student_id}}</td>
                    <td>{{ $result->test_id}}</td>
                    <td class="name-cell">{{ $result->student_name}}</td>
                    @foreach($subjects as $subject)
                    <?php
                    $marks = DB::select("SELECT sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,subject FROM `exam_answer` where test_id in($testids) and student_id=$result->student_id and subject='$subject'");
                    ?>
                    <td>{{ $marks[0]->r }}</td>
                    <td>{{ $marks[0]->w }}</td>
                    <td>{{ $marks[0]->l }}</td>
                    <td>{{ $marks[0]->tot }}</td>
                    @endforeach
                    <td>{{ $result->mark }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script type="text/javascript">
        // Generate PDF as soon as page loads and all resources are ready
        window.onload = function() {
            generateAndDownloadPDF();
        };
        
        function generateAndDownloadPDF() {
            var printContainer = document.getElementById('print-container');
            
            var opt = {
                margin: [0.5, 0, 0.5, 0], // Top, left, bottom, right margins in inches
                filename: "{{ $section }} - mark_sheet.pdf",
                jsPDF: { 
                    unit: 'in', 
                    format: 'letter', 
                    orientation: 'portrait' 
                },
            };
            
            // Create worker and trigger download
            html2pdf()
                .from(printContainer)
                .set(opt)
                .save()
                .then(function() {
                    console.log('PDF generated and download started');
                    // You can remove the alert if you don't want the popup
                    alert('Report successfully generated');
                    //window.close();
                    // Uncomment the line below if you want to redirect after PDF generation
                    // location.href = "{{ route('report.section_exam') }}";
                })
                .catch(function(error) {
                    console.error('Error generating PDF:', error);
                    alert('There was an error generating the PDF. Please try again.');
                });
        }
    </script>
</body>
</html>