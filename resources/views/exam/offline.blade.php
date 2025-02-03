@extends('layouts.app')

@section('title', 'Offline/OMR Upload')
@section('css')

@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary">
                    <form method="post" id="myForm" action="{{ route('exam.offline.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
    
                               <!-- Test ID Dropdown -->
                               <div class="form-group col-lg-4">
                                <label>Offline/OMR Upload</label>
                                <input type="file" name="offline" class="form-control form-control-sm" required>
                                @if ($errors->has('offline'))
                                    <span class="text-danger">{{ $errors->first('offline') }}</span>
                                @endif
                             </div>
    
                               
                               <div class="form-group col-lg-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Upload</button>
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

@endsection