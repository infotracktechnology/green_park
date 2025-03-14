@extends('layouts.app')

@section('title', 'Class Video')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible show fade">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible show fade">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card card-primary" x-data="app">
                        <form method="post" id="myForm" action="{{ route('classvideo.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Class Video</h6>
                                    </div>

                                    <!-- Subject -->
                                    <div class="form-group col-lg-4">
                                        <label>Subject</label>
                                        <select name="subject" class="form-control form-control-sm" required>
                                            <option value="">Select Subject</option>
                                            <option value="physics">Physics</option>
                                            <option value="chemistry">Chemistry</option>
                                            <option value="zoology">Zoology</option>
                                            <option value="botany">Botany</option>
                                        </select>
                                    </div>

                                    <!-- Chapter -->
                                    <div class="form-group col-lg-4">
                                        <label>Chapter</label>
                                        <input type="text" name="chapter" class="form-control form-control-sm" required>
                                    </div>

                                    <!-- Period -->
                                    <div class="form-group col-lg-4">
                                        <label>Period</label>
                                        <select name="period" class="form-control form-control-sm" required>
                                            <option value="">Select Period</option>
                                            @for($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}">Period {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Video ID -->
                                    <div class="form-group col-lg-4">
                                        <label for="video">Video ID</label>
                                        <input type="text" name="video_id" class="form-control form-control-sm" required>
                                    </div>

                                    <!-- Video URLs -->
                                    <div class="form-group col-lg-5">
                                        <label for="video_url">Video URLs</label>
                                        <input type="url" name="video_url"  class="form-control form-control-sm" required>
                                    </div>

                                    <!-- Start Date -->
                                    <div class="form-group col-lg-3">
                                        <label>Start Datetime</label>
                                        <input type="text" id="start_at" name="start_at" class="datetime-picker form-control form-control-sm" required>
                                    </div>

                                    <!-- End Date -->
                                    <div class="form-group col-lg-3">
                                        <label>End Datetime</label>
                                        <input type="text" id="end_at" name="end_at" class="datetime-picker form-control form-control-sm" required>
                                        <div id="end_at_error" class="text-danger"></div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group col-lg-12">
                                        <button type="submit" class="btn btn-primary">Add Video</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> <!-- End Card -->
                </div>
            </div>
        </div>
    </section>
</div>
@endsection



@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
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

    $('#end_at').change(function() {
        $('#end_at_error').text('');
        const startTime = new Date($('#start_at').val());
        const endTime = new Date($(this).val());
        if (startTime >= endTime) {
            $('#end_at_error').text('End time must be greater than start time.');
            $(this).val('');
        }
    });
</script>
@endsection
