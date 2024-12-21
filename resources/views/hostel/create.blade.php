@extends('layouts.app')
@section('title', 'Hostel')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('hostel.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Hostel Details</h6>
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>Branch</label>
                                 <select name="branch_id" id="branch" class="form-control form-control-sm" required>
                                     <option value="" disabled selected>-- Choose Branch --</option>
                                     @foreach ($branches as $branch)
                                         <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             
                              <div class="form-group col-lg-3">
                                 <label>Name</label>
                                 <input type="text" name="name" class="form-control form-control-sm" required>
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>Type</label>
                                 <select name="type" id="type" class="form-control form-control-sm" required>
                                    <option value="">Select</option>
                                    <option value="Boys">Boys</option>
                                    <option value="Girls">Girls</option>
                                 </select>
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>Warden Name</label>
                                 <select name="warden_name" id="staff" class="form-control form-control-sm" required>
                                     <option value="" disabled selected>-- Choose Warden --</option>
                                     @foreach ($staffs as $staff)
                                         <option value="{{ $staff->name }}" @if(old('warden_name') == $staff->name) selected @endif>{{ $staff->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             
                             
                             
                              <div class="form-group col-lg-3">
                                 <label>Address</label>
                                 <input type="text" name="address" class="form-control form-control-sm" required>
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>State</label>
                                <select name="state" id="state" onchange="City(this.value);" class="form-control form-control-sm" required>
                              <option value="">Select State</option>
                              @foreach ($states as $state)
                              <option value="{{$state->State}}">{{$state->State}}</option>
                              @endforeach
                            </select>
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>City</label>
                                 <select name="city" id="city" class="form-control form-control-sm" required>
                                  <option value="">Select City</option>
                                </select>
                              </div>
                              
                              <div class="form-group col-lg-3">
                                 <label>Pincode</label>
                                 <input type="number" name="pincode" class="form-control form-control-sm">
                              </div>
                              <div class="form-group col-lg-3">
                                 <label>Contact Mobile No</label>
                                 <input type="text" name="phone_no" class="form-control form-control-sm @error('phone_no') is-invalid @enderror" required>
                                 @error('phone_no')
                                 <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                 @enderror
                              </div>
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Room Details</h6>
                              </div>
                              <div id="roomDetails">
                                 <div style="margin-left: 10px;" class="row mb-4 room-row">
                                    <div class="form-group col-lg-2">
                                       <label>Block No</label>
                                       <input type="text" name="rooms[0][block_no]" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="form-group col-lg-2">
                                       <label>Floor No</label>
                                       <input type="text" name="rooms[0][floor_no]" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="form-group col-lg-2">
                                       <label>Room No</label>
                                       <input type="text" name="rooms[0][room_no]" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="form-group col-lg-2">
                                       <label>Room Type</label>
                                       <select name="rooms[0][room_type]" class="form-control form-control-sm" >
                                          <option value="">Select</option>
                                          <option value="AC">AC</option>
                                          <option value="Non AC">Non AC</option>
                                       </select>
                                    </div>
                                    <div class="form-group col-lg-2">
                                       <label for="no_of_beds">No of Beds</label>
                                       <div class="d-flex align-items-center">
                                          <input type="text" name="rooms[0][no_of_beds]" class="form-control form-control-sm me-2" required>
                                          <button style="margin-left: 10px;" type="button" class="btn btn-warning btn-sm add-row" id="add"><i class="fa fa-plus"></i></button>
                                       </div>
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
  document.addEventListener('DOMContentLoaded', () => {
    const roomDetailsContainer = document.getElementById('roomDetails');

    roomDetailsContainer.addEventListener('click', (event) => {
      const target = event.target.closest('button');
      if (target && target.classList.contains('add-row')) {
        const rows = roomDetailsContainer.querySelectorAll('.room-row');
        const index = rows.length;

        const firstRow = rows[0];
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input, select').forEach((field) => {
          field.value = '';
          const name = field.getAttribute('name');
          if (name) {
            field.setAttribute('name', name.replace(/rooms\[\d+\]/, `rooms[${index}]`));
          }
        });

        const addButton = newRow.querySelector('button.add-row');
        addButton.classList.remove('add-row');
        addButton.classList.add('remove-row');
        addButton.innerHTML = '<i class="fa fa-minus"></i>';

        roomDetailsContainer.appendChild(newRow);
      } else if (target && target.classList.contains('remove-row')) {
        const rowToRemove = target.closest('.room-row');
        if (rowToRemove) {
          rowToRemove.remove();
        }
      }
    });
  });
</script>
<script>
   function City(state) {
      if(!state) return;
      $.get("{{ route('hostel.create') }}", {state: state}, function(data) {
          var html = '<option value="">Select City</option>';
          $.each(data, function(key, value) {
              html += '<option value="' + value.District + '">' + value.District + '</option>';
          });
          $('#city').html(html);
      });
   }
</script>
@endsection
