

// sweetalert
window.addEventListener('category-added', event => {
    Swal.fire({
        title: "Success!",
        text: "Category has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer:1500
    });
    // .then(() => {
    //     var myModalEl = document.querySelector('#addCategoryModal');
    //     var modal = bootstrap.Modal.getInstance(myModalEl);
    //     modal.hide();
    // });
});
// Edit
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
            text: "Category has been added successfully.",
            icon: "success",
            showConfirmButton: false,
            timer:1500
        });
    });
});

// SHOW EDIT VACCINA MOPAL AND CLOSED
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
        text: "Category has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer:1500
    });
    });

});


// Medicine

// sweetalert
window.addEventListener('medicine-addedModal', event => {
    Swal.fire({
        title: "Success!",
        text: "Category has been added successfully.",
        icon: "success",
        showConfirmButton: false,
        timer:1500
    });
});


// show medicine delete modal
window.addEventListener('show-deleteMedicineModal', () => {
    Swal.fire({
        title: "Are you sure?",
        text: "This Medicine will be permanently deleted!",
// show medicine delete modal
window.addEventListener('show-deleteMedicineModal', () => {

});


// Listen to Livewire v3 browser events
window.addEventListener('show-delete-confirmation', () => {
    Swal.fire({
        title: "Are you sure?",
        text: "This category will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {

            // 🔥 Correct way to call Livewire method in v3
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))
            .deleteMedicine();
                .deleteCategory();

        }
    });
});

// Listen to success event
window.addEventListener('success-medicine-delete', () => {
    Swal.fire({
        title: "Deleted!",
        text: "Medicine has been deleted.",
window.addEventListener('delete-success', () => {
    Swal.fire({
        title: "Deleted!",
        text: "Category has been deleted.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
});
