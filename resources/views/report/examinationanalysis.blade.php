@extends('layouts.app')
@section('title', 'Examination Analysis')
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
                  <h6 class="col-deep-purple">Examination Analysis Reports</h6>
                </div>
             

              <div class="col-md-12">
                <form method="get" id="myForm" action="{{ route('report.section_exam') }}" enctype="multipart/form-data">
                  <div class="row">

                    <div class="form-group col-lg-4">
                      <label>Test Name</label>
                      <select name="test_name" id="test_name" class="select2" required>
                        <option value="">Select Test</option>
                        @foreach ($tests as $test)
                        <option value="{{ $test->name }}">
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