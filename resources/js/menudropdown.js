import $ from 'jquery';

window.$ = window.jQuery = $;

$(function () {
    const openSubmenus = JSON.parse(localStorage.getItem('openSubmenus') || '[]');
    const activeMenuItem = localStorage.getItem('activeMenuItem');

    // Restore open submenus
    $('.menu-option').each(function (index) {
        const $submenu = $(this).next('.sub-menu');
        if ($submenu.length && openSubmenus.includes(index)) {
            $submenu.show();
            $(this).find('.dropdown-arrow').addClass('rotate');
        }
    });

    // Restore active menu item
    if (activeMenuItem) {
        $(activeMenuItem).addClass('active');
    }

    // Handle menu option clicks (for items with submenus)
    $('.menu-option').on('click', function (e) {
        const $submenu = $(this).next('.sub-menu');

        // If this menu option has a submenu, prevent navigation and toggle submenu
        if ($submenu.length) {
            e.preventDefault();

            const index = $('.menu-option').index(this);
            const isCurrentlyOpen = $submenu.is(':visible');

            // Close ALL submenus first
            $('.menu-option').each(function(i) {
                const $otherSubmenu = $(this).next('.sub-menu');
                if ($otherSubmenu.length && $otherSubmenu.is(':visible')) {
                    $otherSubmenu.slideUp();
                    $(this).find('.dropdown-arrow').removeClass('rotate');
                }
            });

            // Clear all open submenus from storage
            localStorage.setItem('openSubmenus', JSON.stringify([]));

            // If the clicked submenu was closed, open it
            if (!isCurrentlyOpen) {
                $submenu.slideDown();
                $(this).find('.dropdown-arrow').addClass('rotate');

                // Save only this one to storage
                localStorage.setItem('openSubmenus', JSON.stringify([index]));
            }
        } else {
            // If no submenu, clear all submenu states and set this as active
            localStorage.setItem('openSubmenus', JSON.stringify([]));

            // Close all submenus
            $('.sub-menu').slideUp();
            $('.dropdown-arrow').removeClass('rotate');

            // Remove active from all and add to this item
            $('.menu-items').removeClass('active');
            $(this).addClass('active');

            // Store active menu item
            const selector = this.id ? `#${this.id}` : null;
            if (selector) {
                localStorage.setItem('activeMenuItem', selector);
            }
        }
    });

    // Handle submenu item clicks (items inside .sub-menu)
    $('.sub-menu .menu-items').on('click', function (e) {
        // Remove active from all menu items
        $('.menu-items').removeClass('active');

        // Add active to clicked item
        $(this).addClass('active');

        // Store active menu item
        const selector = this.id ? `#${this.id}` : null;
        if (selector) {
            localStorage.setItem('activeMenuItem', selector);
        }
    });

    // Handle direct menu items (non-submenu, non-menu-option items)
    $('.menu-items:not(.menu-option):not(.sub-menu .menu-items)').on('click', function () {
        // Remove active from all menu items
        $('.menu-items').removeClass('active');

        // Add active to clicked item
        $(this).addClass('active');

        // Store active menu item
        const selector = this.id ? `#${this.id}` : null;
        if (selector) {
            localStorage.setItem('activeMenuItem', selector);
        }

        // Clear submenu states
        localStorage.setItem('openSubmenus', JSON.stringify([]));
    });
});