@extends('layouts.app')
@section('title', 'BatchList Report')

@section('css')
<style>
  thead th{
    background-color: #56ade8 !important;
     color: #222 !important;
  }
  table th,table td {
  border: 1px solid #222 !important;
  height: 0px !important;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4>BatchList Report</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Campus</th>
                        <th>HOS / DAY</th>
                        <th colspan="2">Batch - A</th>
                        <th colspan="2">Batch - B</th>
                      </tr>
                      <tr>
                        <th></th>
                        <th></th>
                        <th>Section</th>
                        <th>Strength</th>
                        <th>Section</th>
                        <th>Strength</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $grouped = $report->groupBy(['campus','hostel_dayscholar']);
                      ?>

                      @foreach($grouped as $campus => $campusData)
                      @foreach($campusData as $hosDay => $rows)
                      @php
                      $batchA = $rows->where('batch','A');
                      $batchB = $rows->where('batch','B');
                      $max = max($batchA->count(), $batchB->count());
                      @endphp

                      <tr>
                        <td rowspan="{{ $max }}">{{ $campus }}</td>
                        <td rowspan="{{ $max }}">{{ $hosDay }}</td>
                        <td>{{ $batchA[0]->section ?? '-' }}</td>
                        <td>{{ $batchA[0]->strength ?? '-' }}</td>
                        <td>{{ $batchB[0]->section ?? '-' }}</td>
                        <td>{{ $batchB[0]->strength ?? '-' }}</td>
                      </tr>

                      @for($i=1;$i<$max;$i++) <tr>
                        <td>{{ $batchA[$i]->section ?? '-' }}</td>
                        <td>{{ $batchA[$i]->strength ?? '-' }}</td>
                        <td>{{ $batchB[$i]->section ?? '-' }}</td>
                        <td>{{ $batchB[$i]->strength ?? '-' }}</td>
                        </tr>
                        @endfor
                        @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                    <?php
                    $totalA = $report->where('batch','A')->sum('strength');
                    $totalB = $report->where('batch','B')->sum('strength');
                    $grand  = $totalA + $totalB;
                    ?>
                      <tr class="bg-secondary fw-bold">
                        <th colspan="2">TOTAL = {{ $grand }}</th>
                        <th></th>
                        <th>{{ $totalA }}</th>
                        <th></th>
                        <th>{{ $totalB }}</th>
                      </tr>
                    </tfoot>
                  </table>

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
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.print.min.js')}}"></script>

<script>
  $(document).ready(function () {
    $("#myTable").DataTable({
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          footer: true,
        }
      ],
      pageLength: 25,
    });
  });
  
</script>
@endsection