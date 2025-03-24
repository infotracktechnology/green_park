@extends('layouts.app')
@section('title', 'Question Key')
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
                    <h6 class="col-deep-purple">Question Key </h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('questionkey.create')}}" class="btn btn-primary btn-block">Add Question Key</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Academic Year</th>
                <th>Title</th>
                <th>Branches</th>
                <th>Coaching Type</th>
                <th>Attachment</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questionKeys as $key)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                  <td>{{ $key->created_at }}</td>

                    <td>{{ $key->academic_year }}</td>
                    <td>{{ $key->title }}</td>
                    <td>
                        @php
                            $branchNames = \App\Models\Branch::whereIn('id', explode(',', $key->branch))->pluck('name')->toArray();
                        @endphp
                        {{ implode(', ', $branchNames) }}
                    </td>
                    
                    <td>{{ implode(', ', explode(',', $key->coaching_type)) }}</td>
                    
                    <td>
                      <a href="{{ env('APP_URL').$key->file_path }}" class="btn btn-primary text-white" download><i class="fas fa-download"></i></a>
                  </td>
                    
                    <td>
                        <a href="{{ route('questionkey.edit', $key->id) }}" class="btn btn-warning text-white">
                           <i class="fas fa-edit"></i>
                        </a>
                     </td>
                     <td>
                        <form action="{{route('questionkey.destroy', $key->id)}}" method="post" onsubmit="return confirm('Are you sure you want to delete this branch?')">
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