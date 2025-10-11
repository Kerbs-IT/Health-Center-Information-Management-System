<div class="tb-dots card shadow p-4">
    <div class="mb-2">
        <label for="patient_name">Patient Name</label>
        <input type="text" class="form-control w-25" id="patient_name" name="patient_name" disabled value="{{$patient_name}}">
    </div>
    <h4>MEDICAL INFORMATION</h4>
    <div class="mb-2 d-flex gap-2 w-100">
        <div class="mb-2 w-50">
            <label for="tb-type">Type of Tuberculosis(TB):</label>
            <select id="edit_type_of_tuberculosis" name="edit_type_of_tuberculosis" class="form-select">
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
        <div class="mb-2 w-50">
            <label for="tb-case-type">Type of TB Case:</label>
            <select name="edit_type_of_tb_case" id="edit_type_of_tb_case" class="form-select">
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
        <div class="mb-2 w-50">
            <label for="date_of_diagnosis">Date of Diagnosis</label>
            <input type="date" class="form-control" name="edit_date_of_diagnosis" id="edit_date_of_diagnosis">
        </div>
    </div>
    <div class="mb-2 d-flex gap-2">
        <div class="mb-2 w-50">
            <label for="name-of-physician">Name of Physician</label>
            <input type="text" class="form-control" name="edit_name_of_physician" id="edit_name_of_physician">
        </div>
        <div class="mb-2 w-50">
            <label for="sputum">Sputum Test Results</label>
            <input type="text" class="form-control" name="edit_sputum_test_results" id="edit_sputum_test_results">
        </div>

    </div>
    <h3>Medication</h3>
    <div class="mb-2 d-flex gap-2 w-100 ">
        <div class="mb-2 w-25">
            <label for="tb-medicine">TB Medicine Name:</label>
            <select id="edit_tb_medicine" class="form-select">
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
        <div class="mb-2 d-flex gap-2 w-100">
            <div class="mb-2 w-50">
                <label for="dosage-frequency">Dosage / Frequency</label>
                <input type="text" class="form-control" id="edit_tb_dosage_n_frequency">
            </div>
            <div class="mb-2 w-50">
                <label for="duration">Quantity</label>
                <input type="number" class="form-control" id="edit_tb_quantity">
            </div>
            <div class="mb-2 w-50">
                <label for="duration">Start Date</label>
                <input type="date" class="form-control" id="edit_tb_start_date">
            </div>
            <div class="mb-2 w-50">
                <label for="duration">End Date</label>
                <input type="date" class="form-control" id="edit_tb_end_date">
            </div>
            <div class="mb2">
                <label for="" class="text-white">e</label>
                <button type="button" class="btn btn-success px-4" id="edit_tb_medicine_add_btn">Add</button>
            </div>
        </div>
    </div>
    <div class="mb-2">
        <!-- table -->
        <table class="w-100 table">
            <thead>
                <tr class="table-header">
                    <th>Medicine Name</th>
                    <th>Dosage & Frequency</th>
                    <th>Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="edit_tb_tbody">
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No records available.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mb-2 d-flex gap-2 w-100">
        <div class="mb-2 w-50 ">
            <div class="mb-2 w-100">
                <label for="tb-medicine">Treatment Category:</label>
                <select name="edit_treatment_category" id="edit_treatment_category" class="form-select">
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
    <div class="mb-2 d-flex gap-2">
        <div class="mb-2 w-50">
            <label for="date_of_diagnosis">Date of Diagnosis</label>
            <input type="date" class="form-control" name="edit_date_administered" id="edit_date_administered">
        </div>
        <div class="mb-2 w-50">
            <label for="date_of_diagnosis">Side effect(if any)</label>
            <input type="text" class="form-control" name="edit_side_effect" id="edit_side_effect">
        </div>
    </div>
    <div class="mb-2" id="w-100">
        <label for="">Remarks</label>
        <input type="text" class="form-control" name="edit_tb_remarks" id="edit_remarks">
    </div>
    <div class="mb-2" id="w-100">
        <label for="">Outcome</label>
        <input type="text" class="form-control" name="edit_tb_outcome" id="edit_outcome">
    </div>
</div>