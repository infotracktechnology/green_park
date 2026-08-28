@extends('layouts.app')

@section('title', 'Chairman Report')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" />
<style>
    table {
        width: 100%;
        overflow-x: auto !important;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #000;
        padding: 5px;
        color: #000 !important;
    }
    th {
        background-color: #eeece1;
    }
    .omr-mode {
        background-color: #FFD966 !important;
        font-weight: 600;
    }
</style>
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
         <div class="row">
            <div class="col-md-12 col-sm-12">
             
               <div class="card card-primary">
                
                  <form method="get" id="myForm" action="{{ route('report.dump') }}" enctype="multipart/form-data">
                     <div class="card-body">
                        <h6>Chairman's Report</h6>
                        <div class="row">
                           
                          <div class="form-group col-lg-4">
                             <label>Test Name</label>
                             <select name="test_name" id="test_name" class="select2" required>
                                 <option value="">Select Test</option>
                                 @foreach ($tests as $test)
                                     <option value="{{ $test->name }}" @selected($test_name == $test->name)>
                                      {{ $test->name }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                           <div class="form-group col-lg-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                         </div>
                        </div>
                        @if($test_name && count($results) > 0)
                        <div class="row m-t-20">
                            <div class="col-lg-12">
                                <button type="button"
        class="btn btn-primary m-b-20"
        id="exportpdf">
    <i class="fa fa-download"></i> Export to PDF
</button>
                                {{-- <a class="btn btn-primary m-b-20" href="{{ route('report.csv_download', ['test_ids' => $test_ids]) }}"><i class="fa fa-download"></i> Export to CSV</a> --}}
                            </div>



                            <div class="col-lg-12">
                            <div class="table-responsive">
                                @php
                                $coachingTypes = $results->pluck('coaching_type')
                                    ->filter()
                                    ->map(fn($type) => strtoupper(trim($type)))
                                    ->unique()
                                    ->implode(' / ');
                                $hideCampus = $results->every(function ($result) {
                                    return in_array(strtoupper(trim($result->coaching_type)), [
                                        'ONLINE LIVE',
                                        'ONLINE RECORDED',
                                        'TEST BATCH'
                                    ]);
                                });
                            @endphp

                                <table  id="export">
                                 <thead>
                                  <tr role="row">
                                   <th rowspan="2">S.NO</th>
                                   <th rowspan="2">SID</th>
                                   <th rowspan="2">Mode</th>
                                   <th rowspan="2">Name</th>
                                   <th rowspan="2">Sex</th>
                                   @if(!$hideCampus)
                                    <th rowspan="2">Campus</th>
                                   @endif
                                   <th rowspan="2">C Type</th>
                                   @foreach ($subjects as $subject)
                                   <th colspan="4">{{ $subject }}</th>
                                   @endforeach
                                   <th rowspan="2">Net Total</th>
                                  </tr>
                                  <tr>
                                    @foreach ($subjects as $subject)
                                    <th>R</th>
                                    <th>W</th>
                                    <th>L</th>
                                    <th>TOT</th>
                                    @endforeach
                                  </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($results as $result)
                                    <tr>
                                       <td>{{ $loop->iteration }}</td>
                                       {{-- <td>{{ $result->test_id }}</td> --}}
                                       <td>{{ $result->student_id }}</td>
                                       <td class="{{ strtoupper($result->stmode) === 'OMR' ? 'omr-mode' : '' }}"> {{ $result->stmode }} </td>
                                       <td>{{ $result->student_name }}</td>
                                       <td>{{ $result->gender }}</td>
                                        @if(!$hideCampus)
                                            <td>{{ $result->campus }}</td>
                                        @endif
                                       <td>{{ $result->section }}</td>
                                       @foreach ($subjects as $subject)
                                           @php
                                                $mark = $marks->get( $result->student_id . '|' . strtoupper(trim($subject)) );
                                            @endphp
                                            <td>{{ $mark->r ?? 0 }}</td>
                                            <td>{{ $mark->w ?? 0 }}</td>
                                            <td>{{ $mark->l ?? 0 }}</td>
                                            <td>{{ $mark->tot ?? 0 }}</td>
                                        @endforeach
                                       <td>{{ $result->mark }}</td>
                                 </tbody>
                                 @endforeach
                                </table>
                            </div>
                        </div>
                        </div>
                        @endif
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>

function exportTable() {
    const { jsPDF } = window.jspdf;

    const doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: 'a4'
    });

    @php
        $coachingTypes = $results->pluck('coaching_type')
            ->filter()
            ->map(fn($type) => strtoupper(trim($type)))
            ->unique()
            ->implode(' / ');

        $hideCampus = $results->every(function ($result) {
            return in_array(strtoupper(trim($result->coaching_type)), [
                'ONLINE LIVE',
                'ONLINE RECORDED',
                'TEST BATCH'
            ]);
        });
    @endphp

    const coachingTypes = @json($coachingTypes);
    const testName = @json($test_name);
    const hideCampus = {{ $hideCampus ? 'true' : 'false' }};

    // 1. HEADER BOX
    const startX = 6;
    const startY = 6;
    const boxWidth = 285;
    const boxHeight = 17;

    doc.setDrawColor(0, 0, 0);
    doc.setLineWidth(0.5);
    doc.rect(startX, startY, boxWidth, boxHeight);

    doc.line(startX, startY + 8.5, startX + boxWidth, startY + 8.5);

    doc.setFont('times', 'bold');
    doc.setFontSize(13);
    doc.setTextColor(0, 0, 0);
    doc.text('GREEN PARK COACHING CENTRE, NAMAKKAL', 148.5, startY + 6, { align: 'center' });

    doc.setFontSize(10.5);
    const midY = startY + 14.5;
    
    doc.setTextColor(220, 20, 20); 
    const typeText = coachingTypes ? coachingTypes + ' ' : '';
    const mainText = ' - ' + testName + ' - MARKS';
    
    const totalTitleWidth = doc.getTextWidth(typeText + mainText);
    const startTitleX = 148.5 - (totalTitleWidth / 2);
    
    doc.text(typeText, startTitleX, midY);
    
    doc.setTextColor(0, 0, 0);
    doc.text(mainText, startTitleX + doc.getTextWidth(typeText), midY);

    // 2. TABLE HEADERS
    
    let headRow1 = [
        { content: 'S.NO', rowSpan: 2, styles: { fillColor: [238, 236, 225] } },
        { content: 'SID', rowSpan: 2, styles: { fillColor: [238, 236, 225] } },
        { content: 'MODE', rowSpan: 2, styles: { fillColor: [238, 236, 225] } },
        { content: 'STUDENT NAME', rowSpan: 2, styles: { fillColor: [238, 236, 225] } },
        { content: 'SEX', rowSpan: 2, styles: { fillColor: [238, 236, 225] } }
    ];

    if (!hideCampus) {
        headRow1.push({
            content: 'CAMPUS',
            rowSpan: 2,
            styles: { fillColor: [238, 236, 225] }
        });
    }

    headRow1.push({
        content: 'C TYPE',
        rowSpan: 2,
        styles: { fillColor: [238, 236, 225] }
    });

    @foreach($subjects as $subject)
        headRow1.push({
            content: @json(strtoupper(trim($subject))),
            colSpan: 4,
            styles: {
                fillColor: [255, 255, 255],
                font: 'times',
                fontStyle: 'bold'
            }
        });
    @endforeach

    headRow1.push({
        content: 'NET\nTOTAL',
        rowSpan: 2,
        styles: { fillColor: [238, 236, 225] }
    });

    let headRow2 = [];
    @foreach($subjects as $subject)
        headRow2.push(
            { content: 'R', styles: { fillColor: [238, 236, 225] } },
            { content: 'W', styles: { fillColor: [238, 236, 225] } },
            { content: 'L', styles: { fillColor: [238, 236, 225] } },
            { content: 'TOT', styles: { fillColor: [238, 236, 225] } }
        );
    @endforeach

    // 3. TABLE BODY DATA

    let body = [];

    @foreach($results as $result)
        body.push([
            {{ $loop->iteration }},
            @json($result->student_id),
            @json($result->stmode),
            @json($result->student_name),
            @json(strtoupper($result->gender) === 'FEMALE' ? 'F' : 'M')

            @if(!$hideCampus)
                , @json($result->campus)
            @endif
            , @json($result->section)

            @foreach($subjects as $subject)
                @php
                    $key = $result->student_id . '|' . strtoupper(trim($subject));
                    $mark = $marks->get($key);
                @endphp
                , {{ $mark->r ?? 0 }}
                , {{ $mark->w ?? 0 }}
                , {{ $mark->l ?? 0 }}
                , { content: {{ $mark->tot ?? 0 }}, isTot: true }
            @endforeach

            , { content: {{ $result->mark ?? 0 }}, isNet: true }
        ]);
    @endforeach

    // 4. COLUMN STYLES (colStyles Definition)

    let colStyles = {
        0: { cellWidth: 10, halign: 'center' },  
        1: { cellWidth: 16, halign: 'center' },  
        2: { cellWidth: 14, halign: 'center' },  
        3: { cellWidth: hideCampus ? 55 : 44, halign: 'left' },    
        4: { cellWidth: 9,  halign: 'center' }   
    };

    let colIndex = 5;
    if (!hideCampus) {
        colStyles[colIndex] = { cellWidth: 20, halign: 'left' }; 
        colIndex++;
    }

    colStyles[colIndex] = { cellWidth: 14, halign: 'center' };  

    //  AUTOTABLE GENERATION

    doc.autoTable({
        head: [headRow1, headRow2],
        body: body,
        startY: 25,
        theme: 'grid',
        margin: {
            top: 25,
            left: 6,
            right: 6,
            bottom: 8
        },
        styles: {
            font: 'helvetica',
            fontStyle: 'bold',          
            fontSize: 10.5,             
            minCellHeight: 6.5,         
            textColor: [0, 0, 0],
            lineColor: [0, 0, 0],
            lineWidth: 0.3,
            cellPadding: { top: 1, bottom: 1, left: 1, right: 1 },
            halign: 'center',
            valign: 'middle'
        },
        headStyles: {
            textColor: [0, 0, 0],
            lineColor: [0, 0, 0],
            lineWidth: 0.3,
            fontSize: 8.5,
            fontStyle: 'bold',
            valign: 'middle',
            halign: 'center'
        },
        bodyStyles: {
            font: 'helvetica',
            fontStyle: 'bold',
            fontSize: 10.5,
            minCellHeight: 6.5,
            textColor: [0, 0, 0],
            valign: 'middle'
        },
        columnStyles: colStyles,

        //  COLORS & CUSTOM CELL STYLING
        didParseCell: function(data) {
            if (data.section !== 'body') return;

            const raw = data.cell.raw;

            // Student Name & Campus Left Align
            if (data.column.index === 3 || (!hideCampus && data.column.index === 5)) {
                data.cell.styles.halign = 'left';
            }

            // OMR Mode (#fde088)
            if (typeof raw === 'string' && raw.trim().toUpperCase() === 'OMR') {
                data.cell.styles.fillColor = [253, 224, 136];
            }

            // TOT Column (#d9edf7)
            if (raw && raw.isTot) {
                data.cell.styles.fillColor = [217, 237, 247];
                data.cell.text = [String(raw.content)];
            }

            // NET TOTAL (#f9f9d8)
            if (raw && raw.isNet) {
                data.cell.styles.fillColor = [249, 249, 216];
                data.cell.text = [String(raw.content)];
            }
        },

        didDrawPage: function(data) {
            const pageNumber = doc.internal.getNumberOfPages();
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            doc.setTextColor(0, 0, 0);
            doc.text('Page ' + pageNumber, 290, 204, { align: 'right' });
        }
    });

    doc.save('test_dump_report.pdf');
}

$('#exportpdf').click(function(e) {
    e.preventDefault();
    exportTable();
});
</script>

@endsection