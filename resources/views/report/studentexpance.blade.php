@extends('layouts.app')
@section('title', 'Student Expense Report')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          <div class="card card-primary">
            <div class="card-header">
              <h4>Student Expense Report</h4>
            </div>
            <div class="card-body">
              
              {{-- FILTER FORM --}}
              <form method="get" action="{{ route('report.studentexpense') }}">
                <div class="row">

                  {{-- Branch --}}
                  <div class="form-group col-2">
                    <label>Branch</label>
                    <select name="branch" id="branchid" class="select2" required>
                      <option value="">Choose Branch</option>
                      @foreach ($branches as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('branch'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  {{-- Hostel --}}
                  <div class="form-group col-3">
                    <label>Hostel</label>
                    <select name="hostel" id="hostel" class="select2" required>
                      <option value="">Select Hostel</option>
                      @foreach ($hostels as $row)
                      <option value="{{ $row->id }}" @selected($row->id == request('hostel'))>{{ $row->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  {{-- Rooms --}}
                  <div class="form-group col-2">
                    <label>Rooms</label>
                    <select name="room" id="room" class="select2" required>
                      <option value="">Select Room</option>
                      <option value="all" @selected(request('room') == 'all')>All Rooms</option>
                      @foreach ($room as $row)
                      <option value="{{ $row }}" @selected($row == request('room'))>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  {{-- Student --}}
                  <div class="form-group col-3">
                    <label>Student</label>
                    <select name="student_id" id="student_id" class="select2">
                      <option value="">All Students</option>
                      @foreach ($students as $row)
                      <option value="{{ $row->student_id }}" @selected($row->student_id == request('student_id'))>
                        {{ $row->student_id }} - {{ $row->student_name }}
                      </option>
                      @endforeach
                    </select>
                  </div>

                  {{-- From Date --}}
                  <div class="form-group col-2">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ request('from_date') }}">
                  </div>

                  {{-- To Date --}}
                  <div class="form-group col-2">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ request('to_date') }}">
                  </div>

                  {{-- Submit Button --}}
                  <div class="form-group col-lg-2">
                    <label>&nbsp;</label>
                    <button type="submit" name="show" class="btn m-t-25 btn-primary">Submit</button>
                  </div>

                </div>
              </form>

            @if(request()->has('show'))
              <div class="row m-t-20">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table class="table table-striped" style="width:100%;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Student ID</th>
                          <th>Student Name</th>
                          <th>Campus</th>
                          <th>Course</th>
                          <th>Section</th>
                          <th>Phone Card</th>
                          <th>Sick Room</th>
                          <th>Total Expense</th>
                          <th>Available Balance</th>
                          <th>Deposit</th>
                          
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($students as $key => $row)
                          @php
                              $phoneCardTotal  = $expenseData[$row->student_id]['phone_card_total'] ?? 0;
                              $sickRoomTotal   = $expenseData[$row->student_id]['sick_room_total'] ?? 0;
                              $totalExpense    = $expenseData[$row->student_id]['total_expense'] ?? 0;
                              $totalDeposit    = ($row->deposit) + $phoneCardTotal + $sickRoomTotal;
                          @endphp
                          <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->student_id }}</td>
                            <td>{{ $row->student_name }}</td>
                            <td>{{ $row->branch->name ?? '' }}</td>
                            <td>{{ $row->course }}</td>
                            <td>{{ $row->section }}</td>
                            <td>₹ {{ number_format($phoneCardTotal, 2) }}</td>
                            <td>₹ {{ number_format($sickRoomTotal, 2) }}</td>
                            <td>₹ {{ number_format($totalExpense, 2) }}</td>
                            <td>₹ {{ $row->deposit }}</td>
                            <td>₹ {{ number_format($totalDeposit, 2) }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            @endif
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/buttons.html5.min.js') }}"></script>

<script>
  $('.table').DataTable({
    dom: 'Bfrtip',
    buttons: ['csv', 'excel'],
    searching: false,
    paging: false,
  });

  const goToMenu = (params = {}) => {
    const query = new URLSearchParams(params).toString();
    window.location = `{{ route('report.studentexpense') }}?${query}`;
  };

  $("#branchid").change(function() {
    if(!this.value) return;
    goToMenu({
      branch: this.value,
    });
  });

  $("#hostel").change(function() {
    if(!this.value) return;
    goToMenu({
      branch: $("#branchid").val(),
      hostel: this.value,
    });
  });
</script>
@endsection