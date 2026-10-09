@extends('layouts.app')
@section('title', 'Visitor Verification')

@section('css')
<style>
  .profile-pic {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 20%;
    border: 3px solid #dee2e6;
  }
  .visitor-pic {
    width: 110px;
    height: 110px;
    object-fit: cover;
    border-radius: 20%;
    border: 3px solid #dee2e6;
  }
  .visitor-card {
    background: #fdfdfd;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    height: 100%;
    transition: all 0.3s ease;
  }
  .visitor-card:hover {
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }
  .info-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 2px;
  }
  .info-value {
    font-size: 14px;
    font-weight: 600;
    color: #34395e;
  }
  thead th {
    background-color: #56ade8 !important;
    color: #222 !important;
    border: 1px solid #222 !important;
  }
  .table-custom td {
    border: 1px solid #dee2e6 !important;
    vertical-align: middle;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">

      {{-- Search Card --}}
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4>Visitor Verification</h4>
            </div>
            <div class="card-body">
              <form method="GET" action="{{ route('report.verification') }}">
                <div class="row align-items-end">
                  <div class="form-group col-lg-2 col-md-6 mb-3 mb-md-0">
                    <label>Student ID / Student Name</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Enter Student ID or Student Name" value="{{ request('search') }}" required>
                  </div>

                  <div class="form-group col-lg-4 col-md-6 mb-0">
                    <button type="submit" class="btn btn-primary"><i data-feather="search"></i> Search </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      @if(request()->filled('search'))
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4>Results</h4>
            </div>
            <div class="card-body p-0">
              @if($students->count() > 0)
                <div class="table-responsive p-4">
                  <table class="table table-striped table-hover mb-0 table-custom">
                    <thead>
                      <tr>
                        <th style="width: 60px;">#</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>H/D</th>
                        <th class="text-center" style="width: 120px;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($students as $key => $student)
                        <tr>
                          <td>{{ $key + 1 }}</td>
                          <td><strong>{{ $student->student_id }}</strong></td>
                          <td>{{ $student->student_name }}</td>
                          <td>{{ $student->course }}</td>
                          <td>{{ $student->section }}</td>
                          <td>{{ $student->hostel_dayscholar }}</td>
                          <td class="text-center">
                            <a href="{{ route('report.verification', ['student_id' => $student->student_id]) }}"
                               class="btn btn-sm btn-primary">
                              <i class="fas fa-eye"></i> VIEW
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-center py-5">
                  <i data-feather="user-x" style="width: 45px; height: 45px; color: #dc3545;"></i>
                  <h5 class="mt-3">Student Not Found</h5>
                  <p class="text-muted mb-0">No student found for "{{ request('search') }}"</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Selected Student / Visitor Details --}}
      @if($selectedStudent)
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4><i data-feather="user-check"></i> Visitor Details</h4>
            </div>
            <div class="card-body">

              {{-- Photos Checking Logic --}}
              @php
                $studentPhotoPath  = base_path("assets/profilepic/{$selectedStudent->student_id}.jpg");
                $fatherPhotoPath   = base_path("assets/fatherpic/{$selectedStudent->student_id}.jpg");
                $motherPhotoPath   = base_path("assets/motherpic/{$selectedStudent->student_id}.jpg");
                $guardianPhotoPath = base_path("assets/guardianpic/{$selectedStudent->student_id}.jpg");

                $photo         = file_exists($studentPhotoPath)  ? asset("profilepic/{$selectedStudent->student_id}.jpg")  : asset('img/avather.png');
                $fatherPhoto   = file_exists($fatherPhotoPath)   ? asset("fatherpic/{$selectedStudent->student_id}.jpg")   : asset('img/avather.png');
                $motherPhoto   = file_exists($motherPhotoPath)   ? asset("motherpic/{$selectedStudent->student_id}.jpg")   : asset('img/avather.png');
                $guardianPhoto = file_exists($guardianPhotoPath) ? asset("guardianpic/{$selectedStudent->student_id}.jpg") : asset('img/avather.png');
              @endphp

              {{-- Student Profile & Basic Info --}}
              <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                  <img src="{{ $photo }}" class="profile-pic" alt="Student Photo">
                  <h5 class="mt-3 mb-1">{{ $selectedStudent->student_name }}</h5>
                  {{-- <span>{{ $selectedStudent->student_id }}</span> --}}
                </div>

                <div class="col-md-9">
                  <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                      <div class="info-label">Student ID</div>
                      <div class="info-value">{{ $selectedStudent->student_id }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                      <div class="info-label">Student Name</div>
                      <div class="info-value">{{ $selectedStudent->student_name }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                      <div class="info-label">Course</div>
                      <div class="info-value">{{ $selectedStudent->course }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                      <div class="info-label">Section</div>
                      <div class="info-value">{{ $selectedStudent->section }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                      <div class="info-label">Date of Birth</div>
                      <div class="info-value">{{ $selectedStudent->dob }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                      <div class="info-label">Address</div>
                      <div class="info-value">{{ $selectedStudent->door_no }} {{ $selectedStudent->street_name }}</div>
                      <div class="info-value">{{ $selectedStudent->city }}</div>
                      <div class="info-value">{{ $selectedStudent->district }} - {{ $selectedStudent->pincode }}</div>

                    </div>
                  </div>
                </div>
              </div>

              <hr>

              <h5 class="mt-4 mb-4 text-primary">Parent / Guardian Details</h5>
              <div class="row">

                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="visitor-card">
                    <img src="{{ $fatherPhoto }}" class="visitor-pic mb-3" alt="Father Photo">
                    <div><span class="badge badge-primary mb-2">Father</span></div>
                    
                    <div class="info-label">Father Name</div>
                    <div class="info-value mb-2">{{ $selectedStudent->father_name ?? '-' }}</div>

                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $selectedStudent->father_ph_no ?? '-' }}</div>
                  </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="visitor-card">
                    <img src="{{ $motherPhoto }}" class="visitor-pic mb-3" alt="Mother Photo">
                    <div><span class="badge badge-primary mb-2">Mother</span></div>

                    <div class="info-label">Mother Name</div>
                    <div class="info-value mb-2">{{ $selectedStudent->mother_name ?? '-' }}</div>

                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $selectedStudent->mother_ph_no ?? '-' }}</div>
                  </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="visitor-card">
                    <img src="{{ $guardianPhoto }}" class="visitor-pic mb-3" alt="Guardian Photo">
                    <div><span class="badge badge-warning mb-2">Guardian 1</span></div>

                    <div class="info-label">Guardian Name</div>
                    <div class="info-value mb-2">{{ $selectedStudent->guardian_name ?? '-' }}</div>

                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $selectedStudent->guardian_ph_no ?? '-' }}</div>
                  </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="visitor-card">
                    <img src="{{ $guardianPhoto }}" class="visitor-pic mb-3" alt="Guardian Photo">
                    <div><span class="badge badge-warning mb-2">Guardian 2</span></div>

                    <div class="info-label">Guardian Name</div>
                    <div class="info-value mb-2">{{ $selectedStudent->guardianII_name ?? '-' }}</div>

                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $selectedStudent->guardianII_ph_no ?? '-' }}</div>
                  </div>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>
      @endif

    </div>
  </section>
</div>
@endsection

@section('js')
<script>
  if (typeof feather !== 'undefined') {
    feather.replace();
  }
</script>
@endsection