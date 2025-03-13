@extends('layouts.dashboard')

@section('title', 'Class Videos')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">

@endsection

@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                        
                <div class="card card-primary">
  
                    <div class="card-body">
                <div class="card-body">
                    <div class="row">
                           <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple">Class Videos</h6>
                  

                            <div class="row align-items-end">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="subject">Subject</label>
                                    <select name="subject" id="subject" class="form-control form-control-sm" required>
                                        <option value="">Select Subject</option>
                                        <option value="physics">Physics</option>
                                        <option value="chemistry">Chemistry</option>
                                        <option value="zoology">Zoology</option>
                                        <option value="botany">Botany</option>
                                    </select>
                                </div>
                            
                                <div class="col-lg-2 col-md-3 col-sm-12 mb-3">
                                    <button type="show" class="btn btn-primary btn-block">Show</button>
                                </div>
                            </div>
                           
                        </div>
                    </div></div>
                </div></div>
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                @for($i = 1; $i <= 6; $i++)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $i == 1 ? 'active' : '' }}" id="period{{ $i }}-tab" data-toggle="tab" href="#period{{ $i }}" role="tab"
                                            aria-controls="period{{ $i }}" aria-selected="{{ $i == 1 ? 'true' : 'false' }}">
                                            P{{ $i }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                            <div class="tab-content" id="myTabContent2">
                                @for($i = 1; $i <= 6; $i++)
                                    <div class="tab-pane fade {{ $i == 1 ? 'show active' : '' }}" id="period{{ $i }}" role="tabpanel" aria-labelledby="period{{ $i }}-tab">
                                        <h5>P{{ $i }} Content</h5>
                                        <p> P{{ $i }} content goes here.</p>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                
                            </div>

                </div>
                
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
