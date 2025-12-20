<div class="modal-content">
    <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="vaccinationModalLabel">Tuberculosis Record Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
    </div>
    <div class="modal-body">
        <div class="tb-dots card shadow p-md-4 p-2 w-100">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Patient Name</th>
                            <td id="view_patient_name"></td>
                        </tr>
                        <tr>
                            <th>Type of Tuberculosis (TB)</th>
                            <td id="view_type_of_tuberculosis"></td>
                        </tr>
                        <tr>
                            <th>Type of TB Case</th>
                            <td id="view_type_of_tb_case"></td>
                        </tr>
                        <tr>
                            <th>Date of Diagnosis</th>
                            <td id="view_date_of_diagnosis"></td>
                        </tr>
                        <tr>
                            <th>Name of Physician</th>
                            <td id="view_name_of_physician">Dr. Maria Santos</td>
                        </tr>
                        <tr>
                            <th>Sputum Test Results</th>
                            <td id="view_sputum_test_results"></td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <h4 class="border-bottom mt-md-4 mt-2">Medication List</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="table-header">
                            <th>Medicine Name</th>
                            <th>Dosage & Frequency</th>
                            <th>Quality</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody id="view-table-body">
                        <!-- <tr>
                            <td>Isoniazid</td>
                            <td>1 tablet/day</td>
                            <td>6 months</td>
                            <td>2025-01-01</td>
                            <td>2025-07-01</td>
                        </tr> -->
                    </tbody>
                </table>
           </div>
           <div class="table-responsive">
                <table class="table table-bordered mt-md-4 mt-2">
                    <tbody>
                        <tr>
                            <th>Treatment Category</th>
                            <td id="view_treatment_category">First-line Treatment</td>
                        </tr>
                        <tr>
                            <th>Assigned Health Worker</th>
                            <td id="view_assigned_health_worker">Nurse Joy</td>
                        </tr>
                    </tbody>
                </table>
           </div>
            <h4 class="border-bottom mt-md-4 mt-2">Monitoring & Progress</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Date of Diagnosis</th>
                            <td id="view_date_administered">2024-06-01</td>
                        </tr>
                        <tr>
                            <th>Side Effects</th>
                            <td id="view_side_effect">Nausea</td>
                        </tr>
                        <tr>
                            <th>Remarks</th>
                            <td id="view_remarks">Patient is responding well to treatment.</td>
                        </tr>
                        <tr>
                            <th>Outcome</th>
                            <td id="view_outcome">On-going Treatment</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>