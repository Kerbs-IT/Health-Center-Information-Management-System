<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\MedicineBatch;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use \Illuminate\Validation\Rule;

class Medicines extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Form fields ─────────────────────────────────────────────
    public $medicine_name, $category_id, $dosage, $stock;
    public $stock_status, $expiry_status, $expiry_date, $edit_id;
    public $min_age_value, $min_age_unit = 'months';
    public $max_age_value, $max_age_unit = 'months';
    public $min_age_months, $max_age_months;
    public $batch_number, $manufactured_date;

    // ─── Table state ─────────────────────────────────────────────
    public $sortField     = null;
    public $sortDirection = null;
    public $search        = '';
    public $perPage       = 10;
    public $ageFilter     = '';
    public $categoryFilter= '';
    public $archiveMedicineId;
    public $showArchived  = false;
    public string $backUrl = '';
    // ── Bound to ?filter= query string automatically ──────────────
    public string $filterStatus = '';

    public function mount(): void
    {
        $this->filterStatus = request()->query('filter', '');
    }

    public function clearFilter(): void
    {
        $this->filterStatus = '';
    }

    protected $listeners = [
        'resetFormOnModalClose' => 'resetFields',
    ];

    protected $rules = [
        'medicine_name' => 'required|string|max:255',
        'category_id'   => 'required|integer|exists:categories,category_id',
        'dosage'        => 'required|string',
        'min_age_value' => 'nullable|integer|min:0',
        'max_age_value' => 'nullable|integer|min:0',
    ];

    protected $messages = [
        'category_id.required' => 'Please select a category.',
    ];

    // ─── Uniqueness check ─────────────────────────────────────────
    public function openAddMedicine(): void
    {
        $this->resetFields();
        $this->dispatch('show-addMedicineModal');
    }

    private function normalizeDosage(string $dosage): string
    {
        return preg_replace('/(\d+)\s+([a-zA-Z]+)/', '$1$2', trim($dosage));
    }

    private function medicineExists($name, $dosage, $excludeId = null): bool
    {
        $name             = trim($name);
        $singular         = Str::singular($name);
        $plural           = Str::plural($name);
        $normalizedDosage = $this->normalizeDosage($dosage);

        $query = Medicine::withTrashed()
            ->whereRaw("REPLACE(dosage, ' ', '') = ?", [$normalizedDosage])
            ->where(function ($q) use ($name, $singular, $plural) {
                $q->whereRaw('LOWER(medicine_name) = ?', [strtolower($name)])
                  ->orWhereRaw('LOWER(medicine_name) = ?', [strtolower($singular)])
                  ->orWhereRaw('LOWER(medicine_name) = ?', [strtolower($plural)]);
            });

        if ($excludeId) {
            $query->where('medicine_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function validateMedicineUniqueness($excludeId = null): bool
    {
        if ($this->medicineExists($this->medicine_name, $this->dosage, $excludeId)) {
            $singular = Str::singular($this->medicine_name);
            $plural   = Str::plural($this->medicine_name);

            $existingForm = Medicine::whereRaw('LOWER(medicine_name) = ?', [strtolower($singular)])
                ->where('dosage', $this->dosage)
                ->when($excludeId, fn($q) => $q->where('medicine_id', '!=', $excludeId))
                ->exists() ? $singular : $plural;

            $this->addError('medicine_name', "This medicine already exists as '{$existingForm}' with dosage {$this->dosage}.");
            $this->addError('dosage', 'Duplicate medicine + dosage detected.');
            return false;
        }
        return true;
    }

    // ─── Age helpers ─────────────────────────────────────────────

    private function convertToMonths($value, $unit): ?int
    {
        if (is_null($value) || $value === '') return null;
        return $unit === 'years' ? $value * 12 : (int)$value;
    }

    private function convertFromMonths($months): array
    {
        if (is_null($months)) return ['value' => null, 'unit' => 'months'];
        if ($months < 24)     return ['value' => $months, 'unit' => 'months'];
        return ['value' => $months / 12, 'unit' => 'years'];
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['min_age_value', 'min_age_unit', 'max_age_value', 'max_age_unit'])) {
            $this->validateAgeRange();
        }
    }

    private function validateAgeRange(): bool
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

    // ─── Stock / expiry status ────────────────────────────────────

    private function determineStockStatus($stock): string
    {
        if ($stock <= 0)  return 'Out of Stock';
        if ($stock <= 10) return 'Low Stock';
        return 'In Stock';
    }

    private function determineExpiryStatus($expiry_date): string
    {
        $days = now()->diffInDays($expiry_date, false);
        if ($days < 0)   return 'Expired';
        if ($days <= 30) return 'Expiring Soon';
        return 'Valid';
    }

    // ─── CRUD ────────────────────────────────────────────────────

    public function storeMedicineData(): void
    {
        $this->validate([
            'medicine_name' => 'required|string|max:255',
            'category_id'   => 'required|integer|exists:categories,category_id',
            'dosage'        => 'required|string',
            'stock'         => 'required|numeric|min:0',
            'expiry_date'   => 'required|date',
            'min_age_value' => 'nullable|integer|min:0',
            'max_age_value' => 'nullable|integer|min:0',
            'batch_number'  => 'required|string|max:100',
        ], [
            'batch_number.required' => 'Batch number is required.',
            'batch_number.unique'   => 'This batch number already exists.',
        ]);

        if (!$this->validateAgeRange())          return;
        if (!$this->validateMedicineUniqueness()) return;

        $min_age_months = $this->convertToMonths($this->min_age_value, $this->min_age_unit);
        $max_age_months = $this->convertToMonths($this->max_age_value, $this->max_age_unit);
        $stockStatus    = $this->determineStockStatus($this->stock);
        $expiryStatus   = $this->determineExpiryStatus($this->expiry_date);

        DB::transaction(function () use ($min_age_months, $max_age_months, $stockStatus, $expiryStatus) {
            $medicine = Medicine::create([
                'medicine_name'  => trim($this->medicine_name),
                'category_id'    => $this->category_id,
                'dosage'         => $this->dosage,
                'stock'          => $this->stock,
                'expiry_date'    => $this->expiry_date,
                'stock_status'   => $stockStatus,
                'expiry_status'  => $expiryStatus,
                'min_age_months' => $min_age_months,
                'max_age_months' => $max_age_months,
            ]);

            MedicineBatch::create([
                'medicine_id'       => $medicine->medicine_id,
                'batch_number'      => $this->batch_number,
                'quantity'          => (int)$this->stock,
                'initial_quantity'  => (int)$this->stock,
                'manufactured_date' => $this->manufactured_date ?: null,
                'expiry_date'       => $this->expiry_date,
                'expiry_status'     => $expiryStatus,
            ]);
        });

        $this->dispatch('medicine-addedModal');
        $this->dispatch('close-addMedicineModal');
        $this->reset();
        $this->min_age_unit      = 'months';
        $this->max_age_unit      = 'months';
        $this->batch_number      = '';
        $this->manufactured_date = '';
    }

    public function editMedicineData($id): mixed
    {
        $medicine = Medicine::findOrFail($id);

        $this->edit_id       = $id;
        $this->medicine_name = $medicine->medicine_name;
        $this->category_id   = $medicine->category_id;
        $this->dosage        = $medicine->dosage;

        $minAge = $this->convertFromMonths($medicine->min_age_months);
        $maxAge = $this->convertFromMonths($medicine->max_age_months);

        $this->min_age_value = $minAge['value'];
        $this->min_age_unit  = $minAge['unit'];
        $this->max_age_value = $maxAge['value'];
        $this->max_age_unit  = $maxAge['unit'];

        $this->dispatch('show-editMedicine-modal');
        return $this->skipRender();
    }

    public function updateMedicineData(): void
    {
        $this->validate([
            'medicine_name' => 'required|string|max:255',
            'category_id'   => 'required|integer|exists:categories,category_id',
            'dosage'        => 'required|string',
            'min_age_value' => 'nullable|integer|min:0',
            'max_age_value' => 'nullable|integer|min:0',
        ]);

        if (!$this->validateAgeRange())                         return;
        if (!$this->validateMedicineUniqueness($this->edit_id)) return;

        $min_age_months = $this->convertToMonths($this->min_age_value, $this->min_age_unit);
        $max_age_months = $this->convertToMonths($this->max_age_value, $this->max_age_unit);

        Medicine::where('medicine_id', $this->edit_id)->update([
            'medicine_name'  => trim($this->medicine_name),
            'category_id'    => $this->category_id,
            'dosage'         => $this->dosage,
            'min_age_months' => $min_age_months,
            'max_age_months' => $max_age_months,
        ]);

        $this->resetFields();
        $this->dispatch('hide-editMedicine-modal');
        $this->dispatch('close-editMedicine-modal');
    }

    // ─── Archive / restore ────────────────────────────────────────

    public function confirmMedicineArchive($id): void
    {
        $this->archiveMedicineId = $id;
        $this->dispatch('show-medicine-archive-confirmation');
    }

    public function archiveMedicine(): void
    {
        Medicine::findOrFail($this->archiveMedicineId)->delete();
        $this->dispatch('medicine-archive-success');
        $this->resetPage();
    }

    public function restoreMedicine($id): void
    {
        try {
            Medicine::withTrashed()->findOrFail($id)->restore();
            $this->dispatch('medicine-restore-success');
        } catch (\RuntimeException $e) {
            $this->dispatch('medicine-restore-blocked', message: $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleArchived(): void
    {
        $this->showArchived = !$this->showArchived;
        $this->resetPage();
    }

    // ─── Sorting / pagination ─────────────────────────────────────

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    // ─── Age filter helper ────────────────────────────────────────

    private function getAgeRangeFilter($range): ?array
    {
        return match ($range) {
            '0-9months'   => ['min' => 0,   'max' => 9],
            '10-24months' => ['min' => 10,  'max' => 24],
            '2-5years'    => ['min' => 24,  'max' => 60],
            '6-12years'   => ['min' => 72,  'max' => 144],
            '13-17years'  => ['min' => 156, 'max' => 204],
            'adult'       => ['min' => 216, 'max' => null],
            default       => null,
        };
    }

    // ─── Reset ────────────────────────────────────────────────────

    public function resetFields(): void
    {
        $this->medicine_name     = '';
        $this->category_id       = '';
        $this->dosage            = '';
        $this->stock             = '';
        $this->batch_number      = '';
        $this->manufactured_date = '';
        $this->expiry_date       = '';
        $this->min_age_value     = '';
        $this->min_age_unit      = 'months';
        $this->max_age_value     = '';
        $this->max_age_unit      = 'months';
        $this->edit_id           = '';
        $this->resetErrorBag();
    }

    // ─── Render ───────────────────────────────────────────────────

    public function render()
    {
        $validFilters = ['out_of_stock', 'low_stock', 'expiring_soon', 'expired'];

        // Sanitize — ignore anything not in the allowed list
        $activeFilter = in_array($this->filterStatus, $validFilters)
            ? $this->filterStatus
            : '';

        $query = Medicine::query();

        if ($this->showArchived) {
            $query->onlyTrashed();
        }

        // ── Search ────────────────────────────────────────────────
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('medicine_name', 'like', "%{$this->search}%")
                    ->orWhereHas('category', function ($cq) {
                        $cq->withTrashed()
                           ->where('category_name', 'like', "%{$this->search}%");
                    });
            });
        });

        // ── Category filter ───────────────────────────────────────
        $query->when($this->categoryFilter, fn($q) =>
            $q->where('category_id', $this->categoryFilter)
        );

        // ── Age filter ────────────────────────────────────────────
        $query->when($this->ageFilter, function ($q) {
            $range = $this->getAgeRangeFilter($this->ageFilter);
            if ($range) {
                $q->where(function ($q2) use ($range) {
                    if (!is_null($range['max'])) {
                        $q2->where('min_age_months', '<=', $range['max']);
                    }
                    $q2->where(function ($sub) use ($range) {
                        $sub->whereNull('max_age_months')
                            ->orWhere('max_age_months', '>=', $range['min']);
                    });
                });
            }
        });

        // ── Inventory alert filter ────────────────────────────────
        if ($activeFilter === 'out_of_stock') {
            $query->where('stock', '<=', 0);

        } elseif ($activeFilter === 'low_stock') {
            $query->where('stock', '>', 0)
                  ->where('stock', '<=', 10);

        } elseif ($activeFilter === 'expiring_soon') {
            $query->whereHas('allBatches', function ($b) {
                $b->whereNull('deleted_at')
                ->where('expiry_status', 'Expiring Soon');
            });

        } elseif ($activeFilter === 'expired') {
            $query->whereHas('allBatches', function ($b) {
                $b->whereNull('deleted_at')
                ->where('expiry_status', 'Expired');
            });
        }

        // ── Sort ──────────────────────────────────────────────────
        if ($this->sortField === 'category_name') {
            $query->orderBy(
                Category::select('category_name')
                    ->withTrashed()
                    ->whereColumn('categories.category_id', 'medicines.category_id')
                    ->limit(1),
                $this->sortDirection
            );
        } elseif ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        // ── Eager load + paginate ─────────────────────────────────
        $medicines = $query->with([
            'category' => fn($q) => $q->withTrashed(),
            'batches'  => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])->paginate($this->perPage);

        return view('livewire.medicines', [
            'categories'   => Category::orderBy('category_name')->get(),
            'medicines'    => $medicines,
            'activeFilter' => $activeFilter,
               'filterStatus' => $activeFilter,
        ])->layout('livewire.layouts.base', ['page' => 'MEDICINE INVENTORY']);
    }
}