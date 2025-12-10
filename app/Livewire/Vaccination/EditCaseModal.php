<?php

namespace App\Livewire\Vaccination;

use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditCaseModal extends Component
{
    public $caseRecordId;
    // form variables
    public $case_record_id;
    public $update_handled_by;
    public $date_of_vaccination;
    public $time_of_vaccination;
    public $selected_vaccine;
    public $dose;
    public $remarks;
    public $medicalRecordCase;

    protected $listeners = ['caseId' => 'caseId'];

    public function caseId($id)
    {
        $this->caseRecordId = $id;
        // ...
    }


    public function render()
    {
        return view('livewire.vaccination.edit-case-modal');
    }
    public function updateCaseRecord()
    {

        try {

            $data = $this->validate([
                'update_handled_by' => 'required',
                'date_of_vaccination' => 'required',
                'time_of_vaccination' => 'sometimes',
                'selected_vaccine' => 'required',
                'case_record_id' => 'required',
                'dose' => 'required',
                'remarks' => 'sometimes'
            ]);

            // get the vaccine types
            $vaccines = explode(',', $this->selected_vaccine);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $key => $vaccineId) {
                $vaccineText = vaccines::find($vaccineId);

                $selectedVaccinesArray[] = $vaccineText->vaccine_acronym;
            }

            $selectedVaccines = implode(',', $selectedVaccinesArray);

            // handle the vaccination masterlist updates
            // 1. first lets get the case record
            $vaccinationCase = vaccination_case_records::findOrFail($this->caseRecordId);
            $vaccinationMasterlist = vaccination_masterlists::where('medical_record_case_id', $vaccinationCase->medical_record_case_id)->first();


            // this is for the update, check for exisiting vaccination, updates
            // handle if the selected vaccination are already existed

            // add condition for trying to add existing record
            $existingVaccinesAdministered = [];
            $vaccinationCaseRecord = vaccination_case_records::where('medical_record_case_id', $vaccinationCase->medical_record_case_id)->where('id', '!=', $vaccinationCase->id)->where('status', '!=', 'Archived')->get();

            foreach ($vaccinationCaseRecord as $record) {
                // explode the vaccination
                $administeredVaccines = explode(',', $record->vaccine_type);
                foreach ($administeredVaccines as $vaccine) {
                    $vaccineName = Str::upper($vaccine) . "_" . $record->dose_number;
                    $existingVaccinesAdministered[] = $vaccineName;
                }
            }

            // dd($existingVaccinesAdministered);

            // there's a white space on each element, so i trimmed it
            $trimmedExistingVaccineAdministed = array_map('trim', $existingVaccinesAdministered);


            // check if the vaccine is in the existing administered vaccine

            if ($existingVaccinesAdministered) {
                $existingVaccineError = [];
                // dd($selectedVaccinesArray);
                foreach ($selectedVaccinesArray as  $selectedVaccine) {
                    $vaccine =  Str::upper($selectedVaccine) . "_" . $data['dose'];

                    if (in_array($vaccine, $trimmedExistingVaccineAdministed)) {
                        $existingVaccineError[] = $vaccine;
                    }
                }
                if ($existingVaccineError) {
                    $converted = implode(",", $existingVaccineError);

                    return response()->json([
                        'errors' => "Unable to administer the vaccines. $converted already existed."
                    ], 422);
                }
            }

            // this handle the updates of masterlist

            $existingVaccine = explode(',', $vaccinationCase->vaccine_type);

            // dd($existingVaccine);
            foreach ($existingVaccine as $vaccine) {
                $vaccineText = $vaccine == 'Hepatitis B' ? $vaccine : Str::upper($vaccine);
                $itemColumn = $vaccineText == 'Hepatitis B' ? $vaccineText : $vaccineText . "_" . $vaccinationCase->dose_number;


                $vaccinationMasterlist->update([
                    $itemColumn => null
                ]);
            }
            // we empty the vaccination of this record as the logic of update, then later on we will update again with the value of the selected vaccines


            // delete the existing vaccine administed first, then create a new record of the vaccines
            $currentlyAdministedVaccine = vaccineAdministered::where('vaccination_case_record_id', $this->case_record_id)->delete();


            // this if for compiling the selected vaccines




            // GET THE MEDICAL RECORD CASE THAT WE WANT TO UPDATE
            $vaccination_case_record = vaccination_case_records::findOrFail($data['case_record_id']);
            // UPDATE THE DATA
            $vaccination_case_record->update([
                'health_worker_id' => $this->update_handled_by ?? $vaccination_case_record->health_worker_id,
                'date_of_vaccination' => $this->date_of_vaccination ?? $vaccination_case_record->date_of_vaccination,
                'time' => $this->time_of_vaccination ?? $vaccination_case_record->time,
                'vaccine_type' => $selectedVaccines ?? $vaccination_case_record->vaccine_type,
                'dose_number' => $this->dose ?? $vaccination_case_record->dose,
                'remarks' => $this->remarks ?? $vaccination_case_record->remarks
            ]);

            // UPLOAD THE NEW SET OF VACCINES
            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);

                $vaccineAdministered = vaccineAdministered::create([
                    'vaccination_case_record_id' => $this->case_record_id,
                    'vaccine_type' => $vaccine->type_of_vaccine,
                    'dose_number' => $this->dose ?? null,
                    'vaccine_id' => $vaccineId ?? null
                ]);
            }

            // update again the master list
            //  loop through
            $vaccinationMasterlist->refresh();
            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);
                $vaccineText = $vaccine->vaccine_acronym == 'Hepatitis B' ? $vaccine->vaccine_acronym : Str::upper($vaccine->vaccine_acronym);
                $itemColumn = $vaccineText == 'Hepatitis B' ? $vaccineText : $vaccineText . "_" . $this->dose;

                $vaccineTypes = ['BCG', 'Hepatitis B', 'PENTA_1', 'PENTA_2', 'PENTA_3', 'OPV_1', 'OPV_2', 'OPV_3', 'PCV_1', 'PCV_2', 'PCV_3', 'IPV_1', 'IPV_2', 'MCV_1', 'MCV_2'];
                if (in_array($itemColumn, $vaccineTypes)) {
                    $vaccinationMasterlist->update([
                        "$itemColumn" => $this->date_of_vaccination
                    ]);
                }
            }
            // end of updating

            // return response()->json([
            //     'message' => 'updating information successfully'
            // ]);
            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Updated!',
                'text' => 'Record has been updated successfully.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ]);
        }
    }
  
}
