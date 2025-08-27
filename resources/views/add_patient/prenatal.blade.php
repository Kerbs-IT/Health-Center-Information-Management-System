<div class="prenatal-con d-flex w-75 flex-column card shadow p-3 align-self-center h-100 rounded">
    <div class="mb-2 w-100">
        <label for="patient_name" class="w-100">Patient Name</label>
        <input type="text" class="p-2 w-50" disabled id="prenatal_patient_full_name">
    </div>
    <div class="mb-2 w-100">

        <div class="ob-history mb-2">
            <h3>OB HISTORY</h3>
            <div class="type-of-pregnancy d-flex w-100 gap-1">
                <div class="item">
                    <label for="G">G</label>
                    <input type="number" name="G" class="form-control w-100" placeholder="0">
                </div>
                <div class="item">
                    <label for="G">P</label>
                    <input type="number" name="P" class="form-control w-100" placeholder="0">
                </div>
                <div class="item">
                    <label for="G">T</label>
                    <input type="number" name="T" class="form-control w-100" placeholder="0">
                </div>
                <div class="item">
                    <label for="G">Premature</label>
                    <input type="number" name="premature" class="form-control w-100" placeholder="0">
                </div>
                <div class="item">
                    <label for="G">Abortion</label>
                    <input type="number" name="abortion" class="form-control w-100" placeholder="0">
                </div>
                <div class="item">
                    <label for="G">Living Children</label>
                    <input type="number" name="living_children" class="form-control w-100" placeholder="0">
                </div>
            </div>
        </div>
        <h3>Records</h3>
        <div class="previous-record mb-3 d-flex gap-1">
            <div class="item w-25">
                <label for="year_of_pregnancy">Year of Pregnancy</label>
                <input type="number" id="pregnancy_year" name="pregnancy_year" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required>
                <span class="w-100 text-danger" id="preg_year_error"></span>
            </div>
            <div class="item">
                <label for="type_of_delivery">Type of Delivery</label>
                <select name="type_of_delivery" id="type_of_delivery" class="form-select" required>
                    <option value="" disabled selected>Select Type of Delivery</option>
                    <option value="normal_spontaneous_delivery">Normal Spontaneous Delivery (NSD)</option>
                    <option value="cesarean_section">Cesarean Section (CS)</option>
                    <option value="assisted_vaginal_delivery">Assisted Vaginal Delivery</option>
                    <option value="breech_delivery">Breech Delivery</option>
                    <option value="forceps_delivery">Forceps Delivery</option>
                    <option value="vacuum_extraction">Vacuum Extraction</option>
                    <option value="water_birth">Water Birth</option>
                    <option value="home_birth">Home Birth</option>
                    <option value="emergency_cesarean">Emergency Cesarean</option>
                </select>
                <span class="w-100 text-danger" id="type_of_delivery_error"></span>
            </div>
            <div class="item">
                <label for="place_of_delivery">Place of Delivery</label>
                <input type="text" name="place_of_delivery" class="form-control w-100" placeholder="trece" id="place_of_delivery">
                <span class="w-100 text-danger" id="place_of_delivery_error"></span>
            </div>
            <div class="item">
                <label for="birth_attendant">Birth Attendant</label>
                <input type="text" name="birth_attendant" class="form-control w-100" placeholder="Nurse joy" id="birth_attendant">
                <span class="w-100 text-danger" id="birth_attendant_error"></span>
            </div>
            <div class="item">
                <label for="Complication">Complication</label>
                <input type="text" name="Complication" class="form-control w-100" placeholder="" id="complication" value="none">
                <span class="w-100 text-danger" id="complication_error"></span>
            </div>
            <div class="item">
                <label for="G">Outcome</label>
                <select id="pregnancyOutcome" name="pregnancyOutcome" required class="form-select">
                    <option value="" disabled selected>Select Outcome</option>
                    <option value="term">Term Delivery</option>
                    <option value="preterm">Preterm Delivery</option>
                    <option value="abortion">Abortion (Spontaneous/Induced)</option>
                    <option value="ectopic">Ectopic Pregnancy</option>
                    <option value="stillbirth">Stillbirth (IUFD)</option>
                    <option value="living">Living Child</option>
                </select>
                <span class="w-100 text-danger" id="outcome_error"></span>
            </div>
            <div class="d-flex align-self-end mb-0">
                <button type="button" class="btn btn-success" id="add-pregnancy-history-btn"> Add</button>
            </div>
        </div>
        <!-- results -->
        <div class="mb-2">
            <table class="table table-bordered mt-4">
                <thead class="table-secondary text-center">
                    <tr>
                        <th>Year of Pregnancy</th>
                        <th>Type of Delivery</th>
                        <th>Place of Delivery</th>
                        <th>Birth Attendant</th>
                        <th>Complication</th>
                        <th>Outcome</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="previous-records-body">
                    <tr class="text-center h-25">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <!-- <button class="btn btn-danger btn-sm"></button> -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- subjective -->
        <h3>Subjective</h3>
        <div class="subjective-info mb-3 border-bottom">
            <div class="mb-2 d-flex w-100 gap-2">
                <div class="mb-2 w-100 ">
                    <label for="LMP">LMP</label>
                    <input type="date" name="LMP" class="form-control w-100" placeholder="trece" id="LMP">
                </div>
                <div class="mb-2 w-100">
                    <label for="expected_delivery">Expected Delivery</label>
                    <input type="date" name="expected_delivery" class="form-control w-100" placeholder="trece">
                </div>
                <div class="mb-2 w-100">
                    <label for="menarche">Menarche</label>
                    <input type="text" name="menarche" class="form-control w-100" placeholder="ex.13" id="menarche">
                </div>
            </div>
            <h3>Tetanus Toxoid Status (Year)</h3>
            <!-- next row -->
            <div class="mb-2 d-flex w-100 gap-2">
                <div class="mb-2 w-100 ">
                    <label for="TT1">TT1</label>
                    <input type="text" name="TT1" class="form-control w-100" placeholder="YYYY" id="TT1">
                </div>
                <div class="mb-2 w-100">
                    <label for="TT2">TT2</label>
                    <input type="text" name="TT2" class="form-control w-100" placeholder="YYYY" id="TT2">
                </div>
                <div class="mb-2 w-100">
                    <label for="TT2">TT3</label>
                    <input type="text" name="TT2" class="form-control w-100" placeholder="YYYY" id="TT3">
                </div>
            </div>
            <!-- last row -->
            <div class="mb-2 d-flex w-100 gap-2">
                <div class="mb-2 w-100 ">
                    <label for="TT4">TT4</label>
                    <input type="text" name="TT4" class="form-control w-100" placeholder="YYYY" id="TT4">
                </div>
                <div class="mb-2 w-100">
                    <label for="TT2">TT5</label>
                    <input type="text" name="TT2" class="form-control w-100" placeholder="YYYY" id="TT5">
                </div>
            </div>

        </div>
        <!-- ASSESSMENT -->
        <div class="assessment-con mb-3 border-bottom">
            <h4>ASSESSMENT <small class="text-muted fs-5">(put check if yes)</small></h4>
            <div class="checkboxes d-flex gap-2 mb-2 flex-wrap">
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="spotting" class="p-4" value="yes">
                    <label for="spotting" class="w-100 fs-5">Spotting</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="edema" class="p-4" value="yes">
                    <label for="edema" class="w-100 fs-5">Edema</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="severe_headache" class="p-4" value="yes">
                    <label for="severe_headache" class="w-100 fs-5">severe headache</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="blumming_of_vission" class="p-4" value="yes">
                    <label for="blumming_of_vission" class="w-100 fs-5">blumming of vision</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="watery_discharge" class="p-4" value="yes">
                    <label for="watery_discharge" class="w-100 fs-5">Watery discharge</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="severe_vomiting" class="p-4" value="yes">
                    <label for="severe_vomiting" class="w-100 fs-5">severe vomiting</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="hx_of_smoking" class="p-4" value="yes">
                    <label for="hx_of_smoking" class="w-100 fs-5">Hx of smoking </label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="alcohol_drinker" class="p-4" value="yes">
                    <label for="alcohol_drinker" class="w-100 fs-5">alcohol drinker</label>
                </div>
                <div class="mb-1 d-flex align-items-center gap-1">
                    <input type="checkbox" name="drug_intake" class="p-4" value="yes">
                    <label for="drug_intake" class="w-100 fs-5">Drug intake</label>
                </div>
            </div>

        </div>
        <!-- main info about pregnancy -->
        <div class="survey-questionare w-100 ">
            <div class="current-prenancy w-100 d-flex gap-3 mb-3 border-bottom">

                <div class="questions w-100">
                    <h3 class="w-100 bg-success text-white text-center">Kasaysayan ng Pagbubuntis</h3>
                    <div class="mb-4 d-flex">
                        <label for="number_of" class="w-100 fs-5" class="w-50">Bilang ng Pagbubuntis:</label>
                        <select name="number_of_children" id="number_of_children" class="form-select w-50 text-center">
                            <option value="" disabled selected>Select the number</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4+">4+</option>
                        </select>
                    </div>
                    <div class="mb-4 d-flex justify-content-between w-100">
                        <label for="sasarin" class="w-75">Nanganak ng sasarin:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter gap-3">
                            <input type="radio" id="yes" name="answer_1" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="answer_1" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 2nd -->
                    <div class="mb-4 d-flex justify-content-between w-100">
                        <label for="nakuhanan_ng_sunod" class="w-75">3 beses nakuhanan magkasunod:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="answer_2" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="answer_2" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 3rd -->
                    <div class="mb-4 d-flex justify-content-between w-100">
                        <label for="dead_child" class="w-75">Ipinanganak ng patay:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="answer_3" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="answer_3" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 4th -->
                    <div class="mb-2 d-flex justify-content-between w-100">
                        <label for="blood_after_pregnancy" class="w-75">Labis na pagdurogo matapos manganak:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="answer_4" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="answer_4" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                </div>
                <!-- 2nd questions-->
                <div class="questions w-100">
                    <h3 class="w-100 bg-success text-white text-center">Kasalukuyang Problemang Pang Kalusugan</h3>
                    <div class="mb-4 d-flex justify-content-between w-100">
                        <label for="sasarin" class="w-75">Tuberculosis(ubong labis 14 araaw):</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="q2_answer1" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="q2_answer1" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 2nd -->
                    <div class="mb-4 d-flex justify-content-between w-100">
                        <label for="sasarin" class="w-75">Sakit sa Puso:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="q2_answer2" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="q2_answer2" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 3rd -->
                    <div class="mb-4 d-flex justify-content-between w-100">
                        <label for="sasarin" class="w-75">Diabetis:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="q2_answer3" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="q2_answer3" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 4th -->
                    <div class="mb-2 d-flex justify-content-between w-100">
                        <label for="sasarin" class="w-75">Hika:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="q2_answer4" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="q2_answer4" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                    <!-- 4th -->
                    <div class="mb-2 d-flex justify-content-between w-100">
                        <label for="sasarin" class="w-75">Bisyo:</label>
                        <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                            <input type="radio" id="yes" name="q2_answer5" value="yes">
                            <label for="yes">Oo</label>
                            <input type="radio" name="q2_answer5" value="no">
                            <label for="no">HIndi</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hatol">
                <label for="" class="fw-bold fs-5">Decision</label>
                <div class="options px-5 py-2">
                    <div class="mb-2">
                        <input type="radio" name="nurse_decision" id="nurse_f1_option" value="Papuntahin sa Doktor/RHU Alamin? Sundan ang kalagayan">
                        <label for="">Papuntahin sa Doktor/RHU Alamin? Sundan ang kalagayan</label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" name="nurse_decision" id="nurse_f2_option" value="Masusing pagsusuri at aksyon ng kumadrona / Nurse">
                        <label for="">Masusing pagsusuri at aksyon ng kumadrona / Nurse</label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" name="nurse_decision" id="nurse_f3_option" value="pinayong manganak sa Ospital">
                        <label for="">Ipinayong manganak sa Ospital</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>