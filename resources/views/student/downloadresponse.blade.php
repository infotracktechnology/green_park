@extends('layouts.dashboard')
@section('title', 'Download Response')
@section('css')
@endsection
@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">
      <div class="col-md-12 col-lg-12">

        <div class="card card-primary">
          <div class="card-header">
            <h4>Download Response (Online Exam)</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <form action="{{ route('student.downloadresponse') }}" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-lg-4">
                      <select name="category" class="select2" required>
                        <option value="">Select Category</option>
                        @foreach ($category as $row)
                        <option value="{{ $row }}" @selected(request('category')==$row)>{{ $row }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-lg-2">
                      <button type="submit" class="btn btn-primary">Show Result</button>
                    </div>
                  </div>
                </form>
              </div>


              @if($exams->isNotEmpty())
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>EXAM ID</th>
                        <th>EXAM DATE</th>
                        <th>EXAM NAME</th>
                        <th>NO OF QUESTIONS</th>
                        <th>DOWNLOAD</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach($exams as $key => $row)
                      <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->testid }}</td>
                        <td>{{ $row->exam_date }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->total_questions }}</td>
                        <td><a href="{{ route('student.mocktestpdf', $row->name) }}" class="btn btn-primary"> <i class="fas fa-download"></i> Download</a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              @else
              <div class="col-md-12">
                <div class="alert alert-danger">No Record Found</div>
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
