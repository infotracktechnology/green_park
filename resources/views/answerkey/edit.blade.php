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
                    <form method="post" action="{{ route('answerkey.update', $answerKey->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Answer Key Details</h6>
                              </div>
                              
                              <div class="form-group col-lg-3">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                    {{-- <option value="">Select Academic Year</option> --}}
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" {{ $answerKey->academic_year == $row->academic_year ? 'selected' : '' }}>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                            </div>


                              <div class="form-group col-lg-4">
                                <label for="branch">Branch</label>
                                <select name="branch[]" class="form-control select2 @error('branch') is-invalid @enderror" multiple required>
                                    @php
                                        $selectedBranches = explode(',', $answerKey->branch); // Convert stored string to array
                                    @endphp
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" 
                                            {{ in_array($branch->id, $selectedBranches) ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group col-lg-5">
                                <label>Coaching Type</label>
                                <select name="coaching_type[]" class="form-control form-control-sm select2" multiple required>
                                    @foreach ($coachingtype as $row)
                                    <option value="{{$row}}" @selected(in_array($row, explode(',', $answerKey->coaching_type)))>{{$row}}</option>
                                    @endforeach
                                </select>
                            </div>
                            

                            <div class="form-group col-lg-3">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" required value="{{ $answerKey->title }}">
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Attachment <span class="text-danger">(Only PDF files, max size: 2MB*)</span></label>

                                <input type="file" name="file" id="fileInput" class="form-control form-control-sm">
                                <small id="fileName">
                                    @if($answerKey->file_path)
                                        {{ basename($answerKey->file_path) }}
                                    @endif
                                </small>
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
<script>
    document.getElementById('fileInput').addEventListener('change', function() {
        let fileName = this.files[0] ? this.files[0].name : "{{ basename($answerKey->file_path) }}";
        document.getElementById('fileName').innerText = fileName;
    });
</script>

@endsection
