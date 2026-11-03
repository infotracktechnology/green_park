<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>{{ $section }}_omr print</title>
  <style>
     body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
    .title { text-align: center; font-weight: bold; margin-bottom: 10px; }
    .table { border-collapse: collapse; width: 100%; }
    .table th, .table td { border: 1px solid #000; padding: 2px; text-align: center; }
    .page { page-break-after: always; }
     @page {margin: 0px 30px 20px 30px !important;}
    .bold { font-weight: bold; background: #f7f7f7; }
    .header { display: flex; justify-content: space-between; font-weight: bold; margin-top: 10px; font-size: 16px; }
    .size { font-size: 11px; }
    .sub { font-size: 12px; }
    .summary { font-size: 15px; font-weight: bold; }
    .total { font-size: 18px; font-weight: bold; }
    .key { font-size: 14px;}
  </style>
</head>

<body>
  @foreach($answers->groupBy('student_id') as $student_id => $answer)
  <div class="page">
    <div class="title">OMR VALUATION REPORT {{ $test_name }}</div>
    <div class="title">Q Type: {{ $answer->pluck('subject')->unique()->implode(', ') }}</div>
     <table style="width: 100%;">
    <tr>
    <?php $cols = $answer->chunk(36); ?>
      @foreach($cols as $col)
      <td style="padding: 0px 10px;vertical-align: top;">
      <table class="table" style="width: 100%;">
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
      <span>STUDENT: {{ $answer->first()->student_name }}</span>
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
      @php
      $validAnswers = $answer->filter(function ($a) {
        return $a->answer_key !== 'DEL';
        });
      @endphp
        @foreach($validAnswers->pluck('subject')->unique() as $sub)
        <tr class="bold">
            <td class="sub">{{ $sub }}</td>
            <td class="summary">{{ $validAnswers->where('subject', $sub)->where('mark', 4)->count() }}</td>
            <td class="summary">{{ $validAnswers->where('subject', $sub)->where('mark', -1)->count() }}</td>
            <td class="summary">{{ $validAnswers->where('subject', $sub)->where('mark', 0)->count() }}</td>
            <td class="summary">{{ $validAnswers->where('subject', $sub)->sum('mark') }} / {{ $validAnswers->where('subject', $sub)->count() * 4 }}
            </td>
        </tr>
        @endforeach
        <tr class="bold">
          <td>Total</td>
          <td class="summary">{{ $validAnswers->where('mark', 4)->count() }}</td>
          <td class="summary">{{ $validAnswers->where('mark', -1)->count() }}</td>
          <td class="summary">{{ $validAnswers->where('mark', 0)->count() }}</td>
          <td class="total">{{ $validAnswers->sum('mark') }} / {{ $validAnswers->count() * 4 }}</td>
        </tr>
      </tbody>
    </table>
    @php
        $testId = $answer->first()->test_id;
        $studentKeyCorrection = $key_correction->get($testId);
    @endphp
    @if($key_correction->isNotEmpty())
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