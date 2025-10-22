 <div class="mb-3 d-flex flex-column h-190">
     <div class="mb-3 client-info w-100 card shadow p-3 h-100">
         <div class="mb-3 w-100 ">
             <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
             <div class="group d-flex align-items-center justify-content-center gap-2">
                 <input type="text" class="form-control" name="spouse_lname" form-control placeholder="Enter the LastName">
                 <input type="text" class="form-control" name="spouse_fname" placeholder=" Enter the FirstName">
                 <input type="text" class="form-control" name="spouse_MI" placeholder="Enter the Middle Initial">
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
             <div class="mb-3 w-50">
                 <label for="No_of_children" class="text-nowrap">No. Of Living Children:</label>
                 <input type="number" name="number_of_living_children" class="form-control" placeholder="Enter the number of living children">
             </div>
             <div class="mb-3 gap-2 w-50">
                 <label for="" class="form-label text-nowrap mb-0">Plan To Have More Children?</label>
                 <div class="radio-con d-flex gap-2 justify-content-center fs-5 form-radio p-1">
                     <input type="radio" name="plan_to_have_more_children" id="children_plan_yes" value="Yes">
                     <label for="children_plan_yes">Yes</label>
                     <input type="radio" name="plan_to_have_more_children" id="children_plan_no" value="No">
                     <label for="children_plan_no">No</label>
                 </div>
             </div>
             <div class="mb-3 w-50">
                 <label for="montly_income" class="text-nowrap">Average Monthly Income: </label>
                 <input type="number" class="form-control" name="average_montly_income">
             </div>
         </div>
         <!-- type of patient info -->
         <div class="box d-flex justify-content-between type-of-client gap-5">
             <div class="type-of-client">
                 <h4 class="text-nowrap">Type of Client</h4>
                 <div class="type-of-user-inputs">
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="family_planning_type_of_patient" id="new-acceptor" value="new acceptor">
                         <label for="new-acceptor">New Acceptor</label>
                     </div>
                     <div class="mb-3 d-flex align-items-center gap-2">
                         <input type="radio" name="family_planning_type_of_patient" id="current-user" value="current user">
                         <label for="current-user">Current User</label>
                     </div>
                     <div class="current-user-type px-4">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="radio" name="current_user_type" id="family_planning_current-method" value="current method">
                             <label for="family_planning_current-method" class="current_user_type_label">Current Method</label>
                         </div>
                         <!-- new clinic -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="radio" name="current_user_type" id="changing-clinic" value="changing clinic">
                             <label for="changing-clinic" class="current_user_type_label">Changing Clinic</label>
                         </div>
                         <!-- dropout -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="radio" name="current_user_type" id="dropout-restart" value="dropout/restart">
                             <label for="dropout-restart" class="current_user_type_label">Dropout/Restart</label>
                         </div>
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
                             <input type="radio" name="new_acceptor_reason_for_FP" value="spacing">
                             <label for="">spacing</label>
                             <input type="radio" name="new_acceptor_reason_for_FP" value="limiting">
                             <label for="">limiting</label>
                             <input type="radio" name="new_acceptor_reason_for_FP">
                             <label for="">others</label>
                             <input type="text" class="flex-grow-1" name="new_acceptor_reason_for_FP">
                         </div>
                     </div>
                 </div>
                 <!-- FP of current user -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-2">
                         <label for="FP" class="text-decoration-underline text-nowrap current_user_label">Reason for FP:</label>
                         <div class="answers d-flex gap-2">
                             <input type="radio" name="current_user_reason_for_FP" id="current_user_reason_for_FP_spacing" value="spacing">
                             <label for="" class="current_user_label">spacing</label>
                             <input type="radio" name="current_user_reason_for_FP" id="current_user_reason_for_FP_limiting" value="limiting">
                             <label for="current_user_reason_for_FP_limiting" class="current_user_label">limiting</label>
                             <input type="radio" name="current_user_reason_for_FP" id="current_user_reason_for_FP_others">
                             <label for="current_user_reason_for_FP_others" class="current_user_label">others</label>
                             <input type="text" name="current_user_reason_for_FP">
                         </div>
                     </div>
                 </div>
                 <!-- current method -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-4">
                         <label for="FP" class="text-decoration-underline text-nowrap">Reason:</label>
                         <div class="answers d-flex gap-2">
                             <input type="radio" name="current_method_reason" id="current_method_reason_medical_condition" value="medical condition">
                             <label for="current_method_reason_medical_condition" class="current_method_reason_label">medical condition</label>
                             <input type="radio" name="current_method_reason" id="current_method_reason_side_effects" value="side effects">
                             <label for="current_method_reason_side_effects" class="current_method_reason_label">side effects</label>
                             <input type="text" name="current_method_reason">
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
                             <input type="checkbox" name="previously_used_method[]" value="implant">
                             <label for="implant">Implant</label>
                         </div>
                         <!-- injectable -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="Injectable">
                             <label for="Injectable">Injectable</label>
                         </div>
                         <!-- LAM -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="LAM">
                             <label for="LAM">LAM</label>
                         </div>
                     </div>
                     <!-- 2nd column -->
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="UID">
                             <label for="UID">UID</label>
                         </div>
                         <!-- COC -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="COC">
                             <label for="COC">COC</label>
                         </div>
                         <!-- SDM -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="SDM">
                             <label for="SDM">SDM</label>
                         </div>
                     </div>
                     <!-- 3rd -->
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="BTL">
                             <label for="BTL">BTL</label>
                         </div>
                         <!-- POP -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="POP">
                             <label for="POP">POP</label>
                         </div>
                         <!-- BBT -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="BBT">
                             <label for="BBT">BBT</label>
                         </div>
                     </div>
                     <!-- 4TH -->
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="NSV">
                             <label for="NSV">NSV</label>
                         </div>
                         <!-- Condom -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="Condom">
                             <label for="Condom">Condom</label>
                         </div>
                         <!-- BOM/CMM/STM -->
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" value="BOM/CMM/STM">
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