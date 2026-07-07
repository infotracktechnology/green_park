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
  <div style="background-color: #207034;" class="mb-8 p-6 rounded-3xl shadow-xl border border-green-800/30 relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  
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
  </div>

  <!-- Top Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Students -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/10 hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
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
    <div class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/10 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
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
    <div class="group relative overflow-hidden bg-gradient-to-br from-rose-500 to-pink-600 rounded-3xl p-6 text-white shadow-lg shadow-rose-500/10 hover:shadow-xl hover:shadow-rose-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
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
    <div class="group relative overflow-hidden bg-gradient-to-br from-cyan-500 to-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-cyan-500/10 hover:shadow-xl hover:shadow-cyan-500/20 transition-all duration-300 transform hover:-translate-y-1 ripple-light active:scale-[0.98] cursor-pointer">
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
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <i class="fas fa-users text-emerald-600 !text-2xl"></i>
          </div>
          <h3 class="text-lg font-bold text-slate-800">Students Overview</h3>
        </div>
        <span class="text-xs text-slate-400 font-medium">{{ count($data) }} Branches</span>
      </div>
      
      <div class="p-6 scroll-area overflow-y-auto max-h-[400px]">
        <table class="w-full text-center border-collapse">
          <thead>
            <tr class="border-b border-slate-100 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
              <th class="pb-3 text-start w-10"></th>
              <th class="pb-3 text-start">Branch</th>
              <th class="pb-3">OFFLINE</th>
              <th class="pb-3">ONLINE</th>
              <th class="pb-3">TOTAL</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            @foreach($data as $branch)
            <!-- Parent Row -->
            <tr class="group cursor-pointer hover:bg-slate-50/70 transition-colors animate-all duration-150 ripple-dark active:bg-slate-100/50" data-toggle="collapse" data-target="#stu-{{ Str::slug($branch->name) }}">
              <td class="py-4 text-start">
                <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                  <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" id="chevron-stu-{{ Str::slug($branch->name) }}"></i>
                </span>
              </td>
              <td class="py-4 text-start font-semibold text-slate-700 text-sm">{{ $branch->name }}</td>
              
              <td class="py-4 text-blue-600 font-medium text-sm">{{ $branch->student->where('coaching_type', 'OFFLINE')->count() }}</td>
              <td class="py-4 text-rose-500 font-medium text-sm">{{ $branch->student->where('coaching_type', '!=', 'OFFLINE')->count() }}</td>
              <td class="py-4">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200/50">
                  {{ $branch->student->count() }}
                </span>
              </td>
            </tr>
            
            <!-- Collapsible Row -->
            <tr class="collapse" id="stu-{{ Str::slug($branch->name) }}">
              <td colspan="5" class="p-0 bg-slate-50/50 border-y border-slate-100">
                <div class="px-4 py-3">
                  <table class="w-full text-center text-xs">
                    <thead>
                      <tr class="text-slate-400 font-semibold border-b border-slate-200/60">
                        <th class="py-2 text-start pl-4">Section</th>
                        <th class="py-2 text-blue-600">OFFLINE</th>
                        <th class="py-2 text-rose-500">ONLINE</th>
                        <th class="py-2">Total Students</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                      @foreach($branch->student->groupBy('section') as $sec => $students)
                      <tr class="hover:bg-slate-100/50 transition-colors">
                        <td class="py-2.5 text-start pl-4 font-medium text-slate-500">Sec: {{ $sec ?: '-' }}</td>
                        <td class="py-2.5 cursor-pointer text-blue-600 font-semibold hover:underline ripple-dark active:scale-95" onclick="fetchData('{{$sec}}','{{$branch->id}}','OFFLINE')">
                          {{ $students->where('coaching_type','OFFLINE')->count() }}
                        </td>
                        <td class="py-2.5 cursor-pointer text-rose-500 font-semibold hover:underline ripple-dark active:scale-95" onclick="fetchData('{{$sec}}','{{$branch->id}}','ONLINE')">
                          {{ $students->where('coaching_type', '!=', 'OFFLINE')->count() }}
                        </td>
                        <td class="py-2.5 cursor-pointer font-bold text-slate-700 hover:underline ripple-dark active:scale-95" onclick="fetchData('{{$sec}}','{{$branch->id}}','all')">
                          {{ $students->count() }}
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
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
            <i class="fas fa-calendar-check !text-2xl"></i>
          </div>
          <h3 class="text-lg font-bold text-slate-800">Attendance Today</h3>
        </div>
        <span class="text-xs text-indigo-600 font-semibold bg-indigo-50 px-2 py-1 rounded-lg">Realtime</span>
      </div>
      
      <div class="p-6 scroll-area overflow-y-auto max-h-[400px]">
        <table class="w-full text-center border-collapse">
          <thead>
            <tr class="border-b border-slate-100 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
              <th class="pb-3 text-start w-10"></th>
              <th class="pb-3 text-start">Branch</th>
              <th class="pb-3">Total</th>
              <th class="pb-3">Present</th>
              <th class="pb-3">Absent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            @foreach($data as $branch)
            <?php
              $presentCount = $branch->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->unique('student_id')->count(); 
              $absentCount = $branch->student->count() - $presentCount;
            ?>
            <!-- Parent Row -->
            <tr class="group cursor-pointer hover:bg-slate-50/70 transition-colors animate-all duration-150 ripple-dark active:bg-slate-100/50" data-toggle="collapse" data-target="#att-{{ Str::slug($branch->name) }}">
              <td class="py-4 text-start">
                <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                  <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" id="chevron-att-{{ Str::slug($branch->name) }}"></i>
                </span>
              </td>
              <td class="py-4 text-start font-semibold text-slate-700 text-sm">{{ $branch->name }}</td>
              <td class="py-4">
                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-slate-100 text-slate-600">
                  {{ $branch->student->count() }}
                </span>
              </td>
              <td class="py-4">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                  {{ $presentCount }}
                </span>
              </td>
              <td class="py-4">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-100">
                  {{ $absentCount }}
                </span>
              </td>
            </tr>
            
            <!-- Collapsible Row -->
            <tr class="collapse" id="att-{{ Str::slug($branch->name) }}">
              <td colspan="5" class="p-0 bg-slate-50/50 border-y border-slate-100">
                <div class="px-4 py-3">
                  <table class="w-full text-center text-xs">
                    <thead>
                      <tr class="text-slate-400 font-semibold border-b border-slate-200/60">
                        <th class="py-2 text-start pl-4">Section</th>
                        <th class="py-2">Total Students</th>
                        <th class="py-2 text-emerald-600">Present</th>
                        <th class="py-2 text-rose-500">Absent</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                      @foreach($branch->student->groupBy('section') as $sec => $students)
                      <?php 
                        $secPres = $branch->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->where('section', $sec)->unique('student_id')->count(); 
                        $secAbs = $students->count() - $secPres;
                      ?>
                      <tr class="hover:bg-slate-100/50 transition-colors">
                        <td class="py-2.5 text-start pl-4 font-medium text-slate-500">Sec: {{ $sec ?: '-' }}</td>
                        <td class="py-2.5 font-semibold text-slate-700">{{ $students->count() }}</td>
                        <td class="py-2.5 text-emerald-600 font-semibold">{{ $secPres }}</td>
                        <td class="py-2.5 text-rose-500 font-semibold">{{ $secAbs }}</td>
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
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Staff Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center text-sm">
            <i class="fas fa-user-tie"></i>
          </div>
          <h3 class="text-lg font-bold text-slate-800">Staff Overview</h3>
        </div>
        <span class="text-xs text-slate-400 font-medium">Overview</span>
      </div>
      
      <div class="p-6 scroll-area overflow-y-auto max-h-[350px] space-y-4">
        <!-- Teaching Staff Section -->
        <div>
          <div class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100/50 cursor-pointer flex justify-between items-center hover:bg-indigo-50 transition-colors ripple-dark active:scale-[0.99]" data-toggle="collapse" data-target="#teachStaff">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-indigo-500 text-white flex items-center justify-center text-xs">
                <i class="fas fa-chalkboard-teacher"></i>
              </span>
              <span class="font-bold text-slate-800 text-sm">Teaching</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-white text-indigo-700 shadow-sm border border-indigo-100">
                {{ $staffs->except('Others')->map->count()->sum() }}
              </span>
              <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200" id="chevron-teachStaff"></i>
            </div>
          </div>
          
          <div class="collapse" id="teachStaff">
            <div class="mt-2 pl-3 border-l-2 border-indigo-100 space-y-1 py-1">
              @foreach($staffs->except('Others') as $dept => $staff)
              <div class="flex justify-between items-center py-2 px-3 hover:bg-slate-50 rounded-xl transition-colors">
                <span class="text-xs text-slate-600 font-medium">{{ $dept }}</span>
                <span class="px-2 py-0.5 text-xs font-bold rounded bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700 cursor-pointer transition-colors ripple-dark active:scale-90" onclick="fetchStaff('{{ $dept }}')">
                  {{ $staff->count() }}
                </span>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Non-Teaching Staff Section -->
        <div>
          <div class="p-4 rounded-2xl bg-violet-50/40 border border-violet-100/50 cursor-pointer flex justify-between items-center hover:bg-violet-50 transition-colors ripple-dark active:scale-[0.99]" data-toggle="collapse" data-target="#nonTeachStaff">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-violet-500 text-white flex items-center justify-center text-xs">
                <i class="fas fa-broom"></i>
              </span>
              <span class="font-bold text-slate-800 text-sm">Non-Teaching</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-white text-violet-700 shadow-sm border border-violet-100">
                {{ $staffs->only('Others')->map->count()->sum() }}
              </span>
              <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200" id="chevron-nonTeachStaff"></i>
            </div>
          </div>
          
          <div class="collapse" id="nonTeachStaff">
            <div class="mt-2 pl-3 border-l-2 border-violet-100 space-y-1 py-1">
              @foreach($staffs->only('Others') as $dept => $staff)
              <div class="flex justify-between items-center py-2 px-3 hover:bg-slate-50 rounded-xl transition-colors">
                <span class="text-xs text-slate-600 font-medium">{{ $dept == 'Others' ? 'General' : $dept }}</span>
                <span class="px-2 py-0.5 text-xs font-bold rounded bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 cursor-pointer transition-colors ripple-dark active:scale-90" onclick="fetchStaff('{{ $dept }}')">
                  {{ $staff->count() }}
                </span>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Parent Concerns Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <h3 class="text-lg font-bold text-slate-800">Parent Concerns</h3>
        </div>
        <span class="text-xs text-slate-400 font-medium">Status</span>
      </div>
      
      <div class="p-6 scroll-area overflow-y-auto max-h-[350px] flex flex-col justify-center">
        <div class="grid grid-cols-1 gap-4">
          <!-- Total Open Concerns -->
          <a href="{{ route('parent_concern') }}" class="group flex items-center justify-between p-4 rounded-2xl border border-rose-100 bg-rose-50/30 hover:bg-rose-50 transition-all duration-200 ripple-dark active:scale-[0.98]">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center shadow-sm shadow-rose-500/20">
                <i class="fas fa-folder-open text-sm"></i>
              </div>
              <div>
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-rose-700 transition-colors">Total Open</h4>
                <p class="text-[10px] text-slate-400">Awaiting resolution</p>
              </div>
            </div>
            <span class="text-2xl font-extrabold text-rose-600">{{ $concerns->count() }}</span>
          </a>

          <!-- In Progress Concerns -->
          <a href="{{ route('parent_concern') }}" class="group flex items-center justify-between p-4 rounded-2xl border border-amber-100 bg-amber-50/30 hover:bg-amber-50 transition-all duration-200 ripple-dark active:scale-[0.98]">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm shadow-amber-500/20">
                <i class="fas fa-spinner fa-spin text-sm"></i>
              </div>
              <div>
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-amber-700 transition-colors">In Progress</h4>
                <p class="text-[10px] text-slate-400">Under review</p>
              </div>
            </div>
            <span class="text-2xl font-extrabold text-amber-600">{{ $concerns->where('status', 'In Progress')->count() }}</span>
          </a>

          <!-- Closed Concerns -->
          <a href="{{ route('parent_concern') }}" class="group flex items-center justify-between p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30 hover:bg-emerald-50 transition-all duration-200 ripple-dark active:scale-[0.98]">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm shadow-emerald-500/20">
                <i class="fas fa-check-circle text-sm"></i>
              </div>
              <div>
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition-colors">Closed</h4>
                <p class="text-[10px] text-slate-400">Resolved</p>
              </div>
            </div>
            <span class="text-2xl font-extrabold text-emerald-600">{{ $concerns->where('status', 'Closed')->count() }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Latest Updates Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
            <i class="fas fa-bullhorn"></i>
          </div>
          <h3 class="text-lg font-bold text-slate-800">Latest Updates</h3>
        </div>
        <span class="text-xs text-slate-400 font-medium">Broadcasts</span>
      </div>
      
      <div class="p-6 scroll-area overflow-y-auto max-h-[350px] space-y-5">
        <!-- Announcements Section -->
        <div>
          <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i class="fas fa-bullhorn text-amber-500 text-[10px]"></i> Announcements
          </h4>
          <div class="space-y-2">
            @foreach($announcement as $row)
            <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
              <span class="text-xs text-slate-600 font-medium">{{ $row['branch'] }}</span>
              <a href="{{ route('announcement.index') }}" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-amber-100/70 text-amber-800 hover:bg-amber-200 transition-colors ripple-dark active:scale-90">
                {{ $row['count'] }}
              </a>
            </div>
            @endforeach
          </div>
        </div>

        <!-- Chairman Videos Section -->
        <div>
          <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i class="fas fa-video text-rose-500 text-[10px]"></i> Chairman Videos
          </h4>
          <div class="space-y-2">
            @foreach($chairman as $row)
            <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
              <span class="text-xs text-slate-600 font-medium">{{ $row['branch'] }}</span>
              <a href="{{ route('chairmanvideo.index') }}" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-100/70 text-rose-800 hover:bg-rose-200 transition-colors ripple-dark active:scale-90">
                {{ $row['count'] }}
              </a>
            </div>
            @endforeach
          </div>
        </div>
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