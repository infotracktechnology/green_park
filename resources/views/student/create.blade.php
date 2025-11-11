@extends('layouts.app')
@section('title', 'Students')
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary" x-data="app">
            <form method="post" id="myForm" action="{{ route('student.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Add Students</h6>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Admission Date</label>
                    <input type="date" name="admission_date" class="form-control form-control-sm">
                  </div>

                  <div class="form-group col-lg-3">
                    <label for="branch_id">Course</label>
                    <select name="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>


                  <div class="form-group col-lg-3">
                    <label for="branch_id">Campus</label>
                    <select name="campus" class="form-control form-control-sm" required>
                      <option value="" disabled selected>Select Campus</option>
                      @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Coaching Type</label>
                    <select name="coaching_type" id="coaching_type" class="form-control form-control-sm" required>
                      <option value="">Select Coaching Type</option>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}">{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Hostel/Dayscholar</label>
                    <select name="hostel_dayscholar" id="hostel_dayscholar" class="form-control form-control-sm" required>
                      <option value="Dayscholar">Dayscholar</option>
                      <option value="Hostel">Hostel</option>

                    </select>
                  </div>



                  <div class="form-group col-lg-3">
                    <label>Student Name</label>
                    <input type="text" name="student_name" class="form-control form-control-sm  @error('student_name') is-invalid @enderror" required>
                    @error('student_name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>





                  <div class="form-group col-lg-3">
                    <label>Gender</label>
                    <select name="gender" class="form-control form-control-sm" required>
                      <option value="">Select Gender</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control form-control-sm" required>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Age</label>
                    <input type="text" name="age" id="age" class="form-control form-control-sm" required>
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Father Name</label>
                    <input type="text" name="father_name" class="form-control form-control-sm @error('father_name') is-invalid @enderror" required>
                    @error('father_name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Father Mobile No</label>
                    <input type="text" name="father_ph_no" class="form-control form-control-sm @error('father_ph_no') is-invalid @enderror" required>
                    @error('father_ph_no')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Mother Name</label>
                    <input type="text" name="mother_name" class="form-control form-control-sm @error('mother_name') is-invalid @enderror" required>
                    @error('mother_name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>



                  <div class="form-group col-lg-3">
                    <label>Mother Mobile No</label>
                    <input type="text" name="mother_ph_no" class="form-control form-control-sm">
                  </div>


                  <div class="form-group col-lg-3">
                    <label>Bill Type</label>
                    <select name="institution_bill_type" id="bill_type" class="form-control form-control-sm" required>
                      <option value="">Select Bill Type </option>
                      <option value="GPCC,NKL">GPCC,NKL</option>
                      <option value="GPI,NKL">GPI,NKL</option>
                      <option value="GPCI,NKL">GPCI,NKL</option>
                      <option value="GPCI,KARUR">GPCI,KARUR</option>
                      <option value="GPCI,ERODE">GPCI,ERODE</option>
                      <option value="GPCA,COIMBATORE">GPCA,COIMBATORE</option>
                      <option value="GPA,CHENNAI">GPA,CHENNAI</option>
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

@section('js')
<script>
  document.getElementById('coaching_type').addEventListener('change', function() {
      var selectedCoachingType = this.value;
      var hostelSelect = document.getElementById('hostel_dayscholar');
  
      if (selectedCoachingType === 'Offline') {
          hostelSelect.disabled = false;  
      } else {
          hostelSelect.value = '';
          hostelSelect.disabled = true;   
      }
  });
  
  // Initial check if the page loads with a predefined value
  window.onload = function() {
      var selectedCoachingType = document.getElementById('coaching_type').value;
      var hostelSelect = document.getElementById('hostel_dayscholar');
      
      if (selectedCoachingType !== 'Offline') {
          hostelSelect.disabled = true;  // Disable if the value is not "Offline"
      }
  };
</script>


<script>
  document.getElementById('dob').addEventListener('change', function () {
      const dob = new Date(this.value); // Get the selected date of birth
      const today = new Date(); // Get the current date
      
      let age = today.getFullYear() - dob.getFullYear(); // Calculate the year difference
      const monthDiff = today.getMonth() - dob.getMonth(); 
      const dayDiff = today.getDate() - dob.getDate();
  
      // Adjust the age if the birth date hasn't occurred yet this year
      if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
          age--;
      }
  
      // Display the calculated age in the "Age" field
      document.getElementById('age').value = age > 0 ? age : 0; // Ensure age is non-negative
  });
</script>


@endsection