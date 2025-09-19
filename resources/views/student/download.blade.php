@extends('layouts.dashboard')

@section('title', 'Download')

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
                       <h4><i style="font-size: 30px;" class="fas fa-file-pdf"></i> Download</h4>
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
                                    @foreach($downloads as  $download)
                                    <tr>
                                        <td>{{ $download->title }}</td>
                                        <td>
                                            @if($download->file_path)
                                            <a href="{{ env('APP_URL').$download->file_path }}" 
                                               class="btn btn-primary" download>
                                                <i class="fas fa-file-download"></i> Download
                                            </a>
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
