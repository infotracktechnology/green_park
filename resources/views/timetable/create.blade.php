@extends('layouts.app')

@section('title', 'Time Table')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="timetable()">
                     <form method="post" id="myForm" action="{{ route('timetable.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                           
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Add TimeTable</h6>
                              </div>

                              <div class="form-group col-lg-6">
                                    <label for="academic_year">Academic Year</label>
                                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                        @foreach ($academicyear as $row)
                                            <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                 <label for="academic_year">TimeTable Name</label>
                                 <input type="text" name="timetable_name" id="timetable_name" class="form-control form-control-sm" placeholder="Enter TimeTable Name" required>
                             </div>

                           <div class="form-group col-lg-10">
                           <label>Days</label>
                             @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <input type="checkbox" value="{{ $day }}" class="days m-t-15" name="days[]"> {{ $day }}
                             @endforeach
                            </div>

                              <div class="form-group col-lg-2">
                                 <label>Start Time</label>
                                 <input type="time" value="09:00" name="start_time" id="start_time" class="form-control form-control-sm" required>
                               </div>


                               <div class="form-group col-lg-12">
                                 <button type="button" @click="addRow" class="btn btn-primary">Add Session</button>
                              </div>

                               <div class="col-lg-12">
                                 <div class="table-responsive">
                                    <table class="table table-bordered">
                                      <thead>
                                        <tr>
                                          <th>Session / Break</th>
                                          <th>Session / Break Name</th>
                                          <th>No of Periods in Session</th>
                                          <th>Period / Break Duration</th>
                                          <th width="30"></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <template x-for="(row, index) in structure" :key="index">
                                          <tr>
                                            <td>
                                              <select class="form-control form-control-sm" @change="updateName(row)" x-model="row.type">
                                                <option value="academic" selected>Academic Session</option>
                                                <option value="break">Break</option>
                                              </select>
                                            </td>
                                            <td>
                                              <input 
                                                type="text" 
                                                class="form-control form-control-sm"
                                                x-model="row.name"
                                                :name="`structure[${index}][name]`"
                                              >
                                            </td>
                                            <td>
                                              <select class="form-control form-control-sm" x-model="row.periods" :name="`structure[${index}][period]`" x-show="row.type === 'academic'">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                              </select>
                                            </td>
                                            <td>
                                             <div class="input-group">
                                                <select class="form-control form-control-sm" x-model="row.duration" :name="`structure[${index}][duration]`">
                                                    @for($i = 5; $i <= 60; $i += 5)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <span class="m-t-5">(in minutes)</span>
                                            </div>
                                            </td>
                                            <td>
                                              <button 
                                                class="btn btn-danger" 
                                                @click="removeRow(index)" 
                                                x-show="index > 0">
                                                <i class="fas fa-trash"></i>
                                            </td>
                                          </tr>
                                        </template>
                                      </tbody>
                                    </table>
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
function timetable() {
  return {
    structure: [{ type: 'academic', periods: 1, duration: 5 }],
    addRow() {
      var row = { type: 'academic', periods: 1, duration: 5 };
      this.structure.push(row);
      this.updateName(row);
    },
    removeRow(index) {
      this.structure.splice(index, 1);
    },
    updateName(row) {
      const sessionCount = this.structure.filter(item => item.type === 'academic').length;
      row.periods=1;
      if (row.type === 'academic') {
        row.name = 'Session - '+sessionCount;
      }
      else{
        row.name = 'Break';
      }
    },
    init() {
      this.updateName(this.structure[0]);
    }
  };
}

$("#myForm").submit(function(e) {
    e.preventDefault();
    if($('.days:checked').length == 0) {
        alert("Please select at least one day");
    } else {
        this.submit();
    }
});
</script>
@endsection
