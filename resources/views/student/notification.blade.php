@extends('layouts.dashboard')

@section('title', 'Announcements')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .notice-board {
        margin-top: 20px;
    }
    .notice-board-item {
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }
    .notice-board-item:hover {
        transform: translateY(-5px);
    }
    .notice-board-id {
        font-size: 1.2rem;
        color: #007bff;
    }
    .notice-board-item-date {
        color: #6c757d;
    }
    .notice-board-item-title {
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    .notice-board-item-content {
        margin-top: 10px;
        color: #333;
    }
    .card-header h4 {
        display: flex;
        align-items: center;
    }
    .card-header h4 i {
        margin-right: 10px;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-12">
                <div class="card">
                  <div class="card-header">
                    <h4><i style="font-size: 30px;" class="fas fa-bullhorn"></i> Announcements</h4>
                  </div>
                  <div class="card-body">
                    <div class="notice-board">
                        @forelse($announcements as $announcement)
                            <div class="notice-board-item border p-3 mb-3 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="notice-board-id">
                                        <i class="fas fa-id-badge"></i> <strong>#</strong> {{ $announcement->id }}
                                    </div>
                                    <div class="badge badge-info">
                                        {{ $announcement->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="notice-board-item-title mt-2">
                                    <strong>Title :</strong><span class="col-deep-purple">{{ $announcement->title }}</span>
                                </div>
                                <div class="notice-board-item-content">
                                    <i class="fas fa-align-left"></i> <strong>Content :</strong> {!! $announcement->content !!}
                                </div>
                                <div class="notice-board-item-date mt-2">
                                    @if($announcement->attachment)
                                        <a href="{{ env('APP_URL') }}/{{ $announcement->attachment }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fas fa-paperclip"></i> Attachment
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="notice-board-item border p-3 mb-3 rounded text-center">
                                <div class="notice-board-item-date text-muted">
                                    <i class="fas fa-exclamation-circle"></i> No announcement found
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                </div>
              </div>
            </div>
        </div>
      </div>
@endsection
