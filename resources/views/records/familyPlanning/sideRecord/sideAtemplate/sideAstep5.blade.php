<div class="physical-examination w-100 ">
    <h4 class="border-bottom px-1">V. PHYSICAL EXAMINATION</h4>
    <div class="vital-sign">
        <h5>Vital Sign</h5>
        <div class="mb-md-2 mb-0 input-field d-flex gap-md-3 gap-1 w-100 first-row flex-wrap flex-lg-nowrap">
            <div class="mb-md-2 mb-0 flex-fill lg:w-[50%]">
                <label for="BP">Blood Pressure:</label>
                <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="side_A_add_blood_pressure" id="side_A_add_blood_pressure">
                <small class="text-danger error-text" id="side_A_add_blood_pressure_error"></small>
            </div>

            <div class="mb-md-2 mb-0 flex-fill lg:w-[50%]">
                <label for="BP">Pulse Rate(Bpm):</label>
                <input type="text" class="form-control w-100" placeholder=" 60-100" name="side_A_add_pulse_rate" id="side_A_add_pulse_rate">
                <small class="text-danger error-text" id="side_A_add_pulse_rate_error"></small>
            </div>
        </div>
        <!-- 2nd row -->
        <div class="mb-md-2 mb-0 input-field d-flex gap-md-3 gap-1 w-100 second-row flex-wrap flex-lg-nowrap">

            <div class="mb-md-2 mb-0 flex-fill lg:w-[50%]">
                <label for="BP">Height(cm):</label>
                <input type="number" class="form-control w-100" placeholder="00.00" name="side_A_add_height" id="side_A_add_height">
                <small class="text-danger error-text" id="side_A_add_height_error"></small>
            </div>
            <div class="mb-md-2 mb-0 flex-fill lg:w-[50%]">
                <label for="BP">Weight(kg):</label>
                <input type="number" class="form-control w-100" placeholder=" 00.00" name="side_A_add_weight" id="side_A_add_weight">
                <small class="text-danger error-text" id="side_A_add_weight_error"></small>
            </div>
        </div>
    </div>
    <div class="tests d-flex border-bottom flex-xl-nowrap flex-wrap">
        <div class="row-1 w-100">
            <div class="box d-flex w-100 flex-wrap">
                <div class="skin flex-fill">
                    <h5>SKIN:</h5>
                    <div class="list-of-skin px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_skin_type" id="side_A_add_skin_normal" value="Normal">
                                <label for="side_A_add_skin_normal">Normal</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_skin_type" id="side_A_add_skin_pale" value="Pale">
                                <label for="side_A_add_skin_pale">Pale</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_skin_type" id="side_A_add_skin_yellowish" value="Yellowish">
                                <label for="side_A_add_skin_yellowish">Yellowish</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_skin_type" id="side_A_add_skin_hematoma" value="Hematoma">
                                <label for="side_A_add_skin_hematoma">Hematoma</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_skin_type_error"></small>
                </div>
                <div class="CONJUCTIVA flex-fill">
                    <h5>CONJUCTIVA:</h5>
                    <div class="list-of-conjuctiva px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_conjuctiva_type" id="side_A_add_conjuctiva_normal" value="Normal">
                                <label for="side_A_add_conjuctiva_normal">Normal</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_conjuctiva_type" id="side_A_add_conjuctiva_pale" value="Pale">
                                <label for="side_A_add_conjuctiva_pale">Pale</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_conjuctiva_type" id="side_A_add_conjuctiva_yellowish" value="Yellowish">
                                <label for="side_A_add_conjuctiva_yellowish">Yellowish</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_conjuctiva_type_error"></small>
                </div>
            </div>
            <!-- neck -->
            <div class="box d-flex w-100 flex-wrap">
                <div class="neck flex-fill">
                    <h5>NECK:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_neck_type" id="side_A_add_neck_type_normal" value="Normal">
                                <label for="side_A_add_neck_type_normal">Normal</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_neck_type" id="side_A_add_neck_type_neck_mass" value="Neck Mass">
                                <label for="side_A_add_neck_type_neck_mass">Neck Mass</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_neck_type" id="side_A_add_nect_type_enlarge" value="enlarged lymph nodes">
                                <label for="side_A_add_nect_type_enlarge" class="text-wrap">enlarged lymph nodes</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_neck_type_error"></small>
                </div>
                <!-- Breast -->
                <div class="Breast flex-fill">
                    <h5>Breast:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_breast_type" id="side_A_add_breast_normal" value="Normal">
                                <label for="side_A_add_breast_normal">Normal</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_breast_type" id="side_A_add_breast_mass" value="Mass">
                                <label for="side_A_add_breast_mass"> Mass</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_breast_type" id="side_A_add_breast_nipple_discharge" value="Nipple Discharge">
                                <label for="side_A_add_breast_nipple_discharge">Nipple Discharge</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_breast_type_error"></small>
                </div>
            </div>
            <div class="box d-flex flex-wrap">
                <!-- abdomen -->
                <div class="Breast flex-fill">
                    <h5>ABDOMEN:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_abdomen_type" id="side_A_add_abdomen_normal" value="Normal">
                                <label for="side_A_add_abdomen_normal">Normal</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_abdomen_type" id="side_A_add_abdomen_mass" value="Abdominal Mass">
                                <label for="side_A_add_abdomen_mass">Abdominal Mass</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_abdomen_type" id="side_A_add_abdomen_varicosities" value="varicosities">
                                <label for="side_A_add_abdomen_varicosities">varicosities</label>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="side_A_add_abdomen_type_error"></small>
                </div>
                <!-- extremites-->
                <div class="extremites flex-fill">
                    <h5>EXTREMITES:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_extremites_type" id="side_A_add_extremities_normal" value="Normal">
                                <label for="side_A_add_extremities_normal">Normal</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_extremites_type" id="side_A_add_extremities_edema" value="Edema">
                                <label for="side_A_add_extremities_edema">Edema</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_extremites_type" id="side_A_add_extremities_varicosities" value="varicosities">
                                <label for="side_A_add_extremities_varicosities">varicosities</label>
                            </div>

                        </div>
                        <small class="text-danger error-text" id="side_A_add_extremites_type_error"></small>
                    </div>
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
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_extremites_UID_type_normal" value="Normal">
                            <label for="side_A_add_extremites_UID_type_normal">Normal</label>
                        </div>
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_extremites_UID_type_mass" value="Mass">
                            <label for="side_A_add_extremites_UID_type_mass">Mass</label>
                        </div>
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_extremites_UID_type_abdominal_discharge" value="abdominal discharge">
                            <label for="side_A_add_extremites_UID_type_abdominal_discharge">Abdominal Discharge</label>
                        </div>
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_extremites_UID_type_cervical_abnormalities" value="cervial abnormalities">
                            <label for="side_A_add_extremites_UID_type_cervical_abnormalities">Cervial Abnormalities</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_cervical_abnormalities_type" id="side_A_add_cervical_abnormalities_type_warts" value="warts">
                                <label for="side_A_add_cervical_abnormalities_type_warts" class="cervical_abnormalities_type">Warts</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_cervical_abnormalities_type" id="side_A_add_cervical_abnormalities_type_Polyp" value="Polyp or cyst">
                                <label for="side_A_add_cervical_abnormalities_type_Polyp" class="cervical_abnormalities_type">Polyp or cyst</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_cervical_abnormalities_type" id="side_A_add_cervical_abnormalities_type_inflamation_or_erosion" value="inflammation or erosion">
                                <label for="side_A_add_cervical_abnormalities_type_inflamation_or_erosion" class="cervical_abnormalities_type">inflammation or erosion</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_cervical_abnormalities_type" id="side_A_add_cervical_abnormalities_type_bloody_discharge" value="Bloody discharge">
                                <label for="side_A_add_cervical_abnormalities_type_bloody_discharge" class="cervical_abnormalities_type">Bloody discharge</label>
                            </div>
                            <small class="text-danger error-text" id="side_A_add_cervical_abnormalities_type_error"></small>
                        </div>
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_extremites_UID_type_cervical_consistency" value="cervical consistency">
                            <label for="side_A_add_extremites_UID_type_cervical_consistency">cervical consistency</label>
                        </div>
                        <!-- firm or soft -->
                        <div class="inner-type-abnormalities d-flex gap-md-3 gap-1" style="padding-left: 30px;">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_cervical_consistency_type" id="side_A_add_cervical_consistency_type_firm" value="firm">
                                <label for="side_A_add_cervical_consistency_type_firm" class="cervical_consistency_type_label">Firm</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_cervical_consistency_type" id="side_A_add_cervical_consistency_type_soft" value="soft">
                                <label for="side_A_add_cervical_consistency_type_soft" class="cervical_consistency_type_label">Soft</label>
                            </div>
                            <small class="text-danger error-text" id="side_A_add_cervical_consistency_type_error"></small>
                        </div>
                        <!-- tenderness -->
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_cervical_tenderness" value="cervical tenderness">
                            <label for="side_A_add_cervical_tenderness">cervical tenderness</label>
                        </div>
                        <!-- adnexal mass -->
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="side_A_add_adnexal_mass" value="adnexal mass / tenderness">
                            <label for="side_A_add_adnexal_mass">adnexal mass / tenderness</label>
                        </div>
                        <!-- uterine position -->
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="side_A_add_extremites_UID_type" id="physical_examination_extremites_UID_type_uterine" value="uterine position">
                            <label for="physical_examination_extremites_UID_type_uterine">Uterine position</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_uterine_position_type" id="side_A_add_uterine_position_type_mid" value="Mid">
                                <label for="side_A_add_uterine_position_type_mid" class="uterine_position_type_label">Mid</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_uterine_position_type" id="side_A_add_uterine_position_type_anteflexed" value="Anteflexed">
                                <label for="side_A_add_uterine_position_type_anteflexed" class="uterine_position_type_label">Anteflexed</label>
                            </div>
                            <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                                <input type="radio" name="side_A_add_uterine_position_type" id="side_A_add_uterine_position_typer_retroflexed" value="Retroflexed">
                                <label for="side_A_add_uterine_position_typer_retroflexed" class="uterine_position_type_label">Retroflexed</label>
                            </div>
                            <small class="text-danger error-text" id="side_A_add_uterine_position_type_error"></small>
                        </div>
                        <div class="mb-md-2 mb-0 d-flex align-items-center gap-2">
                            <input type="radio" name="physical_examination_extremites_UID_type" id="side_A_add_uterine_depth" value="uterine depth">
                            <label for="side_A_add_uterine_depth">Uterine depth</label>
                            <input type="text" class="w-25" name="side_A_add_uterine_depth_text" id="side_A_add_uterine_depth_text">
                            <small>cm</small>
                        </div>
                    </div>
                </div>
                <small class="text-danger error-text" id="side_A_add_extremites_UID_type_error"></small>
            </div>

            <div class="cervical-consistency">

            </div>
        </div>
    </div>
    <h4 class="border-bottom px-1"> ACKNOWLEDGEMENT</h4>
    <div class="mb-3">This is to certify that the Physician/Nurse/Midwife of the clinic has fully
        explained to me the different methods available in the family planning
        and i freely choose the <input type="text" id="side_A_add_choosen_method" name="side_A_add_choosen_method" class="rounded text-center"> method.
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-md-2 gap-1 flex-lg-row flex-column">
        <div class="mb-md-3 mb-0  w-[100%] lg:w-[50%] d-flex flex-column ">
            <label for="signature_image">Upload Signature</label>
            <input type="file" name="signature_image" id="signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
        </div>
        <div class="mb-md-3 mb-0  w-[100%] lg:w-[50%]">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center" name="side_A_add_date_of_acknowledgement" id="side_A_add_date_of_acknowledgement">
        </div>
    </div>
    <small class="text-danger error-text" id="signature_image_error"></small>
    <small class="text-danger error-text" id="side_A_add_date_of_acknowledgement_error"></small>
    <div class="mb-md-3 mb-0 w-100">
        <p class="text-center">I hereby consent to the inclusion of my FP 1 in the Family Health Registry</p>
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-md-2 gap-1 border-bottom flex-lg-row flex-column">
        <div class="mb-md-3 mb-0 w-[100%] lg:w-[50%] d-flex flex-column">
            <label for="signature_image">Upload Signature</label>
            <input type="file" name="signature_image" id="signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
        </div>
        <div class="mb-md-3 mb-0 w-[100%] lg:w-[50%]">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center" name="side_A_add_date_of_acknowledgement_consent" id="side_A_add_date_of_acknowledgement_consent">
        </div>
    </div>
    <small class="text-danger error-text" id="signature_image_error"></small>
    <small class="text-danger error-text" id="side_A_add_date_of_acknowledgement_consent_error"></small>

</div>