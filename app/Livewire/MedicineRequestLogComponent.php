<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicineRequestLog;
class MedicineRequestLogComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    public function render()
    {
        $logs = MedicineRequestLog::query()
            ->when($this->search, function ($q) {
                $q->where('patient_name', 'like', "%{$this->search}%")
                  ->orWhere('medicine_name', 'like', "%{$this->search}%")
                  ->orWhere('performed_by_name', 'like', "%{$this->search}%");
            })
            ->latest('performed_at')
            ->paginate($this->perPage);

        return view('livewire.medicine-request-log', compact('logs'))
            ->layout('livewire.layouts.base');
    }
}
