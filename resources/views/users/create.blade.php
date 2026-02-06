@extends('layouts.app')
@section('title', 'Users List')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/select2/dist/css/select2.min.css')}}">
@endsection
@section('main')
<div class="main-content">
  <section class="section">
    
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <form method="post" id="myForm" action="{{ route('users.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <label>Role</label>
                    <select name="type" class="form-control form-control-sm" required>
                      <option value="">Select Role</option>
                      {{-- <option value="Admin">Admin</option> --}}
                      <option value="Branch Admin">Branch Admin</option>
                      <option value="Accountant">Accountant</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-3" style="display: none" id="branch_div">
                    <label for="branch">Branch</label>
                    <select name="branch" id="branch" class="select2 form-control">
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label> User Name</label>
                    <input type="text" name="username" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" required>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control form-control-sm" required>
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
@section('js')

<script src="{{asset('bundles/select2/dist/js/select2.full.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('select[name="type"]').on('change', function(){
      if($(this).val() != 'Admin'){
        $('#branch_div').show();
      }else{
        $('#branch_div').hide();
      }
    });
  });
</script>
@endsection