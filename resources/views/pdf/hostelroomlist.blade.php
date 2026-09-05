<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Room Wise Attendance - Room {{ $room }}</title>
<style>
 @page {
      size: A4 landscape;
      margin: 10mm 12mm;
    }

    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 10px;
      color: #000;
      margin: 0;
      padding: 0;
    }

    /* Header Styling */
    .header-box {
      width: 100%;
      border: 1.5px solid #000;
      border-collapse: collapse;
      margin-bottom: 8px;
    }

    .header-box td {
      padding: 4px 8px;
      text-align: center;
    }

    .title-main {
      font-size: 15px;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      border-bottom: 1px solid #000;
    }

    .title-sub {
      font-size: 11px;
      font-weight: bold;
      letter-spacing: 0.3px;
      border-bottom: 1px solid #000;
      background-color: #f5f5f5;
    }

    .meta-row {
      display: flex;
      justify-content: space-between;
      font-size: 10px;
      font-weight: bold;
      padding: 4px 10px !important;
    }

    /* Table Styling */
    .attendance-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }

    .attendance-table thead {
      display: table-header-group; 
    }

    .attendance-table tr {
      page-break-inside: avoid;
    }
    .attendance-table thead {
      display: table-header-group;
    }

    .attendance-table th, 
    .attendance-table td {
      border: 1px solid #000;
      padding: 4px 3px;
      font-size: 9.5px;
      vertical-align: middle;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .attendance-table th {
      background-color: #c9d0d3;
      font-weight: bold;
      text-align: center;
    }

    .student-row td {
      height: 22px;
    }
    /* .summary-table {
      page-break-before: always;
      break-before: page;
    } */

    .summary-table tr {
        page-break-inside: avoid;
    }

    .summary-row td {
      height: 20px;
      background-color: #c9d0d3;
      font-weight: bold;
    }

    /* Utility Classes */
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .uppercase { text-transform: uppercase; }
</style>
</head>
<body>

  <table class="header-box">
    <tr>
      <td class="title-main">
        {{ strtoupper($hostelName) }} - {{ strtoupper(($branchname) ?? 'GREEN PARK ') }}
      </td>
    </tr>
    <tr>
      <td class="title-sub">
        STUDENTS ROOM WISE ATTENDANCE
      </td>
    </tr>
    <tr>
      
      <td class="text-left" style="font-size: 11px; padding-left: 10px;">
        ROOM NO : {{ $room }}
      </td>
    </tr>
  </table>

    <table class="attendance-table">
    <thead>
      <tr>
        <th style="width: 4%;" rowspan="2">S.NO</th>
        <th style="width: 24%;" rowspan="2" class="text-left" style="padding-left: 6px;">STUDENT NAME</th>
        <th style="width: 5%;" rowspan="2">ROOM NO</th>
        <th style="width: 7%;" rowspan="2">COT NO</th>
        <th colspan="14" style="height: 16px; font-size: 9px; letter-spacing: 1px;">
          ATTENDANCE DATES
        </th>
      </tr>
      <tr>
        @for($i = 1; $i <= 14; $i++)
          <th style="width: 4.28%; height: 16px; font-size: 8px; color: #555;">
            &nbsp;/&nbsp;
          </th>
        @endfor
      </tr>
    </thead>
    <tbody>

      @foreach($students as $key => $student)
        <tr class="student-row">
          <td class="text-center font-bold">{{ $key + 1 }}</td>
          <td class="text-left uppercase font-bold" style="padding-left: 6px;">
            {{ $student->student_name }}
          </td>
          <td class="text-center">{{ $student->room_no }}</td>
          <td class="text-center font-bold">{{ $student->cots_no }}</td>

          @for($col = 1; $col <= 14; $col++)
            <td></td>
          @endfor
        </tr>
      @endforeach

      </table>

  <table class="attendance-table summary-table">
    <tbody>
      @php
        $summaries = [
          'MORNING PRESENT',
          'NIGHT PRESENT',
          'MORNING SIGN',
          'NIGHT SIGN'
        ];
      @endphp

      @foreach($summaries as $label)
        <tr class="summary-row">
          <td colspan="4"
              class="text-right font-bold"
              style="width: 40%; padding-right: 12px; letter-spacing: 0.5px; ">
            {{ $label }} :
          </td>

          @for($col = 1; $col <= 14; $col++)
            <td style="width: 4.28%; height: 16px; font-size: 8px; color: #555;"></td>
          @endfor
        </tr>
      @endforeach
    </tbody>
  </table>

</body>
</html>