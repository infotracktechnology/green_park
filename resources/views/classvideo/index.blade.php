@extends('layouts.app')
@section('title', 'Class Videos')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10 col-sm-12 mb-3">
                                    <h6 class="col-deep-purple">Class Videos</h6>
                                </div>
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <a href="{{ route('classvideo.create') }}" class="btn btn-primary btn-block">Add Class Video</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Subject</th>
                                            <th>Chapter</th>
                                            <th>Period</th>
                                            <th>Video Id</th>
                                            <th>Video URL</th>
                                            <th>Start At</th>
                                            <th>End At</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($classvideos as $classvideo)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $classvideo->subject }}</td>
                                            <td>{{ $classvideo->chapter }}</td>
                                            <td>{{ $classvideo->period }}</td>
                                            <td>{{ $classvideo->video_id }}</td>
                                            <td><a href="{{ $classvideo->video_url }}" target="_blank">{{ $classvideo->video_url }}</a></td>

                                            <td>{{ $classvideo->start_at }}</td>
                                            <td>{{ $classvideo->end_at }}</td>
                                            <td>
                                                <a href="{{ route('classvideo.edit', $classvideo->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <form action="{{ route('classvideo.destroy', $classvideo->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this video?');">
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
