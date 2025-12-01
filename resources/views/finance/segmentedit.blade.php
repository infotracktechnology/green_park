@extends('layouts.app')

@section('title', 'Edit Segment')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
                  <div class="card card-primary">
                    @if ($errors->any())
                      <div class="alert alert-danger">
                          @foreach ($errors->all() as $error)
                              {{ $error }}
                          @endforeach
                      </div>
                    @endif
                     <div class="card-header d-flex justify-content-between align-items-center">
                                 <h4>Edit Segment</h4>
                                 <a href="{{ route('bank.create') }}" class="btn btn-secondary btn-sm float-right"><i class="fa fa-arrow-left"></i> Back</a>
                              </div>
                     
                        <div class="card-body">
                            <form method="post" id="myForm" action="{{ route('segment.update', $segment->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                           <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Segment Name</label>
                                    <input type="text" name="name" id="segment_name" class="form-control" value="{{ $segment->name }}" required>
                                </div>
                             </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="segment_branch_id">Branch</label>
                                    <select name="branch_id" id="segment_branch_id"  class="form-control form-control-sm select2" required>
                                        <option value="">Select Branch</option>
                                        @foreach ($branchselect as $id => $branch)
                                            <option value="{{ $id }}" @selected($segment->branch_id == $id)>{{ $branch }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select name="is_active" id="segmentstatus"  class="form-control form-control-sm select2" required>
                                    <option value="">Select Status</option>
                                    <option value="1" @selected($segment->is_active == 1)>Active</option>
                                    <option value="0" @selected($segment->is_active == 0)>InActive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">Update</button>
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