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

      <div class="col-lg-12">
        
        <div class="card card-primary">
          <form action="{{ route('student.classvideo') }}" method="GET">
            <div class="card-header">
              <h4>Class Videos</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                  <label for="subject">Subject</label>
                  <select name="subject" id="subject" class="form-control form-control-sm" required>
                    <option value="">Select Subject</option>
                    @foreach (['PHYSICS', 'CHEMISTRY', 'BOTANY', 'ZOOLOGY'] as $row)
                    <option value="{{$row}}" @selected($row==$subject)>{{$row}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 mb-3">
                  <button type="submit" class="btn btn-primary m-t-25">Show</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      @if($subject)
      {{-- <div class="col-lg-12">
        <div class="card card-info">
          <div class="card-body">
            <ul class="nav nav-pills" id="myTab3" role="tablist">
              @foreach(isset($classvideos) ? $classvideos->keys()->all() : [] as $period)
              <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="period{{ $period }}-tab" data-toggle="tab" href="#period{{ $period }}" role="tab" aria-controls="period{{ $period }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
      Period {{ $period }}
      </a>
      </li>
      @endforeach
      </ul>
      <div class="tab-content" id="myTabContent2">
        @foreach(isset($classvideos) ? $classvideos->keys()->all() : [] as $period)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="period{{ $period }}" role="tabpanel" aria-labelledby="period{{ $period }}-tab">

          @forelse(isset($classvideos[$period]) ? $classvideos[$period] : collect() as $video)
          $videoId = $video->video_id ?? null;
          <iframe src="https://player.vimeo.com/video/{{ $videoId }}" frameborder="0" class="m-t-15" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; gyroscope; accelerometer;" style="height:500px;width:100%;" title="video_20240822_142621"></iframe>
          <script src="https://player.vimeo.com/api/player.js"></script>
          @empty
          <p>No videos for Period {{ $period }}.</p>
          @endforelse
        </div>
        @endforeach
      </div>
    </div>
  </div> --}}
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
                <th>Period</th>
                <th>Day</th>
                <th>Date</th>
                <th>Chapter</th>
                <th>Expiry Date & Time</th>
                <th>Video</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($videos as $video)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $video->day }}</td>
                <td>{{ $video->date }}</td>
                <td>{{ $video->subject }}</td>
                <td>{{ $video->chapter }}</td>
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