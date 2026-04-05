<div class="card shadow p-md-4 p-1 mx-1 d-flex flex-column" style="height: 100%;">

    <h4>GENERAL CONSULTATION</h4>

    <!-- Date -->
    <div class="mb-2">
        <label for="gc_date">Date<span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="gc_date" name="gc_date" value="{{date('Y-m-d')}}" required min="1950-01-01" max="{{date('Y-m-d')}}">
        <small class="text-danger error-text" id="gc_date_error"></small>
    </div>

    <!-- S - Symptoms / Chief Complaint -->
    <div class="mb-2">
        <label for="gc_symptoms">S(Symptoms / Chief Complaint)<span class="text-danger">*</span></label>
        <textarea class="form-control" id="gc_symptoms" name="gc_symptoms" rows="3" placeholder="Describe the patient's symptoms or chief complaint..."></textarea>
        <small class="text-danger error-text" id="gc_symptoms_error"></small>
    </div>

    <!-- A - Diagnosis / Assessment -->
    <div class="mb-2">
        <label for="gc_diagnosis">A(Diagnosis / Assessment)<span class="text-danger">*</span></label>
        <textarea class="form-control" id="gc_diagnosis" name="gc_diagnosis" rows="3" placeholder="Enter the diagnosis or clinical assessment..."></textarea>
        <small class="text-danger error-text" id="gc_diagnosis_error"></small>
    </div>

    <!-- P - Treatment / Plan -->
    <div class="mb-2">
        <label for="gc_treatment">P(Treatment / Plan)<span class="text-danger">*</span></label>
        <textarea class="form-control" id="gc_treatment" name="gc_treatment" rows="3" placeholder="Describe the treatment plan, medications, or instructions..."></textarea>
        <small class="text-danger error-text" id="gc_treatment_error"></small>
    </div>

    <!-- Action Buttons -->
    <div class="buttons w-100 align-self-center d-flex flex-column flex-sm-row justify-content-end gap-2 mt-auto">
        <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
        <button type="button" class="btn btn-success px-5 py-2 fs-5" id="gc_save_record_btn">Save Record</button>
    </div>

</div>