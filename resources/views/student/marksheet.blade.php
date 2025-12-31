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
                  <table class="table">
                    <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Exam Date</th>
                        <th>Subject</th>
                        <th>Student Mark</th>
                        <th>Overall First Mark</th>
                        <th>Marks Range</th>
                        <th>Download Choosen Answer</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($exams as $key => $test)
                      <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $test['exam_date'] }}</td>
                        <td>{{ $test['name'] }}</td>
                        <td><a href="{{ route('student.mark_subject', $test['test_id']) }}">{{ $test['mark']  }} / {{ $test['total'] }}</a></td>
                        <td> {{ $test['first_mark'] }} </td>
                        <td>
                          {{-- @if($test['markrange'])
                          <a href="{{ env('APP_URL').$test['markrange'] }}" class="btn btn-primary">Markrange</a>
                          @endif --}}
                        </td>
                        <td><a href="{{ route('student.mark_download', $test['test_id']) }}" class="btn btn-primary">Download</a></td>
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
                        @foreach ($category as $row)
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


              @if($subjectexam->isNotEmpty())
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table">
                    <?php
                    $first = $subjectexam->first();
                     $subjects = [
                      'PHYSICS'   => ['tot' => 'ptot', 'r' => 'pr', 'w' => 'pw', 'l' => 'pl'],
                      'CHEMISTRY' => ['tot' => 'ctot', 'r' => 'cr', 'w' => 'cw', 'l' => 'cl'],
                      'BOTANY'    => ['tot' => 'btot', 'r' => 'br', 'w' => 'bw', 'l' => 'bl'],
                      'ZOOLOGY'   => ['tot' => 'ztot', 'r' => 'zr', 'w' => 'zw', 'l' => 'zl'],
                     ];
                    ?>
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>EXAM DATE</th>
                        <th>EXAM NAME</th>
                        @if($first->tot)
                        <th>{{ \Str::between($first->category, '(',')') }}</th>
                        <th>TOTAL</th>
                        @else
                        @foreach($subjects as $name => $cols)
                        @if($first->{$cols['tot']})
                        <th>{{ $name }}</th>
                        @endif
                        @endforeach
                        <th>TOTAL</th>
                        @endif
                      </tr>
                    </thead>

                    <tbody>
                      @foreach($subjectexam as $key => $row)
                      <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->exdate }}</td>
                        <td>{{ $row->subject }}</td>
                        @if($row->tot)
                        <td>{{ $row->tot }} / {{ ($row->r + $row->w + $row->l) * 4 }}</td>
                        <td>{{ $row->tot }} / {{ $row->totmark }}</td>
                        @else
                        @foreach($subjects as $cols)
                        @if($row->{$cols['tot']})
                        <td>
                          {{ $row->{$cols['tot']} }} / {{ ($row->{$cols['r']} + $row->{$cols['w']} + $row->{$cols['l']}) * 4 }}
                        </td>
                        @endif
                        @endforeach
                        <td>{{ $row->nettot }} / {{ $row->totmark }}</td>
                        @endif
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