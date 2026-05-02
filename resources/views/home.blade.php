@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('css')
<style>
  :root {
      --text-dark: #1f2937;
      --text-muted: #6b7280;
      --radius: 16px;
  }
  .text-dark { color: var(--text-dark) !important; }
  .text-muted { color: var(--text-muted) !important; }
  
  .bg-pastel-purple { background-color: #dcd6f7 !important; color: #5b4e8e; }
  .bg-pastel-yellow { background-color: #fcf6bd !important; color: #8a7e00; }
  .bg-pastel-blue   { background-color: #d0f4de !important; color: #1e6b36; }
  .bg-pastel-pink   { background-color: #ffdae9 !important; color: #8c2f50; }
  
  .card {
      border: none;
      border-radius: var(--radius);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      margin-bottom: 24px;
      background: #fff;
  }
  .card-header {
      background: transparent !important;
      border-bottom: none;
      padding: 20px 24px 0;
  }
  .card-title { font-weight: 700; color: var(--text-dark); margin: 0; }
  
  /* Custom Scrollbar */
  .scroll-area { overflow-y: auto; height: 350px; padding: 0 5px; }
  .scroll-area::-webkit-scrollbar { width: 5px; }
  .scroll-area::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
  
  .table thead th { border: none; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; }
  .table td { vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
  .cursor-pointer { cursor: pointer; }
  .stat-badge { padding: 8px 12px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; }
</style>
@endsection

@section('main')
<div class="main-content">
  <div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0 text-dark fw-bold">Dashboard</h1>
      <div class="d-flex align-items-center">
        <form action="{{ route('admin.home') }}" method="GET" class="d-flex align-items-center">
          <label class="mb-0 mr-2 text-muted fw-bold">Academic Year:</label>
          <select name="academic_year" class="form-control form-control-sm border-0 shadow-sm rounded-pill px-3" onchange="this.form.submit()" style="width: 150px; background: #fff;">
            @foreach($academic_years as $year)
              <option value="{{ $year->academic_year }}" {{ $active_year == $year->academic_year ? 'selected' : '' }}>
                {{ $year->academic_year }}
              </option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
  </div>

  <!-- Top Stats Row (Derived from your data for the UI look) -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-pastel-purple p-4 h-100 d-flex justify-content-center">
        <h2 class="fw-bold mb-0">{{ $total }}</h2>
        <span class="fs-6 opacity-75">Total Students</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-pastel-yellow p-4 h-100 d-flex justify-content-center">
        <h2 class="fw-bold mb-0">{{ $boys }}</h2>
        <span class="fs-6 opacity-75">Boys</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-pastel-pink p-4 h-100 d-flex justify-content-center">
        <h2 class="fw-bold mb-0">{{ $girls }}</h2>
        <span class="fs-6 opacity-75">Girls</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-pastel-blue p-4 h-100 d-flex justify-content-center">
        <h2 class="fw-bold mb-0">{{ $present }}</h2>
        <span class="fs-6 opacity-75">Present Today</span>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Students Section -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title">Students Overview</h5>
          <i class="fas fa-ellipsis-h text-muted"></i>
        </div>
        <div class="card-body scroll-area">
          <table class="table table-hover text-center">
            <thead>
              <tr>
                <th></th>
                <th class="text-start">Branch</th>
                <th>Total</th>
                <th>Boys</th>
                <th>Girls</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data as $branch)
              <tr class="fw-bold cursor-pointer" data-toggle="collapse" data-target="#stu-{{ Str::slug($branch->name) }}">
                <td><i class="fas fa-chevron-circle-down text-primary"></i></td>
                <td class="text-start text-dark">{{ $branch->name }}</td>
                <td><span class="badge bg-light text-dark">{{ $branch->student->count() }}</span></td>
                <td class="text-primary">{{ $branch->student->where('gender', 'MALE')->count() }}</td>
                <td class="text-danger">{{ $branch->student->where('gender', 'FEMALE')->count() }}</td>
              </tr>
              <tr class="collapse" id="stu-{{ Str::slug($branch->name) }}">
                <td colspan="5" class="p-0">
                  <table class="table table-sm table-striped mb-0 bg-light">
                    @foreach($branch->student->groupBy('section') as $sec => $students)
                    <tr>
                      <td class="w-25 pl-4 text-start small text-muted">Sec: {{ $sec ?: '-' }}</td>
                      <td class="cursor-pointer" onclick="fetchData('{{$sec}}','{{$branch->id}}','all')">{{ $students->count() }}</td>
                      <td class="text-primary cursor-pointer" onclick="fetchData('{{$sec}}','{{$branch->id}}','MALE')">{{ $students->where('gender','MALE')->count() }}</td>
                      <td class="text-danger cursor-pointer" onclick="fetchData('{{$sec}}','{{$branch->id}}','FEMALE')">{{ $students->where('gender','FEMALE')->count() }}</td>
                    </tr>
                    @endforeach
                  </table>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Attendance Section -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title">Attendance</h5>
          <i class="fas fa-ellipsis-h text-muted"></i>
        </div>
        <div class="card-body scroll-area">
          <table class="table table-hover text-center">
            <thead>
              <tr>
                <th></th>
                <th class="text-start">Branch</th>
                <th>Total</th>
                <th>Present</th>
                <th>Absent</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data as $branch)
               <?php
                $presentCount = $branch->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->unique('student_id')->count(); 
              ?>
              <tr class="fw-bold cursor-pointer" data-toggle="collapse" data-target="#att-{{ Str::slug($branch->name) }}">
                <td><i class="fas fa-chevron-circle-down text-success"></i></td>
                <td class="text-start text-dark">{{ $branch->name }}</td>
                <td>{{ $branch->student->count() }}</td>
                <td class="text-success">{{ $presentCount }}</td>
                <td class="text-danger">{{ $branch->student->count() - $presentCount }}</td>
              </tr>
              <tr class="collapse" id="att-{{ Str::slug($branch->name) }}">
                <td colspan="5" class="p-0">
                  <table class="table table-sm table-striped mb-0 bg-light">
                    @foreach($branch->student->groupBy('section') as $sec => $students)
                    <?php $secPres = $branch->attendance->where('attendance_date', date('Y-m-d'))->where('status', 'P')->where('section', $sec)->unique('student_id')->count(); ?>
                    <tr>
                      <td class="w-25 pl-4 text-start small text-muted">Sec: {{ $sec ?: '-' }}</td>
                      <td>{{ $students->count() }}</td>
                      <td class="text-success">{{ $secPres }}</td>
                      <td class="text-danger">{{ $students->count() - $secPres }}</td>
                    </tr>
                    @endforeach
                  </table>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Staff Overview -->
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title">Staff</h5>
        </div>
        <div class="card-body scroll-area">
          <!-- Teaching -->
          <div class="p-3 mb-2 rounded bg-pastel-blue cursor-pointer d-flex justify-content-between" data-toggle="collapse" data-target="#teachStaff">
            <span class="fw-bold text-dark"><i class="fas fa-chalkboard-teacher mr-2"></i> Teaching</span>
            <span class="badge bg-white text-dark">{{ $staffs->except('Others')->map->count()->sum() }}</span>
          </div>
          <div class="collapse mb-3" id="teachStaff">
            <ul class="list-group list-group-flush">
              @foreach($staffs->except('Others') as $dept => $staff)
              <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-2 py-1">
                <small>{{ $dept }}</small>
                <span class="badge bg-light text-success cursor-pointer" onclick="fetchStaff('{{ $dept }}')">{{ $staff->count() }}</span>
              </li>
              @endforeach
            </ul>
          </div>

          <div class="p-3 rounded bg-pastel-purple cursor-pointer d-flex justify-content-between" data-toggle="collapse" data-target="#nonTeachStaff">
            <span class="fw-bold text-dark"><i class="fas fa-broom mr-2"></i> Non-Teaching</span>
            <span class="badge bg-white text-dark">{{ $staffs->only('Others')->map->count()->sum() }}</span>
          </div>
          <div class="collapse mt-2" id="nonTeachStaff">
            <ul class="list-group list-group-flush">
              @foreach($staffs->only('Others') as $dept => $staff)
              <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-2 py-1">
                <small>{{ $dept == 'Others' ? 'General' : $dept }}</small>
                <span class="badge bg-light text-primary cursor-pointer" onclick="fetchStaff('{{ $dept }}')">{{ $staff->count() }}</span>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title">Concerns</h5>
        </div>
        <div class="card-body scroll-area pt-0">
          <ul class="list-group list-group-flush mt-3">
            <li class="list-group-item d-flex justify-content-between border-0 px-0">
              <span class="text-muted">Total Open</span>
              <a href="{{ route('parent_concern') }}" class="fw-bold text-danger">{{ $concerns->count() }}</a>
            </li>
            <li class="list-group-item d-flex justify-content-between border-0 px-0">
              <span class="text-muted">In Progress</span>
              <a href="{{ route('parent_concern') }}" class="fw-bold text-warning">{{ $concerns->where('status', 'In Progress')->count() }}</a>
            </li>
            <li class="list-group-item d-flex justify-content-between border-0 px-0">
              <span class="text-muted">Closed</span>
              <a href="{{ route('parent_concern') }}" class="fw-bold text-success">{{ $concerns->where('status', 'Closed')->count() }}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title">Latest Updates</h5>
        </div>
        <div class="card-body scroll-area">
          <h6 class="text-muted small text-uppercase mb-2">Announcements</h6>
          @foreach($announcement as $row)
          <div class="d-flex justify-content-between mb-2">
            <span class="small">{{ $row['branch'] }}</span>
            <a href="{{ route('announcement.index') }}" class="badge bg-pastel-yellow text-dark">{{ $row['count'] }}</a>
          </div>
          @endforeach

          <h6 class="text-muted small text-uppercase mt-4 mb-2">Chairman Videos</h6>
          @foreach($chairman as $row)
          <div class="d-flex justify-content-between mb-2">
            <span class="small">{{ $row['branch'] }}</span>
            <a href="{{ route('chairmanvideo.index') }}" class="badge bg-pastel-pink text-dark">{{ $row['count'] }}</a>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-pastel-purple">
        <h5 class="modal-title fw-bold text-dark" id="modalLabel">Details</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="modalBody"></div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  function showModal(title, headers, data, mapper) {
      $('#modalLabel').text(title);
      let html = '<table class="table table-striped"><thead><tr>';
      headers.forEach(h => html += `<th>${h}</th>`);
      html += '</tr></thead><tbody>';
      
      data.forEach((item, i) => {
          html += '<tr>';
          mapper(item, i).forEach(cell => html += `<td>${cell || '-'}</td>`);
          html += '</tr>';
      });
      
      $('#modalBody').html(html + '</tbody></table>');
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
</script>
@endsection