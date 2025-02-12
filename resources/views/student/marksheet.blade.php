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
                        @if(!empty($tests))
                        @foreach($tests as $test)
                        <tr>
                            <td>{{ $test->exam_date }}</td>
                            <td>{{ $test->name }}</td>
                            <td><a href="{{ route('student.mark_download', $test->test_id) }}">{{ $test->mark  }} / {{ $test->total }}</a></td>
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
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
                
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 