@extends('layouts.app')
@section('title', 'Edit NEET Achievement')
@section('css')

<link rel="stylesheet" href="{{asset('bundles/summernote/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{asset('bundles/select2/dist/css/select2.min.css')}}">
@endsection
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
@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <form id="myForm" action="{{ route('achievement.update', $achievement->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card card-primary">
                            <div class="card-header">
                                
                                <h6 class="col-deep-purple">Edit NEET Achievement</h6>
                            </div>
                            <div class="card-body row">

                                <div class="form-group col-lg-4">
                                    <label>Academic Year</label>
                                    <select name="academic_year" class="form-control" required>
                                        @foreach ($academicyear as $row)
                                            <option value="{{ $row->academic_year }}" {{ $achievement->academic_year == $row->academic_year ? 'selected' : '' }}>
                                                {{ $row->academic_year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-4">
                                    <label>Branch</label>
                                    <select name="branch[]" class="select2 form-control" multiple required>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ in_array($branch->id, explode(',', $achievement->branch)) ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-4">
                                    <label>Coaching Type</label>
                                    <select name="coaching_type[]" class="select2 form-control" multiple required>
                                        @foreach (['Offline','Online Recorded','Online Live','Test Series','11','12'] as $type)
                                            <option value="{{ $type }}" {{ in_array($type, explode(',', $achievement->coaching_type)) ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-4">
                                    <label>Category</label>
                                    <select name="category[]" id="category" class="select2 form-control" multiple required>
                                        @foreach (['Video','Image','Link'] as $cat)
                                            <option value="{{ $cat }}" {{ in_array($cat, explode(',', $achievement->category)) ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Video Upload -->
                             
                                <div class="form-group col-lg-4" id="video-input" style="display: none;">
                                    <label>Upload Video <span class="text-danger">(max size: 40MB*)</span></label>
                                    <input type="file" name="video" id="video" class="form-control form-control-sm" accept="video/*">
                                    @if($achievement->video)
                                        <div class="mt-1 text-muted">
                                            Current Video: {{ basename($achievement->video) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Image Upload -->
                                <div class="form-group col-lg-4" id="image-input" style="display: none;">
                                    <label>Upload Images <span class="text-danger">(max size: 2MB*)</span></label>
                                    <input type="file" name="images[]" id="images" class="form-control form-control-sm" accept="image/*" multiple>
                                    @if($achievement->images)
                                        <div class="mt-2 text-muted">
                                            @foreach (json_decode($achievement->images, true) as $img)
                                                <div>Image: {{ basename($img) }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Link -->
                                <div class="form-group col-lg-4" id="link-input" style="display: none;">
                                    <label>Link</label>
                                    <input type="url" name="link" class="form-control form-control-sm" value="{{ $achievement->link }}">
                                </div>

                                <!-- Content -->
                                <div class="form-group col-lg-12">
                                    <label>Content</label>
                                    <textarea name="content" class="summernote-simple">{{ $achievement->content }}</textarea>
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
    </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/summernote/summernote-bs4.js')}}"></script>
<script src="{{ asset('bundles/select2/dist/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<script>
    document.getElementById('myForm').addEventListener('submit', function (e) {
        const videoInput = document.getElementById('video');
        const imageInputs = document.getElementById('images');

        // Validate video size
        if (videoInput.files[0] && videoInput.files[0].size > 40 * 1024 * 1024) { // 40MB
            e.preventDefault();
            alert('The uploaded video exceeds the maximum allowed size of 40MB.');
        }

        // Validate each image size
        if (imageInputs.files) {
            for (let i = 0; i < imageInputs.files.length; i++) {
                if (imageInputs.files[i].size > 2 * 1024 * 1024) { // 2MB
                    e.preventDefault();
                    alert('One or more images exceed the maximum allowed size of 2MB.');
                    break;
                }
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('#category').select2();

        function toggleInputs(selectedOptions) {
            $('#video-input').toggle(selectedOptions.includes('Video'));
            $('#image-input').toggle(selectedOptions.includes('Image'));
            $('#link-input').toggle(selectedOptions.includes('Link'));
        }

        // Trigger after select2 finishes rendering
        setTimeout(function () {
            const selectedOptions = $('#category').val() || [];
            toggleInputs(selectedOptions);
        }, 100);

        $('#category').on('change', function () {
            const selected = $(this).val();
            toggleInputs(selected);
        });
    });
</script>
@endsection