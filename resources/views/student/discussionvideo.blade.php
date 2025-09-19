@extends('layouts.dashboard')

@section('title', 'Discussion Videos')

@section('css')
@endsection

@section("meta")
<meta http-equiv="refresh" content="1800">
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">

      <div class="col-lg-12">

        <div class="card card-primary">
          <form action="{{ route('student.discussionvideo') }}" method="GET">

            <div class="card-body">

              <div class="row">
                <div class="col-md-12 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Discussion Videos</h6>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                  <label for="subject">Subject</label>
                  <select name="subject" id="subject" class="form-control form-control-sm" required>
                    <option value="">Select Subject</option>
                    @foreach (['PHYSICS', 'CHEMISTRY', 'ZOOLOGY', 'BOTANY'] as $row)
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
          `
        </div>
      </div>
      @if($subject)

      <div class="col-lg-12">
        <div class="card card-info">
          <div class="card-body">
            <ul class="nav nav-pills" id="myTab3" role="tablist">
              @foreach(isset($discussionvideos) ? $discussionvideos->keys()->all() : [] as $part)
              @php
              $index = preg_replace('/[^0-9]/', '', $part);
              @endphp
              <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="part{{$index}}-tab" data-toggle="tab" href="#part{{$index}}" role="tab" aria-controls="part{{$index}}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                  part{{$index}}
                </a>
              </li>
              @endforeach
            </ul>
            <div class="tab-content" id="myTabContent2">
              @foreach(isset($discussionvideos) ? $discussionvideos->keys()->all() : [] as $part)
              @php
              $index = preg_replace('/[^0-9]/', '', $part);
              @endphp
              <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="part{{$index}}" role="tabpanel" aria-labelledby="part{{$index}}-tab">
                @forelse(isset($discussionvideos[$part]) ? $discussionvideos[$part] : collect() as $video)
                <?php
                $videoId = $video->video_id ?? null;
                ?>
                <iframe src="https://player.vimeo.com/video/{{ $videoId }}" frameborder="0" class="m-t-15" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; gyroscope; accelerometer;" style="height:500px;width:100%;" title="video_20240822_142621"></iframe>
                <script src="https://player.vimeo.com/api/player.js"></script>
                @empty
                <p>No videos for part part{{$index}}.</p>
                @endforelse
              </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

      </div>

    </div>

  </div>

</div>
</div>
@endsection

@section('js')
@endsection