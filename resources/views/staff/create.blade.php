@extends('layouts.app')
@section('title', 'Staff Details')
@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm"  action="{{ route('staff.store') }}" enctype="multipart/form-data">
                        @csrf
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            <h6 class="col-deep-purple"> Staff Details</h6>
                        </div>
                        <div class="form-group col-lg-12"><h6> Personal Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name"  class="form-control form-control-sm text-capitalize " required>
                       </div>
                      
                  
        
                 <div class="form-group col-lg-3">
                    <label>Staff School Initial</label>
                    <input type="text" name="school_initial" class="form-control form-control-sm text-capitalize"  >
                 </div>


                 <div class="form-group col-lg-3">
                  <label>Staff Type </label>
                  <select name="staff_type" class="form-control form-control-sm text-capitalize"  >
                     <option value="">Select</option>
                     <option value="board">Board</option>
                     <option value="Neet">NEET</option>
                     <option value="cbse">CBSE</option>
                     <option value="Foundation">Foundation</option>
                  </select>
               </div>




               <div class="form-group col-lg-3">
                  <label>Hostel/Dayscholar </label>
                  <select name="hostel_dayscholar" class="form-control form-control-sm text-capitalize"  >
                     <option value="">Select</option>
                     <option value="hostel">Hostel</option>
                     <option value="Dayscholar">Dayscholar</option>
                     
                  </select>
               </div>





                 <div class="form-group col-lg-3">
                  <label>Gender</label>
                  <select name="gender" class="form-control form-control-sm text-capitalize"  >
                     <option value="">Select</option>
                     <option value="Male">Male</option>
                     <option value="Female">Female</option>
                    
                  </select>
               </div>
                 <div class="form-group col-lg-3">
                  <label>Date of Birth</label>
                  <input type="date" name="dob" id="dob" class="form-control form-control-sm"  >   
              </div>
              
              <div class="form-group col-lg-3">
                  <label>Age</label>
                  <input type="text" name="age" id="age" class="form-control form-control-sm" >
              </div>
    

            <div class="form-group col-lg-3">
               <label>Blood Group</label>
               <select name="blood_group" class="form-control form-control-sm" >
                   <option value="">Select Blood Group</option>
                   <option value="A+">A+</option>
                   <option value="A-">A-</option>
                   <option value="B+">B+</option>
                   <option value="B-">B-</option>
                   <option value="AB+">AB+</option>
                   <option value="AB-">AB-</option>
                   <option value="O+">O+</option>
                   <option value="O-">O-</option>
               </select>
           </div>

           <div class="form-group col-lg-3">
            <label>Department</label>
            <select name="department" class="form-control form-control-sm" style="text-transform: uppercase;">
                <option value="">Select Department</option>
                <option value="Physics">Physics</option>
                <option value="Chemistry">Chemistry</option>
                <option value="Botany">Botany</option>
                <option value="Zoology">Zoology</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Others">Others</option>
            </select>
        </div>


         <div class="form-group col-lg-3">
            <label>Qualifications</label>
            <input type="text" name="qualifications" class="form-control form-control-sm text-capitalize" >
         </div>



         <div class="form-group col-lg-3">
            <label>Nationality</label>
            <select name="nationality" class="form-control form-control-sm" >
                <option value="">Select Nationality</option>
                <option value="Indian">Indian</option>
                <option value="Foreign">Foreign</option>
                <option value="NRI" >NRI</option>
            </select>
        </div>
           

        <div class="form-group col-lg-3">
         <label>Religion</label>
         <select name="religion" class="form-control form-control-sm" >
             <option value="">Select Religion</option>
             <option value="Hindu">Hindu</option>
             <option value="Christian">Christian</option>
             <option value="Muslim">Muslim</option>
         </select>
     </div>

     <div class="form-group col-lg-3">
      <label>Community</label>
      <select name="community" class="form-control form-control-sm" >
          <option value="">Select Community</option>
          <option value="General" >General</option>
          <option value="BC">BC</option>
          <option value="BCM">BCM</option>
          <option value="MBC">MBC</option>
          <option value="SC">SC</option>
          <option value="SCA">SCA</option>
          <option value="ST">ST</option>
      </select>
  </div>

  <div class="form-group col-lg-3">
   <label>Caste & Sub-Caste</label>
   <input type="text" name="caste" class="form-control form-control-sm text-capitalize" >
</div>


<div class="form-group col-lg-3">
   <label>Mobile No</label>
   <input type="text" name="mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" >
   @error('mob_no')
   <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
   @enderror
