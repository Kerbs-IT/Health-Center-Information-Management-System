<?php

namespace App\Livewire\FamilyPlanning;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use Livewire\Component;
use Livewire\WithPagination;

class PatientCaseTable extends Component
{
    use WithPagination;
    public $medicalRecordCaseId;
    // for sorting
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    // Optional: listen to events for add/edit/archive
    protected $listeners = ['familyPlanningRefreshTable' => '$refresh'];
    protected $paginationTheme = 'bootstrap';
    public function mount($medicalRecordCaseId)
    {
        $this->$medicalRecordCaseId = $medicalRecordCaseId; // THIS catches the ID from URL
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    public function render()
    {
        $familyPlanningCases = family_planning_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->latest()
            ->get();
        $familyPlanningSideB = family_planning_side_b_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->latest()
            ->get();
        
        $patientInfo = medical_record_cases::with(['family_planning_medical_record', 'patient'])->findOrFail($this->medicalRecordCaseId);

        // combine them all record
        $allRecords = collect();
        // push the familyplanningside a
        foreach($familyPlanningCases as $record){
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'family_planning_side_a',
                'data' => $record
            ]);
        }
        // side b
        foreach($familyPlanningSideB as $record){
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'family_planning_side_b',
                'data' => $record
            ]);
        }

        if ($this->sortDirection === 'asc') {
            $allRecords = $allRecords->sortBy('created_at');
        } else {
            $allRecords = $allRecords->sortByDesc('created_at');
        }


        // Manual pagination
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRecords->forPage($currentPage, $perPage),
            $allRecords->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        $address = patient_addresses::where("patient_id", $patientInfo->patient->id)->first() ?? null;
        return view('livewire.family-planning.patient-case-table', 
        ['isActive' => true, 'page' => 'RECORD', 
        'patientInfo' => $patientInfo, 
        'medicalRecordCaseId'=> $this-> medicalRecordCaseId,
        'address'=> $address,
        'allRecords' => $paginatedRecords]);

    }

    public function exportPdf($caseId,$type)
    {
        return redirect()->route("family-planning-side-$type.pdf", [
            'caseId' => $caseId, // Sends "desc"
        ]);
    }

}
