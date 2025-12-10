

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
        bootstrap.Modal.getInstance(
            document.getElementById('editMedicineModal')
        )?.hide();
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

})