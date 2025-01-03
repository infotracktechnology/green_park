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
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/oBkgL2iQygA?si=zj6MWHAiX8oOkSEq" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                  </div>
                  {{-- <div class="card-footer text-right">
                    @foreach($chairmanvideos as $video)
                        @if($video->attachment)
                            <a href="/{{ $video->attachment }}" target="_blank" rel="noopener noreferrer"><i class="fas fa-paperclip"></i> Attachment</a>
                        @endif
                    @endforeach
                  </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 