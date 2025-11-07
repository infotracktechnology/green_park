@extends('layouts.dashboard')
@section('title', 'Mark Details')
@section('css')
<style>
  thead th{
    background-color: #56ade8 !important;
     color: #fff !important;
  }
  table th,table td {
  border: 1px solid #222 !important;
  height: 32px !important;
  }
</style>
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">

      <div class="row m-b-10">
      <div class="col-md-2 offset-md-10">
            <a href="{{ route('student.marksheet') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back</a>
          </div>
      </div>

        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Mark Details - Subject Wise</h4>
                  </div>
                  <div class="card-body">
                    <table class="table">
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
                        @foreach($subjects as $row)
                        <tr>
                            <td>{{ $row->subject }}</td>
                            <td>{{ $row->r }}</td>
                            <td>{{ $row->w }}</td>
                            <td>{{ $row->l }}</td>
                            <td>{{ $row->tot }} / {{ $row->total }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                            <th colspan="3"></th>
                            <th>Total Mark</th>
                            <th>{{ $subjects->sum('tot') }} / {{ $subjects->sum('total') }}</th>
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