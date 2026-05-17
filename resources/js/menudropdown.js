import $ from 'jquery';

window.$ = window.jQuery = $;

$(function () {

    // ===== URL → MENU MAPPING =====
    const urlMenuMap = [
        { match: '/medicines',                              id: 'inventory_medicine',          submenuTrigger: '.fa-warehouse' },
        { match: '/categories',                             id: 'inventory_category',          submenuTrigger: '.fa-warehouse' },
        { match: '/inventory-report',                       id: 'inventory_report',            submenuTrigger: '.fa-warehouse' },
        { match: '/manage-medicine-requests',               id: 'inventory_requests',          submenuTrigger: '.fa-warehouse' },
        { match: '/medicine-request-log',                   id: 'inventory_logs',              submenuTrigger: '.fa-warehouse' },
    ];

    // ===== DETECT ACTIVE ITEM FROM CURRENT URL =====
    const currentPath = window.location.pathname;
    const matchedRoute = urlMenuMap.find(entry => currentPath.includes(entry.match));

    if (matchedRoute) {
        // Find the parent menu-option that owns this submenu
        let $parentMenuOption = null;

        if (matchedRoute.submenuTrigger.startsWith('#')) {
            // It's an ID selector (e.g. #records-menu)
            $parentMenuOption = $(matchedRoute.submenuTrigger);
        } else {
            // It's an icon class selector — find the closest .menu-option ancestor
            $(`.menu-option`).each(function () {
                if ($(this).find(matchedRoute.submenuTrigger).length) {
                    $parentMenuOption = $(this);
                    return false; // break
                }
            });
        }

        if ($parentMenuOption && $parentMenuOption.length) {
            const index = $('.menu-option').index($parentMenuOption);
            // Save to localStorage so the restore logic below picks it up
            localStorage.setItem('openSubmenus', JSON.stringify([index]));
        }

        // Save the active menu item
        localStorage.setItem('activeMenuItem', `#${matchedRoute.id}`);
    }

    // ===== RESTORE OPEN SUBMENUS FROM LOCALSTORAGE =====
    const openSubmenus = JSON.parse(localStorage.getItem('openSubmenus') || '[]');
    const activeMenuItem = localStorage.getItem('activeMenuItem');

    $('.menu-option').each(function (index) {
        const $submenu = $(this).next('.sub-menu');
        if ($submenu.length && openSubmenus.includes(index)) {
            $submenu.show();
            $(this).find('.dropdown-arrow').addClass('rotate');
        }
    });

    // ===== RESTORE ACTIVE MENU ITEM =====
    if (activeMenuItem) {
        $(activeMenuItem).addClass('active');
    }

    // ===== MENU OPTION CLICKS (items with submenus) =====
    $('.menu-option').on('click', function (e) {
        const $submenu = $(this).next('.sub-menu');

        if ($submenu.length) {
            e.preventDefault();

            const index = $('.menu-option').index(this);
            const isCurrentlyOpen = $submenu.is(':visible');

            // Close all submenus
            $('.menu-option').each(function () {
                const $otherSubmenu = $(this).next('.sub-menu');
                if ($otherSubmenu.length && $otherSubmenu.is(':visible')) {
                    $otherSubmenu.slideUp();
                    $(this).find('.dropdown-arrow').removeClass('rotate');
                }
            });

            localStorage.setItem('openSubmenus', JSON.stringify([]));

            // Open this one if it was closed
            if (!isCurrentlyOpen) {
                $submenu.slideDown();
                $(this).find('.dropdown-arrow').addClass('rotate');
                localStorage.setItem('openSubmenus', JSON.stringify([index]));
            }

        } else {
            // No submenu — just set active
            localStorage.setItem('openSubmenus', JSON.stringify([]));
            $('.sub-menu').slideUp();
            $('.dropdown-arrow').removeClass('rotate');
            $('.menu-items').removeClass('active');
            $(this).addClass('active');

            const selector = this.id ? `#${this.id}` : null;
            if (selector) localStorage.setItem('activeMenuItem', selector);
        }
    });

    // ===== SUBMENU ITEM CLICKS =====
    $('.sub-menu .menu-items').on('click', function () {
        $('.menu-items').removeClass('active');
        $(this).addClass('active');

        const selector = this.id ? `#${this.id}` : null;
        if (selector) localStorage.setItem('activeMenuItem', selector);
    });

    // ===== DIRECT MENU ITEM CLICKS (no submenu, not inside one) =====
    $('.menu-items:not(.menu-option):not(.sub-menu .menu-items)').on('click', function () {
        $('.menu-items').removeClass('active');
        $(this).addClass('active');

        const selector = this.id ? `#${this.id}` : null;
        if (selector) localStorage.setItem('activeMenuItem', selector);

        localStorage.setItem('openSubmenus', JSON.stringify([]));
    });

});