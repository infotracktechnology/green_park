@extends('layouts.dashboard')

@section('title', 'Answer Key')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
</style>
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row justify-content-center">
      <div class="col-lg-12">

        <div class="card answer-key-card">
          <div class="card-header">
            <h4><i style="font-size: 30px;" class="fas fa-file-pdf"></i> Answer Key</h4>
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
                @foreach($answerkeys as $answerkey)
                <tr>
                  <td>{{ $answerkey->title }}</td>
                  <td>
                    @if($answerkey->file_path)
                    @foreach ($answerkey->file_path as $file)
                    <a href="{{ env('APP_URL').$file }}" data-action="seen Answer Key - {{ $answerkey->id }}" class="btn btn-primary" download>
                      <i class="fas fa-file-download"></i> Download
                    </a>   
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