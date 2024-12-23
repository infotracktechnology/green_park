@extends('layouts.app')
@section('title', 'Section Shuffle')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
         <div class="row">
            <div class="col-md-12 col-sm-12">
               @if(session()->has('success'))
               <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
               </div>
               @endif

               <div class="card card-primary">
                  <form method="post" id="myForm" action="{{ route('allocation.store') }}" enctype="multipart/form-data">
                     @csrf
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12 col-sm-12 mb-3">
                              <h6 class="col-deep-purple">Hostel Allocation</h6>
                              <div class="col-md-12">
                                  <div class="row">
                                    <div class=" form-group col-3">
                                        <label>Branch</label>
                                        <select name="branch" id="branch" class="form-control form-control-sm" onchange="window.location.href = '/admin/allocation/hostel?branch=' + this.value" required>
                                            <option value="" disabled selected>Choose Branch</option>
                                            @foreach ($branches as $row)
                                                <option value="{{ $row->id }}" @if($row->id == $branch) selected @endif>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group  col-3">
                                        <label>Hostel</label>
                                        <select name="hostel" id="hostel" class="form-control form-control-sm" required>
                                            <option value="">Select </option>
                                            @foreach ($hostels as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-3">
                                        <label>Block</label>
                                        <select name="block_no" id="block_no" class="form-control form-control-sm" required>
                                            <option value="">Select Block</option>
                                            @foreach ($blocks as $block)
                                                <option value="{{ $block->block_no }}">{{ $block->block_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-3">
                                        <label>Floor</label>
                                        <select name="floor_no" id="floor_no" class="form-control form-control-sm" required>
                                            <option value="">Select Floor</option>
                                            @foreach ($floors as $floor)
                                                <option value="{{ $floor->floor_no }}">{{ $floor->floor_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-3">
                                        <label> Room Type</label>
                                        <select name="type" id="type5" class="form-control form-control-sm" required>
                                            <option value="">Select </option>
                                            <option value="AC">AC</option>
                                            <option value="Non_AC">Non AC</option>
                                           
                                        </select>
                                    </div>
                                    <div class="form-group col-3">
                                        <label>Room No</label>
                                        <select name="room_no" id="room_no" class="form-control form-control-sm" required>
                                            <option value="">Select Room No</option>
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->room_no }}">{{ $room->room_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="table-responsive">
                              <table class="table table-sm table-hover" id="myTable">
                                 <thead>
                                    
                                            <tr role="row">
                                                <th>#</th>
                                                <th>Student ID</th>
                                                <th>Coaching Type</th>
                                                <th>Student Name</th>
                                                <th>Section</th>
                                                <th>Gender</th>
                                                <th>Father Name</th>
                                                <th>Mobile No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $student)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"/>
                                                    </td>
                                                    <td>{{ $student->id }}</td>
                                                    <td>{{ $student->coaching_type }}</td>
                                                    <td>{{ $student->student_name }}</td>
                                                    <td>{{ $student->section }}</td>
                                                    <td>{{ $student->gender }}</td>
                                                    <td>{{ $student->father_name }}</td>
                                                    <td>{{ $student->ph_no1 }}</td>
                                                </tr>
                                          @endforeach
                                        </tbody>
                                    </table>
                                    
                           </div>
                        </div>
                        <div class="form-group col-lg-12">
                           <button type="submit" class="btn btn-primary">Save</button>
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
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script>
   // Initialize DataTables
   const table = $('#myTable').DataTable({
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
   });

   Select All Checkbox Functionality
   document.getElementById('selectAll').addEventListener('change', function () {
      const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
      checkboxes.forEach((checkbox) => {
         checkbox.checked = this.checked;
      });
   });
</script>
@endsection
