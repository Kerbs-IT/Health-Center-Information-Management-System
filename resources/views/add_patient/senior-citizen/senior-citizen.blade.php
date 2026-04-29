<div class="d-flex p-md-4 p-2 flex-column">
    <div class="contents bottom-border">
        <div class="mb-3">
            <label for="">Patient Name</label>
            <input type="text" id="senior_patient_name" class="form-control" disabled>
        </div>
        <h5>MEDICAL INFORMATION</h5>
        <div class="mb-3">
            <label for="">Existing Medical Condition</label>
            <input type="text" class="form-control" name="existing_medical_condition" id="existing_medical_condition">
            <small class="text-danger error-text" id="existing_medical_condition_error"></small>
        </div>
        <div class="mb-3">
            <label for="">Alergies</label>
            <input type="text" class="form-control" name="alergies" id="alergies">
            <small class="text-danger error-text" id="alergies_error"></small>
        </div>
        <div class="maintenance-con d-flex gap-2 flex-wrap flex-xl-nowrap justify-content-end mb-md-0 mb-1 flex-md-row flex-column">
            <div class="mb-md-3 mb-0 flex-fill xl:w-[20%]">
                <label class="text-nowrap" for="" class="text-nowrap">Maintenance Medication</label>
                <!-- <select name="" id="medication" class="form-select">
                    <option value="" selected disabled>Select a Medication</option>
                    <option value="amlodipine">Amlodipine 5mg</option>
                </select> -->
                <input type="text" class="form-control" id="maintenance_medication">
            </div>
            <div class="mb-md-3 mb-0 flex-fill xl:w-[20%]">
                <label class="text-nowrap" for="">Dosage & Frequency</label>
                <input type="text" class="form-control" id="dosage_n_frequency">
            </div>
            <div class="mb-md-3 mb-0 flex-fill xl:w-[20%]">
                <label class="text-nowrap" for="">Quantity</label>
                <input type="number" class="form-control" id="maintenance_quantity" min="0" oninput="this.value = Math.max(0, parseInt(this.value) || 0)">
            </div>
            <div class="mb-md-3 mb-0 flex-fill xl:w-[20%]">
                <label class="text-nowrap" for="">Start Date</label>
                <input type="date" class="form-control" id="maintenance_start_date" max="{{date('Y-m-d')}}" oninput="syncEndDateMin(this.value)">
            </div>
            <div class="mb-md-3 mb-0 flex-fill xl:w-[20%]">
                <label class="text-nowrap" for="">End Date</label>
                <input type="date" class="form-control" id="maintenance_end_date">
            </div>
            <div class="mb-md-3 mb-0 d-flex flex-column justify-content-end">
                <label class="text-nowrap" for="" class="text-white" style="color: white !important;">End</label>
                <button type="button" class="btn btn-success" id="medication_add_btn">Add</button>
            </div>
        </div>
        <!-- table -->
        <div class="table-responsive">
            <table class="w-100 table">
                <thead>
                    <tr class="table-header">
                        <th>Maintenance Medication</th>
                        <th>Dosage & Frequency</th>
                        <th>Quantity</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="medication_table_body">
                    <tr id="no-medication-row">
                        <td colspan="6" class="text-center text-muted py-3">No record added.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- prescribing  -->
        <div class="mb-3">
            <label for="">Prescribe by Nurse</label>
            <input type="text"
                class="form-control"
                value="{{ $nurseFullName }}"
                disabled>
            <input type="hidden"
                name="prescribe_by_nurse"
                value="{{ $nurseFullName }}">
            <small class="text-danger error-text" id="prescribe_by_nurse_error"></small>
        </div>
        <div class="mb-3 border-bottom">
            <label for="" class="text-nowrap">Remarks</label>
            <input type="text" class="form-control p-3" name="medication_maintenance_remarks" id="medication_maintenance_remarks">
            <small class="text-danger error-text" id="remarks_error"></small>
        </div>
        <div class="mb-3 ">
            <label for="senior_citizen_date_of_comeback">Date of Comeback<span class="text-danger">*</span></label>
            <input type="date" class="form-control border" name="senior_citizen_date_of_comeback" id="senior_citizen_date_of_comeback" max="{{date('Y-m-d',strtotime('+5 years'))}}">
        </div>
    </div>
    <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto flex-column flex-sm-row">
        <button type="button" class="btn btn-danger px-5 py-2 text-nowrap" onclick="prevStep()">Back</button>
        <button type="submit" class="btn btn-success px-5 py-2 text-nowrap" id="senior_citizen_save_record_btn">Save Record</button>
    </div>
</div>