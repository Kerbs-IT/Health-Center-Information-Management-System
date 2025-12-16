<table class="table table-bordered table-hover mb-0">
    <thead class="seperator">
        <tr>
            <th colspan="12" class="text-start " scope=" col">I. Medical History Record</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Severe headaches/migraine</td>
            <td class="text-center" id="view_severe_headaches_migraine">{{$caseInfo->medical_history->severe_headaches_migraine??''}}</td>
        </tr>
        <tr>
            <td>History of stroke / heart attack / hypertension</td>
            <td class="text-center" id="view_history_of_stroke">{{$caseInfo->medical_history->history_of_stroke??''}}</td>
        </tr>
        <tr>
            <td>Non-traumatic hematoma / frequent bruising or gum bleeding</td>
            <td class="text-center" id="view_non_traumatic_hemtoma">{{$caseInfo->medical_history->non_traumatic_hemtoma??''}}</td>
        </tr>
        <tr>
            <td>Current or history of breast cancer / breast mass</td>
            <td class="text-center" id="view_history_of_breast_cancer">{{$caseInfo->medical_history->history_of_breast_cancer??''}}</td>
        </tr>
        <tr>
            <td>Severe chest pain</td>
            <td class="text-center" id="view_severe_chest_pain">{{$caseInfo->medical_history->severe_chest_pain??''}}</td>
        </tr>
        <tr>
            <td>Cough for more than 14 days</td>
            <td class="text-center" id="view_cough">{{$caseInfo->medical_history->cough??''}}</td>
        </tr>
        <tr>
            <td>Jaundice</td>
            <td class="text-center" id="view_jaundice">{{$caseInfo->medical_history->jaundice??''}}</td>
        </tr>
        <tr>
            <td>Unexplained vaginal bleeding</td>
            <td class="text-center" id="view_unexplained_vaginal_bleeding">{{$caseInfo->medical_history->unexplained_vaginal_bleeding??''}}</td>
        </tr>
        <tr>
            <td>Abnormal vaginal discharge</td>
            <td class="text-center" id="view_abnormal_vaginal_discharge">{{$caseInfo->medical_history->abnormal_vaginal_discharge??''}}</td>
        </tr>
        <tr>
            <td>Intake of phenobarbital or rifampicin</td>
            <td class="text-center" id="view_abnormal_phenobarbital">{{$caseInfo->medical_history->abnormal_phenobarbital??''}}</td>
        </tr>
        <tr>
            <td>Is the client a smoker?</td>
            <td class="text-center" id="view_smoker">{{$caseInfo->medical_history->smoker??''}}</td>
        </tr>
        <tr>
            <td>With disability?</td>
            <td class="text-center" id="view_with_dissability">{{$caseInfo->medical_history->with_dissability??''}} / {{$caseInfo->medical_history->if_with_dissability_specification??''}}</td>
        </tr>
    </tbody>
</table>