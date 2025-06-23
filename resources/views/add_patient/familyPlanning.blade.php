<div class="family-planning d-flex flex-column w-100 h-100 flex-grow-1">
    <div class="client-info p-3 h-100 d-flex flex-column align-items-between">
        <div class="mb-3 client-info " id="family-planning-step2">
            <div class="mb-3 w-100 ">
                <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
                <div class="group d-flex align-items-center justify-content-center gap-2">
                    <input type="text" class="form-control" name="lna class="form-control placeholder="LastName">
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
            <div class="box d-flex justify-content-between type-of-client" id="family-planning-step3">
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
                        <div class="mb-3 d-flex gap-2" >
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
        <!-- next box -->
        <div class="medical-history w-50 d-none flex-column align-self-center card shadow p-3" id="family-planning-step3">
            <h4 class="border-bottom">I.MEDICAL HISTORY</h4>
            <h6>Does the client have any of the following?</h6>
            <div class="list-of-questions">
                <!-- q1 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">severe headaches/migraine</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q1" id="q1_yes">
                        <label for="q1_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q1" id="q1_no">
                        <label for="q1_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q2 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">history of stroke / heart attack / hypertension</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q2" id="q2_yes">
                        <label for="q2_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q2" id="q2_no">
                        <label for="q2_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q3 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">non-traumatic hemtoma/ frequent bruising or gum bleeding</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q3" id="q3_yes">
                        <label for="q3_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q3" id="q3_no">
                        <label for="q3_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q4 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">current or history of breast cancer / breast mass</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q4" id="q4_yes">
                        <label for="q4_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q4" id="q4_no">
                        <label for="q4_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q5 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">severe chest pain</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q5" id="q5_yes">
                        <label for="q5_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q5" id="q5_no">
                        <label for="q5_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q6 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">cough for more than 14 days</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q6" id="q6_yes">
                        <label for="q6_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q6" id="q6_no">
                        <label for="q6_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q7 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">jaundice</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q7" id="q7_yes">
                        <label for="q7_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q7" id="q7_no">
                        <label for="q7_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q8 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">unexplained vaginal bleeding</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q8" id="q8_yes">
                        <label for="q8_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q8" id="q8_no">
                        <label for="q8_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q9 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">abnormal vaginal discharge</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q9" id="q9_yes">
                        <label for="q9_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q9" id="q9_no">
                        <label for="q9_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q10 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">intak of phenobarbital (anti-seizure) or rifampicin (anti-TB) </p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q10" id="q10_yes">
                        <label for="q10_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q10" id="q10_no">
                        <label for="q10_no"class="fs-5">No</label>
                    </div>
                </div>
                <!-- q11 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">is the client a SMOKER?</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q11" id="q11_yes">
                        <label for="q11_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q11" id="q11_no">
                        <label for="q11_no"class="fs-5">No</label>
                    </div>
                </div>
                 <!-- q12 -->
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <div class="question d-flex align-items-center gap-3">
                        <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                        <p class="mb-0 fs-5 fw-light">with Disability?</p>
                    </div>
                    <div class="answers d-flex align-items-center justify-content-center gap-2">
                        <input type="radio" name="q12" id="q12_yes">
                        <label for="q12_yes"class="fs-5">Yes</label>
                        <input type="radio" name="q12" id="q12_no">
                        <label for="q12_no"class="fs-5">No</label>
                    </div>
                </div>
                <div class="mb-2 d-flex gap-2 align-items-center">
                    <label for="dissability_yes" class="text-nowrap fs-5">(If YES please specify):</label>
                    <input type="text" placeholder="Enter the dissability" class="form-control">
                </div>
            </div>
        </div>
        <!-- OBSTERICAL HISTORY -->
        <div class="obstetrical-history w-50 card shadow p-3 align-self-center d-none" id="step4">
            <h4 class="border-bottom px-1">II. OBSTERICAL HISTORY</h4>
            <div class="obstetrical-content p-2">
                <div class="mb-3 border-bottom">
                    <label for="No_pregnancy">Number of Pregnancies:</label>
                    <div class="no-pregnancy  w-100">
                        <div class="box1 d-flex  gap-2">
                             <div class="mb-3 d-flex align-items-center">
                                <label for="">G:</label>
                                <input type="text" placeholder="Enter the number" class="form-control">
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label for="">P:</label>
                                <input type="text" placeholder="Enter the number" class="form-control">
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label for=""class="text-nowrap">Full Term:</label>
                                <input type="text" placeholder="Enter the number" class="form-control">
                            </div>
                        </div>
                        <div class="box-2 d-flex gap-2">
                            <div class="mb-3 d-flex align-items-center">
                                <label for=""class="text-nowrap">Abortion:</label>
                                <input type="text" placeholder="Enter the number" class="form-control">
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label for=""class="text-nowrap">Premature:</label>
                                <input type="text" placeholder="Enter the number" class="form-control">
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label for=""class="text-nowrap">Living Children:</label>
                                <input type="text" placeholder="Enter the number" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- date of last delivery -->
                     <div class="mb-3">
                        <label for="" class="text-nowrap">Date of Last Delivery:</label>
                        <input type="date" name="date-of-last-delivery" class="form-control">
                     </div>
                     <div class="mb-3 d-flex gap-2 w-100">
                        <label for="" class="text-nowrap">Type of Last Delivery:</label>
                        <div class="delivery-type d-flex gap-4">
                            <input type="radio" id="vaginal" name="type-of-delivery">
                            <label for="vaginal">Vaginal</label>
                            <input type="radio" id="cesarean-section" name="type-of-delivery">
                            <label for="cesarean-section">Cesarean Section</label>
                        </div>
                     </div>
                     <!-- last menstrual period -->
                    <div class="mb-3">
                        <label for="" class="text-nowrap">Last menstrual period:</label>
                        <input type="date" name="date-of-last-delivery" class="form-control">
                    </div>
                    <!-- previous -->
                    <div class="mb-3">
                        <label for="" class="text-nowrap">Previous menstrual period:</label>
                        <input type="date" name="date-of-last-delivery" class="form-control">
                    </div>
                    <!-- mesntrual flow -->
                     <div class="mb-3 d-flex flex-column">
                        <label for="">Menstrual flow:</label>
                        <div class="type-of-menstrual d-flex gap-4 align-items-center px-3">
                            <div class="box d-flex align-items-center gap-2">
                                <input type="radio" name="type-of-menstrual" id="scanty">
                                <label for="scanty"> scanty (1-2 pads per day)</label>
                            </div>
                            <div class="box d-flex align-items-center gap-2">
                                <input type="radio" name="type-of-menstrual" id="moderate">
                                <label for="moderate"> moderate (3-5 pads per day)</label>
                            </div>
                            <div class="box d-flex align-items-center gap-2">
                                <input type="radio" name="type-of-menstrual" id="scanty">
                                <label for="scanty"> heavy ( +5 pads per day)</label>
                            </div>
                        </div>
                     </div>
                     <div class="mb-3 d-flex align-items-center gap-3">
                        <input type="checkbox" class="form-checkbox">
                        <label for="">Dysmenorrhea</label>
                     </div>
                     <!-- hydaildiform -->
                     <div class="mb-3 d-flex align-items-center gap-3">
                        <input type="checkbox" class="form-checkbox">
                        <label for="">hydatidiform mole (within the last 12 months)</label>
                     </div>
                     <!-- history of ectopic pregnancy -->
                      <div class="mb-3 d-flex align-items-center gap-3">
                        <input type="checkbox" class="form-checkbox">
                        <label for="">History of ectopic pregnancy</label>
                     </div>
                </div>
            </div>
            <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto">
                <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
                <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
            </div>
        </div>
        <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto">
            <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
            <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
        </div>
    </div>
</div>