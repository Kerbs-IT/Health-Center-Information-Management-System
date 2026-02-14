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
                'vaccination_masterlist_fname' => 'required',
                'vaccination_masterlist_lname' => 'required',
                'vaccination_masterlist_MI' => 'sometimes|nullable|string',
                'vaccination_masterlist_suffix' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'sex' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric|max:100',
                'date_of_birth' => 'required|date|before_or_equal:today',
                'SE_status' => 'sometimes|nullable|string',
                'remarks' => 'sometimes|nullable|string'
            ], [
                // Custom messages with friendly attribute names
                'vaccination_masterlist_fname.required' => 'The first name field is required.',

                'vaccination_masterlist_lname.required' => 'The last name field is required.',

                'vaccination_masterlist_MI.string' => 'The middle initial must be a string.',

                'age.numeric' => 'The age must be a number.',
                'age.max' => 'The age may not be greater than :max.',

                'date_of_birth.required' => 'The date of birth field is required.',
                'date_of_birth.date' => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal' => 'The date of birth must be today or earlier.',

                'SE_status.string' => 'The SE status must be a string.',
            ]);

            // i separated it for easy way of accessing
            $vaccineData = $request->validate([
                'BCG' => 'sometimes|nullable|date',
                'Hepatitis_B' => 'sometimes|nullable|date',
                'PENTA_1'  => 'sometimes|nullable|date',
                'PENTA_2'  => 'sometimes|nullable|date',
                'PENTA_3' => 'sometimes|nullable|date',
                'OPV_1' => 'sometimes|nullable|date',
                'OPV_2' => 'sometimes|nullable|date',
                'OPV_3' => 'sometimes|nullable|date',
                'PCV_1' => 'sometimes|nullable|date',
                'PCV_2' => 'sometimes|nullable|date',
                'PCV_3' => 'sometimes|nullable|date',
                'IPV_1' => 'sometimes|nullable|date',
                'IPV_2' => 'sometimes|nullable|date',
                'MCV_1' => 'sometimes|nullable|date',
                'MCV_2' => 'sometimes|nullable|date',
            ], [
                // Custom messages with friendly attribute names
                'BCG.date' => 'The BCG must be a valid date.',
                'Hepatitis_B.date' => 'The Hepatitis B must be a valid date.',
                'PENTA_1.date' => 'The PENTA 1 must be a valid date.',
                'PENTA_2.date' => 'The PENTA 2 must be a valid date.',
                'PENTA_3.date' => 'The PENTA 3 must be a valid date.',
                'OPV_1.date' => 'The OPV 1 must be a valid date.',
                'OPV_2.date' => 'The OPV 2 must be a valid date.',
                'OPV_3.date' => 'The OPV 3 must be a valid date.',
                'PCV_1.date' => 'The PCV 1 must be a valid date.',
                'PCV_2.date' => 'The PCV 2 must be a valid date.',
                'PCV_3.date' => 'The PCV 3 must be a valid date.',
                'IPV_1.date' => 'The IPV 1 must be a valid date.',
                'IPV_2.date' => 'The IPV 2 must be a valid date.',
                'MCV_1.date' => 'The MCV 1 must be a valid date.',
                'MCV_2.date' => 'The MCV 2 must be a valid date.',
            ]);

            // update the dates of the case record
            // get all the existing vaccine, it will be use to check if the 
            $vaccination_case_records = vaccination_case_records::where('medical_record_case_id', $id)->where('status', '!=', 'Archived')->get();
            $vaccination_masterlist = vaccination_masterlists::with('patient')->where('medical_record_case_id', $id)->firstOrFail();
            $vaccination_medical_record = vaccination_medical_records::where('medical_record_case_id', $id)->firstOrFail();
            // create a variable that will store the existing vaccine
            $existing_vaccines = [];
            // loop to get the vaccine
            foreach ($vaccination_case_records as $record) {
                $vaccines = explode(',', $record->vaccine_type);
                // loop to the vaccines
                foreach ($vaccines as $vac) {
                    $existing_vaccines[] = $vac == 'Hepatitis B' ? Str::upper($vac) : Str::upper($vac) . "_" . $record->dose_number;
                }
                // update all of the patient name on each records
                $record->update([
                    'patient_name' => trim(($data['vaccination_masterlist_fname'] . " " . $data['vaccination_masterlist_MI'] . "." . $data['vaccination_masterlist_lname']), " ")
                ]);
            }
            $middle = substr($data['vaccination_masterlist_MI'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleInitial = $data['vaccination_masterlist_MI'] ? ucwords($data['vaccination_masterlist_MI']) : '';
            $parts = [
                strtolower($data['vaccination_masterlist_fname']),
                $middle,
                strtolower($data['vaccination_masterlist_lname']),
                $data['vaccination_masterlist_suffix'] ?? null,
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));
            // update the patient name
            $vaccination_masterlist->patient->update([
                'first_name' => ucwords(strtolower($data['vaccination_masterlist_fname'] ?? $vaccination_masterlist->patient->first_name)),
                'middle_initial' => !empty($data['vaccination_masterlist_MI']) ? ucwords(strtolower($data['vaccination_masterlist_MI'])) : null,
                'last_name' => ucwords(strtolower($data['vaccination_masterlist_lname'] ?? $vaccination_masterlist->patient->last_name)),
                'full_name'=>  $fullName,
                'sex' => $data['sex'] ?? $vaccination_masterlist->patient->sex,
                'age' => $data['age'] ?? $vaccination_masterlist->patient->age,
                'date_of_birth' => $data['date_of_birth'] ?? $vaccination_masterlist->patient->date_of_birth,
                'suffix' => $data['vaccination_masterlist_suffix']??''
            ]);

            // update the address
            $address = patient_addresses::where('patient_id', $vaccination_masterlist->patient_id)->firstOrFail();
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $data['brgy'] ?? $address->purok
            ]);

            // this approach is for the existing dates


            // loop through the inputs
            foreach ($vaccineData as $name => $date) {
                if (!in_array($name, $existing_vaccines) && $date != null) {
                    // explode the name first to remove the dose number
                    if ($name == 'BCG') {
                    } else if ($name == 'Hepatitis_B') {
                        $vaccine = vaccines::where('vaccine_acronym', 'Hepatitis B')->first();

                        // insert the data to vaccine_case_record
                        $caseRecord = vaccination_case_records::create([
                            'medical_record_case_id' => $id,
                            'patient_name' =>  trim(($data['vaccination_masterlist_fname'] . " " . $data['vaccination_masterlist_MI'] . "." . $data['vaccination_masterlist_lname']), " "),
                            'date_of_vaccination' => $date,
                            'time' =>   null,
                            'vaccine_type' => $name,
                            'dose_number' => 1,
                            'remarks' =>  null,
                            'type_of_record' => 'Case Record',
                            'health_worker_id' => (int) $vaccination_masterlist->health_worker_id
                        ]);
                        // vaccine administered 
                        $vaccineAdministed = vaccineAdministered::create([
                            'vaccination_case_record_id' => $caseRecord->id,
                            'vaccine_type' => $vaccine->type_of_vaccine,
                            'dose_number' =>  1,
                            'vaccine_id' => $vaccine->id
                        ]);
                    } else {
                        $vaccine_acronym = explode("_", $name);
                        $vaccine = vaccines::where('vaccine_acronym', $vaccine_acronym[0])->first();

                        // insert the data to vaccine_case_record
                        $caseRecord = vaccination_case_records::create([
                            'medical_record_case_id' => $id,
                            'patient_name' =>  trim(($data['vaccination_masterlist_fname'] . " " . $data['vaccination_masterlist_MI'] . "." . $data['vaccination_masterlist_lname']), " "),
                            'date_of_vaccination' => $date,
                            'time' => null,
                            'vaccine_type' => Str::ucfirst($vaccine_acronym[0]),
                            'dose_number' => (int) $vaccine_acronym[1],
                            'remarks' =>  null,
                            'type_of_record' => 'Case Record',
                            'health_worker_id' => (int) $vaccination_masterlist->health_worker_id
                        ]);
                        // vaccine administered 
                        $vaccineAdministed = vaccineAdministered::create([
                            'vaccination_case_record_id' => $caseRecord->id,
                            'vaccine_type' => $vaccine->type_of_vaccine,
                            'dose_number' => (int) $vaccine_acronym[1],
                            'vaccine_id' => $vaccine->id
                        ]);
                    }
                } else {
                    if (in_array($name, $existing_vaccines) && $date != null) {
                        // use loop for check if the $name exist in that record, so we can conduct checking to the value
                        foreach ($vaccination_case_records as $record) {
                            $vaccines = explode(',', $record->vaccine_type);
                            $currently_existing_vaccines = [];
                            // loop to the vaccines
                            foreach ($vaccines as $vac) {
                                $currently_existing_vaccines[] = $vac == 'Hepatitis B' ? Str::upper($vac) : Str::upper($vac) . "_" . $record->dose_number;
                            }
                           
                            // add condition if the $name exist in the array
                            if (!in_array($name, $currently_existing_vaccines)) continue;
                            // check if the date is not changed
                            if($vaccineData[$name] == $record->date_of_vaccination)continue;

                            $submittedVaccines = [];
                            foreach ($currently_existing_vaccines as $current_vaccine) {
                                if (isset($vaccineData[$current_vaccine]) && $vaccineData[$current_vaccine] != null) {
                                    $submittedVaccines[$current_vaccine] = $vaccineData[$current_vaccine];
                                }
                            }
                            if(count(array_unique($submittedVaccines)) > 1){
                                if($name != 'Hepatitis_B' || $name != 'BCG'){
                                    list($vaccineName, $doseNumber) = explode('_',$name);

                                    $existingVaccines = explode(',', $record->vaccine_type);

                                    $remainingVaccines = array_filter($existingVaccines, function ($v) use ($vaccineName) {
                                        return trim(Str::upper($v)) !== $vaccineName;
                                    });

                                    // reset the administer vaccine
                                    $removeVaccineAdministered = vaccineAdministered::where('vaccination_case_record_id',$record->id)->delete();
                                    // update existing record
                                    $record->update([
                                        'vaccine_type' => implode(',', $remainingVaccines),
                                        'dose_number' => $doseNumber,
                                    ]);

                                    // loop through the remainingVaccines
                                    foreach($remainingVaccines as $vaccine){
                                        $vaccineInfo = vaccines::where('vaccine_acronym', $vaccine)->first();
                                        $vaccineAdministed = vaccines::create([
                                            'vaccination_case_record_id' => $record->id,
                                            'vaccine_type' => $vaccineInfo -> type_of_vaccine,
                                            'dose_number' => $doseNumber,
                                            'vaccine_id' => $vaccineInfo ->id
                                        ]);
                                    }

                                    // create a new record
                                    $newRecord = vaccination_case_records::create([
                                        'medical_record_case_id' => $record->medical_record_case_id,
                                        'patient_name' => $record->patient_name,
                                        'date_of_vaccination' => $date,
                                        'time' => null,
                                        'vaccine_type' => $vaccineName,
                                        'dose_number' => $doseNumber,
                                        'remarks' => null,
                                        'type_of_record' => 'Case Record',
                                        'health_worker_id' => $record->health_worker_id,
                                    ]);

                                    $vaccineInfo = vaccines::where('vacine_acronym', $vaccineName)->first();
                                    $vaccineAdministed = vaccines::create([
                                        'vaccination_case_record_id' => $newRecord->id,
                                        'vaccine_type' => $vaccineInfo->type_of_vaccine,
                                        'dose_number' => $doseNumber,
                                        'vaccine_id' => $vaccineInfo->id
                                    ]);


                                }
                                
                            }else{
                                // no conflict
                                // just update
                                $vaccination_masterlist-> update([
                                    $name => $date
                                ]);
                                $record -> update([
                                    'date_of_vaccination' => $date
                                ]);

                            }

                            // update all of the patient name on each records
                            $record->update([
                                'patient_name' => trim(($data['vaccination_masterlist_fname'] . " " . $data['vaccination_masterlist_MI'] . "." . $data['vaccination_masterlist_lname']), " ")
                            ]);
                        }
                    }
                }
            }
            // refresh the new address
            $address->refresh();
            $newAddress = $address->house_number . ", " . $address->street . "," . $address->purok . "," . $address->barangay . "," . $address->city . "," . $address->province;
            $vaccination_masterlist->update([
                'name_of_child' => $fullName,
                'Address' => $newAddress,
                'sex' => $data['sex'] ?? $vaccination_masterlist->sex,
                'age' => $data['age'] ?? $vaccination_masterlist->age,
                'date_of_birth' => $data['date_of_birth'] ?? $vaccination_masterlist->date_of_birth,
                'remarks' => $data['remarks'] ?? $vaccination_masterlist->remarks

            ]);
            $vaccination_masterlist->update($vaccineData);

            return response()->json([
                'message' => 'Vaccination Masterlist is Successfully updated'
            ], 200);

            // update the masterlist
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
