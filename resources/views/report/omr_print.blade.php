<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>OMR VALUATION REPORT</title>
  <style>
     body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
    .title { text-align: center; font-weight: bold; margin-bottom: 10px; }
    .table { border-collapse: collapse; width: 100%; }
    .table th, .table td { border: 1px solid #000; padding: 2px; text-align: center; }
    .page { page-break-after: always; }
     @page {margin: 15px 30px 40px 30px !important;}
    .bold { font-weight: bold; background: #f7f7f7; }
    .header { display: flex; justify-content: space-between; font-weight: bold; margin-top: 10px; }
  </style>
</head>

<body>
  @foreach($students as $s)
  <div class="page">
    <div class="title">OMR VALUATION REPORT - {{ $s['test_id'] }}</div>

     <table style="width: 100%;">
        <tr>
      @foreach($s['answers'] as $col)
      <td style="padding: 0px 10px;vertical-align: top;">
      <table class="table"  style="width: {{ 25 * count($s['answers']) }}%;">
        <thead>
          <tr>
            <th>Q</th>
            <th>Key</th>
            <th>Ans</th>
            <th>Res</th>
          </tr>
        </thead>
        <tbody>
          @foreach($col as $a)
          <tr>
            <td>{{ $a->q_no }}</td>
            <td>{{ $a->answer_key ?? '-' }}</td>
            <td>{{ $a->answer }}</td>
            <td>
              @if($a->answer_key === 'DEL') DEL
              @elseif($a->mark == 4) C
              @elseif($a->mark == -1) W
              @else L
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
        </table>
        </td>
      @endforeach
    </tr>
    </table>

    <div class="header">
      <span>STUDENT: {{ $s['student_name'] }}</span>
      <span>EXAM: {{ $test_name }}</span>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Subject</th>
          <th>Right</th>
          <th>Wrong</th>
          <th>Left</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($s['subjects'] as $sub)
        <tr>
          <td>{{ $sub['subject'] }}</td>
          <td>{{ $sub['right'] }}</td>
          <td>{{ $sub['wrong'] }}</td>
          <td>{{ $sub['left'] }}</td>
          <td>{{ $sub['total'] }}</td>
        </tr>
        @endforeach
        <tr class="bold">
          <td>Total</td>
          <td>{{ $s['totals']['totalRight'] }}</td>
          <td>{{ $s['totals']['totalWrong'] }}</td>
          <td>{{ $s['totals']['totalLeft'] }}</td>
          <td>{{ $s['totals']['totalMarks'] }} / {{ $s['totals']['maxMarks'] }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  @endforeach
</body>

</html>