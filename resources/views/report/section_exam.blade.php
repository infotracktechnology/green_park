@extends('layouts.app')
@section('title', 'Section Wise Report')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
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
        background-color: #007bff !important;
        color: #fff !important;
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
                  <h6 class="col-deep-purple">Branch Wise Report</h6>
                </div>
             

              <div class="col-md-12">
                <form method="get" id="myForm" action="{{ route('report.section_exam') }}" enctype="multipart/form-data">
                  <div class="row">

                    <div class="form-group col-lg-4">
                      <label>Exam Category</label>
                      <select name="testcategory" id="testcategory" class="select2" required>
                        <option value="">Select Category</option>
                        @foreach ($category as $row)
                        <option value="{{ $row }}" @selected($row==request('testcategory'))>
                          {{ $row }}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group col-lg-4">
                      <label>Exam Name</label>
                      <select name="test_name" id="testname" class="select2" required>
                        <option value="">Select Test</option>
                        @foreach ($exams as $row)
                        <option value="{{ $row}}" @selected($row==request('test_name'))> {{ $row }}</option>
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
              
              <div class="col-md-8">
                <div class="table-responsive">
                  <table>
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>OMR Print</th>
                        <th>Checking Print</th>
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
<script>
$('#testcategory').change(() => window.location = `{{ route('report.section_exam') }}?testcategory=${$('#testcategory').val()}`);
</script>
@endsection