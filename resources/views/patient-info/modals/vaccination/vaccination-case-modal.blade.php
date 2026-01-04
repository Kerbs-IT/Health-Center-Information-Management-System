 <div class="modal fade" id="viewVaccinationRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header bg-success text-white">
                 <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                 <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
             </div>

             <div class="modal-body">
                 @include('records.vaccination.viewComponent.viewCase')
             </div>
         </div>
     </div>
 </div>