document.addEventListener('livewire:initialized', () => {

    // ── Add Category Modal ─────────────────────────────────────
    const addModalEl = document.getElementById('addCategoryModal');
    if (addModalEl) {
        const addModal = new bootstrap.Modal(addModalEl);

        const openBtn = document.getElementById('openAddCategoryModal');
        if (openBtn) {
            openBtn.addEventListener('click', () => addModal.show());
        }

        Livewire.on('close-addCategoryModal', () => addModal.hide());

        addModalEl.addEventListener('hidden.bs.modal', () => {
            document.body.classList.remove('modal-open');
            document.body.style = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }

    // ── Category Added Toast ───────────────────────────────────
    window.addEventListener('category-added', () => {
        Swal.fire({
            title: "Success!",
            text: "Category has been added successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 1500
        });
    });

    // ── Edit Category Modal ────────────────────────────────────
    const editModalEl = document.getElementById('editCategoryModal');
    if (editModalEl) {
        const editModal = new bootstrap.Modal(editModalEl);

        Livewire.on('show-edit-category-modal', () => editModal.show());
        Livewire.on('hide-edit-category-modal', () => editModal.hide());

        editModalEl.addEventListener('hidden.bs.modal', () => {
            document.body.classList.remove('modal-open');
            document.body.style = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }

});

// ── Archive Confirmation Dialog ────────────────────────────────
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
            const wireEl = document.querySelector('[wire\\:id]');
            if (wireEl) {
                Livewire.find(wireEl.getAttribute('wire:id')).archiveCategory();
            }
        }
    });
});

// ── Archive Success ────────────────────────────────────────────
window.addEventListener('archive-success', () => {
    Swal.fire({
        title: "Archived!",
        text: "Category has been archived successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// ── Restore Success ────────────────────────────────────────────
window.addEventListener('restore-success', () => {
    Swal.fire({
        title: "Restored!",
        text: "Category has been restored successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// ── Category Updated ───────────────────────────────────────────
window.addEventListener('category-updated', () => {
    Swal.fire({
        title: "Updated!",
        text: "Category has been updated successfully.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});

// ── Vaccine Modals ─────────────────────────────────────────────
document.addEventListener('livewire:init', () => {

    Livewire.on('show-editVaccine-modal', () => {
        const el = document.getElementById('EditVaccineModal');
        if (el) new bootstrap.Modal(el).show();
    });

    Livewire.on('close-editVaccine-modal', () => {
        setTimeout(() => {
            const el = document.getElementById('EditVaccineModal');
            if (el) bootstrap.Modal.getInstance(el)?.hide();
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