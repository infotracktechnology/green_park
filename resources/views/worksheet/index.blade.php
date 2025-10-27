@extends('layouts.app')
@section('title', 'Worksheet')
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
            {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
          </div>
          @endif


          <div class="card card-primary">

            <div class="card-body">

              <div class="row">
                <div class="col-md-10 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Worksheet</h6>
                </div>
                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{route('worksheet.create')}}" class="btn btn-primary btn-block">Add Worksheet </a>
                </div>
              </div>
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="myTable">
                    <thead>
                      <tr role="row">
                        <th>Academic Year</th>
                        <th>User Type</th>
                        <th>Course</th>
                        <th>Branch </th>
                        <th>Coaching Type</th>
                        <th>H/D</th>
                        <th>Batch</th>
                        <th>Attachment</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach( $worksheets as $key => $worksheet)
                      <tr>
                        <td>{{ $worksheet->academic_year }}</td>
                        <td>{{ $worksheet->usertype }}</td>
                        <td>{{ $worksheet->course }}</td>
                        <td>{{ $worksheet->branchNames() }}</td>
                        <td>{{ $worksheet->coaching_type }}</td>
                        <td>{{ $worksheet->category }}</td>
                        <td>{{ $worksheet->batch }}</td>
                        <td>
                          @if($worksheet->file_path)
                          <a href="{{ env('APP_URL').'/'.$worksheet->file_path }}" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i></a>
                          @endif
                        </td>
                        <td>
                          <a href="{{ route('worksheet.edit', $worksheet->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                          </a>
                        </td>
                        <td>
                          <form action="{{ route('worksheet.destroy', $worksheet->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this video?');">
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