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
                        <form method="post" id="myForm" action="{{ route('classvideo.upload.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Class Video Upload</h6>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <a href="{{ env('APP_URL').'template/classvideoupload.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Class Video Upload Template (Format)</a>
                                       </div>



                                       {{-- <div class="form-group col-lg-3">
                                        <label for="academic_year">Academic Year</label>
                                        <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                            <option value="">Select Academic Year</option>
                                            @foreach ($academicyear as $row)
                                                <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                                            @endforeach
                                        </select>
                                    </div>  --}}
                                   <!-- Test ID Dropdown -->
                                   <div class="form-group col-lg-3">
                                      <label>upload File</label>
                                      <input type="file" name="file" class="form-control form-control-sm" required>
                                   </div>
                                         
                                   
                                   
                                   <div class="form-group col-lg-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Upload</button>
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

