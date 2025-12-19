<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_plans;
use App\Models\prenatal_case_records;
use App\Models\senior_citizen_case_records;
use App\Models\staff;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccineAdministered;
use App\Models\wra_masterlists;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    //

    public function generateVaccinationPdf(Request $request)
    {
        // Get parameters - add dd() to debug
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'created_at');
        $sortDirection = $request->input('sortDirection', 'asc');
        $entriesPerPage = $request->input('entries', 10);

        // Debug: Check what we received
        // dd([
        //     'search' => $search,
        //     'sortField' => $sortField,
        //     'sortDirection' => $sortDirection,
        // ]);

        // Define allowed sort fields
        $allowedSortFields = [
            'created_at',
            'full_name',
            'age',
            'sex',
            'contact_number',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        ];

        // Validate
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Query
        $vaccinationRecord = medical_record_cases::select(
            'medical_record_cases.*',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        )
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'vaccination')
            ->where('patients.full_name', 'like', '%' . $search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('vaccination_medical_records', 'vaccination_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('vaccination_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy($sortField, $sortDirection)
            ->get();

        // Split into pages
        $recordPages = $vaccinationRecord->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.vaccination-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' => $vaccinationRecord->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('vaccination-records-' . date('Y-m-d') . '.pdf');
    }
    public function generatePrenatalPdf(Request $request)
    {
        // Get parameters - add dd() to debug
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'created_at');
        $sortDirection = $request->input('sortDirection', 'asc');
        $entriesPerPage = $request->input('entries', 10);

        // Debug: Check what we received
        // dd([
        //     'search' => $search,
        //     'sortField' => $sortField,
        //     'sortDirection' => $sortDirection,
        // ]);

        // Define allowed sort fields
        $allowedSortFields = [
            'created_at',
            'full_name',
            'age',
            'sex',
            'contact_number',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        ];

        // Validate
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $prenatalRecord = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'prenatal')
            ->where('patients.full_name', 'like', '%' . $search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                // Add join to vaccination_medical_records to filter by health_worker_id
                $query->join('prenatal_medical_records', 'prenatal_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('prenatal_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy($sortField, $sortDirection)
            ->get();

        $recordPages =  $prenatalRecord->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.prenatal-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $prenatalRecord->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('prenatal-records-' . date('Y-m-d') . '.pdf');
    }
    public function generateSeniorCitizenPdf(Request $request)
    {
        // Get parameters - add dd() to debug
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'created_at');
        $sortDirection = $request->input('sortDirection', 'asc');
        $entriesPerPage = $request->input('entries', 10);

        // Debug: Check what we received
        // dd([
        //     'search' => $search,
        //     'sortField' => $sortField,
        //     'sortDirection' => $sortDirection,
        // ]);

        // Define allowed sort fields
        $allowedSortFields = [
            'created_at',
            'full_name',
            'age',
            'sex',
            'contact_number',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        ];

        // Validate
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        $seniorCitizenRecords = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'senior-citizen')
            ->where('patients.full_name', 'like', '%' . $search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                // Add join to vaccination_medical_records to filter by health_worker_id
                $query->join('senior_citizen_medical_records', 'senior_citizen_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('senior_citizen_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy($sortField, $sortDirection)
            ->get();
        $recordPages =  $seniorCitizenRecords->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.senior-citizen-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $seniorCitizenRecords->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('senior-citizen-records-' . date('Y-m-d') . '.pdf');
    }
    public function generateTbDotsPdf(Request $request)
    {
        // Get parameters - add dd() to debug
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'created_at');
        $sortDirection = $request->input('sortDirection', 'asc');
        $entriesPerPage = $request->input('entries', 10);

        // Debug: Check what we received
        // dd([
        //     'search' => $search,
        //     'sortField' => $sortField,
        //     'sortDirection' => $sortDirection,
        // ]);

        // Define allowed sort fields
        $allowedSortFields = [
            'created_at',
            'full_name',
            'age',
            'sex',
            'contact_number',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        ];

        // Validate
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        $tbRecords =  medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'tb-dots')
            ->where('patients.full_name', 'like', '%' . $search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                // Add join to vaccination_medical_records to filter by health_worker_id
                $query->join('tb_dots_medical_records', 'tb_dots_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('tb_dots_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy($sortField, $sortDirection)
            ->get();
        $recordPages =  $tbRecords->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.tb-dots-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $tbRecords->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('tb-dots-records-' . date('Y-m-d') . '.pdf');
    }
    public function generateFamilyPlanningPdf(Request $request)
    {
        // Get parameters - add dd() to debug
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'created_at');
        $sortDirection = $request->input('sortDirection', 'asc');
        $entriesPerPage = $request->input('entries', 10);

        // Debug: Check what we received
        // dd([
        //     'search' => $search,
        //     'sortField' => $sortField,
        //     'sortDirection' => $sortDirection,
        // ]);

        // Define allowed sort fields
        $allowedSortFields = [
            'created_at',
            'full_name',
            'age',
            'sex',
            'contact_number',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        ];

        // Validate
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $familyPlanning = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'family-planning')
            ->where('patients.full_name', 'like', '%' . $search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                // Add join to vaccination_medical_records to filter by health_worker_id
                $query->join('family_planning_medical_records', 'family_planning_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('family_planning_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy("patients.$sortField", $sortDirection)
            ->get();

        $recordPages =  $familyPlanning->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.family-planning-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $familyPlanning->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('family-planning-records-' . date('Y-m-d') . '.pdf');
    }

    // generate pdf for family planning side A
    public function generateFamilyPlanningSideAPdf(Request $request)
    {

        $id = $request->input("caseId", '');

        if (!$id) {
            return response()->json(['error' => 'Case ID is required'], 400);
        }

        try {
            $familyPlanCaseInfo = family_planning_case_records::with([
                'medical_history',
                'obsterical_history',
                'risk_for_sexually_transmitted_infection',
                'physical_examinations'
            ])->findOrFail($id);
            $medicalRecord = medical_record_cases::with('patient')->where('id', $familyPlanCaseInfo->medical_record_case_id)->first();
            $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();

            // $pdf = PDF::loadView('pdf.family-planning.family-planning-side-a', [
            //     'caseInfo' => $familyPlanCaseInfo,
            //     'medicalRecord' => $medicalRecord,
            //     'patient' => $medicalRecord->patient,
            //     'address' => $address
            // ]);

            // Generate PDF
            $pdf = SnappyPdf::loadView('pdf.family-planning.family-planning-side-a', [
                'caseInfo' => $familyPlanCaseInfo,
                'patient' => $medicalRecord->patient,
                'address' => $address,
                'medicalRecord' => $medicalRecord,
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('family-planning-' . $id . '.pdf');
        } catch (\Exception $e) {
            // \Log::error('PDF Generation Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function generateFamilyPlanningSideBPdf(Request $request)
    {
        $id = $request->input('caseId', '');

        if (!$id) {
            return response()->json(['error' => 'Case ID is required'], 400);
        }

        try {
            $sideBrecord = family_planning_side_b_records::findorFail($id);
            $pdf = SnappyPdf::loadView('pdf.family-planning.family-planning-side-b', [
                'sideBrecord' => $sideBrecord
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('family-planning-side-b-' . $id . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }
    // vacination case
    public function generateVaccinationCasePdf(Request $request)
    {
        $id = $request->input('caseId', '');

        if (!$id) {
            return response()->json(['error' => 'Case ID is required'], 400);
        }

        try {
            $vaccinationCase = vaccination_case_records::findOrFail($id);

            if (Auth::user()->role == 'staff') {
                $staffInfo = staff::where("user_id", Auth::user()->id)->first();
                $healthWorkerName = $staffInfo->full_name;
            } else {
                if (Auth::user()->role == 'nurse') {
                    $staffInfo = staff::where("user_id", $vaccinationCase->health_worker_id)->first();
                    $healthWorkerName = $staffInfo->full_name;
                }
                if(Auth::user()->role == 'patient'){
                    $staffInfo = staff::where("user_id", $vaccinationCase->health_worker_id)->first();
                    $healthWorkerName = $staffInfo->full_name;
                }
            }
            $vaccineAdministered = vaccineAdministered::where(
                'vaccination_case_record_id',
                $vaccinationCase->id
            )->get();

            $pdf = SnappyPdf::loadView('pdf.vaccination.vaccination-case', [
                'vaccinationCase' => $vaccinationCase,
                'vaccineAdministered' => $vaccineAdministered,
                'healthWorkerName' => $healthWorkerName,
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('vaccination-case-' . $id . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    // prenatal case record
    public function generatePrenatalCasePdf(Request $request)
    {
        $id = $request->input('caseId', '');
        try {

            $case = prenatal_case_records::with('pregnancy_timeline_records', 'prenatal_assessment')->findOrFail($id);
            $medicalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])-> where('id',$case->medical_record_case_id)->first();
            $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();
            $fullAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');
            $healthwoker = staff::where('user_id', $case->health_worker_id)->firstOrFail();

            // HANDLE THE LOGO
            $logoBase64 = base64_encode(file_get_contents(public_path('images/trece_logo.png')));
            $treceLogoSrc = 'data:image/png;base64,' . $logoBase64;

            $DOHlogoBase64 = base64_encode(file_get_contents(public_path('images/DOH_logo.png')));
            $DOHLogoSrc = 'data:image/png;base64,' . $DOHlogoBase64;


            $pdf = SnappyPdf::loadView('pdf.prenatal.prenatal-case', [
                'caseInfo' => $case,
                'patient' => $medicalRecord->patient,
                'medicalRecord'=> $medicalRecord,
                'address'=> $fullAddress,
                'treceLogo' => $treceLogoSrc,
                'DOHlogo' => $DOHLogoSrc
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);
           
            // return view('pdf.prenatal.prenatal-case', [
            //     'caseInfo' => $case,
            //     'patient' => $medicalRecord->patient,
            //     'medicalRecord' => $medicalRecord,
            //     'address' => $fullAddress,
            //     'treceLogo' => $treceLogoSrc,
            //     'DOHlogo' => $DOHLogoSrc
            // ]);

            return $pdf->download('prenatal-case-' .Carbon::today()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function generatePregnancyPdf(Request $request){
        $id = $request->input('planId','');

        try {
            $pregnancyRecord = pregnancy_plans::with('donor_name')->findOrFail($id);
            // HANDLE THE LOGO
            $logoBase64 = base64_encode(file_get_contents(public_path('images/trece_logo.png')));
            $treceLogoSrc = 'data:image/png;base64,' . $logoBase64;

            $DOHlogoBase64 = base64_encode(file_get_contents(public_path('images/DOH_logo.png')));
            $DOHLogoSrc = 'data:image/png;base64,' . $DOHlogoBase64;

            $pdf = SnappyPdf::loadView('pdf.prenatal.pregnancy-plan', [
                'pregnancyPlan' => $pregnancyRecord,
                'treceLogo' => $treceLogoSrc,
                'DOHlogo' => $DOHLogoSrc
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('pregnancy-plan-' . Carbon::today()->format('Y-m-d') . '.pdf');
           
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }
    public function generatePrenatalCheckupPdf(Request $request){
        $id = $request->input('caseId','');
        try {
            $pregnancy_checkup = pregnancy_checkups::findOrFail($id);
            $healthWorker = staff::where('user_id', $pregnancy_checkup->health_worker_id)->firstOrFail();

            $pdf = SnappyPdf::loadView('pdf.prenatal.prenatal-checkup', [
                'pregnancy_checkup_info' => $pregnancy_checkup,
                'healthWorker' => $healthWorker
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('pregnancy-check-up-' . Carbon::today()->format('Y-m-d') . '.pdf');
           
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
    // SENIOR CITIZEN
    public function generateSeniorCitizenCasePdf(Request $request){
        $id = $request-> input('caseId','');
        try {
            $caseRecord = senior_citizen_case_records::with('senior_citizen_maintenance_med')->findOrFail($id);
            $medicalRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record'])->Where('id',$caseRecord->medical_record_case_id)->first();
            $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();
            $fullAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');
            $patient_name = $caseRecord->patient_name;

            $pdf = SnappyPdf::loadView('pdf.senior-citizen.senior-citizen-case', [
                'seniorCaseRecord' => $caseRecord,
                'address'=> $fullAddress,
                'medicalRecord'=> $medicalRecord
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);
          
           return $pdf->download('senior-citizen-case-'. Carbon::today()->format('Y-m-d') . '.pdf');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Record not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateTbDotsCasePdf(Request $request){
        $id = $request->input('caseId','');

        try {
            $caseRecord = tb_dots_case_records::with('tb_dots_maintenance_med')->findOrFail($id);
            $healthWorker = staff::where('user_id', $caseRecord->health_worker_id)->firstOrFail();
            $medicalRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->Where('id', $caseRecord->medical_record_case_id)->first();
            $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();
            $fullAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');

            $pdf = SnappyPdf::loadView('pdf.tb-dots.tb-dots-case', [
                'caseRecord' => $caseRecord,
                'healthWorker' => $healthWorker,
                'address' => $fullAddress,
                'medicalRecord' => $medicalRecord
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('tb-dots-case-' . Carbon::today()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
    public function generateTbDotsCheckupPdf(Request $request){
        $id = $request->input("checkupId",'');

        try {
            $checkUpRecord = tb_dots_check_ups::findOrFail($id);
            $medicalRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->Where('id', $checkUpRecord->medical_record_case_id)->first();
            $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();
            $fullAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');

            $pdf = SnappyPdf::loadView('pdf.tb-dots.check-up', [
                'checkUpRecord' => $checkUpRecord,
                'address' => $fullAddress,
                'medicalRecord' => $medicalRecord
            ])
                ->setPaper('A4')  // 8.5" x 13"
                ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);


            return $pdf->download('tb-dots-checkup-' . Carbon::today()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function generateVaccinationMasterlist(Request $request){
        try{
            $query = vaccination_masterlists::where('status', '!=', 'Archived');

            // Search filter
            if (!empty($request->filled('search'))) {
                $query->where('name_of_child', 'like', '%' . $request->search . '%');
            }

            // Apply barangay filter
            if ($request->filled('selectedBrgy')) {
                $query->where('brgy_name', $request->selectedBrgy);
            }

            // Apply month filter
            if ($request->filled('filterMonth')) {
                $query->whereMonth('created_at', $request->filterMonth);
            }

            // Apply year filter
            if ($request->filled('filterYear')) {
                $query->whereYear('created_at', $request->filterYear);
            }

            // If user is health worker
            if (Auth::user()->role == 'staff') {
                $query->where('health_worker_id', Auth::id());
            }
            // Age range filter
            $selectedRange = '0-59 Months'; // Default
            if ($request->filled('ageRange')) {
                switch ($request->ageRange) {
                    case '0-4':
                        $query->whereBetween('age', [0, 4]);
                        $selectedRange = '0-59 Months';
                        break;
                    case '5-9':
                        $query->whereBetween('age', [5, 9]);
                        $selectedRange = '5-9 years old';
                        break;
                    case '10-14':
                        $query->whereBetween('age', [10, 14]);
                        $selectedRange = '10-14 years old';
                        break;
                    case '15-49':
                        $query->whereBetween('age', [15, 49]);
                        $selectedRange = '15-49 years old';
                        break;
                }
            }
            $vaccinationMasterlist = $query->orderBy('name_of_child', 'ASC')->get();

            $brgys = brgy_unit::orderBy('brgy_unit', 'ASC')->get();

            // Generate year options (last 10 years)
            $years = range(date('Y'), date('Y') - 10);
            // Prepare data for PDF
            $data = [
                'vaccinationMasterlist' => $vaccinationMasterlist,
                'selectedRange' => $selectedRange,
                'selectedBrgy' => $request->selectedBrgy ?? '',
                'filterMonth' => $request->filterMonth ?? '',
                'filterYear' => $request->filterYear ?? '',
                'brgys' => $brgys,
                'years' => $years,
                'entries' => $request->entries??'',
                'search' => $request->search??''
            ];

            $pdf = SnappyPdf::loadView('pdf.masterlist.vaccination', $data)
                ->setPaper('legal')  // 8.5" x 13"
                ->setOrientation('landscape')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 5)
                ->setOption('margin-right', 5)
                ->setOption('zoom', 0.85);

            return $pdf->download('vaccination-masterlist-' . date('Y-m-d') . '.pdf');
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }
    public function generateWraMasterlist(Request $request){
        try{
            // Build query
            $query = wra_masterlists::where('status', '!=', 'Archived');
            // Search filter
            if (!empty($request->filled('search'))) {
                $query->where('name_of_wra', 'like', '%' . $request->search . '%');
            }

            // Apply barangay filter
            if ($request->filled('selectedBrgy')) {
                $query->where('brgy_name', $request->selectedBrgy);
            }

            // Apply month filter
            if ($request->filled('selectedMonth')) {
                $query->whereMonth('created_at', $request->selectedMonth);
            }

            // Apply year selected
            if ($request->filled('selectedYear')) {
                $query->whereYear('created_at', $request->selectedYear);
            }
            // Search by name
            if ($request->filled('search')) {
                $query->where('name_of_wra', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('withUnmetNeed')) {
                $query->where('wra_with_MFP_unmet_need', $request->withUnmetNeed);
            }


            if (Auth::user()->role == 'staff') {
                $query->where('health_worker_id', Auth::id());
            }

            // Apply sorting
            $query->orderBy($request->sortField, $request->sortDirection);

            // Paginate
            $wra_masterList = $query
                ->get();

            // Get barangay list for dropdown
            $brgyList = brgy_unit::orderBy('brgy_unit', 'ASC')->get();

            // Get available years from data
            $availableYears = wra_masterlists::selectRaw('YEAR(created_at) as year')
                ->where('status', '!=', 'Archived')
                ->distinct()
                ->orderBy('year', 'DESC')
                ->pluck('year');

            $data = [
                'page' => 'WOMEN OF REPRODUCTIVE AGE',
                'pageHeader' => 'MASTERLIST',
                'masterlistRecords' => $wra_masterList,
                'selectedBrgy' => $request->selectedBrgy ?? '',
                'selectedMonth' => $request->selectedMonth ?? '',
                'selectedYear' => $request->seletedYear ?? '',
                'entries' => $request->entries ?? '',
                'search' => $request->search ?? '',
                'monthName' => $request->monthName ??'',
            ];

            $pdf = SnappyPdf::loadView('pdf.masterlist.wra', $data)
                ->setPaper('legal')  // 8.5" x 13"
                ->setOrientation('landscape')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 5)
                ->setOption('margin-right', 5)
                ->setOption('zoom', 0.85);

            
            return $pdf->download('wra-masterlist-'. date('m-d-Y'). '.pdf');
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }
}
