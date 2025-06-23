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
                                <input type="radio" name="skin-type" id="new-acceptor">
                                <label for="new-acceptor">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="skin-type" id="current-user">
                                <label for="current-user">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="skin-type" id="current-method">
                                <label for="current-method">Yellowish</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="skin-type" id="current-method">
                                <label for="current-method">Hematoma</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="CONJUCTIVA w-50">
                    <h5>CONJUCTIVA:</h5>
                    <div class="list-of-conjuctiva px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="conjuctiva-type" id="new-acceptor">
                                <label for="new-acceptor">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="conjuctiva-type" id="current-user">
                                <label for="current-user">Pale</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="conjuctiva-type" id="current-method">
                                <label for="current-method">Yellowish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- neck -->
            <div class="box d-flex w-100">
                <div class="neck w-50">
                    <h5>NECK:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="neck-type" id="new-acceptor">
                                <label for="new-acceptor">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="neck-type" id="current-user">
                                <label for="current-user">Neck Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="neck-type" id="current-method">
                                <label for="current-method">enlarged lymph nodes</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Breast -->
                <div class="Breast">
                    <h5>Breast:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="breast-type" id="new-acceptor">
                                <label for="new-acceptor">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="breast-type" id="current-user">
                                <label for="current-user"> Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="breast-type" id="current-method">
                                <label for="current-method">Nipple Discharge</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box d-flex">
                <!-- abdomen -->
                <div class="Breast w-50">
                    <h5>ABDOMEN:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abdomen-type" id="new-acceptor">
                                <label for="new-acceptor">Normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abdomen-type" id="current-user">
                                <label for="current-user">Abdominal Mass</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abdomen-type" id="current-method">
                                <label for="current-method">varicosities</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- extremites-->
                <div class="extremites">
                    <h5>EXTREMITES:</h5>
                    <div class="list-of-neck px-2">
                        <div class="type-of-user-inputs ">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="extremites-type" id="new-acceptor">
                                <label for="new-acceptor">normal</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="extremites-type" id="current-user">
                                <label for="current-user">Edema</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="extremites-type" id="current-method">
                                <label for="current-method">varicosities</label>
                            </div>

                        </div>
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
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="extremites-UID-type" id="new-acceptor">
                            <label for="new-acceptor">Normal</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="extremites-UID-type" id="current-user">
                            <label for="current-user">Mass</label>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="extremites-UID-type" id="current-method">
                            <label for="current-method">Abdominal Discharge</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="new-acceptor">
                                <label for="new-acceptor">Warts</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="current-user">
                                <label for="current-user">Polyp or cyst</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="current-method">
                                <label for="current-method">inflammation or erosion</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="current-method">
                                <label for="current-method">Bloody discharge</label>
                            </div>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="cervical-consistency-type" id="new-acceptor">
                            <label for="new-acceptor">cervical consistency</label>
                        </div>
                        <!-- firm or soft -->
                        <div class="inner-type-abnormalities d-flex gap-3" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="new-acceptor">
                                <label for="new-acceptor">Firm</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="current-user">
                                <label for="current-user">Soft</label>
                            </div>
                        </div>
                        <!-- tenderness -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="cervical-tendency-type" id="new-acceptor">
                            <label for="new-acceptor">cervical tenderness</label>
                        </div>
                        <!-- adnexal mass -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="adnexal-mass-type" id="new-acceptor">
                            <label for="new-acceptor">adnexal mass / tenderness</label>
                        </div>
                        <!-- uterine position -->
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="adnexal-mass-type" id="new-acceptor">
                            <label for="new-acceptor">Uterine position</label>
                        </div>
                        <div class="inner-type-abnormalities" style="padding-left: 30px;">
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="new-acceptor">
                                <label for="new-acceptor">Mid</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="current-user">
                                <label for="current-user">Anteflexed</label>
                            </div>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <input type="radio" name="abnormalities-type" id="current-method">
                                <label for="current-method">Retroflexed</label>
                            </div>
                        </div>
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <input type="radio" name="abnormalities-type" id="current-method">
                            <label for="current-method">Uterine depth</label>
                            <input type="text" class="w-25">
                            <small>cm</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cervical-consistency">

            </div>
        </div>
    </div>
    <h4 class="border-bottom px-1"> ACKNOWLEDGEMENT</h4>
    <div class="mb-3">This is to certify that the Physician/Nurse/Midwife of the clinic has fully
        explained to me the different methods available in the family planning
        and i freely choose the <input type="text border-bottom" class="rounded"> method.
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-2">
        <div class="mb-3 w-50 d-flex flex-column">
            <label for="signature_image">Upload Signature</label>
            <input type="file" name="signature_image" id="signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
        </div>
        <div class="mb-3 w-50">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center">
        </div>
    </div>
    <div class="mb-3 w-100">
        <p class="text-center">I hereby consent to the inclusion of my FP 1 in the Family Health Registry</p>
    </div>
    <div class="signature d-flex justify-content-even w-100 gap-2 border-bottom">
        <div class="mb-3 w-50 d-flex flex-column">
            <label for="signature_image">Upload Signature</label>
            <input type="file" name="signature_image" id="signature_image" class="form-control text-center" accept="image/*" required>
            <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
        </div>
        <div class="mb-3 w-50">
            <label for="signature_image" class="text-white">Upload Signature</label>
            <input type="date" class="form-control w-100 text-center">
        </div>
    </div>
    <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-2">
        <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
        <button type="submit" class="btn btn-success px-5 py-2 fs-5" >Submit</button>
    </div>
</div>