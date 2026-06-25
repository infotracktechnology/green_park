@extends('layouts.dashboard')

@section('title', 'Chairman Video')

@section('css')
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">
      @if($chairmanvideos->isEmpty())
        <div class="col-12 text-center mt-5">
          <div class="card p-5">
            <div class="card-body">
              <i style="font-size: 60px;" class="fas fa-video-slash text-muted mb-4"></i>
              <h4>No Videos Available</h4>
              <p class="text-muted">There are no chairman videos assigned to you at this time.</p>
            </div>
          </div>
        </div>
      @else
        @foreach($chairmanvideos as $date => $videos)
        <div class="col-lg-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4><i class="fas fa-calendar"></i> {{ $date }}</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Title</th>
                      <th>Start Date & Time</th>
                      <th>Expiry Date & Time</th>
                      <th>Attachment</th>
                      <th>Video</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($videos as $video)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $video->title }}</td>
                      <td>{{ $video->start_at ? \Carbon\Carbon::parse($video->start_at)->format('d/m/Y H:i') : 'Immediate' }}</td>
                      <td>{{ $video->end_at ? \Carbon\Carbon::parse($video->end_at)->format('d/m/Y H:i') : 'No Expiry' }}</td>
                      <td>
                        @if($video->attachment)
                          <a href="/public/{{ $video->attachment }}" target="_blank"><i class="fas fa-paperclip"></i> View</a>
                        @else
                          -
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('video', base64_encode($video->video_id)) }}" 
                           target="_blank" 
                           class="watch-link" 
                           data-action="seen Chairman Video - {{ $video->id }}">Watch</a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      @endif
    </div>
  </div>
</div>
@endsection

@section('js')
@endsection