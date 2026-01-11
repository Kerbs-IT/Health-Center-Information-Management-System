

document.addEventListener("DOMContentLoaded", function () {
    const linksCon = document.getElementById("links");

    document.addEventListener("click", function (e) {
        const profileImage = e.target.closest("#profile_img");
        if (!profileImage || !linksCon) return;

        // console.log("profile clicked ✅");
        linksCon.classList.toggle("links_active");
    });
});


// Sidebar responsive
// Sidebar responsive

const toggleBtn = document.getElementById("toggleSidebar");
const sidebar = document.querySelector(".menu-bar");
const overlay = document.getElementById("sidebarOverlay");
const closeBtn = document.getElementById("closeSidebar");

// Apply saved state BEFORE browser paints (fix flicker)
(function () {
    const sidebarState = localStorage.getItem("sidebarState");
    if (sidebarState === "collapsed" && window.innerWidth > 992) {
        sidebar.classList.add("collapsed");
    }
})();

toggleBtn.addEventListener("click", () => {
    if (window.innerWidth >= 992) {
        // Desktop: Toggle collapsed state


        // Save state AFTER toggle
        if (sidebar.classList.contains("collapsed")) {
            localStorage.setItem("sidebarState", "collapsed");
        } else {
            localStorage.setItem("sidebarState", "expand");
        }
    } else {
        // Mobile: Show sidebar
        sidebar.classList.add("show");
        overlay.classList.add("active");
    }
});
toggleBtn.addEventListener('click', () => {
    if(window.innerWidth < 992){
        sidebar.classList.add('show');
        overlay.classList.add('active');
        document.body.classList.add('no-scroll');
    }
});

// Close Button
closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("show");
    overlay.classList.remove("active");
    document.body.classList.remove('no-scroll');
});

// Close outside the sidebar
overlay.addEventListener("click", () => {
    sidebar.classList.remove("show");
    overlay.classList.remove("active");
    document.body.classList.remove('no-scroll');
});

