<table class="table table-bordered table-striped mb-0">
    <thead class="seperator">
        <tr>
            <th colspan="12" class="text-start">III. RISK FOR SEXUALLY TRANSMITTED INFECTIONS</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Abnormal discharge from the genital area</td>
            <td id="view_infection_abnormal_discharge_from_genital_area">{{$caseInfo->risk_for_sexually_transmitted_infection->infection_abnormal_discharge_from_genital_area??'N/A'}}/{{$caseInfo->risk_for_sexually_transmitted_infection->origin_of_abnormal_discharge??'N/A'}}</td>
        </tr>
        <tr>
            <td>Scores or ulcers in the genital area</td>
            <td id="view_scores_or_ulcer">{{$caseInfo->risk_for_sexually_transmitted_infection->score_or_ulcer??'N/A'}}</td>
        </tr>
        <tr>
            <td>pain or burning sensation in the genital area</td>
            <td id="view_pain_or_burning_sensation">{{$caseInfo->risk_for_sexually_transmitted_infection->pain_or_burning_sensation??'N/A'}}</td>
        </tr>
        <tr>
            <td>History of treatment for sexually transmitted infection</td>
            <td id="view_history_of_sexually_transmitted_infection">{{$caseInfo->risk_for_sexually_transmitted_infection->history_of_sexually_transmitted_infection??'N/A'}}</td>
        </tr>
        <tr>
            <td>HIV/AIDS/Pelvic inflamatory disease</td>
            <td id="view_sexually_transmitted_disease">{{$caseInfo->risk_for_sexually_transmitted_infection->sexually_transmitted_disease??'N/A'}}</td>
        </tr>
    </tbody>
</table>