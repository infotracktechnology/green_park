@extends('layouts.app')
@section('title', 'Exam Log Report')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">

    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible show fade">
            <div class="alert-body">
              <button class="close" data-dismiss="alert"><span>&times;</span></button>
              {{ session('success') }}
            </div>
          </div>
          @endif
          @if(session('error'))
              <div class="alert alert-danger">
                  {{ session('error') }}
              </div>
          @endif


          <div class="card card-primary">
            <div class="card-body">
              
              <div class="row mb-3">
                <div class="col-md-12">
                  <h6 class="col-deep-purple">Exam Log Report Search</h6>
                </div>
              </div>

              <form method="POST">
                  @csrf
                  <div class="row">
                      <div class="col-md-3">
                          <label>Student ID</label>
                          <input type="text" name="student_id" class="form-control" required>
                      </div>

                      <div class="col-md-3">
                          <label>Exam</label>
                          <select name="exam_id" class="form-control" required>
                              <option value="">Select</option>                              
                              @foreach($exams as $exam)                              
                              <option value="{{$exam->id}}"> {{$exam->name}} </option>
                              @endforeach
                          </select>
                      </div>

                      <div class="col-md-2 mt-4">
                          <button class="btn btn-primary"> Download Report </button>
                      </div>
                  </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>

  </section>
</div>
@endsection