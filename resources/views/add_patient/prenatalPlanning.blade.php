<div class="prenatal-planning d-flex flex-column align-items-center">
    <h1 class="text-center mb-2 planning-header">Plano SA ORAS NG PANGANGANAK AT KAGIPITAN</h1>

    <div class="prenatal-planning-body d-flex flex-column w-75 p-4 shadow card">
        <h4>Mahahalagang Impormasyon:</h4>
        <div class="mb-3 w-100">
            <div class="upper-box d-flex align-items-center gap-1">
                <label for="midwife" class="fs-5 fw-medium text-nowrap">Ako ay papaanakin ni:</label>
                <input type="text" class="flex-grow-1 form-control" name="midwife_name" placeholder="(pangalan ng doctor/nars/midwife, atbp.)">
            </div>
            <small class="text-danger error-text" id="midwife_name_error"></small>
        </div>
        <!-- plano ko manganak -->
        <div class="mb-3">
            <div class="upper-box d-flex align-items-center gap-1">
                <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Plano kong manganak sa:</label>
                <input type="text" class="flex-grow-1 form-control" name="place_of_pregnancy" placeholder="(pangalan ng hospital/lying-in center/ maternity clinic)">
            </div>
            <small class="text-danger error-text" id="place_of_pregnancy_error"></small>
        </div>
        <!-- authorized by philheath -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-lg-row">
                <label for="" class="fs-5 fw-medium text-nowrap">Ito ay pasilid na otorisado ng Philheath:</label>
                <div class="authorize-radio d-flex gap-3 align-items-center">
                    <label for="authorized_by_philhealth_yes" class="fs-5"> Yes:</label>
                    <input type="radio" name="authorized_by_philhealth" value="yes" id="authorized_by_philhealth_yes">
                    <label for="authorized_by_philhealth_no" class="fs-5">Hindi:</label>
                    <input type="radio" name="authorized_by_philhealth" class="mb-0" value="no" id="authorized_by_philhealth_no">
                </div>
                <small class="text-danger error-text" id="authorized_by_philhealth_error"></small>
            </div>
        </div>
        <!-- cost of pregnancy -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-lg-row">
                <label for="cost_of_pregnancy" class="fs-5 fw-medium w-100 text-wrap text-lg-nowrap">Ang tinatayang gagastusin ng panganganak sa pasilidad ay (P):</label>
                <input type="number" class="flex-grow-1 form-control" name="cost_of_pregnancy" id="cost_of_pregnancy">
            </div>
            <small class="text-danger error-text" id="cost_of_pregnancy_error"></small>
        </div>
        <!-- payment method -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-lg-row">
                <label for="pregnancy_payment_method" class="fs-5 fw-medium text-nowrap">Ang Paraan ng pagbabayad ay:</label>
                <select name="payment_method" id="pregnancy_payment_method" class="form-select flex-grow-1">
                    <option value="" disabled selected>Select Payment Method</option>
                    <option value="philhealth">PhilHealth</option>
                    <option value="cash">Cash / Out-of-Pocket</option>
                    <option value="private_insurance">Private Insurance</option>
                    <option value="hmo">HMO</option>
                    <option value="ngo">NGO / Charity Assistance</option>
                    <option value="gov_program">Government Health Program</option>
                    <option value="installment">Installment Plan</option>
                    <option value="employer">Employer / Company Benefit</option>
                </select>
            </div>
            <small class="text-danger error-text" id="payment_method_error"></small>
        </div>
        <!-- mode of transportation -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-lg-row">
                <label for="transportation_mode" class="fs-5 fw-medium text-nowrap">Paraan ng pagbiyahe patungo sa pasilidad ay:</label>
                <select name="transportation_mode" id="transportation_mode" class="form-select flex-grow-1" required>
                    <option value="" disabled selected>Select Mode of Transportation</option>
                    <option value="walking">Walking</option>
                    <option value="tricycle">Tricycle</option>
                    <option value="jeepney">Jeepney</option>
                    <option value="motorcycle">Motorcycle</option>
                    <option value="private_vehicle">Private Vehicle</option>
                    <option value="ambulance">Ambulance</option>
                    <option value="taxi">Taxi / Grab</option>
                    <option value="others">Others</option>
                </select>
            </div>
            <div class="low-box w-100 d-flex justify-content-center">
                <small>(mode of transportation)</small>
            </div>
            <small class="text-danger error-text" id="transportation_mode_error"></small>
        </div>
        <!-- person who will bring me to hospital -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-md-row">
                <label for="accompany_person_to_hospital" class="fs-5 fw-medium text-nowrap">Taong magdadala sakin sa hospital: </label>
                <input type="text" class="flex-grow-1 form-control" name="accompany_person_to_hospital" id="accompany_person_to_hospital" placeholder="Ilagay ang pangalan">
            </div>
            <small class="text-danger error-text" id="accompany_person_to_hospital_error"></small>
        </div>
        <!-- guardian -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-md-row">
                <label for="accompany_through_pregnancy" class="fs-5 fw-medium text-nowrap">Pangalan ng taong sasamahan ako sa panganganak: </label>
                <input type="text" class="flex-grow-1 form-control" name="accompany_through_pregnancy" id="accompany_through_pregnancy" placeholder="Ilagay ang pangalan">
            </div>
            <small class="text-danger error-text" id="accompany_through_pregnancy_error"></small>
        </div>
        <!-- mag-alalaga -->
        <div class="mb-3 w-100">
            <div class="upper-box d-flex gap-1 flex-column flex-md-row">
                <label for="care_person" class="fs-5 fw-medium text-nowrap">Pangalan ng taong mag-aalaga sa akin sa panganganak: </label>
                <input type="text" class="flex-grow-1 form-control" name="care_person" id="care_person" placeholder="Ilagay ang pangalan">
            </div>
            <small class="text-danger error-text" id="care_person_error"></small>
        </div>
        <!-- magbibigay ng dugo -->
        <div class="mb-3">
            <div class="upper-box d-flex gap-1 flex-column flex-md-row">
                <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Maaring magbigay ng dugo, kung sakaling mangailangan: </label>
                <div class="blood-donation d-flex w-100">
                    <input type="text" class="w-50 px-2 form-control flex-grow-1" name="" placeholder="Ilagay ang pangalan" id="donor_name_input">
                    <button type="button" class="btn btn-success" id="donor_name_add_btn">Add</button>
                </div>
                <!-- hidden input since madami to -->
            </div>
            <div class="lower-box p-3 bg-secondary w-100 justify-self-center blood-donor-name-container d-flex gap-3">
                <!-- <div class=" d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                    <h5 class="mb-0">Jan Loiue Salimbago</h5>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                        </svg>
                    </div>
                </div> -->
            </div>
        </div>
        <h5 class="mb-3">Kung magkaroon ng komplikasyon, kailangan sabihan kaagad si:</h5>
        <!-- persons info -->
        <div class="mb-3 w-100 flex-wrap">
            <div class="upper-box d-flex gap-1 flex-md-row flex-column">
                <label for="emergency_person_name" class="fs-5 text-nowrap">Pangalan: </label>
                <input type="text" class="flex-grow-1 form-control" name="emergency_person_name" id="emergency_person_name" placeholder="Ilagay ang pangalan">
            </div>
            <small class="text-danger error-text" id="emergency_person_name_error"></small>
        </div>
        <!-- house info -->
        <div class="mb-3 w-100 flex-wrap">
            <div class="upper-box d-flex gap-1 flex-md-row flex-column">
                <label for="emergency_person_residency" class="fs-5">Tirahan: </label>
                <input type="text" class="flex-grow-1 form-control" name="emergency_person_residency" id="emergency_person_residency" placeholder="address">
            </div>
            <small class="text-danger error-text" id="emergency_person_residency_error"></small>
        </div>
        <!-- contact -->
        <div class="mb-3 w-100 flex-wrap">
            <div class="upper-box d-flex gap-1 flex-md-row flex-column">
                <label for="emergency_person_contact_number" class="fs-5"> Telepono: </label>
                <input type="number" class="flex-grow-1 form-control" name="emergency_person_contact_number" id="emergency_person_contact_number" placeholder="0936627872">
            </div>
            <small class="text-danger error-text" id="emergency_person_contact_number_error"></small>
        </div>
        <!-- patient name -->
        <div class="mb-3 w-100 flex-wrap">
            <div class="upper-box d-flex gap-1 flex-md-row flex-column">
                <label for="name_of_patient" class="fs-5 text-nowrap">Pangalan ng pasyente: </label>
                <input type="text" class="flex-grow-1 form-control" name="name_of_patient" id="name_of_patient" disabled placeholder="Ilagay ang pangalan">
            </div>
        </div>
        <!-- signature -->
        <div class="mb-3 w-100 d-flex flex-column border-bottom">
            <label>Signature</label>

            <!-- Two Action Buttons -->
            <div class="d-flex gap-2 mb-2">
                <button type="button" class="btn btn-outline-primary flex-fill" id="drawSignatureBtn">
                    <i class="bi bi-pencil"></i> Draw Signature
                </button>
                <button type="button" class="btn btn-outline-primary flex-fill" id="uploadSignatureBtn">
                    <i class="bi bi-upload"></i> Upload Signature Photo
                </button>
            </div>

            <!-- Drawing Canvas (hidden by default) -->
            <div id="signatureCanvas" class="d-none mb-2">
                <canvas id="signaturePad" class="border w-100" style="height: 200px;"></canvas>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-sm btn-secondary" id="clearSignature">Clear</button>
                    <button type="button" class="btn btn-sm btn-success" id="saveSignature">Save Signature</button>
                </div>
            </div>

            <!-- File Upload (hidden by default) -->
            <div id="signatureUpload" class="d-none mb-2">
                <input type="file" name="signature_image" id="signature_image" class="form-control" accept="image/*">
                <small class="text-muted">Upload a clear photo or scanned image of the signature.</small>
            </div>

            <!-- Preview Area -->
            <div id="signaturePreview" class="d-none">
                <img id="previewImage" class="border" style="max-width: 300px; max-height: 150px;">
                <button type="button" class="btn btn-sm btn-danger mt-2" id="removeSignature">Remove</button>
            </div>

            <small class="text-danger error-text" id="signature_error"></small>
        </div>
        <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
            <button type="submit" class="btn btn-success px-5 py-2 fs-5" id="prenatal-save-btn">Save Record</button>
        </div>
    </div>
</div>