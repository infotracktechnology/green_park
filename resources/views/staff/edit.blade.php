@extends('layouts.app')
@section('title', 'Staff Details')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm"  action="{{ route('staff.update', $staff->id) }} " enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple"> Staff Details</h6>
                        </div>
                        <div class="form-group col-lg-12"><h6> Personal Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name"  class="form-control form-control-sm text-capitalize" value="{{ $staff->name }}"  required>
                       </div>
                      
                  
                       <div class="form-group col-lg-3">
                        <label>Staff School Initial</label>
                        <input type="text" name="school_initial" class="form-control form-control-sm text-capitalize" value="{{ $staff->school_initial }}" >
                     </div>  
                       <div class="form-group col-lg-3">
                        <label>Staff Type</label>
                        <select name="staff_type" class="form-control form-control-sm text-capitalize" >
                            <option value="">Select</option>
                            <option value="board" {{ $staff->staff_type == 'board' ? 'selected' : '' }}>Board</option>
                            <option value="Neet" {{ $staff->staff_type == 'Neet' ? 'selected' : '' }}>NEET</option>
                            <option value="cbse" {{ $staff->staff_type == 'cbse' ? 'selected' : '' }}>CBSE</option>
                            <option value="Foundation" {{ $staff->staff_type == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                        </select>
                    </div>
                    


                    <div class="form-group col-lg-3">
                        <label for="branch">Branch</label>
                        <select name="branch_id" id="branch" class="form-control form-control-sm" required>
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @if($branch->id == $staff->branch_id) selected @endif>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    



                    <div class="form-group col-lg-3">
                        <label>Hostel/Dayscholar</label>
                        <select name="hostel_dayscholar" class="form-control form-control-sm text-capitalize" >
                            <option value="">Select</option>
                            <option value="hostel" {{ $staff->hostel_dayscholar == 'hostel' ? 'selected' : '' }}>Hostel</option>
                            <option value="Dayscholar" {{ $staff->hostel_dayscholar == 'Dayscholar' ? 'selected' : '' }}>Days Scholar</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-lg-3">
                        <label>Gender</label>
                        <select name="gender" class="form-control form-control-sm text-capitalize" >
                            <option value="">Select</option>
                            <option value="Male" {{ $staff->gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $staff->gender == 'Female' ? 'selected' : '' }}>Female</option>
                           
                        </select>
                    </div>
                 <div class="form-group col-lg-3">
                  <label>Date of Birth</label>
                  <input type="date" name="dob" id="dob" class="form-control form-control-sm" value="{{ $staff->dob }}"  >   
              </div>
              
              <div class="form-group col-lg-3">
                  <label>Age</label>
                  <input type="text" name="age" id="age" class="form-control form-control-sm" value="{{ $staff->age }}" >
              </div>
    
             
           
           <div class="form-group col-lg-3">
               <label>Blood Group</label>
               <select name="blood_group" class="form-control form-control-sm" >
                   <option value="">Select Blood Group</option>
                   <option value="A+" {{ $staff->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                   <option value="A-" {{ $staff->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                   <option value="B+" {{ $staff->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                   <option value="B-" {{ $staff->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                   <option value="AB+" {{ $staff->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                   <option value="AB-" {{ $staff->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                   <option value="O+" {{ $staff->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                   <option value="O-" {{ $staff->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
               </select>
           </div>
           
           <div class="form-group col-lg-3">
            <label>Department</label>
            <select name="department" class="form-control form-control-sm" tyle="text-transform: uppercase;">
                <option value="">Select Department</option>
                <option value="Physics" {{ $staff->department == 'Physics' ? 'selected' : '' }}>Physics</option>
                <option value="Chemistry" {{ $staff->department == 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
                <option value="Botany" {{ $staff->department == 'Botany' ? 'selected' : '' }}>Botany</option>
                <option value="Zoology" {{ $staff->department == 'Zoology' ? 'selected' : '' }}>Zoology</option>
                <option value="Mathematics" {{ $staff->department == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                <option value="Others" {{ $staff->department == 'Others' ? 'selected' : '' }}>Others</option>
            </select>
        </div>
        


         <div class="form-group col-lg-3">
            <label>Qualifications</label>
            <input type="text" name="qualifications" class="form-control form-control-sm text-capitalize" value="{{ $staff->qualifications }}">
         </div>



         <div class="form-group col-lg-3">
            <label>Nationality</label>
            <select name="nationality" class="form-control form-control-sm">
                <option value="">Select Nationality</option>
                <option value="Indian" {{ $staff->nationality == 'Indian' ? 'selected' : '' }}>Indian</option>
                <option value="Foreign" {{ $staff->nationality == 'Foreign' ? 'selected' : '' }}>Foreign</option>
                <option value="NRI" {{ $staff->nationality == 'NRI' ? 'selected' : '' }}>NRI</option>
            </select>
        </div>
        
        <div class="form-group col-lg-3">
            <label>Religion</label>
            <select name="religion" class="form-control form-control-sm">
                <option value="">Select Religion</option>
                <option value="Hindu" {{ $staff->religion == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                <option value="Christian" {{ $staff->religion == 'Christian' ? 'selected' : '' }}>Christian</option>
                <option value="Muslim" {{ $staff->religion == 'Muslim' ? 'selected' : '' }}>Muslim</option>
            </select>
        </div>
        
        <div class="form-group col-lg-3">
            <label>Community</label>
            <select name="community" class="form-control form-control-sm">
                <option value="">Select Community</option>
                <option value="OC" {{ $staff->community == 'General' ? 'selected' : '' }}>General</option>
                <option value="BC" {{ $staff->community == 'BC' ? 'selected' : '' }}>BC</option>
                <option value="BCM" {{ $staff->community == 'BCM' ? 'selected' : '' }}>BCM</option>
                <option value="MBC" {{ $staff->community == 'MBC' ? 'selected' : '' }}>MBC</option>
                <option value="SC" {{ $staff->community == 'SC' ? 'selected' : '' }}>SC</option>
                <option value="SCA" {{ $staff->community == 'SCA' ? 'selected' : '' }}>SCA</option>
                <option value="ST" {{ $staff->community == 'ST' ? 'selected' : '' }}>ST</option>
            </select>
        </div>
        
  <div class="form-group col-lg-3">
   <label>Caste & Sub-Caste</label>
   <input type="text" name="caste" class="form-control form-control-sm text-capitalize" value="{{ $staff->caste }}" >
</div>


<div class="form-group col-lg-3">
   <label>Mobile No</label>
   <input type="text" name="mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror"  value="{{ $staff->mob_no }}">
   @error('mob_no')
   <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
   @enderror
</div>


<div class="form-group col-lg-3">
 <label>Alternate Mobile No</label>
 <input type="text" name="alternate_mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" value="{{ $staff->alternate_mob_no }}">
 @error('mob_no')
 <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
 @enderror
</div>

<div class="form-group col-lg-3">
   <lable>Aadhaar Card No</lable>
   <input type="text" name="aadhaar_no" class="form-control form-control-sm @error('aadhaar_no') is-invalid @enderror" value="{{ $staff->aadhaar_no }}" >
   @error('aadhaar_no')
   <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
   @enderror
</div>

   <!-- Contact Details -->
   <div class="form-group col-lg-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ $staff->email }}">
      @error('email')
      <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
      @enderror
   </div>





   <div class="form-group col-lg-3">
      <label>Address line 1</label>
      <input type="text" name="address_line_1" class="form-control form-control-sm text-capitalize" value ="{{ $staff->address_line_1 }}" >
   </div>

   <div class="form-group col-lg-3">
      <label>Address line 2</label>
      <input type="text" name="address_line_2" class="form-control form-control-sm text-capitalize" value="{{ $staff->address_line_2 }}" >
   </div>

   {{-- <div class="form-group col-lg-3">
      <label>State</label>
      <select name="state" id="state" onchange="City(this.value);" class="form-control form-control-sm">
          <option value="">Select State</option>
          @foreach ($states as $state)
              <option value="{{ $state->State }}" {{ $staff->state == $state->State ? 'selected' : '' }}>
                  {{ $state->State }}
              </option>
          @endforeach
      </select>
  </div>
  
  <div class="form-group col-lg-3">
      <label>City</label>
      <select name="city" id="city" class="form-control form-control-sm">
          <option value="">Select City</option>
          @if(!empty($districts))
              @foreach ($districts as $city)
                  <option value="{{ $city->District }}" {{ $staff->city == $city->District ? 'selected' : '' }}>
                      {{ $city->District }}
                  </option>
              @endforeach
          @endif
      </select>
  </div>
   --}}

   <div class="form-group col-lg-3">
    <label>State</label>
    <input type="text" name="State" class="form-control form-control-sm" value="{{ $staff->state }}">
 </div>

 <div class="form-group col-lg-3">
    <label>City</label>
    <input type="text" name="city" class="form-control form-control-sm" value="{{ $staff->city }}" >
 </div>

     <div class="form-group col-lg-3">
        <label>Pincode</label>
        <input type="number" name="pincode" class="form-control form-control-sm" value="{{ $staff->pincode }}" >
     </div>

     <div class="form-group col-lg-3">
      <label>Photo</label>
          @if(!empty($staff->photo))
            <a href="{{ env('APP_URL').$staff->photo }}" target="_blank"><i class="fas fa-paperclip"></i> Photo</a>
            @else
            <input type="file" name="photo" class="form-control form-control-sm" id="photo">
          @endif
  </div>
  <div class="form-group col-lg-3">
   <label>Biometric no</label>
   <input type="text" name="biometric_no" class="form-control form-control-sm" value="{{ $staff->biometric_no }}" >
</div>




<div class="form-group col-lg-3">
    <label>Marital Status</label>
    <select name="marital_status" class="form-control form-control-sm" onchange="toggleSpouseDetails(this.value)">
        <option value="">Select</option>
        <option value="Single" {{ $staff->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
        <option value="Married" {{ $staff->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
    </select>
</div>

<!-- Address -->
<div class="form-group col-lg-12">
    <h6>Parent & Spouse Details</h6>
    <hr style="border-bottom: 1px solid #ccc;">
</div>

<div class="form-group col-lg-3">
    <label>Father Name</label>
    <input type="text" name="father_name" class="form-control form-control-sm" value="{{ $staff->father_name }}">
</div>

<div class="form-group col-lg-3">
    <label>Mother Name</label>
    <input type="text" name="mother_name" class="form-control form-control-sm" value="{{ $staff->mother_name }}">
</div>

<div class="form-group col-lg-3">
    <label>Father/Mother Mobile No</label>
    <input type="text" name="father_ph_no" class="form-control form-control-sm" value="{{ $staff->father_ph_no }}">
</div>

<!-- Spouse Details Section -->
  
<div class="form-group col-lg-12">
    <div id="spouse-details" style="display: none;">
     
        <div class="row">
    <div class="form-group col-lg-3">
        <label>Spouse Name</label>
        <input type="text" name="spouse_name" class="form-control form-control-sm" value="{{ $staff->spouse_name }}">
    </div>

    <div class="form-group col-lg-3">
        <label>Spouse Mobile No</label>
        <input type="number" name="spouse_ph_no" class="form-control form-control-sm" value="{{ $staff->spouse_ph_no }}">
    </div>

    <div class="form-group col-lg-3">
        <label>Spouse Occupation</label>
        <input type="text" name="spouse_occupation" class="form-control form-control-sm" value="{{ $staff->spouse_occupation }}">
    </div>
</div>

</div></div>
            
              <div class="form-group col-lg-12"><h6>Working School Details </h6> <hr style="border-bottom: 1px solid #ccc;"></div>



                 <!-- Personal Details -->
                 <div class="form-group col-lg-3">
                    <label>Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control form-control-sm" value= "{{ $staff->date_of_joining }}">
                 </div>

                 <div class="form-group col-lg-3">
                  <label>Designation</label>
                  <select name="designation" class="form-control form-control-sm" >
                    <option value="">Select Designation</option>
                    <option value="BT.ASST" {{ $staff->designation == 'BT.ASST' ? 'selected' : '' }}>BT.ASST</option>
                    <option value="PG.ASST" {{ $staff->designation == 'PG.ASST' ? 'selected' : '' }}>PG.ASST</option>
                    <option value="PET" {{ $staff->designation == 'PET' ? 'selected' : '' }}>PET</option>
                    <option value="SS" {{ $staff->designation == 'SS' ? 'selected' : '' }}>SS</option>
                    <option value="Others" {{ $staff->designation == 'Others' ? 'selected' : '' }}>Others</option>
                    <option value="Neet Faculty" {{ $staff->designation == 'Neet Faculty' ? 'selected' : '' }}>Neet Faculty</option>
                    <option value="Foundation Faculty" {{ $staff->designation == 'Foundation Faculty' ? 'selected' : '' }}>Foundation Faculty</option>
                  </select>
                </div>



                <div class="form-group col-lg-3">
                    <label>Experience in Years(GP)</label>
                    <input type="number" name="experience" class="form-control form-control-sm"  value="{{ $staff->experience }}">
                 </div>
  
                 <div class="form-group col-lg-3">
                  <label>Experience in Month(GP)</label>
                  <input type="number" name="experience_month" class="form-control form-control-sm" value="{{ $staff->experience_month }}">
               </div>


              <div class="form-group col-lg-3">
  <label>Class Handling Type</label>
  <select name="class_handling_type" class="form-control form-control-sm" >
    <option value="">Select Type</option>
    <option value="Class Handling" {{ $staff->class_handling_type == 'Class Handling' ? 'selected' : '' }}>Class Handling</option>
    <option value="Free Staff" {{ $staff->class_handling_type == 'Free Staff' ? 'selected' : '' }}>Free Staff</option>
    <option value="Others" {{ $staff->class_handling_type == 'Others' ? 'selected' : '' }}>Others</option>
  </select>
</div>


<div class="form-group col-lg-3">
   <label>Name of the Previous School Worked</label>
   <input type="text" name="previous_School" class="form-control form-control-sm" value="{{ $staff->previous_school }}" >
  </div>
   

  <div class="form-group col-lg-12">
    <h6>Staff Children's Details (Note: If Studying in Our School)</h6>
    <hr style="border-bottom: 1px solid #ccc;">
</div>

<!-- Checkbox to toggle visibility -->
<div class="form-group col-lg-3">
    <label>Children Studying in Our School?</label>
    <div>
        <input type="checkbox" id="childrenCheckbox" onchange="toggleChildrenDetails()" {{ $staff->children_studying == 1 ? 'checked' : '' }}>
        <label for="childrenCheckbox">Yes</label>
        <input type="hidden" id="childrenValue" name="children_studying" value="{{ $staff->children_studying }}">
    </div>
</div>

<!-- Child details container (Initially hidden) -->
<div id="childrenDetailsSection" style="display: {{ $staff->children_studying == 1 ? 'block' : 'none' }};">
    <button type="button" id="addItemButton" class="btn btn-warning">Add children’s detail</button>
    <div id="itemContainer">
        @if(!empty($staff->children_details))
            @foreach($staff->children_details as $index => $child)
                <div class="col-md-12 row mb-3 itemRow">
                    <div class="col-md-3 form-group">
                        <label>Children's Name</label>
                        <input type="text" name="children[{{ $index }}][name]" class="form-control form-control-sm" value="{{ old("children.$index.name", $child['name'] ?? '') }}" />
                    </div>

                    <div class="col-md-2 form-group">
                        <label>Class</label>
                        <input type="text" name="children[{{ $index }}][class]" class="form-control form-control-sm" value="{{ old("children.$index.class", $child['class'] ?? '') }}" />
                    </div>

                    <div class="col-md-2 form-group">
                        <label>Section</label>
                        <input type="text" name="children[{{ $index }}][section]" class="form-control form-control-sm" value="{{ old("children.$index.section", $child['section'] ?? '') }}" />
                    </div>

                    <div class="col-md-1 form-group">
                        <button type="button" class="btn btn-danger mt-4 removeRow">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Template for New Child -->
<template id="itemTemplate">
    <div class="col-md-12 row mb-3 itemRow">
        <div class="col-md-3 form-group">
            <label>Children's Name</label>
            <input type="text" name="children[][name]" class="form-control form-control-sm" />
        </div>

        <div class="col-md-2 form-group">
            <label>Class</label>
            <input type="text" name="children[][class]" class="form-control form-control-sm" />
        </div>

        <div class="col-md-2 form-group">
            <label>Section</label>
            <input type="text" name="children[][section]" class="form-control form-control-sm" />
        </div>

        <div class="col-md-1 form-group">
            <button type="button" class="btn btn-danger mt-4 removeRow">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
</template>

      
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
    function toggleSpouseDetails(status) {
        const spouseDetails = document.getElementById('spouse-details');
        spouseDetails.style.display = (status === 'Married') ? 'block' : 'none';

        if (status !== 'Married') {
            ['spouse_name', 'spouse_ph_no', 'spouse_occupation'].forEach(name => 
                document.querySelector(`[name="${name}"]`).value = ''
            );
        }
    }

    document.addEventListener("DOMContentLoaded", () => 
        toggleSpouseDetails(document.querySelector('select[name="marital_status"]').value)
    );
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Toggle children details section based on checkbox state
    function toggleChildrenDetails() {
        const childrenDetailsSection = document.getElementById("childrenDetailsSection");
        const childrenValue = document.getElementById("childrenValue");
        const checkbox = document.getElementById("childrenCheckbox");

        if (!childrenDetailsSection || !childrenValue || !checkbox) return;

        childrenDetailsSection.style.display = checkbox.checked ? "block" : "none";
        childrenValue.value = checkbox.checked ? "1" : "0";
    }

    // Attach event listener for the checkbox
    let childrenCheckbox = document.getElementById("childrenCheckbox");
    if (childrenCheckbox) {
        childrenCheckbox.addEventListener("change", toggleChildrenDetails);
        toggleChildrenDetails(); // Ensure correct initial state on page load
    }

    // Add new child row
    let addItemButton = document.getElementById("addItemButton");
    let itemContainer = document.getElementById("itemContainer");
    let itemTemplate = document.getElementById("itemTemplate")?.content;

    if (addItemButton && itemContainer && itemTemplate) {
        addItemButton.addEventListener("click", function () {
            let newItem = itemTemplate.cloneNode(true);

            // Find and update input fields with the correct index
            let index = document.querySelectorAll(".itemRow").length;
            newItem.querySelector('[name="children[][name]"]').setAttribute("name", `children[${index}][name]`);
            newItem.querySelector('[name="children[][class]"]').setAttribute("name", `children[${index}][class]`);
            newItem.querySelector('[name="children[][section]"]').setAttribute("name", `children[${index}][section]`);

            itemContainer.appendChild(newItem);
        });
    }

    // Remove a child row and re-index remaining rows
    document.addEventListener("click", function (e) {
        if (e.target.closest(".removeRow")) {
            e.target.closest(".itemRow").remove();
            reIndexRows();
        }
    });

    function reIndexRows() {
        const rows = document.querySelectorAll(".itemRow");
        rows.forEach((row, index) => {
            row.querySelector('[name*="[name]"]').setAttribute("name", `children[${index}][name]`);
            row.querySelector('[name*="[class]"]').setAttribute("name", `children[${index}][class]`);
            row.querySelector('[name*="[section]"]').setAttribute("name", `children[${index}][section]`);
        });
    }
});

</script>



<script>
   
   function showFileNameAndPreview(input, previewId) {
       const file = input.files[0];
       const fileName = file ? file.name : "";
       const previewDiv = document.getElementById(previewId);
       
   const nameElement = previewDiv.querySelector('span');
       nameElement.textContent = "File: " + fileName;

      if (file && file.type.startsWith("image/")) {
           const imgPreview = previewDiv.querySelector('img');
           const reader = new FileReader();
           reader.onload = function(e) {
               imgPreview.src = e.target.result;
               imgPreview.style.display = "block";
           };
           reader.readAsDataURL(file);
       }

       previewDiv.style.display = "block";
   }
function showFileNamesAndPreviews(input, previewId) {
       const files = input.files;
       const previewDiv = document.getElementById(previewId);
       const list = previewDiv.querySelector('ul');
       list.innerHTML = ""; 
       
       
       for (let i = 0; i < files.length; i++) {
           const file = files[i];
           const listItem = document.createElement('li');
           listItem.textContent = file.name;

           
           if (file.type.startsWith("image/")) {
               const img = document.createElement('img');
               img.style.maxWidth = "100px";
               img.style.maxHeight = "100px";
               const reader = new FileReader();
               reader.onload = function(e) {
                   img.src = e.target.result;
               };
               reader.readAsDataURL(file);
               listItem.appendChild(img);
           } else if (file.type === "application/pdf") {
               const link = document.createElement('a');
               link.href = URL.createObjectURL(file);
               link.textContent = "View PDF";
               link.target = "_blank";
               listItem.appendChild(link);
           }

           list.appendChild(listItem);
       }
       previewDiv.style.display = "block";
   }


</script>
<script>
   function City(state) {
      if(!state) return;
      $.get("{{ route('staff.create') }}", {state: state}, function(data) {
          var html = '<option value="">Select City</option>';
          $.each(data, function(key, value) {
              html += '<option value="' + value.District + '">' + value.District + '</option>';
          });
          $('#city').html(html);
      });
   }

//    window.onload = function() {
//      if ("{{ $staff->state }}") {
//        City("{{ $staff->state }}");
//      }
//    };
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

{{-- <div class="form-group col-lg-3">
   <label>Paper Correction</label>
   <select name="paper_correction" class="form-control form-control-sm" >
     <option value="">Select Type</option>
     <option value="Yes" {{ $staff->paper_correction == 'Yes' ? 'selected' : '' }}>Yes</option>
     <option value="No" {{ $staff->paper_correction == 'No' ? 'selected' : '' }}>No</option>
   </select>
 </div>
           


<div class="form-group col-lg-3">
 <label>Handeling Class & Sec</label>
 <input type="text" name="handeling_class" class="form-control form-control-sm" value="{{ $staff->handeling_class }}">
</div> --}}