<!-- step 5 -->
<!-- step 5 -->
<table style="width: 100%; border-collapse: collapse; font-size: 10px;">
    <thead>
        <tr style="background-color: #e9ecef; border-bottom: 2px solid #000;">
            <th colspan="2" style="border: 1px solid #000; padding: 10px 10px; text-align: left; font-weight: bold;">IV. RISKS FOR VIOLENCE AGAINST WOMEN (VAW)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 2px; width: 70%;">History of domestic violence of VAW</td>
            <td style="border: 1px solid #000; padding: 5px 2px; width: 30%;" id="view_history_of_domestic_violence_of_VAW">{{$caseInfo->risk_for_sexually_transmitted_infection->history_of_domestic_violence_of_VAW??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 2px;">Unpleasant relationship with partner</td>
            <td style="border: 1px solid #000; padding: 5px 2px;" id="view_unpleasant_relationship_with_partner">{{$caseInfo->risk_for_sexually_transmitted_infection->unpleasant_relationship_with_partner??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 2px;">Partner does not approve of the visit to FP clinic</td>
            <td style="border: 1px solid #000; padding: 5px 2px;" id="view_partner_does_not_approve">{{$caseInfo->risk_for_sexually_transmitted_infection->partner_does_not_approve??'N/A'}}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px 2px;">Referred to</td>
            <td style="border: 1px solid #000; padding: 5px 2px;" id="view_referred_to">{{$caseInfo->risk_for_sexually_transmitted_infection->referred_to??'N/A'}}</td>
        </tr>
    </tbody>
</table>