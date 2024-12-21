@extends('layouts.app')
@section('title', 'Hostel')
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
                    <h6 class="col-deep-purple">Hostel Details</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('hostel.create')}}" class="btn btn-primary btn-block">Add Hostel</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
          <th>Branch</th>
        <th>Hostel ID</th>
          <th>Name</th>
          <th>Type</th>
          <th>Warden Name</th>
          <th>Mobile No</th>
          <th>Address</th>
          <th>Edit</th>
          {{-- <th>Action</th> --}}
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($hostels as $hostel)
          <tr>
            <td >{{$hostel->branch->name}}</td>
            <td>{{$hostel->id}}</td>
            <td>{{$hostel->name}}</td>
            <td>{{$hostel->type}}</td>
            <td>{{$hostel->warden_name}}</td>
            <td>{{$hostel->phone_no}}</td>
            <td>{{$hostel->address}}</td>
            <td>
              <a href="{{ route('hostel.edit', $hostel->id) }}" class="btn btn-warning text-white">
                 <i class="fas fa-edit"></i>
              </a>
           </td>
           {{-- <td>
              <form action="{{ route('hostel.destroy', $hostel->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this hostel?')">
                 @csrf
                 @method('DELETE')
                 <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                 </button>
              </form>
           </td> --}}
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