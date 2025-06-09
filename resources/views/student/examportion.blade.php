@extends('layouts.dashboard')
@section('title', 'Exam Portion')
@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-12">
                <div class="card">
                  <div class="card-header">
                    <h4><i style="font-size: 30px;" class="fas fa-file-pdf"></i> Exam Portion</h4>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered table-striped">
                      <tr>
                        <th>Title</th>
                        <th>Attachment</th> 
                      </tr>
                      @foreach ($examportions as $examportion)
                      <tr>
                        <td>{{ $examportion->title }}</td>
                        <td>
                          <a href="{{ env('APP_URL') }}public/{{ $examportion->attachment }}" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-paperclip"></i> Attachment
                        </a>
                        </td>
                      </tr>
                      @endforeach
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