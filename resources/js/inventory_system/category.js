// sweetalert
window.addEventListener('category-added', event => {
    Swal.fire({
        title: "Success!",
        text: "Category has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer: 1500
    });
});

// Edit Modal Handlers
document.addEventListener('DOMContentLoaded', function () {
    // Show Edit Modal
    window.addEventListener('show-edit-category-modal', event => {
        let modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    });

    // Hide Edit Modal
    window.addEventListener('hide-edit-category-modal', event => {
        let modalEl = document.getElementById('editCategoryModal');
        let modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    });

    // Reset form on Medicine Modal
    const addMedicineModal = document.getElementById('adMedicineModal');
    const editMedicineModal = document.getElementById('editMedicineModal');
    const addCategoryModal = document.getElementById('addCategoryModal');
    const editCategoryModal = document.getElementById('editCategoryModal');

    if(addMedicineModal){
        addMedicineModal.addEventListener('hidden.bs.modal', function(){
            Livewire.dispatch('resetFormOnModalClose');
        });
    }
    if(editMedicineModal){
        editMedicineModal.addEventListener('hidden.bs.modal', function(){
            Livewire.dispatch('resetFormOnModalClose');
        });
    }
    if(addCategoryModal){
        addCategoryModal.addEventListener('hidden.bs.modal', function(){
            Livewire.dispatch('resetFormOnModalClose');
        });
    }
    if(editCategoryModal){
        editCategoryModal.addEventListener('hidden.bs.modal', function(){
            Livewire.dispatch('resetFormOnModalClose');
        });
    }


});

// Archive Confirmation Dialog
window.addEventListener('show-archive-confirmation', () => {
    Swal.fire({
        title: "Archive this category?",
        text: "You can restore it later from the archived items.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f00606",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, archive it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // Call Livewire method to archive
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).archiveCategory();
        }
    });
});

// Archive Success
window.addEventListener('archive-success', () => {
    Swal.fire({
        title: "Archived!",
        text: "Category has been archived successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// Restore Success
window.addEventListener('restore-success', () => {
    Swal.fire({
        title: "Restored!",
        text: "Category has been restored successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// Category Updated
window.addEventListener('category-updated', () => {
    Swal.fire({
        title: "Updated!",
        text: "Category has been updated successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// Medicine/Vaccine Modals (keep your existing code)
// Medicine Archive Confirmation
window.addEventListener('show-medicine-archive-confirmation', () => {
    Swal.fire({
        title: "Archive this medicine?",
        text: "You can restore it later from the archived items.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f00606",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, archive it!",
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).archiveMedicine();
        }
    });
});

// Medicine Archive Success
window.addEventListener('medicine-archive-success', () => {
    Swal.fire({
        title: "Archived!",
        text: "Medicine has been archived successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// Medicine Restore Success
window.addEventListener('medicine-restore-success', () => {
    Swal.fire({
        title: "Restored!",
        text: "Medicine has been restored successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// Medicine Added
window.addEventListener('medicine-addedModal', () => {
    Swal.fire({
        title: "Success!",
        text: "Medicine has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer: 1500
    });
});

document.addEventListener('livewire:init', function () {
    Livewire.on('show-editMedicine-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('editMedicineModal'));
        modal.show();
    });

    Livewire.on('close-editMedicine-modal', () => {
        setTimeout(() => {
            bootstrap.Modal.getInstance(
                document.getElementById('editMedicineModal')
            )?.hide();
        }, 1500);
        Swal.fire({
            title: "Success!",
            text: "Medicine has been updated successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 1500
        });
    });
});

document.addEventListener('livewire:init', function(){
    Livewire.on('show-editVaccine-modal',() => {
        const modal = new bootstrap.Modal(document.getElementById('EditVaccineModal'));
        modal.show();
    });

    Livewire.on('close-editVaccine-modal', () => {
        setTimeout(() => {
            bootstrap.Modal.getInstance(
                document.getElementById('EditVaccineModal')
            )?.hide();
        }, 1500);
        Swal.fire({
            title: "Success!",
            text: "Vaccine has been updated successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 1500
        });
    });
});

// close for walk-in
window.addEventListener('close-walkin-modal', event => {
    Swal.fire({
        title: "Success!",
        text: "Medicine has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer:1500
    })
    window.location.reload();

});