

document.addEventListener("DOMContentLoaded", function () {
    const linksCon = document.getElementById("links");

    document.addEventListener("click", function (e) {
        const profileImage = e.target.closest("#profile_img");
        if (!profileImage || !linksCon) return;

        console.log("profile clicked ✅");
        linksCon.classList.toggle("links_active");
    });
});
