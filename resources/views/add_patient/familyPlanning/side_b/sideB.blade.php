<div class="add-patient-side-b d-flex flex-column align-items-center ">
    <div class="side-b-content w-full xl:w-[75%] shadow-lg p-3 m-md-0 m-5 rounded-2">
        <h4>Family Planning Client Assessment Record - Side B</h4>
        <div class="assessment border-bottom">
            <div class="input-field">
                <label for="side_b_date_of_visit" class="w-100 fs-5">Date of Visit:</label>
                <input type="date" class="form-control w-100 py-2" name="side_b_date_of_visit" id="side_b_date_of_visit">
                <small class="text-danger" id="side_b_date_of_visit_error"></small>
                <input type="hidden" name="side_b_medical_record_case_id" id="side_b_medical_record_case_id">
                <input type="hidden" name="side_b_health_worker_id" id="side_b_health_worker_id">
            </div>
            <div class="input-field">
                <label for="side_b_medical_findings" class="w-100 fs-5">Medical Findings:</label>
                <label for="side_b_medical_findings" class="w-100">(Medical observation/complaints, service rendered/procedures, laboratory examination, treatment and referrals)</label>
                <textarea name="side_b_medical_findings" id="side_b_medical_findings" class="bg-light border-1 w-100 h-[150px]"></textarea>
                <small class="text-danger" id="side_b_medical_findings_error"></small>
            </div>
            <div class="input-field">
                <label for="side_b_method_accepted" class="fs-5">Method Accepted: </label>
                <input type="text" class="form-control w-100" name="side_b_method_accepted" id="side_b_method_accepted">
                <small class="text-danger" id="side_b_method_accepted_error"></small>
            </div>
            <div class="input-field">
                <label for="side_b_name_n_signature" class="w-100 form-label fs-5">Name & Signature of service provider:</label>
                <!-- signature -->
                <div class="mb-1 w-100 d-flex flex-column border-bottom">
                    <label>Signature</label>

                    <!-- Two Action Buttons -->
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-outline-primary flex-fill" id="add_side_b_name_n_drawSignatureBtn">
                            <i class="bi bi-pencil"></i> Draw Signature
                        </button>
                        <button type="button" class="btn btn-outline-primary flex-fill" id="add_side_b_name_n_uploadSignatureBtn">
                            <i class="bi bi-upload"></i> Upload Signature Photo
                        </button>
                    </div>

                    <!-- Drawing Canvas (hidden by default) -->
                    <div id="add_side_b_name_n_signatureCanvas" class="d-none mb-2">
                        <canvas id="add_side_b_name_n_signaturePad" class="border w-100" style="height: 200px;"></canvas>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-secondary" id="add_side_b_name_n_clearSignature">Clear</button>
                            <button type="button" class="btn btn-sm btn-success" id="add_side_b_name_n_saveSignature">Save Signature</button>
                        </div>
                    </div>

                    <!-- File Upload (hidden by default) -->
                    <div id="add_side_b_name_n_signatureUpload" class="d-none mb-2">
                        <input type="file" name="add_side_b_name_n_signature_image" id="add_side_b_name_n_signature_image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload a clear photo or scanned image of the signature.</small>
                    </div>

                    <!-- Preview Area -->
                    <div id="add_side_b_name_n_signaturePreview" class="d-none">
                        <img id="add_side_b_name_n_previewImage" class="border" style="max-width: 300px; max-height: 150px;">
                        <button type="button" class="btn btn-sm btn-danger mt-2" id="add_side_b_name_n_removeSignature">Remove</button>
                    </div>

                    <small class="text-danger error-text" id="add_side_b_name_n_signature_error"></small>
                </div>
            </div>
            <!-- date of follow up visit -->
            <div class="input-field mb-3">
                <label for="side_b_date_of_follow_up_visit" class="w-100 fs-5">Date of Follow-Up Visit*</label>
                <input type="date" class="form-control w-100 py-2" name="side_b_date_of_follow_up_visit" id="side_b_date_of_follow_up_visit"  max="{{date('Y-m-d',strtotime('+5 years'))}}">
                <small class="text-danger" id="side_b_date_of_follow_up_visit_error"></small>
            </div>
        </div>
        <!-- follow up questions -->
        <div class="side-follow-up-questions">
            <h5>How to Reasonable sure a Client is Not Pregnant</h5>
            <!-- q1 -->
            <div class="mb-2 d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="question d-flex align-items-center gap-3 w-[80%]">

                    <p class="mb-0 fs-5 fw-light">1. Did you have a baby less than six (6) months ago, are you fully or nearly-fully breastfeeding, and have you had no menstrual period since then?</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-center gap-2 w-[20%]">
                    <input type="radio" name="baby_Less_than_six_months_question" id="baby_Less_than_six_months_question_yes" value="Yes">
                    <label for="baby_Less_than_six_months_question_yes" class="fs-5">Yes</label>
                    <input type="radio" name="baby_Less_than_six_months_question" id="baby_Less_than_six_months_question_no" value="No">
                    <label for="baby_Less_than_six_months_question_no" class="fs-5">No</label>
                </div>
                <small class="text-danger" id="baby_Less_than_six_months_question_error"></small>
            </div>
            <!-- q2 -->
            <div class="mb-2 d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="question d-flex align-items-center gap-3 w-[80%]">

                    <p class="mb-0 fs-5 fw-light">2. Have you abstained from sexual intercourse since your last menstrual period or delivery?</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-center gap-2 w-[20%]">
                    <input type="radio" name="sexual_intercouse_or_mesntrual_period_question" id="sexual_intercouse_or_mesntrual_period_question_yes" value="Yes">
                    <label for="sexual_intercouse_or_mesntrual_period_question_yes" class="fs-5">Yes</label>
                    <input type="radio" name="sexual_intercouse_or_mesntrual_period_question" id="sexual_intercouse_or_mesntrual_period_question_no" value="No">
                    <label for="sexual_intercouse_or_mesntrual_period_question_no" class="fs-5">No</label>
                </div>
                <small class="text-danger" id="sexual_intercouse_or_mesntrual_period_question_error"></small>
            </div>
            <!-- q3 -->
            <div class="mb-2 d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="question d-flex align-items-center gap-3 w-[80%]">

                    <p class="mb-0 fs-5 fw-light">3. Have you had a baby in the last four (4) weeks</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-center gap-2 w-[20%]">
                    <input type="radio" name="baby_last_4_weeks_question" id="baby_last_4_weeks_question_yes" value="Yes">
                    <label for="baby_last_4_weeks_question_yes" class="fs-5">Yes</label>
                    <input type="radio" name="baby_last_4_weeks_question" id="baby_last_4_weeks_question_no" value="No">
                    <label for="baby_last_4_weeks_question_no" class="fs-5">No</label>
                </div>
                <small class="text-danger" id="baby_last_4_weeks_question_error"></small>
            </div>
            <!-- q4 -->
            <div class="mb-2 d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="question d-flex align-items-center gap-3 w-[80%]">

                    <p class="mb-0 fs-5 fw-light">4. Did your last menstrual period start within the past seven (7) days</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-center gap-2 w-[20%]">
                    <input type="radio" name="menstrual_period_in_seven_days_question" id="menstrual_period_in_seven_days_question_yes" value="Yes">
                    <label for="menstrual_period_in_seven_days_question_yes" class="fs-5">Yes</label>
                    <input type="radio" name="menstrual_period_in_seven_days_question" id="menstrual_period_in_seven_days_question_no" value="No">
                    <label for="menstrual_period_in_seven_days_question_no" class="fs-5">No</label>
                </div>
                <small class="text-danger" id="menstrual_period_in_seven_days_question_error"></small>
            </div>
            <!-- q5 -->
            <div class="mb-2 d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="question d-flex align-items-center gap-3 w-[80%]">

                    <p class="mb-0 fs-5 fw-light">5. Have you had a miscarriage or abortion in the last seven (7) days? </p>
                </div>
                <div class="answers d-flex align-items-center justify-content-center gap-2 w-[20%]">
                    <input type="radio" name="miscarriage_or_abortion_question" id="miscarriage_or_abortion_question_yes" value="Yes">
                    <label for="miscarriage_or_abortion_question_yes" class="fs-5">Yes</label>
                    <input type="radio" name="miscarriage_or_abortion_question" id="miscarriage_or_abortion_question_no" value="No">
                    <label for="miscarriage_or_abortion_question_no" class="fs-5">No</label>
                </div>
                <small class="text-danger" id="miscarriage_or_abortion_question_error"></small>
            </div>
            <!-- q6 -->
            <div class="mb-2 d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="question d-flex align-items-center gap-3 w-[80%]">

                    <p class="mb-0 fs-5 fw-light">6. Have you been using reliable contraceptive method consistenly and correctly?</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-center gap-2 w-[20%]">
                    <input type="radio" name="contraceptive_question" id="contraceptive_question_yes" value="Yes">
                    <label for="contraceptive_question_yes" class="fs-5">Yes</label>
                    <input type="radio" name="contraceptive_question" id="contraceptive_question_no" value="No">
                    <label for="contraceptive_question_no" class="fs-5">No</label>
                </div>
                <small class="text-danger" id="contraceptive_question_error"></small>
            </div>
            <div class="notices">
                <p>- If the client answered YES to at least one of the questions and she is free of signs or sign or symptoma of pregnancy, provide client with desired method</p>
                <p>- If the client answered NO to all the question, pregnancy cannot be ruled out. the client should await menses or use a pregnancy test</p>
            </div>

        </div>
        <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
            <button type="submit" class="btn btn-success px-5 py-2 fs-5" id="family_planning_submit_btn">Submit</button>
        </div>
    </div>

</div>