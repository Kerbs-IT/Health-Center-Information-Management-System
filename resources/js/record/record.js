import Swal from "sweetalert2";


document.addEventListener('DOMContentLoaded', () =>{
    // record delete
    const records = document.querySelectorAll('.delete-record-icon');

    records.forEach(icon => {
        icon.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently remove the user.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            });
        });
    });
});