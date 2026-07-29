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
  .toggle-markrange {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
      font-weight: 600;
      cursor: pointer;
      user-select: none;
  }
  .toggle-markrange input[type="checkbox"] {
      width: 18px;
      height: 18px;
      cursor: pointer;
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
              <h4>Online Examinations Publish</h4>
            </div>
            <div class="card-body">
              <form method="get" id="filterForm" action="{{ route('exam.onlinepublish') }}" enctype="multipart/form-data">
                <div class="row">
                  <div class="form-group col-lg-2">
                    <label>Start Date</label>
                    <input type="date" value="{{ request('start_date', date('Y-m-01')) }}" name="start_date" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>End Date</label>
                    <input type="date" value="{{ request('end_date', date('Y-m-d')) }}" name="end_date" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </div>
                </div>
              </form>

              <label class="toggle-markrange">
                <input type="checkbox" id="toggleMarkRange">
                Show Mark Range File Column?
              </label>

              <form method="post" id="publishForm" action="{{ route('exam.onlinepublish.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-lg-12">
                    <div class="table-responsive m-t-10">
                      <table>
                        <thead>
                          <tr>
                            <th>Test ID</th>
                            <th>Course</th>
                            <th>Test Name</th>
                            <th>Test Category</th>
                            <th>Total Questions</th>
                            <th class="markrange-col" style="display:none;">Mark Range</th>
                            <th>Publish</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($exams as $exam)
                          <tr>
                            <td>{{ $exam->testid }}</td>
                            <td>{{ $exam->course }}</td>
                            <td>{{ $exam->name }}</td>
                            <td>{{ $exam->testcategory }}</td>
                            <td>{{ $exam->total_questions }}</td>

                            <td class="markrange-col" style="display:none;">
                              @if(isset($exam->markrange_file['online']))
                              <a href="{{ env('APP_URL').$exam->markrange_file['online'] }}" download>{{ basename($exam->markrange_file['online']) }}</a><br>
                              <a class="btn btn-danger text-white" href="{{ route('exam.onlinepublish',['delete'=>$exam->name,'batch'=>'online'])}}"><i class="fas fa-trash"></i></a>
                              @else
                              <input type="file" name="batch[{{ $exam->name }}][online]" accept="application/pdf" class="form-control form-control-sm">
                              @endif
                            </td>

                            <td>
                              <select name="publish[{{ $exam->name }}]" class="form-control form-control-sm" required>
                                <option value="">Select Option</option>
                                <option value="Yes" @selected($exam->publish == 'Yes')>Yes</option>
                                <option value="No" @selected($exam->publish == 'No')>No</option>
                              </select>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
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
  $(document).ready(function(){

    // Toggle Mark Range column visibility
    $('#toggleMarkRange').on('change', function(){
      if($(this).is(':checked')){
        $('.markrange-col').show();
      } else {
        $('.markrange-col').hide();
      }
    });

    // File validation
    $(document).on('change', 'input[type="file"]', function(){
      const file = this.files[0];
      if (file && (file.type !== "application/pdf" || file.size >= 2 * 1024 * 1024)) {
        alert("Only PDF files with size less than 2MB are allowed.");
        this.value = "";
      }
    });

  });
</script>
@endsection
