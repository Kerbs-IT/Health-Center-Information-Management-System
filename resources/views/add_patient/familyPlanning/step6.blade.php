<div class="physical-examination w-75 card shadow p-3 align-self-center d-flex">
    <h4 class="border-bottom px-1">V. PHYSICAL EXAMINATION</h4>
    <div class="vital-sign">
        <h5>Vital Sign</h5>
        <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
            <div class="mb-2 w-50">
                <label for="BP">Blood Pressure:</label>
                <input type="text" class="form-control w-100" placeholder="ex. 120/80" disabled>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Temperature:</label>
                <input type="number" class="form-control w-100" placeholder="00 C" disabled>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Pulse Rate(Bpm):</label>
                <input type="text" class="form-control w-100" placeholder=" 60-100" disabled>
            </div>

        </div>
        <!-- 2nd row -->
        <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
            <div class="mb-2 w-50">
                <label for="BP">Respiratory Rate (breaths/min):</label>
                <input type="text" class="form-control w-100" placeholder="ex. 25" disabled>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Height(cm):</label>
                <input type="number" class="form-control w-100" placeholder="00.00" name="height" disabled>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Weight(kg):</label>
                <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight" disabled>
            </div>
        </div>
    </div>
    <div class="tests d-flex border-bottom">
        <div class="row-1 w-100">
            <div class="box d-flex w-100 ">
                <div class="skin w-50">
                    <h5>SKIN:</h5>
                    <div class="list-of-skin px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_skin_type" id="skin_type_normal" value="Normal">
                                <label for="skin_type_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_skin_type" id="skin_type_pale" value="Pale">
                                <label for="skin_type_pale">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_skin_type" id="skin_type_yellowish" value="Yellowish">
                                <label for="skin_type_yellowish">Yellowish</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_skin_type" id="skin_type_hematoma" value="Hematoma">
                                <label for="skin_type_hematoma">Hematoma</label>
                            </div>
                        </div>
                        <small class="text-danger error-text" id="physical_examination_skin_type_error"></small>
                    </div>
                </div>
                <div class="CONJUCTIVA w-50">
                    <h5>CONJUCTIVA:</h5>
                    <div class="list-of-conjuctiva px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_conjuctiva_type" id="conjuctiva_normal" value="Normal">
                                <label for="conjuctiva_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_conjuctiva_type" id="conjuctiva_pale" value="Pale">
                                <label for="conjuctiva_pale">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_conjuctiva_type" id="conjuctiva_yellowish" value="Yellowish">
                                <label for="conjuctiva_yellowish">Yellowish</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="physical_examination_conjuctiva_type_error"></small>
                </div>
            </div>
            <!-- neck -->
            <div class="box d-flex w-100">
                <div class="neck w-50">
                    <h5>NECK:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_neck_type" id="neck_normal" value="Normal">
                                <label for="neck_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_neck_type" id="neck_pale" value="Pale">
                                <label for="neck_pale">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_neck_type" id="neck_enlarged_lymph_nodes" value="Enlarged lymph nodes">
                                <label for="neck_enlarged_lymph_node">Enlarged lymph nodes</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="physical_examination_neck_type_error"></small>
                </div>
                <!-- Breast -->
                <div class="Breast">
                    <h5>Breast:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_breast_type" id="breast_normal" value="Normal">
                                <label for="breast_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_breast_type" id="breast_mass" value="Mass">
                                <label for="breast_mass"> Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_breast_type" id="breast_nipple_discharge" value="Nipple Discharge">
                                <label for="breast_nipple_discharge">Nipple Discharge</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="physical_examination_breast_type_error"></small>
                </div>
            </div>
            <div class="box d-flex">
                <!-- abdomen -->
                <div class="Breast w-50">
                    <h5>ABDOMEN:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_abdomen_type" id="addomen_normal" value="Normal">
                                <label for="addomen_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_abdomen_type" id="abdominal_mass" value="Abdominal Mass">
                                <label for="abdominal_mass">Abdominal Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_abdomen_type" id="abdominal_varicosities" value="Varicosities">
                                <label for="abdominal_varicosities">Varicosities</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="physical_examination_abdomen_type_error"></small>
                </div>
                <!-- extremites-->
                <div class="extremites">
                    <h5>EXTREMITES:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_extremites_type" id="extremites_type_normal" value="Normal">
                                <label for="extremites_type_normal">normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_extremites_type" id="extremites_type_edema" value="Edema">
                                <label for="extremites_type_edema">Edema</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="physical_examination_extremites_type" id="extremites_type_varicosities" value="varicosities">
                                <label for="extremites_type_varicosities">varicosities</label>
                            </div>

                        </div>
                    </div>
                    <small class="text-danger error-text" id="physical_examination_extremites_type_error"></small>
                </div>
            </div>
        </div>
        <div class="row-2 w-50 d-flex justify-content-between ">
            <!-- extremites-UID-->
            <div class="extremites-UID">
                <h5 class="mb-0">EXTREMITES:</h5>
                <small>(For IUD Acceptors)</small>
                <div class="list-of-neck px-2">
                    <div class="type-of-user-inputs ">
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="extremites_UID_type_normal" value="Normal">
                            <label for="extremites_UID_type_normal">Normal</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="extremites_UID_type_mass" value="Mass">
                            <label for="extremites_UID_type_mass">Mass</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="extremites_UID_type_abdominal_discharge" value="abdominal discharge">
                            <label for="extremites_UID_type_abdominal_discharge">Abdominal Discharge</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="extremites_UID_type_cervical_abnormalities" value="cervial abnormalities">
                            <label for="extremites_UID_type_cervical_abnormalities">Cervial Abnormalities</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="cervical_abnormalities_type" id="cervical_abnormalities_type_warts" value="warts">
                                <label for="cervical_abnormalities_type_warts" class="cervical_abnormalities_type">Warts</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="cervical_abnormalities_type" id="cervical_abnormalities_type_Polyp" value="Polyp or cyst">
                                <label for="cervical_abnormalities_type_Polyp" class="cervical_abnormalities_type">Polyp or cyst</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="cervical_abnormalities_type" id="cervical_abnormalities_type_inflamation_or_erosion" value="inflammation or erosion">
                                <label for="cervical_abnormalities_type_inflamation_or_erosion" class="cervical_abnormalities_type">inflammation or erosion</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="cervical_abnormalities_type" id="cervical_abnormalities_type_bloody_discharge" value="Bloody discharge">
                                <label for="cervical_abnormalities_type_bloody_discharge" class="cervical_abnormalities_type">Bloody discharge</label>
                            </div>
                            <small class="text-danger error-text" id="cervical_abnormalities_type_error"></small>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="extremites_UID_type_cervical_consistency" value="cervical consistency">
                            <label for="">cervical consistency</label>
                        </div>
                        <!-- firm or soft -->
                        <div class="inner-type-abnormalities d-flex gap-3" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="cervical_consistency_type" id="cervical_consistency_type_firm" value="firm">
                                <label for="cervical_consistency_type_firm" class="cervical_consistency_type_label">Firm</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="cervical_consistency_type" id="cervical_consistency_type_soft" value="soft">
                                <label for="cervical_consistency_type_soft" class="cervical_consistency_type_label">Soft</label>
                            </div>
                            <!-- ERROR HANDLING -->
                            <small class="text-danger error-text" id="cervical_consistency_type_error"></small>
                        </div>
                        <!-- tenderness -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="new-acceptor" value="cervical tenderness">
                            <label for="new-acceptor">cervical tenderness</label>
                        </div>
                        <!-- adnexal mass -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="new-acceptor" value="adnexal mass / tenderness">
                            <label for="new-acceptor">adnexal mass / tenderness</label>
                        </div>
                        <!-- uterine position -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="physical_examination_extremites_UID_type_uterine" value="uterine position">
                            <label for="physical_examination_extremites_UID_type_uterine">Uterine position</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="uterine_position_type" id="uterine_position_type_mid" value="Mid">
                                <label for="uterine_position_type_mid" class="uterine_position_type_label">Mid</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="uterine_position_type" id="uterine_position_type_anteflexed" value="Anteflexed">
                                <label for="uterine_position_type_anteflexed" class="uterine_position_type_label">Anteflexed</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="uterine_position_type" id="uterine_position_typer_retroflexed" value="Retroflexed">
                                <label for="uterine_position_typer_retroflexed" class="uterine_position_type_label">Retroflexed</label>
                            </div>
                            <!-- error handling -->
                            <small class="text-danger error-text" id="uterine_position_type_error"></small>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="current-method" value="uterine depth">
                            <label for="current-method">Uterine depth</label>
                            <input type="text" class="w-25" name="uterine_depth_text">
                            <small>cm</small>

                        </div>
                        <small class="text-danger error-text" id="uterine_depth_text_error"></small>
                    </div>
                </div>
                <small class="text-danger error-text" id="physical_examination_extremites_UID_type_error"></small>
            </div>

            <div class="cervical-consistency">

            </div>
        </div>
    </div>
    <h4 class="border-bottom px-1"> ACKNOWLEDGEMENT</h4>
    <div class="mb-3">This is to certify that the Physician/Nurse/Midwife of the clinic has fully
        explained to me the different methods available in the family planning
        and i freely choose the <input type="text border-bottom" class="rounded" name="choosen_method"> method.
    </div>
    <small class="text-danger error-text" id="choosen_method_error"></small>
    <div class="signature d-flex justify-content-even w-100 gap-2">
        <div class="mb-3 w-50 d-flex flex-column">
            <label for="signature_image">Upload Signature</label>
            <input type="file" name="family_planning_signature_image" id="signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
            <small class="text-danger error-text" id="family_planning_signature_image_error"></small>
        </div>
        <div class="mb-3 w-50">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center" name="family_planning_date_of_acknowledgement">
            <small class="text-danger error-text" id="family_planning_date_of_acknowledgement_error"></small>
        </div>
    </div>

    <div class="mb-3 w-100">
        <p class="text-center">I hereby consent to the inclusion of my FP 1 in the Family Health Registry</p>
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-2 border-bottom">
        <div class="mb-3 w-50 d-flex flex-column">
            <label for="family_planning_acknowlegement_consent_signature_image">Upload Signature</label>
            <input type="file" name="family_planning_acknowlegement_consent_signature_image" id="family_planning_acknowlegement_consent_signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
            <!-- ERROR HANDLING -->
            <small class="text-danger error-text" id="family_planning_acknowlegement_consent_signature_image_error"></small>
        </div>
        <div class="mb-3 w-50">
            <label for="family_planning_date_of_acknowledgement_consent" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center" name="family_planning_date_of_acknowledgement_consent" id="family_planning_date_of_acknowledgement_consent">
            <small class="text-danger error-text" id="family_planning_date_of_acknowledgement_consent_error"></small>
        </div>
    </div>
    <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto">
        <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
        <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
    </div>
</div>