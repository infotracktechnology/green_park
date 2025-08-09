@extends('layouts.app')

@section('title', 'Shift')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="workshift()">
                     <form method="post" id="myForm" action="{{ route('workshift.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Add Shift</h6>
                             </div>
                            
                        
                             <div class="form-group col-lg-4">
                                <label for="branch_id">Branch</label>
                                <select name="branchid" id="branchid" class="select2 form-control" required>
                                    <option value="">-- Choose Branch --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="form-group col-lg-4">
                                <label for="title">Shift Name</label>
                                <input type="text" name="shift_name" id="shift_name" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="link">No of Sessions</label>
                                 <select name="no_session" x-model="no_session" id="no_session" class="form-control orm-control-sm" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                 </select>
                            </div>

                            <div class="form-group col-lg-12">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Session Name</th>
                                        <th>Actual Start Time</th>
                                        <th>Session Grace Time <br>(if any) (in min)</th>
                                        <th>Actual End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><input type="text" name="session1_name" class="form-control form-control-sm" required></td>
                                        <td><input type="time" x-model="session1_starttime" name="session1_starttime" class="form-control form-control-sm" required></td>
                                        <td><input type="number" value="0" name="gracetime1" class="form-control form-control-sm" required></td>
                                        <td><input type="time" :min="session1_starttime" name="session1_endtime" class="form-control form-control-sm" required></td>
                                    </tr>

                                    <tr x-show="no_session == 2">
                                        <td>2</td>
                                        <td><input type="text" name="session2_name" class="form-control form-control-sm" :required="no_session == 2"></td>
                                        <td><input type="time" x-model="session2_starttime" name="session2_starttime" class="form-control form-control-sm" :required="no_session == 2"></td>
                                        <td><input type="number" value="0" name="gracetime2" class="form-control form-control-sm" :required="no_session == 2"></td>
                                        <td><input type="time" :min="session2_starttime" name="session2_endtime" class="form-control form-control-sm" :required="no_session == 2"></td>
                                    </tr>
                                    
                                </tbody>
                            </table>
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
function workshift() {
    return {
        no_session: 2,
        session1_starttime: '',
        session2_starttime: '',
    }
}
</script>
@endsection
