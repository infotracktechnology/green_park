@extends('layouts.app')

@section('title', ' Exam Portion')
@section('css')

@endsection
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary" x-data="app">
            <form method="post" id="myForm" action="{{ route('examportion.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Add PDF</h6>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-4">
                    <label for="branch_id">Branch</label>
                    <select name="branch_id[]" id="branch_id" class="select2 form-control " multiple="multiple" required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-5">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2 form-control form-control-sm" multiple="multiple" required>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-4">
                    <label for="attachment">Attachment <span class="text-danger">*PDF only</span></label>
                    <input type="file" name="attachment" id="attachment" class="form-control form-control-sm" required accept=".pdf">
                  </div>
                  
                  <div class="form-group col-lg-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection