@extends('layouts.app')

@section('title', 'Staff Announcement')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card card-primary">

                        <form method="post" id="myForm" action="{{ route('staffannouncement.update', $announcement->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                            <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <h6 class="col-deep-purple"> Update Staff Announcement </h6>
                                    </div>
                                    {{-- User Type --}}
                                    <div class="form-group col-lg-3">
                                        <label>User Type</label>

                                        <select name="usertype"
                                                id="usertype"
                                                class="form-control form-control-sm"
                                                required>

                                            <option value="GROUP"
                                                @selected($announcement->usertype == 'GROUP')>
                                                GROUP
                                            </option>

                                            <option value="INDIVIDUAL"
                                                @selected($announcement->usertype == 'INDIVIDUAL')>
                                                INDIVIDUAL
                                            </option>

                                        </select>
                                    </div>

                                    {{-- Department --}}
                                    <div class="form-group col-lg-3">
                                        <label>Department</label>

                                        <select name="department[]"
                                                id="department"
                                                class="select2"
                                                multiple
                                                required>

                                            <option value="All"
                                                @selected(in_array('All', explode(',', $announcement->department ?? '')))>
                                                All Department
                                            </option>

                                            @foreach ($department as $row)

                                                <option value="{{ $row }}"
                                                    @selected(in_array($row, explode(',', $announcement->department ?? '')))>
                                                    {{ $row }}
                                                </option>

                                            @endforeach

                                        </select>
                                    </div>

                                    {{-- Branch --}}
                                    <div class="form-group col-lg-3">
                                        <label>Branch</label>

                                        <select name="branch[]"
                                                id="branch"
                                                class="select2"
                                                multiple
                                                required>

                                            @foreach ($branches as $branch)

                                                <option value="{{ $branch->id }}"
                                                    @selected(in_array(
                                                        $branch->id,
                                                        explode(',', $announcement->branch ?? '')
                                                    ))>

                                                    {{ $branch->name }}

                                                </option>

                                            @endforeach

                                        </select>
                                    </div>

                                    {{-- Title --}}
                                    <div class="form-group col-lg-3">
                                        <label for="title">Title</label>

                                        <input type="text"
                                               name="title"
                                               id="title"
                                               class="form-control form-control-sm"
                                               value="{{ $announcement->title }}"
                                               required>
                                    </div>

                                    {{-- Attachment --}}
                                    <div class="form-group col-lg-4">
                                        <label for="attachment">Attachment</label>

                                        <input type="file"
                                               name="attachment[]"
                                               id="attachment"
                                               class="form-control form-control-sm"
                                               multiple>

                                        @if (!empty($announcement->attachment))

                                            <div class="mt-2"
                                                 id="existing_attachments_container">

                                                <strong>Current Attachments:</strong>

                                                @foreach($announcement->attachment as $idx => $file)

                                                    <div class="d-flex align-items-center justify-content-between bg-light rounded px-2 py-1 my-1"
                                                         id="existing_file_{{ $idx }}">

                                                        <input type="hidden"
                                                               name="existing_attachment[]"
                                                               value="{{ $file }}">

                                                        <a href="{{ url($file) }}"
                                                           target="_blank"
                                                           class="text-truncate mr-2"
                                                           style="max-width: 180px;">

                                                            <i class="fas fa-paperclip"></i>
                                                            {{ basename($file) }}

                                                        </a>

                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger py-0 px-1"
                                                                onclick="document.getElementById('existing_file_{{ $idx }}').remove();">

                                                            &times;

                                                        </button>

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif

                                    </div>

                                    {{-- Schedule --}}
                                    <div class="form-group col-lg-12">

                                        <div class="custom-control custom-checkbox">

                                            <input type="checkbox"
                                                   name="is_schedule"
                                                   class="custom-control-input"
                                                   id="is_schedule"
                                                   value="1"
                                                   @checked($announcement->is_schedule)>

                                            <label class="custom-control-label"
                                                   for="is_schedule">

                                                Is Schedule

                                            </label>

                                        </div>

                                    </div>

                                    <div class="col-lg-12 row"
                                         id="schedule_fields"
                                         style="{{ $announcement->is_schedule ? '' : 'display: none;' }}">

                                        <div class="form-group col-lg-3">

                                            <label>Start Datetime</label>

                                            <input type="text"
                                                   id="start_at"
                                                   name="start_at"
                                                   class="datetime-picker form-control form-control-sm"
                                                   value="{{ $announcement->start_at }}"
                                                   {{ $announcement->is_schedule ? 'required' : '' }}>

                                        </div>

                                    </div>

                                    {{-- Content --}}
                                    <div class="form-group col-lg-12">

                                        <label for="content">Content</label>

                                        <textarea name="content"
                                                  class="form-control form-control-sm"
                                                  id="content">{{ $announcement->content }}</textarea>

                                    </div>

                                    {{-- Submit --}}
                                    <div class="form-group col-lg-12">

                                        <button type="submit"
                                                class="btn btn-primary">

                                            Submit

                                        </button>

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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>

<script>

flatpickr(".datetime-picker", {
    enableTime: true,
    allowInput: true,
    dateFormat: "Y-m-d H:i",

    plugins: [
        new confirmDatePlugin({
            confirmText: "OK",
            showAlways: false,
            theme: "light"
        })
    ]
});

$('#is_schedule').change(function () {

    if ($(this).is(':checked')) {

        $('#schedule_fields').show();
        $('#start_at').attr('required', true);

    } else {

        $('#schedule_fields').hide();
        $('#start_at').attr('required', false);

    }

});

</script>

@endsection