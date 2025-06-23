 <div class="mb-3 d-flex flex-column h-100" >
     <div class="mb-3 client-info w-100 card shadow p-3">
         <div class="mb-3 w-100 ">
             <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
             <div class="group d-flex align-items-center justify-content-center gap-2">
                 <input type="text" class="form-control" name="lna class=" form-control placeholder="LastName">
                 <input type="text" class="form-control" name="fname" placeholder="FirstName">
                 <input type="text" class="form-control" name="MI" placeholder="Middle Initial">
             </div>
         </div>
         <div class="box d-flex align-items-center w-100 gap-2">
             <div class="mb-3 w-50">
                 <label for="spouse_date_of_birth">Date of Birth</label>
                 <input type="date" name="spouse_date_of_birth" class="form-control">
             </div>
             <div class="mb-3 w-50">
                 <label for="spouse_age">Age</label>
                 <input type="number" name="spouse_age" class="form-control">
             </div>
             <div class="mb-3 w-50">
                 <label for="occupation">Occupation</label>
                 <input type="text" name="spouse_occupation" class="form-control">
             </div>
         </div>
         <!-- children info -->
         <div class="box d-flex align-items-center gap-2 border-bottom mb-3">
             <div class="mb-3 d-flex align-items-center">
                 <label for="No_of_children" class="text-nowrap">No. Of Living Children:</label>
                 <input type="number" name="number_of_children" class="form-control">
             </div>
             <div class="mb-3 d-flex gap-2 align-items-center">
                 <label for="" class="form-label text-nowrap mb-0">Plan To Have More Children?</label>
                 <div class="radio-con d-flex gap-2 form-radio">
                     <input type="radio" name="plans" id="children_plan_yes">
                     <label for="children_plan_yes">Yes</label>
                     <input type="radio" name="plans" id="children_plan_no">
                     <label for="children_plan_no">No</label>
                 </div>
             </div>
             <div class="mb-3 d-flex align-items-center gap-1">
                 <label for="montly_income" class="text-nowrap">Average Monthly Income</label>
                 <input type="number" class="form-control">
             </div>
         </div>
         <!-- type of patient info -->
         <div class="box d-flex justify-content-between type-of-client">
             <div class="type-of-client">
                 <h4 class="text-nowrap">Type of Client</h4>
                 <div class="type-of-user-inputs">
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="type-of-patient" id="new-acceptor">
                         <label for="new-acceptor">New Acceptor</label>
                     </div>
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="type-of-patient" id="current-user">
                         <label for="current-user">Current User</label>
                     </div>
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="type-of-patient" id="current-method">
                         <label for="current-method">Current Method</label>
                     </div>
                     <!-- new clinic -->
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="type-of-patient" id="changing-clinic">
                         <label for="changing-clinic">Changing Clinic</label>
                     </div>
                     <!-- dropout -->
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="type-of-patient" id="dropout-restart">
                         <label for="dropout-restart">Dropout/Restart</label>
                     </div>
                 </div>
             </div>
             <!-- reasons -->
             <div class="client-reasons">
                 <h4 class="text-nowrap">Client Reason</h4>
                 <!-- fp of new user -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-2">
                         <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                         <div class="answers d-flex gap-2">
                             <input type="radio" name="FP">
                             <label for="">spacing</label>
                             <input type="radio" name="FP">
                             <label for="">limiting</label>
                             <input type="radio" name="FP">
                             <label for="">others</label>
                             <input type="text" class="flex-grow-1">
                         </div>
                     </div>
                 </div>
                 <!-- FP of current user -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-2">
                         <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                         <div class="answers d-flex gap-2">
                             <input type="radio" name="FP-current-user">
                             <label for="">spacing</label>
                             <input type="radio" name="FP-current-user">
                             <label for="">limiting</label>
                             <input type="radio" name="FP-current-user">
                             <label for="">others</label>
                             <input type="text" name="others">
                         </div>
                     </div>
                 </div>
                 <!-- current method -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-4">
                         <label for="FP" class="text-decoration-underline text-nowrap">Reason:</label>
                         <div class="answers d-flex gap-2">
                             <input type="radio" name="FP">
                             <label for="">medical condition</label>
                             <input type="radio" name="FP">
                             <label for="">side effects</label>
                             <input type="text">
                         </div>
                     </div>
                 </div>
             </div>
             <!-- current method -->
             <div class="current-method-user">
                 <h4>Previously used Method (for Current User)</h4>
                 <div class="methods d-flex gap-3">
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="implant">
                             <label for="implant">Implant</label>
                         </div>
                         <!-- injectable -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="Injectable">
                             <label for="Injectable">Injectable</label>
                         </div>
                         <!-- LAM -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="LAM">
                             <label for="LAM">LAM</label>
                         </div>
                     </div>
                     <!-- 2nd column -->
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="UID">
                             <label for="UID">UID</label>
                         </div>
                         <!-- COC -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="COC">
                             <label for="COC">COC</label>
                         </div>
                         <!-- SDM -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="SDM">
                             <label for="SDM">SDM</label>
                         </div>
                     </div>
                     <!-- 3rd -->
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="BTL">
                             <label for="BTL">BTL</label>
                         </div>
                         <!-- POP -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="POP">
                             <label for="POP">POP</label>
                         </div>
                         <!-- BBT -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="BBT">
                             <label for="BBT">BBT</label>
                         </div>
                     </div>
                     <!-- 4TH -->
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="NSV">
                             <label for="NSV">NSV</label>
                         </div>
                         <!-- Condom -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="Condom">
                             <label for="Condom">Condom</label>
                         </div>
                         <!-- BOM/CMM/STM -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="BOM/CMM/STM">
                             <label for="BOM/CMM/STM">BOM/CMM/STM</label>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto">
         <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
         <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
     </div>
 </div>