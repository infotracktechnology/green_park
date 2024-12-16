@extends('layouts.app')
@section('title', 'Staff Details')
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
                    <h6 class="col-deep-purple">Staff Details</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('staff.create')}}" class="btn btn-primary btn-block">Add staff</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
        <th>Branch ID</th>
        <th>Full Name</th>
        <th>Designation</th>
        <th>Department</th>
        <th>Email</th>
        <th>Mobile No</th>
        <th>Branch</th>
        <th>City</th>
        <th>State</th>
          <th>Pin Code</th>
         <th>Edit </th>
          <th>Action</th>
        </tr>
  
        </thead>
  
        {{-- <tbody>
          @foreach ($staff as $staff)
          <tr>
            <td>{{$staff->id}}</td>
            <td>{{$staff->name}}</td>
           <td>{{$staff->city}}</td>
            <td>{{$staff->state}}</td>
            <td>{{$staff->pincode}}</td>
            <td>{{$staff->mob_no}}</td>
            <td>{{$staff->email}}</td>
            <td>{{$staff->manager_name}}</td>
            <td>
              <a href="{{route('staff.edit', $staff->id)}}" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a>
            </td>
        
            <td>
              <form action="{{route('staff.destroy', $staff->id)}}" method="post" onsubmit="return confirm('Are you sure you want to Delete This staff?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @endforeach --}}
          
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