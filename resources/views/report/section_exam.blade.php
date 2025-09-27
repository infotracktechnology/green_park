@extends('layouts.app')
@section('title', 'Question Key')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">

    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
            
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif


          <div class="card card-primary">

            <div class="card-body">

              <div class="row">
                @if(session('error'))
                <div class="col-md-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                  </div>
                </div>
                @endif
                <div class="col-md-10 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Exam Section Wise Report</h6>
                </div>
              </div>

              <div class="col-md-12">
                <form method="get" id="myForm" action="{{ route('report.section_exam') }}" enctype="multipart/form-data">
                  <div class="row">

                    <div class="form-group col-lg-4">
                      <label>Test Name</label>
                      <select name="test_name" id="test_name" class="form-control form-control-sm" required>
                        <option value="">Select Test</option>
                        @foreach ($tests as $test)
                        <option value="{{ $test->name }}" @if($test->name == $test_name) selected @endif>
                          {{ $test->name }}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group col-lg-2">
                      <label>&nbsp;</label>
                      <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                  </div>
                </form>
              </div>

              @if($test_name)
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>OMR Valuation Report</th>
                        <th>Overall Report</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($sections as $row)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('report.section_exam',['section' => $row->section,'test_name' => $test_name,'type' => 'omr']) }}" target="_blank">{{ $row->section }}</a></td>
                        <td><a href="{{ route('report.section_exam',['section' => $row->section,'test_name' => $test_name,'type' => 'overall']) }}" target="_blank">{{ $row->section }}</a></td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>

                </div>
              </div>


              <form method="get" class="col-md-12" onsubmit="return confirm('Are you sure you want to publish?')" action="{{ route('report.section_exam') }}" enctype="multipart/form-data">
                <input type="hidden" name="test_name" value="{{ $test_name }}">
                <div class="col-lg-12">
                  <h6>Exam Publish</h6>
                </div>
                <div class="form-group col-lg-3">
                  <label>Result Publish</label>
                  <select name="publish" id="publish" class="form-control form-control-sm" required>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                  </select>
                </div>
                <div class="col-lg-2">
                  <label>&nbsp;</label>
                  <button class="btn btn-primary m-b-20" type="Submit">Publish</button>
                </div>
              </form>

              @endif

            </div>
          </div>
        </div>


      </div>
    </div>

  </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script>
  const table = $('#myTable').DataTable({
  
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  
  });
  
</script>

@endsection