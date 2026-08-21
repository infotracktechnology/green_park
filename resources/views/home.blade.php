@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('css')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style>
  /* Fix Bootstrap collapse conflict with Tailwind CSS v4 */
  .collapse {
    visibility: visible !important;
  }
  .collapse:not(.show) {
    display: none !important;
  }

  /* Custom scrollbar to match clean UI */
  .scroll-area { 
    overflow-y: auto; 
    height: 350px; 
    padding-right: 4px; 
  }
  .scroll-area::-webkit-scrollbar { 
    width: 6px; 
  }
  .scroll-area::-webkit-scrollbar-thumb { 
    background-color: #cbd5e1; 
    border-radius: 9999px; 
  }
  .scroll-area::-webkit-scrollbar-track { 
    background: transparent; 
  }
  
  /* Smooth rotation for chevrons on collapse */
  .rotate-180 {
    transform: rotate(180deg);
  }

  /* Ripple Animation Keyframes */
  @keyframes ripple-anim {
    to {
      transform: translate(-50%, -50%) scale(6);
      opacity: 0;
    }
  }
</style>
@endsection

@section('main')
<div class="main-content">
  
  <!-- Dashboard Welcome & Header Block -->
  {{-- <div style="background-color: #207034;" class="mb-8 p-6 rounded-3xl shadow-xl border border-green-800/30 relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  
    <div class="absolute -right-16 -top-16 w-48 h-48 bg-emerald-400 rounded-full blur-3xl opacity-20"></div>
    <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-lime-400 rounded-full blur-3xl opacity-10"></div>
    
    <div class="relative z-10">
      <div class="flex items-center gap-3 mb-1">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 text-emerald-300">
          <i class="fas fa-chart-line text-sm"></i>
        </span>
        <h1 class="text-2xl font-bold tracking-tight text-white">Green Park ERP</h1>
      </div>
      <p class="text-emerald-100/80 text-sm">Welcome back, <span class="text-emerald-200 font-semibold">{{ auth()->user()->username }}</span>. Here is your dashboard overview.</p>
    </div>
    

    <div class="relative z-10 flex flex-wrap items-center gap-3">
      <!-- Academic Year Badge -->
      <div class="flex items-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-2xl text-emerald-100 text-sm font-medium">
        <i class="far fa-calendar-alt text-emerald-300"></i>
        <span>Academic Year: <strong class="text-white">{{ session('academic_year') }}</strong></span>
      </div>
      <!-- Date Badge -->
      <div class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-2xl text-emerald-100 text-sm font-medium">
        <i class="far fa-clock text-emerald-300"></i>
        <span>{{ date('l, d M Y') }}</span>
      </div>
    </div>
  </div> --}}

  <!-- Top Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4">
    <!-- Card 1: Total Students -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-300 to-teal-400 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/10 hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
      <div class="absolute -right-6 -bottom-6 text-white/10 text-9xl font-bold transition-transform duration-300 group-hover:scale-110 pointer-events-none">
        <i class="fa fa-user-friends"></i>
      </div>
      <div class="relative z-10 flex flex-col justify-between h-full">
        <div class="flex items-center justify-between mb-4">
          <span class="text-emerald-100 text-xs font-semibold tracking-wider uppercase">Total Students</span>
          <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-lg">
            <i class="fa fa-user-friends"></i>
          </div>
        </div>
        <div>
          <h3 class="text-4xl font-extrabold tracking-tight mb-1">{{ $total }}</h3>
          <span class="text-emerald-100/80 text-xs flex items-center gap-1">
            <i class="fas fa-check-circle"></i> Active enrollment
          </span>
        </div>
      </div>
    </div>

    <!-- Card 2: Boys -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-blue-400 to-indigo-500 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/10 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
      <div class="absolute -right-6 -bottom-6 text-white/10 text-9xl font-bold transition-transform duration-300 group-hover:scale-110 pointer-events-none">
        <i class="fa fa-mars"></i>
      </div>
      <div class="relative z-10 flex flex-col justify-between h-full">
        <div class="flex items-center justify-between mb-4">
          <span class="text-blue-100 text-xs font-semibold tracking-wider uppercase">Boys</span>
          <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-lg">
            <i class="fa fa-mars"></i>
          </div>
        </div>
        <div>
          <h3 class="text-4xl font-extrabold tracking-tight mb-1">{{ $boys }}</h3>
          <span class="text-blue-100/80 text-xs flex items-center gap-1">
            <i class="fas fa-percentage"></i> {{ $total > 0 ? number_format(($boys / $total) * 100, 1) : 0 }}% of total
          </span>
        </div>
      </div>
    </div>

    <!-- Card 3: Girls -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-rose-400 to-pink-500 rounded-3xl p-6 text-white shadow-lg shadow-rose-500/10 hover:shadow-xl hover:shadow-rose-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
      <div class="absolute -right-6 -bottom-6 text-white/10 text-9xl font-bold transition-transform duration-300 group-hover:scale-110 pointer-events-none">
        <i class="fa fa-venus"></i>
      </div>
      <div class="relative z-10 flex flex-col justify-between h-full">
        <div class="flex items-center justify-between mb-4">
          <span class="text-rose-100 text-xs font-semibold tracking-wider uppercase">Girls</span>
          <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-lg">
            <i class="fa fa-venus"></i>
          </div>
        </div>
        <div>
          <h3 class="text-4xl font-extrabold tracking-tight mb-1">{{ $girls }}</h3>
          <span class="text-rose-100/80 text-xs flex items-center gap-1">
            <i class="fas fa-percentage"></i> {{ $total > 0 ? number_format(($girls / $total) * 100, 1) : 0 }}% of total
          </span>
        </div>
      </div>
    </div>

    <!-- Card 4: Present Today -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-cyan-400 to-blue-500 rounded-3xl p-6 text-white shadow-lg shadow-cyan-500/10 hover:shadow-xl hover:shadow-cyan-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
      <div class="absolute -right-6 -bottom-6 text-white/10 text-9xl font-bold transition-transform duration-300 group-hover:scale-110 pointer-events-none">
        <i class="fa fa-calendar-check"></i>
      </div>
      <div class="relative z-10 flex flex-col justify-between h-full">
        <div class="flex items-center justify-between mb-4">
          <span class="text-cyan-100 text-xs font-semibold tracking-wider uppercase">Present Today</span>
          <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-lg">
            <i class="fa fa-calendar-check"></i>
          </div>
        </div>
        <div>
          <h3 class="text-4xl font-extrabold tracking-tight mb-1">{{ $present }}</h3>
          <span class="text-cyan-100/80 text-xs flex items-center gap-1">
            <i class="fas fa-percentage"></i> {{ $total > 0 ? number_format(($present / $total) * 100, 1) : 0 }}% attendance rate
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 2: Overview Cards -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
  <!-- Students Overview Column -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
    <!-- Card Header -->
    <div class="px-6 py-4.5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center ring-1 ring-emerald-500/20">
          <i class="fas fa-users text-lg"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800 tracking-tight">Students Overview</h3>
          <p class="text-[11px] text-slate-400 font-medium">Branch & Section wise distribution</p>
        </div>
      </div>
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
        {{ count($data) }} Branches
      </span>
    </div>
    
    <!-- Scrollable Area -->
    <div class="p-4 scroll-area overflow-y-auto max-h-[420px]">
      <table class="w-full text-center border-separate border-spacing-y-1">
        <thead>
          <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
            <th class="pb-3 text-start w-10 pl-2"></th>
            <th class="pb-3 text-start">Branch</th>
            <th class="pb-3">OFFLINE</th>
            <th class="pb-3">ONLINE</th>
            <th class="pb-3">TOTAL</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($data as $branch)
          <!-- Parent Branch Row -->
          <tr class="group cursor-pointer hover:bg-slate-50 transition-all duration-150 rounded-lg active:bg-slate-100/70" data-toggle="collapse" data-target="#stu-{{ Str::slug($branch->name) }}">
            <td class="py-3.5 pl-2 text-start rounded-l-xl">
              <span class="inline-flex w-6 h-6 items-center justify-center rounded-lg bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" id="chevron-stu-{{ Str::slug($branch->name) }}"></i>
              </span>
            </td>
            <td class="py-3.5 text-start font-semibold text-slate-800 text-sm">
              {{ $branch->name }}
            </td>
            
            <td class="py-3.5">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                {{ $branch->student->where('coaching_type', 'OFFLINE')->count() }}
              </span>
            </td>
            <td class="py-3.5">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-500 border border-rose-100">
                {{ $branch->student->where('coaching_type', '!=', 'OFFLINE')->count() }}
              </span>
            </td>
            <td class="py-3.5 pr-2">
              <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">
                {{ $branch->student->count() }}
              </span>
            </td>
          </tr>
          
          <!-- Collapsible Section Details Row -->
          <tr class="collapse" id="stu-{{ Str::slug($branch->name) }}">
            <td colspan="5" class="p-2 bg-slate-50/80 rounded-xl border border-slate-200/60 my-1">
              <div class="px-3 py-2">
                {{-- <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-left mb-2 pl-1">
                  Section Breakdown
                </div> --}}
                <table class="w-full text-center text-xs">
                  <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-200/60 pb-1">
                      <th class="py-2 text-start pl-2">Section</th>
                      <th class="py-2 text-blue-600">Offline</th>
                      <th class="py-2 text-rose-500">Online</th>
                      <th class="py-2 pr-2">Total</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200/40">
                    @foreach($branch->student->groupBy('section') as $sec => $students)
                    <tr class="hover:bg-white/80 transition-colors">
                      <td class="py-2.5 text-start pl-2 font-medium text-slate-700">
                        <span class="inline-block px-2 py-0.5 bg-slate-200/60 text-slate-700 rounded text-[11px] font-semibold">
                          Sec: {{ $sec ?: '-' }}
                        </span>
                      </td>
                      <td class="py-2.5">
                        <button type="button" class="px-2.5 py-1 text-blue-600 font-semibold rounded-md hover:bg-blue-100/60 active:scale-95 transition-all cursor-pointer" onclick="fetchData('{{$sec}}','{{$branch->id}}','OFFLINE')">
                          {{ $students->where('coaching_type','OFFLINE')->count() }}
                        </button>
                      </td>
                      <td class="py-2.5">
                        <button type="button" class="px-2.5 py-1 text-rose-500 font-semibold rounded-md hover:bg-rose-100/60 active:scale-95 transition-all cursor-pointer" onclick="fetchData('{{$sec}}','{{$branch->id}}','ONLINE')">
                          {{ $students->where('coaching_type', '!=', 'OFFLINE')->count() }}
                        </button>
                      </td>
                      <td class="py-2.5 pr-2">
                        <button type="button" class="px-2.5 py-1 font-bold text-slate-800 rounded-md hover:bg-slate-200/60 active:scale-95 transition-all cursor-pointer" onclick="fetchData('{{$sec}}','{{$branch->id}}','all')">
                          {{ $students->count() }}
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Attendance Column -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
    
    <!-- Card Header -->
    <div class="px-6 py-4.5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center ring-1 ring-indigo-500/20">
          <i class="fas fa-calendar-check text-lg"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800 tracking-tight">Attendance Today</h3>
          <p class="text-[11px] text-slate-400 font-medium">Daily student attendance breakdown</p>
        </div>
      </div>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
        Realtime
      </span>
    </div>
    
    <!-- Scrollable Area -->
    <div class="p-4 scroll-area overflow-y-auto max-h-[420px]">
      <table class="w-full text-center border-separate border-spacing-y-1">
        <thead>
          <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
            <th class="pb-3 text-start w-10 pl-2"></th>
            <th class="pb-3 text-start">Branch</th>
            <th class="pb-3">Total</th>
            <th class="pb-3">Present</th>
            <th class="pb-3 pr-2">Absent</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($data as $branch)
          <?php
            $presentCount = $branch->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->unique('student_id')->count(); 
            $absentCount = $branch->student->count() - $presentCount;
          ?>
          <!-- Parent Branch Row -->
          <tr class="group cursor-pointer hover:bg-slate-50 transition-all duration-150 rounded-lg active:bg-slate-100/70" data-toggle="collapse" data-target="#att-{{ Str::slug($branch->name) }}">
            <td class="py-3.5 pl-2 text-start rounded-l-xl">
              <span class="inline-flex w-6 h-6 items-center justify-center rounded-lg bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" id="chevron-att-{{ Str::slug($branch->name) }}"></i>
              </span>
            </td>
            <td class="py-3.5 text-start font-semibold text-slate-800 text-sm">
              {{ $branch->name }}
            </td>
            <td class="py-3.5">
              <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">
                {{ $branch->student->count() }}
              </span>
            </td>
            <td class="py-3.5">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                {{ $presentCount }}
              </span>
            </td>
            <td class="py-3.5 pr-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                {{ $absentCount }}
              </span>
            </td>
          </tr>
          
          <!-- Collapsible Section Details Row -->
          <tr class="collapse" id="att-{{ Str::slug($branch->name) }}">
            <td colspan="5" class="p-2 bg-slate-50/80 rounded-xl border border-slate-200/60 my-1">
              <div class="px-3 py-2">
                {{-- <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-left mb-2 pl-1">
                  Section Attendance Breakdown
                </div> --}}
                <table class="w-full text-center text-xs">
                  <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-200/60 pb-1">
                      <th class="py-2 text-start pl-2">Section</th>
                      <th class="py-2">Total Students</th>
                      <th class="py-2 text-emerald-600">Present</th>
                      <th class="py-2 pr-2 text-rose-500">Absent</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200/40">
                    @foreach($branch->student->groupBy('section') as $sec => $students)
                    <?php 
                      $secPres = $branch->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->where('section', $sec)->unique('student_id')->count(); 
                      $secAbs = $students->count() - $secPres;
                    ?>
                    <tr class="hover:bg-white/80 transition-colors">
                      <td class="py-2.5 text-start pl-2 font-medium text-slate-700">
                        <span class="inline-block px-2 py-0.5 bg-slate-200/60 text-slate-700 rounded text-[11px] font-semibold">
                          Sec: {{ $sec ?: '-' }}
                        </span>
                      </td>
                      <td class="py-2.5 font-bold text-slate-800">
                        {{ $students->count() }}
                      </td>
                      <td class="py-2.5">
                        <span class="px-2 py-0.5 font-semibold text-emerald-600 bg-emerald-50/80 rounded-md">
                          {{ $secPres }}
                        </span>
                      </td>
                      <td class="py-2.5 pr-2">
                        <span class="px-2 py-0.5 font-semibold text-rose-500 bg-rose-50/80 rounded-md">
                          {{ $secAbs }}
                        </span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  </div>

  <!-- Row 3: Staff, Concerns, and Latest Updates -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- 1. Staff Overview Card -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 overflow-hidden flex flex-col h-full">
        <div class="px-6 pt-4 pb-4  border-b border-slate-200 flex justify-between items-center bg-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shadow-sm border border-violet-100">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-none">Staff Overview</h3>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Directory</span>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        </div>
        
        <div class="p-2 pt-3 overflow-y-auto max-h-[380px] space-y-3 custom-scrollbar">
            <!-- Teaching -->
            <div class="group w-full border border-slate-50 rounded-2xl p-1 hover:bg-indigo-50/30 transition-all cursor-pointer" data-toggle="collapse" data-target="#teachStaff">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-md shadow-indigo-100">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <span class="font-bold text-slate-700 text-[13px]">Teaching Staff</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-white text-indigo-600 border border-indigo-100 shadow-sm">
                            {{ $staffs->except('Others')->map->count()->sum() }}
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-300 transition-transform group-focus:rotate-180"></i>
                    </div>
                </div>
                <div class="collapse" id="teachStaff">
                    <div class="px-3 pb-2 space-y-1">
                        @foreach($staffs->except('Others') as $dept => $staff)
                        <div class="flex justify-between items-center py-2 px-3 rounded-xl hover:bg-white transition-all">
                            <span class="text-[11px] text-slate-500 font-medium">{{ $dept }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 hover:bg-indigo-600 hover:text-white cursor-pointer transition-all active:scale-90 shadow-sm" onclick="fetchStaff('{{ $dept }}')">
                                {{ $staff->count() }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Non-Teaching -->
            <div class="group border border-slate-50 rounded-2xl p-1 hover:bg-violet-50/30 transition-all cursor-pointer" data-toggle="collapse" data-target="#nonTeachStaff">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-violet-500 text-white flex items-center justify-center text-xs shadow-md shadow-violet-100">
                            <i class="fas fa-broom"></i>
                        </span>
                        <span class="font-bold text-slate-700 text-[13px]">Non-Teaching</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-white text-violet-600 border border-violet-100 shadow-sm">
                            {{ $staffs->only('Others')->map->count()->sum() }}
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-300 transition-transform group-focus:rotate-180"></i>
                    </div>
                </div>
                <div class="collapse" id="nonTeachStaff">
                    <div class="px-3 pb-2 space-y-1">
                        @foreach($staffs->only('Others') as $dept => $staff)
                        <div class="flex justify-between items-center py-2 px-3 rounded-xl hover:bg-white transition-all">
                            <span class="text-[11px] text-slate-500 font-medium">{{ $dept == 'Others' ? 'General' : $dept }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 hover:bg-violet-600 hover:text-white cursor-pointer transition-all active:scale-90 shadow-sm" onclick="fetchStaff('{{ $dept }}')">
                                {{ $staff->count() }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Parent Concerns Card -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 overflow-hidden flex flex-col h-full">
        <div class="px-6 pt-4 pb-4  border-b border-slate-200 flex justify-between items-center bg-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm border border-rose-100">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-none">Parent Concerns</h3>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Tickets Status</span>
                </div>
            </div>
        </div>
        
        <div class="p-2 pt-3 space-y-4">
            <!-- Open -->
            <a href="{{ route('parent_concern') }}" class="flex items-center justify-between px-3 py-2  rounded-2xl bg-rose-50/40 border border-rose-100/50 hover:bg-rose-50 transition-all active:scale-[0.98] group">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-200 group-hover:scale-110 transition-transform">
                        <i class="fas fa-folder-open text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Open Issues</h4>
                        <p class="text-[10px] text-slate-400">Action Required</p>
                    </div>
                </div>
                <span class="text-xl font-black text-rose-600 leading-none">{{ $concerns->count() }}</span>
            </a>

            <!-- In Progress -->
            <a href="{{ route('parent_concern') }}" class="flex items-center justify-between px-3 py-2 rounded-2xl bg-amber-50/40 border border-amber-100/50 hover:bg-amber-50 transition-all active:scale-[0.98] group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform">
                        <i class="fas fa-spinner fa-spin text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Processing</h4>
                        <p class="text-[10px] text-slate-400">Under Review</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-amber-600 leading-none">{{ $concerns->where('status', 'In Progress')->count() }}</span>
            </a>

            <!-- Closed -->
            <a href="{{ route('parent_concern') }}" class="flex items-center justify-between px-3 py-2 rounded-2xl bg-emerald-50/40 border border-emerald-100/50 hover:bg-emerald-50 transition-all active:scale-[0.98] group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform">
                        <i class="fas fa-check-circle text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Resolved</h4>
                        <p class="text-[10px] text-slate-400">Successfully Closed</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-emerald-600 leading-none">{{ $concerns->where('status', 'Closed')->count() }}</span>
            </a>
        </div>
    </div>

    <!-- 3. Latest Updates Card -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 overflow-hidden flex flex-col h-full">
        <div class="px-6 pt-4 pb-4  border-b border-slate-200 flex justify-between items-center bg-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm border border-amber-100">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-none">Latest Updates</h3>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Communication</span>
                </div>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[380px] space-y-5 custom-scrollbar">
            <!-- Announcements -->
            <div>
                <h4 class="text-[1px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-4 h-[1px] bg-amber-200"></span> Announcements
                </h4>
                <div class="grid grid-cols-1 gap-2">
                    @foreach($announcement as $row)
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100/50 hover:bg-white hover:shadow-sm hover:border-amber-100 transition-all group">
                        <span class="text-[11px] text-slate-600 font-semibold group-hover:text-amber-700">{{ $row['branch'] }}</span>
                        <a href="{{ route('announcement.index') }}" class="px-3 py-1 text-[10px] font-bold rounded-lg bg-white text-amber-600 border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-sm">
                            {{ $row['count'] }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Chairman Videos -->
            <div>
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-4 h-[1px] bg-rose-200"></span> Chairman Videos
                </h4>
                <div class="grid grid-cols-1 gap-2">
                    @foreach($chairman as $row)
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100/50 hover:bg-white hover:shadow-sm hover:border-rose-100 transition-all group">
                        <span class="text-[11px] text-slate-600 font-semibold group-hover:text-rose-700">{{ $row['branch'] }}</span>
                        <a href="{{ route('chairmanvideo.index') }}" class="px-3 py-1 text-[10px] font-bold rounded-lg bg-white text-rose-600 border border-rose-100 group-hover:bg-rose-600 group-hover:text-white transition-all shadow-sm">
                            {{ $row['count'] }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

  <!-- Exam Overview Card -->
  <div class="bg-white mt-8 rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
          <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                  <i class="fas fa-file-alt"></i>
              </div>
              <div>
                  <h3 class="text-lg font-bold text-slate-800"> Exam Overview </h3>
                  <p class="text-[11px] text-slate-400 font-medium"> Latest examination schedule</p>
              </div>
          </div>
          <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100"> Exams </span>
      </div>
      <!-- Exam List -->
      <div class="p-4 scroll-area overflow-y-auto max-h-[350px]">
        
          <table class="w-full">
              <thead>
                  <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                      <th class="pb-3 text-center "> Exam Name </th>
                      <th class="pb-3 text-center"> Course </th>
                      <th class="pb-3 text-center"> Start Time </th>
                      <th class="pb-3 text-center "> Status </th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                  @foreach($exams as $exam)
                  <tr class="hover:bg-slate-50 transition-colors">
                      <td class="py-3.5 pl-2 text-start">
                          <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-700"> {{ $exam->name }} </span>
                          </div>
                      </td>
                      <td class="py-3.5 text-start">
                          <span class="text-xs font-medium text-slate-600">{{ $exam->course }}</span>
                      </td>
                      <td class="py-3.5 text-start">
                          <span class="text-xs font-medium text-slate-600">{{ $exam->start_at ? \Carbon\Carbon::parse($exam->start_at)->format('d M Y H:i') : '-' }}</span>
                      </td>
                      
                      <td class="py-3.5 pr-2 text-end">
                        <a href="{{ route('exam.viewexams','ONLINE') }}">
                          @if($exam->status === 'completed')
                            <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-[10px] font-bold">
                                Completed
                            </span>
                        @elseif($exam->status === 'scheduled')
                            <span class="px-2.5 py-1 rounded-full bg-green-50 text-green-600 text-[10px] font-bold">
                                Scheduled
                            </span>
                        @elseif($exam->status === 'preview')
                            <span class="px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-600 text-[10px] font-bold">
                                Preview
                            </span>
                        @endif
                        </a>
                      </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  </div>

<!-- Student Login Overview -->
  <div class="bg-white mt-8 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
    <!-- Card Header -->
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-500/10 text-violet-600 flex items-center justify-center ring-1 ring-violet-500/20">
                <i class="fas fa-sign-in-alt text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800 tracking-tight">Student Login Overview</h3>
                <p class="text-[11px] text-slate-400 font-medium">Branch wise login distribution</p>
            </div>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200/60">
            Today
        </span>
    </div>
    
    <!-- Scrollable Area -->
    <div class="p-4 scroll-area overflow-y-auto max-h-[420px]">
        <table class="w-full text-center border-separate border-spacing-y-1">
            <thead>
                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="pb-3 text-start w-10 pl-2"></th> <!-- Icon Space -->
                    <th class="pb-3 text-start">Branch</th>
                    <th class="pb-3 text-blue-600">Web</th>
                    <th class="pb-3 text-violet-600">App</th>
                    <th class="pb-3 text-teal-600">Ios</th>
                    <th class="pb-3 pr-2">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($data as $branch)
                <tr class="group hover:bg-slate-50 transition-all duration-150 rounded-lg">
                    <!-- Icon Cell -->
                    <td class="py-3.5 pl-2 text-start rounded-l-xl">
                        <span class="inline-flex w-7 h-7 items-center justify-center rounded-lg bg-slate-100 text-slate-500 group-hover:bg-violet-50 group-hover:text-violet-600 transition-colors">
                            <i class="fas fa-building text-[11px]"></i>
                        </span>
                    </td>
                    <a href="{{ route('report.userlogin') }}">
                    <td class="py-3.5 text-start font-semibold text-slate-800 text-sm"> {{ $branch->name }} </td>
                    <td class="py-3.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100"> {{ $branch->login_web }} </span>
                    </td>
                    <td class="py-3.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-violet-50 text-violet-600 border border-violet-100"> {{ $branch->login_android }} </span>
                    </td>
                    <td class="py-3.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-teal-50 text-teal-600 border border-teal-100"> {{ $branch->login_ios }} </span>
                    </td>
                    <td class="py-3.5 pr-2 rounded-r-xl">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700"> {{ $branch->login_total }} </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
  
  <!-- Details Info Modal -->
  <div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content rounded-3xl border-0 shadow-2xl overflow-hidden">
        <div class="modal-header bg-gradient-to-r from-indigo-600 to-violet-700 text-white px-6 py-4 flex items-center justify-between border-0">
          <h5 class="modal-title font-bold text-base text-white tracking-tight" id="modalLabel">Details</h5>
          <button type="button" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 transition-all rounded-full w-8 h-8 flex items-center justify-center border-0 text-lg leading-none cursor-pointer" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body p-6" id="modalBody"></div>
      </div>
    </div>
  </div>
  
</div>
@endsection

@section('js')
<script>
  function showModal(title, headers, data, mapper) {
      $('#modalLabel').text(title);
      let html = '<div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm"><table class="w-full text-left text-sm border-collapse">';
      html += '<thead class="bg-slate-50 border-b border-slate-100"><tr>';
      headers.forEach(h => html += `<th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">${h}</th>`);
      html += '</tr></thead><tbody class="divide-y divide-slate-100 bg-white">';
      
      data.forEach((item, i) => {
          html += '<tr class="hover:bg-slate-50/50 transition-colors">';
          mapper(item, i).forEach(cell => html += `<td class="px-4 py-3 text-slate-700 font-medium">${cell || '-'}</td>`);
          html += '</tr>';
      });
      
      html += '</tbody></table></div>';
      
      $('#modalBody').html(html);
      $('#infoModal').modal('show');
  }
  
  function fetchStaff(dept) {
      $.get("{{ route('dashboard.staff') }}", { department: dept })
       .done(res => showModal(dept, 
          ['#', 'Name', 'Designation', 'Branch'], 
          res.staffs, 
          (s, i) => [i+1, s.name, s.designation, s.branch?.name]
       ));
  }
  
  function fetchData(sec, camp, gen) {
      if(!sec || !camp) return;
      $.get("{{ route('dashboard.gender') }}", { section: sec, campus: camp, gender: gen })
       .done(res => showModal(`Section ${sec} - ${gen}`, 
          ['#', 'ID', 'Name', 'Type', 'Gender'], 
          res.students, 
          (s, i) => [i+1, s.student_id, s.student_name, s.coaching_type, s.gender]
       ));
  }

  // Micro-animations for accordion chevrons
  $(document).ready(function() {
      $('.collapse').on('show.bs.collapse', function () {
          let id = $(this).attr('id');
          $('#chevron-' + id).addClass('rotate-180');
      });
      $('.collapse').on('hide.bs.collapse', function () {
          let id = $(this).attr('id');
          $('#chevron-' + id).removeClass('rotate-180');
      });
  });
</script>
@endsection