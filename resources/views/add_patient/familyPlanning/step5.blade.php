<div class="sexually-transmitted w-full lg:w-[75%] card shadow p-3 align-self-center d-flex overflow-x-auto">
    <h4 class="border-bottom px-1">III. RISK FOR SEXUALLY TRANSMITTED INFECTIONS</h4>
    <div class="sexually-transmitted-content">
        <h6> Does the client or the client's partner have any of the following?</h6>
        <div class="list-of-questions">
            <div class="mb-2 d-flex align-items-center justify-content-between">
                <div class="question d-flex align-items-center gap-3 w-[70%]">
                    <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                    <p class="mb-0 fs-5 fw-light">Abnormal discharge from the genital area</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                    <input type="radio" name="infection_abnormal_discharge_from_genital_area" id="step5_q1_yes" value="Yes">
                    <label for="step5_q1_yes" class="fs-5">Yes</label>
                    <input type="radio" name="infection_abnormal_discharge_from_genital_area" id="step5_q1_no" value="No">
                    <label for="step5_q1_no" class="fs-5">No</label>
                </div>
                <small class="text-danger error-text" id="infection_abnormal_discharge_from_genital_area_error"></small>
            </div>
            <!-- follow up -->
            <div class="mb-2 d-flex align-items-md-center align-items-start justify-content-between w-100 flex-wrap flex-sm-row flex-column" style="padding-left: 3.125rem;">
                <div class="question d-flex align-items-start gap-3 w-[70%]">
                    <!-- <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div> -->
                    <p class="mb-0 fs-5 fw-light">If "Yes" indicate it from:</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-end justify-content-lg-start  gap-2 w-[30%] align-self-end">
                    <input type="radio" name="origin_of_abnormal_discharge" id="origin_of_abnormal_discharge_vagina" value="Vagina">
                    <label for="origin_of_abnormal_discharge_vagina" class="fs-5">Vagina</label>
                    <input type="radio" name="origin_of_abnormal_discharge" id="origin_of_abnormal_discharge_penis" value="Penis">
                    <label for="origin_of_abnormal_discharge_penis" class="fs-5">Penis</label>
                </div>
                <small class="text-danger error-text" id="origin_of_abnormal_discharge_error"></small>
            </div>
            <!-- next -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
                <div class="question d-flex align-items-center gap-3 w-[70%]">
                    <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                    <p class="mb-0 fs-5 fw-light">Scores or ulcers in the genital area</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                    <input type="radio" name="scores_or_ulcer" id="step5_q2_yes" value="Yes">
                    <label for="step5_q2_yes" class="fs-5">Yes</label>
                    <input type="radio" name="scores_or_ulcer" id="step5_q2_no" value="No">
                    <label for="step5_q2_no" class="fs-5">No</label>
                </div>
                <small class="text-danger error-text" id="scores_or_ulcer_error"></small>
            </div>
            <!-- q3 -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
                <div class="question d-flex align-items-center gap-3 w-[70%]">
                    <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                    <p class="mb-0 fs-5 fw-light">pain or burning sensation in the genital area</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                    <input type="radio" name="pain_or_burning_sensation" id="step5_q3_yes" value="Yes">
                    <label for="step5_q3_yes" class="fs-5">Yes</label>
                    <input type="radio" name="pain_or_burning_sensation" id="step5_q3_no" value="No">
                    <label for="step5_q3_no" class="fs-5">No</label>
                </div>
                <small class="text-danger error-text" id="pain_or_burning_sensation_error"></small>
            </div>
            <!-- q4 -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
                <div class="question d-flex align-items-center gap-3 w-[70%]">
                    <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                    <p class="mb-0 fs-5 fw-light">History of treatment for sexually transmitted infection</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                    <input type="radio" name="history_of_sexually_transmitted_infection" id="step5_q4_yes" value="Yes">
                    <label for="step5_q4_yes" class="fs-5">Yes</label>
                    <input type="radio" name="history_of_sexually_transmitted_infection" id="step5_q4_no" value="No">
                    <label for="step5_q4_no" class="fs-5">No</label>
                </div>
                <small class="text-danger error-text" id="history_of_sexually_transmitted_infection_error"></small>
            </div>
            <!-- q5 -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
                <div class="question d-flex align-items-center gap-3 w-[70%]">
                    <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                    <p class="mb-0 fs-5 fw-light">HIV/AIDS/Pelvic inflamatory disease</p>
                </div>
                <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                    <input type="radio" name="sexually_transmitted_disease" id="step5_q5_yes" value="Yes">
                    <label for="step5_q5_yes" class="fs-5">Yes</label>
                    <input type="radio" name="sexually_transmitted_disease" id="step5_q5_no" value="No">
                    <label for="step5_q5_no" class="fs-5">No</label>
                </div>
                <small class="text-danger error-text" id="sexually_transmitted_disease_error"></small>
            </div>
        </div>
    </div>
    <h4 class="border-bottom px-1">IV. RISKS FOR VIOLENCE AGAINTS WOMEN (VAW)</h4>
    <div class="sexually-transmitted-content border-bottom">
        <!-- q6 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3 w-[70%]">
                <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                <p class="mb-0 fs-5 fw-light">History of domestic violence of VAW</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                <input type="radio" name="history_of_domestic_violence_of_VAW" id="step5_q6_yes" value="Yes">
                <label for="step5_q6_yes" class="fs-5">Yes</label>
                <input type="radio" name="history_of_domestic_violence_of_VAW" id="step5_q6_no" value="No">
                <label for="step5_q6_no" class="fs-5">No</label>
            </div>
            <small class="text-danger error-text" id="history_of_domestic_violence_of_VAW_error"></small>
        </div>
        <!-- q7 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3 w-[70%]">
                <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                <p class="mb-0 fs-5 fw-light">Unpleasant relationship with partner</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                <input type="radio" name="unpleasant_relationship_with_partner" id="step5_q7_yes" value="Yes">
                <label for="step5_q7_yes" class="fs-5">Yes</label>
                <input type="radio" name="unpleasant_relationship_with_partner" id="step5_q7_no" value="No">
                <label for="step5_q7_no" class="fs-5">No</label>
            </div>
            <small class="text-danger error-text" id="unpleasant_relationship_with_partner_error"></small>
        </div>
        <!-- q8 -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div class="question d-flex align-items-center gap-3 w-[70%]">
                <div class="small-box mb-0" style="height: 5px; width:5px; background-color:black"></div>
                <p class="mb-0 fs-5 fw-light">Partner does not approve of the visit to FP clinic</p>
            </div>
            <div class="answers d-flex align-items-center justify-content-start gap-2 w-[30%]">
                <input type="radio" name="partner_does_not_approve" id="step5_q8_yes" value="Yes">
                <label for="step5_q8_yes" class="fs-5">Yes</label>
                <input type="radio" name="partner_does_not_approve" id="step5_q8_no" value="No">
                <label for="step5_q8_no" class="fs-5">No</label>
            </div>
            <small class="text-danger error-text" id="partner_does_not_approve_error"></small>
        </div>
        <div class="reffered-to d-flex flex-column">
            <h5>Reffered to:</h5>
            <div class="list-of-org">
                <div class="type-of-user-inputs px-5">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="referred_to" id="DSWD" value="DSWD">
                        <label for="DSWD">DSWD</label>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="referred_to" id="WCPU" value="WCPU">
                        <label for="WCPU">WCPU</label>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="referred_to" id="NGOs" value="NGOs">
                        <label for="NGOs">NGOs</label>
                    </div>
                    <!-- new clinic -->
                    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                        <input type="radio" name="referred_to" id="others_organization" value="others">
                        <label for="others_organization">Others(specify):</label>
                        <input type="text" class="form-control w-100 md:w-[50%]" name="reffered_to_others">
                        <small class="text-danger error-text" id="reffered_to_others_error"></small>
                    </div>
                </div>
            </div>
            <small class="text-danger error-text" id="referred_to_error"></small>
        </div>
    </div>
    <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-2">
        <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
        <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
    </div>
</div>