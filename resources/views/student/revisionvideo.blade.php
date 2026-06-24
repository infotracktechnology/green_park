@extends('layouts.dashboard')

@section('title', 'Revision Video')

@section('css')
@endsection


@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">

      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <h4>Revision Videos</h4>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Chapter</th>
                    <th>Expiry Date & Time</th>
                    <th>Video</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($revisionvideos as $video)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $video->day }}</td>
                    <td>{{ $video->date }}</td>
                    <td>{{ $video->subject }}</td>
                    <td>{{ $video->chapter }}</td>
                    <td>{{ $video->expire_at->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('video', base64_encode($video->video_id)) }}" data-action="seen {{ $video->video_id }}" target="_blank">Watch</a></td>
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