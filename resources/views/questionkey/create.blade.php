@extends('layouts.app')
@section('title', 'Question key')
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
                     <form method="post" id="myForm" action="{{ route('questionkey.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Question Key Details</h6>
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
                <option value="11">11</option>
                <option value="12">12</option>
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
