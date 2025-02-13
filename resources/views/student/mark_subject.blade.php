@extends('layouts.dashboard')
@section('title', 'Mark Details')
@section('css')
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-md-12 col-lg-12">
            
                <div class="card">
                  <div class="card-header">
                    <h4>Mark Details - Subject Wise</h4>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Right</th>
                            <th>Wrong</th>
                            <th>Left</th>
                            <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($test))
                        <?php
                        $tot=0;
                        $total=0;
                        ?>
                        @foreach($test as $row)
                        <?php
                        $tot+=$row->tot;
                        $total+=$row->total;
                        ?>
                        <tr>
                            <td>{{ $row->subject }}</td>
                            <td>{{ $row->r }}</td>
                            <td>{{ $row->w }}</td>
                            <td>{{ $row->l }}</td>
                            <td>{{ $row->tot }} / {{ $row->total }}</td>
                        </tr>
                        @endforeach
                        @endif
                      </tbody>
                      <tfoot>
                        <tr>
                            <th colspan="3"></th>
                            <th>Total Mark</th>
                            <th>{{ $tot }} / {{ $total }}</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 