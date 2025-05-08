@extends('layouts.app')
@section('title', 'worksheets')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/summernote/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{asset('bundles/select2/dist/css/select2.min.css')}}">

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
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
            @endif
            
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible show fade">{{ session('error') }}</div>
            @endif
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('worksheet.store') }}" enctype="multipart/form-data">
                        @csrf 
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple"> Worksheet</h6>
                              </div>
                              
                              <div class="form-group col-lg-3">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                    {{-- <option value="">Select Academic Year</option> --}}
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                            </div> 




                              <div class="form-group col-lg-4">
                                <label for="branch">Branch</label>
                                <select name="branch[]" class="form-control form-control-sm select2 @error('branch') is-invalid @enderror" multiple required>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            

        <div class="form-group col-lg-5">
            <label>Coaching Type</label>
            <select name="coaching_type[]" class="form-control form-control-sm select2" multiple required>
                <option value="Offline">Offline</option>
                <option value="Online Recorded">Online Recorded</option>
                <option value="Online Live">Online Live</option>
                <option value="Test Series">Test Series</option>
                <option value="11 to XI - OB">11 to XI - OB</option>
                <option value="12 TO XII - OB">12 TO XII - OB</option>
            </select>
        </div>

        <div class="form-group col-lg-3">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control form-control-sm" required>
        </div>
        
        <div class="form-group col-lg-4">
            <label>Attachment <span class="text-danger">(Only PDF files, max size: 2MB*)</span></label>
           
            <input type="file" name="file" class="form-control form-control-sm" required>
        </div>
        
 <div class="form-group col-lg-12">
        <button type="submit" class="btn btn-primary">Submit</button>
 </div>

    </form>

                        
                        </div>
                     </form>
                  </div>
              </div>
          </div>
      </div>
   </section>
</div>
@endsection

@section('js')

@endsection
