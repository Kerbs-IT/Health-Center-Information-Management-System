<table class="table table-bordered table-striped mb-0">
    <thead class="seperator">
        <tr>
            <th colspan="12" class="text-start">
                V. PHYSICAL EXAMINATION
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Weight (cm)</th>
            <td id="view_weight">{{$caseInfo->physical_examinations->weight??'N/A'}}</td>
            <th>Blood Pressure</th>
            <td id="view_blood_pressure">{{$caseInfo->physical_examinations->blood_pressure??'N/A'}}</td>
        </tr>
        <tr>
            <th>Height (cm)</th>
            <td id="view_height">{{$caseInfo->physical_examinations->height??'N/A'}}</td>
            <th>Pulse Rate (Bpm)</th>
            <td id="view_pulse_rate">{{$caseInfo->physical_examinations->pulse_rate??'N/A'}}</td>
        </tr>

    </tbody>
</table>

<!-- General Physical Examination -->
<table class="table table-bordered mb-0">
    <thead class="">
        <tr>
            <th>Body Part</th>
            <th>Findings</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Skin</td>
            <td id="view_skin_type">{{$caseInfo->physical_examinations->skin_type??'N/A'}}</td>
        </tr>
        <tr>
            <td>Conjunctiva</td>
            <td id="view_conjuctiva_type">{{$caseInfo->physical_examinations->conjuctiva_type??'N/A'}}</td>
        </tr>
        <tr>
            <td>Neck</td>
            <td id="view_neck_type">{{$caseInfo->physical_examinations->neck_type??'N/A'}}</td>
        </tr>
        <tr>
            <td>Breast</td>
            <td id="view_breast_type">{{$caseInfo->physical_examinations->breast_type??'N/A'}}</td>
        </tr>
        <tr>
            <td>Abdomen</td>
            <td id="view_abdomen_type">{{$caseInfo->physical_examinations->abdomen_type??'N/A'}}</td>
        </tr>
        <tr>
            <td>Extremities</td>
            <td id="view_extremites_type">{{$caseInfo->physical_examinations->extremites_type??'N/A'}}</td>
        </tr>
        <tr>
            <td>EXTREMITIES (For IUD Acceptors)</td>
            <td id="view_extremites_UID_type">
                @if($caseInfo->physical_examinations->extremites_UID_type == 'uterine depth')
                {{$caseInfo->physical_examinations->extremites_UID_type??'N/A'}} / {{$caseInfo->physical_examinations->uterine_depth_text??'N/A'}}
                @else
                {{$caseInfo->physical_examinations->extremites_UID_type??'N/A'}} /
                {{$caseInfo->physical_examinations->cervical_abnormalities_type??$caseInfo->physical_examinations->cervical_consistency_type??''}}
                @endif
            </td>
        </tr>
    </tbody>
</table>


<!-- <table class="table table-bordered mb-0">
    <thead class="table-header">
        <tr>
            <th colspan="12">EXTREMITIES (For IUD Acceptors)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>General</td>
            <td>Normal / Mass / Abdominal Discharge</td>
        </tr>
        <tr>
            <td>Abnormalities</td>
            <td>Warts / Polyp or cyst / Inflammation or erosion / Bloody discharge</td>
        </tr>
        <tr>
            <td>Cervical consistency</td>
            <td>Firm / Soft</td>
        </tr>
        <tr>
            <td>Cervical tenderness</td>
            <td>Present / Not present</td>
        </tr>
        <tr>
            <td>Adnexal mass / tenderness</td>
            <td>Present / Not present</td>
        </tr>
        <tr>
            <td>Uterine position</td>
            <td>Mid / Anteflexed / Retroflexed</td>
        </tr>
        <tr>
            <td>Uterine depth</td>
            <td>__ cm</td>
        </tr>
    </tbody>
</table> -->

<!-- Acknowledgement Section -->
<div class="ackknowledgement ">
    <h4 class="border-bottom mt-5 seperator px-2">ACKNOWLEDGEMENT</h4>
    <p class="px-2">
        This is to certify that the Physician/Nurse/Midwife of the clinic has fully
        explained to me the different methods available in family planning, and I freely
        choose the <u class="px-3 text-underline" id="view_choosen_method">{{$caseInfo->date_of_choosen_method??'N/A'}}</u> method.
    </p>

    <div class="row mb-3 px-2">
        <div class="col-md-6 d-flex flex-column">
            <label class="fw-bold">Client's Signature:</label>
            <div class="border p-3 text-center" id="view_signature_image">[Signature Image Here]</div>
            <small class="text-muted text-center">Signature on record</small>
        </div>
        <div class="col-md-6 d-flex flex-column">
            <label class="fw-bold">Date:</label>
            <div class="border p-3 text-center" id="view_date_of_acknowledgement">{{$caseInfo->date_of_acknowledgement??''}} </div>
        </div>
    </div>

    <p class="text-center px-2">
        I hereby consent to the inclusion of my FP1 record in the Family Health Registry.
    </p>

    <div class="row mb-3 border-bottom px-2">
        <div class="col-md-6 d-flex flex-column">
            <label class="fw-bold">Nurse Signature:</label>
            <div class="border p-3 text-center" id="view_acknowledgement_consent_signature_image">[Nurse Signature Image Here]</div>
            <small class="text-muted text-center">Authorized personnel</small>
        </div>
        <div class="col-md-6 d-flex flex-column">
            <label class="fw-bold">Date:</label>
            <div class="border p-3 text-center" id="view_date_of_acknowledgement_consent">{{$caseInfo->date_of_acknowledgement_consent??''}}</div>
        </div>
    </div>
</div>