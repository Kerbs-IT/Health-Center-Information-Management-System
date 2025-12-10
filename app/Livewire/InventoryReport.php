<?php

namespace App\Livewire;

use Livewire\Component;

class InventoryReport extends Component
{
    public function render()
    {
        return view('livewire.inventory-report')->layout('livewire.layouts.base');
    }
}
