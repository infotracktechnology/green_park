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
  thead th {
    height: 40px !important;
    text-align: center;
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
                    <thead class="thead">
                      <tr>
                        <th>Campus</th>
                        <th>HOS / DAY</th>
                        <th colspan="2">Batch - A</th>
                        <th colspan="2">Batch - B</th>
                        <th rowspan="2">TOTAL</th>
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
                    @php
                        $grouped = $report->groupBy('campus');
                    @endphp

                    @foreach($grouped as $campus => $campusData)
                        @php
                          $types = [
                              'DAYSCHOLAR-MALE'   => 'DAY BOYS',
                              'HOSTEL-MALE'       => 'HOSTEL BOYS',
                              'DAYSCHOLAR-FEMALE' => 'DAY GIRLS',
                              'HOSTEL-FEMALE'     => 'HOSTEL GIRLS',
                          ];

                          $groups = [];
                          foreach ($types as $type => $label) {
                              [$hosDay, $gender] = explode('-', $type);
                              $rows = $campusData->where('hostel_dayscholar', $hosDay)->where('gender', $gender);
                              $batchA = $rows->where('batch', 'A')->values();
                              $batchB = $rows->where('batch', 'B')->values();
                              $max = max($batchA->count(), $batchB->count());

                              if ($max > 0) {
                                  $groups[] = ['label'  => $label, 'batchA' => $batchA, 'batchB' => $batchB,'max'    => $max, ];
                              }
                          }

                      $campusRowCount = collect($groups)->sum('max');
                      $campusTotal = $campusData ->whereIn('batch', ['A', 'B'])->sum('strength');
                      $campusPrinted = false;
                      $totalPrinted = false;
                      @endphp

                        @foreach($groups as $group)
                            @for($i = 0; $i < $group['max']; $i++) <tr>
                                    @if(!$campusPrinted)
                                        <td rowspan="{{ $campusRowCount }}"> {{ $campus }} </td>
                                        @php
                                          $campusPrinted = true;
                                        @endphp
                                    @endif
                                    @if($i == 0)
                                        <td rowspan="{{ $group['max'] }}"> {{ $group['label'] }}</td>
                                    @endif
                                    <td> {{ $group['batchA'][$i]->section ?? '-' }}</td>
                                    <td> {{ $group['batchA'][$i]->strength ?? '-' }}</td>
                                    <td> {{ $group['batchB'][$i]->section ?? '-' }}</td>
                                    <td>{{ $group['batchB'][$i]->strength ?? '-' }} </td>

                                    @if(!$totalPrinted)
                                        <td rowspan="{{ $campusRowCount }}"> {{ $campusTotal }}</td>
                                        @php
                                            $totalPrinted = true;
                                        @endphp
                                    @endif
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
                        <th colspan="2">TOTAL </th>
                        <th></th>
                        <th>{{ $totalA }}</th>
                        <th></th>
                        <th>{{ $totalB }}</th>
                        <th>{{ $grand }}</th>
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