<div class="side-b-content">
    <div class="assessment border-bottom">
        <div class="input-field">
            <label for="edit_side_b_date_of_visit" class="w-100 fs-5">Date of Visit:</label>
            <input type="date" class="form-control w-100 py-2" name="edit_side_b_date_of_visit" id="edit_date_of_visit">
            <input type="hidden" name="edit_side_b_medical_record_case_id" id="edit_side_b_medical_record_case_id">
            <input type="hidden" name="edit_side_b_health_worker_id" id="edit_side_b_health_worker_id">
        </div>
        <div class="input-field">
            <label for="" class="w-100 fs-5">Medical Findings:</label>
            <label for="edit_side_b_medical_findings" class="w-100">(Medical observation/complaints, service rendered/procedures, laboratory examination, treatment and referrals)</label>
            <textarea name="edit_side_b_medical_findings" id="edit_medical_findings" class="bg-light border-1 w-100 h-[150px]"></textarea>
        </div>
        <div class="input-field">
            <label for="edit_side_b_method_accepted" class="fs-5">Method Accepted: </label>
            <input type="text" class="form-control w-100" name="edit_side_b_method_accepted" id="edit_method_accepted">
        </div>
        <div class="input-field">
            <label for="edit_side_b_name_n_signature" class="w-100 form-label fs-5">Name & Signature of service provider:</label>
            <!-- signature -->
            <div class="mb-1 w-100 d-flex flex-column border-bottom">
                <!-- Two Action Buttons -->
                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-outline-primary flex-fill" id="edit_side_b_drawSignatureBtn">
                        <i class="bi bi-pencil"></i> Draw Signature
                    </button>
                    <button type="button" class="btn btn-outline-primary flex-fill" id="edit_side_b_uploadSignatureBtn">
                        <i class="bi bi-upload"></i> Upload Signature Photo
                    </button>
                </div>

                <!-- Drawing Canvas (hidden by default) -->
                <div id="edit_side_b_signatureCanvas" class="d-none mb-2">
                    <canvas id="edit_side_b_signaturePad" class="border w-100" style="height: 200px;"></canvas>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-secondary" id="edit_side_b_clearSignature">Clear</button>
                        <button type="button" class="btn btn-sm btn-success" id="edit_side_b_saveSignature">Save Signature</button>
                    </div>
                </div>

                <!-- File Upload (hidden by default) -->
                <div id="edit_side_b_signatureUpload" class="d-none mb-2">
                    <input type="file" name="edit_side_b_signature_image" id="edit_side_b_signature_image" class="form-control" accept="image/*">
                    <small class="text-muted">Upload a clear photo or scanned image of the signature.</small>
                </div>

                <!-- Preview Area -->
                <div id="edit_side_b_signaturePreview" class="d-none">
                    <img id="edit_side_b_previewImage" class="border" style="max-width: 300px; max-height: 150px;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" id="edit_side_b_removeSignature">Remove</button>
                </div>

                <small class="text-danger error-text" id="edit_side_b_signature_error"></small>
            </div>
        </div>
        <!-- date of follow up visit -->
        <div class="input-field mb-3">
            <label for="edit_side_b_date_of_follow_up_visit" class="w-100 fs-5">Date of Follow-Up Visit*</label>
            <input type="date" class="form-control w-100 py-2" name="edit_side_b_date_of_follow_up_visit" id="edit_date_of_follow_up_visit" max="{{date('Y-m-d',strtotime('+5 years'))}}">
        </div>
    </div>
    <!-- follow up questions -->
    <div class="side-follow-up-questions">
        <h5>How to Reasonable sure a Client is Not Pregnant</h5>
        <!-- q1 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3">
                <p class="mb-0 fs-5 fw-light">1. Did you have a baby less than six (6) months ago, are you fully or nearly-fully breastfeeding, and have you had no menstrual period since then?</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-center gap-2">
                <input type="radio" name="edit_baby_Less_than_six_months_question" id="edit_baby_Less_than_six_months_question_yes" value="Yes">
                <label for="edit_baby_Less_than_six_months_question_yes" class="fs-5">Yes</label>
                <input type="radio" name="edit_baby_Less_than_six_months_question" id="edit_baby_Less_than_six_months_question_no" value="No">
                <label for="edit_baby_Less_than_six_months_question_no" class="fs-5">No</label>
            </div>
        </div>
        <!-- q2 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3">

                <p class="mb-0 fs-5 fw-light">2. Have you abstained from sexual intercourse since your last menstrual period or delivery?</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-center gap-2">
                <input type="radio" name="edit_sexual_intercouse_or_mesntrual_period_question" id="edit_sexual_intercouse_or_mesntrual_period_question_yes" value="Yes">
                <label for="edit_sexual_intercouse_or_mesntrual_period_question_yes" class="fs-5">Yes</label>
                <input type="radio" name="edit_sexual_intercouse_or_mesntrual_period_question" id="edit_sexual_intercouse_or_mesntrual_period_question_no" value="No">
                <label for="edit_sexual_intercouse_or_mesntrual_period_question_no" class="fs-5">No</label>
            </div>
        </div>
        <!-- q3 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3">

                <p class="mb-0 fs-5 fw-light">3. Have you had a baby in the last four (4) weeks</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-center gap-2">
                <input type="radio" name="edit_baby_last_4_weeks_question" id="edit_baby_last_4_weeks_question_yes" value="Yes">
                <label for="edit_baby_last_4_weeks_question_yes" class="fs-5">Yes</label>
                <input type="radio" name="edit_baby_last_4_weeks_question" id="edit_baby_last_4_weeks_question_no" value="No">
                <label for="edit_baby_last_4_weeks_question_no" class="fs-5">No</label>
            </div>
        </div>
        <!-- q4 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3">

                <p class="mb-0 fs-5 fw-light">4. Did your last menstrual period start within the past seven (7) days</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-center gap-2">
                <input type="radio" name="edit_menstrual_period_in_seven_days_question" id="edit_menstrual_period_in_seven_days_question_yes" value="Yes">
                <label for="edit_menstrual_period_in_seven_days_question_yes" class="fs-5">Yes</label>
                <input type="radio" name="edit_menstrual_period_in_seven_days_question" id="edit_menstrual_period_in_seven_days_question_no" value="No">
                <label for="edit_menstrual_period_in_seven_days_question_no" class="fs-5">No</label>
            </div>
        </div>
        <!-- q5 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3">

                <p class="mb-0 fs-5 fw-light">5. Have you had a miscarriage or abortion in the last seven (7) days? </p>
            </div>
            <div class="answers d-flex align-items-center justify-content-center gap-2">
                <input type="radio" name="edit_miscarriage_or_abortion_question" id="edit_miscarriage_or_abortion_question_yes" value="Yes">
                <label for="edit_miscarriage_or_abortion_question_yes" class="fs-5">Yes</label>
                <input type="radio" name="edit_miscarriage_or_abortion_question" id="edit_miscarriage_or_abortion_question_no" value="No">
                <label for="edit_miscarriage_or_abortion_question_no" class="fs-5">No</label>
            </div>
        </div>
        <!-- q6 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3">

                <p class="mb-0 fs-5 fw-light">6. Have you been using reliable contraceptive method consistenly and correctly?</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-center gap-2">
                <input type="radio" name="edit_contraceptive_question" id="edit_contraceptive_question_yes" value="Yes">
                <label for="edit_contraceptive_question_yes" class="fs-5">Yes</label>
                <input type="radio" name="edit_contraceptive_question" id="edit_contraceptive_question_no" value="No">
                <label for="edit_contraceptive_question_no" class="fs-5">No</label>
            </div>
        </div>
        <div class="notices">
            <p>- If the client answered YES to at least one of the questions and she is free of signs or sign or symptoma of pregnancy, provide client with desired method</p>
            <p>- If the client answered NO to all the question, pregnancy cannot be ruled out. the client should await menses or use a pregnancy test</p>
        </div>

    </div>
</div>