@extends('layouts.app')
@section('title', 'announcement')
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
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{route('announcement.update', $announcement->id)}}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="col-deep-purple">Update Announcement</h6>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class="form-control form-control-sm" required>
                                    @foreach ($academicyear as $row)
                                    <option value="{{ $row->academic_year }}" {{ $announcement->academic_year == $row->academic_year ? 'selected' : '' }}>
                                        {{ $row->academic_year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                         
                            <div class="form-group col-lg-4">
                                <label for="branch">Branch</label>
                                <select name="branch[]" id="branch" class="select2 form-control" multiple required>
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ in_array($branch->id, explode(',', $announcement->branch)) ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group col-lg-4">
                                <label>Coaching Type</label>
                                <select name="coaching_type[]" id="coaching_type" class="select2 form-control" multiple required>
                                     @foreach ($coachingtype as $row)
                                    <option value="{{$row}}" @selected(in_array($row, explode(',', $announcement->coaching_type)))>{{$row}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-lg-3" id="categoryDiv" style="display: none;">
                                <label>Category</label>
                                <select name="category" id="category" class="form-control form-control-sm">
                                    <option value="All" {{ $announcement->category == 'All' ? 'selected' : '' }}>All Types</option>
                                    <option value="Hostel" {{ $announcement->category == 'Hostel' ? 'selected' : '' }}>Hostel</option>
                                    <option value="Day Scholar" {{ $announcement->category == 'Day Scholar' ? 'selected' : '' }}>Day Scholar</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-3">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control form-control-sm" required>
                                    @foreach (['All' => 'All Gender', 'Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" {{ $announcement->gender == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" required value="{{ old('title', $announcement->title) }}">
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="content">Content</label>
                                <textarea name="content" id="content" class="summernote-simple">{{ old('content', $announcement->content) }}</textarea>
                            </div>

                            <div class="form-group col-lg-4">
                                <label for="attachment">Attachment</label>
                                <input type="file" name="attachment" id="attachment" class="form-control form-control-sm">
                                @if (!empty($announcement->attachment))
                                    <p class="mt-2">Current File: {{ basename($announcement->attachment) }}</p>
                                @endif
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
<script>
    $(document).ready(function () {
        $('.select2').select2();

        const selectedCoachingTypes = @json($selectedCoachingTypes);

        function toggleCategoryField(selected) {
            if (selected.includes('Offline')) {
                $('#categoryDiv').show();
                $('#category').prop('disabled', false);
            } else {
                $('#categoryDiv').hide();
                $('#category').prop('disabled', true);
            }
        }

        // Run on page load
        toggleCategoryField(selectedCoachingTypes);

        // Run on change
        $('#coaching_type').on('change', function () {
            toggleCategoryField($(this).val());
        });
    });
</script>
@endsection
