document.addEventListener('livewire:initialized', () => {
    const wireId = () => document.querySelector('[wire\\:id]').getAttribute('wire:id');

    // ── Add Medicine Modal ──
    const addMedicineModalEl = document.getElementById('addMedicineModal');
    const openAddBtn = document.getElementById('openAddMedicineBtn');

    if (addMedicineModalEl) {
        const addMedicineModal = bootstrap.Modal.getOrCreateInstance(addMedicineModalEl);

        if (openAddBtn) {
            openAddBtn.addEventListener('click', () => {
                Livewire.find(wireId()).call('resetFields');
                addMedicineModal.show();
            });
        }

        Livewire.on('close-addMedicineModal', () => addMedicineModal.hide());

        addMedicineModalEl.addEventListener('hidden.bs.modal', () => {
            document.body.classList.remove('modal-open');
            document.body.style = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }

    // ── Edit Medicine Modal ──
    const editMedicineModalEl = document.getElementById('editMedicineModal');

    if (editMedicineModalEl) {
        const editMedicineModal = bootstrap.Modal.getOrCreateInstance(editMedicineModalEl);

        Livewire.on('show-editMedicine-modal', () => editMedicineModal.show());
        Livewire.on('hide-editMedicine-modal', () => editMedicineModal.hide());

        editMedicineModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.find(wireId()).call('resetFields');
            document.body.classList.remove('modal-open');
            document.body.style = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }
});

// ── Medicine Added ──
window.addEventListener('medicine-addedModal', () => {
    Swal.fire({
        title: "Success!",
        text: "Medicine has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer: 1500
    });
});

// ── Edit Medicine Success (SweetAlert only — modal hidden via hide-editMedicine-modal) ──
window.addEventListener('close-editMedicine-modal', () => {
    Swal.fire({
        title: "Updated!",
        text: "Medicine has been updated successfully.",
        icon: "success",
        showConfirmButton: false,
        timer: 1500
    });
});

// Archive Confirmation
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
            Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            ).archiveMedicine();
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

Livewire.on('medicine-restore-blocked', ({ message }) => {
    // If you use SweetAlert2:
    Swal.fire({ icon: 'warning', title: 'Cannot Restore', text: message });

    // Or just alert(message); if you don't have Swal
});