<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\vaccination_case_records;
use App\Models\vaccineAdministered;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

class ImmunizationController extends Controller
{
    public function showByPatient($medicalRecordId)
    {
        // Get all case records for this patient
        $caseRecords = vaccination_case_records::where('medical_record_case_id', $medicalRecordId)->where('status','!=','Archived')->get();

        // Get the first or latest case record for patient info
        $medicalRecord = medical_record_cases::with(['patient', 'vaccination_medical_record'])->findOrFail($medicalRecordId);
        $caseRecord = patients::findOrFail($medicalRecord->patient_id); // or use ->latest()->first()

        $address = patient_addresses::where('patient_id',$medicalRecord->patient_id)->first();
        $fullAddress = collect([
            $address->house_number,
            $address->street,
            $address->purok,
            $address->barangay ?? null,
            $address->city ?? null,
            $address->province ?? null,
        ])->filter()->join(', ');

        // Get all case record IDs for this patient
        $caseRecordIds = $caseRecords->pluck('id');

        // Get ALL vaccine administrations for ALL case records of this patient
        // This will combine vaccinations from case records 11, 23, 44, 45, 46, 47, etc.
        $vaccineAdministered = vaccineAdministered::whereIn('vaccination_case_record_id', $caseRecordIds)
            ->with('vaccination_case_record')
            ->orderBy('vaccine_id')
            ->orderBy('dose_number')
            ->get();

        return view('immunization.card', compact('caseRecord', 'vaccineAdministered', 'fullAddress', 'medicalRecord'));
    }

    public function getCardContent($medicalRecordId){
        $caseRecords = vaccination_case_records::where('medical_record_case_id', $medicalRecordId)->where('status', '!=', 'Archived')->get();

        // Get the first or latest case record for patient info
        $medicalRecord = medical_record_cases::with(['patient','vaccination_medical_record'])->findOrFail($medicalRecordId);
        $caseRecord = patients::findOrFail($medicalRecord->patient_id); // or use ->latest()->first()

        $address = patient_addresses::where('patient_id',$medicalRecord->patient_id)->first();
        $fullAddress = collect([
            $address->house_number,
            $address->street,
            $address->purok,
            $address->barangay ?? null,
            $address->city ?? null,
            $address->province ?? null,
        ])->filter()->join(', ');
        // Get all case record IDs for this patient
        $caseRecordIds = $caseRecords->pluck('id');

        // Get ALL vaccine administrations for ALL case records of this patient
        // This will combine vaccinations from case records 11, 23, 44, 45, 46, 47, etc.
        $vaccineAdministered = vaccineAdministered::whereIn('vaccination_case_record_id', $caseRecordIds)
            ->with('vaccination_case_record')
            ->orderBy('vaccine_id')
            ->orderBy('dose_number')
            ->get();

        return view('immunization.showCard', compact('caseRecord', 'vaccineAdministered','fullAddress', 'medicalRecord'));
    }

    public function generatePDF($medicalRecordId)
    {
        $caseRecords = vaccination_case_records::where('medical_record_case_id', $medicalRecordId)->where('status', '!=', 'Archived')->get();

        // Get the first or latest case record for patient info
        $medicalRecord = medical_record_cases::with(['patient', 'vaccination_medical_record'])->findOrFail($medicalRecordId);
        $caseRecord = patients::findOrFail($medicalRecord->patient_id); // or use ->latest()->first()

        // Get all case record IDs for this patient
        $caseRecordIds = $caseRecords->pluck('id');

        $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();
        $fullAddress = collect([
            $address->house_number,
            $address->street,
            $address->purok,
            $address->barangay ?? null,
            $address->city ?? null,
            $address->province ?? null,
        ])->filter()->join(', ');

        // Get ALL vaccine administrations for ALL case records of this patient
        // This will combine vaccinations from case records 11, 23, 44, 45, 46, 47, etc.
        $vaccineAdministered = vaccineAdministered::whereIn('vaccination_case_record_id', $caseRecordIds)
            ->with('vaccination_case_record')
            ->orderBy('vaccine_id')
            ->orderBy('dose_number')
            ->get();

        $pdf = SnappyPdf::loadView('immunization.card', compact('caseRecord', 'vaccineAdministered','fullAddress','medicalRecord'))
            ->setPaper('A4')  // 8.5" x 13"
            ->setOrientation('portrait')
            ->setOption('enable-local-file-access', true)
            ->setOption('javascript-delay', 500)
            ->setOption('margin-top', 5)      // Reduce margins
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('zoom', 0.85);


        $pdf->setPaper('A4', 'portrait');;
        
           
        return $pdf->download('immunization-card-' .  $medicalRecordId . '.pdf');
    }
}
