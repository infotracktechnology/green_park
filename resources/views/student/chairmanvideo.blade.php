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
                        <iframe class="embed-responsive-item" 
                                src="{{ $chairmanvideo->link }}" 
                                allowfullscreen 
                                title="YouTube video player" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                referrerpolicy="strict-origin-when-cross-origin">
                        </iframe>
                        @else
                        <p>No Video Found</p>
                        @endif
                    </div>
                </div>                
                  <div class="card-footer text-right">
                  
                    @if($chairmanvideo->attachment)
                    <a href="/{{ $chairmanvideo->attachment }}" target="_blank" rel="noopener noreferrer">
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