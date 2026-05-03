document.addEventListener('livewire:initialized', () => {

    // ── Utility: safely close a modal and clean up ALL orphaned backdrops ──
    function closeModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        const instance = bootstrap.Modal.getInstance(modalEl);
        if (instance) {
            instance.hide();
        }
        modalEl.addEventListener('hidden.bs.modal', cleanupBackdrops, { once: true });
    }

    function cleanupBackdrops() {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    // ── Global safety net ──
    document.querySelectorAll('.modal').forEach(modalEl => {
        modalEl.addEventListener('hidden.bs.modal', cleanupBackdrops);
    });

    // ── Open Request Modal: reset form via Livewire THEN show modal ──
    const openRequestBtn = document.getElementById('openRequestModalBtn');
    if (openRequestBtn) {
        openRequestBtn.addEventListener('click', () => {
            const wireEl = document.querySelector('[wire\\:id]');
            if (wireEl) {
                Livewire.find(wireEl.getAttribute('wire:id')).call('resetForm');
            }
            setTimeout(() => {
                const modalEl = document.getElementById('requestMedicineModal');
                if (!modalEl) return;
                let instance = bootstrap.Modal.getInstance(modalEl);
                if (!instance) instance = new bootstrap.Modal(modalEl);
                instance.show();
            }, 50);
        });
    }

    // ── Request submitted successfully ──
    Livewire.on('medicineRequest-added', () => {
        closeModal('requestMedicineModal');
        Swal.fire({
            title: "Success!",
            text: "Medicine request submitted successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 1500
        });
    });

    // ── Show Edit Modal ──
    Livewire.on('show-editRequest-modal', () => {
        closeModal('requestMedicineModal');
        setTimeout(() => {
            const modalEl = document.getElementById('editMedicineModal');
            if (!modalEl) return;
            let instance = bootstrap.Modal.getInstance(modalEl);
            if (!instance) instance = new bootstrap.Modal(modalEl);
            instance.show();
        }, 50);
    });

    // ── Edit updated successfully ──
    Livewire.on('close-medicineRequest-modal', () => {
        Swal.fire({
            title: "Updated!",
            text: "Medicine request updated successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            closeModal('editMedicineModal');
            cleanupBackdrops();
        });
    });

    // ── Delete confirmation ──
    Livewire.on('show-deleleteRequestModal', () => {
        Swal.fire({
            title: "Are you sure?",
            text: "This medicine request will be cancelled permanently!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, cancel it!",
        }).then((result) => {
            if (result.isConfirmed) {
                const wireEl = document.querySelector('[wire\\:id]');
                if (wireEl) {
                    Livewire.find(wireEl.getAttribute('wire:id')).call('deleteRequest');
                }
            }
        });
    });

    // ── Delete success ──
    Livewire.on('success-deleteMedicineRequestModal', () => {
        cleanupBackdrops();
        Swal.fire({
            title: "Cancelled!",
            text: "Medicine request has been cancelled.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
        });
    });

});