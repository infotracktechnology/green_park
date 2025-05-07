@extends('layouts.app')
@section('title', 'Users List')
@section('main')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/summernote/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{asset('bundles/select2/dist/css/select2.min.css')}}">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #6777ef; 
        color: #fff; 
        border: none; 
        padding: 5px 10px; 
        margin: 5px 5px 0 0; 
        border-radius: 3px; 
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        display: none; 
    }

    .select2-container--default .select2-selection--single {
        border-color: #6777ef;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6777ef transparent transparent transparent;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6777ef;
        color: #fff;
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #6777ef;
        min-height: 38px;
        padding: 0;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 0 5px;
    }
</style>
@endsection
<div class="main-content">
   <section class="section">
    <?php
    $role = ['Branch Admin', 'Accountant'];
   ?>
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary">
                     <form method="post" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                      <div class="card-body">
    
                        <div class="row">
                        
                         
                            <div class="form-group col-lg-3">
                                <label>Role</label>
                                <select name="type" class="form-control form-control-sm @error('type') is-invalid @enderror" required>
                                  <option value="">Select Role</option>
                                  <option value="1" {{ $user->type == 1 ? 'selected' : '' }}>Branch Admin</option>
                                  <option value="2" {{ $user->type == 2 ? 'selected' : '' }}>Accountant</option>
                                </select>
                                @error('type')
                                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                              </div>
                              
    
                              <div class="form-group col-lg-4" id="branch_div">
                                <label for="branch">Branch</label>
                                <select name="branch" id="branch" class="select2 form-control" required>
                                 <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch->id == $user->branch ? 'selected' : '' }}>
                                      {{ $branch->name }}
                                    </option>
                                  @endforeach
                                </select>
                              </div>
                              
                              
                     
                           <div class="form-group col-lg-3">
                              <label>User Name</label>
                               <input type="text" name="username" value="{{ $user->username }}" class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                               @error('username')
                               <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                               @enderror
                          </div>
   
                          <div class="form-group col-lg-3">
                           <label>Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control form-control-sm @error('email') is-invalid @enderror" required>
                            @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                       </div>
   
   
                     
   
                    
                      
   
                      <div class="form-group col-lg-3">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror">
                        @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    
                    <div class="form-group col-lg-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-sm @error('confirm_password') is-invalid @enderror">
                        @error('confirm_password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    
                    





   
              {{-- <div class="form-group col-lg-3">
               <label>Status</label>
                <select name="status" class="form-control form-control-sm @error('status') is-invalid @enderror" required>
                  <option value="">Select Status</option>
                  <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
           </div> --}}
   
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
@section('js')

<script src="{{asset('bundles/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select Branch',
            allowClear: true
        });
    });
</script>
<script>
    $(document).ready(function() {
      function toggleBranchDiv() {
        if ($('select[name="type"]').val() == 1) {
          $('#branch_div').show();
        } else {
          $('#branch_div').hide();
        }
      }
  
      // Run on load
      toggleBranchDiv();
  
      // Run on change
      $('select[name="type"]').on('change', toggleBranchDiv);
    });
  </script>
  @endsection