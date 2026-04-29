<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesTable extends Component
{
    use WithPagination;

    public $category_name, $edit_id;
    public $sortField = null;
    public $sortDirection = null;
    public $search;
    public $perPage = 10;
    public $archiveCategoryId;
    public $showArchived = false;

    protected $listeners = [
        'resetFormOnModalClose' => 'resetFields'
    ];

    public function clearError($field)
    {
        $this->resetErrorBag($field);
    }

    public function resetFields()
    {
        $this->reset(['category_name', 'edit_id']);
        $this->resetErrorBag();
    }

    /**
     * Derive the singular root of a word for comparison.
     * Handles: -ies → -y, -ses/-xes/-ches/-shes → -s (remove -es), plain -s.
     */
    private function singularize(string $name): string
    {
        $lower = strtolower(trim($name));

        if (preg_match('/ies$/i', $lower)) {
            return preg_replace('/ies$/i', 'y', $lower);
        }

        if (preg_match('/(?:[sx]|ch|sh)es$/i', $lower)) {
            return preg_replace('/es$/i', '', $lower);
        }

        if (preg_match('/[^s]s$/i', $lower)) {
            return substr($lower, 0, -1);
        }

        return $lower;
    }

    /**
     * Return the three forms to check: exact input, singular root, and +s plural.
     * Archived categories are NOT included — they no longer "occupy" the name.
     */
    private function categoryExists(string $name, ?int $excludeId = null): bool
    {
        $root    = $this->singularize($name);
        $plural  = $root . 's';
        // Handle -y → -ies separately (e.g. "category" → "categories")
        $iesForm = preg_match('/y$/i', $root)
            ? preg_replace('/y$/i', 'ies', $root)
            : null;

        $forms = array_unique(array_filter([
            strtolower(trim($name)),
            $root,
            $plural,
            $iesForm,
        ]));

        $query = Category::withTrashed()->whereRaw(
            'LOWER(TRIM(category_name)) IN (' . implode(',', array_fill(0, count($forms), '?')) . ')',
            $forms
        );

        if ($excludeId) {
            $query->where('category_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function validateCategoryName(?int $excludeId = null): void
    {
        $this->validate([
            'category_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($excludeId) {
                    $root    = $this->singularize($value);
                    $plural  = $root . 's';
                    $iesForm = preg_match('/y$/i', $root)
                        ? preg_replace('/y$/i', 'ies', $root)
                        : null;

                    $forms = array_unique(array_filter([
                        strtolower(trim($value)),
                        $root,
                        $plural,
                        $iesForm,
                    ]));

                    $query = Category::withTrashed()->whereRaw(
                        'LOWER(TRIM(category_name)) IN (' . implode(',', array_fill(0, count($forms), '?')) . ')',
                        $forms
                    );

                    if ($excludeId) {
                        $query->where('category_id', '!=', (int) $excludeId); // 👈 cast to int
                    }

                    $match = $query->first();

                    if (!$match) return;

                    if ($match->trashed()) {
                        $fail('This category is archived. Please restore it instead.');
                    } else {
                        $fail('A category with this name (or its singular/plural form) already exists.');
                    }
                },
            ],
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function storeCategoryData(): void
    {
        $this->validateCategoryName();

        Category::create([
            'category_name' => trim($this->category_name),
        ]);

        $this->reset(['category_name']);
        $this->dispatch('category-added');
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function editCategoryData(int $id): void
    {
        $category = Category::where('category_id', $id)
            ->select('category_id', 'category_name')
            ->firstOrFail();

        $this->edit_id       = $category->category_id;
        $this->category_name = $category->category_name;

        $this->dispatch('show-edit-category-modal');
    }

    public function updateCategoryData(): void
    {
        // Pass edit_id so the category's own name is not flagged as a duplicate
        $this->validateCategoryName($this->edit_id);

        Category::where('category_id', $this->edit_id)->update([
            'category_name' => trim($this->category_name),
        ]);

        $this->reset(['category_name', 'edit_id']);
        $this->dispatch('hide-edit-category-modal');
        $this->dispatch('category-updated');
    }

    // ── Sorting ───────────────────────────────────────────────────────────────

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    // ── Archive / Restore ─────────────────────────────────────────────────────

    public function confirmArchive(int $id): void
    {
        $this->archiveCategoryId = $id;
        $this->dispatch('show-archive-confirmation');
    }

    public function archiveCategory(): void
    {
        Category::findOrFail($this->archiveCategoryId)->delete();
        $this->dispatch('archive-success');
        $this->resetPage();
    }

    public function restoreCategory(int $id): void
    {
        Category::withTrashed()->findOrFail($id)->restore();
        $this->dispatch('restore-success');
        $this->resetPage();
    }

    public function toggleArchived(): void
    {
        $this->showArchived = !$this->showArchived;
        $this->resetPage();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = Category::search($this->search);

        if ($this->showArchived) {
            $query->onlyTrashed();
        }

        $categories = $query
            ->when($this->sortField, fn ($q) => $q->orderBy($this->sortField, $this->sortDirection))
            ->paginate($this->perPage);

        return view('livewire.category-section.category-table', [
            'categories' => $categories,
        ])->layout('livewire.layouts.base', ['page' => 'MEDICINE CATEGORY']);
    }
}