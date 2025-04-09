@extends('layouts.app')
@section('title', ' NEET Achievement')
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

                    @error('video')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror
                    
                    @error('images.*')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="card card-primary" x-data="app">
                        <form method="post" id="myForm" action="{{ route('achievement.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-12 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Add Achievement</h6>
                                    </div>

                                    <div class="form-group col-lg-4">
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
                                        <select name="branch[]" id="branch" class="select2 form-control" multiple="multiple" required>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-4">
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

                                <div class="form-group col-lg-4">
    <label>Category</label>
    <select name="category[]" id="category" class="select2 form-control form-control-sm" multiple="multiple" required>
        <option value="Video">Video</option>
        <option value="Image">Image</option>
        <option value="Link">Link</option>
    </select>
</div>

<!-- Video Input -->
<div class="form-group col-lg-4" id="video-input" style="display: none;">
    <label for="video">Upload Video <span class="text-danger">(max size: 40MB*)</span></label>
    <input type="file" name="video" id="video" class="form-control form-control-sm" accept="video/*">
    @error('video')
    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>
@enderror
</div>

<!-- Image Input -->
<div class="form-group col-lg-4" id="image-input" style="display: none;">
    <label for="images">Upload Images <span class="text-danger">(max size: 2MB*)</span></label>
    <input type="file" name="images[]" id="images" class="form-control form-control-sm" accept="image/*" multiple>
    @error('images.*')
    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>
@enderror
</div>

<!-- Link Input -->
<div class="form-group col-lg-4" id="link-input" style="display: none;">
    <label for="link">Enter Link</label>
    <input type="url" name="link" id="link" class="form-control form-control-sm">
</div>









                                    <div class="form-group col-lg-12">
                                        <label for="content">Content</label>
                                        <textarea name="content" id="content" class="summernote-simple"></textarea>
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

@section('js')
<script src="{{asset('bundles/summernote/summernote-bs4.js')}}"></script>
<script src="{{asset('bundles/select2/dist/js/select2.full.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<script>
    document.getElementById('myForm').addEventListener('submit', function (e) {
    const videoInput = document.getElementById('video');
    if (videoInput.files[0] && videoInput.files[0].size > 40 * 1024 * 1024) { // 40MB
        e.preventDefault();
        alert('The uploaded video exceeds the maximum allowed size of 40MB.');
    }
});
</script>
<script>

    $(document).ready(function() {
        $('.select2').select2();

        $('#category').on('change', function() {
            const selectedOptions = $(this).val();

            // Show or hide inputs based on selected options
            $('#video-input').toggle(selectedOptions.includes('Video'));
            $('#image-input').toggle(selectedOptions.includes('Image'));
            $('#link-input').toggle(selectedOptions.includes('Link'));
        });
    });
</script>
@endsection