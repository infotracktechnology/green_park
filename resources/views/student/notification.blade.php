@extends('layouts.dashboard')

@section('title', 'Announcements')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-12">
                <!-- Support tickets -->
                <div class="card">
                  <div class="card-header">
                    <h4>Announcements</h4>
                    <form class="card-header-form">
                    </form>
                  </div>
                  <div class="card-body">
                    @forelse(auth()->user()->announcement()->get() as $announcement)
                    <div class="support-ticket media pb-1 mb-3 d-flex">
                     
                      <div class="flex-1 ms-3">
                        
                        <span class="fw-bold">#{{ $announcement->id }} </span>
                        <span>{{ $announcement->title }}</span>
                        {!! $announcement->content !!}
                        
                      </div>
                    </div>
                    @empty
                    <div class="support-ticket media pb-1 mb-3 d-flex">
                      <div class="flex-1 ms-3">
                        <span class="fw-bold">No Announcements</span>
                      </div>
                    </div>
                    @endforelse
                    
                  </div>
                </div>
                <!-- Support tickets -->
              </div>
            </div>
        </div>
      </div>
@endsection