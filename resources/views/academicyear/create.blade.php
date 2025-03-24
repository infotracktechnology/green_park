@extends('layouts.app')

@section('title', 'Academic Year')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('academicyear.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Add Academic Year</h6>
                              </div>

                              <div class="form-group col-lg-4">
                                 <label>Academic Year</label>
                                 <input type="text" name="academic_year"  class="form-control form-control-sm" required>
                              </div>

                              <div class="form-group col-lg-8">
                                 <label>Start Month/Year</label>
                                 <div class="row">
                                   <div class="col">
                                    <select name="start_month" id="start_month"  class="form-control form-control-sm" required>
                                       @for($i = 1; $i <= 12; $i++)
                                       <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                       @endfor
                                     </select>
                                   </div>
                                   <div class="col">
                                     <select name="start_year" id="start_year"  class="form-control form-control-sm" required>
                                       @for($i = date('Y')-10; $i <= date('Y')+1; $i++)
                                       <option value="{{ $i }}">{{ $i }}</option>
                                       @endfor
                                     </select>
                                   </div>
                                 </div>
                               </div>

                               <div class="form-group col-lg-8">
                                 <label>End Month/Year</label>
                                 <div class="row">
                                   <div class="col">
                                     <select name="end_month" id="end_month"  class="form-control form-control-sm" required>
                                       @for($i = 1; $i <= 12; $i++)
                                       <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                       @endfor
                                     </select>
                                   </div>
                                   <div class="col">
                                    <select name="end_year" id="end_year"  class="form-control form-control-sm" required>
                                       @for($i = date('Y')-10; $i <= date('Y')+1; $i++)
                                       <option value="{{ $i }}">{{ $i }}</option>
                                       @endfor
                                     </select>
                                   </div>
                                 </div>
                               </div>

                              <div class="form-group col-lg-2">
                                 <div class="form-check m-t-25">
                                    <div class="custom-control custom-checkbox">
                                      <input type="checkbox" name="enable" class="custom-control-input" id="enable"  value="1">
                                      <label class="custom-control-label" for="enable">Enable Academic Year</label>
                                    </div>
                                  </div>
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
<script>
//   function City(state) {
//      if (!state) return;
//      $.get("{{ route('staff.create') }}", {state: state}, function(data) {
//          let html = '<option value="">Select City</option>';
//          $.each(data, function(key, value) {
//              html += '<option value="' + value.District + '">' + value.District + '</option>';
//          });
//          $('#city').html(html);
//      });
//   }
</script>
@endsection
