<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>

    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/css/patient/record.css',
    'resources/css/masterList/masterlist.css',
    'resources/css/masterList/wra_masterlist.css',
    'resources/js/masterlist/wra.js'])
    <div class="masterList-vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column w-100 overflow-x-hidden">
            @include('layout.header')
            <div class="d-flex flex-column flex-grow-1 p-3 overflow-y-auto">
                <!-- LIVEWIRE HERE -->
                <livewire:masterlist.w-r-a>
                <!-- LIVEWIRE END HERE -->
            </div>
        </div>
        <!-- edit modal -->
        <div class="modal fade" id="wraMasterListModal" tabindex="-1" aria-labelledby="wraMasterListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="#" class="flex-column" id="edit-wra-masterlist-form">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="wraModalLabel">Women of Reproductive Age (WRA) Masterlist Details</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                        </div>

                        <div class="modal-body w-100">
                            <div class="mb-2">
                                <label for="house_hold_number" class="fw-bold">House Hold Number:</label>
                                <input type="text" class="form-control border-1" name="house_hold_number">
                            </div>
                            <div class="input-group mb-2">
                                <label for="" class="w-100">Name of Child</label>
                                <div class="full-name d-flex gap-2 w-100 flex-grow-1 flex-wrap flex-lg-nowrap">
                                    <input type="text" name="wra_masterlist_fname" id="wra_masterlist_fname" placeholder="Enter First Name" class="form-control border">
                                    <input type="text" name="wra_masterlist_MI" id="wra_masterlist_MI" placeholder="Enter Middle Initial" class="form-control  border">
                                    <input type="text" name="wra_masterlist_lname" id="wra_masterlist_lname" placeholder="Enter Last Name" class="form-control  border">
                                </div>

                            </div>
                            <div class="input-group mb-2">
                                <h4>Address</h4>
                                <div class="input-field d-flex gap-2 align-items-center w-100 flex-wrap flex-lg-nowrap">
                                    <div class=" mb-2 w-full lg:w-[50%]">
                                        <label for="street">Street*</label>
                                        <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2 border" name="street" value="">
                                        <small class="text-danger error-text" id="street_error"></small>
                                    </div>
                                    <div class="mb-2 w-full lg:w-[50%]">
                                        <label for="brgy">Barangay*</label>
                                        @php
                                        $brgy = \App\Models\brgy_unit::orderBy('brgy_unit') -> get();
                                        @endphp
                                        <select name="brgy" id="edit_brgy" class="form-select py-2">
                                            <option value="" disabled selected>Select a brgy</option>
                                            @foreach($brgy as $brgy_unit)
                                            <option value="{{ $brgy_unit -> brgy_unit }}">{{$brgy_unit -> brgy_unit}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger error-text" id="brgy_error"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-2 w-100 d-flex flex-grow-1 gap-2 ">

                                <div class="input-field flex-grow-1 ">
                                    <label for="age">Age</label>
                                    <input type="number" id="age" placeholder="20" class="form-control" name="age" value="">
                                    <small class="text-danger error-text" id="age_error"></small>
                                </div>
                                <div class="input-field flex-grow-1 ">
                                    <label for="birthdate">Date of Birth</label>
                                    <input type="date" id="birthdate" placeholder="" class="form-control w-100 px-5" name="date_of_birth" value="">
                                    <small class="text-danger error-text" id="date_of_birth_error"></small>
                                </div>
                            </div>
                            <div class="input-group mb-2 w-100 d-flex flex-grow-1 gap-2 flex-wrap flex-lg-nowrap">

                                <div class="input-field flex-grow-1 ">
                                    <label for="age">SE status</label>
                                    <div class="radio-inputs w-100 d-flex justify-content-evenly">
                                        <input type="radio" name="SE_status" value="Yes" id="NHTS">
                                        <label for="NHTS">NHTS</label>
                                        <input type="radio" name="SE_status" value="No" id="NON-NHTS">
                                        <label for="NON-NHTS">NON-NHTS</label>
                                    </div>
                                </div>
                                <div class="input-field flex-grow-1">
                                    <label for="" class="form-label">Do you plan to have more children?</label>

                                    <div class="radio-inputs w-100 d-flex justify-content-center">

                                        <!-- YES OPTION -->
                                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                                            <input type="radio" id="plan_yes" name="plan_to_have_more_children" value="Yes">
                                            <label for="plan_yes" class="mb-0">Yes</label>

                                            <!-- Sub-options (Now / Spacing) -->
                                            <span>(</span>
                                            <div class="sub-radio d-flex gap-4 px-2">
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="radio" id="plan_yes_now" name="plan_to_have_more_children_yes" value="now">
                                                    <label for="plan_yes_now" class="mb-0 plan_yes_label">Now</label>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="radio" id="plan_yes_spacing" name="plan_to_have_more_children_yes" value="spacing">
                                                    <label for="plan_yes_spacing" class="mb-0 plan_yes_label">Spacing</label>
                                                </div>
                                            </div>
                                            <span>)</span>
                                        </div>

                                        <!-- NO OPTION -->
                                        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-center">
                                            <input type="radio" id="plan_no" name="plan_to_have_more_children" value="No">
                                            <label for="plan_no" class="mb-0">No</label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- Currently using any FP method -->
                            <div class="input-field mb-3">
                                <label class="form-label fw-semibold">Are you currently using any FP method?</label>

                                <!-- YES / NO RADIO -->
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" id="fp_yes" name="currently_using_any_FP_method" value="yes">
                                        <label for="fp_yes" class="mb-0">Yes</label>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" id="fp_no" name="currently_using_any_FP_method" value="no">
                                        <label for="fp_no" class="mb-0">No</label>
                                    </div>
                                </div>

                                <!-- CHECKBOX GRID -->
                                <div class="methods row row-cols-1 row-cols-sm-2 row-cols-md-4 g-2 ps-4">

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="implant" value="Implant" name="currently_using_methods[]">
                                            <label class="form-check-label check-box-label" for="implant">Implant</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="injectable" value="Injectable" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="injectable">Injectable</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="lam" value="LAM" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="lam">LAM</label>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="uid" value="UID" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="uid">UID</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="coc" value="COC" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="coc">COC</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="sdm" value="SDM" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="sdm">SDM</label>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="btl" value="BTL" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="btl">BTL</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="pop" value="POP" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="pop">POP</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="bbt" value="BBT" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="bbt">BBT</label>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="nsv" value="NSV" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="nsv">NSV</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="condom" value="Condom" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="condom">Condom</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method" type="checkbox" id="bom" value="BOM/CMM/STM" name="currently_using_methods[]">
                                            <label class="form-check-label  check-box-label" for="bom">BOM/CMM/STM</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- would you like to shift to modern  method  -->
                            <div class="input-group w-100 d-flex gap-2 justify-content-between ">
                                <div class="input-field mb-3 flex-grow-1 border-end">
                                    <label for="">Would you like to shift to modern method?</label>
                                    <div class="radio-inputs w-100 d-flex justify-content-evenly">
                                        <input type="radio" name="shift_to_modern_method" value="Yes" id="shift_to_modern_method_yes">
                                        <label for="shift_to_modern_method_yes">Yes</label>

                                        <input type="radio" name="shift_to_modern_method" value="No" id="shift_to_modern_method_no">
                                        <label for="shift_to_modern_method_no">No</label>
                                    </div>
                                </div>
                                <!-- unmet needs -->
                                <div class="input-field mb-3 flex-grow-1">
                                    <label for="">WRA with MFP Unmet Need?</label>
                                    <div class="radio-inputs w-100 d-flex justify-content-evenly">
                                        <input type="radio" name="wra_with_MFP_unmet_need" value="yes" id="wra_with_MFP_unmet_need_yes">
                                        <label for="wra_with_MFP_unmet_need_yes">Yes</label>
                                        <input type="radio" name="wra_with_MFP_unmet_need" value="no" id="wra_with_MFP_unmet_need_no">
                                        <label for="wra_with_MFP_unmet_need_no">No</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Did wra accept any modern method -->
                            <div class="input-group mb-3">
                                <label for="">Based on TCL on FP, did WRA accept any modern FP method?</label>
                                <div class="radio-inputs w-100 d-flex justify-content-evenly">
                                    <input type="radio" name="wra_accept_any_modern_FP_method" value="yes" id="wra_accept_any_modern_FP_method_yes">
                                    <label for="wra_accept_any_modern_FP_method_yes">Yes</label>
                                    <input type="radio" name="wra_accept_any_modern_FP_method" value="no" id="wra_accept_any_modern_FP_method_no">
                                    <label for="wra_accept_any_modern_FP_method_no">No</label>
                                </div>
                                <!-- if the wra accept any modern method -->
                                <label for="date_when_FP_method_accepted" class="w-100 fw-bold">Date when FP method accepted:</label>
                                <input type="date" name="date_when_FP_method_accepted" class="form-control w-100 bg-light modern-FP-inputs" id="date_when_FP_method_accepted">
                                <label for="" class="w-100 ">Moden FP methods:</label>
                                <div class="methods row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 ps-4">

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_implant" value="Implant" name="selected_modern_FP_method[]">
                                            <label class="form-check-label modern-check-box-label" for="modern_implant">Implant</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_injectable" value="Injectable" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_injectable">Injectable</label>
                                        </div>

                                    </div>

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_uid" value="UID" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_uid">UID</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_coc" value="COC" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_coc">COC</label>
                                        </div>

                                    </div>

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_btl" value="BTL" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_btl">BTL</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_pop" value="POP" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_pop">POP</label>
                                        </div>

                                    </div>

                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_nsv" value="NSV" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_nsv">NSV</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input fp-method modern-FP-inputs" type="checkbox" id="modern_condom" value="Condom" name="selected_modern_FP_method[]">
                                            <label class="form-check-label  modern-check-box-label" for="modern_condom">Condom</label>
                                        </div>

                                    </div>

                                </div>
                            </div>



                        </div>



                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="add-cancel-btn">Cancel</button>
                            <button type="submit" class="btn btn-success" id="update_wra_masterlist_save_btn">Update Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('masterlist_wra');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>