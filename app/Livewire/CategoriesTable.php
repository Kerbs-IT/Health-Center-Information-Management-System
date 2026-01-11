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
    public $showArchived = false; // Toggle to show/hide archived items

    protected $listeners = [
        'resetFormOnModalClose' => 'resetFields'
    ];
    // Add this method
    public function resetFields()
    {
        $this->reset(['category_name', 'edit_id']);
        $this->resetErrorBag();
    }
    // Create Category
    public function storeCategoryData(){
        $validated = $this->validate([
            'category_name' => 'required|unique:categories,category_name'
        ]);
        Category::create($validated);
        $this->reset(['category_name']);
        $this->dispatch('category-added');
    }

    // EDIT (Load data into modal)
    public function editCategoryData($id)
    {
        $this->dispatch('show-edit-category-modal');
        $category = Category::where('category_id', $id)->select('category_name')->first();

        $this->edit_id = $id;
        $this->category_name = $category->category_name;
    }

    // UPDATE
    public function updateCategoryData()
    {
        $this->validate([
            'category_name' => 'required|unique:categories,category_name'
        ]);

        Category::where('category_id', $this->edit_id)->update([
            'category_name' => $this->category_name
        ]);

        $this->reset(['category_name', 'edit_id']);
        $this->dispatch('hide-edit-category-modal');
        $this->dispatch('category-updated');
    }

    public function sortBy($field){
        if($this->sortField === $field){
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    // Archive confirmation
    public function confirmArchive($id){
        $this->archiveCategoryId = $id;
        $this->dispatch('show-archive-confirmation');
    }

    // Archive the category (soft delete)
    public function archiveCategory(){
        $category = Category::findOrFail($this->archiveCategoryId);
        $category->delete(); // This uses soft delete if you have SoftDeletes trait
        $this->dispatch('archive-success');
        $this->resetPage();
    }

    // Restore archived category
    public function restoreCategory($id){
        Category::withTrashed()->findOrFail($id)->restore();
        $this->dispatch('restore-success');
        $this->resetPage();
    }

    // Toggle archived view
    public function toggleArchived(){
        $this->showArchived = !$this->showArchived;
        $this->resetPage();
    }

    public function render()
    {
        $query = Category::search($this->search);

        // Show archived or active categories
        if ($this->showArchived) {
            $query = $query->onlyTrashed();
        }

        $categories = $query->when($this->sortField, function($query){
            $query->orderBy($this->sortField, $this->sortDirection);
        })->paginate($this->perPage);

        return view('livewire.category-section.category-table',[
            'categories' => $categories
        ])->layout('livewire.layouts.base');
    }
}