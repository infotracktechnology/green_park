@extends('layouts.app')
@section('title', 'Hostel')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="hostelForm()">
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
                                    <option value="MALE">Boys</option>
                                    <option value="FEMALE">Girls</option>
                                 </select>
                              </div>
                              {{-- <div class="form-group col-lg-3">
                                 <label>Warden Name</label>
                                 <select name="warden_name" id="staff" class="form-control form-control-sm" required>
                                     <option value="" disabled selected>-- Choose Warden --</option>
                                     @foreach ($staffs as $staff)
                                         <option value="{{ $staff->name }}" @if(old('warden_name') == $staff->name) selected @endif>{{ $staff->name }}</option>
                                     @endforeach
                                 </select>
                             </div> --}}
                             
                             <div class="form-group col-lg-3">
                              <label>Room Type</label>
                              <select name="room_type" class="form-control form-control-sm" >
                                 <option value="">Select</option>
                                 <option value="AC">AC</option>
                                 <option value="Non AC">Non AC</option>
                              </select>
                           </div>
                             
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Room Details</h6>
                              </div>
                              <div class="col-md-12 col-sm-12 mb-3">
                              <button type="button" class="btn btn-warning btn-sm" @click="addRoom"><i class="fa fa-plus"></i> Add Room</button>
                              </div>
                              <div id="roomDetails">
                                 <template x-for="(room, index) in rooms" :key="index">
                                    <div style="margin-left: 10px;" class="row mb-4 room-row">
                                       <!-- Block No input has been removed -->
                                       <div class="form-group col-lg-4">
                                          <label>Floor</label>
                                          <input type="text" :name="`rooms[${index}][floor]`" class="form-control form-control-sm" required>
                                       </div>
                                       <div class="form-group col-lg-3">
                                          <label>Room No</label>
                                          <input type="text" :name="`rooms[${index}][room_no]`" class="form-control form-control-sm" required>
                                       </div>
                                       
                                       <div class="form-group col-lg-2">
                                          <label for="no_of_beds">No of Cots</label>
                                          <div class="d-flex align-items-center">
                                             <input type="number" :name="`rooms[${index}][no_of_cots]`" class="form-control form-control-sm me-2" required>
                                          </div>
                                       </div>
                                       <div class="form-group col-lg-2">
                                             <template x-if="index !== 0">
                                                <button style="margin-top: 25px;" type="button" class="btn btn-warning btn-sm" @click="removeRoom(index)"><i class="fa fa-minus"></i></button>
                                             </template>
                                          </div>
                                       </div>
                                    </div>
                                 </template>
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
   function hostelForm() {
      return {
         rooms: [{ floor_no: '', room_no: '', no_of_beds: '', cart_no: '' }],
         addRoom() {
            this.rooms.push({ floor_no: '', room_no: '', no_of_beds: '', cart_no: '' });
         },
         removeRoom(index) {
            this.rooms.splice(index, 1);
         }
      }
   }
</script>
@endsection