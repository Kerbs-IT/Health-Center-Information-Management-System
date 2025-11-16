const profileImageCon = document.getElementById('profile_img');
var linksCon = document.getElementById('links');

profileImageCon.addEventListener('click', () => {
    console.log('working');
    if(linksCon.classList.contains('links_active')){
        linksCon.classList.remove('links_active');
    }else{
        linksCon.classList.add('links_active');
    }
});


// Sidebar responsive
// Sidebar responsive

const toggleBtn = document.getElementById("toggleSidebar");
const sidebar = document.querySelector(".menu-bar");
const overlay = document.getElementById("sidebarOverlay");
const closeBtn = document.getElementById("closeSidebar");

// Apply saved state BEFORE browser paints (fix flicker)
(function(){
  const sidebarState = localStorage.getItem("sidebarState");
  if(sidebarState === "collapsed" && window.innerWidth > 992){
    sidebar.classList.add("collapsed");
  }
})();

toggleBtn.addEventListener("click", () => {
  if(window.innerWidth  >= 992){

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