</div>


<div class="form-group col-lg-3">
 <label>Alternate Mobile No</label>
 <input type="text" name="alternate_mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" >
 @error('mob_no')
 <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
 @enderror
</div>

<div class="form-group col-lg-3">
   <lable>Aadhaar Card No</lable>
   <input type="text" name="aadhaar_no" class="form-control form-control-sm @error('aadhaar_no') is-invalid @enderror" >
   @error('aadhaar_no')
   <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
   @enderror
</div>

   <!-- Contact Details -->
   <div class="form-group col-lg-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control form-control-sm">
   </div>





   <div class="form-group col-lg-3">
      <label>Address line 1</label>
      <input type="text" name="address_line_1" class="form-control form-control-sm text-capitalize" >
   </div>

   <div class="form-group col-lg-3">
      <label>Address line 2</label>
      <input type="text" name="address_line_2" class="form-control form-control-sm text-capitalize" >
   </div>

   {{-- <div class="form-group col-lg-3">
      <label>State</label>
     <select name="state" id="state" onchange="City(this.value);" class="form-control form-control-sm" >
   <option value="">Select State</option>
   @foreach ($states as $state)
   <option value="{{$state->State}}">{{$state->State}}</option>
   @endforeach
 </select>
   </div>

     <div class="form-group col-lg-3">
        <label>City</label>
        <select name="city" id="city" class="form-control form-control-sm" >
         <option value="">Select City</option>
       </select>
     </div> --}}

     <div class="form-group col-lg-3">
        <label>State</label>
        <input type="text" name="state" class="form-control form-control-sm" >
     </div>

     <div class="form-group col-lg-3">
        <label>City</label>
        <input type="text" name="city" class="form-control form-control-sm" >
     </div>




     <div class="form-group col-lg-3">
        <label>Pincode</label>
        <input type="number" name="pincode" class="form-control form-control-sm" >
     </div>

     <div class="form-group col-lg-3">
      <label>Photo</label>
      <input type="file" name="photo" class="form-control form-control-sm" id="photo" onchange="showFileNameAndPreview(this, 'photoPreview')">
      <!-- Show photo file name and preview -->
      <div id="photoPreview" style="display:none;">
          <span id="photoName"></span>
          <br>
          <img id="photoImage" src="" alt="Photo Preview" style="max-width: 100px; max-height: 100px; display:none;">
      </div>
   </div>

   <div class="form-group col-lg-3">
    <label>Biometric no</label>
    <input type="text" name="biometric_no" class="form-control form-control-sm" >
 </div>


 <div class="form-group col-lg-3">
    <label>Marital Status</label>
    <select name="marital_status" class="form-control form-control-sm"  onchange="toggleSpouseDetails(this.value)">
     <option value="">Select</option>
       <option value="Single">Single</option>
       <option value="Married">Married</option>
    </select>
 </div>

              
                
                 <div class="form-group col-lg-12"><h6> Parent & Spouse Details </h6> <hr style="border-bottom: 1px solid #ccc;"></div>

                 <div class="form-group col-lg-3">
                  <label>Father Name</label>
                   <input type="text" name="father_name"  class="form-control form-control-sm">
                 
              </div>
              <div class="form-group col-lg-3">
               <label>Mother Name</label>
                <input type="text" name="mother_name"  class="form-control form-control-sm" >
              
           </div>

           <div class="form-group col-lg-3">
            <label>Father/Mother Mobile No</label>
             <input type="text" name="father_ph_no"  class="form-control form-control-sm">
            
        </div>
    

         
        <div class="form-group col-lg-12">
            <div id="spouse-details" style="display: none;">
             
                <div class="row">
                    <div class="form-group col-lg-3">
                        <label>Spouse Name</label>
                        <input type="text" name="spouse_name" class="form-control form-control-sm">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Spouse Mobile No</label>
                        <input type="number" name="spouse_ph_no" class="form-control form-control-sm">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Spouse Occupation</label>
                        <input type="text" name="spouse_occupation" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>
   



            
              <div class="form-group col-lg-12"><h6>Working School Details </h6> <hr style="border-bottom: 1px solid #ccc;"></div>

                 <!-- Personal Details -->
                 <div class="form-group col-lg-3">
                    <label>Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control form-control-sm" >
                 </div>
                 <div class="form-group col-lg-3">
                  <label>Designation</label>
                  <select name="designation" class="form-control form-control-sm" >
                      <option value="">Select Designation</option>
                      <option value="BT.ASST">BT.ASST</option>
                      <option value="PG.ASST">PG.ASST</option>
                      <option value="PET">PET</option>
                      <option value="SS">SS</option>
                      <option value="Neet Faculty">Neet Faculty</option>
                      <option value="Foundation Faculty">Foundation Faculty</option>
                      <option value="Others">Others</option>
                  </select>
              </div>
              



               <div class="form-group col-lg-3">
                  <label>Experience in Years(GP)</label>
                  <input type="number" name="experience" class="form-control form-control-sm" >
               </div>

               <div class="form-group col-lg-3">
                <label>Experience in Month(GP)</label>
                <input type="number" name="experience_month" class="form-control form-control-sm" >
             </div>


               <div class="form-group col-lg-3">
                  <label>Class Handling Type</label>
                  <select name="class_handling_type" class="form-control form-control-sm" >
                      <option value="">Select Type</option>
                      <option value="Class Handling">Class Handling</option>
                      <option value="Free Staff">Free Staff</option>
                      <option value="Others">Others</option>
                  </select>
              </div>

              <div class="form-group col-lg-3">
                <label>Name of the Previous School Worked</label>
                <input type="text" name="previous_School" class="form-control form-control-sm" >
               </div>
           
       
                 

               <div class="form-group col-lg-12">
                <h6>Staff Children's Details (Note: If Studying in Our School)</h6>
                <hr style="border-bottom: 1px solid #ccc;">
            </div>
            
            <!-- Checkbox to toggle visibility -->
            <div class="form-group col-lg-3">
                <label>Children Studying in Our School?</label>
                <div>
                    <input type="checkbox" id="childrenCheckbox" onchange="toggleChildrenDetails()">
                    <label for="childrenCheckbox">Yes</label>
                    <input type="hidden" id="childrenValue" name="children_studying" value="0">
                </div>
            </div>
            
            <!-- Child details container (Initially hidden) -->
            <div id="childrenDetailsSection" style="display: none;">
                <button type="button" id="addItemButton" class="btn btn-warning">Add children’s detail</button>
                <div id="itemContainer"></div>
            </div>
            
            <!-- Template for Cloning -->
            <template id="itemTemplate">
                <div class="col-md-12 row mb-3 itemRow">
                    <div class="col-lg-4 form-group">
                        <label class="col-blue">Children's Name</label>
                        <input type="text" name="children[][name]" class="form-control form-control-sm" />
                    </div>
            
                    <div class="col-md-3 form-group">
                        <label class="col-blue">Class</label>
                        <input type="text" name="children[][class]" class="form-control form-control-sm" />
                    </div>
            
                    <div class="col-md-3 form-group">
                        <label class="col-blue">Section</label>
                        <input type="text" name="children[][section]" class="form-control form-control-sm" />
                    </div>
            
                    <div class="col-md-1 form-group">
                        <button type="button" class="btn btn-danger mt-4 removeRow">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            </template>
            
            
            
        
        <!-- JavaScript -->
       



      
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
    function toggleChildrenDetails() {
        const checkbox = document.getElementById('childrenCheckbox');
        const childrenValue = document.getElementById('childrenValue');
        const childrenDetailsSection = document.getElementById('childrenDetailsSection');
        const itemContainer = document.getElementById('itemContainer');

        if (checkbox.checked) {
            childrenDetailsSection.style.display = 'block';
            childrenValue.value = "1"; // Set to 1 when checked
        } else {
            childrenDetailsSection.style.display = 'none';
            childrenValue.value = "0"; // Set to 0 when unchecked
            itemContainer.innerHTML = ''; // Clear all added fields
        }
    }

    document.getElementById("addItemButton").addEventListener("click", function() {
        const template = document.getElementById("itemTemplate").content.cloneNode(true);
        const index = document.querySelectorAll(".itemRow").length;

        // Update the names of the input fields with the current index
        template.querySelector('[name="children[][name]"]').setAttribute("name", `children[${index}][name]`);
        template.querySelector('[name="children[][class]"]').setAttribute("name", `children[${index}][class]`);
        template.querySelector('[name="children[][section]"]').setAttribute("name", `children[${index}][section]`);

        // Add event listener for remove button
        template.querySelector(".removeRow").addEventListener("click", function() {
            this.closest(".itemRow").remove();
        });

        // Append the new item to the container
        document.getElementById("itemContainer").appendChild(template);
    });

    // Collect children data as JSON
    function collectChildrenData() {
        const children = [];
        const rows = document.querySelectorAll(".itemRow");

        rows.forEach(row => {
            const name = row.querySelector('input[name*="[name]"]').value;
            const className = row.querySelector('input[name*="[class]"]').value;
            const section = row.querySelector('input[name*="[section]"]').value;

            children.push({ name, class: className, section });
        });

        return children;
    }

    // Submit form data
    function submitForm() {
        const childrenDetails = collectChildrenData();
        const childrenStudying = document.getElementById("childrenValue").value;

        // Use AJAX to send the data to the server
        fetch("/save-staff", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ 
                children_studying: childrenStudying, 
                children_details: childrenDetails 
            })
        })
        .then(response => response.json())
        .then(data => console.log("Success:", data))
        .catch(error => console.error("Error:", error));
    }
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














