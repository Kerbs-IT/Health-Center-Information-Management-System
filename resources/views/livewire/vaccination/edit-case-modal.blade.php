 <form method="POST" action="#" class="flex-column" id="edit-vaccination-case-form">
     @method('PUT')
     @csrf
     <div class="modal-header">
         <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
     </div>

     <div class="modal-body">
         <div class="inner w-100 rounded">

             <div class="mb-2 w-100">
                 <label for="patient_name">Patient Name</label>
                 <input type="text" class="form-control bg-light" disabled placeholder="Jan Louie Salimbago" id="edit-patient-name">
             </div>

             <div class="mb-2 w-100">
                 <label for="administered_by">Administered By</label>
                 <input type="text" class="form-control bg-light" disabled placeholder="Nurse" value="Nurse Joy">
             </div>
             @if(Auth::user()-> role == 'nurse')
             <div class="mb-2 w-100">
                 <label for="update_handled_by" class="w-100 form-label">Handled By:</label>
                 <select name="update_handled_by" id="update_handled_by" class="form-select w-100">
                     <option value="" selected disabled>Select the Health Worker</option>
                 </select>
                 <small class="text-danger error-text" id="update_handled_by_error"></small>
             </div>
             @elseif(Auth::user()-> role == 'staff')
             <div class="mb-2 w-100">
                 <label for="administered_by">Handled By:</label>
                 <input type="text" class="form-control bg-light" disabled placeholder="Nurse" value="Nurse Joy">
             </div>
             @endif

             <div class="mb-2 w-100">
                 <label for="date_of_vaccination">Date of Vaccination</label>
                 <input type="date" id="edit_date_of_vaccination" class="form-control" name="date_of_vaccination">
                 <small class="text-danger error-text" id="date_of_vaccination_error"></small>
             </div>

             <div class="mb-2 w-100">
                 <label for="time_of_vaccination">Time</label>
                 <input type="time" class="form-control" name="time_of_vaccination" id="edit-time-of-vaccination">
                 <small class="text-danger error-text" id="time_of_vaccination_error"></small>
             </div>
             <!-- Hidden data -->
             <div class="vaccine-administered" hidden id="vaccine-administered"></div>

             <div class="mb-2">
                 <label for="vaccine_type">Vaccine Type:</label>
                 <div class="d-flex gap-2">
                     <select name="vaccine_type" id="update_vaccine_type" class="form-select w-100">
                         <option value="">Select Vaccine</option>
                     </select>
                     <button type="button" class="btn btn-success" id="update-add-vaccine-btn">Add</button>
                 </div>
                 <small class="text-danger error-text" id="vaccine_type_error"></small>
             </div>
             <!-- container of the vaccines -->
             <div class="mb-2 bg-secondary p-3 d-flex flex-wrap rounded gap-2 update-vaccine-container justify-content-center">


             </div>
             <!-- hidden inputs -->
             <input type="text" name="selected_vaccine" id="update_selected_vaccine" hidden>
             <input type="number" name="case_record_id" id="case_record_id" hidden>

             <div class="mb-2 w-100">
                 <label for="dose">Vaccine Dose Number:</label>
                 <select id="edit-dose" name="dose" required class="form-select">
                     <option value="" disabled>Select Dose</option>
                     <option value="1" selected>1st Dose</option>
                     <option value="2">2nd Dose</option>
                     <option value="3">3rd Dose</option>
                 </select>
                 <small class="text-danger error-text" id="dose_error"></small>
             </div>

             <div class="mb-2 w-100">
                 <label for="remarks">Remarks*</label>
                 <input type="text" class="form-control" id="edit-remarks" name="remarks">
                 <small class="text-danger error-text" id="remarks_error"></small>
             </div>
         </div>
     </div>

     <div class="modal-footer d-flex justify-content-between">
         <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
         <button type="submit" class="btn btn-success" id="update-save-btn">Save Record</button>
     </div>
 </form>