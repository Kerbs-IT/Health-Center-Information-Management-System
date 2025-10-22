<!-- Family Planning Client Assessment Record -->
<table class="table mb-0">
    <thead class="border-2 border-bottom">
        <tr class="border-dark border-b-2">
            <td colspan="12" class="border border-dark">
                <h4 class="text-start mb-0">
                    FAMILY PLANNING CLIENT ASSESSMENT RECORD
                </h4>
                <p class="text-start mb-0">
                    Instruction for Physicians, Nurses and Midwives:
                    Make sure that the client is not pregnant by using the questions listed in SIDE B.
                    Completely fill out or check the required information.
                    Refer accordingly for any abnormal history/findings for further medical evaluation.
                </p>
            </td>

            <td colspan="4" class="">
                <p class="mb-0 text-start">
                    CLIENT ID: <span id="view_client_id">1010</span>
                </p>
                <p class="mb-0 text-start">
                    PHILHEALTH NO: <span id="view_philhealth_no">1010</span>
                </p>
                <p class="mb-0 text-start">
                    NHTS?: <span id="view_NHTS">1010</span>
                </p>
            </td>
        </tr>
    </thead>
    <tbody>
        <!-- Client Information -->
        <tr>
            <td colspan="2" class="fw-bold text-start border bg-light">Name of Client:</td>
            <td colspan="6" id="view_client_name">Andrew E.</td>

            <td colspan="2" class="fw-bold">Date of Birth:</td>
            <td colspan="2" id="view_client_date_of_birth">Feb 1</td>

            <td class="fw-bold">Age:</td>
            <td id="view_client_age">30</td>

            <td class="fw-bold">Occupation:</td>
            <td colspan="2" id="view_occupation">Chef</td>
        </tr>

        <!-- Address and Personal Details -->
        <tr>
            <td class="fw-bold text-start  border">Address:</td>
            <td colspan="6" id="view_client_address">Blk 2 Lot 1 Inodoro Vill</td>

            <td colspan="3" class="fw-bold">Contact Number:</td>
            <td colspan="2" id="view_client_contact_number">+63-10-231-231</td>

            <td class="fw-bold">Civil Status:</td>
            <td id="view_client_civil_status">Single</td>

            <td class="fw-bold">Religion:</td>
            <td id="view_client_religion">Catholic</td>
        </tr>

        <!-- Spouse Information -->
        <tr>
            <td colspan="2" class="fw-bold text-start border ">Name of Spouse:</td>
            <td colspan="6" id="view_spouse_name"></td>

            <td colspan="2" class="fw-bold">Date of Birth:</td>
            <td colspan="2" id="view_spouse_date_of_birth"></td>

            <td class="fw-bold">Age:</td>
            <td id="view_spouse_age"></td>

            <td class="fw-bold">Occupation:</td>
            <td colspan="2" id="view_spouse_occupation">Chef</td>
        </tr>

        <!-- Family & Financial Information -->
        <tr class="border-b-4 border-black">
            <td colspan="3" class="fw-bold">No. of Living Children:</td>
            <td id="view_number_of_living_children">2</td>

            <td colspan="6" class="fw-bold">Plan to Have More Children?</td>
            <td id="view_plan_to_have_more_children">Yes</td>

            <td colspan="4" class="fw-bold">Average Monthly Income:</td>
            <td id="view_average_montly_income">15,000</td>
        </tr>

        <!-- Category Headers -->
        <tr class="text-center fw-bold table-secondary">
            <th colspan="4">Type of Client</th>
            <th colspan="8">Client Reason</th>
            <th colspan="4">Previously Used Methods</th>
        </tr>

        <!-- Category Data -->
        <tr class="text-center border-b-3 border-dark">
            <td colspan="4" id="view_type_of_patient">Current User</td>
            <td colspan="8" id="view_reason">Spacing</td>
            <td colspan="4" id="view_previously_used_method">Injectable, Condom</td>
        </tr>
    </tbody>
</table>