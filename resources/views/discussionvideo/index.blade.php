@extends('layouts.app')
@section('title', 'Discussion Videos')
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
                    <h6 class="col-deep-purple">Discussion Videos </h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('discussionvideo.create')}}" class="btn btn-primary btn-block">Add Discussion Video</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Branch</th>
                        <th>Coaching Type</th>
                        <th>Subject</th>
                        <th>Part</th>
                        <th>Video Id</th> 
                        <th> Start At</th>
                        <th> End At</th>  
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($discussionvideos as $discussionvideo)
                    <tr>
                        <td>{{ $discussionvideo->id }}</td>
                       <td>{{ $discussionvideo->academic_year }}</td>
                        <td>
                            @php
                                $branchNames = \App\Models\Branch::whereIn('id', explode(',', $discussionvideo->branch))->pluck('name')->toArray();
                            @endphp
                            {{ implode(', ', $branchNames) }}
                        </td>
                        <td>{{ implode(', ', explode(',', $discussionvideo->coaching_type)) }}</td>
                        <td>{{ $discussionvideo->subject }}</td>
                        <td>{{ $discussionvideo->part }}</td>
                        <td>{{ $discussionvideo->video_id }}</td>
                        <td>{{ $discussionvideo->start_at }}</td>
                         <td>{{ $discussionvideo->end_at }}</td>
                        <td>
                            <a href="{{route('discussionvideo.edit', $discussionvideo->id)}}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        </td>
                        <td>
                            <form action="{{ route('discussionvideo.destroy', $discussionvideo->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Discussion video?');">
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
    </section>
</div>
@endsection
@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
      $(document).ready(function() {
        $('#myTable').DataTable();
    });
    </script>
@endsection