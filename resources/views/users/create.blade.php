@extends('layouts.app')
@section('title', 'Users List')
@section('main')
<div class="main-content">
   <section class="section">
      <?php
       $role = ['Super Admin', 'Admin', 'User'];
      ?>
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary">
                     <form method="post" id="myForm" action="{{ route('users.store') }}" enctype="multipart/form-data">
                        @csrf
                      <div class="card-body">
    
                      <div class="row">
                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                            @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                       </div>

                       <div class="form-group col-lg-3">
                        <label>Email</label>
                         <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" required>
                         @error('email')
                         <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                         @enderror
                    </div>


                    <div class="form-group col-lg-3">
                     <label>Password</label>
                      <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" required>
                      @error('password')
                      <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                 </div>


                 <div class="form-group col-lg-3">
                  <label>Confirm Password</label>
                   <input type="password" name="confirm_password" class="form-control form-control-sm @error('confirm_password') is-invalid @enderror" required>
                   @error('confirm_password')
                   <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                   @enderror
              </div>

              <div class="form-group col-lg-3">
               <label>Role</label>
                <select name="type" class="form-control form-control-sm @error('type') is-invalid @enderror" required>
                  <option value="">Select Role</option>
                  @foreach ($role as $key => $item)
                  <option value="{{ $key }}">{{ $item }}</option>
                  @endforeach
                </select>
                @error('type')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
           </div>


           <div class="form-group col-lg-3">
            <label>Status</label>
             <select name="status" class="form-control form-control-sm @error('status') is-invalid @enderror" required>
               <option value="">Select Status</option>
               <option value="1">Active</option>
               <option value="0">Inactive</option>
             </select>
             @error('status')
             <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
             @enderror
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