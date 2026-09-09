<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Range Report</title>
    <style>
        @page {
            margin: 15px 25px 20px 25px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 0;
        }

        .banner-container {
            width: 100%;
            text-align: center;
            margin-bottom: 12px;
        }

        .banner-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .report-heading {
            text-align: center;
            margin-bottom: 15px;
        }

        .report-main-title {
            color: #cc0000;
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }

        .report-sub-title {
            color: #000000;
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 15px;
        }

        .grid-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .grid-table td.column {
            width: 48.5%;
        }

        .grid-table td.spacer {
            width: 3%;
        }

        .grid-table td.center-spacer {
            width: 25.75%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #1a4f8a;
            page-break-inside: avoid;
        }

        .data-table th, .data-table td {
            border: 1px solid #1a4f8a;
            padding: 4px 6px;
            text-align: center;
            font-size: 10.5px;
        }

        .th-subject {
            background-color: #faecd4 !important;
            color: #cc0000 !important;
            font-size: 11px !important;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px !important;
        }

        .th-first-mark {
            background-color: #d9ead3 !important;
            color: #cc0000 !important;
            font-size: 11px !important;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px !important;
        }

        .th-cols {
            background-color: #e7e6e6;
            color: #000;
            font-weight: bold;
            font-size: 9.5px !important;
            padding: 5px 2px !important;
        }

        .data-table tbody td {
            color: #164282;
            font-weight: bold;
            font-size: 10.5px;
        }

        .sno-col {
            width: 12%;
        }

        .range-col {
            width: 58%;
        }

        .count-col {
            width: 30%;
        }

        .footer-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            border: none;
        }

        .footer-table td {
            border: none;
            vertical-align: bottom;
            text-align: right;
            padding-right: 15px;
        }

        .sign-text {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <div class="banner-container">
        @php
            $imgPath = public_path('assets/img/image.png');
            if (!file_exists($imgPath)) {
                $imgPath = base_path('assets/img/image.png');
            }
        @endphp
        @if(file_exists($imgPath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imgPath)) }}" alt="Header">
        @endif
    </div>

    <div class="report-heading">
        <div class="report-main-title">
              STUDENTS RANGE REPORT ({{ date('d-m-Y') }})
        </div>
        <div class="report-sub-title">
            {{ !empty($test_name) ? $test_name : 'CUMULATIVE TEST' }}
        </div>
    </div>

    @php
        $subjects = [];

        if (isset($physicsReport) && $physicsReport->count() > 0) {
            $subjects[] = [
                'name' => 'PHYSICS',
                'first_mark' => $physicsFirstMark ?? 0,
                'total' => $physicsTotal ?? 200,
                'report' => $physicsReport
            ];
        }

        if (isset($chemistryReport) && $chemistryReport->count() > 0) {
            $subjects[] = [
                'name' => 'CHEMISTRY',
                'first_mark' => $chemistryFirstMark ?? 0,
                'total' => $chemistryTotal ?? 200,
                'report' => $chemistryReport
            ];
        }

        if (isset($botanyReport) && $botanyReport->count() > 0) {
            $subjects[] = [
                'name' => 'BOTANY',
                'first_mark' => $botanyFirstMark ?? 0,
                'total' => $botanyTotal ?? 200,
                'report' => $botanyReport
            ];
        }

        if (isset($zoologyReport) && $zoologyReport->count() > 0) {
            $subjects[] = [
                'name' => 'ZOOLOGY',
                'first_mark' => $zoologyFirstMark ?? 0,
                'total' => $zoologyTotal ?? 200,
                'report' => $zoologyReport
            ];
        }

        if (isset($biologyReport) && $biologyReport->count() > 0) {
            $subjects[] = [
                'name' => 'BIOLOGY',
                'first_mark' => $biologyFirstMark ?? 0,
                'total' => $biologyTotal ?? 200,
                'report' => $biologyReport
            ];
        }
    @endphp

    {{-- ================= 4. SUBJECT TABLES (2 PER ROW OR 1 CENTERED) ================= --}}
    @foreach(array_chunk($subjects, 2) as $row)
        @if(count($row) == 2)
            <table class="grid-table">
                <tr>
                    <td class="column">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="3" class="th-subject">{{ $row[0]['name'] }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="th-first-mark">FIRST MARK : {{ $row[0]['first_mark'] }} / {{ $row[0]['total'] }}</th>
                                </tr>
                                <tr>
                                    <th class="th-cols sno-col">S.No</th>
                                    <th class="th-cols range-col">MARKS RANGE</th>
                                    <th class="th-cols count-col">NO: OF STUDENTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($row[0]['report'] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ strtoupper($item['range']) }}</td>
                                        <td>{{ $item['count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>

                    <td class="spacer"></td>

                    <td class="column">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="3" class="th-subject">{{ $row[1]['name'] }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="th-first-mark">FIRST MARK : {{ $row[1]['first_mark'] }} / {{ $row[1]['total'] }}</th>
                                </tr>
                                <tr>
                                    <th class="th-cols sno-col">S.No</th>
                                    <th class="th-cols range-col">MARKS RANGE</th>
                                    <th class="th-cols count-col">NO: OF STUDENTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($row[1]['report'] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ strtoupper($item['range']) }}</td>
                                        <td>{{ $item['count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

        @else
            <table class="grid-table">
                <tr>
                    <td class="center-spacer"></td>
                    <td class="column">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="3" class="th-subject">{{ $row[0]['name'] }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="th-first-mark">FIRST MARK : {{ $row[0]['first_mark'] }} / {{ $row[0]['total'] }}</th>
                                </tr>
                                <tr>
                                    <th class="th-cols sno-col">S.No</th>
                                    <th class="th-cols range-col">MARKS RANGE</th>
                                    <th class="th-cols count-col">NO: OF STUDENTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($row[0]['report'] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ strtoupper($item['range']) }}</td>
                                        <td>{{ $item['count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                    <td class="center-spacer"></td>
                </tr>
            </table>
        @endif
    @endforeach

    {{-- ================= 5. OVERALL TABLE (CENTERED) ================= --}}
    @if(isset($overallReport) && $overallReport->count() > 0)
        <table class="grid-table">
            <tr>
                <td class="center-spacer"></td>
                <td class="column">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th colspan="3" class="th-subject">OVERALL</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="th-first-mark">FIRST MARK : {{ $overallFirstMark ?? 0 }} / {{ $overallTotal ?? 400 }}</th>
                            </tr>
                            <tr>
                                <th class="th-cols sno-col">S.No</th>
                                <th class="th-cols range-col">MARKS RANGE</th>
                                <th class="th-cols count-col">NO: OF STUDENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overallReport as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ strtoupper($item['range']) }}</td>
                                    <td>{{ $item['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                <td class="center-spacer"></td>
            </tr>
        </table>
    @endif

    {{-- ================= 6. SIGNATURE FOOTER ================= --}}
    <table class="footer-table">
        <tr>
            <td>
                <div style="font-family: cursive; font-size: 17px; color: #0000a0; margin-bottom: 2px;">
                    Mng. Sign
                </div>
                <div class="sign-text">CHAIRMAN</div>
            </td>
        </tr>
    </table>

</body>
</html>