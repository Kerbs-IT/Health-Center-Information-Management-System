<div class="mb-3">
    <!-- type of patient info -->
    <div class="box d-flex flex-column align-items-center type-of-client">
        <div class="spouse w-100">
            <!-- spouse info-->
            <div class="family-inputs mb-md-2 mb-1 d-flex flex-column gap-1 w-100">
                <div class="client-info mb-md-2 mb-1">
                    <h5>Client Information</h5>
                    <div class="family-planning-inputs d-flex gap-1 flex-wrap flex-lg-nowrap">
                        <div class="input-field  flex-fill lg:w-[50%]">
                            <label for="side_A_add_client_id">Client ID:</label>
                            <input type="text" id="side_A_add_client_id" placeholder="Enter the client ID" class="form-control" name="side_A_add_client_id">
                            <small class="text-danger error-text" id="side_A_add_client_id_error"></small>
                        </div>
                        <div class="input-field  flex-fill lg:w-[50%]">
                            <label for="side_A_add_philhealth_no">Philhealth No:</label>
                            <input type="text" id="side_A_add_philhealth_no" placeholder="Enter the Religion" class="form-control" name="side_A_add_philhealth_no">
                            <small class="text-danger error-text" id="side_A_add_philhealth_no_error"></small>
                        </div>
                        <div class="input-field  flex-fill lg:w-[50%] ">
                            <label for="NHTS" class="">NHTS?:</label>
                            <div class="inputs d-flex gap-5 w-100 justify-content-center">
                                <div class="radio-input">
                                    <input type="radio" name="side_A_add_NHTS" value="Yes" id="nhts_yes">
                                    <label for="nhts_yes">Yes</label>
                                </div>
                                <div class="radio-input">
                                    <input type="radio" name="side_A_add_NHTS" value="No" id="nhts_no">
                                    <label for="nhts_no">No</label>
                                </div>
                            </div>
                            <small class="text-danger error-text" id="side_A_add_NHTS_error"></small>
                        </div>
                    </div>

                    <div class="mb-md-2 mb-1">
                        <div class=" w-100 ">
                            <label for="name_of_spouse" class="text-nowrap">Name of Client:</label>
                            <div class="group d-flex align-items-center justify-content-center gap-2 w-100 flex-wrap flex-lg-nowrap">
                                <div class="inputs-form flex-fill lg:w-[50%] ">
                                    <input type="text" class="form-control" id="side_A_add_client_fname" name="side_A_add_client_fname" placeholder="FirstName">
                                    <small class="text-danger error-text" id="side_A_add_client_fname_error"></small>
                                </div>
                                <div class="inputs-form flex-fill lg:w-[50%] ">
                                    <input type="text" class="form-control" id="side_A_add_client_MI" name="side_A_add_client_MI" placeholder="Middle Initial">
                                    <small class="text-danger error-text" id="side_A_add_client_MI_error"></small>
                                </div>
                                <div class="inputs-form flex-fill lg:w-[50%] ">
                                    <input type="text" class="form-control" id="side_A_add_client_lname" name="side_A_add_client_lname" placeholder="LastName">
                                    <small class="text-danger error-text" id="side_A_add_client_lname_error"></small>
                                </div>

                            </div>
                            <!-- HIDDEN INPUTS -->
                            <input type="hidden" name="side_A_add_health_worker_id" id="side_A_add_health_worker_id">
                        </div>
                    </div>
                    <div class="mb-md-2 mb-1 d-flex gap-1  flex-wrap flex-lg-nowrap">
                        <!-- date of birth -->
                        <div class="input-field flex-fill lg:w-[50%]">
                            <label for="side_A_add_client_date_of_birth">Date of Birth</label>
                            <input type="date" id="side_A_add_client_date_of_birth" min="1950-01-01" max="{{date('Y-m-d')}}" placeholder="01-02-25" class="form-control w-100 px-5" name="side_A_add_client_date_of_birth">

                            <small class="text-danger error-text" id="side_A_add_client_date_of_birth_error"></small>

                        </div>

                        <!-- age -->
                        <div class="input-field flex-fill lg:w-[50%]">
                            <label for="side_A_add_client_age">Age</label>
                            <input type="number" id="side_A_add_client_age" placeholder="20" class="form-control" name="side_A_add_client_age">
                            <small class="text-danger error-text" id="side_A_add_client_age_error"></small>
                        </div>
                        <!-- place of birth -->
                        <div class="input-field flex-fill lg:w-[50%]">
                            <label for="side_A_add_occupation">Occupation</label>
                            <input type="text" id="side_A_add_occupation" placeholder="Enter Occupation" class="form-control" name="side_A_add_occupation" value="">

                            <small class="text-danger error-text" id="side_A_add_occupation_error"></small>

                        </div>
                    </div>
                    <div class="family-planning-inputs d-flex gap-1 mb-md-2 mb-1 flex-md-nowrap flex-lg-nowrap flex-wrap flex-lg-row flex-column">
                        <div class="input-field flex-fill lg:w-[50%]">
                            <label for="civil_status" class="">Civil Status</label>
                            <select name="side_A_add_client_civil_status" id="side_A_add_client_civil_status" class="form-select">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorce">Divorce</option>
                            </select>

                            <small class="text-danger error-text" id="side_A_add_client_civil_status_error"></small>

                        </div>
                        <div class="input-field flex-fill lg:w-[50%]">
                            <label for="side_A_add_family_plan_religion">Religion</label>
                            <input type="text" id="side_A_add_client_religion" placeholder="Enter the Religion" class="form-control" name="side_A_add_client_religion">
                            <small class="text-danger error-text" id="side_A_add_client_religion_error"></small>
                        </div>
                    </div>
                    <div class="input-group mb-md-2 mb-1">
                        <h5>Address</h5>
                        <div class="input-field d-flex gap-2 align-items-lg-center align-items-none w-100  flex-md-nowrap flex-lg-nowrap flex-wrap flex-lg-row flex-column">
                            <div class=" mb-md-2 mb-1 flex-fill lg:w-[50%]">
                                <label for="street">Street*</label>
                                <input type="text" id="add_street" placeholder="Blk & Lot n Street" class="form-control py-2 border" name="add_street" value="">
                                <small class="text-danger error-text" id="street_error"></small>
                            </div>
                            <div class="mb-md-2 mb-1 flex-fill lg:w-[50%]">
                                <label for="brgy">Barangay*</label>
                                @php
                                $brgy = \App\Models\brgy_unit::orderBy('brgy_unit') -> get();
                                @endphp
                                <select name="add_brgy" id="add_brgy" class="form-select py-2">
                                    <option value="" disabled selected>Select a brgy</option>
                                    @foreach($brgy as $brgy_unit)
                                    <option value="{{ $brgy_unit -> brgy_unit }}">{{$brgy_unit -> brgy_unit}}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger error-text" id="brgy_error"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <h5>Spouse Information</h5>
                <div class="mb-md-2 mb-1">
                    <div class=" w-100 ">
                        <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
                        <div class="group d-flex align-items-center justify-content-center gap-2 flex-wrap flex-lg-nowrap">
                            <div class="input-form flex-fill">
                                <input type="text" class="form-control" id="side_A_add_spouse_fname" name="side_A_add_spouse_fname" placeholder="FirstName">
                                <small class="text-danger error-text" id="side_A_add_spouse_fname_error"></small>
                            </div>
                            <div class="input-form flex-fill">
                                <input type="text" class="form-control" id="side_A_add_spouse_MI" name="side_A_add_spouse_MI" placeholder="Middle Initial">
                                <small class="text-danger error-text" id="side_A_add_spouse_MI_error"></small>
                            </div>
                            <div class="input-form flex-fill">
                                <input type="text" class="form-control" id="side_A_add_spouse_lname" name="side_A_add_spouse_lname" placeholder="LastName">
                                <small class="text-danger error-text" id="side_A_add_spouse_lname_error"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-md-2 mb-1 d-flex gap-2 flex-md-nowrap flex-lg-nowrap flex-wrap flex-lg-row flex-column">
                    <div class="input-field flex-fill lg:w-[50%]">
                        <label for="contact_number" class="">Contact Number</label>
                        <input type="number" placeholder="+63-936-627-8671" class="form-control bg-light" id="side_A_add_spouse_contact_number" name="side_A_add_spouse_contact_number">

                        <small class="text-danger error-text" id="side_A_add_spouse_contact_number_error"></small>

                    </div>
                    <div class="input-field flex-fill lg:w-[50%]">
                        <label for="age">Age</label>
                        <input type="text" id="side_A_add_spouse_age" placeholder="20" class="form-control bg-light" name="side_A_add_spouse_age">

                        <small class="text-danger error-text" id="side_A_add_spouse_age_error"></small>

                    </div>
                    <div class="input-field flex-fill lg:w-[50%]">
                        <label for="age">Occupation</label>
                        <input type="text" id="side_A_add_spouse_occupation" placeholder="20" class="form-control bg-light" name="side_A_add_spouse_occupation">

                        <small class="text-danger error-text" id="side_A_add_spouse_occupation_error"></small>

                    </div>
                </div>
            </div>
        </div>
        <div class="other-info">
            <h4>Children Information</h4>
            <div class="box d-flex align-items-center gap-2 border-bottom mb-md-3 mb-0 flex-wrap flex-xl-nowrap">
                <div class="mb-md-3 mb-0">
                    <div class="input-form d-flex align-items-center">
                        <label for="No_of_children" class="text-nowrap">No. Of Living Children:</label>
                        <input type="number" id="side_A_add_number_of_living_children" name="side_A_add_number_of_living_children" class="form-control">
                    </div>
                    <small class="text-danger error-text" id="side_A_add_number_of_living_children_error"></small>
                </div>
                <div class="input-form">
                    <div class="mb-md-3 mb-0 d-flex gap-2 align-items-center">
                        <label for="" class="form-label text-nowrap mb-0">Plan To Have More Children?</label>
                        <div class="radio-con d-flex gap-2 form-radio">
                            <input type="radio" name="side_A_add_plan_to_have_more_children" id="side_A_add_children_plan_yes" value="Yes">
                            <label for="children_plan_yes">Yes</label>
                            <input type="radio" name="side_A_add_plan_to_have_more_children" id="side_A_add_children_plan_no" value="No">
                            <label for="children_plan_no">No</label>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_plan_to_have_more_children_error"></small>
                </div>

                <div class="input-form">
                    <div class="mb-md-3 mb-0 d-flex align-items-center gap-1">
                        <label for="montly_income" class="text-nowrap">Average Monthly Income</label>
                        <input type="number" id="side_A_add_average_montly_income" name="side_A_add_average_montly_income" class="form-control">
                    </div>
                    <small class="text-danger error-text" id="side_A_add_average_montly_income_error"></small>
                </div>
            </div>
        </div>
        <div class="d-flex gap-5 mb-md-3 mb-0 border-bottom w-100 flex-wrap flex-lg-nowrap">

            <div class="type-of-client">
                <h4 class="text-nowrap">Type of Client</h4>
                <div class="type-of-user-inputs">
                    <div class="mb-md-3 mb-0 d-flex align-items-center gap-2">
                        <input type="radio" name="side_A_add_type_of_patient" id="side_A_add_new_acceptor" value="new acceptor">
                        <label for="side_A_add_new_acceptor">New Acceptor</label>
                    </div>
                    <div class="mb-md-3 mb-0 d-flex align-items-center gap-2">
                        <input type="radio" name="side_A_add_type_of_patient" id="side_A_add_current_user" value="current user">
                        <label for="side_A_add_current_user">Current User</label>
                    </div>
                    <div class="mb-md-3 mb-0 d-flex align-items-center gap-2">
                        <input type="radio" name="side_A_add_current_user_type" id="side_A_add_current_method" value="current method">
                        <label for="side_A_add_current_method" class="side_a_current_user_type_label">Current Method</label>
                    </div>
                    <!-- new clinic -->
                    <div class="mb-md-3 mb-0 d-flex align-items-center gap-2">
                        <input type="radio" name="side_A_add_current_user_type" id="side_A_add_changing_clinic" value="changing clinic">
                        <label for="side_A_add_changing_clinic" class="side_a_current_user_type_label">Changing Clinic</label>
                    </div>
                    <!-- dropout -->
                    <div class="mb-md-3 mb-0 d-flex align-items-center gap-2">
                        <input type="radio" name="side_A_add_current_user_type" id="side_A_add_dropout_restart" value="dropout restart">
                        <label for="side_A_add_dropout_restart" class="side_a_current_user_type_label">Dropout/Restart</label>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_current_user_type_error"></small>
                </div>
                <small class="text-danger error-text" id="side_A_add_type_of_patient_error"></small>
            </div>
            <!-- reasons -->
            <div class="client-reasons">
                <h4 class="text-nowrap">Client Reason</h4>
                <!-- fp of new user -->
                <div class="reason-con">
                    <div class="mb-md-3 mb-0 d-flex gap-2 flex-wrap">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="side_A_add_new_acceptor_reason_for_FP" value="spacing" id="side_a_new_acceptor_spacing">
                            <label for="side_a_new_acceptor_spacing" class="side_a_new_acceptor_label">spacing</label>
                            <input type="radio" name="side_A_add_new_acceptor_reason_for_FP" value="limiting" id="side_a_new_acceptor_limiting">
                            <label for="side_a_new_acceptor_limiting" class="side_a_new_acceptor_label">limiting</label>
                            <input type="radio" name="side_A_add_new_acceptor_reason_for_FP" id="side_A_add_new_acceptor_reason_for_FP_others" value="others">
                            <label for="side_A_add_new_acceptor_reason_for_FP_others" class="side_a_new_acceptor_label">others</label>
                            <input type="text" name="side_A_add_new_acceptor_reason_text" id="side_A_add_new_acceptor_reason_text" class="flex-grow-1">
                        </div>
                        <small class="text-danger error-text" id="side_A_add_new_acceptor_reason_for_FP_error"></small>
                    </div>
                </div>
                <!-- FP of current user -->
                <div class="reason-con">
                    <div class="mb-md-3 mb-0 d-flex gap-2 flex-wrap">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="side_A_add_current_user_reason_for_FP" value="spacing" id="current_user_reason_for_fp_spacing">
                            <label for="current_user_reason_for_fp_spacing" class="side_a_current_user_label">spacing</label>
                            <input type="radio" name="side_A_add_current_user_reason_for_FP" value="limiting" id="current_user_reason_for_fp_limiting">
                            <label for="current_user_reason_for_fp_limiting" class="side_a_current_user_label">limiting</label>
                            <input type="radio" name="side_A_add_current_user_reason_for_FP" id="side_A_add_current_user_reason_for_FP_others" value="others">
                            <label for="side_A_add_current_user_reason_for_FP_others" class="side_a_current_user_label">others</label>
                            <input type="text" name="side_A_add_current_user_reason_for_FP" id="side_A_add_current_user_reason_text">
                        </div>
                        <small class="text-danger error-text" id="side_A_add_current_user_reason_for_FP_error"></small>
                    </div>
                </div>
                <!-- current method -->
                <div class="reason-con">
                    <div class="mb-md-3 mb-0 d-flex gap-4 flex-wrap">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="side_A_add_current_method_reason" value="medical condition" id="side_a_current_method_medical_condition">
                            <label for="side_a_current_method_medical_condition" class="side_a_current_method_label">medical condition</label>
                            <input type="radio" name="side_A_add_current_method_reason" id="side_A_add_current_method_reason_side_effect" value="side effects">
                            <label for="side_A_add_current_method_reason_side_effect" class="side_a_current_method_label">side effects</label>
                            <input type="text" id="side_A_add_side_effects_text" name="side_A_add_current_method_reason">
                        </div>
                        <small class="text-danger error-text" id="side_A_add_current_method_reason_error"></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- current method -->
        <div class="mb-md-3 mb-0 border-bottom w-100">

            <div class="current-method-user">
                <h4>Previously used Method (for Current User)</h4>
                <div class="methods d-flex gap-3 flex-wrap">
                    <div class="method-row">
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="Implant">
                            <label for="implant">Implant</label>
                        </div>
                        <!-- injectable -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="Injectable">
                            <label for="Injectable">Injectable</label>
                        </div>
                        <!-- LAM -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="LAM">
                            <label for="LAM">LAM</label>
                        </div>
                    </div>
                    <!-- 2nd column -->
                    <div class="method-row">
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="UID">
                            <label for="UID">UID</label>
                        </div>
                        <!-- COC -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="COC">
                            <label for="COC">COC</label>
                        </div>
                        <!-- SDM -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="SDM">
                            <label for="SDM">SDM</label>
                        </div>
                    </div>
                    <!-- 3rd -->
                    <div class="method-row">
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="BTL">
                            <label for="BTL">BTL</label>
                        </div>
                        <!-- POP -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="POP">
                            <label for="POP">POP</label>
                        </div>
                        <!-- BBT -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="BBT">
                            <label for="BBT">BBT</label>
                        </div>
                    </div>
                    <!-- 4TH -->
                    <div class="method-row">
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="NSV">
                            <label for="NSV">NSV</label>
                        </div>
                        <!-- Condom -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="Condom">
                            <label for="Condom">Condom</label>
                        </div>
                        <!-- BOM/CMM/STM -->
                        <div class="mb-md-3 mb-1 d-flex align-items-center gap-2">
                            <input type="checkbox" name="side_A_add_previously_used_method[]" value="BOM/CMM/STM">
                            <label for="BOM/CMM/STM">BOM/CMM/STM</label>
                        </div>
                    </div>
                </div>
                <small class="text-danger error-text" id="side_A_add_previously_used_method_error"></small>
            </div>
        </div>
    </div>
</div>