{{-- <script>
   document.getElementById("addItemButton").addEventListener("click", function() {
       const template = document.getElementById("itemTemplate").content.cloneNode(true);
       const index = document.querySelectorAll(".itemRow").length;

             // Update the names of the input fields with the current index
       template.querySelector('[name="children[][name]"]').setAttribute("name", `children[${index}][name]`);
       template.querySelector('[name="children[][class]"]').setAttribute("name", `children[${index}][class]`);
       template.querySelector('[name="children[][section]"]').setAttribute("name", `children[${index}][section]`);

       document.getElementById("itemContainer").appendChild(template);
   });
   
   document.addEventListener("click", function(e) {
       if (e.target.classList.contains("removeRow")) {
           e.target.closest(".itemRow").remove();
       }
   });
   
   function collectChildrenData() {
       const children = [];
       const rows = document.querySelectorAll(".itemRow");
       rows.forEach(row => {
           const name = row.querySelector('[name="children_name[]"]').value;
           const className = row.querySelector('[name="class[]"]').value;
           const section = row.querySelector('[name="section[]"]').value;
   
           children.push({ name, class: className, section });
       });



       return JSON.stringify(children);
   }
   
   function submitForm() {
       const childrenDetails = collectChildrenData();
       // Use AJAX to send the data to the server
       fetch("/save-staff", {
           method: "POST",
           headers: { "Content-Type": "application/json" },
           body: JSON.stringify({ children_details: childrenDetails })
       })
       .then(response => response.json())
       .then(data => console.log(data))
       .catch(error => console.error("Error:", error));
   }
   </script> --}}


  {{-- <div class="form-group col-lg-3">
               <label>Paper Correction</label>
               <select name="paper_correction" class="form-control form-control-sm" >
                   <option value="">Select Type</option>
                   <option value="Yes">Yes</option>
                   <option value="No">No</option>
                   
               </select>
           </div> --}}
           

