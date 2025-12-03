<div class="obstetrical-history w-[100%] md:max-w-[900px] card shadow p-3 align-self-center d-flex" >
    <h4 class="border-bottom px-1">II. OBSTERICAL HISTORY</h4>
    <div class="obstetrical-content p-2">
        <div class="mb-3 border-bottom">
            <label for="No_pregnancy">Number of Pregnancies:</label>
            <div class="no-pregnancy  w-100">
                <div class="box1 d-flex  gap-2 flex-wrap flex-sm-nowrap">
                    <div class="mb-md-3 mb-1 d-flex align-items-center flex-fill">
                        <label for="">G:</label>
                        <input type="text" placeholder="Enter the number" class="form-control">
                    </div>
                    <div class="mb-md-3 mb-1 d-flex d-flex align-items-center flex-fill">
                        <label for="">P:</label>
                        <input type="text" placeholder="Enter the number" class="form-control">
                    </div>
                    <div class="mb-md-3 mb-1 d-flex d-flex align-items-center flex-fill">
                        <label for="" class="text-nowrap">Full Term:</label>
                        <input type="text" placeholder="Enter the number" class="form-control">
                    </div>
                </div>
                <div class="box-2 d-flex flex-fill flex-wrap gap-2">
                    <div class="mb-md-3 mb-0 d-flex flex-fill align-items-center flex-wrap ">
                        <label for="" class="text-nowrap">Abortion:</label>
                        <input type="text" placeholder="Enter the number" class="form-control">
                    </div>
                    <div class="mb-md-3 mb-0 d-flex flex-fill align-items-center flex-wrap ">
                        <label for="" class="text-nowrap">Premature:</label>
                        <input type="text" placeholder="Enter the number" class="form-control">
                    </div>
                    <div class="mb-md-3 mb-0 d-flex flex-fill align-items-center flex-wrap ">
                        <label for="" class="text-nowrap">Living Children:</label>
                        <input type="text" placeholder="Enter the number" class="form-control">
                    </div>
                </div>
            </div>
            <!-- date of last delivery -->
            <div class="mb-md-3 mb-1">
                <label for="" class="text-nowrap">Date of Last Delivery:</label>
                <input type="date" name="date-of-last-delivery" class="form-control">
            </div>
            <div class="mb-md-3 mb-1 d-flex  flex-wrap 1875remgap-2 w-100">
                <label for="" class="text-nowrap">Type of Last Delivery:</label>
                <div class="delivery-type d-flex gap-4">
                    <input type="radio" id="vaginal" name="type-of-delivery">
                    <label for="vaginal">Vaginal</label>
                    <input type="radio" id="cesarean-section" name="type-of-delivery">
                    <label for="cesarean-section">Cesarean Section</label>
                </div>
            </div>
            <!-- last menstrual period -->
            <div class="mb-md-3 mb-1">
                <label for="" class="text-nowrap">Last menstrual period:</label>
                <input type="date" name="date-of-last-delivery" class="form-control">
            </div>
            <!-- previous -->
            <div class="mb-md-3 mb-1">
                <label for="" class="text-nowrap">Previous menstrual period:</label>
                <input type="date" name="date-of-last-delivery" class="form-control">
            </div>
            <!-- mesntrual flow -->
            <div class="mb-md-3 mb-1 d-flex flex-column">
                <label for="">Menstrual flow:</label>
                <div class="type-of-menstrual d-flex gap-4 align-items-center px-3 flex-wrap">
                    <div class="box d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-menstrual" id="scanty">
                        <label for="scanty"> scanty (1-2 pads per day)</label>
                    </div>
                    <div class="box d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-menstrual" id="moderate">
                        <label for="moderate"> moderate (3-5 pads per day)</label>
                    </div>
                    <div class="box d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-menstrual" id="scanty">
                        <label for="scanty"> heavy ( +5 pads per day)</label>
                    </div>
                </div>
            </div>
            <div class="mb-md-3 mb-1 d-flex align-items-center gap-3">
                <input type="checkbox" class="form-checkbox">
                <label for="">Dysmenorrhea</label>
            </div>
            <!-- hydaildiform -->
            <div class="mb-md-3 mb-1 d-flex align-items-center gap-3">
                <input type="checkbox" class="form-checkbox">
                <label for="">hydatidiform mole (within the last 12 months)</label>
            </div>
            <!-- history of ectopic pregnancy -->
            <div class="mb-md-3 mb-1 d-flex align-items-center gap-3">
                <input type="checkbox" class="form-checkbox">
                <label for="">History of ectopic pregnancy</label>
            </div>
        </div>
    </div>
    <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-auto">
        <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
        <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
    </div>
</div>