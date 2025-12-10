<div class="physical-examination w-100 ">
    <h4 class="border-bottom px-1">V. PHYSICAL EXAMINATION</h4>
    <div class="vital-sign">
        <h5>Vital Sign</h5>
        <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
            <div class="mb-2 w-50">
                <label for="BP">Blood Pressure:</label>
                <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="edit_blood_pressure" id="edit_blood_pressure">
                <small class="text-danger error-text" id="edit_blood_pressure_error"></small>
            </div>

            <div class="mb-2 w-50">
                <label for="BP">Pulse Rate(Bpm):</label>
                <input type="text" class="form-control w-100" placeholder=" 60-100" name="edit_pulse_rate" id="edit_pulse_rate">
                <small class="text-danger error-text" id="edit_pulse_rate_error"></small>
            </div>
        </div>
        <!-- 2nd row -->
        <div class="mb-2 input-field d-flex gap-3 w-100 second-row">

            <div class="mb-2 w-50">
                <label for="BP">Height(cm):</label>
                <input type="number" class="form-control w-100" placeholder="00.00" name="edit_height" id="edit_height">
                <small class="text-danger error-text" id="edit_height_error"></small>
            </div>
            <div class="mb-2 w-50">
                <label for="BP">Weight(kg):</label>
                <input type="number" class="form-control w-100" placeholder=" 00.00" name="edit_weight" id="edit_weight">
                <small class="text-danger error-text" id="edit_weight_error"></small>
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
                                <input type="radio" name="edit_skin_type" id="edit_skin_normal" value="Normal">
                                <label for="edit_skin_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_skin_type" id="edit_skin_pale" value="Pale">
                                <label for="edit_skin_pale">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_skin_type" id="edit_skin_yellowish" value="Yellowish">
                                <label for="edit_skin_yellowish">Yellowish</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_skin_type" id="edit_skin_hematoma" value="Hematoma">
                                <label for="edit_skin_hematoma">Hematoma</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_skin_type_error"></small>
                </div>
                <div class="CONJUCTIVA w-50">
                    <h5>CONJUCTIVA:</h5>
                    <div class="list-of-conjuctiva px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_conjuctiva_type" id="edit_conjuctiva_normal" value="Normal">
                                <label for="edit_conjuctiva_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_conjuctiva_type" id="edit_conjuctiva_pale" value="Pale">
                                <label for="edit_conjuctiva_pale">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_conjuctiva_type" id="edit_conjuctiva_yellowish" value="Yellowish">
                                <label for="edit_conjuctiva_yellowish">Yellowish</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_conjuctiva_type_error"></small>
                </div>
            </div>
            <!-- neck -->
            <div class="box d-flex w-100">
                <div class="neck w-50">
                    <h5>NECK:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_neck_type" id="edit_neck_type_normal" value="Normal">
                                <label for="edit_neck_type_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_neck_type" id="edit_neck_type_neck_mass" value="Neck Mass">
                                <label for="edit_neck_type_neck_mass">Neck Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_neck_type" id="edit_nect_type_enlarge" value="enlarged lymph nodes">
                                <label for="edit_nect_type_enlarge">enlarged lymph nodes</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_neck_type_error"></small>
                </div>
                <!-- Breast -->
                <div class="Breast">
                    <h5>Breast:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_breast_type" id="edit_breast_normal" value="Normal">
                                <label for="edit_breast_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_breast_type" id="edit_breast_mass" value="Mass">
                                <label for="edit_breast_mass"> Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_breast_type" id="edit_breast_nipple_discharge" value="Nipple Discharge">
                                <label for="edit_breast_nipple_discharge">Nipple Discharge</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_breast_type_error"></small>
                </div>
            </div>
            <div class="box d-flex">
                <!-- abdomen -->
                <div class="Breast w-50">
                    <h5>ABDOMEN:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_abdomen_type" id="edit_abdomen_normal" value="Normal">
                                <label for="edit_abdomen_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_abdomen_type" id="edit_abdomen_mass" value="Abdominal Mass">
                                <label for="edit_abdomen_mass">Abdominal Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_abdomen_type" id="edit_abdomen_varicosities" value="varicosities">
                                <label for="edit_abdomen_varicosities">varicosities</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_abdomen_type_error"></small>
                </div>
                <!-- extremites-->
                <div class="extremites">
                    <h5>EXTREMITES:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_extremites_type" id="edit_extremities_normal" value="Normal">
                                <label for="edit_extremities_normal">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_extremites_type" id="edit_extremities_edema" value="Edema">
                                <label for="edit_extremities_edema">Edema</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_extremites_type" id="edit_extremities_varicosities" value="varicosities">
                                <label for="edit_extremities_varicosities">varicosities</label>
                            </div>

                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_extremites_type_error"></small>
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
                            <input type="radio" name="edit_extremites_UID_type" id="edit_extremites_UID_type_normal" value="Normal">
                            <label for="edit_extremites_UID_type_normal">Normal</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_extremites_UID_type_mass" value="Mass">
                            <label for="edit_extremites_UID_type_mass">Mass</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_extremites_UID_type_abdominal_discharge" value="abdominal discharge">
                            <label for="edit_extremites_UID_type_abdominal_discharge">Abdominal Discharge</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_extremites_UID_type_cervical_abnormalities" value="cervial abnormalities">
                            <label for="edit_extremites_UID_type_cervical_abnormalities">Cervial Abnormalities</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_cervical_abnormalities_type" id="edit_cervical_abnormalities_type_warts" value="warts">
                                <label for="edit_cervical_abnormalities_type_warts" class="edit_cervical_abnormalities_type">Warts</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_cervical_abnormalities_type" id="edit_cervical_abnormalities_type_Polyp" value="Polyp or cyst">
                                <label for="edit_cervical_abnormalities_type_Polyp" class="edit_cervical_abnormalities_type">Polyp or cyst</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_cervical_abnormalities_type" id="edit_cervical_abnormalities_type_inflamation_or_erosion" value="inflammation or erosion">
                                <label for="edit_cervical_abnormalities_type_inflamation_or_erosion" class="edit_cervical_abnormalities_type">inflammation or erosion</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_cervical_abnormalities_type" id="edit_cervical_abnormalities_type_bloody_discharge" value="Bloody discharge">
                                <label for="edit_cervical_abnormalities_type_bloody_discharge" class="edit_cervical_abnormalities_type">Bloody discharge</label>
                            </div>
                            <small class="text-danger error-text" id="edit_cervical_abnormalities_type_error"></small>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_extremites_UID_type_cervical_consistency" value="cervical consistency">
                            <label for="edit_extremites_UID_type_cervical_consistency">cervical consistency</label>
                        </div>
                        <!-- firm or soft -->
                        <div class="inner-type-abnormalities d-flex gap-3" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_cervical_consistency_type" id="edit_cervical_consistency_type_firm" value="firm">
                                <label for="edit_cervical_consistency_type_firm" class="edit_cervical_consistency_type_label">Firm</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_cervical_consistency_type" id="edit_cervical_consistency_type_soft" value="soft">
                                <label for="edit_cervical_consistency_type_soft" class="edit_cervical_consistency_type_label">Soft</label>
                            </div>
                            <small class="text-danger error-text" id="edit_cervical_consistency_type_error"></small>
                        </div>
                        <!-- tenderness -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_cervical_tenderness" value="cervical tenderness">
                            <label for="edit_cervical_tenderness">cervical tenderness</label>
                        </div>
                        <!-- adnexal mass -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_adnexal_mass" value="adnexal mass / tenderness">
                            <label for="edit_adnexal_mass">adnexal mass / tenderness</label>
                        </div>
                        <!-- uterine position -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="edit_extremites_UID_type" id="edit_physical_examination_extremites_UID_type_uterine" value="uterine position">
                            <label for="edit_physical_examination_extremites_UID_type_uterine">Uterine position</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_uterine_position_type" id="edit_uterine_position_type_mid" value="Mid">
                                <label for="edit_uterine_position_type_mid" class="edit_uterine_position_type_label">Mid</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_uterine_position_type" id="edit_uterine_position_type_anteflexed" value="Anteflexed">
                                <label for="edit_uterine_position_type_anteflexed" class="edit_uterine_position_type_label">Anteflexed</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="edit_uterine_position_type" id="edit_uterine_position_typer_retroflexed" value="Retroflexed">
                                <label for="edit_uterine_position_typer_retroflexed" class="edit_uterine_position_type_label">Retroflexed</label>
                            </div>
                            <small class="text-danger error-text" id="edit_uterine_position_type_error"></small>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="edit_uterine_depth" value="uterine depth">
                            <label for="edit_uterine_depth">Uterine depth</label>
                            <input type="text" class="w-25" name="edit_uterine_depth_text" id="edit_uterine_depth_text">
                            <small>cm</small>
                            <small class="text-danger error-text" id="edit_uterine_depth_text_error"></small>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="edit_extremites_UID_type_error"></small>
                </div>
            </div>

            <div class="cervical-consistency">

            </div>
        </div>
    </div>
    <h4 class="border-bottom px-1"> ACKNOWLEDGEMENT</h4>
    <div class="mb-3">This is to certify that the Physician/Nurse/Midwife of the clinic has fully
        explained to me the different methods available in the family planning
        and i freely choose the <input type="text" id="edit_choosen_method" name="edit_choosen_method" class="rounded text-center"> method.
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-2">
        <div class="mb-3 w-50 d-flex flex-column">
            <label for="edit_family_planning_signature_image">Upload Signature</label>
            <input type="file" name="edit_family_planning_signature_image" id="edit_family_planning_signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
        </div>
        <div class="mb-3 w-50">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center" name="edit_date_of_acknowledgement" id="edit_date_of_acknowledgement">
        </div>
    </div>
    <div class="mb-3 w-100">
        <p class="text-center">I hereby consent to the inclusion of my FP 1 in the Family Health Registry</p>
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-2 border-bottom">
        <div class="mb-3 w-50 d-flex flex-column">
            <label for="edit_family_planning_acknowlegement_consent_signature_image">Upload Signature</label>
            <input type="file" name="edit_family_planning_acknowlegement_consent_signature_image" id="edit_family_planning_acknowlegement_consent_signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
        </div>
        <div class="mb-3 w-50">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center" name="edit_date_of_acknowledgement_consent" id="edit_date_of_acknowledgement_consent">
        </div>
    </div>

</div>