document.addEventListener("DOMContentLoaded", () => {

    const toggleBtn = document.querySelector("[data-collapse-toggle='navbar-default']");
    const menu = document.getElementById("navbar-default");

    const iconOpen = document.getElementById("icon-open");
    const iconClose = document.getElementById("icon-close");

    const navLinks = menu.querySelectorAll("a");

    function openMenu() {
        menu.classList.remove("hidden");

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                menu.classList.remove("max-h-0", "opacity-0", "-translate-y-3");
                menu.classList.add("max-h-screen", "opacity-100", "translate-y-0");
            });
        });

        // Icon animation
        iconOpen.classList.add("rotate-90", "opacity-0");
        iconOpen.classList.remove("rotate-0", "opacity-100");

        iconClose.classList.remove("rotate-90", "opacity-0");
        iconClose.classList.add("rotate-0", "opacity-100");
    }

    function closeMenu() {
        menu.classList.remove("max-h-screen", "opacity-100", "translate-y-0");
        menu.classList.add("max-h-0", "opacity-0", "-translate-y-3");

        // Icon animation
        iconOpen.classList.remove("rotate-90", "opacity-0");
        iconOpen.classList.add("rotate-0", "opacity-100");

        iconClose.classList.add("rotate-90", "opacity-0");
        iconClose.classList.remove("rotate-0", "opacity-100");

        // Hide after animation
        setTimeout(() => {
            if (menu.classList.contains("max-h-0")) {
                menu.classList.add("hidden");
            }
        }, 700);
    }

    toggleBtn.addEventListener("click", () => {
        const isOpen = menu.classList.contains("max-h-screen");
        isOpen ? closeMenu() : openMenu();
    });

    // Auto-close when clicking nav links
    navLinks.forEach(link => {
        link.addEventListener("click", () => {
            if(window.innerWidth < 768){
                closeMenu();
            }
        });
    });


    window.addEventListener("resize", () => {

        // Desktop reset
        if (window.innerWidth >= 768) {
            menu.classList.remove("hidden", "max-h-0", "opacity-0", "-translate-y-3");
            menu.classList.add("max-h-none", "opacity-100", "translate-y-0");

            // Reset icon state
            iconOpen.classList.add("opacity-100", "rotate-0");
            iconOpen.classList.remove("opacity-0", "rotate-90");

            iconClose.classList.add("opacity-0", "rotate-90");
            iconClose.classList.remove("opacity-100", "rotate-0");
        }

        // **Mobile reset**
        else {
            menu.classList.add("hidden", "max-h-0", "opacity-0", "-translate-y-3");
            menu.classList.remove("max-h-none", "opacity-100", "translate-y-0");

            // Make sure hamburger returns to default (open icon visible)
            iconOpen.classList.add("opacity-100", "rotate-0");
            iconOpen.classList.remove("rotate-90", "opacity-0");

            iconClose.classList.add("rotate-90", "opacity-0");
            iconClose.classList.remove("rotate-0", "opacity-100");
        }
    });

});