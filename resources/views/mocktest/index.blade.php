@extends('layouts.app')
@section('title', 'Mock Test')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
          </div>
          @endif
          @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
          </div>
          @endif
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <div class="col-md-8 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Mock Tests</h6>
                </div>
               
                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{ route('mocktest.create') }}" class="btn btn-primary btn-block">Add Mock Test</a>
                </div>
              </div>

               <form action="{{ route('mocktest.index') }}" method="get">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <select name="coaching_type" class="select2" required>
                      <option value="">Select Coaching Type</option>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}" @selected(request('coaching_type')==$row)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </form>

             <form action="{{ route('mocktest.destroy','bulk') }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger m-b-10">Delete Selected</button>
              
              <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="checkAll" /></th>
                      <th>Branch</th>
                      <th>Coaching Type</th>
                      <th>Exam Name</th>
                      <th>Start At</th>
                      <th>End At</th>
                      <th>Edit</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($mocktests as $mocktest)
                    <tr>
                      <td><input type="checkbox" class='ids' name="ids[]" value="{{$mocktest->id}}" /></td>
                      <td>{{ $mocktest->branchNames() }}</td>
                      <td>{{ $mocktest->coaching_type }}</td>
                      <td>{{ $mocktest->exam_name }}</td>
                      <td>{{ $mocktest->start_at->format('d-m-Y h:i A') }}</td>
                      <td>{{ $mocktest->end_at->format('d-m-Y h:i A') }}</td>
                      <td>
                        <a href="{{ route('mocktest.edit', $mocktest->id) }}" class="btn btn-primary">
                          <i class="fas fa-edit"></i>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                
              </div>
             </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
  $(document).ready(function() {
      $('#myTable').DataTable({
        pageLength: 25,
        searching: false,
      });
  });
</script>
@endsection