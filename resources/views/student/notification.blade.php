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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }
    .notice-board-item:hover {
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .notice-board-item-title {
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
    }
    .notice-board-item-content {
        color: #333;
    }
    .card-header h4 {
        display: flex;
        align-items: center;
    }
    .card-header h4 i {
        margin-right: 10px;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .rotate-90 {
        transform: rotate(90deg);
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
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
                            <div class="notice-board-item border mb-3 rounded" x-data="{ open: false, logged: false }">
                                <div class="p-3 cursor-pointer d-flex justify-content-between align-items-center" 
                                     @click="open = !open; if(open && !logged) { logAnnouncementView('{{ addslashes($announcement->id) }}'); logged = true; }">
                                    <div class="notice-board-item-title mb-0 d-flex align-items-center">
                                        <i class="fas fa-chevron-right mr-3 transition-all" :class="open ? 'rotate-90' : ''"></i>
                                        <span class="col-deep-purple">{{ $announcement->title }}</span>
                                    </div>
                                    <div class="badge badge-info">
                                        {{ $announcement->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                
                                <div x-show="open" x-cloak x-transition class="p-3 border-top bg-white rounded-bottom">
                                    <div class="notice-board-item-content">
                                        <i class="fas fa-align-left text-primary"></i> <strong>Content :</strong> 
                                        <div class="mt-2 ml-4">{!! $announcement->content !!}</div>
                                    </div>
                                    @if($announcement->attachment)
                                        <div class="notice-board-item-date mt-3 ml-4">
                                            <a href="{{ env('APP_URL') }}/{{ $announcement->attachment }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-paperclip"></i> View Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="notice-board-item border p-4 mb-3 rounded text-center">
                                <div class="text-muted">
                                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                    <p>No announcements found</p>
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

@section('js')
<script>
    function logAnnouncementView(id) {
        $.post('{{ route("student.logActivity") }}', {
            _token: '{{ csrf_token() }}',
            module: 'Announcement',
            action: 'Seen Announcement -' + id,
            student_id : '{{ auth()->user()->student_id }}'
        });
    }
</script>
@endsection
