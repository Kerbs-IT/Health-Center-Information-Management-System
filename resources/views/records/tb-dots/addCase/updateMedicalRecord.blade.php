<div class="tb-dots card shadow p-4">
    <div class="mb-2">
        <label for="patient_name">Patient Name</label>
        <input type="text" class="form-control w-25" id="patient_name" name="patient_name" disabled value="Jan Louie Samlimbago">
    </div>
    <h4>MEDICAL INFORMATION</h4>
    <div class="mb-2 d-flex gap-2 w-100">
        <div class="mb-2 w-50">
            <label for="tb-type">Type of Tuberculosis(TB):</label>
            <select name="tb_type" id="tb-type" class="form-select">
                <option value="">-- Select Type --</option>
                <option value="pulmonary">Pulmonary TB</option>
                <option value="extrapulmonary_lymph">Extrapulmonary TB - Lymph Node</option>
                <option value="extrapulmonary_pleural">Extrapulmonary TB - Pleural</option>
                <option value="extrapulmonary_bone_joint">Extrapulmonary TB - Bone and Joint</option>
                <option value="extrapulmonary_genitourinary">Extrapulmonary TB - Genitourinary</option>
                <option value="extrapulmonary_meningeal">Extrapulmonary TB - Meningeal</option>
                <option value="extrapulmonary_abdominal">Extrapulmonary TB - Abdominal</option>
                <option value="extrapulmonary_miliary">Extrapulmonary TB - Miliary</option>
                <option value="latent">Latent TB Infection (LTBI)</option>
                <option value="active">Active TB Disease</option>
                <option value="mdr">Drug-Resistant TB - MDR-TB</option>
                <option value="xdr">Drug-Resistant TB - XDR-TB</option>
                <option value="rr">Drug-Resistant TB - RR-TB</option>
            </select>
        </div>
        <div class="mb-2 w-50">
            <label for="tb-case-type">Type of TB Case:</label>
            <select name="tb_case_type" id="tb-case-type" class="form-control">
                <option value="">-- Select Case Type --</option>
                <option value="new">New</option>
                <option value="relapse">Relapse</option>
                <option value="treatment_after_failure">Treatment After Failure</option>
                <option value="treatment_after_lost_to_followup">Treatment After Lost to Follow-up (TALF)</option>
                <option value="transfer_in">Transfer In</option>
                <option value="retreatment_others">Re-treatment Others</option>
                <option value="previously_treated">Previously Treated</option>
                <option value="unknown">Unknown</option>
            </select>
        </div>
        <div class="mb-2 w-50">
            <label for="date_of_diagnosis">Date of Diagnosis</label>
            <input type="date" class="form-control">
        </div>
    </div>
    <div class="mb-2 d-flex gap-2">
        <div class="mb-2 w-50">
            <label for="name-of-physician">Name of Physician</label>
            <input type="text" class="form-control" name="name-of-physician">
        </div>
        <div class="mb-2 w-50">
            <label for="sputum">Sputum Test Results</label>
            <input type="text" class="form-control" name="spotum">
        </div>
        <div class="mb-2 w-50">
            <label for="signature_image">Upload Signature</label>
            <input type="file" name="signature_image" id="signature_image" class="form-control text-center" accept="image/*" required>
        </div>
    </div>
    <h3>Medication</h3>
    <div class="mb-2 d-flex gap-2 w-100 ">
        <div class="mb-2 w-50">
            <label for="tb-medicine">TB Medicine Name:</label>
            <select name="tb_medicine" id="tb-medicine" class="form-select">
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
                <input type="text" name="dosage-frequency" class="form-control">
            </div>
            <div class="mb-2 w-50">
                <label for="duration">Duration</label>
                <input type="text" name="dosage-frequency" class="form-control">
            </div>
            <div class="mb2">
                <label for="" class="text-white">e</label>
                <button class="btn btn-success px-4">Add</button>
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
            <tbody>
                <tr>
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
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mb-2 d-flex gap-2 w-100">
        <div class="mb-2 w-50 ">
            <div class="mb-2 w-100">
                <label for="tb-medicine">Treatment Category:</label>
                <select name="tb_medicine" id="tb-medicine" class="form-select">
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
        <div class="mb-2 w-50">
            <div class="mb-2 w-100">
                <label for="tb-medicine">Assigned health worker:</label>
                <select name="tb_medicine" id="tb-medicine" class="form-select">
                    <option value="">-- Select Worker --</option>
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
            <input type="date" class="form-control">
        </div>
        <div class="mb-2 w-50">
            <label for="date_of_diagnosis">Side effect(if any)</label>
            <input type="text" class="form-control">
        </div>
    </div>
    <div class="mb-2" id="w-100">
        <label for="">Remarks</label>
        <input type="text" class="form-control">
    </div>
    <div class="mb-2" id="w-100">
        <label for="">Outcome</label>
        <input type="text" class="form-control">
    </div>
</div>