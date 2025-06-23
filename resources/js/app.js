import './bootstrap';
import 'bootstrap';

import Swal from 'sweetalert2';




const logoutBtn = document.getElementById('logout-btn');
    const logoutUrl = "{{ route('logout') }}"; 

if(logoutBtn){
    logoutBtn.addEventListener('click', (e) =>{
        e.preventDefault();
            
        Swal.fire({
            title: 'Are you sure?',
            text: "Your Session will be terminated.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Logout'
        }).then(result =>{
            if(result.isConfirmed){
               document.getElementById('logout-form').submit();
            }
        })
    })
};
  
