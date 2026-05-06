@extends('layouts.dashboard')

@section('title', 'NEET Achievements')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
    .achievement-card {
        transition: transform 0.2s;
        border: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .achievement-card:hover {
        transform: translateY(-5px);
    }
    .achievement-media {
        max-height: 200px;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .achievement-media img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    .achievement-media i {
        font-size: 50px;
        color: #2b66a2;
    }
    .achievement-content {
        padding: 15px;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-trophy mr-2 text-warning"></i> NEET Achievements</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @forelse($achievements as $achievement)
                                <div class="col-md-4 col-sm-6 mb-4">
                                    <div class="card achievement-card h-100">
                                        <div class="achievement-media">
                                            @php
                                                $categories = is_array($achievement->filecategory) ? $achievement->filecategory : explode(',', $achievement->filecategory);
                                            @endphp
                                            
                                            @if(in_array('Image', $categories) && !empty($achievement->images))
                                                <img src="{{ asset(is_array($achievement->images) ? $achievement->images[0] : json_decode($achievement->images)[0]) }}" alt="Achievement Image">
                                            @elseif(in_array('Video', $categories) && $achievement->video)
                                                <i class="fas fa-play-circle"></i>
                                            @elseif(in_array('pdf', $categories) && $achievement->pdf)
                                                <i class="fas fa-file-pdf"></i>
                                            @elseif(in_array('Link', $categories) && $achievement->link)
                                                <i class="fas fa-link"></i>
                                            @else
                                                <i class="fas fa-award"></i>
                                            @endif
                                        </div>
                                        <div class="achievement-content">
                                            <div class="mb-2">
                                                {!! $achievement->content !!}
                                            </div>
                                            <div class="mt-3">
                                                @if(in_array('Video', $categories) && $achievement->video)
                                                    <a href="{{ asset($achievement->video) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2 mr-1">
                                                        <i class="fas fa-video"></i> Watch Video
                                                    </a>
                                                @endif
                                                @if(in_array('pdf', $categories) && $achievement->pdf)
                                                    <a href="{{ asset($achievement->pdf) }}" target="_blank" class="btn btn-sm btn-outline-danger mb-2 mr-1">
                                                        <i class="fas fa-file-pdf"></i> View PDF
                                                    </a>
                                                @endif
                                                @if(in_array('Link', $categories) && $achievement->link)
                                                    <a href="{{ $achievement->link }}" target="_blank" class="btn btn-sm btn-outline-info mb-2 mr-1">
                                                        <i class="fas fa-external-link-alt"></i> Open Link
                                                    </a>
                                                @endif
                                                @if(in_array('Image', $categories) && !empty($achievement->images))
                                                    @php
                                                        $imgs = $achievement->images;
                                                    @endphp
                                                    @foreach($imgs as $img)
                                                        <a href="{{ asset($img) }}" target="_blank" class="btn btn-sm btn-outline-success mb-2 mr-1">
                                                            <i class="fas fa-image"></i> View Image {{ $loop->iteration }}
                                                        </a>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="text-muted small mt-2">
                                                <i class="far fa-calendar-alt"></i> {{ $achievement->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-award fa-4x text-muted mb-3"></i>
                                    <h5>No achievements found yet.</h5>
                                    <p class="text-muted">Stay tuned for updates!</p>
                                </div>
                                @endforelse
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
<script>
    // Any specific JS for achievements if needed
</script>
@endsection
