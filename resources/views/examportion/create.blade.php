@extends('layouts.app')

@section('title', ' Exam Portion')
@section('css')
@endsection
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('examportion.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Add PDF</h6>
                             </div>
                             
                             <div class="form-group col-lg-3">
                                <label>Branch</label>
                                <select name="branch_id" class="form-control form-control-sm"  required>
                                    <option value="{{ $branches->implode('id', ',') }}">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Coaching Type</label>
                                <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required>
                                   <option value="Offline,Online Recorded,Online Live">All</option>
                                   <option value="Offline">Offline</option>
                                   <option value="Online Recorded">Online Recorded</option>
                                   <option value="Online Live">Online Live</option>
                                </select>
                             </div>
                             <div class="form-group col-lg-3">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="attachment">Attachment <span class="text-danger">*PDF only</span></label>
                                <input type="file" name="attachment" id="attachment" class="form-control form-control-sm" required accept=".pdf" >
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
