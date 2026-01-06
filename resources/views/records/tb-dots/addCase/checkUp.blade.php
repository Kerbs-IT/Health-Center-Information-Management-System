<div class="card shadow">
    <div class="card-body">
        <!-- Date of Visit -->
        <div class="mb-3">
            <label for="visit_date" class="form-label">Date of Visit</label>
            <input type="date" class="form-control" id="visit_date" name="date_of_visit" value="<?= date('Y-m-d') ?>">
            <input type="hidden" name="patient_name" value="{{$patient_name}}">
            <input type="hidden" name="handled_by" value="{{$healthWorkerId}}">
        </div>

        <!-- Weight / Vitals -->
        <div class="vital-sign w-100">
            <h5>Vital Sign</h5>
            <div class="mb-2 input-field d-flex gap-3 w-100 first-row  flex-wrap flex-xl-nowrap flex-md-row flex-column">
                <div class="mb-md-2 mb-1 flex-fill xl:w-[50%]">
                    <label for="BP">Blood Pressure:</label>
                    <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="blood_pressure">
                    <small class="text-danger error-text" id="blood_pressure_error"></small>
                </div>
                <div class="mb-md-2 mb-1 flex-fill xl:w-[50%]">
                    <label for="BP">Temperature:</label>
                    <input type="number" class="form-control w-100" placeholder="00 C" name="temperature">
                    <small class="text-danger error-text" id="temperature_error"></small>
                </div>
                <div class="mb-md-2 mb-1 flex-fill xl:w-[50%]">
                    <label for="BP">Pulse Rate(Bpm):</label>
                    <input type="text" class="form-control w-100" placeholder=" 60-100" name="pulse_rate">
                    <small class="text-danger error-text" id="pulse_rate_error"></small>
                </div>

            </div>
            <!-- 2nd row -->
            <div class="mb-2 input-field d-flex gap-3 w-100 second-row flex-wrap flex-xl-nowrap flex-md-row flex-column">
                <div class="mb-md-2 mb-1 flex-fill xl:w-[50%]">
                    <label for="BP">Respiratory Rate (breaths/min):</label>
                    <input type="text" class="form-control w-100" placeholder="ex. 25" name="respiratory_rate">
                    <small class="text-danger error-text" id="respiratory_rate_error"></small>
                </div>
                <div class="mb-md-2 mb-1 flex-fill xl:w-[50%]">
                    <label for="BP">Height(cm):</label>
                    <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                    <small class="text-danger error-text" id="height_error"></small>
                </div>
                <div class="mb-md-2 mb-1 flex-fill xl:w-[50%]">
                    <label for="BP">Weight(kg):</label>
                    <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight">
                    <small class="text-danger error-text" id="weight_error"></small>
                </div>
            </div>
        </div>

        <!-- Adherence -->
        <div class="mb-3">
            <label for="adherence" class="form-label">Adherence to Treatment</label>
            <select class="form-select" id="adherence" name="adherence_of_treatment">
                <option value="">-- Select Option --</option>
                <option value="No Missed">No missed doses</option>
                <option value="Missed 1-2">Missed 1–2 doses</option>
                <option value="Missed Multiple">Missed multiple doses</option>
            </select>
            <small class="text-danger error-text" id="adherence_of_treatment_error"></small>
        </div>

        <!-- Side Effects -->
        <div class="mb-3">
            <label for="side_effects" class="form-label">Side Effects</label>
            <input type="text" class="form-control" id="side_effects" name="side_effect" placeholder="e.g., nausea, rashes">
            <small class="text-danger error-text" id="side_effect_error"></small>
        </div>

        <!-- Progress Note -->
        <div class="mb-3">
            <label for="progress_note" class="form-label">Progress Note</label>
            <textarea class="form-control" id="progress_note" name="progress_note" rows="3" placeholder="Enter doctor's or nurse's notes here"></textarea>
            <small class="text-danger error-text" id="progress_note_error"></small>
        </div>

        <!-- Sputum Test Result -->
        <div class="mb-3">
            <label for="sputum_result" class="form-label">Sputum Test Result</label>
            <select class="form-select" id="sputum_result" name="sputum_test_result">
                <option value="">-- Select Result --</option>
                <option value="Not Done">Not Done</option>
                <option value="Positive">Positive</option>
                <option value="Negative">Negative</option>
            </select>
            <small class="text-danger error-text" id="sputum_test_result_error"></small>
        </div>

        <!-- Treatment Phase -->
        <div class="mb-3">
            <label for="treatment_phase" class="form-label">Treatment Phase</label>
            <select class="form-select" id="treatment_phase" name="treatment_phase">
                <option value="">-- Select Phase --</option>
                <option value="intensive">Intensive Phase</option>
                <option value="continuation">Continuation Phase</option>
            </select>
            <small class="text-danger error-text" id="treatment_phase_error"></small>
        </div>

        <!-- Outcome Update -->
        <div class="mb-3">
            <label for="treatment_outcome" class="form-label">Outcome Update</label>
            <select class="form-select" id="treatment_outcome" name="outcome">
                <option value="">-- Select Outcome --</option>
                <option value="ongoing">Ongoing Treatment</option>
                <option value="cured">Cured</option>
                <option value="transferred">Transferred</option>
                <option value="defaulted">Defaulted</option>
                <option value="died">Died</option>
                <option value="treatment_failed">Treatment Failed</option>
            </select>
            <small class="text-danger error-text" id="outcome_error"></small>
        </div>
        <div class="mb-3">
            <label for="add_date_of_comeback">Date of Comeback*</label>
            <input type="date" name="add_date_of_comeback" class="form-control" id="add_date_of_comeback">
        </div>
    </div>
</div>