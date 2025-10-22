@extends('layouts.dashboard')
@section('title', 'Mark Details')
@section('css')
@endsection
@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">
      <div class="col-md-12 col-lg-12">

        <div class="card">
          <div class="card-header">
            <h4>Mark Details</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Exam Date</th>
                        <th>Subject</th>
                        <th>Student Mark</th>
                        <th>Overall First Mark</th>
                        <th>Marks Range</th>
                        <th>Download Choosen Answer</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($tests as $test)
                      <tr>
                        <td>{{ $test->exam_date }}</td>
                        <td>{{ $test->name }}</td>
                        <td><a href="{{ route('student.mark_subject', $test->test_id) }}">{{ $test->mark  }} / {{ $test->total }}</a></td>
                        <td>
                          <?php
                      $overall_mark = \DB::select("SELECT sum(mark)mark from exam_answer where test_id=$test->test_id group by student_id order by mark desc limit 1");
                      ?>
                          {{ $overall_mark[0]->mark }} / {{ $test->total }}
                        </td>
                        <td><a href="#" class="btn btn-primary">Range</a></td>
                        <td><a href="{{ route('student.mark_download', $test->test_id) }}" class="btn btn-primary">Download</a></td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-md-12 m-t-20">
                <form action="{{ route('student.marksheet') }}" enctype="multipart/form-data">
                  <div class="row">

                    <div class="form-group col-lg-12">
                      <h6 style="color: #dc3545;">Select Subjectwise Marksheet</h6>
                    </div>

                    <div class="form-group col-lg-4">
                      <select name="exam" class="form-control form-control-sm" required>
                        <option value="">Select Exam</option>
                        @foreach (['CUMULATIVE (CHEBOT)','CUMULATIVE (PHYZOO)','GRAND TEST','WEEKEND (BOTANY)','WEEKEND (CHEMISTRY)','WEEKEND (PHYSICS)','WEEKEND (ZOOLOGY)'] as $row)
                        <option value="{{ $row }}" @selected(request('exam')==$row)>{{ $row }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-lg-2">
                      <button type="submit" class="btn btn-primary">Show Marks</button>
                    </div>
                  </div>
                </form>
              </div>


              @if($subjectexam)
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>EXAM DATE</th>
                        <th>EXAM NAME</th>
                        @foreach($subjectexam->first()?->Header(request('exam')) as $subject)
                        <th>{{ $subject }}</th>
                        @endforeach
                        <th>TOTAL</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($subjectexam as $key=>$row)
                      <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $row->exdate }}</td>
                        <td>{{ $row->subject }}</td>
                        @foreach($row->getScoresForHeader(request('exam')) as $subject => $mark)
                        <td>{{ $mark[0] }} / {{ $mark[1] }}</td>
                        @endforeach
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection