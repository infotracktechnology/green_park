<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verification Form</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm; 
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 12px;
            font-weight: bold;
            background: #fff;
            margin: 40px;
        }

        .page-container {
            width: 100%;
            padding: 5px 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .logo-cell {
            width: 80px;
            vertical-align: middle;
            text-align: left;
        }

        .logo-cell img {
            width: 70px;
            height: auto;
        }

        .title-cell {
            text-align: center;
            vertical-align: middle;
            padding-right: 70px; 
        }

        .college-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .report-subtitle {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .section-title-wrap {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1.5px dotted #000;
            padding-bottom: 2px;
            display: inline-block;
            text-transform: uppercase;
        }

        .particulars-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .particulars-info-col {
            width: 75%;
            vertical-align: top;
        }

        .particulars-box-col {
            width: 25%;
            vertical-align: top;
            text-align: right;
            padding-right: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3.5px 0;
            font-size: 11px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-lbl {
            width: 32%;
            white-space: nowrap;
        }

        .info-colon {
            width: 4%;
            text-align: center;
        }

        .info-val {
            width: 64%;
            text-transform: uppercase;
        }

        .ac-status-box {
            width: 95px;
            height: 30px;
            border: 1.2px solid #000;
            text-align: center;
            line-height: 38px;
            font-size: 12px;
            font-weight: bold;
            float: right;
            text-transform: uppercase;
            padding-bottom: 30px;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .checklist-table th {
            font-size: 12px;
            font-weight: bold;
            padding-bottom: 8px;
            text-align: center;
        }

        .checklist-table td {
            padding: 6px 2px;
            vertical-align: middle;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.3;
        }

        .q-col {
            width: 82%;
            padding-right: 15px;
        }

        .check-col {
            width: 9%;
            text-align: center;
        }

        .chk-box {
            width: 22px;
            height: 22px;
            border: 1.2px solid #000;
            margin: 0 auto;
        }

        .verify-text {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 25px;
        }

        .seals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .seal-box-container {
            width: 200px;
            height: 75px;
            border: 1.2px solid #000;
            position: relative;
        }

        .seal-box-container td {
            vertical-align: bottom;
            text-align: center;
            padding-bottom: 6px;
            font-size: 11px;
            font-weight: bold;
        }

        .incharge-cell {
            text-align: center;
            vertical-align: bottom;
            padding-bottom: 6px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="page-container">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}" alt="Logo">
            </td>
            <td class="title-cell">
                <div class="college-title">GREEN PARK INSTITUTE, NAMAKKAL</div>
                <div class="report-subtitle">VERIFICATION FORM (LONGTERM - {{ $student->academic_year ?? '2027' }})</div>
            </td>
        </tr>
    </table>

    {{-- STUDENT PARTICULARS --}}
    <div class="section-title-wrap">
        <span class="section-title" style="text-transform: none;">Student's Particulars</span>
    </div>

    <table class="particulars-wrapper">
        <tr>
            <td class="particulars-info-col">
                <table class="info-table">
                    <tr>
                        <td class="info-lbl">STUDENT'S NAME</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->student_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">USER NAME</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->user_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">GENDER</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->gender }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">CLASS & SECTION</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->class_section ?? 'LONGTERM - RSH11' }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">BATCH</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->batch ?? 'A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">FATHER'S NAME</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->father_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">FATHER'S MOBILE NO</td>
                        <td class="info-colon">:</td>
                        <td class="info-val">{{ $student->father_ph_no }}</td>
                    </tr>
                </table>
            </td>

            <td class="particulars-box-col">
                @if(strtoupper(trim($student->hostel_dayscholar ?? 'HOSTEL')) === 'HOSTEL')
                    <div class="ac-status-box">
                        {{ $student->AC_NON_AC ?? 'NON AC' }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- CHECKLIST --}}
    <div class="section-title-wrap" style="margin-top: 20px;">
        <span class="section-title">VERIFICATION CHECK LIST</span>
    </div>

    <table class="checklist-table">
        <thead>
            <tr>
                <th class="q-col"></th>
                <th class="check-col">YES</th>
                <th class="check-col">NO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="q-col">1. Whether the hostel admission form is completely filled and submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">2. Whether the visitor's card proforma is completely filled, photos pasted in specified places and submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">3. Whether the Hostel rules and regulations are completely studied, and duly signed by the parent and student ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">4. Whether colour Xerox copy of student Aadhaar Card is submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">5. Whether colour Xerox copies of id-proof for father,mother or guardian are submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">6. Whether colour Xerox copies of X & XII Marksheets are submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">7. Whether the Hostel Refund form is signed and submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">8. Whether one copy of student Recent colour passport size photo is submitted ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
            <tr>
                <td class="q-col">9. Whether the hostel allotment details are pasted on the cover containing documents 1 - 8 ?</td>
                <td class="check-col"><div class="chk-box"></div></td>
                <td class="check-col"><div class="chk-box"></div></td>
            </tr>
        </tbody>
    </table>

    {{-- FOOTER VERIFICATION NOTE --}}
    <div class="verify-text">
        All the documents mentioned above are checked and verified
    </div>

    <table class="seals-table">
        <tr>
            <td style="width: 35%; vertical-align: bottom;">
                <table class="seal-box-container">
                    <tr>
                        <td>Verified Seal</td>
                    </tr>
                </table>
            </td>

            <td style="width: 30%;" class="incharge-cell">
                Incharge
            </td>

            <td style="width: 35%; vertical-align: bottom;" align="right">
                <table class="seal-box-container" style="float: right;">
                    <tr>
                        <td>Submitted Seal</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>

</body>
</html>