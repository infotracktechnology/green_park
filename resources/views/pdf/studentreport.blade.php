<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statement of Marks</title>

    <style>
        @page {
            size: legal portrait;
            margin: 8mm; 
        }

        
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 14px;
            line-height: 1.2;
        }

        .text-center { text-align: center;
        font-size: 14px;
     }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-img {
            max-width: 75px;
            max-height: 75px;
            display: block;
        }

        .institute-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .statement-title {
            font-size: 12.5px;
            font-weight: bold;
            margin-top: 4px;
        }

        .date-range {
            font-size: 11.5px;
            font-weight: bold;
            margin-top: 3px;
        }

        .roll-box {
            border: 1.5px solid #000;
            padding: 5px 12px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            min-width: 35px;
        }

        .student-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .student-info-table td {
            padding: 2.5px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .section-heading {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 4px 0;
            letter-spacing: 0.3px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 2.5px 4px;
            text-align: center;
            font-size: 12px;
        }

        .report-table th {
            background-color: #ededed;
            font-weight: bold;
        }

        .report-table td.exam-col {
            text-align: left;
            padding-left: 6px;
            font-weight: bold;
        }

        .report-table tr.avg-row td {
            font-weight: bold;
            background-color: #fafafa;
        }

      
        .footer-section {
            margin-top: 9px;
            width: 100%;
            border-collapse: collapse;
        }

        .sign-img {
            max-height: 35px;
            display: block;
            margin-left: auto;
        }

        .chairman-text {
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }

        .page-break {
            page-break-after: always;
        }
        .attendance-report-box {
            border: 1px solid #000;
            padding: 10px 15px;
            margin-left: 25px;
            margin-top: 10px;
            width: 90%;
            box-sizing: border-box;
        }

        .attendance-report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 2px dashed #000;
        }

        .attendance-details {
            width: 50%;
            margin: 0 auto;
            font-size: 14px;
        }

        .attendance-details div {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .attendance-details strong {
            display: table-cell;
            width: 60%;
            text-align: left;
            font-weight: bold;
            white-space: nowrap;
        }

        .attendance-details span {
            display: table-cell;
            width: 28%;
            text-align: left;
             font-weight: bold;
            white-space: nowrap;
        }
        .average-180-box {
    border: 1px solid #000;
    padding: 10px 15px;
    margin-left: 25px;
    margin-top: 10px;
    width: 90%;
    box-sizing: border-box;
}

.average-180-title {
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    padding-bottom: 7px;
    margin-bottom: 8px;
    border-bottom: 2px dashed #000;
}

.average-180-details {
    width: 65%;
    margin: 0 auto;
    font-size: 14px;
}

.average-180-details div {
    display: table;
    width: 100%;
    margin-bottom: 6px;
}

.average-180-details strong {
    display: table-cell;
    width: 75%;
    text-align: left;
}

.average-180-details span {
    display: table-cell;
    width: 25%;
    text-align: left;
    font-weight: bold;
    white-space: nowrap;
}
    </style>
</head>
<body>

@php
   

    $signPath = asset('img/favicon.png');
    $signData = file_exists($signPath) ? base64_encode(file_get_contents($signPath)) : '';
@endphp

@foreach($reports as $studentReport)
    @php
        $student = $studentReport['student'];
        $reportGroups = collect($studentReport['report'] ?? []);
    @endphp

    <div class="page-container">
        {{-- ================= HEADER ================= --}}
        <table class="header-table">
            <tr>
                <td style="width: 15%;">
                   <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}">
                </td>
                <td style="width: 73%;" class="text-center">
                    <div class="institute-title uppercase">
                        GREEN PARK COACHING CENTRE, {{ strtoupper($student->branch->campus == 'GP' ? 'NAMAKKAL' : ($student->branch->campus ?? '')) }}
                    </div>
                    <div class="statement-title uppercase">
                        STATEMENT OF MARKS ({{ $student->coaching_type === 'OFFLINE' ? 'LONGTERM' : $student->coaching_type }})
                    </div>
                    @if(!empty($student->academic_period) || !empty($student->academic_year))
                        <div class="date-range">
                            @if(request('from_date') && request('to_date'))
                                ({{ \Carbon\Carbon::parse(request('from_date'))->format('d-m-Y') }}
                                To
                                {{ \Carbon\Carbon::parse(request('to_date'))->format('d-m-Y') }})
                            @endif
                        </div>
                    @endif
                </td>
                <td style="width: 12%; text-align: right;">
                    <div class="roll-box">
                        {{  $loop->iteration }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- ================= STUDENT DETAILS ================= --}}
        <table class="student-info-table">
            <tr>
                <td style="width: 83%;">
                    STUDENT NAME : <span class="uppercase">{{ $student->student_name }}</span>
                </td>

                 @if(strtoupper(trim($student->coaching_type ?? '')) === 'OFFLINE')
                <td>
                    CLASS : <span class="uppercase">{{ $student->section }} </span>
                @endif
            </td>

                <td>
                @if(strtoupper(trim($student->coaching_type ?? '')) !== 'OFFLINE')
                    COURSE : <span class="uppercase">{{ $student->course }}</span>
                @endif
            </td>
            </tr>
            <tr>
                <td>
                    STUDENT ID : <span>{{ $student->student_id  }}</span>
                </td>
                <td>
                    BATCH : <span class="uppercase"> {{ $student->batch }}</span>
                </td>
            </tr>
        </table>

        {{-- ================= TABLES FOR TEST CATEGORIES ================= --}}
        @foreach($reportGroups as $group)
        
            @php
                $rows = collect($group['rows'] ?? []);
                $subjectLabels = [];
                $subjectMaxMarks = $group['max_marks'] ?? [];

                $allSubjects = ['PHYSICS','CHEMISTRY','BOTANY','ZOOLOGY','BIOLOGY',
                ];
                foreach ($allSubjects as $label) {

                    $hasSubjectExam = $rows->contains(function ($row) use ($label) {

                        if ($row['_is_absent_exam'] ?? false) {
                            return false;
                        }
                        $rowSubjects = $row['subjects'] ?? [];
                        if (!is_array($rowSubjects)) {
                            return false;
                        }
                        return array_key_exists($label, $rowSubjects)
                            && $rowSubjects[$label] !== null;
                    });
                    if ($hasSubjectExam) {
                        $subjectLabels[] = $label;
                    }
                }

                $hasTotal = ($group['type'] ?? '') === 'simple'
                    && $rows->contains(
                        fn($r) => isset($r['total']) && $r['total'] !== null
                    );

                $hasOverallTop = ($group['type'] ?? '') === 'simple'
                    && $rows->contains(
                        fn($r) => isset($r['overall_top']) && $r['overall_top'] !== null
                    );

                    $categoryTitle = strtoupper(
                        trim($group['category'] ?? 'TEST REPORT')
                    );

                    if (str_contains($categoryTitle, 'WEEKEND')) {
                        $categoryTitle = 'WEEKEND SLIP TEST MARKS';
                    } elseif (str_contains($categoryTitle, 'GRAND')) {
                        $categoryTitle = 'GRAND TEST MARKS';
                    }
            @endphp

            {{-- Category Title --}}
            <div class="section-heading">
                 {{ $categoryTitle }}
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="{{ !empty($subjectMaxMarks) ? 1 : 1 }}" style="width: 6%;">S.No</th>
                        <th rowspan="{{ !empty($subjectMaxMarks) ? 1 : 1 }}" style="width: 34%;">Exam Name</th>

                        @foreach($subjectLabels as $subject)
                                @php
                                    $maxMark = null;
                                    foreach ($subjectMaxMarks as $maxData) {
                                        if (is_array($maxData)
                                            && isset($maxData['label'])
                                            && strtoupper($maxData['label']) === strtoupper($subject)) {
                                            $maxMark = $maxData['mark'] ?? null;
                                            break;
                                        }
                                    }
                                @endphp
                                <th>
                                    {{ $subject }}
                                    @if($maxMark !== null)
                                        <br>
                                        <span style="font-size: 9px; font-weight: normal;">
                                            ({{ $maxMark }})
                                        </span>
                                    @endif
                                </th>
                            @endforeach

                        @if($hasTotal)
                            <th>
                                Total
                                @if(isset($group['total_max']))
                                    <br><span style="font-size: 9px; font-weight: normal;">({{ $group['total_max'] }})</span>
                                @endif
                            </th>
                        @endif

                        @if($hasOverallTop)
                            <th>
                                Overall I Mark
                                @if(isset($group['total_max']))
                                    <br><span style="font-size: 9px; font-weight: normal;">({{ $group['total_max'] }})</span>
                                @endif
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td>{{ $row['sno'] ?? $loop->iteration }}</td>
                            <td class="exam-col">
                                {{ $row['label'] ?? $row['exam'] ?? '' }}
                                @if(!empty($row['date']))
                                    ({{ $row['date'] }})
                                @elseif(!empty($row['range']))
                                    ({{ $row['range'] }})
                                @endif
                            </td>

                             {{-- Subject Marks --}}
                           @foreach($subjectLabels as $label)
                            @php
                                $rowSubjects = $row['subjects'] ?? [];
                                $val = is_array($rowSubjects) && array_key_exists($label, $rowSubjects) ? $rowSubjects[$label] : null;
                            @endphp
                            <td style="width: 8%;">
                                @if($val === 'AB' )
                                    AB
                                @elseif( $val === '' || $val === null)
                                    -
                                @else
                                    {{ $val }}
                                @endif
                            </td>

                             @endforeach

                            @if($hasTotal)
                                <td class="text-bold" style="width: 10%;">
                                    @if(isset($row['total']) && $row['total'] !== null)
                                        {{ $row['total'] }}
                                    @else
                                        AB
                                    @endif
                                </td>
                            @endif

                            @if($hasOverallTop)
                                <td class="text-bold" style="width: 10%;">{{ $row['overall_top'] ?? '-' }}</td>
                            @endif
                        </tr>
                    @endforeach

                    @if(isset($group['averages']) || isset($group['show_average']))
                        <tr class="avg-row">
                            <td colspan="2" class="text-bold" style="font-size: 11px;">Average</td>
                           @foreach($subjectLabels as $label)
                            @php
                                $maxMark = null;

                                foreach ($subjectMaxMarks as $maxData) {
                                    if (
                                        is_array($maxData) &&
                                        isset($maxData['label']) &&
                                        strtoupper($maxData['label']) === strtoupper($label)
                                    ) {
                                        $maxMark = $maxData['mark'] ?? null;
                                        break;
                                    }
                                }
                            @endphp

                            <td>
                                {{ $group['averages'][$label] ?? '0' }}

                                @if($maxMark !== null)
                                    / {{ $maxMark }}
                                @endif
                            </td>
                        @endforeach

                            @if($hasTotal)
                                <td>
                                    {{ $group['averages']['total'] ?? '0' }}
                                    @if(isset($group['total_max']))
                                        / {{ $group['total_max'] }}
                                    @endif
                                </td>
                            @endif

                            @if($hasOverallTop)
                                <td>{{ $group['averages']['overall_top'] ?? '-' }}</td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>
        @endforeach
        <div class="attendance-report-box">
            <div class="attendance-report-title">
                SCHOOL ATTENDANCE REPORT
                @if(request('from_date') && request('to_date'))
            ({{ \Carbon\Carbon::parse(request('from_date'))->format('d-m-Y') }}
            To
            {{ \Carbon\Carbon::parse(request('to_date'))->format('d-m-Y') }})
        @endif
            </div>
            <div class="attendance-details">
                <div>
                    <strong>Total No of Working Days</strong>
                    <span>: {{ $student->total_wrk_days }} Days</span>
                </div>
                <div>
                    <strong>No of Days Present</strong>
                    <span>: {{ $student->present_days }} Days</span>
                </div>
                <div>
                    <strong>No of Days Absent</strong>
                    <span>: {{ $student->absent_days }} Days</span>
                </div>
            </div>
        </div>
