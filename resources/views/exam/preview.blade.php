@extends('layouts.app')

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
        padding: 0px 5px !important;
    }

    .pagination>li>a,
    .pagination>li>span {
        padding: 6px 12px;
        line-height: 2.8;
        text-decoration: none;
        border: none;
        background-size: 100% 100% !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-9">
                    <table>
                        <tbody>
                            <tr>
                                <td style="padding: 5px 15px; border: 2px solid #666"><i class="fa fa-user" style="font-size:90px;"></i></td>
                                <td>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td style="padding: 0px 5px;">Candidate Name</td>
                                                <td> : <span class="col-orange">Admin</span></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0px 5px;">Exam Name</td>
                                                <td> : <span class="col-orange">{{ $exam->name }}</span></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0px 5px;">Subject Name</td>
                                                <td> : <span class="col-orange"> {{ $exam->subject_name }}</span></td>
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
                <div class="col-md-3">
                    <table border="1" style="width: 100%;">
                        <tbody>
                            <tr style="padding:5px;">
                                <td>Physics</td>
                                <td><span class="col-orange">{{ $exam->physics_questions }}</span></td>
                            </tr>
                            <tr style="padding:5px;">
                                <td>Chemistry</td>
                                <td><span class="col-orange">{{ $exam->chemistry_questions }}</span></td>
                            </tr>
                            <tr style="padding:5px;">
                                <td>Biology</td>
                                <td><span class="col-orange">{{ $exam->biology_questions }}</span></td>
                            </tr>
                            <tr style="padding:5px;">
                                <td>Total</td>
                                <td><span class="col-orange">{{ $exam->total_questions }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-8">
                    <!-- Question Display -->
                    <div class="question-container">
                        @foreach ($exam->questions as $index => $question)
                        <div id="question-{{ $index }}" class="question" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                            <img src="{{ Storage::disk('public')->url($question['image']) }}" alt="Question Image" style="max-width: 100%;">
                            <p>Question {{ $index + 1 }}</p>
                            <div>
                                <input type="radio" name="question-{{ $index }}" value="A"> A
                                <input type="radio" name="question-{{ $index }}" value="B"> B
                                <input type="radio" name="question-{{ $index }}" value="C"> C
                                <input type="radio" name="question-{{ $index }}" value="D"> D
                            </div>
                            <button class="btn-save" data-index="{{ $index }}">Save</button>
                            <button class="btn-mark" data-index="{{ $index }}">Mark for Review</button>
                            <button class="btn-reset" data-index="{{ $index }}">Reset</button>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-responsive" style="border:dotted;">
                        <table class="table table-borderless mb-0 test-questions">
                            <thead>
                                <tr>
                                    <td class="full-width"> <a class="not-attempted">1</a></td>
                                    <td>Not Visited</td>
                                    <td class="full-width"> <a class="not-answered">1</a></td>
                                    <td>Not Answered</td>
                                </tr>
                                <tr>
                                    <td class="full-width"> <a class="que-save">1</a></td>
                                    <td>Answered</td>
                                    <td class="full-width"> <a class="que-mark">1</a></td>
                                    <td>Marked for Review</td>
                                </tr>
                                <tr>
                                    <td> <a class="que-save-mark">1</a></td>
                                    <td colspan="3">Answered &amp; Marked for Review (will be considered for evaluation)</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="panel" style="overflow-y: scroll;">
                        <ul class="pagination test-questions my-4">
                            @foreach ($exam->questions as $index => $question)
                            <li data-seq="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="{{ $index === 0 ? 'not-answered' : 'not-attempted' }}" data-index="{{ $index }}">{{ $index  < 9 ? '0' : '' }}{{ $index + 1 }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    var timer = 7200; // 2 hours in seconds
    var activeQuestion = 0;
    const questions = @json($exam->questions);

    // Timer
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
                submitAllAnswers();
            }
        }, 1000);
    }

    // Open Question
    function openQuestion(index) {
        $('.question').hide();
        $(`#question-${index}`).show();
        $('.pagination li').removeClass('active');
        $(`.pagination li[data-seq="${index}"]`).addClass('active');
        activeQuestion = index;
    }

    // Update Counts
    function updateCounts() {
        let notVisited = 0;
        let notAnswered = 0;
        let answered = 0;
        let marked = 0;
        let answeredAndMarked = 0;

        $('.pagination a').each(function () {
            const className = $(this).attr('class');
            if (className.includes('not-answered')) notAnswered++;
            if (className.includes('que-save')) answered++;
            if (className.includes('que-mark')) marked++;
            if (className.includes('que-save-mark')) answeredAndMarked++;
        });

        notVisited = questions.length - (notAnswered + answered + marked + answeredAndMarked);

        $('#countNotVisited').text(notVisited);
        $('#countNotAnswered').text(notAnswered);
        $('#countAnswered').text(answered);
        $('#countMarked').text(marked);
        $('#countAnsweredAndMarked').text(answeredAndMarked);
    }

    // Save Answer
    $('.btn-save').click(function () {
        const index = $(this).data('index');
        $(`.pagination li[data-seq="${index}"] a`)
            .removeClass('not-answered que-mark que-save-mark')
            .addClass('que-save');
        updateCounts();
    });

    // Mark for Review
    $('.btn-mark').click(function () {
        const index = $(this).data('index');
        $(`.pagination li[data-seq="${index}"] a`)
            .removeClass('not-answered que-save que-save-mark')
            .addClass('que-mark');
        updateCounts();
    });

    // Reset Answer
    $('.btn-reset').click(function () {
        const index = $(this).data('index');
        $(`#question-${index} input[type="radio"]`).prop('checked', false);
        $(`.pagination li[data-seq="${index}"] a`)
            .removeClass('que-save que-mark que-save-mark')
            .addClass('not-answered');
        updateCounts();
    });

    // Pagination Click
    $('.pagination a').click(function (e) {
        const index = $(this).data('index');
        if (!$(e.target).hasClass("que-save") &&
                !$(e.target).hasClass("que-save-mark") &&
                !$(e.target).hasClass("que-mark")) {
                $(e.target).addClass("not-answered").removeClass("not-attempted");
            }
        openQuestion(index);
    });

    // Submit All Answers
    function submitAllAnswers() {
        alert('Time is up! Submitting all answers.');
        // Add logic to submit answers
    }

    // Initialize
    startTimer();
    openQuestion(0);
    updateCounts();

</script>
@endsection