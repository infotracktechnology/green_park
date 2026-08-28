<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Range Report</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 8px;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
        }

        .outer-border {
            border: 2.5px solid #000;
            padding: 4px;
            min-height: 250mm;
        }

        .inner-border {
            border: 1.5px solid #000;
            padding: 20px 25px;
            min-height: calc(250mm - 12px);
        }

        .banner-container {
            width: 100%;
            text-align: center;
            border: 2px solid #000;
            margin-bottom: 20px;
        }

        .banner-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Report Headings */
        .report-type {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .red-text {
            color: #cc0000;
        }

        .test-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* First Mark Section */
        .first-mark-container {
            width: 85%;
            margin: 0 auto 8px auto;
            text-align: right;
        }

        .first-mark {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        /* Table Styling */
        table {
            width: 85%;
            margin: 0 auto;
            border-collapse: collapse;
            border: 1.5px solid #3c6e94;
        }

        th {
            background-color: #f5cdb4;
            color: #000;
            font-size: 16px;
            font-weight: 900;
            padding: 10px 6px;
            border: 1px solid #3c6e94;
            text-align: center;
            line-height: 1.2;
        }

        td {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 9px 8px;
            border: 1px solid #3c6e94;
        }

        .sno {
            color: #124378;
            width: 15%;
        }

        .range {
            color: #124378;
            width: 53%;
            letter-spacing: 0.5px;
        }

        .count {
            color: #0070c0;
            font-size: 18px;
            width: 32%;
        }
    </style>
</head>

<body>

    <div class="outer-border">
        <div class="inner-border">

            <div class="banner-container">
                @php
                    $imgPath = public_path('assets/img/image.png');
                    if (!file_exists($imgPath)) {
                        $imgPath = base_path('assets/img/image.png');
                    }
                @endphp
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imgPath)) }}" alt="Header">
            </div>


            <div class="report-type">
                <span class="red-text">{{ $coachingTypes }}</span> RANGE REPORT
            </div>

            <div class="test-title">
                {{ $test_name }}
            </div>

            <div class="first-mark-container">
                <span class="first-mark">
                    FIRST MARK: {{ $firstMark }}
                </span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">SNO</th>
                        <th style="width: 53%;">MARKS RANGE</th>
                        <th style="width: 32%;">NO: OF<br>STUDENTS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rangeReport as $index => $row)
                        <tr>
                            <td class="sno">
                                {{ $index + 1 }}
                            </td>
                            <td class="range">
                                {{ $row['range'] }}
                            </td>
                            <td class="count">
                                {{ $row['count'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</body>

</html>