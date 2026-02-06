<?php

namespace App\Http\Controllers;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\pregnancy_plans;
use App\Models\prenatal_case_records;
use App\Models\senior_citizen_case_records;
use App\Models\staff;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use App\Models\wra_masterlists;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Type\Integer;

class RecordsController extends Controller
{
    public function allRecords()
    {
        return view('records.allRecords.allRecord', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecordPatientDetails()
    {
        return view('records.allRecords.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecord_editPatientDetails()
    {
        return view('records.allRecords.editPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecordsCase()
    {
        return view('records.allRecords.patientCase', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewVaccinationRecord()
    {
        return view('records.allRecords.viewVaccinationRecord', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function updateVacciationCaseRecord(Request $request, $id)
    {

        try {

            $data = $request->validate([
                'update_handled_by' => 'required',
                'date_of_vaccination' => 'required',
                'time_of_vaccination' => 'sometimes',
                'selected_vaccine' => 'required',
                'case_record_id' => 'required',
                'dose' => 'required',
                'remarks' => 'sometimes',
                'height' => [
                    'nullable',
                    'numeric',
                    'between:30,250'      // cm
                ],

                'weight' => [
                    'nullable',
                    'numeric',
                    'between:1,300'       // kg
                ],

                'temperature' => [
                    'nullable',
                    'numeric',
                    'between:35,42'       // °C
                ],

                'date_of_comeback' => 'required|date'
            ]);

            // get the vaccine types
            $vaccines = explode(',', $data['selected_vaccine']);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $key => $vaccineId) {
                $vaccineText = vaccines::find($vaccineId);

                $selectedVaccinesArray[] = $vaccineText->vaccine_acronym;
            }

            $selectedVaccines = implode(',', $selectedVaccinesArray);

            // handle the vaccination masterlist updates
            // 1. first lets get the case record
            $vaccinationCase = vaccination_case_records::findOrFail($data['case_record_id']);
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

                if ($vaccinationMasterlist) {
                    $vaccinationMasterlist->update([
                        $itemColumn => null
                    ]);
                }
            }
            // we empty the vaccination of this record as the logic of update, then later on we will update again with the value of the selected vaccines


            // delete the existing vaccine administed first, then create a new record of the vaccines
            $currentlyAdministedVaccine = vaccineAdministered::where('vaccination_case_record_id', $data['case_record_id'])->delete();


            // this if for compiling the selected vaccines




            // GET THE MEDICAL RECORD CASE THAT WE WANT TO UPDATE
            $vaccination_case_record = vaccination_case_records::findOrFail($data['case_record_id']);
            // UPDATE THE DATA
            $vaccination_case_record->update([
                'health_worker_id' => $data['update_handled_by'] ?? $vaccination_case_record->health_worker_id,
                'date_of_vaccination' => $data['date_of_vaccination'] ?? $vaccination_case_record->date_of_vaccination,
                'time' => $data['time_of_vaccination'] ?? $vaccination_case_record->time,
                'vaccine_type' => $selectedVaccines ?? $vaccination_case_record->vaccine_type,
                'dose_number' => $data['dose'] ?? $vaccination_case_record->dose,
                'remarks' => $data['remarks'] ?? $vaccination_case_record->remarks,
                'height' => $data['height'] ?? $vaccination_case_record->height,
                'weight' => $data['weight'] ?? $vaccination_case_record->weight,
                'temperature' => $data['temperature'] ?? $vaccination_case_record->temperature,
                'date_of_comeback' => $data['date_of_comeback'] ?? $vaccination_case_record->date_of_comeback,
                'vaccination_status' => 'completed'
            ]);

            // UPLOAD THE NEW SET OF VACCINES
            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);

                $vaccineAdministered = vaccineAdministered::create([
                    'vaccination_case_record_id' => $data['case_record_id'],
                    'vaccine_type' => $vaccine->type_of_vaccine,
                    'dose_number' => $data['dose'] ?? null,
                    'vaccine_id' => $vaccineId ?? null
                ]);
            }

            // update again the master list
            //  loop through
            if ($vaccinationMasterlist) {
                $vaccinationMasterlist->refresh();
            }

            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);
                $vaccineText = $vaccine->vaccine_acronym == 'Hepatitis B' ? $vaccine->vaccine_acronym : Str::upper($vaccine->vaccine_acronym);
                $itemColumn = $vaccineText == 'Hepatitis B' ? $vaccineText : $vaccineText . "_" . $data['dose'];

                $vaccineTypes = ['BCG', 'Hepatitis B', 'PENTA_1', 'PENTA_2', 'PENTA_3', 'OPV_1', 'OPV_2', 'OPV_3', 'PCV_1', 'PCV_2', 'PCV_3', 'IPV_1', 'IPV_2', 'MCV_1', 'MCV_2'];
                if (in_array($itemColumn, $vaccineTypes)) {
                    if ($vaccinationMasterlist) {
                        $vaccinationMasterlist->update([
                            "$itemColumn" => $data['date_of_vaccination']
                        ]);
                    }
                }
            }
            // end of updating

            return response()->json([
                'message' => 'updating information successfully'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 402);
        }
    }

    // vaccination
    public function vaccinationRecord()
    {
        $vaccinationRecord = medical_record_cases::with('patient')->where('type_of_case', 'vaccination')->get();
        return view('records.vaccination.vaccination', ['isActive' => true, 'page' => 'RECORD', 'vaccinationRecord' => $vaccinationRecord]);
    }
    public function viewDetails($id)
    {
        $info = patients::with('medical_record_case.vaccination_medical_record')->findOrFail($id);
        $address = patient_addresses::where('patient_id', $id)->firstorFail();
        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
        return view('records.vaccination.patientDetails', ['isActive' => true, 'page' => 'RECORD', 'info' => $info, 'fullAddress' => $fullAddress, 'address' => $address]);
    }
    public function vaccinationEditDetails($id)
    {
        $info = patients::with('medical_record_case.vaccination_medical_record')->findOrFail($id);
        $address = patient_addresses::where('patient_id', $id)->firstorFail();
        $street = $address->house_number . ($address->street ? ', ' . $address->street : '');
        return view('records.vaccination.editPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'info' => $info, 'address' => $address, 'street' => $street]);
    }
    public function vaccinationUpdateDetails(Request $request, $id)
    {
        try {
            $patient = patients::findorFail($id);
            $medical_record_case = medical_record_cases::with('patient')->where('patient_id', $id)->firstOrFail();
            $vaccination_medical_record = vaccination_medical_records::where('medical_record_case_id', $medical_record_case->id);
            $patient_address = patient_addresses::where('patient_id', $id)->firstOrFail();
            // update the full name of vaccination case record
            $vaccination_case_record = vaccination_case_records::where('medical_record_case_id', $medical_record_case->id)->get();

            // get the masterlist record of the patient
            $vaccinationMasterlist = vaccination_masterlists::where('patient_id', $id)->first();

            if (!$vaccinationMasterlist) return;

            $data = $request->validate([
                'first_name' => [
                    'required',
                    'string',
                    Rule::unique('patients')
                        ->ignore($medical_record_case->patient->id) // <-- IMPORTANT
                        ->where(function ($query) use ($request) {
                            return $query->where('first_name', $request->first_name)
                                ->where('last_name', $request->last_name);
                        }),
                ],
                'last_name' => 'sometimes|nullable|string',
                'middle_initial' => 'sometimes|nullable|string',
                'date_of_birth' => 'required|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric',
                'sex' => 'required|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'mother_name' => 'sometimes|nullable|string',
                'father_name' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'vaccination_height' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'vaccination_weight' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'suffix' => 'sometimes|nullable|string'
            ]);

            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleName = $data['middle_initial'] ? ucwords(strtolower($data['middle_initial'])) : '';
            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name']),
                $data['suffix'] ?? null,
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));
            $age = Carbon::parse($data['date_of_birth'])->age;
            $ageInMonth = $this->calculateAgeInMonths($data['date_of_birth']);
            $sex = $data['sex'] ?? '';
            $patient->update([
                'first_name' => ucfirst(strtolower($data['first_name'])) ?? ucfirst($patient->first_name),
                'last_name' =>  ucfirst(strtolower($data['last_name'])) ?? ucfirst($patient->last_name),
                'middle_initial' => $middleName,
                'full_name' => ucwords(strtolower($fullName)),
                'date_of_birth' => $data['date_of_birth'] ?? $patient->date_of_birth,
                'place_of_birth' => $data['place_of_birth'] ?? '',
                'age' => $age ?? $patient->age,
                'sex' => $sex?? ucfirst($sex) ?? '',
                'age_in_months' => $ageInMonth ?? 0,
                'contact_number' => $data['contact_number'] ?? '',
                'nationality' => $data['nationality'] ?? '',
                'suffix' => $data['suffix'] ?? ''

            ]);
            // update each record associate to patient vaccination case the vaccination case record
            foreach ($vaccination_case_record as $record) {
                $record->update([
                    'patient_name' => trim(
                        $fullName ?
                            ucwords(strtolower($fullName)) : ''
                    )
                ]);
            }

            $vaccination_medical_record->update([
                'date_of_registration' => $data['date_of_registration'] ?? $medical_record_case->date_of_registration,
                'mother_name' => $data['mother_name'] ? ucwords($data['mother_name']) : '',
                'father_name' => $data['father_name'] ? ucwords($data['father_name']) : '',
                'birth_height' => $data['vaccination_height'] ?? null,
                'birth_weight' => $data['vaccination_weight'] ?? null,
            ]);
            $blk_n_street = explode(',', $data['street'], 2); // Limit to 2 parts

            $patient_address->update([
                'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                'street' => isset($blk_n_street[1]) ? trim($blk_n_street[1]) : null,
                'purok' => $data['brgy'] ?? $patient_address->purok
            ]);
            // refresh the record
            $patient_address->refresh();

            $newAddress = implode(',', array_filter([
                $patient_address->house_number,
                $patient_address->street,
                $patient_address->purok,
                $patient_address->barangay,
                $patient_address->city,
                $patient_address->province
            ]));
            // update the masterlist 
            if ($vaccinationMasterlist) {
                $vaccinationMasterlist->update([
                    'name_of_child' =>  $fullName ? ucwords(strtolower($fullName)) : ucwords($vaccinationMasterlist->name_of_child),
                    'sex' => $data['sex'] ? ucfirst($data['sex']) : null,
                    'age' => $data['age'] ?? $vaccinationMasterlist->age,
                    'date_of_birth' => $data['date_of_birth'] ?? $vaccinationMasterlist->date_of_birth,
                    'Address' => $newAddress ?? ''
                ]);
            }



            return response()->json([
                'message' => 'Patient information is successfully updated'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Patient information is not successfully updated',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Patient information is not successfully updated',
                'errors' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()  // Full stack trace
            ], 422);
        }
    }
    public function vaccinationCase($id)
    {
        $healthWorkerName = '';
        if (Auth::user()->role == 'staff') {
            $staffInfo = staff::where("user_id", Auth::user()->id)->first();
            $healthWorkerName = $staffInfo->full_name;
        }
        $medical_record_case = medical_record_cases::with(['patient', 'vaccination_medical_record'])->findOrFail($id);
        $vaccination_case_record = vaccination_case_records::where('medical_record_case_id', $medical_record_case->id)->where('status', '!=', 'Archived')->get();
        // dd($vaccination_case_record);


        // $vaccine_administered = vaccineAdministered::where('vaccination_case_record_id', $vaccination_case_record[0]->id)->get();
        // dd($medical_record_case, $vaccination_case_record, $vaccine_administered);
        return view('records.vaccination.patientCase', [
            'isActive' => true,
            'page' => 'RECORD',
            'vaccination_case_record' => $vaccination_case_record,
            'medical_record_case' => $medical_record_case,
            'healthWorkerName' => $healthWorkerName
        ]);
    }
    public function vaccinationViewCase($id)
    {

        try {
            $healthWorkerName = '';

            $vaccinationCase = vaccination_case_records::findOrFail($id);

            if (Auth::user()->role == 'staff') {
                $staffInfo = staff::where("user_id", Auth::user()->id)->first();
                $healthWorkerName = $staffInfo->full_name;
            } else {
                if (Auth::user()->role == 'nurse') {
                    $staffInfo = staff::where("user_id", $vaccinationCase->health_worker_id)->first();
                    $healthWorkerName = $staffInfo->full_name;
                }
            }
            $vaccineAdministered = vaccineAdministered::where(
                'vaccination_case_record_id',
                $vaccinationCase->id
            )->get();

            return response()->json([
                'vaccinationCase'     => $vaccinationCase,
                'vaccineAdministered' => $vaccineAdministered,
                'healthWorkerName' => $healthWorkerName
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Vaccination case not found.'
            ], 404);
        } catch (\Exception $e) {
            // For any other kind of exception
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deletePatient($typeOfPatient, $id,)
    {
        try {
            $patient = patients::findOrFail($id);

            // if(!$patient)return;

            $patient->update([
                'status' => 'Archived'
            ]);

            if ($typeOfPatient === 'vaccination') {
                $vaccinationMasterlistRecord = vaccination_masterlists::where("patient_id", $id)->first();
                $vaccinationMasterlistRecord->update([
                    'status' => 'Archived'
                ]);
            }
            if ($typeOfPatient == 'prenatal' || $typeOfPatient == 'family-planning') {
                $wraMasterlistRecord = wra_masterlists::where("patient_id", $id)->first();
                if ($wraMasterlistRecord) {
                    $wraMasterlistRecord->update([
                        'status' => 'Archived'
                    ]);
                }
            }




            return response()->json(['message' => 'Patient Record has been deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Unexpected error occurred",
                'error' => $e->getMessage()
            ], 422);
        }
    }

    // add vaccination case record
    public function addVaccinationCaseRecord(Request $request, $id)
    {

        try {
            $data = $request->validate([
                'add_patient_full_name' => 'required',
                'add_handled_by' => 'required',
                'add_date_of_vaccination' => 'required',
                'add_time_of_vaccination' => 'sometimes|nullable|string',
                'selected_vaccine_type' => 'required',
                'add_record_dose' => 'required',
                'add_case_remarks' => 'sometimes|nullable|string',
                'add_height' => [
                    'nullable',
                    'numeric',
                    'between:30,250'      // cm
                ],
                'add_weight' => [
                    'nullable',
                    'numeric',
                    'between:1,300'       // kg
                ],
                'add_temperature' => [
                    'nullable',
                    'numeric',
                    'between:35,42'       // °C
                ],
                'add_date_of_comeback' => 'required|date'
            ]);

            // get the vaccine types
            $vaccines = explode(',', $data['selected_vaccine_type']);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $key => $vaccineId) {
                $vaccineText = vaccines::find($vaccineId);

                $selectedVaccinesArray[] = $vaccineText->vaccine_acronym;
            }


            // add condition for trying to add existing record
            $existingVaccinesAdministered = [];
            $vaccinationCaseRecord = vaccination_case_records::where('medical_record_case_id', $id)->where('status', '!=', 'Archived')->get();

            foreach ($vaccinationCaseRecord as $record) {
                // explode the vaccination
                $administeredVaccines = explode(',', $record->vaccine_type);
                foreach ($administeredVaccines as $vaccine) {
                    $vaccineName = Str::upper($vaccine) . "_" . $record->dose_number;
                    $existingVaccinesAdministered[] = $vaccineName;
                }
            }

            // there's a white space on each element, so i trimmed it
            $trimmedExistingVaccineAdministed = array_map('trim', $existingVaccinesAdministered);


            // check if the vaccine is in the existing administered vaccine

            if ($existingVaccinesAdministered) {
                $existingVaccineError = [];
                // dd($selectedVaccinesArray);
                foreach ($selectedVaccinesArray as  $selectedVaccine) {
                    $vaccine =  Str::upper($selectedVaccine) . "_" . $data['add_record_dose'];

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



            $selectedVaccines = implode(',', $selectedVaccinesArray);

            $newCaseRecord = vaccination_case_records::create([
                'medical_record_case_id' => $id,
                'patient_name' => $data['add_patient_full_name'],
                'date_of_vaccination' => $data['add_date_of_vaccination'],
                'time' => $data['add_time_of_vaccination'] ?? null,
                'vaccine_type' => $selectedVaccines,
                'dose_number' => (int) $data['add_record_dose'],
                'remarks' => $data['add_case_remarks'] ?? null,
                'type_of_record' => 'Case Record',
                'health_worker_id' => (int) $data['add_handled_by'],
                'height' => $data['add_height'] ?? null,
                'weight' => $data['add_weight'] ?? null,
                'temperature' => $data['add_temperature'] ?? null,
                'date_of_comeback' => $data['add_date_of_comeback'],
                'vaccination_status' => 'completed'
            ]);

            // id of medical case record
            $medicalCaseRecordId = $newCaseRecord->id;

            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);

                $vaccineAdministered = vaccineAdministered::create([
                    'vaccination_case_record_id' => $medicalCaseRecordId,
                    'vaccine_type' => $vaccine->type_of_vaccine,
                    'dose_number' => $data['add_record_dose'] ?? null,
                    'vaccine_id' => $vaccineId ?? null
                ]);
            }

            // vaccination Masterlist list
            $vaccinationMasterlist = vaccination_masterlists::where('medical_record_case_id', $id)->first();

            //  loop through
            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);
                $vaccineText = $vaccine->vaccine_acronym == 'Hepatitis B' ? $vaccine->vaccine_acronym : Str::upper($vaccine->vaccine_acronym);
                $itemColumn = $vaccineText == 'Hepatitis B' ? $vaccineText : $vaccineText . "_" . $data['add_record_dose'];

                $vaccineTypes = ['BCG', 'Hepatitis B', 'PENTA_1', 'PENTA_2', 'PENTA_3', 'OPV_1', 'OPV_2', 'OPV_3', 'PCV_1', 'PCV_2', 'PCV_3', 'IPV_1', 'IPV_2', 'MCV_1', 'MCV_2'];
                if (in_array($itemColumn, $vaccineTypes)) {
                    if ($vaccinationMasterlist) {
                        $vaccinationMasterlist->update([
                            "$itemColumn" => $data['add_date_of_vaccination']
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Patient has been added'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }
    public function deleteVaccinationCase($id)
    {
        try {
            $vaccination_case_record = vaccination_case_records::findOrFail($id);
            $vaccination_case_record->update([
                'status' => 'Archived'
            ]);

            // update the masterlist record
            $vaccinationMasterlist = vaccination_masterlists::where('medical_record_case_id', $vaccination_case_record->medical_record_case_id)->first();
            $vaccines = explode(",", $vaccination_case_record->vaccine_type);

            foreach ($vaccines as $vaccine) {
                $vaccineText = $vaccine == 'Hepatitis B' ? $vaccine : Str::upper($vaccine);
                $itemColumn = $vaccineText == 'Hepatitis B' ? $vaccineText : $vaccineText . "_" . $vaccination_case_record->dose_number;


                $vaccinationMasterlist->update([
                    $itemColumn => null
                ]);
            };

            return response()->json([
                'success' => true,
                'message' => 'Vaccination case deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vaccination case.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // prenatal
    public function prenatalRecord()
    {
        $prenatalRecord = medical_record_cases::with('patient')->where('type_of_case', 'prenatal')->get();
        return view('records.prenatal.prenatal', ['isActive' => true, 'page' => 'RECORD', 'prenatalRecord' => $prenatalRecord]);
    }
    public function viewPrenatalDetail($id)
    {
        $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_case_record.pregnancy_history_questions', 'prenatal_medical_record',])->where('id', $id)->firstOrFail();
        // $caseInfo = prenatal_case_records::with('pregnancy_history_questions')->where('medical_record_case_id',$id)->firstOrFail();
        $prenatalCaseRecord = prenatal_case_records::with('pregnancy_history_questions')->where('medical_record_case_id', $prenatalRecord->id)->firstOrFail();
        // address
        $address = patient_addresses::where('patient_id', $prenatalRecord->patient->id)->firstorFail();
        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
        return view('records.prenatal.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'prenatalRecord' => $prenatalRecord, 'prenatalCaseRecord' => $prenatalCaseRecord, 'fullAddress' => $fullAddress]);
    }

    public function editPrenatalDetail($id)
    {
        $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $id)->firstOrFail();
        $caseRecord = prenatal_case_records::where('medical_record_case_id', $id)->where("status", "!=", 'Archived')->firstOrFail();

        $address = patient_addresses::where('patient_id', $prenatalRecord->patient->id)->firstOrFail();
        return view('records.prenatal.editPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'prenatalRecord' => $prenatalRecord, 'address' => $address, 'caseRecord' => $caseRecord]);
    }
    public function prenatalCase($caseId)
    {
        $prenatalCaseRecords = medical_record_cases::with('pregnancy_checkup')->where('id', $caseId)->firstOrFail();

        $familyPlanningMedicalCase = medical_record_cases::where('patient_id', $prenatalCaseRecords->patient_id)->where('type_of_case', 'family-planning')->first() ?? null;
        // dd($familyPlanningMedicalCase);
        $patientInfo = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $caseId)->first();

        $prenatal_case_record = prenatal_case_records::with('pregnancy_timeline_records')->where("medical_record_case_id", $caseId)->where("status", '!=', 'Archived')
            ->get();
        $pregnancy_plan = pregnancy_plans::where("medical_record_case_id", $caseId)->where("status", '!=', 'Archived')->first();

        if (!$familyPlanningMedicalCase) {
            return view('records.prenatal.prenatalPatientCase', [
                'isActive' => true,
                'page' => 'RECORD',
                'prenatalCaseRecords' => $prenatalCaseRecords,
                'familyPlanningRecord' => null,
                'familyPlanSidebRecord' => null,
                'caseId' => $caseId,
                'patientInfo' => $patientInfo,
                'prenatal_case_record' => $prenatal_case_record,
                'pregnancy_plan' => $pregnancy_plan
            ]);
        }
        $familyPlanCaseInfo = family_planning_case_records::with(['medical_history', 'obsterical_history', 'risk_for_sexually_transmitted_infection', 'physical_examinations'])
            ->where('medical_record_case_id', $familyPlanningMedicalCase->id)->first() ?? null;
        // dd($familyPlanCaseInfo);
        $familyPlanSideB = family_planning_side_b_records::where('medical_record_case_id', $familyPlanningMedicalCase->id)->first() ?? null;

        return view(
            'records.prenatal.prenatalPatientCase',
            [
                'isActive' => true,
                'page' => 'RECORD',
                'prenatalCaseRecords' => $prenatalCaseRecords,
                'familyPlanningRecord' => $familyPlanCaseInfo,
                'familyPlanSidebRecord' => $familyPlanSideB,
                'patientInfo' => $patientInfo,
                'caseId' => $caseId,
                'prenatal_case_record' => $prenatal_case_record,
                'pregnancy_plan' => $pregnancy_plan
            ]
        );
    }

    // senior Citizen

    public function seniorCitizenRecord()
    {
        $seniorCitizenRecords = medical_record_cases::with('patient')->where('type_of_case', 'senior-citizen')
            ->whereHas('patient', function ($query) {
                $query->where('status', '!=', 'Archived');
            })
            ->get();
        return view('records.seniorCitizen.seniorCitizen', ['isActive' => true, 'page' => 'RECORD', 'seniorCitizenRecords' => $seniorCitizenRecords]);
    }
    public function seniorCitizenDetail($id)
    {
        $seniorCitizenRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record'])->findOrFail($id);
        // address
        $address = patient_addresses::where('patient_id', $seniorCitizenRecord->patient->id)->firstorFail();
        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
        return view('records.seniorCitizen.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'seniorCitizenRecord' => $seniorCitizenRecord, 'fullAddress' => $fullAddress]);
    }
    public function editSeniorCitizenDetail($id)
    {
        $seniorCitizenRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record'])->findOrFail($id);
        // address
        $address = patient_addresses::where('patient_id', $seniorCitizenRecord->patient->id)->firstorFail();
        return view('records.seniorCitizen.editPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'seniorCitizenRecord' => $seniorCitizenRecord, 'address' => $address]);
    }
    public function viewSeniorCitizenCases($id)
    {
        $seniorCaseRecords = senior_citizen_case_records::where('medical_record_case_id', $id)->where('status', '!=', 'Archived')->get();
        $patientRecord = medical_record_cases::with('patient', 'senior_citizen_medical_record')->findOrFail($id);
        return view(
            'records.seniorCitizen.seniorCitizenPatientCase',
            [
                'isActive' => true,
                'page' => 'RECORD',
                'seniorCaseRecords' =>  $seniorCaseRecords,
                'patient_name' => $patientRecord->patient->full_name,
                'healthWorkerId' => $patientRecord->senior_citizen_medical_record->health_worker_id,
                'medicalRecordId' => $id
            ]
        );
    }
    public function viewSeniorCitizenCaseInfo()
    {
        return view('records.seniorCitizen.viewCase', ['isActive' => true, 'page' => 'RECORD']);
    }

    // -------------------------- family planning
    public function familyPlanningRecord()
    {
        $familyPlanning = medical_record_cases::with('patient')
            ->where('type_of_case', 'family-planning')
            ->where('status', '!=', 'Archived')
            ->get();
        return view('records.familyPlanning.familyPlanning', ['isActive' => true, 'page' => 'RECORD', 'familyPlanningRecords' => $familyPlanning]);
    }
    public function familyPlanningDetail($id)
    {
        $familyPlanningRecords = medical_record_cases::with(['patient', 'family_planning_case_record', 'family_planning_medical_record'])->findOrFail($id);
        return view('records.familyPlanning.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'familyPlanningRecord' => $familyPlanningRecords]);
    }
    public function editFamilyPlanningDetail($id)
    {
        $familyPlanningRecords = medical_record_cases::with(['patient', 'family_planning_case_record', 'family_planning_medical_record'])->findOrFail($id);
        $address = patient_addresses::where('patient_id', $familyPlanningRecords->patient->id)->firstOrFail();
        return view('records.familyPlanning.editPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'familyPlanningRecord' => $familyPlanningRecords, 'address' => $address]);
    }
    public function viewFamilyPlanningCase($id)
    {
        $familyPlanningCases = family_planning_case_records::where('medical_record_case_id', $id)
            ->where('status', '!=', 'Archived')
            ->get();
        $familyPlanningSideB = family_planning_side_b_records::where('medical_record_case_id', $id)
            ->where('status', '!=', 'Archived')
            ->get();
        $patientInfo = medical_record_cases::with(['family_planning_medical_record', 'patient'])
            ->where('status', '!=', 'Archived')
            ->findOrFail($id);
        $address = patient_addresses::where("patient_id", $patientInfo->patient->id)->first() ?? null;
        return view('records.familyPlanning.familyPlanningCase', [
            'isActive' => true,
            'page' => 'RECORD',
            'familyPlanningCases' => $familyPlanningCases,
            'patientInfo' => $patientInfo,
            'familyPlanningSideB' => $familyPlanningSideB,
            'medicalRecordCaseId' => $id,
            'address' => $address
        ]);
    }

    // --------------------------- tb dots ----------------------------------------
    public function tb_dotsRecord()
    {
        $tbRecords = medical_record_cases::with('patient')->where('type_of_case', 'tb-dots')
            ->where('status', "!=", 'Archived')
            ->get();
        return view('records.tb-dots.tb-dots', ['isActive' => true, 'page' => 'RECORD', 'tbRecords' => $tbRecords]);
    }
    public function tb_dotsDetail($id)
    {
        try {
            $tbRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->findOrFail($id);

            $address = patient_addresses::where('patient_id', $tbRecord->patient->id)->firstorFail();
            $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
            return view('records.tb-dots.viewtb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'tbDotsRecord' => $tbRecord, 'fullAddress' => $fullAddress]);
        } catch (\Exception $e) {
            return  view('records.tb-dots.viewtb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'error' => $e->getMessage()]);
        }
    }
    public function editTb_dotsDetail($id)
    {
        try {
            $tbRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->findOrFail($id);

            $address = patient_addresses::where('patient_id', $tbRecord->patient->id)->firstorFail();
            return view('records.tb-dots.editTb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'tbDotsRecord' => $tbRecord, 'address' => $address]);
        } catch (\Exception $e) {
            return view('records.tb-dots.editTb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'error' => $e->getMessage()]);
        }
    }
    public function viewTb_dotsCase($id)
    {
        $tbDotsCaseRecords = tb_dots_case_records::where('medical_record_case_id', $id)
            ->where('status', '!=', 'Archived')
            ->get();
        $patientRecord = medical_record_cases::with('patient', 'tb_dots_medical_record')
            ->where('status', '!=', 'Archived')
            ->findOrFail($id);

        // check up 

        $checkUpRecords = tb_dots_check_ups::where('medical_record_case_id', $id)->get();
        return view('records.tb-dots.tb_dotsCase', [
            'isActive' => true,
            'page' => 'RECORD',
            'tbDotsRecords' =>  $tbDotsCaseRecords,
            'checkUpRecords' => $checkUpRecords,
            'patient_name' => $patientRecord->patient->full_name,
            'healthWorkerId' => $patientRecord->tb_dots_medical_record->health_worker_id,
            'medicalRecordId' => $id,
            'patientInfo' => $patientRecord
        ]);
    }
    private function calculateAgeInMonths($dateOfBirth)
    {
        if (!$dateOfBirth) {
            return null;
        }

        $dob = Carbon::parse($dateOfBirth);
        $now = Carbon::now();

        return $dob->diffInMonths($now);
    }
}
