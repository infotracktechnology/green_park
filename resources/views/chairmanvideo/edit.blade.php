@extends('layouts.app')

@section('title', 'Edit Chairman Video')
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
                     <form method="post" id="myForm" action="{{ route('chairmanvideo.update', $chairmanvideo->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                           <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Edit Video</h6>
                             </div>
                             

                             <div class="form-group col-lg-3">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                    {{-- <option value="">Select Academic Year</option> --}}
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" {{ $chairmanvideo->academic_year == $row->academic_year ? 'selected' : '' }}>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                            </div>




                             <div class="form-group col-lg-4">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id[]" id="branch_id" class="select2 form-control " multiple="multiple" required>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ in_array($branch->id, explode(',', $chairmanvideo->branch_id)) ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Coaching Type</label>
                                <select name="coaching_type[]" class="form-control form-control-sm select2" multiple required>
                                    @foreach(['Offline', 'Online Recorded', 'Online Live', 'Test Series', '11 to XI - OB','12 TO XII - OB'] as $type)
                                        <option value="{{ $type }}" {{ in_array($type, explode(',', $chairmanvideo->coaching_type)) ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="form-group col-lg-3">
                                <label>Gender</label>
                                <select name="gender" id="gender" class="form-control form-control-sm" required>
                                   <option value="Male,Female" {{ $chairmanvideo->gender == 'Male,Female' ? 'selected' : '' }}>All</option>
                                   <option value="Male" {{ $chairmanvideo->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                   <option value="Female" {{ $chairmanvideo->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                             </div>
                             <div class="form-group col-lg-3">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" value="{{ $chairmanvideo->title }}" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="video_id">Video ID</label>
                                <input type="number" name="video_id" id="video_id" class="form-control form-control-sm" value="{{ $chairmanvideo->video_id }}" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="attachment">Attachment</label>
                                <input type="file" name="attachment" id="attachment" class="form-control form-control-sm">
                                <div class="mt-2">
                                @if(isset($chairmanvideo->attachment))
                                    <a href="/public/{{ $chairmanvideo->attachment }}" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-paperclip"></i> Attachment
                                    </a>
                                @endif
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <button type="submit" class="btn btn-primary">Update</button>
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
