<div class="obstetrical-history w-100 border-bottom ">
    <h4 class="border-bottom px-1">II. OBSTERICAL HISTORY</h4>
    <div class="obstetrical-content p-2">
        <div class="mb-3 border-bottom">
            <label for="No_pregnancy">Number of Pregnancies:</label>
            <div class="no-pregnancy  w-100">
                <div class="box1 d-flex  gap-2">
                    <div class="mb-3 d-flex align-items-center">
                        <label for="">G:</label>
                        <input type="text" placeholder="Enter the number" class="form-control" name="edit_G" id="edit_G">
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label for="edit_P">P:</label>
                        <input type="text" placeholder="Enter the number" class="form-control" name="edit_P" id="edit_P">
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label for="edit_full_term" class="text-nowrap">Full Term:</label>
                        <input type="text" placeholder="Enter the number" class="form-control" name="edit_full_term" id="edit_full_term">
                    </div>
                </div>
                <div class="box-2 d-flex gap-2">
                    <div class="mb-3 d-flex align-items-center">
                        <label for="edit_abortion" class="text-nowrap">Abortion:</label>
                        <input type="text" placeholder="Enter the number" class="form-control" name="edit_abortion" id="edit_abortion">
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label for="edit_premature" class="text-nowrap">Premature:</label>
                        <input type="text" placeholder="Enter the number" class="form-control" name="edit_premature" id="edit_premature">
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label for="edit_living_children" class="text-nowrap">Living Children:</label>
                        <input type="text" placeholder="Enter the number" class="form-control" name="edit_living_children" id="edit_living_children">
                    </div>
                </div>
            </div>
            <!-- date of last delivery -->
            <div class="mb-3">
                <label for="edit_date_of_last_delivery" class="text-nowrap">Date of Last Delivery:</label>
                <input type="date" name="edit_date_of_last_delivery" id="edit_date_of_last_delivery" class="form-control">
            </div>
            <div class="mb-3 d-flex gap-2 w-100">
                <label for="" class="text-nowrap">Type of Last Delivery:</label>
                <div class="delivery-type d-flex gap-4">
                    <input type="radio" id="edit_type_of_delivery_vaginal" name="edit_type_of_last_delivery" value="Vaginal">
                    <label for="edit_type_of_delivery_vaginal">Vaginal</label>
                    <input type="radio" id="edit_type_of_last_delivery_cesarean" name="edit_type_of_last_delivery" value="Cesarean Section">
                    <label for="edit_type_of_last_delivery_cesarean">Cesarean Section</label>
                </div>
            </div>
            <!-- last menstrual period -->
            <div class="mb-3">
                <label for="edit_date_of_last_delivery_menstrual_period" class="text-nowrap">Last menstrual period:</label>
                <input type="date" name="edit_date_of_last_delivery_menstrual_period" id="edit_date_of_last_delivery_menstrual_period" class="form-control">
            </div>
            <!-- previous -->
            <div class="mb-3">
                <label for="edit_date_of_previous_delivery_menstrual_period" class="text-nowrap">Previous menstrual period:</label>
                <input type="date" name="edit_date_of_previous_delivery_menstrual_period" id="edit_date_of_previous_delivery_menstrual_period" class="form-control">
            </div>
            <!-- mesntrual flow -->
            <div class="mb-3 d-flex flex-column">
                <label for="">Menstrual flow:</label>
                <div class="type-of-menstrual d-flex gap-4 align-items-center px-3">
                    <div class="box d-flex align-items-center gap-2">
                        <input type="radio" name="edit_type_of_menstrual" id="edit_scanty" value="scanty (1-2 pads per day)">
                        <label for="edit_scanty"> scanty (1-2 pads per day)</label>
                    </div>
                    <div class="box d-flex align-items-center gap-2">
                        <input type="radio" name="edit_type_of_menstrual" id="edit_moderate" value="moderate (3-5 pads per day)">
                        <label for="edit_moderate"> moderate (3-5 pads per day)</label>
                    </div>
                    <div class="box d-flex align-items-center gap-2">
                        <input type="radio" name="edit_type_of_menstrual" id="edit_heavy" value="heavy ( +5 pads per day)">
                        <label for="edit_heavy"> heavy ( +5 pads per day)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center gap-3">
                <input type="checkbox" class="form-checkbox" name="edit_Dysmenorrhea" id="edit_Dysmenorrhea" value="Yes">
                <label for="edit_Dysmenorrhea">Dysmenorrhea</label>
            </div>
            <!-- hydaildiform -->
            <div class="mb-3 d-flex align-items-center gap-3">
                <input type="checkbox" class="form-checkbox" id="edit_hydatidiform_mole" name="edit_hydatidiform_mole" value="Yes">
                <label for="edit_hydatidiform_mole">hydatidiform mole (within the last 12 months)</label>
            </div>
            <!-- history of ectopic pregnancy -->
            <div class="mb-3 d-flex align-items-center gap-3">
                <input type="checkbox" class="form-checkbox" id="edit_ectopic_pregnancy" name="edit_ectopic_pregnancy" value="Yes">
                <label for="edit_ectopic_pregnancy">History of ectopic pregnancy</label>
            </div>
        </div>
    </div>

</div>