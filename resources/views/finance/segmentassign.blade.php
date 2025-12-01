@extends('layouts.app')

@section('title', 'Segment Assign')

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

            <div class="card-body">
              <form method="get" action="{{ route('assignsegment') }}" enctype="multipart/form-data" class="mb-3" id="sectionForm">
                <div class="row">
                  <div class="col-12 mb-3">
                    <h6 class="col-deep-purple">Segment Assign</h6>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Academic Year</label>
                    <select name="academic_year" class="form-control form-control-sm academic_year select2" required>
                      <option value="">Select Academic Year</option>
                      @foreach ($academicyear as $row)
                      <option value="{{ $row->academic_year }}" {{ request()->academic_year == $row->academic_year ? 'selected' : ''}}>{{ $row->academic_year }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <select name="branch" class="form-control form-control-sm branch select2" required>
                      <option value="">Select Branch</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" {{ $selectedbranch == $branch->id ? 'selected' : ''}}>{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" class="form-control form-control-sm course select2" required>
                      <option value="">Select Course</option>
                      @if($coursesbybranch)
                      @foreach ($coursesbybranch as $course)
                      <option value="{{ $course->course }}" {{ $selectedcourse == $course->course ? 'selected' : ''}}>{{ $course->course }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Batch</label>
                    <select name="batch" class="form-control form-control-sm batch select2" required>
                      <option value="">Select Batch</option>
                      @if($batchesbybranch)
                      @foreach ($batchesbybranch as $batch)
                      <option value="{{ $batch->batch }}" {{ $selectedbatch == $batch->batch ? 'selected' : ''}}>{{ $batch->batch }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>Section</label>
                    <select name="section" class="section select2" required>
                      <option value="">Select Section</option>
                      @if($sectionsbybranch)
                      @foreach ($sectionsbybranch as $section)
                      <option value="{{ $section->section }}" {{ $selectedsection == $section->section ? 'selected' : ''}}>{{ $section->section }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group col-lg-3">
                    <button type="submit" class="btn btn-primary mt-2 mt-lg-4">Get</button>
                    <a href="{{ route('assignsegment') }}" class="btn btn-secondary mt-2 mt-lg-4">Clear</a>
                  </div>
                </div>
              </form>
              @if($students)

              <div class="table-responsive mb-3">
                <table class="table table-sm table-hover" id="myTable">
                  <thead>
                    <tr role="row">
                      <th>
                        <input type="checkbox" id="selectAll" />
                      </th>
                      <th>Student ID</th>
                      <th>Campus</th>
                      <th>Coaching Type</th>
                      <th>Course</th>
                      <th>Batch</th>
                      <th>Section</th>
                      <th>Student Name</th>
                      <th>Gender</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($students as $student)
                    <tr>
                      <td>
                        <input type="checkbox" name="student_ids[]" value="{{$student->id}}" />
                      </td>
                      <td>{{$student->id}}</td>
                      <td>{{$student->campus}}</td>
                      <td>{{$student->coaching_type}}</td>
                      <td>{{$student->course}}</td>
                      <td>{{$student->batch}}</td>
                      <td>{{$student->section}}</td>
                      <td>{{$student->student_name}}</td>
                      <td>{{$student->gender}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <form method="post" id="myForm" action="{{ route('assignsegment') }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                  <div class="form-group col-lg-3">
                    <label>Segment</label>
                    <select name="segment[]" class="form-control form-control-sm select2" required multiple>
                      @foreach ($segments as $segment)
                      <option value="{{ $segment->id }}" {{ request('segment') == $segment->id ? 'selected' : ''}}>{{ $segment->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-12">
                    <button type="submit" class="btn btn-primary">Assign</button>
                  </div>
                </div>
              </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<!-- Add Select2 JavaScript (since it's missing from layout) -->
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
     
  // Initialize DataTables
  const table = $('#myTable').DataTable({
     "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
     columnDefs: [
       { targets: 0, orderable: false }
     ],
  });
  
  // Wait for TomSelect to finish initializing first
   setTimeout(function() {
       
       // Step 1: Destroy any existing TomSelect instances on select2 elements
       $('.select2').each(function() {
           const element = this;
           
           // Check if TomSelect is attached and destroy it
           if (element.tomselect) {
               element.tomselect.destroy();
           }
           
           // Check if Select2 is already attached and destroy it
           if ($(element).hasClass('select2-hidden-accessible')) {
               $(element).select2('destroy');
           }
       });
       
       // Step 2: Initialize Select2 with proper settings
       $('.select2').select2({
           theme: 'default',
           width: '100%',
           placeholder: function() {
               return $(this).find('option:first').text();
           }
       });
  
  let coursejson = @json($courses);
  let batchjson = @json($batches);
  let sectionjson = @json($sections);
  
  let branchselect = $('.branch');
  let courseselect = $('.course');
  let batchselect = $('.batch');
  let sectionselect = $('.section');
  
  // Function to safely reinitialize Select2
       function reinitializeSelect2(element, isModal = false) {
           // Destroy existing instances
           if (element[0].tomselect) {
               element[0].tomselect.destroy();
           }
           if (element.hasClass('select2-hidden-accessible')) {
               element.select2('destroy');
           }
           
           // Reinitialize Select2
           const config = {
               theme: 'default',
               width: '100%',
               placeholder: element.find('option:first').text()
           };
           
           if (isModal) {
               config.dropdownParent = $('#billTypeModal');
           }
           
           element.select2(config);
       }
  
  
  branchselect.on('change', function() {
     let branchId = this.value;
     courseselect.empty();
     batchselect.empty();
     sectionselect.empty();
     
     courseselect.append('<option value="">Select Course</option>');
     coursesorted = coursejson.sort(function(a, b) {
        return a.course.localeCompare(b.course);
     });     
     coursesorted.forEach(function(courseval) {
        if (courseval.campus == branchId) {
           courseselect.append('<option value="' + courseval.course + '">' + courseval.course + '</option>');
        }
     });
  
     reinitializeSelect2(courseselect);
     reinitializeSelect2(batchselect);
     reinitializeSelect2(sectionselect);
  });
  
  courseselect.on('change', function() {
     let branchvalue = branchselect.find('option:selected').val();
     let coursevalue = this.value;
     batchselect.empty();
     sectionselect.empty();
     batchselect.append('<option value="">Select Batch</option>');
     batchsorted = batchjson.sort(function(a, b) {
        return a.batch.localeCompare(b.batch);
     })
     batchsorted.forEach(function(batchval) {
        if (batchval.campus == branchvalue && batchval.course == coursevalue) {
           batchselect.append('<option value="' + batchval.batch + '">' + batchval.batch + '</option>');
        }
     });
  
     reinitializeSelect2(batchselect);
     reinitializeSelect2(sectionselect);
  });
  
  batchselect.on('change', function() {
     let branchvalue = branchselect.find('option:selected').val();
     let coursevalue = courseselect.find('option:selected').val();
     let batchvalue = this.value;
     sectionselect.empty();
     sectionselect.append('<option value="">Select Section</option>');
     sectionsorted = sectionjson.sort(function(a, b) {
        return a.section.localeCompare(b.section);
     })
     sectionsorted.forEach(function(sectionval) {
        if (sectionval.campus == branchvalue && sectionval.course == coursevalue && sectionval.batch == batchvalue) {
           sectionselect.append('<option value="' + sectionval.section + '">' + sectionval.section + '</option>');
        }
     });
  
     reinitializeSelect2(sectionselect);
  });
   }, 500);
  
  
  @if(count($students) > 0)
  $('#selectAll').on('change', function() {
   const isChecked = $(this).prop('checked');
   table.$('input[name="student_ids[]"]').prop('checked', isChecked);
  });
  
  table.rows().every(function() {
       const row = this.node();
       const checkbox = $(row).find('input[name="student_ids[]"]');
       if (checkbox.is(':checked')) {
           selectedIds.add(checkbox.val());
       }
   });
  
  // Update your form submission handler
  $('#myForm').on('submit', function(e) {
   e.preventDefault(); // Prevent default form submission
   
   // Collect all checked student IDs from ALL pages
   const selectedIds = new Set();
   
   // Method 1: Loop through all data (including non-visible)
   table.rows().every(function() {
       const row = this.node();
       const checkbox = $(row).find('input[name="student_ids[]"]');
       if (checkbox.is(':checked')) {
           selectedIds.add(checkbox.val());
       }
   });
   
   if (selectedIds.size === 0) {
       alert('Please select at least one student.');
       return false;
   }
   
   // Add selected IDs as hidden fields
   selectedIds.forEach(function(id) {
       $('#myForm').append(`<input type="hidden" name="student_ids[]" value="${id}" />`);
   });
   
   // Submit the form
   this.submit();
   
  });
  
  @endif
  
  
  
  });
  
</script>

@endsection