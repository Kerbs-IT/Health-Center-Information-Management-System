<div class="mb-3">
    <!-- type of patient info -->
    <div class="box d-flex flex-column align-items-center type-of-client">
        <div class="spouse w-100">
            <!-- spouse info-->
            <div class="family-inputs mb-2 d-flex flex-column gap-1 w-100">
                <h5>Spouse Information</h5>
                <div class="mb-2">
                    <div class=" w-100 ">
                        <label for="name_of_spouse" class="text-nowrap">Name of Spouse:</label>
                        <div class="group d-flex align-items-center justify-content-center gap-2">
                            <input type="text" class="form-control bg-light" name="lna class=" form-control placeholder="LastName">
                            <input type="text" class="form-control bg-light" name="fname" placeholder="FirstName">
                            <input type="text" class="form-control bg-light" name="MI" placeholder="Middle Initial">
                        </div>
                    </div>
                </div>
                <div class="mb-2 d-flex gap-2">
                    <div class="input-field w-50">
                        <label for="contact_number" class="">Contact Number</label>
                        <input type="number" placeholder="+63-936-627-8671" class="form-control bg-light" name="contact_number" value="">
                        @error('contact_number')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-field w-50">
                        <label for="age">Age</label>
                        <input type="text" id="age" placeholder="20" class="form-control bg-light" name="age" value="">
                        @error('age')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-field w-50">
                        <label for="age">Occupation</label>
                        <input type="text" id="age" placeholder="20" class="form-control bg-light" name="age" value="chef">
                        @error('age')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="other-info">
            <h4>Children Information</h4>
            <div class="box d-flex align-items-center gap-2 border-bottom mb-3">
                <div class="mb-3 d-flex align-items-center">
                    <label for="No_of_children" class="text-nowrap">No. Of Living Children:</label>
                    <input type="number" name="number_of_children" class="form-control">
                </div>
                <div class="mb-3 d-flex gap-2 align-items-center">
                    <label for="" class="form-label text-nowrap mb-0">Plan To Have More Children?</label>
                    <div class="radio-con d-flex gap-2 form-radio">
                        <input type="radio" name="plans" id="children_plan_yes">
                        <label for="children_plan_yes">Yes</label>
                        <input type="radio" name="plans" id="children_plan_no">
                        <label for="children_plan_no">No</label>
                    </div>
                </div>
                <div class="mb-3 d-flex align-items-center gap-1">
                    <label for="montly_income" class="text-nowrap">Average Monthly Income</label>
                    <input type="number" class="form-control">
                </div>

            </div>
        </div>
        <div class="d-flex gap-5 mb-3 border-bottom w-100">

            <div class="type-of-client">
                <h4 class="text-nowrap">Type of Client</h4>
                <div class="type-of-user-inputs">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-patient" id="new-acceptor">
                        <label for="new-acceptor">New Acceptor</label>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-patient" id="current-user">
                        <label for="current-user">Current User</label>
                    </div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-patient" id="current-method">
                        <label for="current-method">Current Method</label>
                    </div>
                    <!-- new clinic -->
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-patient" id="changing-clinic">
                        <label for="changing-clinic">Changing Clinic</label>
                    </div>
                    <!-- dropout -->
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <input type="radio" name="type-of-patient" id="dropout-restart">
                        <label for="dropout-restart">Dropout/Restart</label>
                    </div>
                </div>
            </div>
            <!-- reasons -->
            <div class="client-reasons">
                <h4 class="text-nowrap">Client Reason</h4>
                <!-- fp of new user -->
                <div class="reason-con">
                    <div class="mb-3 d-flex gap-2">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="FP">
                            <label for="">spacing</label>
                            <input type="radio" name="FP">
                            <label for="">limiting</label>
                            <input type="radio" name="FP">
                            <label for="">others</label>
                            <input type="text" class="flex-grow-1">
                        </div>
                    </div>
                </div>
                <!-- FP of current user -->
                <div class="reason-con">
                    <div class="mb-3 d-flex gap-2">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason for FP:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="FP-current-user">
                            <label for="">spacing</label>
                            <input type="radio" name="FP-current-user">
                            <label for="">limiting</label>
                            <input type="radio" name="FP-current-user">
                            <label for="">others</label>
                            <input type="text" name="others">
                        </div>
                    </div>
                </div>
                <!-- current method -->
                <div class="reason-con">
                    <div class="mb-3 d-flex gap-4">
                        <label for="FP" class="text-decoration-underline text-nowrap">Reason:</label>
                        <div class="answers d-flex gap-2">
                            <input type="radio" name="FP">
                            <label for="">medical condition</label>
                            <input type="radio" name="FP">
                            <label for="">side effects</label>
                            <input type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- current method -->
        <div class="mb-3 border-bottom w-100">

            <div class="current-method-user">
                <h4>Previously used Method (for Current User)</h4>
                <div class="methods d-flex gap-3">
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="implant">
                            <label for="implant">Implant</label>
                        </div>
                        <!-- injectable -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="Injectable">
                            <label for="Injectable">Injectable</label>
                        </div>
                        <!-- LAM -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="LAM">
                            <label for="LAM">LAM</label>
                        </div>
                    </div>
                    <!-- 2nd column -->
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="UID">
                            <label for="UID">UID</label>
                        </div>
                        <!-- COC -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="COC">
                            <label for="COC">COC</label>
                        </div>
                        <!-- SDM -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="SDM">
                            <label for="SDM">SDM</label>
                        </div>
                    </div>
                    <!-- 3rd -->
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="BTL">
                            <label for="BTL">BTL</label>
                        </div>
                        <!-- POP -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="POP">
                            <label for="POP">POP</label>
                        </div>
                        <!-- BBT -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="BBT">
                            <label for="BBT">BBT</label>
                        </div>
                    </div>
                    <!-- 4TH -->
                    <div class="method-row">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="NSV">
                            <label for="NSV">NSV</label>
                        </div>
                        <!-- Condom -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="Condom">
                            <label for="Condom">Condom</label>
                        </div>
                        <!-- BOM/CMM/STM -->
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <input type="checkbox" name="BOM/CMM/STM">
                            <label for="BOM/CMM/STM">BOM/CMM/STM</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>