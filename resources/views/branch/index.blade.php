@extends('layouts.app')
@section('title', 'Branch List')
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
                <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
            @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
  
                    <div class="row">
                    <div class="col-md-10 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Branch Details</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('branch.create')}}" class="btn btn-primary btn-block">Add Branch</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
        <th>Branch ID</th>
          <th>Name</th>
          <th>Address Line 1</th>
          <th>Address Line 2</th>
          <th>City</th>
          <th>State</th>
          <th>Pin Code</th>
          <th>Mobile No</th>
          <th>Email</th>
          <th>Manager Name</th>
          <th>Edit </th>
          <th>Action</th>
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($branches as $branch)
          <tr>
            <td>{{$branch->id}}</td>
            <td>{{$branch->name}}</td>
            <td>{{$branch->address_line_1}}</td>
            <td>{{$branch->address_line_2}}</td>
            <td>{{$branch->city}}</td>
            <td>{{$branch->state}}</td>
            <td>{{$branch->pincode}}</td>
            <td>{{$branch->mob_no}}</td>
            <td>{{$branch->email}}</td>
            <td>{{$branch->manager_name}}</td>
            <td>
              <a href="{{route('branch.edit', $branch->id)}}" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a>
            </td>
        
            <td>
              <form action="{{route('branch.destroy', $branch->id)}}" method="post" onsubmit="return confirm('Are you sure you want to Delete This branch?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
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