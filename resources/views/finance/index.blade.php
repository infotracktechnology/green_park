@extends('layouts.app')
@section('title', 'Fees Plan')
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
           @if(session()->has('error'))
              <div class="alert alert-error alert-dismissible fade show" style="background-color:red ! important" role="alert">
                  {{ session('error') }}
              </div>
           @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
  
                    <div class="row">
                    <div class="col-md-10 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Fees Plan</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('feesplan.create')}}" class="btn btn-primary btn-block">Add Fees Plan</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
          <th>#</th>
          <th>Academic Year</th>
          <th>
            <select id="branchFilter" class="form-control form-control-sm">
              @if(!auth()->user()->branch)
              <option value="">All Branches</option>              
              @endif
                @foreach ($fees_plan->pluck('branch.name')->unique() as $branch_name)
                  <option value="{{ $branch_name }}">{{ $branch_name }}</option>
                @endforeach
            </select>
          </th>
          <th>Plan Name</th>
          <th>Coaching Type</th>
          <th>Batch</th>
          <th>Segment Name</th>
          <th>Status</th>
          <th>Edit</th>
          {{-- <th>Delete</th> --}}
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($fees_plan as $row)
          <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$row->academic_year}}</td>
            <td>{{optional($row->branch)->name}}</td>
            <td>{{$row->name}}</td>
            <td>{{$row->coaching_type}}</td>
            <td>{{$row->batch}}</td>
            <td>{{optional($row->segment)->name}}</td>
            <td>
              @if($row->is_active == 1)
              <span class="badge badge-success bg-success text-white">Active</span>
              @else
              <span class="badge badge-danger bg-danger text-white">Inactive</span>
              @endif
            </td>
            <td><a href="{{route('feesplan.edit', $row->id)}}" class="btn btn-primary"><i class="fas fa-edit"></i></a></td>
            {{-- <td> <form action="{{ route('feesplan.destroy', $row->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger">
                 <i class="fas fa-trash"></i>
              </button>
           </form></td> --}}
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
    "ordering": false,

  });
  $('#branchFilter').on('change', function(){
  table.column(2).search(this.value).draw(); // Assuming Branch is column index 2
});

  setTimeout(function() {
            $(".alert").fadeOut(1500);
        }, 3000);

</script>

@endsection