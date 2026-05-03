

document.addEventListener('livewire:initialized', () => {


    const addModalEl = document.getElementById('addCategoryModal');
    const addModal = new bootstrap.Modal(addModalEl);

    document.getElementById('openAddCategoryModal').addEventListener('click', () => {
        addModal.show();
    });

    Livewire.on('close-addCategoryModal', () => addModal.hide());

    addModalEl.addEventListener('hidden.bs.modal', () => {
        document.body.classList.remove('modal-open');
        document.body.style = '';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });

    window.addEventListener('category-added', event => {
        Swal.fire({
            title: "Success!",
            text: "Category has been added successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 1500
        });
    });


    const editModalEl = document.getElementById('editCategoryModal');
    const editModal = new bootstrap.Modal(editModalEl);

    Livewire.on('show-edit-category-modal', () => editModal.show());
    Livewire.on('hide-edit-category-modal', () => editModal.hide());

    editModalEl.addEventListener('hidden.bs.modal', () => {
        document.body.classList.remove('modal-open');
        document.body.style = '';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });

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

