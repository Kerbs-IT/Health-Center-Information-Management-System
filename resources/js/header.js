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
