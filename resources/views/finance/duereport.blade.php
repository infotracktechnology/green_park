@extends('layouts.app')
@section('title', 'Due Report')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')

<div class="main-content">
  <section class="section">
    <div class="card card-primary shadow-sm mb-4">
      @if(session()->has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
              </div>
           @endif
           @if(session()->has('error'))
              <div class="alert alert-error alert-dismissible fade show" role="alert">
                  {{ session('error') }}
              </div>
           @endif
           <div class="card-header">
            <h4>Due Report</h4>
           </div>
      <div class="card-body p-3">
        {{-- Filters Summary --}}
            {{-- <div class="alert alert-info py-2">
                <strong>Filters Applied:</strong><br>
                Academic Year: {{ $academicYear ?? 'All' }},
                Branch: {{ $branchId ?? 'All' }},
                Course: {{ $course ?? 'All' }},
                Batch: {{ $batch ?? 'All' }},
                Section: {{ $section ?? 'All' }},
                Student: {{ $studentId ?? 'All' }},
                Fee Type: {{ $feeType ?? 'All' }},
                Instalment: {{ $instalment ?? 'All' }}
            </div> --}}
            <div class="row">
                <div class="col-12">
                              <div class="col-12 mb-3">
            <form method="GET" action="{{route('fees.report.due')}}" id="searchform">
              <div class="row">
                <div class="col-md-3 mb-2">
                  <label class="form-label">Academic Year</label>
                  <select name="academic_year" class="form-control form-control-sm" required>
                    <option value="">Select Academic Year</option>
                    @foreach ($academicYearselect as $academic_year)
                    <option value="{{$academic_year}}" @selected(request('academic_year') == $academic_year)>{{$academic_year}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-2">
                  <label class="form-label">Branch</label>
                  <select name="branch_id" class="form-control form-control-sm" required>
                    @if(!auth()->user()->branch)
                    <option value="all" @selected(request('branch_id') == 'all')>All</option>
                    @endif
                    @foreach ($branchselect as $id => $branch)
                    <option value="{{$id}}" @selected(request('branch_id') == $id)>{{$branch}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mb-2">
                  <label class="form-label">Report Type</label>
                  <select name="report_type" class="form-control form-control-sm" required>
                    <option value="">Select Report</option>
                    {{-- <option value="simple" @selected(request('report_type') == 'simple')>Simple</option> --}}
                    <option value="summary" @selected(request('report_type') == 'summary')>Summary</option>
                    <option value="detail" @selected(request('report_type') == 'detail')>Detail</option>
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary mt-4">Get</button>
                </div>
              </div>
            </form>
          </div>
                </div>
                @if(isset($report) && count($report))
                <div class="col-12">

            <div class="table-responsive">
                <table id="dueReportTable" class="table table-bordered table-striped table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Section</th>
                            <th class="text-end">Total Fee</th>
                            <th class="text-end">Collected Fee</th>
                            <th class="text-end text-danger">Due Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $totalFeeSum = 0;
                            $totalCollectedSum = 0;
                            $totalDueSum = 0;
                        @endphp

                        @foreach($report as $i => $row)
                            @php
                                $totalFeeSum += $row['total_fee'];
                                $totalCollectedSum += $row['collected_fee'];
                                $totalDueSum += $row['due'];
                            @endphp

                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $row['student_id'] }}</td>
                                <td>{{ $row['student_name'] }}</td>
                                <td>{{ $row['branch'] }}</td>
                                <td>{{ $row['course'] }}</td>
                                <td>{{ $row['batch'] }}</td>
                                <td>{{ $row['section'] }}</td>

                                <td class="text-end">{{ number_format($row['total_fee'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['collected_fee'], 2) }}</td>

                                <td class="text-end text-danger fw-bold">
                                    {{ number_format($row['due'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Grand Total</td>

                            <td class="text-end">{{ number_format($totalFeeSum, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCollectedSum, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totalDueSum, 2) }}</td>
                        </tr>
                    </tfoot>

                </table>
                </div>
            </div>
            @elseif(isset($report) && count($report) == 0)
            <div class="col-12">
                <div class="alert alert-info mt-4">No records found!</div>
            </div>
            @endif
            @if(isset($summary) && count($summary))
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm" id="summaryTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Fee Type</th>
                        <th class="text-end">Total Fee</th>
                        <th class="text-end">Collected</th>
                        <th class="text-end text-danger">Due</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $grandFee = 0;
                        $grandCollected = 0;
                        $grandDue = 0;
                    @endphp

                    @foreach($summary as $i => $row)
                        @php
                            $grandFee += $row['total_fee'];
                            $grandCollected += $row['collected'];
                            $grandDue += $row['due'];
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row['fee_type'] }}</td>
                            <td class="text-end">{{ number_format($row['total_fee'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['collected'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row['due'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">Grand Total</td>
                        <td class="text-end">{{ number_format($grandFee, 2) }}</td>
                        <td class="text-end">{{ number_format($grandCollected, 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($grandDue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
              </div>
            </div>
            @elseif(isset($summary) && count($summary) == 0)
            <div class="col-12">
                <div class="alert alert-info mt-4">No records found!</div>
            </div>
            @endif

            @if(isset($detailed) && count($detailed))
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm" id="detailTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Instalment</th>
                        <th>Due Date</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Collected</th>
                        <th class="text-end text-danger">Due</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($detailed as $i => $d)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $d['student_id'] }}</td>
                            <td>{{ $d['student_name'] }}</td>
                            <td>{{ $d['fee_type'] }}</td>
                            <td>{{ $d['instalment'] }}</td>
                            <td>{{ $d['due_date'] }}</td>

                            <td class="text-end">{{ number_format($d['amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($d['collected'], 2) }}</td>

                            <td class="text-end text-danger fw-bold">
                                {{ number_format($d['due'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
              </div>
            </div>
            @elseif(isset($detailed) && count($detailed) == 0)
            <div class="col-12">
                <div class="alert alert-info mt-4">No records found!</div>
            </div>
            @endif
            </div>

      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/pdfmake.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/vfs_fonts.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.html5.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.print.min.js')}}"></script>
<script src="{{asset('js/page/datatables.js')}}"></script>
<script>
@if(isset($report) && count($report))
  $(document).ready(function() {
    $('#dueReportTable').DataTable({
      ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', footer: true },
            { extend: 'csv', footer: true },
            { extend: 'excel', footer: true },
            { extend: 'pdf', footer: true },
            { extend: 'print', footer: true }
        ]
    });
  });
@endif

@if(isset($summary) && count($summary))
  $(document).ready(function() {
    $('#summaryTable').DataTable({
      ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', footer: true },
            { extend: 'csv', footer: true },
            { extend: 'excel', footer: true },
            { extend: 'pdf', footer: true },
            { extend: 'print', footer: true }
        ]
    });
  });
@endif
</script>
@endsection