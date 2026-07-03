@extends('layouts.app')
@section('title', 'Hostel')
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary" x-data="hostelForm()">
            <form method="post" id="myForm" action="{{ route('hostel.update', $hostels->id) }}" enctype="multipart/form-data">
              @method('PUT')
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Update Hostel</h6>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <select name="branch_id" id="branch" class="form-control form-control-sm" required>
                      <option value="" disabled selected>-- Choose Branch --</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" @selected($branch->id == $hostels->branch_id)>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ $hostels->name }}" class="form-control form-control-sm" required>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Type</label>
                    <select name="type" id="type" class="form-control form-control-sm" required>
                      <option value="">Select</option>
                      <option value="BOYS" @selected($hostels->type == 'BOYS')>BOYS</option>
                      <option value="GIRLS" @selected($hostels->type == 'GIRLS')>GIRLS</option>
                    </select>
                  </div>
                  {{-- <div class="form-group col-lg-3">
                                 <label>Warden Name</label>
                                 <select name="warden_name" id="staff" class="form-control form-control-sm" required>
                                     <option value="" disabled selected>-- Choose Warden --</option>
                                     @foreach ($staffs as $staff)
                                         <option value="{{ $staff->name }}" @selected($staff->name == $hostels->warden_name)>{{ $staff->name }}</option>
                  @endforeach
                  </select>
                </div> --}}

                <div class="form-group col-lg-3">
                  <label>Room Type</label>
                  <select name="room_type" class="form-control form-control-sm">
                    <option value="">Select</option>
                    <option value="AC" @selected($hostels->room_type == 'AC')>AC</option>
                    <option value="Non AC" @selected($hostels->room_type == 'Non AC')>Non AC</option>
                  </select>
                </div>

                <div class="col-md-12 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Room Details</h6>
                </div>

                <div id="roomDetails">
                  <template x-for="(room, index) in rooms" :key="index">
                    <div style="margin-left: 10px;" class="row mb-4 room-row">
                      <!-- Block No input has been removed -->
                      <div class="form-group col-lg-4">
                        <label>Floor</label>
                        <input type="text" x-model="room.floor" :name="`rooms[${index}][floor]`" class="form-control form-control-sm" required>
                      </div>
                      <div class="form-group col-lg-3">
                        <label>Room No</label>
                        <input type="text" x-model="room.room_no" :name="`rooms[${index}][room_no]`" class="form-control form-control-sm" required>
                      </div>

                      <div class="form-group col-lg-2">
                        <label for="no_of_beds">No of Cots</label>
                        <div class="d-flex align-items-center">
                          <input type="number" x-model="room.no_of_cots" :name="`rooms[${index}][no_of_cots]`" class="form-control form-control-sm me-2" required>
                        </div>
                      </div>
                      <div class="form-group col-lg-3">
                        <label for="no_of_beds">Cot Type</label>
                        <div class="d-flex align-items-center">
                          <select x-model="room.cot_type" :name="`rooms[${index}][cot_type]`" class="form-control form-control-sm me-2" required>
                          <option value="C-">C- SINGLE</option>
                          <option value="L-">L- LOWER</option>
                          <option value="U-">U - UPPER</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group col-lg-1">
                        <template x-if="!room.id">
                          <button style="margin-top: 25px;" type="button" class="btn btn-danger" @click="removeRoom(index)"><i class="fa fa-minus"></i></button>
                        </template>
                      </div>
                  </template>
                </div>

                <div class="col-md-12 col-sm-12 mb-3">
                  <button type="button" class="btn btn-warning" @click="addRoom"><i class="fa fa-plus"></i> Add Room</button>
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
  function hostelForm() {
     return {
        rooms: [],
        addRoom() {
           this.rooms.push({ floor: '', room_no: '', no_of_cots: '', cot_type: 'C-' });
        },
        removeRoom(index) {
           if(confirm('Are you sure you want to remove this room?')){
               this.rooms.splice(index, 1);
           }
        },
        init() {
           this.rooms = @json($rooms);
           console.log(this.rooms);
           
        }
           
     }
  }
</script>
@endsection