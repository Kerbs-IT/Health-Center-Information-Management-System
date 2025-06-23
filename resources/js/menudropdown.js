
import $ from 'jquery';

window.$ = window.jQuery = $;

$(function () {
    const openSubmenus = JSON.parse(localStorage.getItem('openSubmenus') || '[]');

    // Only restore for menu options that actually have a sub-menu
    $('.menu-option').each(function (index) {
        const $submenu = $(this).next('.sub-menu');
        if ($submenu.length && openSubmenus.includes(index)) {
            $submenu.show();
            $(this).find('.dropdown-arrow').addClass('rotate');
        }
    });

    // Handle menu option clicks
    $('.menu-option').on('click', function (e) {
        const $submenu = $(this).next('.sub-menu');
        
        // If this menu option has a submenu, prevent navigation and toggle submenu
        if ($submenu.length) {
            e.preventDefault(); // Prevent Laravel route navigation
            
            const index = $('.menu-option').index(this);
            
            // Close all other open submenus first
            $('.menu-option').each(function(i) {
                const $otherSubmenu = $(this).next('.sub-menu');
                if ($otherSubmenu.length && i !== index && $otherSubmenu.is(':visible')) {
                    $otherSubmenu.slideUp();
                    $(this).find('.dropdown-arrow').removeClass('rotate');
                    
                    // Update localStorage
                    const openSubmenus = JSON.parse(localStorage.getItem('openSubmenus') || '[]');
                    const idx = openSubmenus.indexOf(i);
                    if (idx !== -1) {
                        openSubmenus.splice(idx, 1);
                        localStorage.setItem('openSubmenus', JSON.stringify(openSubmenus));
                    }
                }
            });

            // Toggle the clicked submenu
            $submenu.slideToggle();
            $(this).find('.dropdown-arrow').toggleClass('rotate');

            const openSubmenus = JSON.parse(localStorage.getItem('openSubmenus') || '[]');
            const i = openSubmenus.indexOf(index);

            if ($submenu.is(':visible') && i === -1) {
                openSubmenus.push(index);
            } else if (!$submenu.is(':visible') && i !== -1) {
                openSubmenus.splice(i, 1);
            }

            localStorage.setItem('openSubmenus', JSON.stringify(openSubmenus));
        } else {
            // If no submenu, clear all submenu states before navigation
            localStorage.setItem('openSubmenus', JSON.stringify([]));
            // Allow normal Laravel route navigation
        }
    });

    // Highlight active menu-items (optional persistence can be added)
    const menuItem = document.querySelectorAll('.menu-items');
    menuItem.forEach(e => {
        e.addEventListener('click', () => {
            menuItem.forEach(item => item.classList.remove('active'));
            e.classList.add('active');
        });
    });
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