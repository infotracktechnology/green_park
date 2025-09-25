<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>DOOR NAME LIST</title>
  <style type="text/css">
    body {
      font-family: Arial, Helvetica, sans-serif;
      padding: 0 !important;
      margin: 0 !important;
    }

     @page {
      margin: 15px 30px 40px 30px !important;
    }


    h2,
    h4 {
      text-align: center;
      margin: 2px;
    }

    table {
      border-collapse: collapse;
      margin: 0px;
      width: 100%;
    }

    .table th,
    .table thead th,
    .table td {
      border: 1px solid #222;
      padding: 1px 3px;
      font-size: 12px !important;
    }

   th {
      background-color: #c2c7cc !important;
      font-size: 14px !important;
      padding: 5px !important;
    }
    
  </style>
</head>

<body>
  <h2>GREEN PARK COACHING CENTRE ({{ $branchname }})</h2>
  <h4>STUDENTS NAME LIST</h4>
  <h3>SEC: {{ $section }}</h3>

  <table style="width: 100%;">
    @foreach($students->chunk(100) as $student)
    <tr>
      @foreach($student->chunk(50) as $row)
      <td style="vertical-align: top;padding: 0px 5px;">
        <table class="table">
          <tr>
            <th>Roll No</th>
            <th>OMR NO</th>
            <th>STUDENT NAME</th>
          </tr>
          @foreach($row as $key => $item)
          <tr>
            <td style="text-align: center;font-weight: bold;">{{ $key+1 }}</td>
            <td style="text-align: center;font-weight: bold;">{{ $item->student_id }}</td>
            <td>{{ $item->student_name }}</td>
          </tr>
          @endforeach
        </table>
      </td>
      @endforeach
    </tr>
    @endforeach
  </table>

</body>

</html>