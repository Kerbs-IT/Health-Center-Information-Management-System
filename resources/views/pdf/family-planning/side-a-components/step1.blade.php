<!-- Family Planning Client Assessment Record -->
<!-- STEP 1 -->
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td colspan="3" style="border: 1px solid #000; padding: 5px; width: 75%;">
            <h4 style="margin: 0 0 2px 0; font-size: 10px; font-weight: bold;">
                FAMILY PLANNING CLIENT ASSESSMENT RECORD
            </h4>
            <p class="instruction-text">
                Instruction for Physicians, Nurses and Midwives:
                Make sure that the client is not pregnant by using the questions listed in SIDE B.
                Completely fill out or check the required information.
                Refer accordingly for any abnormal history/findings for further medical evaluation.
            </p>
        </td>
        <td style="border: 1px solid #000; padding: 3px; width: 25%; vertical-align: top;">
            <p style="margin: 0 0 2px 0; font-size: 10px;">
                CLIENT ID: <span id="view_client_id">{{$caseInfo->client_id??''}}</span>
            </p>
            <p style="margin: 0 0 2px 0; font-size: 10px;">
                PHILHEALTH NO: <span id="view_philhealth_no">{{$caseInfo->philhealth_no??''}}</span>
            </p>
            <p style="margin: 0; font-size: 10px;">
                NHTS?: <span id="view_NHTS">{{$caseInfo->NHTS??''}}</span>
            </p>
        </td>
    </tr>
</table>

<!-- Client Information -->
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 10%; font-weight: bold; background-color: #f8f9fa; font-size: 10px;">Name of Client:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 30%; font-size: 10px;" id="view_client_name">{{$caseInfo->client_name}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 12%; font-weight: bold; font-size: 10px;">Date of Birth:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 12%; font-size: 10px;" id="view_client_date_of_birth">{{$caseInfo->client_date_of_birth}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 6%; font-weight: bold; font-size: 10px;">Age:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 6%; font-size: 10px;" id="view_client_age">{{$caseInfo->client_age??'0'}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 12%; font-weight: bold; font-size: 10px;">Occupation:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 12%; font-size: 10px;" id="view_occupation">{{$caseInfo->client_occupation??''}}</td>
    </tr>
</table>

<!-- Address and Personal Details -->
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 8%; font-weight: bold; background-color: #f8f9fa; font-size: 10px;">Address:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 30%; font-size: 10px;" id="view_client_address">{{$caseInfo->client_address??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 12%; font-weight: bold; font-size: 10px;">Contact Number:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 14%; font-size: 10px;" id="view_client_contact_number">{{$caseInfo->client_contact_number??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 10%; font-weight: bold; font-size: 10px;">Civil Status:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 10%; font-size: 10px;" id="view_client_civil_status">{{$caseInfo->client_civil_status??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 8%; font-weight: bold; font-size: 10px;">Religion:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 8%; font-size: 10px;" id="view_client_religion">{{$caseInfo->client_religion??''}}</td>
    </tr>
</table>

<!-- Spouse Information -->
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 10%; font-weight: bold; background-color: #f8f9fa; font-size: 10px;">Name of Spouse:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 30%; font-size: 10px;" id="view_spouse_name">{{
            trim(
                ($caseInfo->spouse_fname ?? '') . ' ' .
                (!empty($caseInfo->spouse_middle_initial) 
                    ? strtoupper(substr($caseInfo->spouse_middle_initial, 0, 1)) . '. ' 
                    : ''
                ) .
                ($caseInfo->spouse_last_name ?? '')
            )
        }}</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 12%; font-weight: bold; font-size: 10px;">Date of Birth:</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 12%; font-size: 10px;" id="view_spouse_date_of_birth">{{$caseInfo->spouse_date_of_birth??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 6%; font-weight: bold; font-size: 10px;">Age:</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 6%; font-size: 10px;" id="view_spouse_age">{{$caseInfo->spouse_age??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 12%; font-weight: bold; font-size: 10px;">Occupation:</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 12%; font-size: 10px;" id="view_spouse_occupation">{{$caseInfo->spouse_occupation??''}}</td>
    </tr>
</table>

<!-- Family & Financial Information -->
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 18%; font-weight: bold; font-size: 10px;">No. of Living Children:</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 12%; font-size: 10px;" id="view_number_of_living_children">{{$caseInfo->number_of_living_children??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 20%; font-weight: bold; font-size: 10px;">Plan to Have More Children?</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 12%; font-size: 10px;" id="view_plan_to_have_more_children">{{$caseInfo->plan_to_have_more_children??''}}</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 20%; font-weight: bold; font-size: 10px;">Average Monthly Income:</td>
        <td style="border: 1px solid #000; padding: 5px 2px; width: 18%; font-size: 10px;" id="view_average_montly_income">{{$caseInfo->average_monthly_income??''}}</td>
    </tr>
</table>

<!-- Category Headers and Data -->
<table style="width: 100%; border-collapse: collapse;">
    <tr style="background-color: #e9ecef;">
        <th style="border: 1px solid #000; padding: 5px; width: 25%; text-align: center; font-weight: bold; font-size: 10px;">Type of Client</th>
        <th style="border: 1px solid #000; padding: 5px; width: 50%; text-align: center; font-weight: bold; font-size: 10px;">Client Reason</th>
        <th style="border: 1px solid #000; padding: 5px; width: 25%; text-align: center; font-weight: bold; font-size: 10px;">Previously Used Methods</th>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-size: 10px;" id="view_type_of_patient">{{$caseInfo->type_of_patient??''}}</td>
        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-size: 10px;" id="view_reason">Spacing</td>
        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-size: 10px;" id="view_previously_used_method">{{$caseInfo->previously_used_method??''}}</td>
    </tr>
</table>