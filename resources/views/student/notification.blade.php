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
                <!-- Announcement -->
                <div class="card">
                  <div class="card-header">
                    <h4>Announcements</h4>
                    <form class="card-header-form">
                    </form>
                  </div>
                  <div class="card-body">
                    <div class="notice-board">
                      @forelse(auth()->user()->announcement()->latest()->get() as $announcement)
                      <div class="notice-board-item border p-3 mb-3 rounded">
                        <div class="notice-board-id"> <strong>#</strong> {{ $announcement->id }}</div>
                        <div class="notice-board-item-date float-right"> 
                          <strong>Time:</strong> {{ $announcement->created_at }}
                        </div>
                        <div class="notice-board-item-title"><strong>Title :</strong> {{ $announcement->title }}</div>
                        <div class="notice-board-item-content"><strong>Content :</strong> {!! $announcement->content !!}</div>
                      </div>
                      @empty
                      <div class="notice-board-item border p-3 mb-3 rounded">
                        <div class="notice-board-item-date">No announcement found</div>
                      </div>
                      @endforelse
                    </div>
                  </div>
                </div>
                <!-- Notice board -->
              </div>
            </div>
        </div>
      </div>
@endsection