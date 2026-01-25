<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_plans;
use App\Models\prenatal_case_records;
use App\Models\senior_citizen_case_records;
use App\Models\staff;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use App\Models\User;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

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
        $startDate = $request->input('startDate', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input("endDate", Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));

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
            ->whereDate('patients.created_at', '>=', $startDate)
            ->whereDate('patients.created_at', '<=', $endDate)
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
            'startDate' => Carbon::parse($startDate)->format('M-d-Y'),
            'endDate' => Carbon::parse($endDate)->format('M-d-Y')
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
        $startDate = $request->input('startDate', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input("endDate", Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));

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
            ->whereDate('patients.created_at', '>=', $startDate)
            ->whereDate('patients.created_at', '<=', $endDate)
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
            'startDate' => Carbon::parse($startDate)->format('M-d-Y'),
            'endDate' => Carbon::parse($endDate)->format('M-d-Y')
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
        $startDate = $request->input('startDate', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input("endDate", Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
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
            ->whereDate('patients.created_at', '>=', $startDate)
            ->whereDate('patients.created_at', '<=', $endDate)
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
            'startDate' => Carbon::parse($startDate)->format('M-d-Y'),
            'endDate' => Carbon::parse($endDate)->format('M-d-Y')
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
        $startDate = $request->input('startDate', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input("endDate", Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
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
            ->whereDate('patients.created_at', '>=', $startDate)
            ->whereDate('patients.created_at', '<=', $endDate)
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
            'startDate' => Carbon::parse($startDate)->format('M-d-Y'),
            'endDate' => Carbon::parse($endDate)->format('M-d-Y')
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
        $startDate = $request->input('startDate', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input("endDate", Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
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
            ->whereDate('patients.created_at', '>=', $startDate)
            ->whereDate('patients.created_at', '<=', $endDate)
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
            'startDate' => Carbon::parse($startDate)->format('M-d-Y'),
            'endDate' => Carbon::parse($endDate)->format('M-d-Y')
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



            // return view('pdf.family-planning.family-planning-side-a', [
            //     'caseInfo' => $familyPlanCaseInfo,
            //     'patient' => $medicalRecord->patient,
            //     'address' => $address,
            //     'medicalRecord' => $medicalRecord,
            // ]);

            // Generate PDF
            $pdf = Pdf::loadView('pdf.family-planning.family-planning-side-a', [
                'caseInfo' => $familyPlanCaseInfo,
                'patient' => $medicalRecord->patient,
                'address' => $address,
                'medicalRecord' => $medicalRecord,
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('family-planning-' . date('Y-m-d') . '.pdf');
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

            // return view('pdf.family-planning.family-planning-side-b', [
            //     'sideBrecord' => $sideBrecord
            // ]);
            $pdf = Pdf::loadView('pdf.family-planning.family-planning-side-b', [
                'sideBrecord' => $sideBrecord
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('family-planning-side-b-' . date('Y-m-d') . '.pdf');
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
                if (Auth::user()->role == 'patient') {
                    $staffInfo = staff::where("user_id", $vaccinationCase->health_worker_id)->first();
                    $healthWorkerName = $staffInfo->full_name;
                }
            }
            $vaccineAdministered = vaccineAdministered::where(
                'vaccination_case_record_id',
                $vaccinationCase->id
            )->get();

            // return view('pdf.vaccination.vaccination-case', [
            //     'vaccinationCase' => $vaccinationCase,
            //     'vaccineAdministered' => $vaccineAdministered,
            //     'healthWorkerName' => $healthWorkerName,
            // ]);

            $pdf = Pdf::loadView('pdf.vaccination.vaccination-case', [
                'vaccinationCase' => $vaccinationCase,
                'vaccineAdministered' => $vaccineAdministered,
                'healthWorkerName' => $healthWorkerName,
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('vaccination-case-' . date('Y-m-d') . '.pdf');
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
            $medicalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $case->medical_record_case_id)->first();
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

            // return view('pdf.prenatal.prenatal-case', [
            //     'caseInfo' => $case,
            //     'patient' => $medicalRecord->patient,
            //     'medicalRecord' => $medicalRecord,
            //     'address' => $fullAddress,
            //     'treceLogo' => $treceLogoSrc,
            //     'DOHlogo' => $DOHLogoSrc
            // ]);

            $pdf = Pdf::loadView('pdf.prenatal.prenatal-case', [
                'caseInfo' => $case,
                'patient' => $medicalRecord->patient,
                'medicalRecord' => $medicalRecord,
                'address' => $fullAddress,
                'treceLogo' => $treceLogoSrc,
                'DOHlogo' => $DOHLogoSrc
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
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

            return $pdf->download('prenatal-case-' . Carbon::today()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function generatePregnancyPdf(Request $request)
    {
        $id = $request->input('planId', '');

        try {
            $pregnancyRecord = pregnancy_plans::with('donor_name')->findOrFail($id);
            // HANDLE THE LOGO
            $logoBase64 = base64_encode(file_get_contents(public_path('images/trece_logo.png')));
            $treceLogoSrc = 'data:image/png;base64,' . $logoBase64;

            $DOHlogoBase64 = base64_encode(file_get_contents(public_path('images/DOH_logo.png')));
            $DOHLogoSrc = 'data:image/png;base64,' . $DOHlogoBase64;

            // return view('pdf.prenatal.pregnancy-plan', [
            //     'pregnancyPlan' => $pregnancyRecord,
            //     'treceLogo' => $treceLogoSrc,
            //     'DOHlogo' => $DOHLogoSrc
            // ]);

            $pdf = Pdf::loadView('pdf.prenatal.pregnancy-plan', [
                'pregnancyPlan' => $pregnancyRecord,
                'treceLogo' => $treceLogoSrc,
                'DOHlogo' => $DOHLogoSrc
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
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
    public function generatePrenatalCheckupPdf(Request $request)
    {
        $id = $request->input('caseId', '');
        try {
            $pregnancy_checkup = pregnancy_checkups::findOrFail($id);
            $healthWorker = staff::where('user_id', $pregnancy_checkup->health_worker_id)->firstOrFail();

            // return view('pdf.prenatal.prenatal-checkup', [
            //     'pregnancy_checkup_info' => $pregnancy_checkup,
            //     'healthWorker' => $healthWorker
            // ]);

            $pdf = Pdf::loadView('pdf.prenatal.prenatal-checkup', [
                'pregnancy_checkup_info' => $pregnancy_checkup,
                'healthWorker' => $healthWorker
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
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
    public function generateSeniorCitizenCasePdf(Request $request)
    {
        $id = $request->input('caseId', '');
        try {
            $caseRecord = senior_citizen_case_records::with('senior_citizen_maintenance_med')->findOrFail($id);
            $medicalRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record'])->Where('id', $caseRecord->medical_record_case_id)->first();
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
            $patientInfo = patients::where("id", $medicalRecord->patient_id)->first();

            // return view('pdf.senior-citizen.senior-citizen-case', [
            //      'seniorCaseRecord' => $caseRecord,
            //     'address' => $fullAddress,
            //      'medicalRecord' => $medicalRecord
            // ]);
            $pdf = Pdf::loadView('pdf.senior-citizen.senior-citizen-case', [
                'seniorCaseRecord' => $caseRecord,
                'address' => $fullAddress,
                'medicalRecord' => $medicalRecord
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('zoom', 0.85);

            return $pdf->download('senior-citizen-case-' . Carbon::today()->format('Y-m-d') . '.pdf');
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

    public function generateTbDotsCasePdf(Request $request)
    {
        $id = $request->input('caseId', '');

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

            // return view('pdf.tb-dots.tb-dots-case', [
            //     'caseRecord' => $caseRecord,
            //     'healthWorker' => $healthWorker,
            //     'address' => $fullAddress,
            //     'medicalRecord' => $medicalRecord
            // ]);

            $pdf = Pdf::loadView('pdf.tb-dots.tb-dots-case', [
                'caseRecord' => $caseRecord,
                'healthWorker' => $healthWorker,
                'address' => $fullAddress,
                'medicalRecord' => $medicalRecord
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
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
    public function generateTbDotsCheckupPdf(Request $request)
    {
        $id = $request->input("checkupId", '');

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

            // return view('pdf.tb-dots.check-up', [
            //     'checkUpRecord' => $checkUpRecord,
            //     'address' => $fullAddress,
            //     'medicalRecord' => $medicalRecord
            // ]);

            $pdf = Pdf::loadView('pdf.tb-dots.check-up', [
                'checkUpRecord' => $checkUpRecord,
                'address' => $fullAddress,
                'medicalRecord' => $medicalRecord
            ])
                ->setPaper('A4', 'portrait')  // 8.5" x 13"
                // ->setOrientation('portrait')
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

    public function generateVaccinationMasterlist(Request $request)
    {
        try {
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

            // get the assigned
           $healthWorkerFullName = null;
            if ($request->filled('selectedBrgy')) {
                $assignedArea = brgy_unit::where("brgy_unit", $request->selectedBrgy)->first();

                if ($assignedArea) {
                    $staff = staff::where("assigned_area_id", $assignedArea->id)->first();
                    $healthWorkerFullName = $staff ? $staff->full_name : null;
                }
            }

            $assignedArea = null;
            if (Auth::user()->role == 'staff' && Auth::user()->staff) {
                $assignedArea = brgy_unit::find(Auth::user()->staff->assigned_area_id)?->brgy_unit;
            }

            // midwife name
            $midwife= User::where('role','nurse')->first();
            $midwifeName = null;
            if($midwife){
                $midwifeName = $midwife->nurses->full_name;
            }
            
            
            $data = [
                'vaccinationMasterlist' => $vaccinationMasterlist,
                'selectedRange' => $selectedRange,
                'selectedBrgy' => $request->selectedBrgy ?? '',
                'filterMonth' => $request->filterMonth ?? '',
                'filterYear' => $request->filterYear ?? '',
                'brgys' => $brgys,
                'years' => $years,
                'entries' => $request->entries ?? '',
                'search' => $request->search ?? '',
                'healthWorkerFullName' => $healthWorkerFullName,
                'assignedArea' => $assignedArea,
                'midwifeName'=> $midwifeName
            ];

            

            // return view('pdf.masterlist.vaccination', $data);

            $pdf = Pdf::loadView('pdf.masterlist.vaccination', $data)
                ->setPaper('legal', 'landscape')  // 8.5" x 13"
                // ->setOrientation('landscape')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 5)
                ->setOption('margin-right', 5)
                ->setOption('zoom', 0.85);

            return $pdf->download('vaccination-masterlist-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }
    public function generateWraMasterlist(Request $request)
    {
        try {
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
            // $query->orderBy($request->sortField, $request->sortDirection);
            $query->orderBy('name_of_wra', 'ASC');

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

            // midwife name
            $midwife = User::where('role', 'nurse')->first();
            $midwifeName = null;
            if ($midwife) {
                $midwifeName = $midwife->nurses->full_name;
            }

            $data = [
                'page' => 'WOMEN OF REPRODUCTIVE AGE',
                'pageHeader' => 'MASTERLIST',
                'masterlistRecords' => $wra_masterList,
                'selectedBrgy' => $request->selectedBrgy ?? '',
                'selectedMonth' => $request->selectedMonth ?? 'January - December',
                'selectedYear' => $request->selectedYear ?? '2025',
                'entries' => $request->entries ?? '',
                'search' => $request->search ?? '',
                'monthName' => $request->monthName ?? '',
                'midwifeName' => $midwifeName
            ];

            // return view('pdf.masterlist.wra', $data);

            $pdf = Pdf::loadView('pdf.masterlist.wra', $data)
                ->setPaper('legal','landscape')  // 8.5" x 13"
                // ->setOrientation('landscape')
                ->setOption('enable-local-file-access', true)
                ->setOption('javascript-delay', 500)
                ->setOption('margin-top', 5)      // Reduce margins
                ->setOption('margin-bottom', 5)
                ->setOption('margin-left', 5)
                ->setOption('margin-right', 5)
                ->setOption('zoom', 0.85);


            return $pdf->download('wra-masterlist-' . date('m-d-Y') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    // generate the nurse report
    public function generateDashboardTable(Request $request)
    {
        $patientCount = $this->patientCount();
        $patientPerDay = $this->patientAddedToday();

        // Get age and sex distributions
        $ageData = $this->getAgeDistribution($request);
        $sexData = $this->getSexDistribution($request);

        $generatedDate = now()->format('F d, Y h:i A');
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $pdf = Pdf::loadView('pdf.dashboard.nurse-tables', compact(
            'patientCount',
            'patientPerDay',
            'ageData',
            'sexData',
            'generatedDate',
            'startDate',
            'endDate'
        ))
            ->setPaper('letter', 'portrait')
            ->setOption('enable-local-file-access', true)
            ->setOption('javascript-delay', 500)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('zoom', 0.85);

        return $pdf->download('patient-detailed-report-' . date('m-d-Y') . '.pdf');
    }
    public function generateDashboardGraph(Request $request)
    {
        // Get BAR CHART date range
        $barStartDate = $request->input('bar_start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $barEndDate = $request->input('bar_end_date', Carbon::now()->endOfYear()->format('Y-m-d'));

        // Get PIE CHART date range
        $pieStartDate = $request->input('pie_start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $pieEndDate = $request->input('pie_end_date', Carbon::now()->endOfYear()->format('Y-m-d'));

        // Get the selected patient type (default to 'all')
        $selectedType = $request->input('patient_type', 'all');

        try {
            $user = Auth::user();

            // ===== BAR CHART DATA =====
            $query = medical_record_cases::with('patient')
                ->whereHas('patient', function ($q) {
                    $q->where('status', '!=', 'Archived');
                })
                ->where('status', '!=', 'Archived')
                ->whereDate('created_at', '>=', $barStartDate)
                ->whereDate('created_at', '<=', $barEndDate);

            // If staff, filter by health worker
            if ($user->role === 'staff') {
                $staffId = $user->id;
                $query->where(function ($q) use ($staffId) {
                    $q->whereHas('vaccination_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    })
                        ->orWhereHas('prenatal_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('senior_citizen_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('tb_dots_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('family_planning_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        });
                });
            }

            // Get all records for bar chart
            $records = $query->get();

            // Process data
            $monthlyData = [];
            foreach ($records as $record) {
                $yearMonth = Carbon::parse($record->created_at)->format('Y-m');
                $type = $record->type_of_case;

                if (!isset($monthlyData[$yearMonth])) {
                    $monthlyData[$yearMonth] = [];
                }
                if (!isset($monthlyData[$yearMonth][$type])) {
                    $monthlyData[$yearMonth][$type] = 0;
                }
                $monthlyData[$yearMonth][$type]++;
            }

            // Generate months for bar chart
            $start = Carbon::parse($barStartDate);
            $end = Carbon::parse($barEndDate);
            $uniqueMonths = [];
            $monthLabels = [];

            while ($start <= $end) {
                $yearMonth = $start->format('Y-m');
                $uniqueMonths[] = $yearMonth;
                $monthLabels[] = $start->format('M Y');
                $start->addMonth();
            }

            // Build result
            $caseMap = [
                'vaccination'     => 'vaccination',
                'prenatal'        => 'prenatal',
                'senior'          => 'senior-citizen',
                'tb'              => 'tb-dots',
                'family_planning' => 'family-planning',
            ];

            $patientData = [
                'all' => [
                    'label' => 'All Patients',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'vaccination' => [
                    'label' => 'Vaccination',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'prenatal' => [
                    'label' => 'Prenatal Care',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'senior' => [
                    'label' => 'Senior Citizen',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'tb' => [
                    'label' => 'TB Treatment',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'family_planning' => [
                    'label' => 'Family Planning',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
            ];

            foreach ($uniqueMonths as $index => $yearMonth) {
                if (isset($monthlyData[$yearMonth])) {
                    foreach ($monthlyData[$yearMonth] as $type => $count) {
                        $patientData['all']['data'][$index] += $count;
                        $key = array_search($type, $caseMap);
                        if ($key !== false) {
                            $patientData[$key]['data'][$index] = $count;
                        }
                    }
                }
            }

            // ===== PIE CHART DATA =====
            $baseQuery = medical_record_cases::with('patient')
                ->whereHas('patient', function ($q) {
                    $q->where('status', '!=', 'Archived');
                })
                ->where('status', '!=', 'Archived')
                ->whereDate('created_at', '>=', $pieStartDate)
                ->whereDate('created_at', '<=', $pieEndDate);

            if ($user->role === 'staff') {
                $staffId = $user->id;
                $baseQuery->where(function ($q) use ($staffId) {
                    $q->whereHas('vaccination_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    })
                        ->orWhereHas('prenatal_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('senior_citizen_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('tb_dots_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('family_planning_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        });
                });
            }

            $pieData = [
                'vaccinationCount' => (clone $baseQuery)->where('type_of_case', 'vaccination')->count(),
                'prenatalCount' => (clone $baseQuery)->where('type_of_case', 'prenatal')->count(),
                'seniorCitizenCount' => (clone $baseQuery)->where('type_of_case', 'senior-citizen')->count(),
                'tbDotsCount' => (clone $baseQuery)->where('type_of_case', 'tb-dots')->count(),
                'familyPlanningCount' => (clone $baseQuery)->where('type_of_case', 'family-planning')->count(),
            ];

            //  Calculate totals for each patient type for the bar chart
            $patientTypeTotals = [
                'vaccination' => array_sum($patientData['vaccination']['data']),
                'prenatal' => array_sum($patientData['prenatal']['data']),
                'senior' => array_sum($patientData['senior']['data']),
                'tb' => array_sum($patientData['tb']['data']),
                'family_planning' => array_sum($patientData['family_planning']['data']),
            ];

            // Format BOTH date ranges for display
            $barDateRangeText = Carbon::parse($barStartDate)->format('M d, Y') . ' - ' . Carbon::parse($barEndDate)->format('M d, Y');
            $pieDateRangeText = Carbon::parse($pieStartDate)->format('M d, Y') . ' - ' . Carbon::parse($pieEndDate)->format('M d, Y');

            //  Return view with patientTypeTotals
            return view('pdf.dashboard.graph-table', compact(
                'patientData',
                'pieData',
                'barDateRangeText',
                'pieDateRangeText',
                'selectedType',
                'patientTypeTotals'  // ← ADD THIS
            ));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    private function patientCount()
    {

        if (Auth::user()->role == 'nurse') {
            try {

                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', '!=', 'Archived')
                    ->where('medical_record_cases.status', '!=', 'Archived');

                $totalPatient = (clone $baseQuery)
                    ->count();

                $types = (clone $baseQuery)
                    ->select('medical_record_cases.type_of_case', DB::raw('COUNT(*) as total'))
                    ->groupBy('medical_record_cases.type_of_case')
                    ->get();
                $vaccination = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'vaccination')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $prenatal = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'prenatal')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $tbDots = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'tb-dots')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $familyPlanning = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'family-planning')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $seniorCitizen = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'senior-citizen')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');


                return  [
                    'overallPatients' => $totalPatient,
                    'vaccinationCount' => $vaccination,
                    'prenatalCount' => $prenatal,
                    'tbDotsCount' => $tbDots,
                    'seniorCitizenCount' => $seniorCitizen,
                    'familyPlanningCount' => $familyPlanning,
                    'types' => $types
                ];
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        if (Auth::user()->role == 'staff') {

            try {

                $staffId = Auth::user()->id;
                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', '!=', 'Archived')
                    ->where('medical_record_cases.status', '!=', 'Archived');


                $types = (clone $baseQuery)
                    ->select('medical_record_cases.type_of_case', DB::raw('COUNT(*) as total'))
                    ->groupBy('medical_record_cases.type_of_case')
                    ->get();
                $vaccination = (clone $baseQuery)
                    ->join('vaccination_medical_records as v', 'v.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('v.health_worker_id', $staffId) // filter by staff
                    ->where('medical_record_cases.type_of_case', 'vaccination')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $prenatal = (clone $baseQuery)
                    ->join('prenatal_medical_records as p', 'p.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('p.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'prenatal')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $tbDots = (clone $baseQuery)
                    ->join('tb_dots_medical_records as t', 't.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('t.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'tb-dots')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $familyPlanning = (clone $baseQuery)
                    ->join('family_planning_medical_records as f', 'f.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('f.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'family-planning')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $seniorCitizen = (clone $baseQuery)
                    ->join('senior_citizen_medical_records as s', 's.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('s.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'senior-citizen')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $totalPatient = $vaccination + $prenatal +  $tbDots + $familyPlanning + $seniorCitizen;

                return [
                    'overallPatients' => $totalPatient,
                    'vaccinationCount' => $vaccination,
                    'prenatalCount' => $prenatal,
                    'tbDotsCount' => $tbDots,
                    'seniorCitizenCount' => $seniorCitizen,
                    'familyPlanningCount' => $familyPlanning,
                    'types' => $types
                ];
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return [];
    }
    private function patientCountPerArea($startDate = null, $endDate = null)
    {

        try {
            $data = [];
            $caseTypes = ['vaccination', 'prenatal', 'senior-citizen', 'tb-dots', 'family-planning'];

            // Base query
            $baseQuery = patient_addresses::query()
                ->join('medical_record_cases', 'patient_addresses.patient_id', '=', 'medical_record_cases.patient_id')
                ->join('patients', 'patient_addresses.patient_id', '=', 'patients.id')
                ->where('medical_record_cases.status', '!=', 'Archived')
                ->where('patient_addresses.barangay', 'Hugo Perez')
                ->where('patients.status', '!=', 'Archived')
                ->whereNotNull('patient_addresses.purok');
                

            // Add date range filter if provided
            if ($startDate && $endDate) {
                $baseQuery->whereDate('patients.created_at', '>=', $startDate)
                    ->whereDate('patients.created_at', '<=', $endDate);
            }

            $brgyUnits = brgy_unit::get();

            if (Auth::user()->role == 'nurse') {
                foreach ($brgyUnits as $unit) {
                    $data[$unit->brgy_unit] = [];

                    foreach ($caseTypes as $type) {
                        $count = (clone $baseQuery)
                            ->where('patient_addresses.purok', $unit->brgy_unit)
                            ->where('medical_record_cases.type_of_case', $type)
                            ->distinct()
                            ->count('patient_addresses.patient_id');

                        $data[$unit->brgy_unit][$type] = $count;
                    }
                }
            }

            if (Auth::user()->role == 'staff') {
                $staffId = Auth::user()->id;

                foreach ($brgyUnits as $unit) {
                    $data[$unit->brgy_unit] = [];

                    foreach ($caseTypes as $type) {
                        $query = (clone $baseQuery)
                            ->where('patient_addresses.purok', $unit->brgy_unit)
                            ->where('medical_record_cases.type_of_case', $type);

                        // Join the appropriate medical record table based on type
                        switch ($type) {
                            case 'vaccination':
                                $query->join('vaccination_medical_records as vmr', 'vmr.medical_record_case_id', '=', 'medical_record_cases.id')
                                    ->where('vmr.health_worker_id', $staffId);
                                break;
                            case 'prenatal':
                                $query->join('prenatal_medical_records as pmr', 'pmr.medical_record_case_id', '=', 'medical_record_cases.id')
                                    ->where('pmr.health_worker_id', $staffId);
                                break;
                            case 'senior-citizen':
                                $query->join('senior_citizen_medical_records as smr', 'smr.medical_record_case_id', '=', 'medical_record_cases.id')
                                    ->where('smr.health_worker_id', $staffId);
                                break;
                            case 'tb-dots':
                                $query->join('tb_dots_medical_records as tmr', 'tmr.medical_record_case_id', '=', 'medical_record_cases.id')
                                    ->where('tmr.health_worker_id', $staffId);
                                break;
                            case 'family-planning':
                                $query->join('family_planning_medical_records as fmr', 'fmr.medical_record_case_id', '=', 'medical_record_cases.id')
                                    ->where('fmr.health_worker_id', $staffId);
                                break;
                        }

                        $count = $query->distinct()->count('patient_addresses.patient_id');
                        $data[$unit->brgy_unit][$type] = $count;
                    }
                }
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Patient count per area error: ' . $e->getMessage());
            return [];
        }
    }
    public function generatePatientCountReport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $data = $this->patientCountPerArea($startDate, $endDate);

        $pdf = PDF::loadView('pdf.areaReport.patient-count-per-area-report', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedDate' => now()->format('F d, Y')
        ]);

        return $pdf->download('patient-count-per-area-report.pdf');
    }
    private function patientAddedToday()
    {
        try {
            $user = Auth::user();
            $today = Carbon::today();

            // Base query (shared)
            $baseQuery = medical_record_cases::query()
                ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                ->where('patients.status', '!=', 'Archived')
                ->where('medical_record_cases.status', '!=', 'Archived')
                ->whereDate('medical_record_cases.created_at', $today);

            /**
             * ✅ If STAFF: filter by health_worker_id across tables
             */
            if ($user->role === 'staff') {
                $staffId = $user->id;

                $baseQuery
                    ->leftJoin('vaccination_medical_records as v', function ($join) use ($staffId) {
                        $join->on('v.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('v.health_worker_id', $staffId);
                    })
                    ->leftJoin('prenatal_medical_records as p', function ($join) use ($staffId) {
                        $join->on('p.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('p.health_worker_id', $staffId);
                    })
                    ->leftJoin('senior_citizen_medical_records as s', function ($join) use ($staffId) {
                        $join->on('s.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('s.health_worker_id', $staffId);
                    })
                    ->leftJoin('tb_dots_medical_records as t', function ($join) use ($staffId) {
                        $join->on('t.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('t.health_worker_id', $staffId);
                    })
                    ->leftJoin('family_planning_medical_records as f', function ($join) use ($staffId) {
                        $join->on('f.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('f.health_worker_id', $staffId);
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('v.id')
                            ->orWhereNotNull('p.id')
                            ->orWhereNotNull('s.id')
                            ->orWhereNotNull('t.id')
                            ->orWhereNotNull('f.id');
                    });
            }

            // ✅ Overall
            $totalPatient = (clone $baseQuery)->count();

            // ✅ Grouped types
            $types = (clone $baseQuery)
                ->select('medical_record_cases.type_of_case', DB::raw('COUNT(DISTINCT medical_record_cases.id) as total'))
                ->groupBy('medical_record_cases.type_of_case')
                ->get();

            // ✅ Individual counts
            $vaccination = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'vaccination')
                ->count('medical_record_cases.id');

            $prenatal = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'prenatal')
                ->count('medical_record_cases.id');

            $tbDots = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'tb-dots')
                ->count('medical_record_cases.id');

            $familyPlanning = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'family-planning')
                ->count('medical_record_cases.id');

            $seniorCitizen = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'senior-citizen')
                ->count('medical_record_cases.id');

            return [
                'overallPatients'     => $totalPatient,
                'vaccinationCount'    => $vaccination,
                'prenatalCount'       => $prenatal,
                'tbDotsCount'         => $tbDots,
                'seniorCitizenCount'  => $seniorCitizen,
                'familyPlanningCount' => $familyPlanning,
                'types'               => $types,
            ];
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    private function monthlyPatientStats()
    {
        $user = Auth::user();
        $staffId = $user->id; // change if your staff table uses different FK

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $caseMap = [
            'vaccination'     => 'vaccination',
            'prenatal'        => 'prenatal',
            'senior'          => 'senior-citizen',
            'tb'              => 'tb-dots',
            'family_planning' => 'family-planning',
        ];

        // Base query
        $baseQuery = medical_record_cases::query()
            ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', '!=', 'Archived')
            ->whereYear('medical_record_cases.created_at', now()->year);

        /**
         * ✅ Only apply joins + filters if user is STAFF
         */
        if ($user->role === 'staff') {

            $baseQuery->leftJoin('vaccination_medical_records as v', function ($join) use ($staffId) {
                $join->on('v.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('v.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('prenatal_medical_records as p', function ($join) use ($staffId) {
                $join->on('p.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('p.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('senior_citizen_medical_records as s', function ($join) use ($staffId) {
                $join->on('s.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('s.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('tb_dots_medical_records as t', function ($join) use ($staffId) {
                $join->on('t.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('t.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('family_planning_medical_records as f', function ($join) use ($staffId) {
                $join->on('f.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('f.health_worker_id', '=', $staffId);
            });

            // ✅ Important: filter to only records where staff handled at least one case
            $baseQuery->where(function ($q) {
                $q->whereNotNull('v.id')
                    ->orWhereNotNull('p.id')
                    ->orWhereNotNull('s.id')
                    ->orWhereNotNull('t.id')
                    ->orWhereNotNull('f.id');
            });
        }

        // Final query
        $raw = $baseQuery
            ->selectRaw("
            MONTH(medical_record_cases.created_at) as month,
            medical_record_cases.type_of_case,
            COUNT(DISTINCT medical_record_cases.id) as total
        ")
            ->groupByRaw("MONTH(medical_record_cases.created_at), medical_record_cases.type_of_case")
            ->get();

        // Result skeleton
        $result = [
            'all' => ['label' => 'All Patients', 'data' => array_fill(0, 12, 0)],
            'vaccination' => ['label' => 'Vaccination', 'data' => array_fill(0, 12, 0)],
            'prenatal' => ['label' => 'Prenatal Care', 'data' => array_fill(0, 12, 0)],
            'senior' => ['label' => 'Senior Citizen', 'data' => array_fill(0, 12, 0)],
            'tb' => ['label' => 'TB Treatment', 'data' => array_fill(0, 12, 0)],
            'family_planning' => ['label' => 'Family Planning', 'data' => array_fill(0, 12, 0)],
        ];

        // Fill values
        foreach ($raw as $row) {
            $monthIndex = $row->month - 1;
            $type = $row->type_of_case;

            $result['all']['data'][$monthIndex] += $row->total;

            $key = array_search($type, $caseMap);
            if ($key !== false) {
                $result[$key]['data'][$monthIndex] = $row->total;
            }
        }

        return $result;
    }

    // for socio demographic report
    public function getAgeDistribution(Request $request)
    {
        $user = Auth::user();
        $staffId = $user->id;

        $startDate = $request->input('start_date', Carbon::now()->startOfYear());
        $endDate = $request->input('end_date', Carbon::now()->endOfYear());

        // Start building the query
        $query = medical_record_cases::query()
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', '!=', 'Archived')
            ->whereBetween('patients.created_at', [$startDate, $endDate]);

        // Apply staff filter using leftJoin approach (matching patientAddedToday pattern)
        if ($user->role === 'staff') {
            $query
                ->leftJoin('vaccination_medical_records as v', function ($join) use ($staffId) {
                    $join->on('v.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('v.health_worker_id', $staffId);
                })
                ->leftJoin('prenatal_medical_records as p', function ($join) use ($staffId) {
                    $join->on('p.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('p.health_worker_id', $staffId);
                })
                ->leftJoin('senior_citizen_medical_records as s', function ($join) use ($staffId) {
                    $join->on('s.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('s.health_worker_id', $staffId);
                })
                ->leftJoin('tb_dots_medical_records as t', function ($join) use ($staffId) {
                    $join->on('t.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('t.health_worker_id', $staffId);
                })
                ->leftJoin('family_planning_medical_records as f', function ($join) use ($staffId) {
                    $join->on('f.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('f.health_worker_id', $staffId);
                })
                ->where(function ($q) {
                    $q->whereNotNull('v.id')
                        ->orWhereNotNull('p.id')
                        ->orWhereNotNull('s.id')
                        ->orWhereNotNull('t.id')
                        ->orWhereNotNull('f.id');
                });
        }

        // Execute query and get records with patient data
        $records = $query->select('medical_record_cases.*', 'patients.date_of_birth', 'patients.sex')
            ->with('patient')
            ->get();

        // Initialize result structure
        $ageDistribution = [
            'vaccination' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'prenatal' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'seniorCitizen' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'tbDots' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'familyPlanning' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
        ];

        $totals = [
            '0-11' => 0,
            '1-5' => 0,
            '6-17' => 0,
            '18-59' => 0,
            '60+' => 0,
        ];

        // Process each record
        foreach ($records as $record) {
            if (!$record->patient || !$record->patient->date_of_birth) {
                continue;
            }

            $age = Carbon::parse($record->patient->date_of_birth)->age;
            $ageGroup = $this->getAgeGroup($age);
            $caseType = $this->mapCaseType($record->type_of_case);

            if ($caseType && isset($ageDistribution[$caseType][$ageGroup])) {
                $ageDistribution[$caseType][$ageGroup]++;
                $totals[$ageGroup]++;
            }
        }

        return [
            'distribution' => $ageDistribution,
            'totals' => $totals
        ];
    }

    public function getSexDistribution(Request $request)
    {
        $user = Auth::user();
        $staffId = $user->id;

        $startDate = $request->input('start_date', Carbon::now()->startOfYear());
        $endDate = $request->input('end_date', Carbon::now()->endOfYear());

        // Start building the query
        $query = medical_record_cases::query()
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', '!=', 'Archived')
            ->whereBetween('patients.created_at', [$startDate, $endDate]);

        // Apply staff filter using leftJoin approach
        if ($user->role === 'staff') {
            $query
                ->leftJoin('vaccination_medical_records as v', function ($join) use ($staffId) {
                    $join->on('v.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('v.health_worker_id', $staffId);
                })
                ->leftJoin('prenatal_medical_records as p', function ($join) use ($staffId) {
                    $join->on('p.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('p.health_worker_id', $staffId);
                })
                ->leftJoin('senior_citizen_medical_records as s', function ($join) use ($staffId) {
                    $join->on('s.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('s.health_worker_id', $staffId);
                })
                ->leftJoin('tb_dots_medical_records as t', function ($join) use ($staffId) {
                    $join->on('t.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('t.health_worker_id', $staffId);
                })
                ->leftJoin('family_planning_medical_records as f', function ($join) use ($staffId) {
                    $join->on('f.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where('f.health_worker_id', $staffId);
                })
                ->where(function ($q) {
                    $q->whereNotNull('v.id')
                        ->orWhereNotNull('p.id')
                        ->orWhereNotNull('s.id')
                        ->orWhereNotNull('t.id')
                        ->orWhereNotNull('f.id');
                });
        }

        // Execute query and get records with patient data
        $records = $query->select('medical_record_cases.*', 'patients.sex')
            ->with('patient')
            ->get();

        // Initialize result structure
        $sexDistribution = [
            'vaccination' => ['Male' => 0, 'Female' => 0],
            'prenatal' => ['Male' => 0, 'Female' => 0],
            'seniorCitizen' => ['Male' => 0, 'Female' => 0],
            'tbDots' => ['Male' => 0, 'Female' => 0],
            'familyPlanning' => ['Male' => 0, 'Female' => 0],
        ];

        $totals = [
            'Male' => 0,
            'Female' => 0,
        ];

        // Process each record
        foreach ($records as $record) {
            if (!$record->patient || !$record->patient->sex) {
                continue;
            }

            $sex = ucfirst(strtolower($record->patient->sex));
            $caseType = $this->mapCaseType($record->type_of_case);

            if ($caseType && isset($sexDistribution[$caseType][$sex])) {
                $sexDistribution[$caseType][$sex]++;
                $totals[$sex]++;
            }
        }

        return [
            'distribution' => $sexDistribution,
            'totals' => $totals
        ];
    }

    private function getAgeGroup($age)
    {
        if ($age < 1) {
            return '0-11'; // 0-11 months
        } elseif ($age >= 1 && $age <= 5) {
            return '1-5';
        } elseif ($age >= 6 && $age <= 17) {
            return '6-17';
        } elseif ($age >= 18 && $age <= 59) {
            return '18-59';
        } else {
            return '60+';
        }
    }

    private function mapCaseType($typeOfCase)
    {
        $mapping = [
            'vaccination' => 'vaccination',
            'prenatal' => 'prenatal',
            'senior-citizen' => 'seniorCitizen',
            'tb-dots' => 'tbDots',
            'family-planning' => 'familyPlanning',
        ];

        return $mapping[$typeOfCase] ?? null;
    }
}
