<?php

namespace App\Livewire\ManageVaccines;

use App\Models\vaccines;
use Livewire\Component;
use Livewire\WithPagination;

class ManageVaccines extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'Active';

    protected $paginationTheme = 'bootstrap';
    
    protected $listeners = ['manageVaccineRefresh' => '$refresh'];

    // Reset pagination when search or filter changes
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $vaccines = vaccines::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('type_of_vaccine', 'like', '%' . $this->search . '%')
                        ->orWhere('vaccine_acronym', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('type_of_vaccine')
            ->paginate(10);

        return view('livewire.manage-vaccines.manage-vaccines', [
            'vaccines' => $vaccines,
        ]);
    }
   
}