{{-- 
<div class="form-group col-lg-3">
 <label>Handeling Class & Sec</label>
 <input type="text" name="handeling_class" class="form-control form-control-sm" >
</div> --}}
 


{{-- 
                 <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <input type="text" name="branch" class="form-control form-control-sm text-capitalize" >
                 </div> --}}

   {{-- <div class="form-group col-lg-3">
                    <label>Employment Type</label>
                    <select name="employment_type" class="form-control form-control-sm" >
                     <option value="">Select</option>
                       <option value="Full-Time">Full-Time</option>
                       <option value="Part-Time">Part-Time</option>
                       <option value="Contract">Contract</option>
                    </select>
                 </div> --}}

                 {{-- <div class="form-group col-lg-12"><h6> Banking Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                 <!-- Banking Details -->
                 <div class="form-group col-lg-3">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control form-control-sm" >
                 </div>

                 <div class="form-group col-lg-3">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control form-control-sm" >
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control form-control-sm text-capitalize" >
                 </div> --}}

               <!-- Upload Fields -->


{{-- <div class="form-group col-lg-3">
   <label>Certificates</label>
   <input type="file" name="experience_certificates[]" class="form-control form-control-sm" id="experience_certificates" multiple onchange="showFileNamesAndPreviews(this, 'certificatesPreview')">
   
   <div id="certificatesPreview" style="display:none;">
       <ul id="certificatesList"></ul>
   </div>
</div> --}}
{{-- 
<div class="form-group col-lg-3">
   <label>ID Proof</label>
   <input type="file" name="id_proof" class="form-control form-control-sm" id="id_proof" onchange="showFileNameAndPreview(this, 'idProofPreview')">
   
   <div id="idProofPreview" style="display:none;">
       <span id="idProofName"></span>
       <br>
       <a id="idProofLink" href="#" target="_blank" style="display:none;">View ID Proof</a>
   </div>
</div> --}}











