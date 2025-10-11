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
            <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                <div class="mb-2 w-50">
                    <label for="BP">Blood Pressure:</label>
                    <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="blood_pressure">
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Temperature:</label>
                    <input type="number" class="form-control w-100" placeholder="00 C" name="temperature">
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Pulse Rate(Bpm):</label>
                    <input type="text" class="form-control w-100" placeholder=" 60-100" name="pulse_rate">
                </div>

            </div>
            <!-- 2nd row -->
            <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                <div class="mb-2 w-50">
                    <label for="BP">Respiratory Rate (breaths/min):</label>
                    <input type="text" class="form-control w-100" placeholder="ex. 25" name="respiratory_rate">
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Height(cm):</label>
                    <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                </div>
                <div class="mb-2 w-50">
                    <label for="BP">Weight(kg):</label>
                    <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight">
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
        </div>

        <!-- Side Effects -->
        <div class="mb-3">
            <label for="side_effects" class="form-label">Side Effects</label>
            <input type="text" class="form-control" id="side_effects" name="side_effect" placeholder="e.g., nausea, rashes">
        </div>

        <!-- Progress Note -->
        <div class="mb-3">
            <label for="progress_note" class="form-label">Progress Note</label>
            <textarea class="form-control" id="progress_note" name="progress_note" rows="3" placeholder="Enter doctor's or nurse's notes here"></textarea>
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
        </div>

        <!-- Treatment Phase -->
        <div class="mb-3">
            <label for="treatment_phase" class="form-label">Treatment Phase</label>
            <select class="form-select" id="treatment_phase" name="treatment_phase">
                <option value="">-- Select Phase --</option>
                <option value="intensive">Intensive Phase</option>
                <option value="continuation">Continuation Phase</option>
            </select>
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
        </div>
    </div>
</div>