@extends('layouts.dashboard')

@section('title', 'Class Videos')

@section('css')
@endsection

@section("meta")
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">
      @foreach($classvideos as $date => $videos)
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <h4> <i class="fas fa-calendar"></i> {{ $date }}</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Subject</th>
                    <th>Period</th>
                    <th>Chapter</th>
                    <th>Part</th>
                    <th>Expiry Date & Time</th>
                    <th>Video</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($videos as $video)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $video->subject }}</td>
                    <td>{{ $video->period }}</td>
                    <td>{{ $video->chapter }}</td>
                    <td>{{ $video->part }}</td>
                    <td>{{ $video->end_at->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('video', base64_encode($video->video_id)) }}" target="_blank">Watch</a></td>
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
</div>
@endsection

@section('js')
@endsection