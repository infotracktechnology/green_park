@extends('layouts.dashboard')

@section('title', 'Worksheet')

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4><i style="font-size: 30px;" class="fas fa-file-pdf"></i> Worksheet</h4>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-hover">

              <thead>
                <tr>
                  <th>Title</th>
                  <th>Attachment</th>
                </tr>
              </thead>
              <tbody>
                @foreach($worksheets as $worksheet)
                <tr>
                  <td>{{ $worksheet->title }}</td>
                  <td>
                    @if($worksheet->file_path)
                    @foreach ($worksheet->file_path as $file)
                      <a href="{{ env('APP_URL').$file }}" class="btn btn-primary" download> <i class="fas fa-file-download"></i> Download</a>   
                    @endforeach
                    @endif
                  </td>
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
@endsection
