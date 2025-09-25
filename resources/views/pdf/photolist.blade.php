<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>Long Term Photo LIST</title>
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
      padding: 3px;
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
        <h4>Long Term Photo LIST - {{ $students[0]->academic_year }}</h4>
      </td>
    </tr>
  </table>

  <h3>SEC: {{ $section }}</h3>
  <table class="table">
    <tr>
      <th>S.No</th>
      <th>OMR No</th>
      <th>Student Name</th>
      <th>Photo Number</th>
    </tr>
    @foreach($students as $key => $item)
    <tr>
      <td style="text-align: center;font-weight: bold;">{{ $key+1 }}</td>
      <td style="text-align: center;font-weight: bold;">{{ $item->student_id }}</td>
      <td style="width: 40%;font-weight: bold;">{{ $item->student_name }}</td>
      <td></td>
    </tr>
    @endforeach
  </table>
</body>

</html>