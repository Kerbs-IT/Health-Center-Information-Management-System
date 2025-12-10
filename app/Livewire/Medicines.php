<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Category;

use Livewire\WithPagination;

class Medicines extends Component
{
    use WithPagination;
    public $medicine_name,$category_id, $dosage, $stock, $expiry_date, $edit_id;

    public $sortField = null;
    public $sortDirection = null;
    public $search = '';
    public $filterCategory;
    public $perPage = 10;
    protected $rules = [
            'medicine_name' => 'required|string|max:255',
            'category_id'   => 'required|integer|exists:categories,category_id',
            'dosage'        => 'required|string',
            'stock'         => 'required|numeric|min:0',
            'expiry_date'   => 'required|date'
    ];
    protected $messages = [
        'category_id.required' => 'Please select a category.',
    ];
    private function determineStatus($stock, $expiry_date)
    {
        if (now()->diffInDays($expiry_date, false) <= 30) {
            return 'Expiring Soon';
        }
        if ($stock <= 0) {
            return 'Out of Stock';
        }

        if ($stock <= 10) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function storeMedicineData(){
        $this->validate();

        $exists = Medicine::where('medicine_name', $this->medicine_name)->where('dosage', $this->dosage)->exists();

        if($exists){
            $this->addError('medicine_name', 'This medicine with the same dosage already exists');
            $this->addError('dosage', 'Duplicate medicine + dosage detected');
            return;
        }
        $status = $this->determineStatus($this->stock, $this->expiry_date);
        Medicine::create([
            'medicine_name' => $this->medicine_name,
            'category_id'   => $this->category_id,
            'dosage'        => $this->dosage,
            'stock'         => $this->stock,
            'expiry_date'   => $this->expiry_date,
            'status'        => $status
        ]);
        $this->reset();
        // $this->dispatch('close-add-medicine-modal');
    }
    public function editMedicineData($id){
        $medicine = Medicine::findOrFail($id);
        $this->edit_id  = $id;
        $this->medicine_name = $medicine->medicine_name;
        $this->category_id   = $medicine->category_id;
        $this->dosage        = $medicine->dosage;
        $this->stock         = $medicine->stock;
        $this->expiry_date   = $medicine->expiry_date;


        $this->dispatch('show-editMedicine-modal');
        return $this->skipRender();
    }
    public function updateMedicineData(){
        $this->validate();

        $status = $this->determineStatus($this->stock, $this->expiry_date);
        $exists = Medicine::where('medicine_name', $this->medicine_name)
                    ->where('dosage', $this->dosage)
                    ->where('medicine_id', '!=', $this->edit_id)
                    ->exists();

        if ($exists) {
            $this->addError('medicine_name', 'This medicine with the same dosage already exists.');
            $this->addError('dosage', 'Duplicate medicine + dosage detected.');
            return;
        }
        Medicine::where('medicine_id', $this->edit_id)->update([
            'medicine_name' => $this->medicine_name,
            'category_id'   => $this->category_id,
            'dosage'        => $this->dosage,
            'stock'         => $this->stock,
            'expiry_date'   => $this->expiry_date,
            'status'        => $status
        ]);

        session()->flash('message','Medicine updated Successfully');
        $this->resetFields();
        $this->dispatch('close-editMedicine-modal');
    }
    public function resetFields()
    {
        $this->medicine_name = '';
        $this->category_id = '';
        $this->dosage = '';
        $this->stock = '';
        $this->expiry_date = '';
        $this->edit_id = '';
    }





    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }
    public function render()
    {

        $medicines = Medicine::with('category')->search($this->search)->when($this->filterCategory, function($query){
            $query->where('category_id', $this->filterCategory);
        })->when($this->sortField === 'category_name', function($query){
            $query->orderBy(
                Category::select('category_name')->whereColumn('categories.category_id', 'medicines.category_id'),$this->sortDirection);
            })->when($this->sortField && $this->sortField !== 'category_name', function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })->paginate(perPage: $this->perPage);


        return view('livewire.medicines',[
            'categories' => Category::orderBy('category_name')->get(),
            'medicines' =>  $medicines
        ])->layout('livewire.layouts.base');
    }
}
