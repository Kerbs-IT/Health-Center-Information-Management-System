<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicineRequestLog;

class MedicineRequestLogComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search       = '';
    public $perPage      = 10;
    public $filterAction = '';
    public $startDate    = '';
    public $endDate      = '';

    public function updatedStartDate(): void { $this->resetPage(); }
    public function updatedEndDate(): void   { $this->resetPage(); }
    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFilterAction(): void { $this->resetPage(); }

    public function updateLogsDateRange(string $start, string $end): void
    {
        $this->startDate = $start;
        $this->endDate   = $end;
        $this->resetPage();
    }

    public function clearLogsDateRange(): void
    {
        $this->startDate = '';
        $this->endDate   = '';
        $this->resetPage();
    }

    public function render()
    {
        $logs = MedicineRequestLog::query()
            ->with(['medicineRequest:id,batches_snapshot'])
            ->when($this->search, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('patient_name',      'like', "%{$this->search}%")
                        ->orWhere('medicine_name',    'like', "%{$this->search}%")
                        ->orWhere('performed_by_name','like', "%{$this->search}%")
                )
            )
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->when($this->startDate,   fn($q) => $q->whereDate('performed_at', '>=', $this->startDate))
            ->when($this->endDate,     fn($q) => $q->whereDate('performed_at', '<=', $this->endDate))
            ->latest('performed_at')
            ->paginate($this->perPage);

        return view('livewire.medicine-request-log', compact('logs'))
            ->layout('livewire.layouts.base', ['page' => 'MEDICINE LOGS']);
    }
}