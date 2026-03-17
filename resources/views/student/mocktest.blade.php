@extends('layouts.dashboard')

@section('title', 'Mock Test (OMR Sheet)')

@section('main')
<style>
  .omr-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      text-align: center;
  }
  
  .omr-table td, .omr-table th {
      border: 1px solid #dee2e6;
      padding: 6px 4px;
      vertical-align: middle;
  }
  
  .omr-table th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
  }
  
  .q-num {
      color: #28a745;
      font-weight: bold;
      width: 30px;
  }

  .omr-radio {
      appearance: none;
      width: 19px;
      height: 19px;
      border: 1px solid #adb5bd;
      border-radius: 50%;
      margin: 0 3px;
      cursor: pointer;
      vertical-align: middle;
      transition: all 0.2s ease;
  }
  
  .omr-radio:checked {
      background-color: #000;
      border-color: #000;
      box-shadow: inset 0 0 0 2px #fff;
  }
</style>

<div class="main-content">
  <div class="card card-primary">
    <div class="card-header d-flex justify-content-between">
      <h4>Mock Test (OMR Sheet)</h4>
    </div>
      {{-- Timer Display --}}

      <div class="card-body">
        @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
        @endif

        @if(session('download_pdf'))
        <div class="alert alert-info d-flex justify-content-between align-items-center">
          <div>
            MockTest PDF is ready for download.
          </div>
          <a href="{{ route('student.mocktestpdf', session('download_pdf')) }}" class="btn btn-success">
            <i class="fas fa-download"></i> Download Response PDF
          </a>
        </div>
        @else

        <form action="{{ route('student.mock') }}" method="GET">
          <div class="row">
            <div class="form-group col-lg-3">
              <select class="select2" id="exam_name" name="exam_name" required>
                <option value="">-- Select Exam Name --</option>
                @foreach ($mocktests as $row)
                <option value="{{$row->exam_name}}" @selected($row->exam_name==request('exam_name'))>{{$row->exam_name}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-lg-2">
              <button type="submit" class="btn btn-primary btn-block">START TEST</button>
            </div>

          @if($exam && $timer)
          <div class="form-group col-lg-2">
            <div class="badge badge-danger font-weight-bold" style="font-size: 1.2rem;">
              Time Remaining: <span id="timerDisplay">Loading...</span>
            </div>
          </div>
          @endif
          </div>

        </form>

        @if($exam)
        <form id="omrForm" action="{{ route('student.mock') }}" onsubmit="return confirm('Are you sure you want to submit?')" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="table-responsive">
            <input type="hidden" name="testname" value="{{ $exam->name }}">
            <input type="hidden" name="test_id" value="{{ $exam->testid }}">
            <input type="hidden" name="student_id" value="{{ $student->student_id }}">
            <table class="omr-table">
              <thead>
                <tr>
                  @for($i = 0; $i < 5; $i++) 
                    <th class="q-num">Q.No</th>
                    <th>RESPONSE <br> 1 &nbsp; 2 &nbsp; 3 &nbsp; 4</th>
                  @endfor
                </tr>
              </thead>
              <tbody>
                <?php 
                $tq = $exam->total_questions/5;
                $Subjects =[];
                $qno = 1;
                foreach(explode(',', $exam->subject_name) as $col) {
                  $subjectkey = strtolower($col);
                  $subtotal = (int) ($exam->{"{$subjectkey}_questions"} ?? 0);
                  for($i = 1; $i <= $subtotal; $i++){
                    $Subjects[$qno] = strtoupper($col);
                    $qno++;
                  }
                }
                ?>
                @for($row = 1; $row <= $tq; $row++) 
                  <tr>
                  @for($col = 0; $col < 5; $col++) 
                    <?php $q = $row + ($col * $tq); ?> 
                    <td>{{ $q }}
                      <input type="hidden" name="subject[{{ $q }}]" value="{{ $Subjects[$q] ?? '' }}">
                    </td>
                    <td>
                      @for($opt = 1; $opt <= 4; $opt++) 
                        {{-- Changed to input type="radio" --}}
                        <input type="radio" class="omr-radio" name="answers[{{ $q }}]" value="{{ $opt }}">
                      @endfor
                    </td>
                  @endfor
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Final Submit</button>
          </div>
        </form>
        @endif

        @endif
      </div>
    </div>
  </div>
@endsection

@section('js')
<script>
  $(document).ready(function() {
      $('.omr-radio').on('click', function() {
          var name = $(this).attr('name');
          if ($(this).data('waschecked') == true) {
              $(this).prop('checked', false);
              $(this).data('waschecked', false);
          } else {
              $(this).data('waschecked', true);
          }
          $('input[name="' + name + '"]').not(this).data('waschecked', false);
      });
  
      var distance = {{ $timer ?? 0 }};
  
      @if($exam && $timer)  
          var timerInterval = setInterval(function() {
              if (distance <= 0) {
                  clearInterval(timerInterval);
                  $('#timerDisplay').text("00h 00m 00s");
                  $('#omrForm').removeAttr('onsubmit');
                  $('#omrForm').submit();
              } else {
                  distance--;
                  var hours = Math.floor(distance / 3600);
                  var minutes = Math.floor((distance % 3600) / 60);
                  var seconds = distance % 60;
                  $('#timerDisplay').text(
                      `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
                  );
              }
          }, 1000);
      @endif
  });
</script>
@endsection