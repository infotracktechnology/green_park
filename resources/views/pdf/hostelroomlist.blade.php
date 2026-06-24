<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Room Wise Attendance - Room {{ $room }}</title>
  <style>
    @page {
      size: landscape;
      margin: 15px;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      color: #000;
      margin: 0;
      padding: 0;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 5px;
    }
    .header-table td {
      border: 1px solid #000;
      padding: 6px;
      text-align: center;
      font-weight: bold;
    }
    .main-table {
      width: 100%;
      border-collapse: collapse;
    }
    .main-table th, .main-table td {
      border: 1px solid #000;
      padding: 5px;
      font-size: 10px;
      height: 20px; 
    }
    .main-table th {
      background-color: #E2E2E2 !important;
      text-align: center;
      font-weight: bold;
    }
    .text-center {
      text-align: center;
    }
    .text-left {
      text-align: left;
    }
    .bold {
      font-weight: bold;
    }
  </style>
</head>
<body>

  <table class="header-table">
    <tr>
      <td style="font-size: 18px; letter-spacing: 1px;">
        {{ strtoupper($hostelName) }} - {{ strtoupper(($branchname) ?? 'GREEN PARK ') }}
      </td>
    </tr>
    <tr>
      <td style="font-size: 13px;">
        STUDENTS ROOM WISE ATTENDANCE
      </td>
    </tr>
    <tr>
      
      <td class="text-left" style="font-size: 11px; padding-left: 10px;">
        ROOM NO : {{ $room }}
      </td>
    </tr>
  </table>

  <table class="main-table">
    <thead>
      <tr>
        <th width="4%" rowspan="2" class="text-center">S.<br>NO</th>
        <th width="24%" rowspan="2" class="text-left">STUDENT NAME</th>
        <th width="6%" rowspan="2" class="text-center">ROOM</th>
        <th width="6%" rowspan="2" class="text-center">COT<br>NO</th>
        <th colspan="16" style="padding: 2px; font-size: 9px;">
          DATES
        </th>
      </tr>
      <tr>
        @for($i = 1; $i <= 16; $i++)
          <th width="3.7%" style="height: 12px; font-size: 8px;">&nbsp;&nbsp;/&nbsp;&nbsp;</th>
        @endfor
      </tr>
    </thead>
    <tbody>
      
      @for($rowNum = 1; $rowNum <= 26; $rowNum++)
        @php
          $studentIndex = $rowNum - 1;
          $student = isset($students[$studentIndex]) ? $students[$studentIndex] : null;
        @endphp
        
        <tr>
          <td class="text-center bold">{{ $rowNum }}</td>
          
          @if($student)
            <td class="text-left bold" style="text-transform: uppercase;">{{ $student->student_name }}</td>
            <td class="text-center bold">{{ $student->room_no }}</td>
            <td class="text-center bold">{{ $student->cots_no }}</td>
          @else
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          @endif

          @for($col = 1; $col <= 16; $col++)
            <td></td>
          @endfor
        </tr>
      @endfor

      <tr>
        <td class="text-center bold">27</td>
        <td class="bold">MORNING PRESENT</td>
        <td></td>
        <td></td>
        @for($col = 1; $col <= 16; $col++)
          <td></td>
        @endfor
      </tr>
      <tr>
        <td class="text-center bold">28</td>
        <td class="bold">NIGHT PRESENT</td>
        <td></td>
        <td></td>
        @for($col = 1; $col <= 16; $col++)
          <td></td>
        @endfor
      </tr>
      <tr>
        <td class="text-center bold">29</td>
        <td class="bold">MORNING SIGN</td>
        <td></td>
        <td></td>
        @for($col = 1; $col <= 16; $col++)
          <td></td>
        @endfor
      </tr>
      <tr>
        <td class="text-center bold">30</td>
        <td class="bold">NIGHT SIGN</td>
        <td></td>
        <td></td>
        @for($col = 1; $col <= 16; $col++)
          <td></td>
        @endfor
      </tr>

    </tbody>
  </table>

</body>
</html>