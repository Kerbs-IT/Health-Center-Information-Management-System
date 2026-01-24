<div class="card shadow">
    <div class="card-body">
        <!-- Date of Visit -->
        <div class="mb-3">
            <label for="visit_date" class="form-label">Date of Visit<span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="edit_checkup_date_of_visit" name="edit_checkup_date_of_visit" min="1950-01-01" max="{{date('Y-m-d')}}" value="<?= date('Y-m-d') ?>">
            <input type="hidden" name="patient_name" value="{{$patient_name}}">
            <input type="hidden" name="handled_by" value="{{$healthWorkerId}}">
        </div>

        <!-- Weight / Vitals -->
        <div class="vital-sign w-100">
            <h5>Vital Sign</h5>
            <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                <div class="mb-2 w-50">
                    <label for="BP">Blood Pressure:</label>
                    <input type="text" class="form-control w-100" placeholder="Enter the blood pressure" id="edit_checkup_blood_pressure" name="edit_checkup_blood_pressure">
                    <small class="text-danger error-text" id="edit_checkup_blood_pressure_error"></small>
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Temperature(°C):</label>
                    <input type="text" class="form-control w-100" placeholder="Enter the temperature" id="edit_checkup_temperature" name="edit_checkup_temperature">
                    <small class="text-danger error-text" id="edit_checkup_temperature_error"></small>
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Pulse Rate(Bpm):</label>
                    <input type="text" class="form-control w-100" placeholder="Enter the pulse rate" id="edit_checkup_pulse_rate" name="edit_checkup_pulse_rate">
                    <small class="text-danger error-text" id="edit_checkup_pulse_rate_error"></small>
                </div>

            </div>
            <!-- 2nd row -->
            <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                <div class="mb-2 w-50">
                    <label for="BP">Respiratory Rate (breaths/min):</label>
                    <input type="text" class="form-control w-100" placeholder="Enter the respiratory rate" id="edit_checkup_respiratory_rate" name="edit_checkup_respiratory_rate">
                    <small class="text-danger error-text" id="edit_checkup_respiratory_rate_error"></small>
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Height(cm):</label>
                    <input type="text" class="form-control w-100" placeholder="Enter the height" id="edit_checkup_height" name="edit_checkup_height">
                    <small class="text-danger error-text" id="edit_checkup_height_error"></small>
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Weight(kg):</label>
                    <input type="text" class="form-control w-100" placeholder="Enter the weight" id="edit_checkup_weight" name="edit_checkup_weight">
                    <small class="text-danger error-text" id="edit_checkup_weight_error"></small>
                </div>
            </div>
        </div>

        <!-- Adherence -->
        <div class="mb-3">
            <label for="adherence" class="form-label">Adherence to Treatment<span class="text-danger">*</span></label>
            <select class="form-select" id="edit_checkup_adherence_of_treatment" name="edit_checkup_adherence_of_treatment">
                <option value="">-- Select Option --</option>
                <option value="No Missed">No missed doses</option>
                <option value="Missed 1-2">Missed 1–2 doses</option>
                <option value="Missed Multiple">Missed multiple doses</option>
            </select>
            <small class="text-danger error-text" id="edit_checkup_adherence_of_treatment_error"></small>
        </div>

        <!-- Side Effects -->
        <div class="mb-3">
            <label for="side_effects" class="form-label">Side Effects</label>
            <input type="text" class="form-control" id="edit_checkup_side_effect" name="edit_checkup_side_effect" placeholder="e.g., nausea, rashes">
            <small class="text-danger error-text" id="edit_checkup_side_effect_error"></small>
        </div>

        <!-- Progress Note -->
        <div class="mb-3">
            <label for="progress_note" class="form-label">Progress Note</label>
            <textarea class="form-control" id="edit_checkup_progress_note" name="edit_checkup_progress_note" rows="3" placeholder="Enter doctor's or nurse's notes here"></textarea>
            <small class="text-danger error-text" id="edit_checkup_progress_note_error"></small>
        </div>

        <!-- Sputum Test Result -->
        <div class="mb-3">
            <label for="sputum_result" class="form-label">Sputum Test Result</label>
            <select class="form-select" id="edit_checkup_sputum_test_result" name="edit_checkup_sputum_test_result">
                <option value="">-- Select Result --</option>
                <option value="Not Done">Not Done</option>
                <option value="Positive">Positive</option>
                <option value="Negative">Negative</option>
            </select>
            <small class="text-danger error-text" id="edit_checkup_sputum_test_result_error"></small>
        </div>

        <!-- Treatment Phase -->
        <div class="mb-3">
            <label for="treatment_phase" class="form-label">Treatment Phase</label>
            <select class="form-select" id="edit_checkup_treatment_phase" name="edit_checkup_treatment_phase">
                <option value="">-- Select Phase --</option>
                <option value="intensive">Intensive Phase</option>
                <option value="continuation">Continuation Phase</option>
            </select>
            <small class="text-danger error-text" id="edit_checkup_treatment_phase_error"></small>
        </div>

        <!-- Outcome Update -->
        <div class="mb-3">
            <label for="treatment_outcome" class="form-label">Outcome Update</label>
            <select class="form-select" id="edit_checkup_outcome" name="edit_checkup_outcome">
                <option value="">-- Select Outcome --</option>
                <option value="ongoing">Ongoing Treatment</option>
                <option value="cured">Cured</option>
                <option value="transferred">Transferred</option>
                <option value="defaulted">Defaulted</option>
                <option value="died">Died</option>
                <option value="treatment_failed">Treatment Failed</option>
            </select>
            <small class="text-danger error-text" id="edit_checkup_outcome_error"></small>
        </div>
        <div class="mb-3">
            <label for="edit_date_of_comeback">Date of Comeback<span class="text-danger">*</span></label>
            <input type="date" name="edit_date_of_comeback" class="form-control" id="edit_date_of_comeback" min="1950-01-01" max="{{date('Y-m-d',strtotime('+5 years'))}}">
            <small class="text-danger error-text" id="edit_date_of_comeback_error"></small>
        </div>
    </div>
</div>