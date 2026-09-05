<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Phone Turn Register - {{ $room ?? $section ?? '' }}</title>
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
      padding: 5px 4px;
      font-size: 9px;
      height: 18px; 
    }
    .main-table th {
      background-color: #c9d0d3 !important;
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
      <td colspan="3" style="font-size: 15px; padding: 6px; letter-spacing: 0.5px;">
        {{ strtoupper($hostelName) }} - {{ strtoupper($branchname) }}
      </td>
    </tr>
    <tr>
      <td colspan="3" style="font-size: 11px; padding: 4px; font-weight: bold;">
        PHONE TURN REGISTER
      </td>
    </tr>
    <tr style="font-size: 9px; text-align: left; font-weight: normal;">
    <td width="34%" style="border-right: none; font-weight: bold; padding-left: 8px; height: 16px;">
        <div style="text-align: left;">
            @if(isset($room))
                ROOM NO : {{ $room }}
            @else
                CLASS : {{ $section ?? '' }}
            @endif
        </div>
    </td>

      <td width="33%" style="border-left: none; border-right: none; font-weight: bold; text-align: center;">
        MONTH :
      </td>
      <td width="33%" style="border-left: none; font-weight: bold; text-align: right;  padding-right: 80px;">
        INCHARGE :
      </td>
    </tr>
  </table>

  <table class="main-table">
    <thead>
      <tr>
        <th width="5%" class="text-center">S.<br>NO</th>
        <th width="32%" class="text-left bold">STUDENT NAME</th>
        @for($col = 1; $col <= 14; $col++)
          <th width="3.8%">&nbsp;</th>
        @endfor
        {{-- <th width="12%" class="text-center" style="font-size: 8px;">TOTAL<br>AMOUNT</th>
        <th width="13%" class="text-center">SIGN</th> --}}
      </tr>
    </thead>
    <tbody>
      
      @foreach($students as $index => $student)
      <tr>
        <td class="text-center bold">{{ $index + 1 }}</td>
        <td class="text-left bold" style="text-transform: uppercase;">
          {{ $student->student_name }}
        </td>
        
        @for($col = 1; $col <= 14; $col++)
          <td></td>
        @endfor
      </tr>
      @endforeach

      @if(count($students) < 15)
        @for($rowNum = count($students) + 1; $rowNum <= 15; $rowNum++)
        <tr>
          <td class="text-center bold">{{ $rowNum }}</td>
          <td>&nbsp;</td>
          @for($col = 1; $col <= 14; $col++)
            <td></td>
          @endfor
       
        </tr>
        @endfor
      @endif

    </tbody>
  </table>

</body>
</html>