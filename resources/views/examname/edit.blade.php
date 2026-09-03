@extends('layouts.app')

@section('title', 'Edit Exam Name')

@section('main')

<div class="main-content">
   <section class="section">
      <div class="section-body">

         <div class="row">
            <div class="col-12">

               @if(session('success'))
                  <div class="alert alert-success alert-dismissible show fade">
                     {{ session('success') }}
                  </div>
               @endif

               <div class="card card-primary">

                  <form method="post"
                        id="myForm"
                        action="{{ route('examname.update', ['examname' => $examname->id]) }}">

                     @csrf
                     @method('PUT')

                     <div class="card-body">

                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Edit Test</h6>
                  </div>


                  <div class="form-group col-lg-3">
                    <label for="branch">Select Academic Year:</label>
                    <select name="academic_year" id="academic_year" class="form-control form-control-sm">
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" @selected($row->academic_year == $examname->academic_year)>{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>User Type</label>
                    <select name="usertype" id="usertype" class="form-control form-control-sm" required>
                      <option value="GROUP" @selected($examname->usertype == 'GROUP')>GROUP</option>
                      <option value="INDIVIDUAL" @selected($examname->usertype == 'INDIVIDUAL')>INDIVIDUAL STUDENT</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==$examname->course)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch">Branch</label>
                    <select name="branch[]" id="branch" class="select2" multiple required>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$examname->branch)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type[]" id="coaching_type" class="select2" multiple required>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $examname->coaching_type)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>H/D</label>
                    <select name="category[]" id="category" class="select2" multiple>
                      @foreach ($hostel as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $examname->category)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Batch</label>
                    <select name="batch[]" id="batch" class="select2" multiple>
                      @foreach ($batch as $row)
                      <option value="{{$row}}" @selected(in_array($row, explode(',', $examname->batch)))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="All" @selected($examname->gender == 'All') >All Gender</option>
                      <option value="MALE" @selected($examname->gender == 'MALE')>MALE</option>
                      <option value="FEMALE" @selected($examname->gender == 'FEMALE')>FEMALE</option>
                    </select>
                  </div>


                  <div class="form-group col-lg-2">
                    <label>Section</label>
                    <select name="section" id="section" class="form-control form-control-sm">
                      <option value="">Select Section</option>
                      <option value="{{ implode(',', $section)}}" @selected(implode(',', $section)==$examname->section)>All</option>
                      @foreach ($section as $row)
                      <option value="{{$row}}" @selected($row==$examname->section)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-4">
                    <label>Students</label>
                    <select name="students" id="students" class="form-control form-control-sm select2" required>
                      @foreach ($students as $k => $row)
                      <option value="{{$k}}" @selected($k==$examname->students)>{{$k}} - {{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Test Category</label>
                    <select name="testcategory" class="form-control form-control-sm" required>
                      <option value="">Select Test Category</option>
                      @foreach ($testcategory as $row)
                      <option value="{{$row}}" @selected($row==$examname->testcategory)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Test ID <span class="text-danger">(should be unique*)</span></label>
                    <input type="number" name="id" value="{{ $examname->testid }}" id="id" class="form-control form-control-sm numberk" disabled>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Test Name</label>
                    <input type="text" name="name" value="{{ $examname->name }}" id="name" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-2">
                    <label>Exam Date</label>
                    <input type="date" value="{{ $examname->exam_date }}" name="exam_date" class="form-control form-control-sm">
                  </div>


                  {{-- <div class="form-group col-lg-4">
                    <label>Start Datetime</label>
                    <input type="text" id="start_at" value="{{ $examname->start_at }}" name="start_at" class="datetime-picker form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-4">
                    <label>End Datetime</label>
                    <input type="text" id="end_at" value="{{ $examname->end_at }}" name="end_at" class="datetime-picker form-control form-control-sm">
                    <div id="end_at_error" class="text-danger"></div>
                  </div> --}}


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