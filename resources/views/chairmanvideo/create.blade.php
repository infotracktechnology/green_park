@extends('layouts.app')

@section('title', ' Chairman Video')
@section('css')
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #6777ef; 
        color: #fff; 
        border: none; 
        padding: 5px 10px; 
        margin: 5px 5px 0 0; 
        border-radius: 3px; 
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        display: none; 
    }

    .select2-container--default .select2-selection--single {
        border-color: #6777ef;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6777ef transparent transparent transparent;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6777ef;
        color: #fff;
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #6777ef;
        min-height: 38px;
        padding: 0;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 0 5px;
    }
</style>
@endsection
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('chairmanvideo.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Add Video</h6>
                             </div>
                             
                             <div class="form-group col-lg-3">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                    <option value="">Select Academic Year</option>
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
                                    <option value="Offline">Offline</option>
                                    <option value="Online Recorded">Online Recorded</option>
                                    <option value="Online Live">Online Live</option>
                                    <option value="Test Series">Test Series</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                             <div class="form-group col-lg-3">
                                <label>Gender</label>
                                <select name="gender" id="gender" class="form-control form-control-sm" required>
                                   <option value="Male,Female">All</option>
                                   <option value="Male">Male</option>
                                   <option value="Female">Female</option>
                                </select>
                             </div>
                             <div class="form-group col-lg-3">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="link">Video ID</label>
                                <input type="number" name="video_id" id="video_id" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="attachment">Attachment</label>
                                <input type="file" name="attachment" id="attachment" class="form-control form-control-sm" onchange="saveAs('assets/attachments', this)">
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
