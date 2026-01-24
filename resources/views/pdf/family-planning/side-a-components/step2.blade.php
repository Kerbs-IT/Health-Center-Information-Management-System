<!-- step 2 -->
<table style="width: 100%; border-collapse: collapse; font-size: 10px;">
    <thead>
        <tr style="background-color: #e9ecef; border-bottom: 2px solid #000;">
            <th colspan="2" style="border: 1px solid #000; padding: 10px 10px; text-align: left; font-weight: bold;">I. Medical History Record</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px; width: 85%;">Severe headaches/migraine</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center; width: 15%;" id="view_severe_headaches_migraine">{{$caseInfo->medical_history->severe_headaches_migraine??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">History of stroke / heart attack / hypertension</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_history_of_stroke">{{$caseInfo->medical_history->history_of_stroke??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Non-traumatic hematoma / frequent bruising or gum bleeding</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_non_traumatic_hemtoma">{{$caseInfo->medical_history->non_traumatic_hemtoma??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Current or history of breast cancer / breast mass</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_history_of_breast_cancer">{{$caseInfo->medical_history->history_of_breast_cancer??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Severe chest pain</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_severe_chest_pain">{{$caseInfo->medical_history->severe_chest_pain??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Cough for more than 14 days</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_cough">{{$caseInfo->medical_history->cough??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Jaundice</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_jaundice">{{$caseInfo->medical_history->jaundice??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Unexplained vaginal bleeding</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_unexplained_vaginal_bleeding">{{$caseInfo->medical_history->unexplained_vaginal_bleeding??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Abnormal vaginal discharge</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_abnormal_vaginal_discharge">{{$caseInfo->medical_history->abnormal_vaginal_discharge??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Intake of phenobarbital or rifampicin</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_abnormal_phenobarbital">{{$caseInfo->medical_history->abnormal_phenobarbital??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Is the client a smoker?</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_smoker">{{$caseInfo->medical_history->smoker??''}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">With disability?</td>
            <td style="border: 1px solid #000; padding: 5px 5px; text-align: center;" id="view_with_dissability">{{$caseInfo->medical_history->with_dissability??''}} / {{$caseInfo->medical_history->if_with_dissability_specification??''}}</td>
        </tr>
    </tbody>
</table>