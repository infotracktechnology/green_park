@extends('layouts.app')
@section('title', 'Examinations')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css"/>
@endsection
<?php
$branch_ids =  explode(',', $exam->branch_id);
$coaching_types = explode(',', $exam->coaching_type);
?>
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
             <div class="col-12">
                  <div class="card card-primary">
                     <form method="post" id="myForm" action="{{ route('exam.update', $exam->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Edit Test</h6>
                              </div>


                              <div class="form-group col-lg-3">
                                <label for="branch">Select Academic Year:</label>
                                <select name="academic_year" id="academic_year" class="form-control form-control-sm">
                                    <option value="">-- Choose  Academic Year--</option>
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" @selected($row->academic_year == $exam->academic_year)>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                            </div>

                              <div class="form-group col-lg-4">
                                <label>Branch</label>
                                <select name="branch_id[]" class="select" multiple required>
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected(in_array($branch->id, $branch_ids))>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
    

                              <div class="form-group col-lg-4">
                                 <label>Coaching Type</label>

                                 <select name="coaching_type[]" id="coaching_type" class="select" multiple required>
                                    <option value="">Select Coaching Type</option>
                                    <option value="Offline" @selected(in_array('Offline', $coaching_types))>Offline</option>
                                    <option value="Online Recorded" @selected(in_array('Online Recorded', $coaching_types))>Online Recorded</option>
                                    <option value="Online Live" @selected(in_array('Online Live', $coaching_types))>Online Live</option>
                                    <option value="Test Series" @selected(in_array('Test Series', $coaching_types))>Test Series</option>
                                    <option value=" XI - OB" @selected(in_array('11', $coaching_types))>  XI - OB</option>
                                    <option value=" XII - OB" @selected(in_array('12', $coaching_types))> XII - OB</option>
                                 </select>
                              </div>
  <div class="form-group col-lg-4">  
                                <label>Category</label>
                                <select name="category" id="category" class="form-control form-control-sm"  required>
                                    <option value="">Select Category</option>
                                    <option value="Cumulative Test" @selected($exam->category == 'Cumulative Test')>Cumulative Test</option>
                                    <option value="Grand Test" @selected($exam->category == 'Grand Test')>Grand Test</option>
                                    <option value="Physics (Weekend)" @selected($exam->category == 'Physics (Weekend)')>Physics (Weekend)</option>
                                    <option value="Chemistry (Weekend)" @selected($exam->category == 'Chemistry (Weekend)')>Chemistry (Weekend)</option>
                                    <option value="Botany (Weekend)" @selected($exam->category == 'Botany (Weekend)')>Botany (Weekend)</option>
                                    <option value="Zoology (Weekend)" @selected($exam->category == 'Zoology (Weekend)')>Zoology (Weekend)</option>
                                    <option value="Term Test-2" @selected($exam->category == 'Term Test-2')>Term Test-2</option>
                                    <option value="UNIT TEST - (CHEMISTRY/BOTANY)" @selected($exam->category == 'UNIT TEST - (CHEMISTRY/BOTANY)')>UNIT TEST - (CHEMISTRY/BOTANY)</option>
                                    <option value="UNIT TEST - (PHYSICS/ZOOLOGY)" @selected($exam->category == 'UNIT TEST - (PHYSICS/ZOOLOGY)')>UNIT TEST - (PHYSICS/ZOOLOGY)</option>
                                    <option value="70% SYLLABUS TEST" @selected($exam->category == '70% SYLLABUS TEST')>70% SYLLABUS TEST</option>
                                    <option value="FULL TEST - 1" @selected($exam->category == 'FULL TEST - 1')>FULL TEST - 1</option>
                                    <option value="FULL TEST - 2" @selected($exam->category == 'FULL TEST - 2')>FULL TEST - 2</option>



                                  
                                </select>
                            </div>

                              

                        
                            
                            


                              <div class="form-group col-lg-2">
                                <label>Test ID <span class="text-danger">(should be unique*)</span></label>
                                <input type="number" name="id" value="{{ $exam->id }}" id="id" class="form-control form-control-sm numberk" disabled>
                            </div>

                              <div class="form-group col-lg-4">
                                <label>Test Name</label>
                                <input type="text" name="name" value="{{ $exam->name }}" id="name" class="form-control form-control-sm" required>
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Start Datetime</label>
                                <input type="text" id="start_at" value="{{ $exam->start_at }}"  name="start_at" class="datetime-picker form-control form-control-sm">
                            </div>
                            
                            <div class="form-group col-lg-4">
                                <label>End Datetime</label>
                                <input type="text" id="end_at" value="{{ $exam->end_at }}" name="end_at" class="datetime-picker form-control form-control-sm">
                                <div id="end_at_error" class="text-danger"></div>
                            </div>

                            <div class="form-group col-lg-3">
                                <label>Result Publish</label>
                                <select name="publish" id="publish" class="form-control form-control-sm" required>
                                    <option value="No" @selected($exam->publish == 'No')>No</option>
                                    <option value="Yes" @selected($exam->publish == 'Yes')>Yes</option>
                                </select>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
@section('js')
<script>
     flatpickr(".datetime-picker", {
        enableTime: true,
        allowInput: true,
        dateFormat: "Y-m-d H:i",
        plugins: [
            new confirmDatePlugin({
                confirmText: "OK",
                showAlways: false,
            })
        ]
    });
    $('#end_at').change(function() {
        $('#end_at_error').text('');
        const startTime = new Date($('#start_at').val());
        const endTime = new Date($(this).val());
        if (startTime >= endTime) {
            $('#end_at_error').text('End time must be greater than start time.');
            $(this).val('');
        }
    })
</script>
@endsection