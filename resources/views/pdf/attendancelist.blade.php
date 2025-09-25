<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>ATTENDANCE LIST</title>
  <style type="text/css">
    body {
      font-family: Arial, Helvetica, sans-serif;
      padding: 0 !important;
      margin: 0 !important;
    }

    @page {
      margin: 15px 30px 40px 30px !important;
    }


    h2,h4 {
      text-align: center;
      margin: 2px;
    }

    table {
      border-collapse: collapse;
      margin: 0px;
      width: 100%;
    }

    .table th,.table thead th,.table td {
      border: 1px solid #222;
      padding: 1px 3px;
      font-size: 12px !important;
    }

    th {
      background-color: #c2c7cc !important;
      font-size: 14px !important;
      padding: 8px !important;
    }
    .logo {
      height: 70px;
    }
  </style>
</head>

<body>
  <table style="width: 100%;">
    <tr>
      <td style="width: 10%;">
       <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(asset('img/favicon.png'))) }}">
      </td>
      <td style="width: 80%;">
        <h2>GREEN PARK COACHING CENTRE ({{ $branchname }})</h2>
        <h4>STUDENTS ATTENDANCE LIST - {{ $students[0]->academic_year }}</h4>
      </td>
    </tr>
  </table>

  <h3>SEC: {{ $section }}</h3>
  <table class="table">
    <tr>
      <th>Roll No</th>
      <th>OMR NO</th>
      <th>STUDENT NAME</th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
    @foreach($students as $key => $item)
    <tr>
      <td style="text-align: center;font-weight: bold;">{{ $key+1 }}</td>
      <td style="text-align: center;font-weight: bold;">{{ $item->student_id }}</td>
      <td style="width: 40%;">{{ $item->student_name }}</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    @endforeach
    <tr>
      <th colspan="3">No of Students Present</th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
     <tr>
      <th colspan="3">No of Students Absent</th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
  </table>
</body>

</html>