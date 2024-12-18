@extends('layouts.app')
@section('title', 'Admission')
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

                        <div class="form-group col-lg-3">
                           <label>Name</label>
                            <input type="text" name="name"  class="form-control form-control-sm text-capitalize" required>
                       </div>
                      
                  
        
                 <div class="form-group col-lg-3">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control form-control-sm text-capitalize" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control form-control-sm text-capitalize" required>
                 </div>

                 <!-- Contact Details -->
                 <div class="form-group col-lg-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" required>
                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Mobile No</label>
                    <input type="text" name="mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" required>
                    @error('mob_no')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                 </div>


                 <div class="form-group col-lg-3">
                  <label>Alternate Mobile No</label>
                  <input type="text" name="alternate_mob_no" class="form-control form-control-sm @error('mob_no') is-invalid @enderror" required>
                  @error('mob_no')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
               </div>

                 <div class="form-group col-lg-3">
                  <label>Gender</label>
                  <select name="gender" class="form-control form-control-sm text-capitalize" required>
                     <option value="">Select</option>
                     <option value="Male">Male</option>
                     <option value="Female">Female</option>
                     <option value="other">Other</option>
                  </select>
               </div>

                 <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <input type="text" name="branch" class="form-control form-control-sm text-capitalize" required>
                 </div>

                 <!-- Address -->
                 <div class="form-group col-lg-3">
                  <label>Address line 1</label>
                  <input type="text" name="address_line_1" class="form-control form-control-sm text-capitalize" required>
               </div>

               <div class="form-group col-lg-3">
                  <label>Address line 2</label>
                  <input type="text" name="address_line_2" class="form-control form-control-sm text-capitalize" required>
               </div>

                 <div class="form-group col-lg-3">
                    <label>City</label>
                    <select name="city" id="city" class="form-control form-control-sm" required>
                     <option value="">Select City</option>
                     @foreach ($districts as $district)
                     <option value="{{$district->District}}">{{$district->District}}</option>
                     @endforeach
                   </select>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>State</label>
                   <select name="state" id="state" class="form-control form-control-sm" required>
                 <option value="">Select State</option>
                 @foreach ($states as $state)
                 <option value="{{$state->State}}">{{$state->State}}</option>
                 @endforeach
               </select>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Pincode</label>
                    <input type="number" name="pincode" class="form-control form-control-sm" required>
                 </div>
                 <div class="form-group col-lg-12"><h6> Personal Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                 <!-- Personal Details -->
                 <div class="form-group col-lg-3">
                    <label>Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Employment Type</label>
                    <select name="employment_type" class="form-control form-control-sm" required>
                     <option value="">Select</option>
                       <option value="Full-Time">Full-Time</option>
                       <option value="Part-Time">Part-Time</option>
                       <option value="Contract">Contract</option>
                    </select>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Qualifications</label>
                    <input type="text" name="qualifications" class="form-control form-control-sm text-capitalize" required>
                 </div>

                 <div class="form-group col-lg-3">
                  <label>Blood Group</label>
                  <select name="blood_group" class="form-control form-control-sm">
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
                    <label>Marital Status</label>
                    <select name="marital_status" class="form-control form-control-sm">
                     <option value="">Select</option>
                       <option value="Single">Single</option>
                       <option value="Married">Married</option>
                    </select>
                 </div>
                 <div class="form-group col-lg-12"><h6> Banking Details</h6> <hr style="border-bottom: 1px solid #ccc;"></div>
                 <!-- Banking Details -->
                 <div class="form-group col-lg-3">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control form-control-sm" required>
                 </div>

                 <div class="form-group col-lg-3">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control form-control-sm text-capitalize" required>
                 </div>

               <!-- Upload Fields -->
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
   <label>Certificates</label>
   <input type="file" name="experience_certificates[]" class="form-control form-control-sm" id="experience_certificates" multiple onchange="showFileNamesAndPreviews(this, 'certificatesPreview')">
   
   <div id="certificatesPreview" style="display:none;">
       <ul id="certificatesList"></ul>
   </div>
</div>

<div class="form-group col-lg-3">
   <label>ID Proof</label>
   <input type="file" name="id_proof" class="form-control form-control-sm" id="id_proof" onchange="showFileNameAndPreview(this, 'idProofPreview')">
   
   <div id="idProofPreview" style="display:none;">
       <span id="idProofName"></span>
       <br>
       <a id="idProofLink" href="#" target="_blank" style="display:none;">View ID Proof</a>
   </div>
</div>

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


@endsection