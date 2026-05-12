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
            <form method="post" id="myForm" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="card-body">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <label>Role</label>
                    <select name="type" class="form-control form-control-sm" required>
                      <option value="">Select Role</option>
                      <option value="Branch Admin" @selected($user->type == 'Branch Admin')>Branch Admin</option>
                      <option value="Accountant" @selected($user->type == 'Accountant')>Accountant</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-3" id="branch_div">
                    <label for="branch">Branch</label>
                    <select name="branch[]"  class="select2 form-control" multiple>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected(in_array($branch->id, explode(',',$user->branch_ids)))>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label> User Name</label>
                    <input type="text" value="{{$user->username}}" name="username" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Email</label>
                    <input type="email" value="{{$user->email}}" name="email" class="form-control form-control-sm" required>
                  </div>


                  <div class="form-group col-lg-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox"  id="reset_password" name="reset_password">
                      <label class="form-check-label" for="reset_password">
                        Reset Password
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-lg-3 reset_password_inputs" style="display: none;">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-3 reset_password_inputs" style="display: none;">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control form-control-sm">
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
  function toggleBranchDiv() {
    if ($('select[name="type"]').val()!= 'Admin') {
      $('#branch_div').show();
    } else {
      $('#branch_div').hide();
    }
  }
  toggleBranchDiv();
  $('select[name="type"]').on('change', toggleBranchDiv);
  
$("#reset_password").change(function() {
  if (this.checked) {
  $(".reset_password_inputs").show();
  $(".reset_password_inputs input").prop("required", true);
  } 
  else {
  $(".reset_password_inputs").hide();
  $(".reset_password_inputs input").prop("required", false);
  }
  });
</script>
@endsection