@extends('layouts.app')
@section('title', 'NEET Achievements')
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
                    <div class="col-md-9 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">NEET Achievements</h6>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                  <a href="{{route('achievement.create')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add </a>
                    </div>
                    </div>
                    <div class="col-12">
                      <div class="table-responsive">
                        <table class="table table-striped table-sm" id="myTable">
                            <thead>
                                <tr role="row">
                                    <th>Academic Year</th>
                                    <th>Branch</th>
                                    <th>Coaching Type</th>
                                    <th>Category</th>
                                    {{-- <th>Video Attachment</th> --}}
                                    {{-- <th>Image Attachment</th> --}}
                                    {{-- <th>Link</th> --}}
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                    
                            <tbody>
                                @foreach ($achievements as $achievement)
                                    <tr>
                                        <td>{{ $achievement->academic_year }}</td>
                                        <td>
                                            @php
                                                $branchNames = collect(explode(',', $achievement->branch))
                                                    ->map(fn($branchId) => optional(app\Models\Branch::find(trim($branchId)))->name)
                                                    ->filter()
                                                    ->implode(', ');
                                            @endphp
                                            {{ $branchNames }}
                                        </td>
                                        <td>{{ $achievement->coaching_type }}</td>
                                        <td>{{ $achievement->category }}</td>
                                       {{-- Video Attachment --}}
                                       {{-- <td>
                                        <a href="{{ env('APP_URL') . '/' . $achievement->video }}" class="btn btn-primary text-white" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td> --}}
                                    
{{-- Image Attachments --}}
{{-- <td>
  @foreach(json_decode($achievement->images, true) as $image)
      <a href="{{ env('APP_URL') . '/' . $image }}" class="btn btn-primary text-white mb-1" download>
          <i class="fas fa-download"></i>
      </a>
  @endforeach
</td> --}}

                                    {{-- <td>{{ $achievement->link }}</td> --}}
                                        <td>
                                            <a href="{{ route('achievement.edit', $achievement->id) }}" class="btn btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <form action="{{ route('achievement.destroy', $achievement->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')">
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