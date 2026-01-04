<!-- prengancy checkup -->
<div class="modal fade" id="pregnancyCheckUpModal" tabindex="-1" aria-labelledby="pregnancyCheckUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Check-Up Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- use include to lessen the code lines -->
            <div class="modal-body">
                @include('records.prenatal.viewComponent.viewPregnancyCheckup')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>