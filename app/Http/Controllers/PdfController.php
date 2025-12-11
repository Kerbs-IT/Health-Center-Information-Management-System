<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
    public function generatePrenatalPdf(Request $request){
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

        $recordPages =  $prenatalRecord ->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.prenatal-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $prenatalRecord ->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('prenatal-records-' . date('Y-m-d') . '.pdf');

    }
    public function generateSeniorCitizenPdf(Request $request){
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
        $recordPages =  $tbRecords ->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.tb-dots-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $tbRecords ->count(),
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

        $recordPages =  $familyPlanning ->chunk($entriesPerPage);

        $pdf = Pdf::loadView('pdf.family-planning-records', [
            'recordPages' => $recordPages, // Changed from vaccinationRecord
            'totalRecords' =>  $familyPlanning ->count(),
            'entriesPerPage' => $entriesPerPage,
            'search' => $search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('family-planning-records-' . date('Y-m-d') . '.pdf');
    }

}
