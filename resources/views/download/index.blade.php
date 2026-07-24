@extends('layouts.app')
@section('title', 'Download')
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
                  <h6 class="col-deep-purple">Download</h6>
                </div>
                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{route('download.create')}}" class="btn btn-primary btn-block">Add Download</a>
                </div>
              </div>

               <form action="{{ route('download.index') }}" method="get">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <select name="coaching_type" class="select2" required>
                      <option value="">Select Coaching Type</option>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}" @selected(request('coaching_type')==$row)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </form>

              <div class="col-12">
                <form action="{{ route('download.destroy','bulk') }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger m-b-10">Delete Selected</button>

                  <div class="table-responsive">
                    <table class="table table-striped table-sm" id="myTable">
                      <thead>
                        <tr role="row">
                          <th>#</th>
                          <th>User Type</th>
                          <th>Course</th>
                          <th>Branch </th>
                          <th>Coaching Type</th>
                          <th>Title</th>
                          <th>Attachment</th>
                          <th>Edit</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($downloads as $key => $download)
                        <tr>
                          <td><input type="checkbox" class='ids' name="ids[]" value="{{$download->id}}" /></td>
                          <td>{{ $download->usertype }}</td>
                          <td>{{ $download->course }}</td>
                          <td>{{ $download->branchNames() }}</td>
                          <td>{{ $download->coaching_type }}</td>
                          <td>{{ $download->title }}</td>
                          <td>
                            @if($download->file_path)
                            @foreach($download->file_path as $file)
                            <a href="{{ env('APP_URL').'/'.$file }}" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i></a>
                            @endforeach
                            @endif
                          </td>
                          <td>
                            <a href="{{ route('download.edit', $download->id) }}" class="btn btn-primary">
                              <i class="fas fa-edit"></i>
                            </a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </form>
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