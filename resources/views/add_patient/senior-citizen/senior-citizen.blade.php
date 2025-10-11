<div class="d-flex p-4 flex-column">
    <div class="contents bottom-border">
        <div class="mb-3">
            <label for="">Patient Name:</label>
            <input type="text" id="patient_name" class="form-control">
        </div>
        <h5>MEDICAL INFORMATION</h5>
        <div class="mb-3">
            <label for="">Existing Medical Condition</label>
            <input type="text" class="form-control" name="existing_medical_condition" id="existing_medical_condition">
        </div>
        <div class="mb-3">
            <label for="">Alergies</label>
            <input type="text" class="form-control" name="alergies" id="alergies">
        </div>
        <div class="maintenance-con d-flex gap-2 align-items-center ">
            <div class="mb-3">
                <label for="" class="text-nowrap">Maintenance Medication</label>
                <!-- <select name="" id="medication" class="form-select">
                    <option value="" selected disabled>Select a Medication</option>
                    <option value="amlodipine">Amlodipine 5mg</option>
                </select> -->
                <input type="text" class="form-control" id="maintenance_medication">
            </div>
            <div class="mb-3">
                <label for="">Dosage & Frequency</label>
                <input type="text" class="form-control" id="dosage_n_frequency">
            </div>
            <div class="mb-3">
                <label for="">Quantity</label>
                <input type="Number" class="form-control" id="maintenance_quantity">
            </div>
            <div class="mb-3">
                <label for="">Start Date</label>
                <input type="date" class="form-control" id="maintenance_start_date">
            </div>
            <div class="mb-3">
                <label for="">End Date</label>
                <input type="date" class="form-control" id="maintenance_end_date">
            </div>
            <div class="mb-3">
                <label for="" class="text-white">End Date</label>
                <button type="button" class="btn btn-success" id="medication_add_btn">Add</button>
            </div>
        </div>
        <!-- table -->
        <table class="w-100 table">
            <thead>
                <tr class="table-header">
                    <th>Maintenance Medication</th>
                    <th>Dosage & Frequency</th>
                    <th>Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="medication_table_body">
                <!-- <tr>
                    <td>Amlodipine 5mg</td>
                    <td>1x/day</td>
                    <td>90 days</td>
                    <td>2025-01-01</td>
                    <td>2025-02-01</td>
                    <td class=" align-middle text-center">
                        <div class="delete-icon d-flex align-items-center justify-self-center w-100 h-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" width="20" height="20" viewBox="0 0 448 512">
                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                            </svg>
                        </div>
                    </td>
                    <input type="hidden" name="medicines[]" value="">
                </tr> -->
            </tbody>
        </table>
        <!-- prescribing  -->
        <div class="mb-3">
            <label for="">Prescribe by Nurse</label>
            <input type="text" class="form-control" name="prescribe_by_nurse" id="prescribe_by_nurse">
        </div>
        <div class="mb-3 border-bottom">
            <label for="" class="text-nowrap">Remarks *</label>
            <input type="text" class="form-control p-3" name="medication_maintenance_remarks" id="medication_maintenance_remarks">
        </div>
    </div>
    <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto">
        <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
        <button type="submit" class="btn btn-success px-5 py-2 fs-5" id="senior_citizen_save_record_btn">Save Record</button>
    </div>
</div>