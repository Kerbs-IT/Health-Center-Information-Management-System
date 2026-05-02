<?php

namespace App\Http\Controllers;

use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use App\Models\wra_masterlists;
use App\Services\PatientUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class masterListController extends Controller
{
    public function viewVaccinationMasterList()
    {
        $vaccination_masterlist = vaccination_masterlists::OrderBy('name_of_child','ASC')->get();
        return view('masterlist.vaccination', ['isActive' => true, 'page' => 'VACCINATION', 'pageHeader' => 'MASTERLIST', 'vaccinationMasterlist' => $vaccination_masterlist]);
    }
    public function viewWRAMasterList()
    {
        $wra_masterList = wra_masterlists::where('status','!=','Archived')->orderBy('name_of_wra','ASC')->get();
        return view('masterlist.wra', ['isActive' => true, 'page' => 'WOMEN OF REPRODUCTIVE AGE', 'pageHeader' => 'MASTERLIST','masterlistRecords'=> $wra_masterList]);
    }

    public function getInfo($typeOfRecord, $id)
    {

        if ($typeOfRecord == 'vaccination') {
            try {
                $vaccinationMasterlistInfo = vaccination_masterlists::findOrFail($id);

                $patientAddress = patient_addresses::findOrFail($vaccinationMasterlistInfo->address_id);
                $patientDetails = patients::findOrFail($vaccinationMasterlistInfo->patient_id);
                return response()->json([
                    'info' => $vaccinationMasterlistInfo,
                    'address_info' =>  $patientAddress,
                    'patientDetails' => $patientDetails
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ],402);
            }
        }elseif($typeOfRecord == 'wra'){
            try {
                $wraMasterlistInfo = wra_masterlists::findOrFail($id);
                $patientAddress = patient_addresses::findOrFail($wraMasterlistInfo->address_id);
                $patientDetails = patients::findOrFail($wraMasterlistInfo->patient_id);
                return response()->json([
                    'info' => $wraMasterlistInfo,
                    'address_info' =>  $patientAddress,
                    'patientDetails' => $patientDetails
                ],200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ],402);
            }
        }
    }

    public function updateVaccinationMasterlist(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'vaccination_masterlist_fname'  => 'required',
                'vaccination_masterlist_lname'  => 'required',
                'vaccination_masterlist_MI'     => 'sometimes|nullable|string',
                'vaccination_masterlist_suffix' => 'sometimes|nullable|string',
                'street'        => 'required',
                'brgy'          => 'required',
                'sex'           => 'required|string',
                'age'           => 'sometimes|nullable|numeric|max:100',
                'date_of_birth' => 'required|date|before_or_equal:today',
                'SE_status'     => 'sometimes|nullable|string',
                'remarks'       => 'sometimes|nullable|string',
            ], [
                'vaccination_masterlist_fname.required' => 'The first name field is required.',
                'vaccination_masterlist_lname.required' => 'The last name field is required.',
                'age.numeric'                           => 'The age must be a number.',
                'age.max'                               => 'The age may not be greater than :max.',
                'date_of_birth.required'                => 'The date of birth field is required.',
                'date_of_birth.date'                    => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal'         => 'The date of birth must be today or earlier.',
            ]);

            // Vaccine fields — key is the form input name, value is the submitted date
            // Note: "Hepatitis_B" (underscore) is what the form sends, matching input name="Hepatitis_B"
            $vaccineData = $request->validate([
                'BCG'         => 'sometimes|nullable|date',
                'Hepatitis_B' => 'sometimes|nullable|date',
                'PENTA_1'     => 'sometimes|nullable|date',
                'PENTA_2'     => 'sometimes|nullable|date',
                'PENTA_3'     => 'sometimes|nullable|date',
                'OPV_1'       => 'sometimes|nullable|date',
                'OPV_2'       => 'sometimes|nullable|date',
                'OPV_3'       => 'sometimes|nullable|date',
                'PCV_1'       => 'sometimes|nullable|date',
                'PCV_2'       => 'sometimes|nullable|date',
                'PCV_3'       => 'sometimes|nullable|date',
                'IPV_1'       => 'sometimes|nullable|date',
                'IPV_2'       => 'sometimes|nullable|date',
                'MCV_1'       => 'sometimes|nullable|date',
                'MCV_2'       => 'sometimes|nullable|date',
            ], [
                'BCG.date'         => 'The BCG must be a valid date.',
                'Hepatitis_B.date' => 'The Hepatitis B must be a valid date.',
                'PENTA_1.date'     => 'The PENTA 1 must be a valid date.',
                'PENTA_2.date'     => 'The PENTA 2 must be a valid date.',
                'PENTA_3.date'     => 'The PENTA 3 must be a valid date.',
                'OPV_1.date'       => 'The OPV 1 must be a valid date.',
                'OPV_2.date'       => 'The OPV 2 must be a valid date.',
                'OPV_3.date'       => 'The OPV 3 must be a valid date.',
                'PCV_1.date'       => 'The PCV 1 must be a valid date.',
                'PCV_2.date'       => 'The PCV 2 must be a valid date.',
                'PCV_3.date'       => 'The PCV 3 must be a valid date.',
                'IPV_1.date'       => 'The IPV 1 must be a valid date.',
                'IPV_2.date'       => 'The IPV 2 must be a valid date.',
                'MCV_1.date'       => 'The MCV 1 must be a valid date.',
                'MCV_2.date'       => 'The MCV 2 must be a valid date.',
            ]);

            // ------------------------------------------------------------------
            // Helper: convert vaccine acronym + dose → form key (matches $vaccineData keys)
            // BCG           → 'BCG'
            // HEPATITIS B   → 'Hepatitis_B'
            // PENTA + dose1 → 'PENTA_1'
            // ------------------------------------------------------------------
            $toFormKey = function (string $acronym, int $dose): string {
                $upper = Str::upper(trim($acronym));
                if ($upper === 'HEPATITIS B') return 'Hepatitis_B';
                if ($upper === 'BCG')         return 'BCG';
                return $upper . '_' . $dose;
            };

            // ------------------------------------------------------------------
            // Helper: convert form key → masterlist DB column name
            // 'Hepatitis_B' → 'Hepatitis B'
            // 'BCG'         → 'BCG'
            // 'PENTA_1'     → 'PENTA_1'
            // ------------------------------------------------------------------
            $toColumnName = function (string $formKey): string {
                if ($formKey === 'Hepatitis_B') return 'Hepatitis B';
                return $formKey;
            };

            // ------------------------------------------------------------------
            // Helper: resolve vaccine_type string and dose number from form key
            // for use when creating new case records
            // ------------------------------------------------------------------
            $resolveVaccineType = function (string $formKey): array {
                if ($formKey === 'Hepatitis_B') return ['Hepatitis B', 1];
                if ($formKey === 'BCG')         return ['BCG', 1];
                $parts = explode('_', $formKey); // e.g. 'PENTA_1' → ['PENTA', '1']
                return [$parts[0], (int) $parts[1]];
            };

            // ------------------------------------------------------------------
            // Helper: find vaccine model by form key
            // ------------------------------------------------------------------
            $findVaccine = function (string $formKey) {
                if ($formKey === 'Hepatitis_B') return vaccines::where('vaccine_acronym', 'HEPATITIS B')->first();
                if ($formKey === 'BCG')         return vaccines::where('vaccine_acronym', 'BCG')->first();
                $parts = explode('_', $formKey);
                return vaccines::where('vaccine_acronym', $parts[0])->first();
            };

            // ------------------------------------------------------------------
            // Fetch records
            // ------------------------------------------------------------------
            $vaccination_case_records = vaccination_case_records::where('medical_record_case_id', $id)
                ->where('status', '!=', 'Archived')
                ->get();

            $vaccination_masterlist = vaccination_masterlists::with('patient')
                ->where('medical_record_case_id', $id)
                ->firstOrFail();

            $vaccination_medical_record = vaccination_medical_records::where('medical_record_case_id', $id)
                ->firstOrFail();

            $healthWorkerId = !empty($vaccination_masterlist->health_worker_id)
                ? (int) $vaccination_masterlist->health_worker_id
                : null;

            // ------------------------------------------------------------------
            // Build vaccineRecordMap: formKey → case record
            // e.g. ['BCG' => $record1, 'Hepatitis_B' => $record1, 'PENTA_1' => $record1]
            // ------------------------------------------------------------------
            $vaccineRecordMap = [];
            foreach ($vaccination_case_records as $record) {
                $recordVaccines = array_map('trim', explode(',', $record->vaccine_type));
                foreach ($recordVaccines as $vac) {
                    $formKey = $toFormKey($vac, $record->dose_number);
                    $vaccineRecordMap[$formKey] = $record;
                }
            }

            // ------------------------------------------------------------------
            // Build patient name strings
            // ------------------------------------------------------------------
            $middle   = substr($data['vaccination_masterlist_MI'] ?? '', 0, 1);
            $middle   = $middle ? strtoupper($middle) . '.' : null;
            $parts    = [
                strtolower($data['vaccination_masterlist_fname']),
                $middle,
                strtolower($data['vaccination_masterlist_lname']),
                $data['vaccination_masterlist_suffix'] ?? null,
            ];
            $fullName   = ucwords(trim(implode(' ', array_filter($parts))));
            $patientName = trim($data['vaccination_masterlist_fname'] . ' ' . ($data['vaccination_masterlist_MI'] ?? '') . '.' . $data['vaccination_masterlist_lname']);
            $sex = $data['sex'] ? ucwords(strtolower($data['sex'])) : null;

            // Update patient name on all case records
            foreach ($vaccination_case_records as $record) {
                $record->update(['patient_name' => $patientName]);
            }

            // ------------------------------------------------------------------
            // Update patient details
            // ------------------------------------------------------------------
            $serviceData = [
                'first_name'     => $data['vaccination_masterlist_fname'],
                'last_name'      => $data['vaccination_masterlist_lname'],
                'middle_initial' => $data['vaccination_masterlist_MI']     ?? null,
                'suffix'         => $data['vaccination_masterlist_suffix'] ?? null,
                'street'         => $data['street'],
                'brgy'           => $data['brgy'],
                'sex'            => $data['sex']                           ?? null,
                'date_of_birth'  => $data['date_of_birth'],
                'contact_number' => null,
                'civil_status'   => null,
                'place_of_birth' => null,
                'nationality'    => null,
            ];

            $patientUpdateService = new PatientUpdateService();
            $patientUpdateService->updatePatientDetails($serviceData, $vaccination_masterlist->patient_id);

            $vaccination_masterlist->patient->update([
                'first_name'     => ucwords(strtolower($data['vaccination_masterlist_fname'])),
                'middle_initial' => !empty($data['vaccination_masterlist_MI']) ? ucwords(strtolower($data['vaccination_masterlist_MI'])) : null,
                'last_name'      => ucwords(strtolower($data['vaccination_masterlist_lname'])),
                'full_name'      => $fullName,
                'sex'            => $sex ?? null,
                'age'            => $data['age'] ?? $vaccination_masterlist->patient->age,
                'date_of_birth'  => $data['date_of_birth'] ?? $vaccination_masterlist->patient->date_of_birth,
                'suffix'         => $data['vaccination_masterlist_suffix'] ?? '',
            ]);

            $address      = patient_addresses::where('patient_id', $vaccination_masterlist->patient_id)->firstOrFail();
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0]  ?? $address->house_number,
                'street'       => $blk_n_street[1]  ?? null,
                'purok'        => $data['brgy']      ?? $address->purok,
            ]);

            // ------------------------------------------------------------------
            // Process each vaccine — 5 scenarios
            // ------------------------------------------------------------------
            foreach ($vaccineData as $formKey => $newDate) {

                // Scenario 4: no date submitted — skip
                if (!$newDate) continue;

                $existingRecord = $vaccineRecordMap[$formKey] ?? null;

                // Scenario 1: vaccine has no case record yet → create new record
                if (!$existingRecord) {
                    [$vaccineTypeStr, $dose] = $resolveVaccineType($formKey);
                    $vaccine = $findVaccine($formKey);

                    $newRecord = vaccination_case_records::create([
                        'medical_record_case_id' => $id,
                        'patient_name'           => $patientName,
                        'date_of_vaccination'    => $newDate,
                        'time'                   => null,
                        'vaccine_type'           => $vaccineTypeStr,
                        'dose_number'            => $dose,
                        'remarks'                => null,
                        'type_of_record'         => 'Case Record',
                        'health_worker_id'       => $healthWorkerId,
                        'vaccination_status'     => 'completed',
                    ]);

                    if ($vaccine) {
                        vaccineAdministered::create([
                            'vaccination_case_record_id' => $newRecord->id,
                            'vaccine_type'               => $vaccine->type_of_vaccine,
                            'dose_number'                => $dose,
                            'vaccine_id'                 => $vaccine->id,
                        ]);
                    }

                    continue;
                }

                // Scenario 4: date hasn't changed — skip
                if ($newDate == $existingRecord->date_of_vaccination) continue;

                // Get all form keys that belong to this same case record
                $siblingsInRecord = array_keys(array_filter(
                    $vaccineRecordMap,
                    fn($r) => $r->id === $existingRecord->id
                ));

                $isAlone = count($siblingsInRecord) === 1;

                if ($isAlone) {
                    // Scenario 2: vaccine is alone in its record → just update the date
                    $existingRecord->update(['date_of_vaccination' => $newDate]);
                } else {
                    // Check if ALL siblings are being submitted with the SAME new date
                    $allSameNewDate = true;
                    foreach ($siblingsInRecord as $siblingKey) {
                        $siblingDate = $vaccineData[$siblingKey] ?? null;
                        if (!$siblingDate || $siblingDate !== $newDate) {
                            $allSameNewDate = false;
                            break;
                        }
                    }

                    if ($allSameNewDate) {
                        // Scenario 5: all vaccines in the shared record → just update the record date once
                        // Use the first sibling to avoid updating multiple times per record
                        if ($formKey === $siblingsInRecord[0]) {
                            $existingRecord->update(['date_of_vaccination' => $newDate]);
                        }
                    } else {
                        // Scenario 3: only THIS vaccine has a different date
                        // → split it out, remove from shared record, create new record

                        // Remove this vaccine from the existing record
                        $remainingVaccines = array_filter(
                            array_map('trim', explode(',', $existingRecord->vaccine_type)),
                            fn($vac) => $toFormKey($vac, $existingRecord->dose_number) !== $formKey
                        );

                        // Rebuild vaccineAdministered for remaining vaccines
                        vaccineAdministered::where('vaccination_case_record_id', $existingRecord->id)->delete();
                        $existingRecord->update(['vaccine_type' => implode(',', $remainingVaccines)]);

                        foreach ($remainingVaccines as $vac) {
                            $vaccineInfo = vaccines::where('vaccine_acronym', Str::upper(trim($vac)))->first();
                            if ($vaccineInfo) {
                                vaccineAdministered::create([
                                    'vaccination_case_record_id' => $existingRecord->id,
                                    'vaccine_type'               => $vaccineInfo->type_of_vaccine,
                                    'dose_number'                => $existingRecord->dose_number,
                                    'vaccine_id'                 => $vaccineInfo->id,
                                ]);
                            }
                        }

                        // Create new case record for this vaccine with the new date
                        [$vaccineTypeStr, $dose] = $resolveVaccineType($formKey);
                        $vaccine = $findVaccine($formKey);

                        $recordHealthWorkerId = !empty($existingRecord->health_worker_id)
                            ? (int) $existingRecord->health_worker_id
                            : null;

                        $newRecord = vaccination_case_records::create([
                            'medical_record_case_id' => $existingRecord->medical_record_case_id,
                            'patient_name'           => $patientName,
                            'date_of_vaccination'    => $newDate,
                            'time'                   => null,
                            'vaccine_type'           => $vaccineTypeStr,
                            'dose_number'            => $dose,
                            'remarks'                => null,
                            'type_of_record'         => 'Case Record',
                            'health_worker_id'       => $recordHealthWorkerId,
                            'vaccination_status'     => 'completed',
                        ]);

                        if ($vaccine) {
                            vaccineAdministered::create([
                                'vaccination_case_record_id' => $newRecord->id,
                                'vaccine_type'               => $vaccine->type_of_vaccine,
                                'dose_number'                => $dose,
                                'vaccine_id'                 => $vaccine->id,
                            ]);
                        }
                    }
                }
            }

            // ------------------------------------------------------------------
            // Update masterlist — name, address, demographics
            // ------------------------------------------------------------------
            $address->refresh();
            $newAddress = $address->house_number . ', ' . $address->street . ',' . $address->purok . ',' . $address->barangay . ',' . $address->city . ',' . $address->province;

            $vaccination_masterlist->update([
                'name_of_child' => $fullName,
                'Address'       => $newAddress,
                'sex'           => $sex ?? $vaccination_masterlist->sex,
                'age'           => $data['age'] ?? $vaccination_masterlist->age,
                'date_of_birth' => $data['date_of_birth'] ?? $vaccination_masterlist->date_of_birth,
                'remarks'       => $data['remarks'] ?? $vaccination_masterlist->remarks,
                'SE_status'     => $data['SE_status'] ?? $vaccination_masterlist->SE_status,
            ]);

            // ------------------------------------------------------------------
            // Write vaccine dates to masterlist columns
            // Remap form keys to exact DB column names before updating
            // 'Hepatitis_B' → 'Hepatitis B', everything else stays the same
            // ------------------------------------------------------------------
            $masterlistVaccineData = [];
            foreach ($vaccineData as $formKey => $date) {
                if (!$date) continue;
                $col = $toColumnName($formKey);
                $masterlistVaccineData[$col] = $date;
            }

            if (!empty($masterlistVaccineData)) {
                $vaccination_masterlist->update($masterlistVaccineData);
            }

            return response()->json([
                'message' => 'Vaccination Masterlist is Successfully updated'
            ], 200);
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
}
