@extends('layouts.app')

@section('title', 'Shift Assign')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="workshift()">
                      <div class="card-body">
                           <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Shift Assign</h6>
                             </div>

                    <div class="form-group col-lg-12">

                            <table class="table table-bordered" x-show="shown" x-transition>
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Shift Name</th>
                                        <th>Teaching Staff</th>
                                        <th>Non Teaching Staff</th>
                                        <th>Total Staff</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffs->groupBy('shiftid') as $shift)
                                    <?php
                                    $shift_name = is_null($shift->first()->shift) ? 'Not Assigned' : $shift->first()->shift->shift_name;
                                    ?>
                                    <tr @class([$shift_name == 'Not Assigned' ? 'col-red' : 'col-blue','font-weight-bold'])>
                                        <td>1</td>
                                        <td>{{$shift_name}}</td>
                                        <td>{{$shift->where('department', '!=', 'Others')->count()}}</td>
                                        <td>{{$shift->where('department', 'Others')->count()}}</td>
                                        <td>
                                            @if($shift_name == 'Not Assigned')
                                            <a href="javascript:void(0);" x-on:click="shown = 0" class="col-red">{{$shift->count()}}</a>
                                            @else
                                            <a href="javascript:void(0);" class="col-blue">{{$shift->count()}}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                   
                                    
                                </tbody>
                            </table>
 

                            <table class="table table-bordered" x-show="!shown" x-transition>
                                <form method="post" id="myForm" action="{{ route('workshift.assign') }}" enctype="multipart/form-data">
                                @csrf
                                <thead>
                                    <tr>
                                        <td><a href="{{ route('workshift.assign') }}" class="btn btn-primary">Back</a></td>
                                        <td>
                                            <select name="shift" class="form-control form-control-sm">
                                            <option value="">Select Shift</option> 
                                            @foreach($shifts as $shift) 
                                            <option value="{{$shift->id}}">{{$shift->shift_name}}</option> @endforeach
                                        </select>
                                        </td>
                                        <td><button type="submit" name="assign" class="btn btn-primary">Assign</button></td>
                                          <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th><input type="checkbox" x-model="checkAll" /></th>
                                        <th>Name</th>
                                        <th>School Initial</th>
                                        <th>Department</th>
                                        <th>Shift</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffs->whereNull('shiftid') as $staff)
                                    <tr>
                                        <td><input type="checkbox" name="staff_ids[]" value="{{$staff->id}}" :checked="checkAll"/></td>
                                        <td>{{$staff->name}}</td>
                                        <td>{{$staff->school_initial}}</td>
                                        <td>{{ $staff->department }}</td>
                                        <td>{{ $staff->shift?->shift_name }}</td>
                                    </tr>
                                    @endforeach
                                   
                                    
                                </tbody>
                                </form>
                            </table>

                            </div>
                           </div>
                           
                        
                            </div>
                        </div>
                     
                  </div>
              </div>
   </section>
</div>
@endsection
@section('js')
<script>
function workshift() {
    return {
        shown: 1,
        checkAll: 0,
    }
}
</script>
@endsection
