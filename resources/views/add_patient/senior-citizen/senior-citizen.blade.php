<div class="d-flex p-lg-4 p-md-2 p-1 flex-column w-xl-75 card justify-content-center shadow">
    <div class="contents bottom-border w-100">
        <div class="mb-3 w-100">
            <label for="">Patient Name:</label>
            <input type="text" id="patient_name" class="form-control w-100">
        </div>
        <h5>MEDICAL INFORMATION</h5>
        <div class="mb-3 w-100">
            <label for="">Existing Medical Condition</label>
            <input type="text" class="form-control" name="existing_medical_condition" id="existing_medical_condition">
        </div>
        <div class="mb-3 w-100">
            <label for="">Alergies</label>
            <input type="text" class="form-control" name="alergies" id="alergies">
        </div>
        <div class="maintenance-con d-flex gap-2 flex-wrap flex-column flex-md-row">
            <div class="mb-0 mb-md-3 flex-fill" style="position:relative;">
                <label for="">Maintenance Medication</label>
                <input type="text" class="form-control" id="maintenance_medication" autocomplete="off">
                <div id="medicine_suggestions"
                    class="list-group position-absolute w-100 shadow text-dark"
                    style="z-index:999; display:none; max-height:200px; overflow-y:auto;">
                </div>
            </div>
            <!-- Hidden medicine ID -->
            <input type="hidden" id="maintenance_medicine_id">

            <div class="mb-0 mb-md-3 flex-fill">
                <label for="">Dosage & Frequency</label>
                <input type="text" class="form-control" id="dosage_n_frequency">
            </div>
            <div class="mb-0 mb-md-3 flex-fill">
                <label for="">Quantity</label>
                <input type="Number" class="form-control" id="maintenance_quantity">
            </div>
            <div class="mb-0 mb-md-3 flex-fill">
                <label for="">Start Date</label>
                <input type="date" class="form-control" id="maintenance_start_date">
            </div>
            <div class="mb-0 mb-md-3 flex-fill">
                <label for="">End Date</label>
                <input type="date" class="form-control" id="maintenance_end_date">
            </div>
            <div class="mb-2 mb-md-3 flex-fill flex flex-column">
                <label for="" class="text-white">End Date</label>
                <button type="button" class="btn btn-success" id="medication_add_btn">Add</button>
            </div>
        </div>
        <!-- table -->
         <div class="table-responsive">
            <table class="w-100 table">
                <thead>
                    <tr class="table-header text-nowrap">
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
         </div>
        <!-- prescribing  -->
        <div class="mb-md-3 mb-0">
            <label for="">Prescribe by Nurse</label>
            <input type="text" class="form-control" name="prescribe_by_nurse" id="prescribe_by_nurse">
        </div>
        <div class="mb-md-3 mb-0 border-bottom">
            <label for="" class="text-nowrap">Remarks *</label>
            <input type="text" class="form-control p-3" name="medication_maintenance_remarks" id="medication_maintenance_remarks">
        </div>
    </div>
    <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mt-2 flex-end">
        <button type="button" class=" bg-red-700 hover:bg-red-800 text-white px-5 py-2  fs-5 rounded" onclick="prevStep()">Back</button>
        <button type="submit" class=" bg-green-700 hover:bg-green-800 text-white px-5 py-2 fs-5 rounded" id="senior_citizen_save_record_btn">Save Record</button>
    </div>
</div>