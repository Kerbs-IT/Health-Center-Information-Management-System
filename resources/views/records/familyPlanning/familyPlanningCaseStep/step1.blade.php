<div class="mb-3">
    <!-- type of patient info -->
    <div class="box d-flex flex-column align-items-center type-of-client">
        <div class="spouse w-100">
            <!-- spouse info-->
            <div class="family-inputs mb-2 d-flex flex-column gap-1 w-100">
                <div class="client-info mb-2">
                    <h5>Client Information</h5>
                    <div class="family-planning-inputs d-flex gap-1">
                        <div class="input-field w-50">
                            <label for="edit_client_id">Client ID:</label>
                            <input type="text" id="edit_client_id" placeholder="Enter the client ID" class="form-control" name="edit_client_id">
                            <small class="text-danger error-text" id="edit_client_id_error"></small>
                        </div>
                        <div class="input-field w-50">
                            <label for="edit_philhealth_no">Philhealth No:</label>
                            <input type="text" id="edit_philhealth_no" placeholder="Enter the Religion" class="form-control" name="edit_philhealth_no">
                            <small class="text-danger error-text" id="edit_philhealth_no_error"></small>
                        </div>
                        <div class="input-field w-50 ">
                            <label for="NHTS" class="">NHTS?:</label>
                            <div class="inputs d-flex gap-5 w-100 justify-content-center">
                                <div class="radio-input">
                                    <input type="radio" name="edit_NHTS" value="Yes" id="nhts_yes">
                                    <label for="nhts_yes">Yes</label>
                                </div>
                                <div class="radio-input">
                                    <input type="radio" name="edit_NHTS" value="No" id="nhts_no">
                                    <label for="nhts_no">No</label>
                                </div>
                            </div>
                            <small class="text-danger error-text" id="edit_NHTS_error"></small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="name_of_spouse" class="text-nowrap">Name of Client:</label>
                        <div class="group d-flex  justify-content-center gap-2">
                            <div class="input-form w-50">
                                <input type="text" class="form-control" id="edit_client_fname" name="edit_client_fname" placeholder="FirstName">
                                <small class="text-danger error-text" id="edit_client_fname_error"></small>
                            </div>
                            <div class="input-form w-50">
                                <input type="text" class="form-control" id="edit_client_MI" name="edit_client_MI" placeholder="Middle Initial">
                                <small class="text-danger error-text" id="edit_client_MI_error"></small>
                            </div>
                            <div class="input-form w-50">
                                <input type="text" class="form-control" id="edit_client_lname" name="edit_client_lname" placeholder="LastName">
                                <small class="text-danger error-text" id="edit_client_lname_error"></small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 d-flex gap-1">
                        <!-- date of birth -->
                        <div class="input-field w-50">
                            <label for="edit_client_date_of_birth">Date of Birth</label>
                            <input type="date" id="edit_client_date_of_birth" placeholder="01-02-25" class="form-control w-100 px-5" name="edit_client_date_of_birth">

                            <small class="text-danger error-text" id="edit_client_date_of_birth_error"></small>

                        </div>

                        <!-- age -->
                        <div class="input-field w-50">
                            <label for="edit_client_age">Age</label>
                            <input type="number" id="edit_client_age" placeholder="20" class="form-control" name="edit_client_age">
                            <small class="text-danger error-text" id="edit_client_age_error"></small>
                        </div>
                        <!-- place of birth -->
                        <div class="input-field w-50">
                            <label for="edit_occupation">Occupation</label>
                            <input type="text" id="edit_occupation" placeholder="trece martires city" class="form-control" name="edit_occupation" value="">

                            <small class="text-danger error-text" id="edit_occupation_error"></small>

                        </div>
                    </div>
                    <div class="family-planning-inputs d-flex gap-1 mb-2">
                        <div class="input-field w-50">
                            <label for="civil_status" class="">Civil Status</label>
                            <select name="edit_client_civil_status" id="civil_status" class="form-select">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorce">Divorce</option>
                            </select>

                            <small class="text-danger error-text" id="edit_client_civil_status_error"></small>

                        </div>
                        <div class="input-field w-50">
                            <label for="edit_family_plan_religion">Religion</label>
                            <input type="text" id="edit_client_religion" placeholder="Enter the Religion" class="form-control" name="edit_client_religion">
                            <small class="text-danger error-text" id="edit_client_religion_error"></small>
                        </div>
                    </div>
                </div>
                <h5>Spouse Information</h5>
                <div class="mb-2">
                    <div class=" w-100 ">
                        <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
                        <div class="group d-flex  justify-content-center gap-2">
                            <div class="input-form w-50">
                                <input type="text" class="form-control" id="edit_spouse_fname" name="edit_spouse_fname" placeholder="FirstName">
                                <small class="text-danger error-text" id="edit_spouse_fname_error"></small>
                            </div>

                            <div class="input-form w-50">
                                <input type="text" class="form-control" id="edit_spouse_MI" name="edit_spouse_MI" placeholder="Middle Initial">
                                <small class="text-danger error-text" id="edit_spouse_MI_error"></small>
                            </div>

                            <div class="input-form w-50">
                                <input type="text" class="form-control" id="edit_spouse_lname" name="edit_spouse_lname" placeholder="LastName">
                                <small class="text-danger error-text" id="edit_spouse_lname_error"></small>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="mb-2 d-flex gap-2">
                    <div class="input-field w-50">
                        <label for="contact_number" class="">Contact Number</label>
                        <input type="number" placeholder="+63-936-627-8671" class="form-control bg-light" id="edit_spouse_contact_number" name="edit_spouse_contact_number">

                        <small class="text-danger error-text" id="edit_spouse_contact_number_error"></small>

                    </div>
                    <div class="input-field w-50">
                        <label for="age">Age</label>
                        <input type="text" id="edit_spouse_age" placeholder="20" class="form-control bg-light" name="edit_spouse_age">

                        <small class="text-danger error-text" id="edit_spouse_age_error"></small>

                    </div>
                    <div class="input-field w-50">
                        <label for="age">Occupation</label>
                        <input type="text" id="edit_spouse_occupation" placeholder="20" class="form-control bg-light" name="edit_spouse_occupation">

                        <small class="text-danger error-text" id="edit_spouse_occupation_error"></small>

                    </div>
                </div>
            </div>
        </div>
        <div class="other-info">
            <h4>Children Information</h4>
            <div class="box d-flex align-items-center gap-2 border-bottom mb-3">
                <div class="mb-3 d-flex align-items-center">
                    <label for="No_of_children" class="text-nowrap">No. Of Living Children:</label>
                    <input type="number" id="edit_number_of_living_children" name="edit_number_of_living_children" class="form-control">
                    <small class="text-danger error-text" id="edit_number_of_living_children_error"></small>
                </div>
                <div class="mb-3 d-flex gap-2 align-items-center">
                    <label for="" class="form-label text-nowrap mb-0">Plan To Have More Children?</label>
                    <div class="radio-con d-flex gap-2 form-radio">
                        <input type="radio" name="edit_plan_to_have_more_children" id="edit_children_plan_yes" value="Yes">
                        <label for="children_plan_yes">Yes</label>
                        <input type="radio" name="edit_plan_to_have_more_children" id="edit_children_plan_no" value="No">
                        <label for="children_plan_no">No</label>
                    </div>
                    <small class="text-danger error-text" id="edit_number_of_living_children_error"></small>
                </div>
                <div class="mb-3 d-flex align-items-center gap-1">
                    <label for="montly_income" class="text-nowrap">Average Monthly Income</label>
                    <input type="number" id="edit_average_montly_income" name="edit_average_montly_income" class="form-control">
                    <small class="text-danger error-text" id="edit_number_of_living_children_error"></small>
                </div>

            </div>
        </div>
        <div class="d-flex gap-5 mb-3 border-bottom w-100">

            <div class="type-of-client">
                <h4 class="text-nowrap">Type of Client</h4>
                <div class="type-of-user-inputs">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="edit_type_of_patient" id="edit_new_acceptor" value="new acceptor">
                        <label for="edit_new_acceptor">New Acceptor</label>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="edit_type_of_patient" id="edit_current_user" value="current user">
                        <label for="edit_current_user">Current User</label>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="edit_current_user_type" id="edit_current_method" value="current method">
                        <label for="edit_current_method">Current Method</label>
                    </div>
                    <!-- new clinic -->
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="edit_current_user_type" id="edit_changing_clinic" value="changing clinic">
                        <label for="edit_changing_clinic">Changing Clinic</label>
                    </div>
                    <!-- dropout -->
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="edit_current_user_type" id="edit_dropout_restart" value="dropout restart">
                        <label for="edit_dropout_restart">Dropout/Restart</label>
                    </div>
                </div>
                <small class="text-danger error-text" id="edit_type_of_patient_error"></small>
                <small class="text-danger error-text" id="edit_current_user_type_error"></small>
            </div>
            <!-- reasons -->
            <div class="client-reasons">
                <h4 class="text-nowrap">Client Reason</h4>
                <!-- fp of new user -->
                <div class="reason-con">
                    <div class="mb-3 d-flex gap-2">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="edit_new_acceptor_reason_for_FP" value="spacing">
                            <label for="">spacing</label>
                            <input type="radio" name="edit_new_acceptor_reason_for_FP" value="limiting">
                            <label for="">limiting</label>
                            <input type="radio" name="edit_new_acceptor_reason_for_FP" id="edit_new_acceptor_reason_for_FP_others" value="others">
                            <label for="">others</label>
                            <input type="text" name="edit_new_acceptor_reason_text" id="edit_new_acceptor_reason_text" class="flex-grow-1">
                        </div>
                    </div>
                </div>
                <!-- FP of current user -->
                <div class="reason-con">
                    <div class="mb-3 d-flex gap-2">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="edit_current_user_reason_for_FP" value="spacing">
                            <label for="">spacing</label>
                            <input type="radio" name="edit_current_user_reason_for_FP" value="limiting">
                            <label for="">limiting</label>
                            <input type="radio" name="edit_current_user_reason_for_FP" id="edit_current_user_reason_for_FP_others" value="others">
                            <label for="">others</label>
                            <input type="text" name="edit_current_user_reason_for_FP" id="edit_current_user_reason_text">
                        </div>
                    </div>
                </div>
                <!-- current method -->
                <div class="reason-con">
                    <div class="mb-3 d-flex gap-4">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="edit_current_method_reason" value="medical condition">
                            <label for="">medical condition</label>
                            <input type="radio" name="edit_current_method_reason" id="edit_current_method_reason_side_effect" value="side effects">
                            <label for="">side effects</label>
                            <input type="text" id="edit_side_effects_text" name="edit_current_method_reason">
                        </div>
                    </div>
                </div>
                <small class="text-danger error-text" id="edit_new_acceptor_reason_for_FP_error"></small>
                <small class="text-danger error-text" id="edit_current_user_reason_for_FP_error"></small>
                <small class="text-danger error-text" id="edit_current_method_reason_error"></small>
            </div>
        </div>

        <!-- current method -->
        <div class="mb-3 border-bottom w-100">

            <div class="current-method-user">
                <h4>Previously used Method (for Current User)</h4>
                <div class="methods d-flex gap-3">
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="implant">
                            <label for="implant">Implant</label>
                        </div>
                        <!-- injectable -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="Injectable">
                            <label for="Injectable">Injectable</label>
                        </div>
                        <!-- LAM -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="LAM">
                            <label for="LAM">LAM</label>
                        </div>
                    </div>
                    <!-- 2nd column -->
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="UID">
                            <label for="UID">UID</label>
                        </div>
                        <!-- COC -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="COC">
                            <label for="COC">COC</label>
                        </div>
                        <!-- SDM -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="SDM">
                            <label for="SDM">SDM</label>
                        </div>
                    </div>
                    <!-- 3rd -->
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="BTL">
                            <label for="BTL">BTL</label>
                        </div>
                        <!-- POP -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="POP">
                            <label for="POP">POP</label>
                        </div>
                        <!-- BBT -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="BBT">
                            <label for="BBT">BBT</label>
                        </div>
                    </div>
                    <!-- 4TH -->
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="NSV">
                            <label for="NSV">NSV</label>
                        </div>
                        <!-- Condom -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="Condom">
                            <label for="Condom">Condom</label>
                        </div>
                        <!-- BOM/CMM/STM -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="edit_previously_used_method[]" value="BOM/CMM/STM">
                            <label for="BOM/CMM/STM">BOM/CMM/STM</label>
                        </div>
                    </div>
                </div>
                <small class="text-danger error-text" id="edit_previously_used_method_error"></small>
            </div>
        </div>
    </div>
</div>