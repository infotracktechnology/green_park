@extends('layouts.app')
@section('title', 'Fees Collection / Payment')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  .student-info-section {
    background: #f8f9fa;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    margin-bottom: 1.5rem;
  }
  .student-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    margin: 0 auto 10px;
    display: block;
  }
  .student-info-details dl { margin-bottom: 0.5rem; font-size: 0.875rem; }
  .student-info-details dt {
    font-weight: 600;
    width: 120px;
    color: #6c757d;
    padding-right: 5px;
    float: left;
    clear: left;
  }
  .student-info-details dd {
    margin-left: 125px;
    margin-bottom: 0.3rem;
    display: block;
  }
  .fee-details-area .nav-pills .nav-link {
    border-radius: 0;
    margin-bottom: 2px;
    font-size: 0.9rem;
    text-align: left;
    background: #f8f9fa;
    color: #495057;
    border: 1px solid transparent;
    padding: 0.75rem 1rem;
  }
  .fee-details-area .nav-pills .nav-link.active {
    background: #e9ecef;
    color: #007bff;
    border-left: 3px solid #007bff;
  }
  .fee-details-tabs .nav-tabs {
    margin-bottom: 1rem;
  }
  .fee-details-tabs .nav-tabs .nav-link {
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    font-weight: 500;
    color: #6c757d;
    padding: 0.5rem 1rem;
  }
  .fee-details-tabs .nav-tabs .nav-link.active {
    color: #495057;
    background: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
  }
  .fee-breakdown-table th,
  #receipt-details .table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 0.85rem;
  }
  .fee-breakdown-table td, .fee-breakdown-table th,
  #receipt-details .table td, #receipt-details .table th {
    padding: 0.5rem;
    vertical-align: middle;
  }
  .address-icon {
    color: #007bff;
    margin-left: 5px;
  }
</style>
@endsection

@section('main')

<div class="main-content" x-data="feePaymentScreen()">
  <section class="section">
    <div class="section-header">
      <h1>Fees Collection / Payment</h1>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body p-3">
        <form method="GET" action="#">
          <div class="row align-items-end gy-2">

            <!-- Branch Input -->
            <div class="col-md-3">
              <label for="branch_filter" class="form-label">Branch</label>
              <select name="branch" id="branch_filter" class="form-control form-control-sm">
                <option value="">All Branches</option>
                @foreach ($branches as $id => $branch)
                  <option value="{{ $id }}" {{ request('branch') == $id ? 'selected' : '' }}>{{ $branch }}</option>
                @endforeach
              </select>
            </div>

            <!-- Coaching Type -->
            <div class="col-md-2">
              <label for="coaching_type" class="form-label">Coaching Type</label>
              <select name="coaching_type" id="coaching_type" class="form-control form-control-sm">
                <option value="">All Types</option>
                @foreach ($coachingTypes as $coachingType)
                  <option value="{{ $coachingType->coaching_type }}" {{ request('coaching_type') == $coachingType->coaching_type ? 'selected' : '' }}>
                    {{ ucfirst($coachingType->coaching_type) }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Search By -->
            <div class="col-md-2">
              <label for="student_search_type" class="form-label">Search By</label>
              <select name="student_search_type" id="student_search_type" class="form-control form-control-sm">
                <option value="student_name" {{ request('student_search_type') == 'student_name' ? 'selected' : '' }}>Student Name</option>
                <option value="user_name" {{ request('student_search_type') == 'user_name' ? 'selected' : '' }}>User Name</option>
                <option value="student_id" {{ request('student_search_type') == 'student_id' ? 'selected' : '' }}>Student ID</option>
                <option value="father_name" {{ request('student_search_type') == 'father_name' ? 'selected' : '' }}>Father Name</option>
                <option value="mother_name" {{ request('student_search_type') == 'mother_name' ? 'selected' : '' }}>Mother Name</option>
                <option value="parent_mobile" {{ request('student_search_type') == 'parent_mobile' ? 'selected' : '' }}>Mobile Number</option>
              </select>
            </div>

            <!-- Search Term -->
            <div class="col-md-3 position-relative">
              <label for="student_query" class="form-label">Search Term</label>
              <input type="text" name="student_query" id="student_query" class="form-control form-control-sm"
                     placeholder="Enter Search Term..." autocomplete="off" value="{{ request('student_query') }}">
              <div id="student_suggestions" class="list-group position-absolute w-100" style="z-index: 999;"></div>
            </div>

            <!-- Search Button -->
            <div class="col-md-2 text-end">
              <label class="form-label d-none d-md-block">&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fas fa-search me-1"></i> Search
              </button>
            </div>

          </div>
        </form>

        <!-- Student Profile Card -->
        @if($student)
        <div class="student-info-section mt-4">
          <h5 class="fw-bold text-primary mb-3">
            <i class="fas fa-user me-2 text-secondary"></i> {{ $student->student_name }}
            <small class="text-muted">({{ $student->student_id }})</small>
          </h5>
          <div class="row">
            <div class="col-md-6">
              <ul class="list-unstyled small">
                <li><strong>Father Name:</strong> {{ $student->father_name }}</li>
                <li><strong>Mother Name:</strong> {{ $student->mother_name }}</li>
                <li><strong>Parent Mobile:</strong> {{ $student->ph_no1 }}</li>
              </ul>
            </div>
            <div class="col-md-6">
              <ul class="list-unstyled small">
                <li><strong>Section:</strong> {{ $student->section }}</li>
                <li><strong>Coaching Type:</strong> {{ ucfirst($student->coaching_type) }}</li>
                <li><strong>Student Status:</strong> {{ ucfirst($student->student_status) }}</li>
              </ul>
            </div>
          </div>
        </div>
        @endif

      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  const students = @json($students);
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById('student_query');
    const suggestionsBox = document.getElementById('student_suggestions');
    const typeSelector = document.getElementById('student_search_type');

    input.addEventListener('keyup', function () {
      const query = input.value.toLowerCase();
      const field = typeSelector.value;
      suggestionsBox.innerHTML = '';

      if (query.length < 2) return;

      const matches = students
        .map(student => student[field])
        .filter(val => val && val.toLowerCase().includes(query))
        .filter((v, i, a) => a.indexOf(v) === i)
        .slice(0, 10);

      matches.forEach(match => {
        const item = document.createElement('a');
        item.href = '#';
        item.className = 'list-group-item list-group-item-action';
        item.textContent = match;
        item.addEventListener('click', function (e) {
          e.preventDefault();
          input.value = match;
          suggestionsBox.innerHTML = '';
        });
        suggestionsBox.appendChild(item);
      });
    });

    document.addEventListener('click', function (e) {
      if (!suggestionsBox.contains(e.target) && e.target !== input) {
        suggestionsBox.innerHTML = '';
      }
    });
  });
</script>
@endsection
