<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statement of Marks</title>

    <style>
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 11px;
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Header Layout */
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
            font-size: 15px;
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

        /* Student Info Grid */
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

        /* Section Titles */
        .section-heading {
            text-align: center;
            font-size: 11.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 4px 0;
            letter-spacing: 0.3px;
        }

        /* Report Tables */
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
            font-size: 10px;
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

        /* Footer Signature */
        .footer-section {
            margin-top: 15px;
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
    </style>
</head>
<body>

@php
   

    $signPath = public_path('img/chairman_sign.png');
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
                        GREEN PARK CAREER ACADEMY, {{ $student->branch->name ?? 'COIMBATORE' }}
                    </div>
                    <div class="statement-title uppercase">
                        STATEMENT OF MARKS ({{ $student->coaching_type ?? 'LONGTERM' }})
                    </div>
                    @if(!empty($student->academic_period) || !empty($student->academic_year))
                        <div class="date-range">
                            ({{ $student->academic_period ?? $student->academic_year }})
                        </div>
                    @endif
                </td>
                <td style="width: 12%; text-align: right;">
                    <div class="roll-box">
                        {{ $student->roll_no ?? $loop->iteration }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- ================= STUDENT DETAILS ================= --}}
        <table class="student-info-table">
            <tr>
                <td style="width: 58%;">
                    STUDENT NAME : <span class="uppercase">{{ $student->student_name ?? '' }}</span>
                </td>
                <td style="width: 42%;">
                    COURSE : <span class="uppercase">{{ $student->course ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    STUDENT ID : <span>{{ $student->student_id ?? '' }}</span>
                </td>
                <td>
                    GENDER : <span class="uppercase">{{ $student->gender ?? 'MALE' }}</span>
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

                $hasTotal = $rows->contains(
                    fn($r) => isset($r['total']) && $r['total'] !== null
                );

                $hasOverallTop = ($group['type'] ?? '') === 'simple'
                    && $rows->contains(
                        fn($r) => isset($r['overall_top']) && $r['overall_top'] !== null
                    );
            @endphp

            {{-- Category Title --}}
            <div class="section-heading">
                {{ strtoupper($group['category'] ?? 'TEST REPORT') }}
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
                                @if($val === 'AB')
                                    AB
                                @elseif($val === null || $val === '')
                                    -
                                @else
                                    {{ $val }}
                                @endif
                            </td>

                             @endforeach

                            {{-- Total --}} 
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

                    {{-- Average Row --}}
                    @if(isset($group['averages']) || isset($group['show_average']))
                        <tr class="avg-row">
                            <td colspan="2" class="text-bold" style="font-size: 11px;">Average</td>
                            @foreach($subjectLabels as $label)
                                <td>
                                    {{ $group['averages'][$label] ?? '0' }}
                                    @if(isset($subjectMaxMarks[$label]))
                                        / {{ $subjectMaxMarks[$label] }}
                                    @endif
                                </td>
                            @endforeach

                            @if($hasTotal)
                                <td>{{ $group['averages']['total'] ?? '0' }}</td>
                            @endif

                            @if($hasOverallTop)
                                <td>{{ $group['averages']['overall_top'] ?? '-' }}</td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>
        @endforeach

        {{-- ================= FOOTER / SIGNATURE ================= --}}
        <table class="footer-section">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%; text-align: center;">
                    @if($signData)
                        <img class="sign-img" src="data:image/png;base64,{{ $signData }}" alt="Signature">
                    @else
                        {{-- Fallback styling if image not present --}}
                        <div style="font-family: cursive; font-size: 16px; color: #1a0dab; margin-bottom: 2px;">
                            Mvg. Bymd
                        </div>
                    @endif
                    <div class="chairman-text">CHAIRMAN</div>
                </td>
            </tr>
        </table>
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>