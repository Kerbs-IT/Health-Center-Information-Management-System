<?php

namespace App\Livewire;

use App\Models\brgy_unit;
use Livewire\Component;
use Livewire\WithPagination;

class PurokArchived extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // ─── Restore ─────────────────────────────────────────────────────────────

    public function restorePurok(int $id): void
    {
        brgy_unit::findOrFail($id)->update(['status' => 'Active']);
        $this->resetPage();
        $this->dispatch('purokRestored');
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $data = brgy_unit::where('status', 'Archived')
            ->orderBy('brgy_unit', 'ASC')
            ->paginate(10);

        return view('livewire.purok-archived', compact('data'));
    }
}
