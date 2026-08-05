<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Report - {{ $student->student_name ?? '' }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 14px;
        }
        
        .container {
            padding: 15px;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            font-size: 15px;
            font-weight: bold;
            margin-top: 5px;
        }

        .outer-border-box {
            border: 1px solid #000;
            padding: 30px 20px;
            min-height: 850px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .details-table td {
            padding: 10px 5px;
            font-size: 14px;
        }
        .label {
            width: 25%;
        }
        .colon {
            width: 5%;
        }
        .value {
            width: 70%;
        }

        .page-break {
            page-break-after: always;
        }

        .sub-header {
            width: 100%;
            margin-bottom: 15px;
        }
        .section-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .marks-table th, .marks-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 12px;
        }
        .marks-table th {
            background-color: #cccccc;
            font-weight: bold;
        }
        .marks-table td.exam-name {
            text-align: left;
            padding-left: 8px;
        }
        .avg-row {
            background-color: #d0d0d0;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- ================= PAGE 1: STUDENTS PARTICULARS ================= -->
    
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 15%;">
                    <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}">
                </td>
                <td style="width: 85%;" class="text-center">
                    <div class="title font-39">GREEN PARK COACHING CENTRE({{ $student->branch->name ?? '' }}) </div>
                    <div class="subtitle">STUDENTS PARTICULARS ({{ $student->academic_year ?? '' }})</div>
                </td>
            </tr>
        </table>

    <div class="outer-border-box">
        <table class="details-table">
            <tr>
                <td class="label">Student Name</td>
                <td class="colon">:</td>
                <td class="value text-bold">{{ strtoupper($student->student_name ) }}</td>
            </tr>
            <tr>
                <td class="label">Student ID</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->student_id }}</td>
            </tr>
            <tr>
                <td class="label">Course</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->course  }}</td>
            </tr>
            <tr>
                <td class="label">Campus</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->branch->name  }}</td>
            </tr>
            <tr>
                <td class="label">Coaching Type</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->coaching_type}}</td>
            </tr>
            <tr>
                <td class="label">Father Name</td>
                <td class="colon">:</td>
                <td class="value text-bold">{{ strtoupper($student->father_name ) }}</td>
            </tr>
            <tr>
                <td class="label">Father Occupation</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->father_occupation }}</td>
            </tr>
            <tr>
                <td class="label">Father Phone No</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->father_ph_no}}</td>
            </tr>
            <tr>
                <td class="label">Mother Name</td>
                <td class="colon">:</td>
                <td class="value text-bold">{{ strtoupper($student->mother_name ) }}</td>
            </tr>
            <tr>
                <td class="label">Mother Occupation</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->mother_occupation }}</td>
            </tr>
            <tr>
                <td class="label">Mother Phone No</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->mother_ph_no }}</td>
            </tr>
        </table>

    </div>

    <div class="page-break"></div>

    {{-- <! CONSOLIDATED MARKS > --}}
    @foreach($report->groupBy('category') as $category => $tests) 
        @if(!$loop->first)
            <div class="page-break"></div>
        @endif

    <div class="container">
        
        <!-- Header -->
        <div class="text-center">
            <div class="title">GREEN PARK COACHING CENTRE ({{ $student->branch->name ?? '' }})</div>
            <div class="subtitle" style="margin-bottom: 20px;">STUDENTS CONSOLIDATED MARKS</div>
        </div>

        <table class="sub-header">
            <tr>
                <td class="text-bold" style="font-size: 15px;">
                    Student Name : <span style="font-weight: bold;">{{ strtoupper($student->student_name ?? '') }}</span>
                </td>
                <td style="text-align: right; font-size: 15px;" class="text-bold">
                    Course : {{ $student->course ?? '' }}
                </td>
            </tr>
        </table>

        

        @php
            $subjects = ['phy' => 'Phy','che' => 'Che','bot' => 'Bot','zoo' => 'Zoo','bio' => 'Bio',];
            $showSubjects = [];
            foreach ($subjects as $key => $label) {
                $showSubjects[$key] = $tests->contains(function ($row) use ($key) {
                    return ($row->{$key.'_r'} ?? 0) > 0 || ($row->{$key.'_w'} ?? 0) > 0 || ($row->{$key.'_l'} ?? 0) > 0 || ($row->{$key.'_tot'} ?? 0) > 0;
                });
            }
        @endphp
        <div class="section-title">{{ strtoupper($category) }}</div>

        <!-- Dynamic Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Exam Date</th>
                    <th>Exam Name</th>
                    @foreach($subjects as $key => $label)
                        @if($showSubjects[$key])
                            <th>{{ $label }}</th>
                        @endif
                    @endforeach

                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tests as $index => $row)

                    <tr>
                        <td>{{ $index + 1 }}.</td>
                        <td>
                            {{ \Carbon\Carbon::parse($row->exdate)->format('d-m-Y') }}
                        </td>
                        <td class="exam-name">
                           {{ $row->subject }}
                        </td>
                        @foreach($subjects as $key => $label)
                            @if($showSubjects[$key])
                                <td>{{ $row->{$key.'_tot'} ?? 'AB' }}</td>
                            @endif
                        @endforeach

                        <td>{{ $row->nettot ?? 'AB' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 15px;">
                            No Exam Reports Found for this Student.
                        </td>
                    </tr>
                @endforelse

               @if($tests->count() > 0)
                <tr class="avg-row">
                    <td colspan="3" class="text-bold">Average</td>

                  @foreach($subjects as $key => $label)
                    @if($showSubjects[$key])
                        <td>{{ round($tests->avg($key.'_tot')) }}</td>
                    @endif
                @endforeach

                <td>{{ round($tests->avg('nettot')) }}</td>
                </tr>
                @endif  
            </tbody>
        </table>
        @endforeach
    </div>

</body>
</html>
