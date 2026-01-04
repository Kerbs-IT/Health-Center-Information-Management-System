<!-- view pregnancy plan -->
<div class="modal fade" id="viewPregnancyPlanRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Medical Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>

            <div class="modal-body">
                @include('records.prenatal.viewComponent.viewPregnancyPlan')
            </div>
        </div>
    </div>
</div>