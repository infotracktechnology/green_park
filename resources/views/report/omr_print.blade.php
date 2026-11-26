<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>{{ $section }}_omr print</title>
  <style>
    @page {
      margin: 40px 25px 15px 25px;
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      font-family: Arial, sans-serif;
      font-size: 10px;
      margin: 0;
      padding: 0;
    }

    .page {
      margin: 0;
      padding: 0;
      page-break-after: always;
      break-after: page;
    }

    .page:last-child {
      page-break-after: auto;
      break-after: auto;
    }

    .title {
      text-align: center;
      font-weight: bold;
      margin: 0 0 6px 0;
    }

    .layout-table {
      width: 100%;
      border-collapse: collapse;
    }

    .layout-col {
      padding: 0 8px;
      vertical-align: top;
    }

    .table {
      border-collapse: collapse;
      width: 100%;
    }

    .table th, .table td {
      border: 1px solid #000;
      padding: 2px;
      text-align: center;
    }

    .bold {
      font-weight: bold;
      background: #f7f7f7;
    }

    .header {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      margin: 10px 0 4px 0;
      font-size: 16px;
    }

    .size { font-size: 11px; }
    .sub { font-size: 12px; }
    .summary { font-size: 15px; font-weight: bold; }
    .total { font-size: 18px; font-weight: bold; }
    .key { font-size: 14px; margin: 6px 0 0 0; }

    @media print {
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>
  @foreach($answers->groupBy('student_id') as $student_id => $answer)
  @php
    $firstAnswer = $answer->first();
    $qTypes = $answer->pluck('qtype')->filter()->unique()->implode(', ');
    $validAnswers = $answer->where('answer_key', '!=', 'DEL');
    $testId = $firstAnswer->test_id;
    $studentKeyCorrection = $key_correction->get($testId);
  @endphp

  <div class="page">
    <div class="title">OMR VALUATION REPORT {{ $test_name }}</div>
    <div class="title">Q Type: {{ $qTypes ?: '-' }}</div>

    <table class="layout-table">
      <tr>
        @foreach($answer->chunk(36) as $col)
        <td class="layout-col">
          <table class="table">
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
                <td class="size">{{ $a->q_no }}</td>
                <td class="size">{{ $a->answer_key ?? '-' }}</td>
                <td class="size">{{ $a->answer }}</td>
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
      <span>STUDENT: {{ $firstAnswer->student_name }}</span>
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
        @foreach($validAnswers->groupBy('subject') as $sub => $subLogs)
        <tr class="bold">
          <td class="sub">{{ $sub }}</td>
          <td class="summary">{{ $subLogs->where('mark', 4)->count() }}</td>
          <td class="summary">{{ $subLogs->where('mark', -1)->count() }}</td>
          <td class="summary">{{ $subLogs->where('mark', 0)->count() }}</td>
          <td class="summary">{{ $subLogs->sum('mark') }} / {{ $subLogs->count() * 4 }}</td>
        </tr>
        @endforeach
        <tr class="bold">
          <td class="sub">Total</td>
          <td class="summary">{{ $validAnswers->where('mark', 4)->count() }}</td>
          <td class="summary">{{ $validAnswers->where('mark', -1)->count() }}</td>
          <td class="summary">{{ $validAnswers->where('mark', 0)->count() }}</td>
          <td class="total">{{ $validAnswers->sum('mark') }} / {{ $validAnswers->count() * 4 }}</td>
        </tr>
      </tbody>
    </table>

    @if(!empty($studentKeyCorrection))
      <p class="key">Key Correction: Q.NO: {{ $studentKeyCorrection }}</p>
    @endif
  </div>
  @endforeach

  <script>
    window.onload = function() {
      window.print();
    }
    window.onafterprint = function() {
      window.close();
    }
  </script>
</body>
</html>