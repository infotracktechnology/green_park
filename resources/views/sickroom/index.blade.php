@extends('layouts.app')
@section('title', 'sickroom')
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
              @if(session()->has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
              </div>
           @endif
            
                <div class="card card-primary">
                  <div class="card-body">
                    <div class="row">
                    <div class="col-md-10 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Sick Room Entry</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('sickroom.create')}}" class="btn btn-primary btn-block">Add Entry</a>
                    </div>
                    </div>
                    <div class="col-12">
                        <div class="col-12">
                            <div class="table-responsive">
              <table class="table table-striped table-sm" id="myTable">
          
              <thead>
            <tr>
                <th>#</th>
                <th>Class</th>
                <th>Section</th>
                <th>Student ID</th> 
                <th>Room No</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Reason</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $entry->student_class }}</td>
                    <td>{{ $entry->section }}</td>
                    <td>{{ $entry->room_no }}</td>
                    <td>{{ $entry->student_id }}</td>
                    <td>{{ $entry->in_time }}</td>
                    <td>{{ $entry->out_time }}</td>
                    <td>{{ $entry->reason }}</td>
                    <td>
                        <a href="{{ route('sickroom.edit', $entry->id) }}" class="btn btn-warning text-white">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    
                    <td>
                        <form action="{{ route('sickroom.destroy', $entry->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this branch?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
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