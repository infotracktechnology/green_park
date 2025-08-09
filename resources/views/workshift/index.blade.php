@extends('layouts.app') 
@section('title', 'Staff Shift')

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
              <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
          @endif
          <div class="card card-primary">
  
            <div class="card-body">

            <div class="row">
            <div class="col-md-9 col-sm-8 mb-2">
            <h6 class="col-deep-purple">Staff Shift</h6>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <a href="{{route('workshift.create')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add Shift</a>
                  </div>
                  </div>
                  <div class="col-12">
                  <div class="table-responsive">
    <table class="table table-striped table-sm" id="myTable">
        <thead>
  
            <tr role="row">
              <th>Branch </th>
              <th>Shift Name</th>
              <th>No Of Sessions</th>
              <th>No Of Staffs</th>
              <th>Edit </th>
              <th>Delete </th>
            </tr>
      
            </thead>
           <tbody>
                @foreach ($workshifts as $workshift)
                <tr>
                  <td>{{$workshift->branch->name}}</td>
                  <td>{{$workshift->shift_name}}</td>
                  <td>{{$workshift->no_session}}</td>
                  <td>{{$workshift->staff->count()}}</td>
                </td> 
                <td>
                    <a href="{{ route('workshift.edit', $workshift->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
              <td>
                   <form action="{{ route('workshift.destroy', $workshift->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')" class="d-inline">
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
