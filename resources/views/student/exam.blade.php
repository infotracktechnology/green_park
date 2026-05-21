@extends('layouts.dashboard')
@section('title', 'Examinations')
@section('css')
<style>
  .test-questions td a {
      padding: 8px 14px !important;
      background-size: 100% 100% !important;
  }
  
  .pagination {
      display: inline-block !important;
      padding-left: 0;
      margin: 20px 0;
      border-radius: 0px;
  }
  
  .pagination li {
      display: inline !important;
      padding: 0px 0px !important;
  }
  
  .pagination>li>a,
  .pagination>li>span {
      padding: 7px 12px;
      line-height: 2.9;
      text-decoration: none;
      border: none;
      background-size: 100% 100% !important;
      background-repeat: no-repeat !important;
      background-position: center !important;
  }
  table th,table td {
    border: none !important;
  }
  .info-table td {
    height: 20px !important;
  }
  
  .question-panel{
    overflow-x:hidden;
    height:auto;
  }
  .question-panel img{
    max-width:100%;
    height:auto;
    display:block;
  }
  
  /* Task 1: Utility to ensure gap for buttons if flex-gap isn't supported */
  .action-buttons button {
      margin-bottom: 8px;
      margin-right: 6px;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <form method="post" id="examForm" action="{{ route('exam.submit') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="test_id" value="{{ $exam->testid }}">
        <input type="hidden" name="testname" value="{{ $exam->name }}">
        <input type="hidden" name="student_id" value="{{ auth()->user()->student_id }}">
        <div class="row exam-paper">

          <!-- Warnings -->
          <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
              <p class="font-weight-bold mb-0"><i class="fa fa-exclamation-triangle"></i> Warning: Your exam will be automatically submitted if you switch tabs or leave the exam window.</p>
            </div>
          </div>

          <!-- Task 3: Added Instructions Panel -->
          <div class="col-md-12 mb-3">
            <div class="card shadow-sm border-info" style="border-radius: 5px;">
              <div class="card-body p-3">
                <h6 class="text-info font-weight-bold mb-2"><i class="fa fa-info-circle"></i> Instructions</h6>
                <div class="row small text-muted">
                  <div class="col-md-6">
                    <ul class="pl-3 mb-1">
                      <li><strong>Save & Next:</strong> Saves your selected answer and moves to the next question.</li>
                      <li><strong>Save & Mark For Review:</strong> Saves your answer and flags it for later review. (It will be evaluated)</li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <ul class="pl-3 mb-1">
                      <li><strong>Mark for Review & Next:</strong> Flags the question without answering and moves to the next question.</li>
                      <li><strong>Clear:</strong> Removes your currently selected answer.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <table>
              <tbody>
                <tr>
                  <td style="padding: 5px 15px;"><i class="fa fa-user" style="font-size:90px;"></i></td>
                  <td>
                    <table class="info-table">
                      <tbody>
                        <tr>
                          <td style="padding: 0px 5px;">Candidate Name</td>
                          <td> : <span class="col-orange">{{ auth()->user()->student_name  }}</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 0px 5px;">Exam Name</td>
                          <td> : <span class="col-orange">{{ $exam->name }}</span>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding: 0px 5px;">Subject Name</td>
                          <td> : <span class="col-orange"> {{ $exam->subject_name }}</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 0px 5px;">Total Questions</td>
                          <td> : <span class="col-orange"> {{ $exam->total_questions }}</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 0px 5px;">Remaining Time</td>
                          <td>
                            : <span class="badge badge-danger" id="timerDisplay">00:00:00</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-4">
            <div class="mt-2" style="border:dotted;">
              <table class="table table-borderless mb-0 test-questions">
                <thead>
                  <tr>
                    <td> <a class="not-attempted countNotVisited">1</a></td>
                    <td>Not Visited</td>
                    <td> <a class="not-answered countNotAnswered">1</a></td>
                    <td>Not Answered</td>
                  </tr>
                  <tr>
                    <td><a class="que-save countAnswered">1</a></td>
                    <td>Answered</td>
                    <td><a class="que-mark countMarked">1</a></td>
                    <td>Marked for Review</td>
                  </tr>
                  <tr>
                    <td><a class="que-save-mark countAnsweredAndMarked">1</a></td>
                    <td colspan="3">Answered &amp; Marked for Review (will be considered for evaluation)</td>
                  </tr>
                </thead>
              </table>
            </div>

            <div class="mt-2">
              @if($exam->physics_questions > 0)
              <button type="button" class="btn btn-primary m-1" onclick="openQuestion({{ $exam->phy_start }})">
                Physics ({{ $exam->physics_questions }})
              </button>
              @endif

              @if($exam->chemistry_questions > 0)
              <button type="button" class="btn btn-primary m-1" onclick="openQuestion({{ $exam->chem_start }})">
                Chemistry ({{ $exam->chemistry_questions }})
              </button>
              @endif

              @if($exam->botany_questions > 0)
              <button type="button" class="btn btn-primary m-1" onclick="openQuestion({{ $exam->bot_start }})">
                Botany ({{ $exam->botany_questions }})
              </button>
              @endif

              @if($exam->zoology_questions > 0)
              <button type="button" class="btn btn-primary m-1" onclick="openQuestion({{ $exam->zoo_start }})">
                Zoology ({{ $exam->zoology_questions }})
              </button>
              @endif

            </div>

          </div>
          <div class="col-md-8">
            <div class="question-container p-t-30 p-b-10">
              <input type="hidden" name="total_question" value="{{ count($exam->questions) }}">
              @foreach ($exam->questions as $index => $question)
              <?php
                $key = $index + 1;
               ?>
              <div id="question-{{ $key }}" class="question" style="display: {{ $key === 1 ? 'block' : 'none' }};">
                <div class="question-panel">
                  <p>Question {{ $key }}</p>
                  <img src="{{ env('APP_URL').$question['image'] }}" alt="Question Image">
                </div>

                <input type="hidden" name="subject[{{ $key }}]" value="{{ $question['subject'] }}">
                <table class="table table-borderless mb0">
                  <tbody>
                    <tr>
                      <td> <input type="radio" name="question[{{ $key }}]" value="1"> 1 ) </td>
                      <td> <input type="radio" name="question[{{ $key }}]" value="2"> 2 ) </td>
                      <td> <input type="radio" name="question[{{ $key }}]" value="3"> 3 ) </td>
                      <td> <input type="radio" name="question[{{ $key }}]" value="4"> 4 ) </td>
                    </tr>
                  </tbody>
                </table>

                <input type="hidden" name="status[{{ $key }}]" id="status-{{ $key }}" value="not-attempted">


                <div class="d-flex flex-wrap action-buttons mt-3">
                  <button type="button" class="btn-save btn btn-success" data-index="{{ $key }}"> {{ $key == $exam->total_questions ? 'Save & Submit' : 'Save & Next' }} </button>
                  <button type="button" class="btn btn-warning btn-save-mark-answer text-white" data-index="{{ $key }}">Save &amp; Mark For Review</button>
                  <button type="button" class="btn-mark btn btn-primary" data-index="{{ $key }}">Mark for Review & Next</button>
                  <button type="button" class="btn-reset btn btn-light" data-index="{{ $key }}">Clear</button>
                </div>
              </div>
              @endforeach
            </div>


            <div class="d-flex justify-content-between align-items-center flex-wrap m-t-20 mt-4 mb-4">
              <div class="action-buttons">
                <button type="button" class="btn btn-outline-secondary" id="btnPrevQue">
                  << Back </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnNextQue">Next >></button>
              </div>
              <button type="button" class="btn btn-success btn-submit-answer">Submit Exam</button>
            </div>

          </div>
          <div class="col-md-4">
            <div class="panel" style="overflow-y: scroll;">
              <ul class="pagination test-questions my-4">
                @foreach ($exam->questions as $index => $question)
                <?php
                  $key = $index + 1;
                ?>
                <li data-seq="{{ $key }}">
                  <a href="javascript:void(0);" class="not-attempted" data-index="{{ $key }}">
                    {{ $key < 10 ? '0' : '' }}{{ $key }}
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
        <div class="row exam-summery justify-content-center" style="display: none;">
          <h3>Exam Summary</h3>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No of Questions</th>
                <th>Answered</th>
                <th>Not Answered</th>
                <th>Marked for Review</th>
                <th>Answered &amp; Marked for Review</th>
                <th>Not Visited</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ $exam->total_questions }}</td>
                <td class="countAnswered">0</td>
                <td class="countNotAnswered">0</td>
                <td class="countMarked">0</td>
                <td class="countAnsweredAndMarked">0</td>
                <td class="countNotVisited">0</td>
              </tr>
            </tbody>
          </table>
          <hr>
          <div class="col-md-12 text-center">
            <h4> Are you sure you want to submit for final marking?<br>No changes will be allowed after submission.</h4>
            <button type="button" class="btn btn-success btn-lg mx-auto m-2" id="btnYesSubmitConfirm">Yes, Submit Exam</button>
            <button type="button" class="btn btn-danger btn-lg mx-auto m-2" id="btnNoSubmitConfirm">No, Go Back</button>
          </div>
        </div>

      </form>
    </div>
  </section>
