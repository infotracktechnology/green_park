@extends('layouts.app') 
@section('title', 'Exam Portion')

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
            <div class="col-md-9 col-sm-12 mb-3">
            <h6 class="col-deep-purple">Exam Portion</h6>
            </div>
            <div class="col-md-2 col-sm-6 mb-3 ">
                <a href="{{route('examportion.create')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add</a>
                  </div>
                  </div>
                  <div class="col-12">
                  <div class="table-responsive">
    <table class="table table-striped table-sm" id="myTable">
        <thead>
  
            <tr role="row">
            <th>Branch </th>
            <th>Academic Year </th>
              <th>Coaching Type</th>
              <th>Title</th>
             <th>Attachment</th>
              <th>Action</th>
            </tr>
      
            </thead>
           <tbody>
                @foreach ($examportions as $examportion)
                <tr>
                  
                  <td>
                    @php
                        $branchNames = collect(explode(',', $examportion->branch_id))
                            ->map(fn($branchId) => optional(app\Models\Branch::find(trim($branchId)))->name)
                            ->filter()
                            ->implode(', ');
                    @endphp
                    {{ $branchNames }}
                </td>
                <td>{{$examportion->academic_year}}</td>
                  <td>{{$examportion->coaching_type}}</td>
                  <td>{{$examportion->title}}</td>
                 {{-- <td>{{$examportion->attachment}}</td> --}}
                 <td>
                  <a href="{{ env('APP_URL').$examportion->attachment }}" class="btn btn-primary text-white" download><i class="fas fa-download"></i></a>
              </td>
                </td> 
              <td>
                   <form action="{{ route('examportion.destroy', $examportion->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this PDF?')" class="d-inline">
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
