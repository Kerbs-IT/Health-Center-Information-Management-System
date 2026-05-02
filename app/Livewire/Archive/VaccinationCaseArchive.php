<?php

namespace App\Livewire\Archive;

use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccines;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class VaccinationCaseArchive extends Component
{

    use WithPagination;

    public $entries = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    public $medical_record_id;

    // Add 'medical_record_id' here so it persists in the URL
    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search', 'patient_id', 'type_of_case', 'medical_record_id'];
    protected $paginationTheme = 'bootstrap';

    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->medical_record_id = request('medical_record_id');

        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
    }

    public function updatingEntries()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
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


    public function activateVaccinationRecord($recordId)
    {
        $record = vaccination_case_records::findOrFail($recordId);

        // Validate the record is actually archived
        if ($record->status !== 'Archived') {
            $this->dispatch('activationError', message: 'Record is not archived and cannot be activated.');
            return;
        }

        // Get the vaccines from the record being activated
        $vaccines = explode(',', $record->vaccine_type);
        $vaccinesArray = array_map(function ($v) {
            return Str::upper(trim($v));
        }, $vaccines);

        // Check for existing active records with same vaccines and dose
        $existingActiveVaccines = [];
        $vaccinationCaseRecords = vaccination_case_records::where('medical_record_case_id', $record->medical_record_case_id)
            ->where('status', '!=', 'Archived')
            ->where('id', '!=', $recordId) // Exclude the current record
            ->get();

        foreach ($vaccinationCaseRecords as $activeRecord) {
            $administeredVaccines = explode(',', $activeRecord->vaccine_type);

            foreach ($administeredVaccines as $vaccine) {
                $vaccineName = Str::upper(trim($vaccine)) . "_" . $activeRecord->dose_number;
                $existingActiveVaccines[] = $vaccineName;
            }
        }

        // Check if activating this record would create duplicates
        $duplicateVaccines = [];
        foreach ($vaccinesArray as $vaccine) {
            $vaccineName = $vaccine . "_" . $record->dose_number;

            if (in_array($vaccineName, $existingActiveVaccines)) {
                $duplicateVaccines[] = $vaccineName;
            }
        }

        if ($duplicateVaccines) {
            $converted = implode(", ", $duplicateVaccines);
            $this->dispatch('activationError', message: "Cannot activate record. $converted already exists in active records.");
            return;
        }

        // No duplicates found, proceed with activation
        $record->update(['status' => 'Active']);

        
        // -------------------------------------------------------
        // Update the vaccination masterlist with the vaccine dates
        // -------------------------------------------------------

        $validVaccineColumns = [
            'BCG',
            'Hepatitis B',
            'PENTA_1',
            'PENTA_2',
            'PENTA_3',
            'OPV_1',
            'OPV_2',
            'OPV_3',
            'PCV_1',
            'PCV_2',
            'PCV_3',
            'IPV_1',
            'IPV_2',
            'MCV_1',
            'MCV_2',
        ];

        $masterlist = vaccination_masterlists::where('medical_record_case_id', $record->medical_record_case_id)->first();
        $singleDose = ['BCG', 'HEPATITIS B'];

        if ($masterlist) {
            foreach ($vaccinesArray as $upper) {
                // $vaccinesArray is already uppercased from the top of the method

                if (in_array($upper, $singleDose)) {
                    $itemColumn = $upper === 'HEPATITIS B' ? 'Hepatitis B' : 'BCG';
                } else {
                    $itemColumn = $upper . '_' . $record->dose_number;
                }

                $masterlist->update([
                    $itemColumn => $record->date_of_vaccination,
                ]);
            }
        }

        $this->dispatch('patientActivated');
    }

    public function render()
    {
        $records = vaccination_case_records::where('medical_record_case_id', $this->medical_record_id)
            ->where('status', 'Archived')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.archive.vaccination-case-archive', compact('records'));
    }
}
