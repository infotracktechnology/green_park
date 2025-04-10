@extends('layouts.dashboard')

@section('title', 'Revision Videos')

@section('css')
@endsection


@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">

                        <div class="col-lg-12">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="col-md-12 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Revision Videos</h6>
                                       </div>
                                    
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Chapter</th>
                                        <th>Video</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revisionvideos as $video)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $video->subject }}</td>
                                        <td>{{ $video->chapter }}</td>
                                        <td><a href="{{ route('video', base64_encode($video->video_id)) }}" target="_blank">Watch</a></td>
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
                
            </div>
@endsection

@section('js')
@endsection