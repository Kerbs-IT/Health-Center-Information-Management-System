<div class="tb-dots card shadow p-md-4 p-1 mx-1">
    <div class="mb-2">
        <label for="patient_name">Patient Name</label>
        <input type="text" class="form-control w-100 w-md-50 " id="patient_name" name="patient_name" disabled value="Jan Louie Samlimbago">
    </div>
    <h4>MEDICAL INFORMATION</h4>
    <div class="mb-2 d-flex gap-2 w-100 flex-wrap flex-md-row flex-column">
        <div class="mb-md-2 mb-0 flex-fill">
            <label for="tb-type">Type of Tuberculosis(TB):</label>
            <select id="tb-type" name="tb_type" class="form-select">
                <option value="">-- Select Type --</option>
                <option value="pulmonary">Pulmonary TB</option>
                <option value="Extrapulmonary Lymph">Extrapulmonary TB - Lymph Node</option>
                <option value="Extrapulmonary Pleural">Extrapulmonary TB - Pleural</option>
                <option value="Extrapulmonary Bone Joint">Extrapulmonary TB - Bone and Joint</option>
                <option value="Extrapulmonary Genitourinary">Extrapulmonary TB - Genitourinary</option>
                <option value="Extrapulmonary Meningeal">Extrapulmonary TB - Meningeal</option>
                <option value="Extrapulmonary Abdominal">Extrapulmonary TB - Abdominal</option>
                <option value="Extrapulmonary Miliary">Extrapulmonary TB - Miliary</option>
                <option value="latent">Latent TB Infection (LTBI)</option>
                <option value="Active">Active TB Disease</option>
                <option value="Mdr">Drug-Resistant TB - MDR-TB</option>
                <option value="Xdr">Drug-Resistant TB - XDR-TB</option>
                <option value="Rr">Drug-Resistant TB - RR-TB</option>
            </select>
        </div>
        <div class="mb-md-2 mb-0 flex-fill">
            <label for="tb-case-type">Type of TB Case:</label>
            <select name="tb_case_type" id="tb-case-type" class="form-control">
                <option value="" disabled>-- Select Case Type --</option>
                <option value="New">New</option>
                <option value="Relapse">Relapse</option>
                <option value="Treatment After Failure">Treatment After Failure</option>
                <option value="Treatment After Lost_To Followup">Treatment After Lost to Follow-up (TALF)</option>
                <option value="Transfer In">Transfer In</option>
                <option value="Retreatment Others">Re-treatment Others</option>
                <option value="Previously Treated">Previously Treated</option>
                <option value="Unknown">Unknown</option>
            </select>
        </div>
        <div class="mb-md-2 mb-0 flex-fill">
            <label for="date_of_diagnosis">Date of Diagnosis</label>
            <input type="date" class="form-control" name="tb_date_of_diagnosis">
        </div>
    </div>
    <div class="mb-md-2 mb-2 d-flex gap-2 flex-wrap flex-column flex-md-row">
        <div class="mb-md-2 mb-0 flex-fill">
            <label for="name-of-physician">Name of Physician</label>
            <input type="text" class="form-control" name="name_of_physician">
        </div>
        <div class="mb-md-2 mb-0 flex-fill">
            <label for="sputum">Sputum Test Results</label>
            <input type="text" class="form-control" name="sputum_result">
        </div>
    </div>
    <h3>Medication</h3>
    <div class="mb-2 d-flex gap-2 w-100 ">
        <div class="mb-2 w-100">
            <label for="tb-medicine">TB Medicine Name:</label>
            <select id="tb_medicine" class="form-select">
                <option value="">-- Select Medicine --</option>
                <!-- First-line -->
                <option value="isoniazid">Isoniazid (INH)</option>
                <option value="rifampicin">Rifampicin (RIF)</option>
                <option value="pyrazinamide">Pyrazinamide (PZA)</option>
                <option value="ethambutol">Ethambutol (EMB)</option>
                <option value="streptomycin">Streptomycin (SM)</option>
                <!-- FDCs -->
                <option value="hrze">HRZE (INH + RIF + PZA + EMB)</option>
                <option value="hr">HR (INH + RIF)</option>
                <!-- Second-line -->
                <option value="levofloxacin">Levofloxacin (LFX)</option>
                <option value="moxifloxacin">Moxifloxacin (MFX)</option>
                <option value="bedaquiline">Bedaquiline (BDQ)</option>
                <option value="linezolid">Linezolid (LZD)</option>
                <option value="clofazimine">Clofazimine (CFZ)</option>
                <option value="cycloserine">Cycloserine (CS)</option>
                <option value="delamanid">Delamanid (DLM)</option>
                <option value="amikacin">Amikacin (AMK)</option>
                <option value="kanamycin">Kanamycin (KM)</option>
            </select>
        </div>
        <div class="mb-2 d-flex gap-2 w-100 flex-column flex-md-row">
            <div class="mb-0 mb-md-2 flex-fill">
                <label for="dosage-frequency">Dosage / Frequency</label>
                <input type="text" class="form-control" id="tb_dosage_n_frequency">
            </div>
            <div class="mb-0 mb-md-2 flex-fill">
                <label for="duration">Quantity</label>
                <input type="number" class="form-control" id="tb_quantity">
            </div>
            <div class="mb-0 mb-md-2 flex-fill">
                <label for="duration">Start Date</label>
                <input type="date" class="form-control" id="tb_start_date">
            </div>
            <div class="mb-0 mb-md-2 flex-fill">
                <label for="duration">End Date</label>
                <input type="date" class="form-control" id="tb_end_date">
            </div>
            <div class="mb-0 mb-md-2 flex flex-column">
                <label for="" class="text-white">e</label>
                 <button type="button" class="btn btn-success px-4" id="tb_medicine_add_btn">Add</button>
            </div>
        </div>
    </div>
    <div class="mb-2 table-responsive">
        <!-- table -->
        <table class="w-100 table">
            <thead>
                <tr class="table-header text-nowrap">
                    <th>Medicine Name</th>
                    <th>Dosage & Frequency</th>
                    <th>Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="add_patient_tb_table_body">
                <!-- <tr>
                    <td>Izoniazid</td>
                    <td>1 tablet/day</td>
                    <td>6 months</td>
                    <td>2025-01-01</td>
                    <td>2025-02-01</td>
                    <td class=" align-middle text-center">
                        <div class="delete-icon d-flex align-items-center justify-self-center w-100 h-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" width="20" height="20" viewBox="0 0 448 512">
                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                            </svg>
                        </div>
                    </td>
                </tr> -->
            </tbody>
        </table>
    </div>
    <div class="mb-2 d-flex gap-2 w-100">
        <div class="mb-2 w-100 ">
            <div class="mb-2 w-100">
                <label for="tb-medicine">Treatment Category:</label>
                <select name="treatment_medicine_name" id="tb-medicine" class="form-select">
                    <option value="">-- Select Medicine --</option>
                    <!-- First-line -->
                    <option value="isoniazid">Isoniazid (INH)</option>
                    <option value="rifampicin">Rifampicin (RIF)</option>
                    <option value="pyrazinamide">Pyrazinamide (PZA)</option>
                    <option value="ethambutol">Ethambutol (EMB)</option>
                    <option value="streptomycin">Streptomycin (SM)</option>
                    <!-- FDCs -->
                    <option value="hrze">HRZE (INH + RIF + PZA + EMB)</option>
                    <option value="hr">HR (INH + RIF)</option>
                    <!-- Second-line -->
                    <option value="levofloxacin">Levofloxacin (LFX)</option>
                    <option value="moxifloxacin">Moxifloxacin (MFX)</option>
                    <option value="bedaquiline">Bedaquiline (BDQ)</option>
                    <option value="linezolid">Linezolid (LZD)</option>
                    <option value="clofazimine">Clofazimine (CFZ)</option>
                    <option value="cycloserine">Cycloserine (CS)</option>
                    <option value="delamanid">Delamanid (DLM)</option>
                    <option value="amikacin">Amikacin (AMK)</option>
                    <option value="kanamycin">Kanamycin (KM)</option>
                </select>
            </div>
        </div>

    </div>
    <h4>MONITORING & PROGRESS</h4>
    <!-- monitoring progress -->
    <div class="mb-2 d-flex gap-2 flex-wrap flex-column flex-md-row">
        <div class="mb-2 flex-fill">
            <label for="date_of_diagnosis">Date of Medication Administered</label>
            <input type="date" class="form-control" name="tb_date_of_medication_administered">
        </div>
        <div class="mb-2 flex-fill">
            <label for="date_of_diagnosis">Side effect(if any)</label>
            <input type="text" class="form-control" name="treatment_side_effect">
        </div>
    </div>
    <div class="mb-2" id="w-100">
        <label for="">Remarks</label>
        <input type="text" class="form-control" name="tb_remarks">
    </div>
    <div class="mb-2" id="w-100">
        <label for="">Outcome</label>
        <input type="text" class="form-control" name="tb_outcome">
    </div>
    <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mt-2">
        <button type="button" class="bg-red-700 hover:bg-red-800 text-white px-5 py-2  fs-5 rounded" onclick="prevStep()">Back</button>
        <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 fs-5 rounded" id="tb_dots_save_record_btn">Save Record</button>
    </div>
</div>
