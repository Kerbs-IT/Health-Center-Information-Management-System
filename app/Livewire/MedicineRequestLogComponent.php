<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicineRequestLog;

class MedicineRequestLogComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search  = '';
    public $perPage = 10;
    public $filterAction = ''; // '', 'approved', 'dispensed', 'rejected'

    public function render()
    {
        $logs = MedicineRequestLog::query()
            ->when($this->search, fn($q) =>
                $q->where('patient_name',     'like', "%{$this->search}%")
                  ->orWhere('medicine_name',  'like', "%{$this->search}%")
                  ->orWhere('performed_by_name', 'like', "%{$this->search}%")
            )
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->latest('performed_at')
            ->paginate($this->perPage);

        return view('livewire.medicine-request-log', compact('logs'))
            ->layout('livewire.layouts.base', ['page' => 'MEDICINE LOGS']);
    }
}