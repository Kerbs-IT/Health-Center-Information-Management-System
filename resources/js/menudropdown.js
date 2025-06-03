
import $ from 'jquery';

window.$ = window.jQuery = $;

$('.menu-option').on('click', function (){
    $(this).next('.sub-menu').slideToggle();
    $(this).find('.dropdown-arrow').toggleClass('rotate');
});
// $('.menu-option').on('click', function (e) {
//     const next = $(this).next('.sub-menu');

//     // Only toggle if this menu-option has a submenu immediately following it
//     if (next.length > 0) {
//         e.preventDefault(); // Optional: prevent navigation if it's an <a>
//         next.slideToggle();
//         $(this).find('.dropdown-arrow').toggleClass('rotate');
//     }
// });


const menuItem = document.querySelectorAll('.menu-items');

menuItem.forEach(e => {
    e.classList.remove('active');
    e.addEventListener('click', () =>{
       
        if(e.classList.contains('active')){
            e.classList.remove('active');
            
        }else{
            menuItem.forEach(item => item.classList.remove('active'));
            e.classList.add('active');
        }
        // menuItem.forEach(item => item.classList.remove('active'));
        // e.classList.add('active');
        
    })
});