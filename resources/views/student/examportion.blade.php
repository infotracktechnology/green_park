@extends('layouts.dashboard')
@section('title', 'Exam Portions')
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
                      <thead>
                        <tr>
                          <th>Title</th>
                          <th>Attachment</th>
                        </tr>
                      </thead>
                      <tbody>
                      @foreach ($examportions as $examportion)
                      <tr>
                        <td>{{ $examportion->title }}</td>
                        <td>
                          @if($examportion->attachment)
                           @foreach($examportion->attachment as $file)
                                <a href="{{ url($file) }}" data-action="seen Exam Portions - {{ $examportion->id }}" target="_blank" class="btn btn-primary">
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
    </div>
</div>
@endsection 