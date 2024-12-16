@extends('layouts.app')
@section('title', 'Users List')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('main')
<div class="main-content">
   <section class="section">

    <div class="section-body"> 
        <div class="row">
            <div class="col-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
            @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
  
                    <div class="row">
                    <div class="col-10 mb-3">
                    <h6 class="col-deep-purple">Users Details</h6>
                    </div>
                    <div class="col-2 mb-3">
                      <a href="{{route('users.create')}}" class="btn btn-primary btn-block">Add User</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
  
          <th>#</th>
  
          <th>Name</th>
  
          <th>Email</th>
  
          <th>Role</th>
  
          <th>Status</th>

          <th>Edit</th>
  
          <th>Delete</th>
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($users as $key => $user)

          <tr>

            <td>{{$key+1}}</td>

            <td>{{$user->name}}</td>

            <td>{{$user->email}}</td>

            <td>{{$user->role()}}</td>

            <td> {!! $user->status_text() !!}</td>

            <td><a href="{{route('users.edit', $user->id)}}" class="btn btn-primary"><i class="fas fa-edit"></i></a></td>

            <td>
              <form action="{{route('users.destroy', $user->id)}}" onsubmit="return confirm('Are you sure?')" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
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
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
  const table = $('#myTable').DataTable({

    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],

  });

</script>
@endsection