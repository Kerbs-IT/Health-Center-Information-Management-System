<!-- STEP 4 -->
<table style="width: 100%; border-collapse: collapse; font-size: 10px;">
    <thead>
        <tr style="background-color: #e9ecef; border-bottom: 2px solid #000;">
            <th colspan="2" style="border: 1px solid #000; padding: 10px 10px; text-align: left; font-weight: bold;">III. RISK FOR SEXUALLY TRANSMITTED INFECTIONS</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px; width: 70%;">Abnormal discharge from the genital area</td>
            <td style="border: 1px solid #000; padding: 5px 5px; width: 30%;" id="view_infection_abnormal_discharge_from_genital_area">{{$caseInfo->risk_for_sexually_transmitted_infection->infection_abnormal_discharge_from_genital_area??'N/A'}}/{{$caseInfo->risk_for_sexually_transmitted_infection->origin_of_abnormal_discharge??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">Scores or ulcers in the genital area</td>
            <td style="border: 1px solid #000; padding: 5px 5px;" id="view_scores_or_ulcer">{{$caseInfo->risk_for_sexually_transmitted_infection->score_or_ulcer??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">pain or burning sensation in the genital area</td>
            <td style="border: 1px solid #000; padding: 5px 5px;" id="view_pain_or_burning_sensation">{{$caseInfo->risk_for_sexually_transmitted_infection->pain_or_burning_sensation??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">History of treatment for sexually transmitted infection</td>
            <td style="border: 1px solid #000; padding: 5px 5px;" id="view_history_of_sexually_transmitted_infection">{{$caseInfo->risk_for_sexually_transmitted_infection->history_of_sexually_transmitted_infection??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 5px;">HIV/AIDS/Pelvic inflamatory disease</td>
            <td style="border: 1px solid #000; padding: 5px 5px;" id="view_sexually_transmitted_disease">{{$caseInfo->risk_for_sexually_transmitted_infection->sexually_transmitted_disease??'N/A'}}</td>
        </tr>
    </tbody>
</table>