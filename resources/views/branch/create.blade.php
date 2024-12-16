@extends('layouts.app')
@section('title', 'Admission')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm"  action="{{ route('branch.store') }}" enctype="multipart/form-data">
                        @csrf
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple">Add Branch</h6>
                        </div>

                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name"  class="form-control form-control-sm" required>
                       </div>
                      
                      
                  <div class="form-group col-lg-3">
                    <label>Address Line1</label>
                     <input type="text"  name="address_line_1" class="form-control form-control-sm" required>
                </div>

                <div class="form-group col-lg-3">
                  <label>Address Line2</label>
                   <input type="text" name="address_line_2" class="form-control form-control-sm">
              </div>

              <div class="form-group col-lg-3" >
                <label>City</label>
                 <select name="city" id="city" class="form-control form-control-sm" required>
                   <option value="">Select City</option>
                   @foreach ($districts as $district)
                   <option value="{{$district->District}}">{{$district->District}}</option>
                   @endforeach
                 </select>
            </div>



              
            <div class="form-group col-lg-3">
              <label>State</label>
               <select name="state" id="state" class="form-control form-control-sm" required>
                 <option value="">Select State</option>
                 @foreach ($states as $state)
                 <option value="{{$state->State}}">{{$state->State}}</option>
                 @endforeach
               </select>
          </div>

        


            <div class="form-group col-lg-3">
              <label>Pincode</label>
               <input type="number" name="pincode" class="form-control form-control-sm">
          </div>
         
               
          <div class="form-group col-lg-3">
            <label>Contact Mobile No</label>
             <input type="text" name="mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" required>
             @error('mob_no')
             <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
             @enderror
        </div>


        <div class="form-group col-lg-3">
            <label>Email</label>
             <input type="text" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror">
             @error('email')
             <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
             @enderror
        </div>

        <div class="form-group col-lg-3">
            <label>Manager Name</label>
             <input type="text" name="manager_name" class="form-control form-control-sm">
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


@endsection