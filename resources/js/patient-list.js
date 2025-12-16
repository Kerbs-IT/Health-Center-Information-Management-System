

function confirmArchive(patientId) {
        Swal.fire({
            title: 'Archive Patient?',
            text: "This patient will be moved to archived status.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.archivePatient(patientId);
            }
        });
    }

    function confirmActivate(patientId) {
        Swal.fire({
            title: 'Activate Patient?',
            text: "This patient will be moved to active status.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, activate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.activatePatient(patientId);
            }
        });
    }

    // Listen for Livewire events
    window.addEventListener('patientArchived', event => {
        Swal.fire({
            icon: 'success',
            title: 'Archived!',
            text: 'Patient has been archived successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    window.addEventListener('patientActivated', event => {
        Swal.fire({
            icon: 'success',
            title: 'Activated!',
            text: 'Patient has been activated successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    });