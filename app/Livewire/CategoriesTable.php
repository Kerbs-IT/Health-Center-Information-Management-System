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

    public $deleteCategoryId;
    // Input Fields Validation

    // public function updated($fields){
    //     $this->validateOnly($fields,[
    //         'category_name' => 'required|unique:categories,category_name'
    //     ]);
    // }

    // Create Category
    public function storeCategoryData(){
        //
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

        }else{
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function confirmDelete($id){
        $this->deleteCategoryId = $id;
        $this->dispatch('show-delete-confirmation');
    }
    public function deleteCategory(){
        Category::findOrFail($this->deleteCategoryId)->delete();
        $this->dispatch('delete-success');
    }

    public function render()
    {
        $categories = Category::search($this->search)->when($this->sortField, function($query){
            $query->orderBy($this->sortField, $this->sortDirection);
        })->paginate($this->perPage);

        return view('livewire.category-section.category-table',[
            'categories' => $categories
        ])->layout('livewire.layouts.base');
    }

}
