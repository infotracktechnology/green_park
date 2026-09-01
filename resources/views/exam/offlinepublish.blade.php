@extends('layouts.app')
@section('title', 'Examinations Publish')
@section('css')
<style>
  table {
      width: 100%;
      overflow-x: auto !important;
      border-collapse: collapse;
  }
  th, td {
      border: 1px solid #000;
      padding: 5px;
      color: #000 !important;
  }
  th {
      background-color: #eeece1;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          @if(session('success'))
          <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
          @endif

          <div class="card card-primary">
            <div class="card-header">
              <h4>Offline Examinations Publish</h4>
            </div>
            <div class="card-body">
              <form method="get" id="myForm" action="{{ route('exam.offlinepublish') }}" enctype="multipart/form-data">
                <div class="row">
                  <div class="form-group col-lg-2">
                    <label>Start Date</label>
                    <input type="date" value="{{ request('start_date', date('Y-m-01')) }}" name="start_date" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>End Date</label>
                    <input type="date" value="{{ request('end_date', date('Y-m-d')) }}" name="end_date" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" >
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected(request('course') == $row)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </div>
                </div>
              </form>

              <form method="post" id="myForm" action="{{ route('exam.offlinepublish') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course" value="{{ request('course') }}">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="table-responsive m-t-10">
                      <table>
                        <thead>
                          <tr>
                            <th>Test ID</th>
                            <th>Test Name</th>
                            <th>Test Category</th>
                            <th>Total Questions</th>
                            @foreach($batch as $row)
                            <th>Mark Range(BATCH {{ $row }})</th>
                            @endforeach
                            <th>Publish</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($exams as $exam)
                          <tr>
                            <td>{{ $exam->testid }}</td>
                            <td>{{ $exam->name }}</td>
                            <td>{{ $exam->testcategory }}</td>
                            <td>{{ $exam->total_questions }}</td>
                            @foreach($batch as $row)
                            <td>
                              @if(isset($exam->markrange_file[$row]))
                              <a href="{{ env('APP_URL').$exam->markrange_file[$row] }}" download>{{ basename($exam->markrange_file[$row]) }}</a><br>
                              <a class="btn btn-danger text-white" href="{{ route('exam.offlinepublish',['delete'=>$exam->name,'batch'=>$row])}}"><i class="fas fa-trash"></i></a>
                              @else
                              <input type="file" name="batch[{{ $exam->name }}][{{ $row }}]" accept="application/pdf" class="form-control form-control-sm">
                              @endif
                            </td>
                            @endforeach

                            <td>
                              <select name="publish[{{ $exam->name }}]" class="form-control form-control-sm" required>
                                <option value="">Select Option</option>
                                <option value="Yes" @selected($exam->publish == 'Yes')>Yes</option>
                                <option value="No" @selected($exam->publish == 'No')>No</option>
                              </select>
                            </td>
                          </tr>
                          @endforeach
                      </table>
                    </div>

                    <div class="d-flex m-t-20 justify-content-center">
                      <button type="submit" class="btn btn-primary">Publish All</button>
                    </div>
                  </div>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
  </section>
</div>
@endsection
@section('js')
<script>
  $(document).on('change', `input[type="file"]`, function(){
    const file = this.files[0];
    if (file && (file.type !== "application/pdf" || file.size >= 2 * 1024 * 1024)) {
      alert("Only PDF files with size less than 2MB are allowed.");
      this.value = "";
    }
  });
</script>
@endsection