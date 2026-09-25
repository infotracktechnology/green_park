@extends('layouts.dashboard')
@section('title', 'Contact Directory')
@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
 .main-content{
    font-family: 'Poppins', sans-serif;
 }
  .contact-hub-wrapper {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    border: 1px solid #f1f5f9;
  }

  .hub-header {
    background: linear-gradient(135deg, #003909 0%, #a2ea9879 100%);
    padding: 28px 32px;
    border-radius: 16px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
  }

  .badge-accent {
    background: rgba(255, 255, 255, 0);
    backdrop-filter: blur(5px);
    color: #ffffff;
    padding: 5px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .hub-title {
    color: #ffffff;
    font-weight: 800;
    font-size: 24px;
  }

  .hub-desc {
    color: #ffffff;
    font-size: 14px;
    max-width: 550px;
  }

  .header-icon-box {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #38bdf8;
  }

  /* Bento Tile Design */
  .contact-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    position: relative;
    overflow: hidden;
    height: 100%;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .contact-tile:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
    background: #ffffff;
    border-color: #cbd5e1;
  }

  .tile-accent-bar {
    height: 4px;
    width: 100%;
  }

  .tile-body {
    padding: 24px;
  }

  .tile-icon-avatar {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .tile-title {
    font-weight: 700;
    font-size: 17px;
    color: #0f172a;
  }

  .tile-subtitle {
    font-size: 13px;
    color: #64748b;
    display: block;
  }

  .numbers-wrapper {
    margin-top: 18px;
  }

  .numbers-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
    margin-bottom: 10px;
  }

  /* Phone Pill / Action Item */
  .phone-action-pill {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    border-radius: 12px;
    margin-bottom: 10px;
    text-decoration: none !important;
    transition: all 0.2s ease-in-out;
  }

  .phone-action-pill:hover {
    background: #f1f5f9;
    border-color:  rgb(82, 137, 82);
    transform: scale(1.01);
  }

  .phone-number {
    font-size: 14.5px;
    font-weight: 600;
    color: #1e293b;
    letter-spacing: 0.3px;
  }

  .pulse-dot-wrap {
    margin-right: 10px;
    display: flex;
    align-items: center;
  }

  .pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
  }

  .call-btn-chip {
    background: green;
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
    transition: all 0.2s;
  }

  .phone-action-pill:hover .call-btn-chip {
    background: rgb(82, 137, 82);
  }

  .empty-state-box {
    padding: 40px;
    border: 2px dashed #cbd5e1;
    border-radius: 16px;
    display: inline-block;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          
          <!-- Main Wrapper -->
          <div class="contact-hub-wrapper">

            <!-- Banner Header -->
            <div class="hub-header">
              <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="mb-2">
                  <span class="badge-accent"><i class="fas fa-headset"></i> Quick Support</span>
                  <h3 class="hub-title mt-2 mb-1">Help & Contact Center</h3>
                  <p class="hub-desc mb-0">Please use the numbers below to contact the required department or person immediately.</p>
                </div>
                {{-- <div class="header-icon-box d-none d-md-flex">
                  
                </div> --}}
              </div>
            </div>

            <!-- Contact Sections Grid -->
            <div class="row mt-4">
              @forelse($contacts as $contactIndex => $contact)
                @php
                  $colorSchemes = [
                    ['gradient' => 'linear-gradient(135deg, #4f46e5, #7c3aed)', 'light' => '#eef2ff', 'text' => '#4f46e5'],
                    ['gradient' => 'linear-gradient(135deg, #059669, #10b981)', 'light' => '#ecfdf5', 'text' => '#059669'],
                    ['gradient' => 'linear-gradient(135deg, #d97706, #f59e0b)', 'light' => '#fffbeb', 'text' => '#d97706'],
                    ['gradient' => 'linear-gradient(135deg, #e11d48, #f43f5e)', 'light' => '#fff1f2', 'text' => '#e11d48'],
                  ];
                  $theme = $colorSchemes[$contactIndex % count($colorSchemes)];
                @endphp

                <div class="col-12 col-md-6 col-xl-4 mb-4">
                  <div class="contact-tile">
                    <!-- Top Color Bar Accent -->
                    <div class="tile-accent-bar" style="background: {{ $theme['gradient'] }};"></div>

                    <div class="tile-body">
                      <!-- Tile Header -->
                      <div class="d-flex align-items-start mb-3">
                        <div class="tile-icon-avatar" style="background: {{ $theme['light'] }}; color: {{ $theme['text'] }};">
                          <i class="fas fa-building"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                          <h5 class="tile-title mb-1">{{ $contact['title'] ?? 'Help Desk' }}</h5>
                          @if(!empty($contact['subtitle']))
                            <span class="tile-subtitle">{{ $contact['subtitle'] }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="numbers-wrapper">
                        <span class="numbers-label">Available Lines</span>
                        
                        @forelse($contact['contacts'] ?? [] as $phone)
                          @if(!empty($phone['number']))
                            <a href="tel:{{ $phone['number'] }}" class="phone-action-pill">
                              <div class="d-flex align-items-center">
                                <div class="pulse-dot-wrap">
                                  <span class="pulse-dot"></span>
                                </div>
                                <span class="phone-number">{{ $phone['number'] }}</span>
                              </div>
                              <span class="call-btn-chip">
                                <i class="fas fa-phone"></i> 
                              </span>
                            </a>
                          @endif
                        @empty
                          <div class="text-center py-3 text-muted small">
                            <i class="fas fa-phone-slash mr-1"></i> No lines available
                          </div>
                        @endforelse
                      </div>
                    </div>
                  </div>
                </div>

              @empty
                <div class="col-12 text-center py-5">
                  <div class="empty-state-box">
                    <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                    
                    <p class="text-muted">Contact details will appear here once added.</p>
                  </div>
                </div>
              @endforelse
            </div>

          </div>

        </div>
      </div>
    </div>
  </section>
</div>
@endsection
