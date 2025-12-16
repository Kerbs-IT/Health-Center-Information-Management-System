<table class="table table-bordered table-striped mb-0">
    <thead class="seperator">
        <tr>
            <th colspan="12" class="text-start">II.Obstetrical History Record</th>
        </tr>
    </thead>
    <tbody class="">
        <tr>
            <td colspan="12" class="p-0">
                <div style="display: flex; text-align: center; margin: 0;">
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong>Gravida (G):</strong>
                    </div>
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong id="view_G">{{$caseInfo->obsterical_history->G??'N/A'}}</strong>
                    </div>
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong>Para (P):</strong>
                    </div>
                    <div style="flex: 1; padding: 4px;">
                        <strong id="view_P">{{$caseInfo->obsterical_history->P??'N/A'}}</strong>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12" class="p-0">
                <div style="display: flex; text-align: center; margin: 0;">
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong>Term:</strong>
                    </div>
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong id="view_full_term">{{$caseInfo->obsterical_history->full_term??'N/A'}}</strong>
                    </div>
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong>Abortion:</strong>
                    </div>
                    <div style="flex: 1; padding: 4px;">
                        <strong id="view_abortion">{{$caseInfo->obsterical_history->abortion??'N/A'}}</strong>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12" class="p-0">
                <div style="display: flex; text-align: center; margin: 0;">
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong>Premature:</strong>
                    </div>
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong id="view_premature">{{$caseInfo->obsterical_history->premature??'N/A'}}</strong>
                    </div>
                    <div style="flex: 1; border-right: 1px solid #dee2e6; padding: 4px;">
                        <strong>Living Children:</strong>
                    </div>
                    <div style="flex: 1; padding: 4px;">
                        <strong id="view_living_children">{{$caseInfo->obsterical_history->living_children??'N/A'}}</strong>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>Date of Last Delivery</td>
            <td id="view_date_of_last_delivery">{{$caseInfo->obsterical_history->date_of_last_delivery??'N/A'}}</td>
        </tr>
        <tr>
            <td>Type of Last Delivery</td>
            <td id="view_type_of_last_delivery">{{$caseInfo->obsterical_history->type_of_last_delivery??'N/A'}}</td>
        </tr>
        <tr>
            <td>Last Menstrual Period</td>
            <td id="view_date_of_last_delivery_menstrual_period">{{$caseInfo->obsterical_history->date_of_last_delivery_menstrual_period??'N/A'}}</td>
        </tr>
        <tr>
            <td>Previous Menstrual Period</td>
            <td id="view_date_of_previous_delivery_menstrual_period">{{$caseInfo->obsterical_history->date_of_previous_delivery_menstrual_period??'N/A'}}</td>
        </tr>
        <tr>
            <td>Menstrual Flow</td>
            <td id="view_type_of_menstrual">{{$caseInfo->obsterical_history->type_of_menstrual??'N/A'}}</td>
        </tr>
        <tr>
            <td>Dysmenorrhea</td>
            <td id="view_Dysmenorrhea">{{$caseInfo->obsterical_history->Dysmenorrhea??'N/A'}}</td>
        </tr>
        <tr>
            <td>Hydatidiform Mole (last 12 months)</td>
            <td id="view_hydatidiform_mole">{{$caseInfo->obsterical_history->hydatidiform_mole??'N/A'}}</td>
        </tr>
        <tr>
            <td>History of Ectopic Pregnancy</td>
            <td id="view_ectopic_pregnancy">{{$caseInfo->obsterical_history->ectopic_pregnancy??'N/A'}}</td>
        </tr>
    </tbody>
</table>