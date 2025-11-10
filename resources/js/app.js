import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css'; 

import './bootstrap';
import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Swal from 'sweetalert2';




const root = document.querySelector(":root");
const logoutBtn = document.getElementById('logout-btn');
const logoutUrl = "{{ route('logout') }}"; 

function logout(btn) {
    if (btn) {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            Swal.fire({
                title: "Are you sure?",
                text: "Your Session will be terminated.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Logout",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("logout-form").submit();
                }
            });
        });
    }
}
// logout on the sidebar
logout(logoutBtn);
// logout on the header bar
const headerLogout = document.getElementById('headerLogOut');
logout(headerLogout);



// get the current color pallete

async function currentColorPallete() {
    try {
        const response = await fetch("/color-pallete");
        const data = await response.json();

        // console.log('current color pallete: ', data);
        root.style.setProperty('--primaryColor', data.primaryColor);
        root.style.setProperty('--secondaryColor', data.secondaryColor);
        root.style.setProperty('--tertiaryColor', data.tertiaryColor);


        const rootTextBarProperty = "--menu-text-color";
        const rootActiveProperty = "--active-menu-text";
        const rootPrimaryTextProperty = "--primary-bg-text";
        const wrapperElement = document.querySelectorAll('.wrapper a.active');
        // console.log(wrapperElement);
        if (wrapperElement) {
            // console.log("active");
            hexToRgb(data.tertiaryColor, rootActiveProperty);
        }
        hexToRgb(data.secondaryColor, rootTextBarProperty);
        hexToRgb(data.primaryColor, rootPrimaryTextProperty);
    } catch (error) {   
        
    }
}

function hexToRgb(hex, rootElement) {
    const root = document.querySelector(":root");
    hex = hex.replace(/^#/, "");

    if (hex.length === 3) {
        hex = hex
            .split("")
            .map((c) => c + c)
            .join("");
    }

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    const luminance = 0.299 * r + 0.587 * g + 0.114 * b;

    if (luminance > 186) {
        // High luminance = light background → use dark text
        // console.log("true bg is LIGHT");
        root.style.setProperty(rootElement, "black"); // dark text
    } else {
        // Low luminance = dark background → use light text
        // console.log("true bg is DARK");
        root.style.setProperty(rootElement, "white"); // light text
    }
}

currentColorPallete();
  


// Sidebar responsive
// Sidebar responsive

const toggleBtn = document.getElementById("toggleSidebar");
const sidebar = document.querySelector(".menu-bar");
const overlay = document.getElementById("sidebarOverlay");
const closeBtn = document.getElementById("closeSidebar");

// ✅ Apply saved state BEFORE browser paints (fix flicker)
(function(){
  const sidebarState = localStorage.getItem("sidebarState");
  if(sidebarState === "collapsed" && window.innerWidth > 992){
    sidebar.classList.add("collapsed");
  }
})();

toggleBtn.addEventListener("click", () => {
  if(window.innerWidth  >= 992){
    // Large screens → collapse sidebar
    sidebar.classList.toggle("collapsed");

    // save state
    if(sidebar.classList.contains("collapsed")){
      localStorage.setItem("sidebarState", "collapsed");
    }else{
      localStorage.setItem("sidebarState", "expand");
    }
  }else{
    // Medium/Small slider sidebar
    sidebar.classList.add("show");
    overlay.classList.add("active");
  }
});
// Close Button
closeBtn.addEventListener("click",() =>{
  sidebar.classList.remove("show");
  overlay.classList.remove("active");
});
// close outside the sidebar

overlay.addEventListener("click", () =>{
  sidebar.classList.remove("show");
  overlay.classList.remove("active")
});

