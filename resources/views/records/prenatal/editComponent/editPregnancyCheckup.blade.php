<div class="inner w-100 rounded">

    <div class="mb-2 w-100">
        <label for="patient_name">Patient Name</label>
        <input type="text" class="form-control bg-light" disabled placeholder="Enter the name" id="edit_patient_name">
        <input type="hidden" class="form-control bg-light" name="edit_check_up_full_name" id="edit_check_up_full_name">
    </div>

    <div class="mb-2 w-100">
        <label for="administered_by">Administered By</label>
        <input type="text" class="form-control bg-light" name="check_up_handled_by" disabled placeholder="Nurse" id="edit_check_up_handled_by">
        <input type="hidden" class="form-control bg-light" name="edit_health_worker_id" placeholder="Nurse" id="edit_health_worker_id">
    </div>
    <div class="mb-2 w-100">
        <label for="time_of_vaccination">Time</label>
        <input type="time" class="form-control" name="edit_check_up_time" id="edit_check_up_time" step="1">
    </div>

    <div class="vital-sign w-100 border-bottom">
        <h5>Vital Sign</h5>
        <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
            <div class="mb-2 w-50">
                <label for="BP">Blood Pressure:</label>
                <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="edit_check_up_blood_pressure" id="edit_check_up_blood_pressure">
                <small class="text-danger" id="edit_check_up_blood_pressure_error"></small>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Temperature:</label>
                <input type="number" class="form-control w-100" placeholder="00 C" name="edit_check_up_temperature" id="edit_check_up_temperature">
                <small class="text-danger" id="edit_check_up_temperature_error"></small>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Pulse Rate(Bpm):</label>
                <input type="text" class="form-control w-100" placeholder=" 60-100" name="edit_check_up_pulse_rate" id="edit_check_up_pulse_rate">
                <small class="text-danger" id="edit_check_up_pulse_rate_error"></small>
            </div>

        </div>
        <!-- 2nd row -->
        <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
            <div class="mb-2 w-50">
                <label for="BP">Respiratory Rate (breaths/min):</label>
                <input type="text" class="form-control w-100" placeholder="ex. 25" name="edit_check_up_respiratory_rate" id="edit_check_up_respiratory_rate">
                <small class="text-danger" id="edit_check_up_respiratory_rate_error"></small>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Height(cm):</label>
                <input type="number" class="form-control w-100" placeholder="00.00" name="edit_check_up_height" id="edit_check_up_height">
                <small class="text-danger" id="edit_check_up_height_error"></small>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Weight(kg):</label>
                <input type="number" class="form-control w-100" placeholder=" 00.00" name="edit_check_up_weight" id="edit_check_up_weight">
                <small class="text-danger" id="edit_check_up_weight_error"></small>
            </div>
        </div>
        <!-- 3rd row -->
    </div>
    <!-- QUESTIONS -->
    <div class="my-4">
        <h5 class="mb-4">Prenatal Symptoms and Concerns</h5>
        <!-- Question 1 -->
        <div class="mb-3">
            <label class="form-label">1. Do you have any pain in your lower abdomen or back?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_abdomen_question" value="Yes" id="edit_abdomen_question_Yes">
                    <label class="form-check-label" for="q1-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_abdomen_question" value="No" id="edit_abdomen_question_No">
                    <label class="form-check-label" for="q1-no">No</label>
                </div>
                <small class="text-danger" id="edit_abdomen_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_abdomen_question_remarks" id="edit_abdomen_question_remarks">
                <small class="text-danger" id="edit_abdomen_question_remarks_error"></small>
            </div>
        </div>

        <!-- Question 2 -->
        <div class="mb-3">
            <label class="form-label">2. Have you experienced any vaginal bleeding or spotting?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_vaginal_question" value="Yes" id="edit_vaginal_question_Yes">
                    <label class="form-check-label" for="q2-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_vaginal_question" value="No" id="edit_vaginal_question_No">
                    <label class="form-check-label" for="q2-no">No</label>
                </div>
                <small class="text-danger" id="edit_vaginal_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_vaginal_question_remarks" id="edit_vaginal_question_remarks">
                <small class="text-danger" id="edit_vaginal_question_remarks_error"></small>
            </div>
        </div>

        <!-- Question 3 -->
        <div class="mb-3">
            <label class="form-label">3. Do you have swelling in your hands, feet, or face?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_swelling_question" value="Yes" id="edit_swelling_question_Yes">
                    <label class="form-check-label" for="q3-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_swelling_question" value="No" id="edit_swelling_question_No">
                    <label class="form-check-label" for="q3-no">No</label>
                </div>
                <small class="text-danger" id="edit_swelling_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_swelling_question_remarks" id="edit_swelling_question_remarks">
                <small class="text-danger" id="edit_swelling_question_remarks_error"></small>
            </div>
        </div>

        <!-- Question 4 -->
        <div class="mb-3">
            <label class="form-label">4. Do you have persistent headache?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_headache_question" value="Yes" id="edit_headache_question_Yes">
                    <label class="form-check-label" for="q4-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_headache_question" value="No" id="edit_headache_question_No">
                    <label class="form-check-label" for="q4-no">No</label>
                </div>
                <small class="text-danger" id="edit_headache_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_headache_question_remarks" id="edit_headache_question_remarks">
                <small class="text-danger" id="edit_headache_question_remarks_error"></small>
            </div>
        </div>
        <!-- Question 5 -->
        <div class="mb-3">
            <label class="form-label">5. Do you have Blurry vision or flashing lights??</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_blurry_vission_question" value="Yes" id="edit_blurry_vission_question_Yes">
                    <label class="form-check-label" for="q5-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_blurry_vission_question" value="No" id="edit_blurry_vission_question_No">
                    <label class="form-check-label" for="q5-no">No</label>
                </div>
                <small class="text-danger" id="edit_blurry_vission_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_blurry_vission_question_remarks" id="edit_blurry_vission_question_remarks">
                <small class="text-danger" id="edit_blurry_vission_question_remarks_error"></small>
            </div>
        </div>
        <!-- Question 6 -->
        <div class="mb-3">
            <label class="form-label">6. Do you have painful or frequent urination?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_urination_question" value="Yes" id="edit_urination_question_Yes">
                    <label class="form-check-label" for="q6-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_urination_question" value="No" id="edit_urination_question_No">
                    <label class="form-check-label" for="q6-no">No</label>
                </div>
                <small class="text-danger" id="edit_urination_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_urination_question_remarks" id="edit_urination_question_remarks">
                <small class="text-danger" id="edit_urination_question_remarks_error"></small>
            </div>
        </div>
        <!-- Question 7 -->
        <div class="mb-3">
            <label class="form-label">7. Do you have Felt baby move? (if after 20 weeks)?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_baby_move_question" value="Yes" id="edit_baby_move_question_Yes">
                    <label class="form-check-label" for="q7-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_baby_move_question" value="No" id="edit_baby_move_question_No">
                    <label class="form-check-label" for="q7-no">No</label>
                </div>
                <small class="text-danger" id="edit_baby_move_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_baby_move_question_remarks" id="edit_baby_move_question_remarks">
                <small class="text-danger" id="edit_baby_move_question_remarks_error"></small>
            </div>
        </div>

        <!-- Question 9 -->
        <div class="mb-3">
            <label class="form-label">8. Do you feel decreased baby movement?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_decreased_baby_movement" value="Yes" id="edit_decreased_baby_movement_Yes">
                    <label class="form-check-label" for="q9-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_decreased_baby_movement" value="No" id="edit_decreased_baby_movement_No">
                    <label class="form-check-label" for="q9-no">No</label>
                </div>
                <small class="text-danger" id="edit_decreased_baby_movement_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_decreased_baby_movement_remarks" id="edit_decreased_baby_movement_remarks">
                <small class="text-danger" id="edit_decreased_baby_movement_remarks_error"></small>
            </div>
        </div>
        <!-- Question 10 -->
        <div class="mb-3">
            <label class="form-label">9. Do you have feel Other concerns or symptoms?</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="" type="radio" name="edit_other_symptoms_question" value="Yes" id="edit_other_symptoms_question_Yes">
                    <label class="form-check-label" for="q10-yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="" type="radio" name="edit_other_symptoms_question" value="No" id="edit_other_symptoms_question_No">
                    <label class="form-check-label" for="q10-no">No</label>
                </div>
                <small class="text-danger" id="edit_other_symptoms_question_error"></small>
                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="edit_other_symptoms_question_remarks">
                <small class="text-danger" id="edit_other_symptoms_question_remarks_error"></small>
            </div>
        </div>
        <!-- overall remarks -->
        <div class="mb-2 w-100">
            <label for="remarks">Remarks*</label>
            <input type="text" class="form-control" name="edit_overall_remarks" id="edit_overall_remarks">
        </div>
        <div class="mb-2 w-100">
            <label for="">Date of comeback</label>
            <input type="date" class="form-control" name="edit_date_of_comeback" id="edit_date_of_comeback">
        </div>
    </div>
</div>