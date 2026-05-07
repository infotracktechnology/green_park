@extends('layouts.dashboard')

@section('title', 'Student Download')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
  <div class="section-body">
    <div class="row justify-content-center">
      <div class="col-lg-12">

        <div class="card answer-key-card">
          <div class="card-header">
            <h4><i style="font-size: 30px;" class="fas fa-file-pdf"></i> Downloadable Files</h4>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>File Name</th>
                  <th>Download</th>
                </tr>
              </thead>
              <tbody>
                @foreach($files as $file)
                <tr>
                  <td>{{ basename($file) }}</td>
                  <a href="{{ env('APP_URL')."uploads/Student Download/".$file }}" class="btn btn-primary btn-sm" download>Download</a>
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