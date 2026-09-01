<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Range Report</title>
    <style type="text/css">
        @page {
            size: A4 portrait;
            margin: 8mm; /* Clean page margin */
        }

        * {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            color: #000;
            background-color: #fff;
        }

       

        /* Banner Container */
        .banner-container {
            width: 100%;
            text-align: center;
            border: 1.5px solid #000;
            margin-bottom: 16px;
            line-height: 0;
        }

        .banner-container img {
            width: 100%;
            height: 150px;
            display: block;
        }

        /* Report Headings */
        .report-type {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .red-text {
            color: #cc0000;
        }

        .test-title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-wrap {
            width: 88%;
            margin-left: auto;
            margin-right: auto;
        }

        /* First Mark Section */
        .first-mark-container {
            width: 100%;
            text-align: right;
            margin-bottom: 8px;
        }

        .first-mark {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* Table Styling */
        table {
            width: 100%;
            table-layout: fixed; 
            border-collapse: collapse;
            border: 1.5px solid rgb(0, 0, 0);
            page-break-inside: avoid;
        }

        th {
            background-color: #f5cdb4;
            color: #000;
            font-size: 14px;
            font-weight: bold;
            padding: 9px 5px;
            border: 1px solid black;
            text-align: center;
            vertical-align: middle;
            line-height: 1.3;
        }

        td {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #000000;
            vertical-align: middle;
        }

        .col-sno {
            width: 16%;
            color: #124378;
        }

        .col-range {
            width: 52%;
            color: #124378;
            letter-spacing: 0.3px;
        }

        .col-count {
            width: 32%;
            color: #0070c0;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <div class="outer-border">
        <div class="inner-border">

            <!-- Banner Header -->
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

            <!-- Report Headings -->
            <div class="report-type">
                <span class="red-text">{{ $coachingTypes }}</span> RANGE REPORT
            </div>

            <div class="test-title">
                {{ $test_name }}
            </div>

            <!-- Content Area (Table + First Mark aligned together) -->
            <div class="content-wrap">
                <div class="first-mark-container">
                    <span class="first-mark">
                        FIRST MARK: {{ $firstMark }}
                    </span>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="col-sno">SNO</th>
                            <th class="col-range">MARKS RANGE</th>
                            <th class="col-count">NO: OF<br>STUDENTS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rangeReport as $index => $row)
                            <tr>
                                <td class="col-sno">
                                    {{ $index + 1 }}
                                </td>
                                <td class="col-range">
                                    {{ $row['range'] }}
                                </td>
                                <td class="col-count">
                                    {{ $row['count'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>

</html>