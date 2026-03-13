<?php

namespace App\Livewire;

use App\Models\brgy_unit;
use Livewire\Component;
use Livewire\WithPagination;

class PurokManagement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Add
    public string $addName = '';

    // Edit
    public ?int $editId = null;
    public string $editName = '';

    // ─── Validation ──────────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'addName'  => 'required|string|max:255|unique:brgy_units,brgy_unit',
            'editName' => 'required|string|max:255|unique:brgy_units,brgy_unit,' . $this->editId,
        ];
    }

    protected $messages = [
        'addName.required'  => 'Purok name is required.',
        'addName.unique'    => 'This Purok name already exists.',
        'editName.required' => 'Purok name is required.',
        'editName.unique'   => 'This Purok name already exists.',
    ];

    // ─── Add ─────────────────────────────────────────────────────────────────

    public function openAdd(): void
    {
        $this->reset(['addName']);
        $this->resetValidation();
        $this->dispatch('openAddModal');
    }

    public function saveAdd(): void
    {
        $this->validateOnly('addName');

        brgy_unit::create([
            'brgy_unit' => trim($this->addName),
            'status'    => 'Active',
        ]);

        $this->reset(['addName']);
        $this->dispatch('closeAddModal');
        $this->dispatch('purokAdded');
        $this->resetPage();
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function openEdit(int $id): void
    {
        $purok = brgy_unit::findOrFail($id);
        $this->editId   = $purok->id;
        $this->editName = $purok->brgy_unit;
        $this->resetValidation();
        $this->dispatch('openEditModal');
    }

    public function saveEdit(): void
    {
        $this->validateOnly('editName');

        brgy_unit::findOrFail($this->editId)->update([
            'brgy_unit' => trim($this->editName),
        ]);

        $this->dispatch('closeEditModal');
        $this->dispatch('purokUpdated');
        $this->reset(['editId', 'editName']);
    }

    // ─── Archive ─────────────────────────────────────────────────────────────

    public function archivePurok(int $id): void
    {
        $purok = brgy_unit::findOrFail($id);

        if ($purok->staff()->exists()) {
            $this->dispatch('purokInUse');
            return;
        }

        $purok->update(['status' => 'Archived']);
        $this->resetPage();
        $this->dispatch('purokArchived');
    }
    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $data = brgy_unit::where('status', 'Active')
            ->orderBy('brgy_unit', 'ASC')
            ->paginate(10);

        return view('livewire.purok-management', compact('data'));
    }
}
