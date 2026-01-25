<!-- step 6 -->
<table style="width: 100%; border-collapse: collapse; font-size: 10px;">
    <tr style="background-color: #e9ecef; border-bottom: 2px solid #000;">
        <th colspan="4" style="border: 1px solid #000; padding: 10px 10px; text-align: left; font-weight: bold;">
            V. PHYSICAL EXAMINATION
        </th>
    </tr>
    <tr>
        <th style="border: 1px solid #000; padding: 10px 10px; width: 25%; font-weight: bold;">Weight (cm)</th>
        <td style="border: 1px solid #000; padding: 10px 10px; width: 25%;" id="view_weight">{{$caseInfo->physical_examinations->weight??'N/A'}}</td>
        <th style="border: 1px solid #000; padding: 10px 10px; width: 25%; font-weight: bold;">Blood Pressure</th>
        <td style="border: 1px solid #000; padding: 10px 10px; width: 25%;" id="view_blood_pressure">{{$caseInfo->physical_examinations->blood_pressure??'N/A'}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid #000; padding: 10px 10px; font-weight: bold;">Height (cm)</th>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_height">{{$caseInfo->physical_examinations->height??'N/A'}}</td>
        <th style="border: 1px solid #000; padding: 10px 10px; font-weight: bold;">Pulse Rate (Bpm)</th>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_pulse_rate">{{$caseInfo->physical_examinations->pulse_rate??'N/A'}}</td>
    </tr>
</table>

<!-- General Physical Examination -->
<table style="width: 100%; border-collapse: collapse; margin-top: 1px; font-size: 10px;">
    <tr style="background-color: #e9ecef;">
        <th style="border: 1px solid #000; padding: 10px 10px; width: 30%; font-weight: bold;">Body Part</th>
        <th style="border: 1px solid #000; padding: 10px 10px; width: 70%; font-weight: bold;">Findings</th>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">Skin</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_skin_type">{{$caseInfo->physical_examinations->skin_type??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">Conjunctiva</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_conjuctiva_type">{{$caseInfo->physical_examinations->conjuctiva_type??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">Neck</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_neck_type">{{$caseInfo->physical_examinations->neck_type??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">Breast</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_breast_type">{{$caseInfo->physical_examinations->breast_type??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">Abdomen</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_abdomen_type">{{$caseInfo->physical_examinations->abdomen_type??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">Extremities</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_extremites_type">{{$caseInfo->physical_examinations->extremites_type??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 10px 10px;">EXTREMITIES (For IUD Acceptors)</td>
        <td style="border: 1px solid #000; padding: 10px 10px;" id="view_extremites_UID_type">
            @if($caseInfo->physical_examinations->extremites_UID_type == 'uterine depth')
            {{$caseInfo->physical_examinations->extremites_UID_type??'N/A'}} / {{$caseInfo->physical_examinations->uterine_depth_text??'N/A'}}
            @else
            {{$caseInfo->physical_examinations->extremites_UID_type??'N/A'}} /
            {{$caseInfo->physical_examinations->cervical_abnormalities_type??$caseInfo->physical_examinations->cervical_consistency_type??''}}
            @endif
        </td>
    </tr>
</table>

<!-- Acknowledgement Section - Compact -->
<table style="width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px;">
    <tr style="background-color: #e9ecef; border-bottom: 2px solid #000;">
        <th style="border: 1px solid #000; padding: 1px 2px; text-align: left; font-weight: bold;">ACKNOWLEDGEMENT</th>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 3px;">
            <p class="ack-text">
                This is to certify that the Physician/Nurse/Midwife of the clinic has fully
                explained to me the different methods available in family planning, and I freely
                choose the <u style="padding: 0 10px;">{{$caseInfo->choosen_method??'N/A'}}</u> method.
            </p>

            <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                <tr>
                    <td style="width: 50%; padding-right: 5px; vertical-align: top;">
                        <p class="ack-label">Client's Signature:</p>
                        @if($caseInfo->signature_image != null)
                        <img src="{{ storage_path('app/public/' . $caseInfo->signature_image	) }}" alt="Signature" style="max-width:200px; height:40px;">
                        <div style="width: 100%;border-bottom:1px solid black; margin-top: 1px;"></div>
                        @else
                        <div class="ack-signature-box" id="view_signature_image"></div>
                        @endif
                        <p style="margin: 0; font-size: 10px; color: #6c757d; text-align: center;">Signature on record</p>
                    </td>
                    <td style="width: 50%; padding-left: 5px; vertical-align: top;">
                        <p class="ack-label">Date:</p>
                        <div class="ack-signature-box" id="view_date_of_acknowledgement">{{$caseInfo->date_of_acknowledgement??''}}</div>
                    </td>
                </tr>
            </table>

            <p class="ack-text" style="text-align: center; margin: 3px 0;">
                I hereby consent to the inclusion of my FP1 record in the Family Health Registry.
            </p>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding-right: 5px; vertical-align: top; ">
                        <p class="ack-label">Client's Signature:</p>
                        @if($caseInfo->acknowledgement_consent_signature_image != null)
                        <img src="{{ storage_path('app/public/' . $caseInfo->acknowledgement_consent_signature_image	) }}" alt="Signature" style="max-width:200px; height:40px;">
                        <div style="width: 100%;border-bottom:1px solid black; margin-top: 1px;"></div>
                        @else
                        <div class="ack-signature-box" id="view_acknowledgement_consent_signature_image"></div>
                        @endif

                        <p style="margin: 0; font-size: 10px; color: #6c757d; text-align: center;">Signature on record</p>
                    </td>
                    <td style="width: 50%; padding-left: 5px; vertical-align: top;">
                        <p class="ack-label">Date:</p>
                        <div class="ack-signature-box" id="view_date_of_acknowledgement_consent">{{$caseInfo->date_of_acknowledgement_consent??''}}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>