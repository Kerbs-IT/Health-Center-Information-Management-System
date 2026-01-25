<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="vaccinationModalLabel">Senior Citizen Medicine Maintenance Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <div class="d-flex p-md-4 p-2 shadow-lg flex-column">
            <div class="contents ">
                <div class="table-responsive">
                    <table class="table table-bordered  table-light">
                        <tbody>
                            <!-- Full Name Row -->
                            <tr>
                                <td colspan="2" class="w-25 fw-semibold">Full Name:</td>
                                <td colspan="5" class="w-75 bg-white" id="view_patient_name">Jan Louie Salimbago</td>
                            </tr>

                            <!-- Section Header -->
                            <tr>
                                <td colspan="7" class=" text-uppercase fw-bold ">
                                    Medical Information
                                </td>
                            </tr>

                            <!-- Existing Medical Condition -->
                            <tr>
                                <td colspan="2" class="w-25 fw-semibold">Existing Medical Condition:</td>
                                <td colspan="5" class="w-75 bg-white" id="view_existing_medical_condition">None</td>
                            </tr>

                            <!-- Allergies -->
                            <tr>
                                <td colspan="2" class="w-25 fw-semibold">Allergies:</td>
                                <td colspan="5" class="w-75 bg-white" id="view_alergies">None</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <!-- table -->
                <div class="table-responsive">
                    <table class="w-100 table">
                        <thead>
                            <tr class="table-header">
                                <th>Maintenance Medication</th>
                                <th>Dosage & Frequency</th>
                                <th>Duration</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody id="viewCaseBody">
                            <!-- <tr>
                                <td>Amlodipine 5mg</td>
                                <td>1x/day</td>
                                <td>90 days</td>
                                <td>2025-01-01</td>
                                <td>2025-02-01</td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
                <!-- nurse -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2" class="w-25 fw-semibold bg-light">Prescribe by:</td>
                            <td colspan="5" class="w-75 bg-white" id="view_prescribe_by_nurse">Nurse Joy</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bg-light">Remarks</td>
                            <td colspan="5" id="view_remarks">none</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bg-light">Date of Comeback</td>
                            <td colspan="5" id="view_date_of_comeback">none</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>