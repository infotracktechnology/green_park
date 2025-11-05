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
  @foreach($answers->groupBy('student_id') as $answer)
  <div class="page">
    <div class="title">OMR VALUATION REPORT {{ $test_name }}</div>
     <table style="width: 100%;">
    <tr>
    <?php $cols = $answer->chunk(45); ?>
      @foreach($cols as $col)
      <td style="padding: 0px 10px;vertical-align: top;">
      <table class="table"  style="width: {{ 25 * count($cols) }}%;">
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

   
  </div>
  @endforeach
</body>

</html>