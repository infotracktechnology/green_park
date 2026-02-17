@extends('layouts.dashboard')

@section('title', 'Mock Test (OMR Sheet)')

@section('main')
<style>
  .omr-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      text-align: center;
  }
  
  .omr-table td, .omr-table th {
      border: 1px solid #dee2e6;
      padding: 6px 4px;
      vertical-align: middle;
  }
  
  .omr-table th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
  }

  .q-num {
      color: #28a745;
      font-weight: bold;
      width: 30px;
  }

  .omr-radio {
      appearance: none;
      width: 15px;
      height: 15px;
      border: 1px solid #adb5bd;
      border-radius: 50%;
      margin: 0 3px;
      cursor: pointer;
      vertical-align: middle;
      transition: all 0.2s ease;
  }
  
  .omr-radio:checked {
      background-color: #000;
      border-color: #000;
      box-shadow: inset 0 0 0 2px #fff;
  }
  
  .omr-radio:hover {
      border-color: #333;
      background-color: #e9ecef;
  }
  
</style>

<div class="main-content">
  <div class="card card-primary">
    <div class="card-header">
      <h4>Mock Test (OMR Sheet)</h4>
    </div>
    <div class="card-body">
    <form action="#" method="POST">
    </form>
    
    <form action="#" method="POST">
      @csrf

      <div class="form-group col-lg-3">
        <select class="select2" id="exam_name" name="exam_name" required>
          <option value="">-- Select Exam Name --</option>
          @foreach ($mocktests as $row)
          <option value="{{$row->exam_name}}">{{$row->exam_name}}</option>
          @endforeach
        </select>
      </div>


      <div class="table-responsive">
        <table class="omr-table">
          <thead>
            <tr>
              @for($i = 0; $i < 4; $i++) 
                <th class="q-num">Q.</th>
                <th>1 &nbsp; 2 &nbsp; 3 &nbsp; 4</th>
                @endfor
            </tr>
          </thead>
        </table>
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">Final Submit</button>
      </div>
    </form>
    </div>
  </div>
</div>
@endsection
@section('js')
@endsection