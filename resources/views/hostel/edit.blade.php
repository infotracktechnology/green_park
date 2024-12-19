@extends('layouts.app')
@section('title', 'Hostel Edit')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('hostel.update', $hostel->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Hostel Details -->
                            <div class="row">
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <h6 class="col-deep-purple">Update Hostel</h6>
                                </div>
                                <!-- Hostel Fields -->
                                <div class="form-group col-lg-3">
                                    <label>Name</label>
                                    <input type="text" name="name" value="{{ $hostel->name }}" class="form-control form-control-sm" required>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Type</label>
                                    <select name="type" id="type" class="form-control form-control-sm" required>
                                        <option value="">Select</option>
                                        <option value="Boys" @if($hostel->type == 'Boys') selected @endif>Boys</option>
                                        <option value="Girls" @if($hostel->type == 'Girls') selected @endif>Girls</option>
                                    </select>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Warden Name</label>
                                    <input type="text" name="warden_name" value="{{ $hostel->warden_name }}" class="form-control form-control-sm" required>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Address</label>
                                    <input type="text" name="address" value="{{ $hostel->address }}" class="form-control form-control-sm" required>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>State</label>
                                    <select name="state" onchange="City(this.value);" class="form-control form-control-sm" required>
                                      <option value="">Select State</option>
                                      @foreach ($states as $state)
                                      <option value="{{$state->State}}" @if($hostel->state == $state->State) selected @endif>{{$state->State}}</option>
                                      @endforeach
                                    </select>
                               </div>

                                <div class="form-group col-lg-3">
                                    <label>City</label>
                                     <select name="city" id="city"  class="form-control form-control-sm" required >
                                       <option value="">Select City</option>
                                       @foreach ($districts as $district) 
                                       <option value="{{$district->District}}" @if($hostel->city == $district->District) selected @endif>{{$district->District}}</option>
                                       @endforeach
                                     </select>
                                </div>

                                

                                <div class="form-group col-lg-3">
                                    <label>Pincode</label>
                                    <input type="number" name="pincode" value="{{ $hostel->pincode }}" class="form-control form-control-sm" required>
                                </div>

                                <div class="form-group col-lg-3">
                                    <label>Contact Mobile No</label>
                                    <input type="text" name="phone_no" value="{{ $hostel->phone_no }}" maxlength="10" class="form-control form-control-sm @error('phone_no') is-invalid @enderror">
                                    @error('phone_no')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Room Details -->
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <h6 class="col-deep-purple">Room Details</h6>
                                    
                                </div>
                                <meta name="csrf-token" content="{{ csrf_token() }}">

                                <div class="form-group col-lg-12">
                                    <button style="margin-left: 10px;" type="button" id="addRoomBtn" class="btn btn-warning btn-sm"><i class="fa fa-plus"></i>ADD</button>
                                </div>
                                <div id="roomDetails">
                                    @foreach ($hostel->rooms as $index => $room)
                                    <div style="margin-left: 10px;" class="row mb-4 room-row">
                                        <!-- Floor No -->
                                        <div class="form-group col-lg-3">
                                            <label>Floor No</label>
                                            <input type="text" name="rooms[{{ $index }}][floor_no]" value="{{ $room->floor_no }}" class="form-control form-control-sm" required>
                                        </div>

                                        <!-- Room No -->
                                        <div class="form-group col-lg-3">
                                            <label>Room No</label>
                                            <input type="text" name="rooms[{{ $index }}][room_no]" value="{{ $room->room_no }}" class="form-control form-control-sm" required>
                                        </div>

                                        <!-- Room Type -->
                                        <div class="form-group col-lg-3">
                                            <label>Room Type</label>
                                            <select name="rooms[{{ $index }}][room_type]" class="form-control form-control-sm" required>
                                                <option value="">Select</option>
                                                <option value="AC" @if($room->room_type == 'AC') selected @endif>AC</option>
                                                <option value="Non AC" @if($room->room_type == 'Non AC') selected @endif>Non AC</option>
                                            </select>
                                        </div>

                                        <!-- No of Beds -->
                                        <div class="form-group col-lg-3">
                                            <label>No of Beds</label>
                                            <div class="d-flex align-items-center">
                                            <input type="number" name="rooms[{{ $index }}][no_of_beds]" value="{{ $room->no_of_beds }}" class="form-control form-control-sm" required>
                                            <button style="margin-left: 10px;" type="button" class="btn btn-danger btn-sm" onclick="deleteRoom(event);"><i class="fa fa-trash"></i></button>
                                        </div>
                                        </div>

                                        <!-- Hidden Room ID Field -->
                                        <input type="hidden" name="rooms[{{ $index }}][id]" value="{{ $room->id }}">
                                    </div>
                                    @endforeach
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
     function deleteRoom(e){
        // Check if the clicked element is a delete button
        if (e.target && e.target.classList.contains('btn-danger')) {
            const roomRow = e.target.closest('.room-row');
            roomRow.remove();
        }
    }


 document.addEventListener('DOMContentLoaded', function() {
  const roomDetailsContainer = document.getElementById('roomDetails');
  const addRoomBtn = document.getElementById('addRoomBtn');

  // Add new room on button click
  addRoomBtn.addEventListener('click', function() {
    const roomIndex = roomDetailsContainer.querySelectorAll('.room-row').length;
    const newRoomRow = `
      <div style="margin-left: 10px;" class="row mb-4 room-row" id="room-row-${roomIndex}">
        <div class="form-group col-lg-3">
          <label>Floor No</label>
          <input type="text" name="rooms[${roomIndex}][floor_no]" class="form-control form-control-sm" required>
        </div>
        <div class="form-group col-lg-3">
          <label>Room No</label>
          <input type="text" name="rooms[${roomIndex}][room_no]" class="form-control form-control-sm" required>
        </div>
        <div class="form-group col-lg-3">
          <label>Room Type</label>
          <select name="rooms[${roomIndex}][room_type]" class="form-control form-control-sm">
            <option value="">Select</option>
            <option value="AC">AC</option>
            <option value="Non AC">Non AC</option>
          </select>
        </div>
        <div class="form-group col-lg-3">
          <label>No of Beds</label>
          <div class="d-flex align-items-center">
          <input type="number" name="rooms[${roomIndex}][no_of_beds]" class="form-control form-control-sm" required>
           <button style="margin-left: 10px;" type="button" onclick="deleteRoom(event);" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
        </div>
        
    `;
    roomDetailsContainer.insertAdjacentHTML('beforeend', newRoomRow);
  });

  // Event delegation for delete buttons
 

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