</div>
@endsection

@section('js')
<script>
  var timer = Number({{ $second }});
  var activeQuestion = {{ $maxQuestions }};
  const questions = @json($exam->questions);
  const form = $('#examForm');
  var testid = Number({{ $exam->testid }});
  var testname = "{{ $exam->name }}";
  var max_qno = Number({{ $maxQuestions }});
  max_qno = max_qno > 0 ? max_qno : 1;
  const answers = @json($answers);
  let isSubmitting = false;
  
  function startTimer() {
      const interval = setInterval(function () {
          if (timer > 0) {
              timer--;
              const hours = Math.floor(timer / 3600);
              const minutes = Math.floor((timer % 3600) / 60);
              const seconds = timer % 60;
              $('#timerDisplay').text(
                  `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
              );
          } else {
              clearInterval(interval);
              isSubmitting = true;
              form.submit();
          }
      }, 1000);
  }
  
  function setStatus(qno, status, ans) {
      document.getElementById('status-'+qno).value = status;
      var subject = $(`[name="subject[${qno}]`).val();
      $.ajax({
          url: "{{ route('exam.save') }}",
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          method: 'POST',
          data: {
              test_id: testid,
              testname: testname,
              q_no: qno,
              subject: subject,
              status: status,
              answer: ans
          },
          success: function (data) {
              //console.log(data);
          },
      });
  }
  
   function openQuestion(index) {
    $('.question').hide();
    $(`#question-${index}`).show();
    $('.pagination li').removeClass('active');
    $(`.pagination li[data-seq="${index}"]`).addClass('active');
    const a = $(`.pagination li[data-seq="${index}"] a`);
    if (!$(a).hasClass("que-save") && !$(a).hasClass("que-save-mark") && !$(a).hasClass("que-mark")) {
        $(a).addClass("not-answered").removeClass("not-attempted");
        $(`#status-${index}`).val('not-answered'); 
    }
    activeQuestion = index;
    updateCounts();
  }
  
  function updateCounts() {
      let notVisited = 0;
      let notAnswered = 0;
      let answered = 0;
      let marked = 0;
      let answeredAndMarked = 0;
  
      $('.pagination a').each(function () {
          if ($(this).hasClass('not-answered')) notAnswered++;
          if ($(this).hasClass('que-save')) answered++;
          if ($(this).hasClass('que-mark')) marked++;
          if ($(this).hasClass('que-save-mark')) answeredAndMarked++;
      });
  
      notVisited = questions.length - (notAnswered + answered + marked + answeredAndMarked);
  
      $('.countNotVisited').text(notVisited);
      $('.countNotAnswered').text(notAnswered);
      $('.countAnswered').text(answered);
      $('.countMarked').text(marked);
      $('.countAnsweredAndMarked').text(answeredAndMarked);
  }
  
  function clearLog(testid, qno) {
      $.ajax({
          url: "{{ route('exam.clearlog') }}",
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          method: 'POST',
          data: {
              test_id: testid,
              q_no: qno,
          },
          success: function (data) {
              console.log(data);
          },
      });
  }
  
  $('.btn-save').click(function () {
      const index = $(this).data('index');
      const radio = $(`#question-${index} input[type="radio"]`);
      if (!radio.is(':checked')) {
          alert('Please select an answer first.');
          return;
      }
      var answer = $(`#question-${index} input[type="radio"]:checked`).val();
      if(answer == 0){
        alert('Please select an answer first.');
        return;
      }
      setStatus(index, 'que-save',answer);
      $(`.pagination li[data-seq="${index}"] a`)
          .removeClass('not-answered que-mark que-save-mark')
          .addClass('que-save');
      NextQuestion(index);
  });
  
  $('.btn-save-mark-answer').click(function () {
      const index = $(this).data('index');
      const radio = $(`#question-${index} input[type="radio"]`);
      if (!radio.is(':checked')) {
          alert('Please select an answer first.');
          return;
      }
      var answer = $(`#question-${index} input[type="radio"]:checked`).val();
      if(answer == 0){
        alert('Please select an answer first.');
        return;
      }
      setStatus(index, 'que-save-mark',answer);
      $(`.pagination li[data-seq="${index}"] a`)
          .removeClass('not-answered que-mark que-save')
          .addClass('que-save-mark');
      NextQuestion(index);
  });
  
  $('.btn-mark').click(function () {
      const index = $(this).data('index');
      setStatus(index, 'que-mark', 0);
      $(`.pagination li[data-seq="${index}"] a`)
          .removeClass('not-answered que-save que-save-mark')
          .addClass('que-mark');
      NextQuestion(index);
  });
  
  $('.btn-reset').click(function () {
    const index = $(this).data('index');
    $(`#question-${index} input[type="radio"]`).prop('checked', false);
    $(`#status-${index}`).val('not-answered');
    $(`.pagination li[data-seq="${index}"] a`).removeClass('que-save que-mark que-save-mark').addClass('not-answered');
    updateCounts();
    setStatus(index, 'not-answered', 0);
    clearLog(testid, index);
  });
  
  $('.pagination a').click(function (e) {
      const index = $(this).data('index');
      openQuestion(index);
  });
  
  $('#btnPrevQue').click(function () {
      PreviousQuestion(activeQuestion);
  });
  
  $('#btnNextQue').click(function () {
      NextQuestion(activeQuestion);
  });
  
  function NextQuestion(index) {
      updateCounts();
      if (index < questions.length) {
          openQuestion(index + 1);
      }
      return;
  }
  
  function PreviousQuestion(index) {
      updateCounts();
      if (index > 1) {
          openQuestion(index - 1);
      }
      return;
  }
  
  $(".btn-submit-answer").click(function () {
      $('.exam-paper').hide();
      $('.exam-summery').show();
  });
  
   $('#btnYesSubmitConfirm').click(function () {
    $(this).prop('disabled', true);
    $(this).text('Submitting...');
    isSubmitting = true;
    form.submit();
  });
  
  $('#btnNoSubmitConfirm').click(function () {
      if (timer === 0) {
          isSubmitting = true; 
          form.submit();
      }
      $('.exam-paper').show();
      $('.exam-summery').hide();
  });
  
  window.addEventListener('beforeunload', function (e) {
      if (!isSubmitting) {
          var confirmationMessage = 'If you leave this page, your exam will be automatically submitted. Are you sure?';
          e.returnValue = confirmationMessage;
          return confirmationMessage;
      }
  });
  
  document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "hidden" && !isSubmitting) {
          isSubmitting = true;
          form.submit();
      }
  });

  $('.logout').on('click', function (e) {
      e.preventDefault();
      if(!isSubmitting){
        isSubmitting = true;
        form.submit();
      }
  });
  
  startTimer();
  openQuestion(max_qno);
  updateCounts();
  
  $(document).ready(function () {
      Object.keys(answers).forEach(qno => {
          const answer = answers[qno];
          const status = answer.status;
          const ans = answer.answer;
          if (status === 'que-save' || status === 'que-save-mark') {
              $(`#question-${qno} input[type="radio"][value="${ans}"]`).prop('checked', true);
          }
  
          $(`#status-${qno}`).val(status);
          
          $(`.pagination li[data-seq="${qno}"] a`).removeClass('not-attempted not-answered que-mark que-save que-save-mark')
          .addClass(status);
          
      });
      updateCounts();
  });
</script>
@endsection