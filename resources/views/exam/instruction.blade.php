@extends('layouts.app')

@section('title', 'Examinations')
@section('css')

@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
            <div class="col-md-12">
                <h4 class="text-center">Please read the instructions carefully</h4>
                <h4><strong><u>General Instructions:</u></strong></h4>
                <ol>
                    <li>Total duration of NEET  is 180 min.</li>
                    
                    <li>The clock will be set at the server. The countdown timer in the top right corner of screen will display the remaining time available for you to complete the examination. When the timer reaches zero, the examination will end by itself. You will not be required to end or submit your examination.</li>
                    <li>
                        The Questions Palette displayed on the right side of screen will show the status of each question using one of the following symbols:
                        <ol>
                            <li><img src="{{asset('img/icon/logo1.png')}}"> You have not visited the question yet.<br><br></li>
                            <li><img src="{{asset('img/icon/logo2.png')}}"> You have not answered the question.<br><br></li>
                            <li><img src="{{asset('img/icon/logo3.png')}}"> You have answered the question.<br><br></li>
                            <li><img src="{{asset('img/icon/logo4.png')}}"> You have NOT answered the question, but have marked the question for review.<br><br></li>
                            <li><img src="{{asset('img/icon/logo5.png')}}"> The question(s) "Answered and Marked for Review" will be considered for evalution.<br><br></li>
                        </ol>
                    </li>
                    <li>You can click on the "&gt;" arrow which apperes to the left of question palette to collapse the question palette thereby maximizing the question window. To view the question palette again, you can click on "&lt;" which appears on the right side of question window.</li>
                    <li>You can click on your "Profile" image on top right corner of your screen to change the language during the exam for entire question paper. On clicking of Profile image you will get a drop-down to change the question content to the desired language.</li>
                    
                </ol>
                <h4><strong><u>Navigating to a Question:</u></strong></h4>
                <ol start="7">
                    <li>
                        To answer a question, do the following:
                        <ol type="a">
                            <li>Click on the question number in the Question Palette at the right of your screen to go to that numbered question directly. Note that using this option does NOT save your answer to the current question.</li>
                            <li>Click on <strong>Save &amp; Next</strong> to save your answer for the current question and then go to the next question.</li>
                            <li>Click on <strong>Mark for Review &amp; Next</strong> to save your answer for the current question, mark it for review, and then go to the next question.</li>
                        </ol>
                    </li>
                </ol>
                <h4><strong><u>Answering a Question:</u></strong></h4>
                <ol start="8">
                    <li>
                        Procedure for answering a multiple choice type question:
                        <ol type="a">
                            <li>To select you answer, click on the button of one of the options.</li>
                            <li>To deselect your chosen answer, click on the button of the chosen option again or click on the <strong>Clear Response</strong> button</li>
                            <li>To change your chosen answer, click on the button of another option</li>
                            <li>To save your answer, you MUST click on the Save &amp; Next button.</li>
                            <li>To mark the question for review, click on the Mark for Review &amp; Next button.</li>
                        </ol>
                    </li>
                    <li>To change your answer to a question that has already been answered, first select that question for answering and then follow the procedure for answering that type of question.</li>
                </ol>
                <h4><strong><u>Navigating through sections:</u></strong></h4>
                <ol start="10">
                    <li>Sections in this question paper are displayed on the top bar of the screen. Questions in a section can be viewed by click on the section name. The section you are currently viewing is highlighted.</li>
                    <li>After click the Save &amp; Next button on the last question for a section, you will automatically be taken to the first question of the next section.</li>
                    <li>You can shuffle between sections and questions anything during the examination as per your convenience only during the time stipulated.</li>
                    <li>Candidate can view the corresponding section summery as part of the legend that appears in every section above the question palette.</li>
                </ol>
                <hr>
                <span class="text-danger">Please note all questions will appear in your default language. This language can be changed for a particular question later on.</span>
                <hr>
                <form action="{{ route('exam.show', $test_id) }}" method="GET" enctype="multipart/form-data">
                <label>
                    <input type="checkbox" name="accept" value="{{ $test_id }}" required>&nbsp;&nbsp;I have read and understood the instructions. All computer hardware allotted to me are in proper working condition. I declare  that I am not in possession of / not wearing / not  carrying any prohibited gadget like mobile phone, Bluetooth  devices  etc. /any prohibited material with me into the Examination Hall. I agree that in case of not adhering to the instructions, I shall be liable to be debarred from this Test and/or to disciplinary action, which may include ban from future Tests / Examinations
                </label>
                <hr>
                <div class="text-center">
                    <button type="submit" class="btn btn-success">Proceed</button>
                </div>
                </form>
            </div>
          </div>
      </div>
   </section>
</div>
@endsection