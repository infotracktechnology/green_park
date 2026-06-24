<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Hostel Attendance List - {{ $room ?? $section ?? '' }}</title>
  <style>
    @page {
      size: portrait;
      margin: 18px;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 10px;
      color: #000;
      margin: 0;
      padding: 0;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
    }
    .header-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
      font-weight: bold;
    }
    .main-table {
      width: 100%;
      border-collapse: collapse;
    }
    .main-table th, .main-table td {
      border: 1px solid #000;
      padding: 5px 6px;
      font-size: 9px;
      height: 18px; 
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
      
      <td style="font-size: 18px; padding: 6px; letter-spacing: 0.5px;">
        {{ strtoupper($hostelName ?? 'GREEN PARK COACHING CENTRE, NAMAKKAL') }}
      </td>
    </tr>
    <tr>
      <td style="font-size: 18px; padding: 4px; font-weight: bold; letter-spacing: 0.5px;">
        ATTENDANCE LIST
      </td>
    </tr>

    <tr style="font-size: 12px; text-align: left;">
      <td style="font-weight: bold; padding-left: 8px; height: 16px;">
        @if(isset($room))
          ROOM NO : {{ $room }}
        @else
          SEC : LT - {{ $section ?? '' }}
        @endif
      </td>
    </tr>
  </table>

  <table class="main-table">
    <thead>
      <tr>
        <th width="6%" class="text-center">S.NO</th>
        <th width="14%" class="text-center" style="font-size: 8px;">OMR ROLL<br>NO</th>
        <th width="27%" class="text-left">STUDENT NAME</th>
        <th width="27%" class="text-center">&nbsp;</th> 
        <th width="13%" class="text-center">&nbsp;</th> 
        <th width="13%" class="text-center">&nbsp;</th> 
      </tr>
    </thead>
    <tbody>
      
      @foreach($students as $index => $student)
      <tr>
        <td class="text-center bold">{{ $index + 1 }}</td>
        <td class="text-center bold">{{ $student->student_id ?? $student->user_name ?? 'N/A' }}</td>
        <td class="text-left bold" style="text-transform: uppercase;">
          {{ $student->student_name }}
        </td>
        <td class="text-left bold" style="text-transform: uppercase;">{{ $student->father_name }}</td>
        <td></td> 
        <td></td> 
      </tr>
      @endforeach

      @if(count($students) < 15)
        @for($rowNum = count($students) + 1; $rowNum <= 15; $rowNum++)
        <tr>
          <td class="text-center bold">{{ $rowNum }}</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        @endfor
      @endif

    </tbody>
  </table>

</body>
</html>