<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="vaccinationModalLabel">Client Record Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        @include('records.familyPlanning.viewCaseComponent.step1')
        <!-- step 2 -->
        @include('records.familyPlanning.viewCaseComponent.step2')
        <!-- step 3 -->
        @include('records.familyPlanning.viewCaseComponent.step3')
        <!-- step 4 -->
        @include('records.familyPlanning.viewCaseComponent.step4')
        <!-- step 5 -->
        @include('records.familyPlanning.viewCaseComponent.step5')
    </div>
</div>