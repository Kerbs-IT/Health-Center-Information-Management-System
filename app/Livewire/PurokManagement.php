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

        $input = strtolower(trim($this->addName));

        $similar = brgy_unit::where('status', 'Active')
            ->get()
            ->filter(function ($purok) use ($input) {
                similar_text($input, strtolower(trim($purok->brgy_unit)), $percent);
                return $percent >= 80;
            })
            ->pluck('brgy_unit')
            ->values();

        if ($similar->isNotEmpty()) {
            $this->dispatch('similarPurokFound', names: $similar);
            return;
        }

        $this->doSaveAdd();
    }

    public function confirmSaveAdd(): void
    {
        $this->validateOnly('addName');
        $this->doSaveAdd();
    }

    private function doSaveAdd(): void
    {
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

        $input = strtolower(trim($this->editName));

        $similar = brgy_unit::where('status', 'Active')
            ->where('id', '!=', $this->editId)
            ->get()
            ->filter(function ($purok) use ($input) {
                similar_text($input, strtolower(trim($purok->brgy_unit)), $percent);
                return $percent >= 80;
            })
            ->pluck('brgy_unit')
            ->values();

        if ($similar->isNotEmpty()) {
            $this->dispatch('similarPurokFoundEdit', names: $similar);
            return;
        }

        $this->doSaveEdit();
    }

    public function confirmSaveEdit(): void
    {
        $this->validateOnly('editName');
        $this->doSaveEdit();
    }

    private function doSaveEdit(): void
    {
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
