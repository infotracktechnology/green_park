@extends('layouts.dashboard')
@section('title', 'Chairman Video')
@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-8">
                <div class="card">
                  <div class="card-header">
                    <h4><i class="fas fa-video"></i> Chairman Video</h4>
                  </div>
                  <div class="card-body">
                    <div class="embed-responsive embed-responsive-16by9">
                        @if($chairmanvideo->link)
                            @php
                                preg_match('/vimeo\.com\/(\d+)/', $chairmanvideo->link, $matches);
                                $videoId = $matches[1] ?? null;
                            @endphp
                            @if($videoId)
                                <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                        frameborder="0" 
                                        allow="autoplay; fullscreen; picture-in-picture; clipboard-write; gyroscope; accelerometer" 
                                        style="position:absolute;top:0;left:0;width:100%;height:100%;" 
                                        title="video_20240822_142621"></iframe>
                                <script src="https://player.vimeo.com/api/player.js"></script>
                            @else
                                <p>Invalid Video Link</p>
                            @endif
                        @else
                            <p>No Video Found</p>
                        @endif
                    </div>
                </div>         
                  <div class="card-footer text-right">
                    @if($chairmanvideo->attachment)
                    <a href="/public/{{ $chairmanvideo->attachment }}" target="_blank" rel="noopener noreferrer">
                  <i class="fas fa-paperclip"></i> Attachment
                    </a>
                  @endif
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 