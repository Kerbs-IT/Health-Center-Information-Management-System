document.addEventListener('livewire:initialized', () => {
    const wireId = () => {
        const el = document.querySelector('[wire\\:id]');
        return el ? el.getAttribute('wire:id') : null;
    };

    // ── Add Medicine Modal ─────────────────────────────────────────
    const addMedicineModalEl = document.getElementById('addMedicineModal');
    const openAddBtn         = document.getElementById('openAddMedicineBtn');

    if (addMedicineModalEl) {
        const addMedicineModal = bootstrap.Modal.getOrCreateInstance(addMedicineModalEl);

        if (openAddBtn) {
            openAddBtn.addEventListener('click', () => {
                // 1. Open instantly — no server wait
                addMedicineModal.show();

                // 2. Reset fields in the background after modal is visible
                const id = wireId();
                if (id) Livewire.find(id).call('resetFields');
            });
        }

        Livewire.on('close-addMedicineModal', () => addMedicineModal.hide());

        addMedicineModalEl.addEventListener('hidden.bs.modal', () => {
            document.body.classList.remove('modal-open');
            document.body.style = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }

    // ── Edit Medicine Modal ────────────────────────────────────────
    // Opening is triggered by Livewire dispatching 'show-editMedicine-modal'
    // after editMedicineData() fetches the record — that round-trip is
    // unavoidable since we need the data before we can show the form.
    const editMedicineModalEl = document.getElementById('editMedicineModal');

    if (editMedicineModalEl) {
        const editMedicineModal = bootstrap.Modal.getOrCreateInstance(editMedicineModalEl);

        Livewire.on('show-editMedicine-modal', () => editMedicineModal.show());
        Livewire.on('hide-editMedicine-modal', () => editMedicineModal.hide());
        Livewire.on('close-editMedicine-modal', () => editMedicineModal.hide());

        editMedicineModalEl.addEventListener('hidden.bs.modal', () => {
            // Reset silently after modal finishes closing animation
            const id = wireId();
            if (id) Livewire.find(id).call('resetFields');

            document.body.classList.remove('modal-open');
            document.body.style = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }
});

// ── Medicine Added ─────────────────────────────────────────────────
window.addEventListener('medicine-addedModal', () => {
    Swal.fire({
        title: 'Success!',
        text: 'Medicine has been added successfully.',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500
    });
});

// ── Medicine Updated ───────────────────────────────────────────────
window.addEventListener('close-editMedicine-modal', () => {
    Swal.fire({
        title: 'Updated!',
        text: 'Medicine has been updated successfully.',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500
    });
});

// ── Archive Confirmation ───────────────────────────────────────────
window.addEventListener('show-medicine-archive-confirmation', () => {
    Swal.fire({
        title: 'Archive this medicine?',
        text: 'You can restore it later from the archived items.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f00606',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, archive it!',
    }).then((result) => {
        if (result.isConfirmed) {
            const el = document.querySelector('[wire\\:id]');
            if (el) Livewire.find(el.getAttribute('wire:id')).archiveMedicine();
        }
    });
});

// ── Medicine Archived ──────────────────────────────────────────────
window.addEventListener('medicine-archive-success', () => {
    Swal.fire({
        title: 'Archived!',
        text: 'Medicine has been archived successfully.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
});

// ── Medicine Restored ──────────────────────────────────────────────
window.addEventListener('medicine-restore-success', () => {
    Swal.fire({
        title: 'Restored!',
        text: 'Medicine has been restored successfully.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
});

// ── Medicine Restore Blocked ───────────────────────────────────────
// Fires when restoring is prevented (e.g. category still archived)
document.addEventListener('livewire:initialized', () => {
    Livewire.on('medicine-restore-blocked', ({ message }) => {
        Swal.fire({
            icon: 'warning',
            title: 'Cannot Restore',
            text: message
        });
    });
});