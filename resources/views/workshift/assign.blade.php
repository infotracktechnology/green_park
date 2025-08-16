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

                             @if(session()->has('success'))
                            <div class="col-md-12 col-sm-12 mb-3">

                             <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
                            </div>
                             @endif

                             @if(session()->has('error'))
                            <div class="col-md-12 col-sm-12 mb-3">
                             <div class="alert alert-danger alert-dismissible show fade">{{ session('error') }}</div>
                            </div>
                             @endif

                             <!-- Branch Selection - Hide after selection -->
                             <div class="form-group col-lg-4">
                                <label for="branch_id">Branch</label>
                                <select name="branchid" id="branchid" class="form-control" required x-model="branchid" x-on:change="getShifts">
                                    <option value="">-- Choose Branch --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-lg-12">
                                <!-- Staff Summary Table - Show after branch selection -->
                                <table class="table table-bordered" x-show="showStaffList" x-transition>
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
                                        <template x-for="(group, index) in groupedStaffs" :key="index">
                                            <tr :class="group.shift_name == 'Not Assigned' ? 'col-red font-weight-bold' : 'col-blue font-weight-bold'">
                                                <td x-text="index + 1"></td>
                                                <td x-text="group.shift_name"></td>
                                                <td x-text="group.teaching_count"></td>
                                                <td x-text="group.non_teaching_count"></td>
                                                <td>
                                                    <a href="javascript:void(0);" 
                                                       x-on:click="group.shift_name == 'Not Assigned' ? showAllStaffs() : null"
                                                       :class="group.shift_name == 'Not Assigned' ? 'col-red' : 'col-blue'"
                                                       x-text="group.total_count">
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>

                                <!-- All Staff Assignment Table - Show when clicking on any total count -->
                                <div x-show="filteredStaffList" x-transition>
                                    <form method="post" id="myForm" action="{{ route('workshift.assign') }}" enctype="multipart/form-data">
                                        @csrf
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>
                                                        <button type="button" x-on:click="showStaffList = true ; filteredStaffList = false" class="btn btn-primary">Back</button>
                                                    </td>
                                                    <td>
                                                        <select name="shift" class="form-control form-control-sm" 
                                                                x-model="selectedShiftForAssignment" 
                                                                {{-- x-on:change="updateCheckboxes()"  --}}
                                                                required>
                                                            <option value="">Select Shift</option> 
                                                            <template x-for="shift in availableShifts" :key="shift.id">
                                                                <option :value="shift.id" x-text="shift.shift_name"></option>
                                                            </template>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button type="submit" name="assign" class="btn btn-primary">Assign</button>
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" x-model="checkAll" x-on:change="toggleAllCheckboxes" />
                                                    </th>
                                                    <th>Name</th>
                                                    <th>School Initial</th>
                                                    <th>Department</th>
                                                    <th>Current Shift</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="staff in filteredStaffs" :key="staff.id">
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" 
                                                                   name="staff_ids[]" 
                                                                   :value="staff.id" 
                                                                   x-model="selectedStaffs"
                                                                   :id="'staff_' + staff.id"/>
                                                        </td>
                                                        <td x-text="staff.name"></td>
                                                        <td x-text="staff.school_initial"></td>
                                                        <td x-text="staff.department"></td>
                                                        <td x-text="staff.shift ? staff.shift.shift_name : 'Not Assigned'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
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
        branchid: null,
        showStaffList: false,
        filteredStaffList: false,
        checkAll: false,
        selectedStaffs: [],
        selectedShiftForAssignment: '',
        allStaffs: @json($staffs),
        allShifts: @json($shifts),
        filteredStaffs: [],
        
        get availableShifts() {
            return this.allShifts.filter(shift => shift.branchid == this.branchid);
        },
        
        get groupedStaffs() {
            const groups = {};            
            
            this.filteredStaffs.forEach(staff => {
                const shiftKey = staff.shiftid || 'unassigned';
                const shiftName = staff.shift ? staff.shift.shift_name : 'Not Assigned';
                
                if (!groups[shiftKey]) {
                    groups[shiftKey] = {
                        shift_name: shiftName,
                        staffs: [],
                        teaching_count: 0,
                        non_teaching_count: 0,
                        total_count: 0
                    };
                }
                
                groups[shiftKey].staffs.push(staff);
                groups[shiftKey].total_count++;
                
                if (staff.department === 'Others') {
                    groups[shiftKey].non_teaching_count++;
                } else {
                    groups[shiftKey].teaching_count++;
                }
            });            
            
            return Object.values(groups);
        },
        
        getShifts() {
            if (!this.branchid || this.branchid == '') {
                this.branchid = null;
                return;
            }
            
            this.filteredStaffs = this.allStaffs.filter(staff => staff.branch_id == this.branchid);
            this.showStaffList = true;
            this.filteredStaffList = false;
            this.selectedStaffs = [];
            this.checkAll = false;
            this.selectedShiftForAssignment = '';
        },
        
        showAllStaffs() {
            this.showStaffList = false;
            this.filteredStaffList = true;
            this.selectedStaffs = [];
            this.checkAll = false;
            this.selectedShiftForAssignment = '';
        },
        
        // updateCheckboxes() {
        //     if (!this.selectedShiftForAssignment) {
        //         this.selectedStaffs = [];
        //         this.checkAll = false;
        //         return;
        //     }
            
        //     // Pre-check staff who are already assigned to the selected shift
        //     this.selectedStaffs = this.filteredStaffs
        //         .filter(staff => staff.shiftid == this.selectedShiftForAssignment)
        //         .map(staff => staff.id.toString());            
                
        //     // Update checkAll status
        //     this.checkAll = this.selectedStaffs.length === this.filteredStaffs.length;
        // },
        
        toggleAllCheckboxes() {
            if (this.checkAll) {
                this.selectedStaffs = this.filteredStaffs.map(staff => staff.id.toString());
            } else {
                this.selectedStaffs = [];
            }
        }
    }
}
</script>
@endsection
