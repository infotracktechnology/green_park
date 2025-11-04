<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>Overall Mark Sheet</title>
  <style>
    body{font-family:Helvetica,Arial,sans-serif;font-size:11px;margin:0;padding:0;}
    table{border-collapse:collapse;width:100%;margin-bottom:10px;}
    th,td{border:1px solid #000;padding:5px;font-size:12px;text-align:center;page-break-inside:avoid;}
    th{font-weight:bold;background:#f2f2f2;}
    @page {margin: 15px 30px 40px 30px !important;}
    .name-cell{text-align:left!important;padding-left:5px!important;}
    thead{display:table-header-group;}
    tfoot{display:table-footer-group;}
  </style>
</head>
<body>
  <div class="container">
    <div style="text-align:center;">
      <h3 style="margin:0;">GREEN PARK COACHING CENTRE, NAMAKKAL</h3>
      <h4 style="margin:5px 0;">{{ $test_name }}</h4>
      <h4 style="margin:5px 0;">Section: {{ $section }}</h4>
    </div>

    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Roll No</th>
          <th>Q Type</th>
          <th class="name-cell">Name</th>
          @foreach($subjects as $subject)
            <th colspan="4">{{ $subject }}</th>
          @endforeach
          <th>Total</th>
        </tr>
        <tr>
          <th></th><th></th><th></th><th class="name-cell"></th>
          @foreach($subjects as $subject)
            <th>R</th><th>W</th><th>L</th><th>T</th>
          @endforeach
          <th></th>
        </tr>
      </thead>

      <tbody>
        @foreach($results as $i => $student)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $student['student_id'] }}</td>
          <td>{{ $student['test_id'] }}</td>
          <td class="name-cell">{{ $student['student_name'] }}</td>

          @foreach($subjects as $subject)
            <?php $sub = $student['subjects'][$subject] ?? ['right'=>0,'wrong'=>0,'left'=>0,'total'=>0];?>
            <td>{{ $sub['right'] }}</td>
            <td>{{ $sub['wrong'] }}</td>
            <td>{{ $sub['left'] }}</td>
            <td>{{ $sub['total'] }}</td>
          @endforeach

          <td>{{ $student['total'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</body>
</html>
