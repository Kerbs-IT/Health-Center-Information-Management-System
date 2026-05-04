 <div class="mb-3 d-flex flex-column min-h-190 w-[95%]">
     <div class="mb-3 client-info w-100 card shadow p-3 h-100">
         <div class="mb-3 w-100 ">
             <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
             <div class="group d-flex align-items-center justify-content-center gap-2 flex-wrap flex-md-nowrap">
                 <input type="text" class="form-control flex-fill" name="spouse_lname" placeholder="Enter the LastName">
                 <input type="text" class="form-control flex-fill" name="spouse_fname" placeholder=" Enter the FirstName">
                 <input type="text" class="form-control flex-fill" name="spouse_MI" placeholder="Enter the Middle Initial">
                 <div class="input-field w-50">
                     <select name="spouse_suffix" id="spouse_suffix" class="form-select py-2">
                         <option value="" selected>Select Suffix</option>
                         <option value="Jr.">Jr</option>
                         <option value="Sr.">Sr</option>
                         <option value="II.">II</option>
                         <option value="III.">III</option>
                         <option value="IV.">IV</option>
                         <option value="V.">V</option>
                     </select>
                     <small class="text-danger" id="spouse_suffix_error"></small>
                 </div>
             </div>
             <small class="text-danger error-text" id="spouse_fname_error"></small>
             <small class="text-danger error-text" id="spouse_lname_error"></small>
             <small class="text-danger error-text" id="spouse_MI_error"></small>
         </div>
         <div class="box d-flex w-100 gap-md-2 gap-0 flex-wrap flex-md-nowrap flex-md-row flex-column">
             <div class="mb-md-3 mb-0 w-100 md:w-[50%]">
                 <label for="spouse_date_of_birth">Date of Birth</label>
                 <input type="date" name="spouse_date_of_birth" class="form-control w-100">
                 <small class="text-danger error-text" id="spouse_date_of_birth_error"></small>
             </div>
             <div class="mb-md-3 mb-0 w-100 md:w-[50%]">
                 <label for="spouse_age">Age</label>
                 <input type="number" name="spouse_age" class="form-control w-100">
                 <small class="text-danger error-text" id="spouse_age_error"></small>
             </div>
             <div class="mb-md-3 mb-0 w-100 md:w-[50%]">
                 <label for="occupation">Occupation</label>
                 <input type="text" name="spouse_occupation" class="form-control w-100">
                 <small class="text-danger error-text" id="spouse_occupation_error"></small>
             </div>
         </div>
         <!-- children info -->
         <div class="box d-flex align-items-center gap-2 border-bottom mb-3  flex-wrap flex-md-nowrap flex-md-row flex-column">
             <div class="mb-3 w-100 md:w-[50%]">
                 <label for="No_of_children" class="text-nowrap">No. Of Living Children:</label>
                 <input type="number" name="number_of_living_children" class="form-control" placeholder="Enter the number of living children">
                 <small class="text-danger error-text" id="number_of_living_children_error"></small>
             </div>
             <div class="mb-3 gap-2 w-100 md:w-[50%]">
                 <label for="" class="form-label text-nowrap mb-0">Plan To Have More Children?</label>
                 <div class="radio-con d-flex gap-2 justify-content-center fs-5 form-radio p-1">
                     <input type="radio" name="plan_to_have_more_children" id="children_plan_yes" value="Yes">
                     <label for="children_plan_yes">Yes</label>
                     <input type="radio" name="plan_to_have_more_children" id="children_plan_no" value="No">
                     <label for="children_plan_no">No</label>
                 </div>
                 <small class="text-danger error-text" id="plan_to_have_more_children_error"></small>
             </div>
             <div class="mb-3 w-100 md:w-[50%]">
                 <label for="montly_income" class="text-nowrap">Average Monthly Income: </label>
                 <input type="number" class="form-control" name="average_montly_income">
                 <small class="text-danger error-text" id="average_montly_income_error"></small>
             </div>
         </div>
         <!-- type of patient info -->
         <div class="box d-flex justify-content-between type-of-client gap-3 gap-md-5 flex-wrap w-100">

             <div class="type-of-client flex-fill">
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
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="radio" name="current_user_type" id="changing-clinic" value="changing clinic">
                             <label for="changing-clinic" class="current_user_type_label">Changing Clinic</label>
                         </div>
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="radio" name="current_user_type" id="dropout-restart" value="dropout/restart">
                             <label for="dropout-restart" class="current_user_type_label">Dropout/Restart</label>
                         </div>
                     </div>
                 </div>
                 <small class="text-danger error-text" id="family_planning_type_of_patient_error"></small>
                 <small class="text-danger error-text" id="current_user_type_error"></small>
             </div>

             <div class="client-reasons flex-fill flex-wrap min-w-0">
                 <h4 class="text-nowrap">Client Reason</h4>

                 <!-- FP reason for new acceptor -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-2">
                         <label class="text-decoration-underline text-nowrap new_acceptor_label">Reason for FP:</label>
                         <div class="answers d-flex gap-2 flex-wrap flex-fill">
                             <input type="radio" name="new_acceptor_reason_for_FP" value="spacing" id="new_acceptor_reason_for_FP_spacing">
                             <label for="new_acceptor_reason_for_FP_spacing" class="new_acceptor_label">spacing</label>

                             <input type="radio" name="new_acceptor_reason_for_FP" value="limiting" id="new_acceptor_reason_for_FP_limiting">
                             <label for="new_acceptor_reason_for_FP_limiting" class="new_acceptor_label">limiting</label>

                             <!-- FIX: added value="others" -->
                             <input type="radio" name="new_acceptor_reason_for_FP" value="others" id="new_acceptor_reason_for_FP_others">
                             <label for="new_acceptor_reason_for_FP_others" class="new_acceptor_label">others</label>

                             <!-- FIX: unique name so it never collides with the radio group -->
                             <input type="text"
                                 class="flex-grow-1"
                                 name="new_acceptor_reason_text"
                                 id="new_acceptor_reason_text"
                                 placeholder="Specify if others">
                         </div>
                     </div>
                     <small class="text-danger error-text" id="new_acceptor_reason_for_FP_error"></small>
                 </div>

                 <!-- FP reason for current user -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-2">
                         <label class="text-decoration-underline text-nowrap current_user_label">Reason for FP:</label>
                         <div class="answers d-flex gap-2 flex-wrap flex-fill">
                             <input type="radio" name="current_user_reason_for_FP" value="spacing" id="current_user_reason_for_FP_spacing">
                             <!-- FIX: for attribute was empty -->
                             <label for="current_user_reason_for_FP_spacing" class="current_user_label">spacing</label>

                             <input type="radio" name="current_user_reason_for_FP" value="limiting" id="current_user_reason_for_FP_limiting">
                             <label for="current_user_reason_for_FP_limiting" class="current_user_label">limiting</label>

                             <!-- FIX: added value="others" -->
                             <input type="radio" name="current_user_reason_for_FP" value="others" id="current_user_reason_for_FP_others">
                             <label for="current_user_reason_for_FP_others" class="current_user_label">others</label>

                             <!-- FIX: unique name so it never collides with the radio group -->
                             <input type="text"
                                 class="flex-grow-1"
                                 name="current_user_reason_text"
                                 id="current_user_reason_text"
                                 placeholder="Specify if others">
                         </div>
                         <small class="text-danger error-text" id="current_user_reason_for_FP_error"></small>
                     </div>
                 </div>

                 <!-- Current method reason -->
                 <div class="reason-con">
                     <div class="mb-3 d-flex gap-4">
                         <label class="text-decoration-underline text-nowrap">Reason:</label>
                         <div class="answers d-flex gap-2 flex-wrap flex-fill">
                             <input type="radio" name="current_method_reason" value="medical condition" id="current_method_reason_medical_condition">
                             <label for="current_method_reason_medical_condition" class="current_method_reason_label">medical condition</label>

                             <input type="radio" name="current_method_reason" value="side effects" id="current_method_reason_side_effects">
                             <label for="current_method_reason_side_effects" class="current_method_reason_label">side effects</label>

                             <!-- FIX: unique name so it never collides with the radio group -->
                             <input type="text"
                                 class="flex-grow-1"
                                 name="current_method_reason_text"
                                 id="current_method_reason_text"
                                 placeholder="Specify side effects">
                         </div>
                         <small class="text-danger error-text" id="current_method_reason_error"></small>
                     </div>
                 </div>
             </div>

             <!-- Previously used method -->
             <div class="current-method-user">
                 <h4>Previously used Method (for Current User)</h4>
                 <div class="methods d-flex gap-3 flex-wrap">
                     <div class="method-row">
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="implant" value="Implant">
                             <label for="implant">Implant</label>
                         </div>
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="injectable" value="Injectable">
                             <label for="injectable">Injectable</label>
                         </div>
                         <div class="mb-3 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="lam" value="LAM">
                             <label for="lam">LAM</label>
                         </div>
                     </div>
                     <div class="method-row">
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="iud" value="IUD">
                             <label for="iud">IUD</label>
                         </div>
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="coc" value="COC">
                             <label for="coc">COC</label>
                         </div>
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="sdm" value="SDM">
                             <label for="sdm">SDM</label>
                         </div>
                     </div>
                     <div class="method-row">
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="btl" value="BTL">
                             <label for="btl">BTL</label>
                         </div>
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="pop" value="POP">
                             <label for="pop">POP</label>
                         </div>
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="bbt" value="BBT">
                             <label for="bbt">BBT</label>
                         </div>
                     </div>
                     <div class="method-row">
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="nsv" value="NSV">
                             <label for="nsv">NSV</label>
                         </div>
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="condom" value="Condom">
                             <label for="condom">Condom</label>
                         </div>
                         <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                             <input type="checkbox" name="previously_used_method[]" id="bom-cmm-stm" value="BOM/CMM/STM">
                             <label for="bom-cmm-stm">BOM/CMM/STM</label>
                         </div>
                     </div>
                 </div>
                 <small class="text-danger error-text" id="previously_used_method_error"></small>
             </div>

         </div>
     </div>
     <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto flex-column flex-sm-row">
         <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
         <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
     </div>
 </div>