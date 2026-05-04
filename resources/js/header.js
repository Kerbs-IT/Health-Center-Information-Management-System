document.addEventListener("DOMContentLoaded", function () {

    // ===== PROFILE DROPDOWN =====
    const linksCon = document.getElementById("links");
    document.addEventListener("click", function (e) {
        const profileImage = e.target.closest("#profile_img");
        if (!profileImage || !linksCon) return;
        linksCon.classList.toggle("links_active");
    });

    // ===== SIDEBAR =====
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".menu-bar");
    const overlay = document.getElementById("sidebarOverlay");
    const closeBtn = document.getElementById("closeSidebar");

    // ✅ Remove pre-collapsed class, then apply real .collapsed
    document.documentElement.classList.remove('sidebar-pre-collapsed');
    if (localStorage.getItem('sidebarState') === 'collapsed' && window.innerWidth > 992) {
        sidebar.classList.add('collapsed');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle("collapsed");
                if (sidebar.classList.contains("collapsed")) {
                    localStorage.setItem("sidebarState", "collapsed");
                } else {
                    localStorage.setItem("sidebarState", "expand");
                }
            } else {
                sidebar.classList.add("show");
                overlay.classList.add("active");
                document.body.classList.add('no-scroll');
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            sidebar.classList.remove("show");
            overlay.classList.remove("active");
            document.body.classList.remove('no-scroll');
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.remove("show");
            overlay.classList.remove("active");
            document.body.classList.remove('no-scroll');
        });
    }
});