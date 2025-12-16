<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Category;

use Livewire\WithPagination;

class Medicines extends Component
{
    use WithPagination;
    public $medicine_name, $category_id, $dosage, $stock, $expiry_date, $edit_id;

    // Age fields - separate for months and years
    public $min_age_value, $min_age_unit = 'months';
    public $max_age_value, $max_age_unit = 'months';

    // Internal storage in months
    public $min_age_months, $max_age_months;
    public $medicine_name,$category_id, $dosage, $stock, $expiry_date, $edit_id, $deleteMedicineId;

    public $sortField = null;
    public $sortDirection = null;
    public $search = '';
    public $perPage = 10;
    public $ageFilter = ''; // For filtering by age range
    public $deleteMedicineId;

    protected $rules = [
        'medicine_name' => 'required|string|max:255',
        'category_id'   => 'required|integer|exists:categories,category_id',
        'dosage'        => 'required|string',
        'stock'         => 'required|numeric|min:1',
        'expiry_date'   => 'required|date',
        'min_age_value' => 'nullable|integer|min:0',
        'max_age_value' => 'nullable|integer|min:0'
    ];

    protected $messages = [
        'category_id.required' => 'Please select a category.',
    ];

    // Convert age value and unit to months
    private function convertToMonths($value, $unit)
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        return $unit === 'years' ? $value * 12 : $value;
    }

    // Convert months back to value and unit
    private function convertFromMonths($months)
    {
        if (is_null($months)) {
            return ['value' => null, 'unit' => 'months'];
        }

        // If less than 24 months, keep as months
        if ($months < 24) {
            return ['value' => $months, 'unit' => 'months'];
        }

        // Otherwise convert to years
        return ['value' => $months / 12, 'unit' => 'years'];
    }

    // Validate that max age is greater than min age
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['min_age_value', 'min_age_unit', 'max_age_value', 'max_age_unit'])) {
            $this->validateAgeRange();
        }
    }

    private function validateAgeRange()
    {
        $minMonths = $this->convertToMonths($this->min_age_value, $this->min_age_unit);
        $maxMonths = $this->convertToMonths($this->max_age_value, $this->max_age_unit);

        if (!is_null($minMonths) && !is_null($maxMonths) && $maxMonths < $minMonths) {
            $this->addError('max_age_value', 'Maximum age must be greater than or equal to minimum age.');
            return false;
        }

        $this->resetErrorBag(['max_age_value']);
        return true;
    }

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

    public function storeMedicineData()
    {
        $this->validate();

        if (!$this->validateAgeRange()) {
            return;
        }

        $exists = Medicine::where('medicine_name', $this->medicine_name)->where('dosage', $this->dosage)->exists();

        if ($exists) {
            $this->addError('medicine_name', 'This medicine with the same dosage already exists');
            $this->addError('dosage', 'Duplicate medicine + dosage detected');
            return;
        }

        // Convert age values to months before saving
        $min_age_months = $this->convertToMonths($this->min_age_value, $this->min_age_unit);
        $max_age_months = $this->convertToMonths($this->max_age_value, $this->max_age_unit);

        $status = $this->determineStatus($this->stock, $this->expiry_date);
        Medicine::create([
            'medicine_name' => $this->medicine_name,
            'category_id'   => $this->category_id,
            'dosage'        => $this->dosage,
            'stock'         => $this->stock,
            'expiry_date'   => $this->expiry_date,
            'status'        => $status,
            'min_age_months' => $min_age_months,
            'max_age_months' => $max_age_months
        ]);

        $this->dispatch('medicine-addedModal');
        $this->reset();
        // session()->flash('message', 'Medicine added successfully');
    }

    public function editMedicineData($id)
    {
        $medicine = Medicine::findOrFail($id);
        $this->edit_id  = $id;
        $this->medicine_name = $medicine->medicine_name;
        $this->category_id   = $medicine->category_id;
        $this->dosage        = $medicine->dosage;
        $this->stock         = $medicine->stock;
        $this->expiry_date   = $medicine->expiry_date;

        // Convert stored months back to value and unit
        $minAge = $this->convertFromMonths($medicine->min_age_months);
        $maxAge = $this->convertFromMonths($medicine->max_age_months);

        $this->min_age_value = $minAge['value'];
        $this->min_age_unit = $minAge['unit'];
        $this->max_age_value = $maxAge['value'];
        $this->max_age_unit = $maxAge['unit'];

        $this->dispatch('show-editMedicine-modal');
        return $this->skipRender();
    }

    public function updateMedicineData()
    {
        $this->validate();

        if (!$this->validateAgeRange()) {
            return;
        }

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

        // Convert age values to months before updating
        $min_age_months = $this->convertToMonths($this->min_age_value, $this->min_age_unit);
        $max_age_months = $this->convertToMonths($this->max_age_value, $this->max_age_unit);

        Medicine::where('medicine_id', $this->edit_id)->update([
            'medicine_name' => $this->medicine_name,
            'category_id'   => $this->category_id,
            'dosage'        => $this->dosage,
            'stock'         => $this->stock,
            'expiry_date'   => $this->expiry_date,
            'status'        => $status,
            'min_age_months' => $min_age_months,
            'max_age_months' => $max_age_months
        ]);

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
        $this->min_age_value = '';
        $this->min_age_unit = 'months';
        $this->max_age_value = '';
        $this->max_age_unit = 'months';
        $this->edit_id = '';
    }

    public function confirmMedicineDelete($id){
        $this->deleteMedicineId = $id;
        $this->dispatch('show-deleteMedicineModal');
    }
    public function deleteMedicine(){
        Medicine::findOrFail($this->deleteMedicineId)->delete();
        $this->dispatch('success-medicine-delete');

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


    private function getAgeRangeFilter($range)
    {
        return match ($range) {
            '0-9months' => ['min' => 0, 'max' => 9],
            '10-24months' => ['min' => 10, 'max' => 24],
            '2-5years' => ['min' => 24, 'max' => 60],     // 2-5 years = 24-60 months
            '6-12years' => ['min' => 72, 'max' => 144],   // 6-12 years = 72-144 months
            '13-17years' => ['min' => 156, 'max' => 204], // 13-17 years = 156-204 months
            'adult' => ['min' => 216, 'max' => null],     // 18+ years = 216+ months
            default => null,
        };
    }

    public function render()
    {
        $medicines = Medicine::with('category')
            ->search($this->search)
            ->when($this->ageFilter, function ($query) {
                $range = $this->getAgeRangeFilter($this->ageFilter);

                if ($range) {
                    $query->where(function ($q) use ($range) {

                        // min_age must be <= selected max
                        if (!is_null($range['max'])) {
                            $q->where('min_age_months', '<=', $range['max']);
                        }

                        // max_age must be >= selected min OR NULL (open-ended)
                        $q->where(function ($sub) use ($range) {
                            $sub->whereNull('max_age_months')
                                ->orWhere('max_age_months', '>=', $range['min']);
                        });

                    });
                }
            })

            ->when($this->sortField === 'category_name', function ($query) {
                $query->orderBy(
                    Category::select('category_name')->whereColumn('categories.category_id', 'medicines.category_id'),
                    $this->sortDirection
                );
            })
            ->when($this->sortField && $this->sortField !== 'category_name', function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(perPage: $this->perPage);

        return view('livewire.medicines', [
            'categories' => Category::orderBy('category_name')->get(),
            'medicines' =>  $medicines
        ])->layout('livewire.layouts.base');
    }
}