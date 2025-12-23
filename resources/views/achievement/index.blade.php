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
                <form action="{{ route('achievement.destroy','bulk') }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')">
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
                        <th>Batch</th>
                        <th>File Type</th>
                        <th>Edit</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach($achievements as $achievement)
                      <tr>
                        <td><input type="checkbox" class='ids' name="ids[]" value="{{$achievement->id}}" /></td>
                        <td>{{ $achievement->usertype }}</td>
                        <td>{{ $achievement->course }}</td>
                        <td>{{ $achievement->branchNames() }}</td>
                        <td>{{ $achievement->coaching_type }}</td>
                        <td>{{ $achievement->category }}</td>
                        <td>{{ $achievement->batch }}</td>
                        <td>{{ $achievement->filecategory }}</td>
                        <td><a href="{{ route('achievement.edit', $achievement->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a></td>
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