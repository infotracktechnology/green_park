@extends('layouts.app')
@section('title', 'Chairman Video')

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
                <div class="col-md-9 col-sm-8 mb-2">
                  <h6 class="col-deep-purple">Chairman Video</h6>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                  <a href="{{route('chairmanvideo.create')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add</a>
                </div>
              </div>
              <div class="col-12">
                <form action="{{ route('chairmanvideo.destroy','bulk') }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')">
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
                        <th>H/D</th>
                        <th>Section</th>
                        <th>Batch</th>
                        <th>Title</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>

                    </thead>

                    <tbody>
                      @foreach ($chairmanvideos as $chairmanvideo)
                      <tr>
                        <td><input type="checkbox" name="ids[]" value="{{$chairmanvideo->id}}"></td>
                        <td>{{ $chairmanvideo->usertype }}</td>
                        <td>{{ $chairmanvideo->course }}</td>
                        <td>{{ $chairmanvideo->branchNames() }}</td>
                        <td>{{ $chairmanvideo->coaching_type}}</td>
                        <td>{{ $chairmanvideo->category}}</td>
                        <td>{{ $chairmanvideo->section}}</td>
                        <td>{{ $chairmanvideo->batch}}</td>
                        <td>{{ $chairmanvideo->title}}</td>

                        <td>
                          <a href="{{ route('chairmanvideo.edit', $chairmanvideo->id) }}" class="btn btn-warning text-white">
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
  });
</script>
@endsection