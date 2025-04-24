@extends('layouts.app')

@section('title', 'Revision Video')


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
                        <form method="post" id="myForm" action="{{ route('revisionvideo.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Revision Video</h6>
                                    </div>

                                    <!-- File -->

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <a href="{{ env('APP_URL').'template/revisionvideo.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Revision Video Upload Template (Format)</a>
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
                                   <div class="form-group col-lg-3">
                                      <label>upload File</label>
                                      <input type="file" name="file" class="form-control form-control-sm" required>
                                   </div>
                                         
                                    

                                    <!-- End Date -->
                                    <div class="form-group col-lg-3">
                                        <label>Expiry Datetime</label>
                                        <input type="text" id="expire_at" name="expire_at" class="datetime-picker form-control form-control-sm" required>
                                        <div id="end_at_error" class="text-danger"></div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group col-lg-3">
                                        <button type="submit" class="btn btn-primary m-t-25">Upload Video</button>
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

  
</script>
@endsection
