<!-- step 3 -->
<table style="width: 100%; border-collapse: collapse; font-size: 10px;">
    <tr style="background-color: #e9ecef; border-bottom: 2px solid #000;">
        <th colspan="4" style="border: 1px solid #000; padding: 10px 10px; text-align: left; font-weight: bold;">II. Obstetrical History Record</th>
    </tr>
    <!-- 2-Column Section -->
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%; font-weight: bold;">Gravida (G):</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%;" id="view_G">{{$caseInfo->obsterical_history->G??'N/A'}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%; font-weight: bold;">Para (P):</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%;" id="view_P">{{$caseInfo->obsterical_history->P??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%; font-weight: bold;">Term:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%;" id="view_full_term">{{$caseInfo->obsterical_history->full_term??'N/A'}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%; font-weight: bold;">Abortion:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%;" id="view_abortion">{{$caseInfo->obsterical_history->abortion??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%; font-weight: bold;">Premature:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%;" id="view_premature">{{$caseInfo->obsterical_history->premature??'N/A'}}</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%; font-weight: bold;">Living Children:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 25%;" id="view_living_children">{{$caseInfo->obsterical_history->living_children??'N/A'}}</td>
    </tr>
    <!-- Single Column Section -->
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Date of Last Delivery:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_date_of_last_delivery">{{$caseInfo->obsterical_history->date_of_last_delivery??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Type of Last Delivery:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_type_of_last_delivery">{{$caseInfo->obsterical_history->type_of_last_delivery??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Last Menstrual Period:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_date_of_last_delivery_menstrual_period">{{$caseInfo->obsterical_history->date_of_last_delivery_menstrual_period??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Previous Menstrual Period:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_date_of_previous_delivery_menstrual_period">{{$caseInfo->obsterical_history->date_of_previous_delivery_menstrual_period??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Menstrual Flow:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_type_of_menstrual">{{$caseInfo->obsterical_history->type_of_menstrual??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Dysmenorrhea:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_Dysmenorrhea">{{$caseInfo->obsterical_history->Dysmenorrhea??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">Hydatidiform Mole (last 12 months):</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_hydatidiform_mole">{{$caseInfo->obsterical_history->hydatidiform_mole??'N/A'}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%; font-weight: bold;" colspan="2">History of Ectopic Pregnancy:</td>
        <td style="border: 1px solid #000; padding: 5px 5px; width: 50%;" colspan="2" id="view_ectopic_pregnancy">{{$caseInfo->obsterical_history->ectopic_pregnancy??'N/A'}}</td>
    </tr>
</table>
