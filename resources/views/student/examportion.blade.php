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
            <div class="col-md-6 col-lg-12 col-xl-8">
                <div class="card">
                  <div class="card-header">
                    <h4><i class="fas fa-video"></i> Exam Portion</h4>
                  </div>
                  <div class="card-body">
                    <span class="font-weight-bold">Title : {{ $examportion->title }}</span>
                    @if($examportion->attachment)
                    <a href="/public/{{ $examportion->attachment }}" target="_blank" rel="noopener noreferrer">
                  <i class="fas fa-paperclip"></i> Attachment
                    </a>
                  @endif
                    </div>
                </div>
                                
                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 