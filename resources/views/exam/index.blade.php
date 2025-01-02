@extends('layouts.app') 
@section('title', 'Examinations') 

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" />
@endsection 


@section('main')
<div class="main-content">
 <section class="section">
  <div class="section-body">
   <div class="row">
    <div class="col-md-12 col-sm-12">
    
     @if(session()->has('success'))
     <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
     @endif

     <div class="card card-primary">
      <div class="card-body">
       <div class="row">
        <div class="col-md-8 col-sm-12 mb-3">
         <h6 class="col-deep-purple">Examinations</h6>
        </div>
        <div class="col-md-2 col-sm-12 mb-3">
            <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addShechedule">Add Shechedule</button>
           </div>
        <div class="col-md-2 col-sm-12 mb-3">
         <a href="{{route('exam.create')}}" class="btn btn-primary btn-block">Add Test</a>
        </div>
       </div>
       <div class="col-12">
        <div class="table-responsive">
         <table class="table table-striped table-sm" id="myTable">
          <thead>
           <tr role="row">
            <th>Test Code</th>
            <th>Branch</th>
            <th>Coaching Type</th>
            <th>Name</th>
            <th>Subject</th>
            <th>Total Questions</th>
            <th>Perview</th>
            <th>Action</th>
           </tr>
          </thead>

          <tbody>
           @foreach ($tests as $test)
           <tr>
            <td>{{ $test->id }}</td>
            <td>{{ strpos($test->branch_id, ',') ? 'All' : $test->branch->name }}</td>
            <td>{{ $test->coaching_type }}</td>
            <td>{{ $test->name }}</td>
            <td>{{ $test->subject_name }}</td>
            <td>{{ $test->total_questions }}</td>
            <td><a href="{{ route('exam.show', $test->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a></td>
            <td>
             <form action="{{ route('exam.destroy', $test->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete?')">
              @csrf 
              @method('DELETE')
              <button type="submit" class="btn btn-danger">
               <i class="fas fa-trash"></i>
              </button>
             </form>
            </td>
           </tr>
           @endforeach
          </tbody>

         </table>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>



 </section>
</div>

<div class="modal fade" id="addShechedule">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="formModal">Test Shechedule</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('exam.index') }}" method="get" enctype="multipart/form-data">
            <div class="row">
            <div class="form-group col-12">
              <label for="branch">Test</label>
              <select name="test_id" id="test_id" class="form-control form-control-sm" required>
                <option value="">Select Test</option>
                @foreach ($tests as $test)
                <option value="{{ $test->id }}">{{ $test->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-12">
                <label>Start Datetime</label>
                <input type="datetime-local" name="start_at" class="form-control form-control-sm" required>
              </div>

              <div class="form-group col-12">
                <label>End Datetime</label>
                <input type="datetime-local" name="end_at" class="form-control form-control-sm" required>
              </div>

              <div class="form-group col-12">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection 

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script>
 const table = $("#myTable").DataTable({
  lengthMenu: [
   [10, 25, 50, -1],
   [10, 25, 50, "All"],
  ],
 });
</script>

@endsection