@php
    $finalAverage180 = [];

    foreach ($reportGroups as $group) {

        $category = strtoupper($group['category'] ?? '');
        $averageData = $group['average_180'] ?? [];

        if (empty($averageData)) {
            continue;
        }

        if (str_contains($category, 'WEEKEND')) {

            $values = [];

            foreach ($averageData as $value) {
                if (is_numeric($value)) {
                    $values[] = (float) $value;
                }
            }

            if (count($values) > 0) {

                $finalAverage180[] = [
                    'label' => 'Weekend Slip Test',
                    'value' => round(array_sum($values) / count($values)),
                ];
            }
        }

 
        elseif (str_contains($category, 'CUMULATIVE')) {

            if (isset($averageData['TOTAL'])) {

                $finalAverage180[] = [
                    'label' => $group['category'],
                    'value' => $averageData['TOTAL'],
                ];
            }
        }

        elseif (str_contains($category, 'GRAND')) {

            if (isset($averageData['TOTAL'])) {

                $finalAverage180[] = [
                    'label' => 'Grand Test',
                    'value' => $averageData['TOTAL'],
                ];
            }
        }
    }
@endphp


@if(!empty($finalAverage180))

    <div class="average-180-box">

        <div class="average-180-title">
            AVERAGE
        </div>

        <div class="average-180-details">

            @foreach($finalAverage180 as $item)

                <div>
                    <strong>{{ $item['label'] }}</strong>

                    <span>
                        : {{ number_format($item['value'], 0) }} / 180
                    </span>
                </div>

            @endforeach

        </div>

    </div>

@endif

        {{-- ================= FOOTER / SIGNATURE ================= --}}
        <table class="footer-section">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%; padding: 20px; text-align: center;">
                 <img class="chairman-logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/chairman_sign.jpeg'))) }}" style="width: 80px;">
                    <div class="chairman-text">CHAIRMAN</div>
                </td>   
            </tr>
        </table>
    </div>

    {{-- @if(!$loop->last)
        <div class="page-break"></div>
    @endif --}}
@endforeach

</body>
</html>