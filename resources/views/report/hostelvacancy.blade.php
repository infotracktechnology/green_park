@extends('layouts.app')
@section('title', 'Hostel Vacancy Report')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Hostel Vacancy Report</h4>
                </div>

                <div class="card-body">

                    <form method="GET" action="{{ route('report.hostelvacancy') }}">
                        <div class="row">

                            <div class="form-group col-md-3">
                                <label>Branch</label>
                                <select name="branch" id="branchid" class="select2" required>
                                    <option value="">Choose Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            @selected(request('branch') == $branch->id)>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Hostel</label>
                                <select name="hostel" id="hostel" class="select2" required>
                                    <option value="">Choose Hostel</option>
                                    @foreach($hostels as $hostel)
                                        <option value="{{ $hostel->id }}"
                                            @selected(request('hostel') == $hostel->id)>
                                            {{ $hostel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Room No</label>
                                <select name="room" id="room" class="select2">
                                    <option value="">Choose Room</option>
                                    <option value="all" @selected(request('room') == 'all')>All Rooms</option>
                                    @foreach($room as $roomNo)
                                        <option value="{{ $roomNo }}"
                                            @selected(request('room') == $roomNo)>
                                            {{ $roomNo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block ">
                                    Search
                                </button>
                            </div>

                        </div>
                    </form>

                    <hr>

                      <div class="table-responsive">
                          <table class="table table-striped table-bordered" id="tableExport">
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Hostel Name</th>
                                      <th>Room No</th>
                                      <th>Cot Type </th>
                                      <th>Total Cots </th>
                                      <th>Occupied Beds</th>
                                      <th>Available Vacancies</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach($vacancy_log as $key => $row)
                                      <tr>
                                          <td>{{ $key + 1 }}</td>
                                          <td>{{ $row->hostel_name }}</td>
                                          <td>{{ $row->room_no }}</td>
                                          <td>{{ $row->room_type }}</td>
                                          <td>{{ $row->capacity }}</td>
                                          <td>{{ $row->occupied }}</td>
                                          <td>{{ $row->vacancy }}</td>
                                      </tr>
                                  @endforeach
                              </tbody>
                          </table>
                      </div>

                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script>
$(function () {
    $('#tableExport').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel'],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "Please select Branch & Hostel and click Search."
        }
    });

    function goToMenu(params) {
        window.location = "{{ route('report.hostelvacancy') }}?" + $.param(params);
    }

    $('#branchid').change(function () {
        if (this.value) {
            goToMenu({ branch: this.value });
        } else {
            window.location = "{{ route('report.hostelvacancy') }}";
        }
    });

    $('#hostel').change(function () {
        if (this.value) {
            goToMenu({
                branch: $('#branchid').val(),
                hostel: this.value
            });
        } else {
            goToMenu({ branch: $('#branchid').val() });
        }
    });
});
</script>
@endsection