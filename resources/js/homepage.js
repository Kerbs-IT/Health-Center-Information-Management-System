import 'bootstrap/dist/js/bootstrap.bundle.min.js';
// navigation bar:


// Carousel


    const cardContainer = document.getElementById('cardContainer');
    const cardCarousel = document.getElementById('cardCarousel');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const roleFilter = document.getElementById('roleFilter');

    let scrollPosition = 0;
    let slideInterval;

    const cardWidth = 300; // width of one card + margin
    const scrollAmount = cardWidth; // adjust as needed

    const startAutoSlide = () => {
        slideInterval = setInterval(() => {
            scrollPosition += scrollAmount;

            if (scrollPosition >= cardContainer.scrollWidth - cardCarousel.clientWidth) {
                scrollPosition = 0;
            }

            cardCarousel.scrollTo({ left: scrollPosition, behavior: 'smooth' });
        }, 4000); 
    };

    const resetAutoSlide = () => {
        clearInterval(slideInterval);
        startAutoSlide();
    };

    nextBtn.addEventListener('click', () => {
        scrollPosition += scrollAmount;
        if (scrollPosition >= cardContainer.scrollWidth - cardCarousel.clientWidth) {
            scrollPosition = 0;
        }
        cardCarousel.scrollTo({ left: scrollPosition, behavior: 'smooth' });
        resetAutoSlide();
    });

    prevBtn.addEventListener('click', () => {
        scrollPosition -= scrollAmount;
        if (scrollPosition < 0) {
            scrollPosition = cardContainer.scrollWidth - cardCarousel.clientWidth;
        }
        cardCarousel.scrollTo({ left: scrollPosition, behavior: 'smooth' });
        resetAutoSlide();
    });

    roleFilter.addEventListener('change', () => {
        const selected = roleFilter.value;
        const cards = document.querySelectorAll('.specialist-card');

        cards.forEach(card => {
            card.style.display = selected === 'all' || card.dataset.role === selected ? 'block' : 'none';
        });

        // Reset scroll when filtering
        scrollPosition = 0;
        cardCarousel.scrollTo({ left: 0, behavior: 'smooth' });
        resetAutoSlide();
    });

    startAutoSlide(); // start auto-sliding on load






// about us page:

function changeContent(type) {
    const content = document.getElementById('about-content');

    if (type === 'about') {
        content.innerHTML = `
            The Health Center in Barangay Hugo Perez, Trece Martires City is a friendly and welcoming place where residents can get the care they need. 
            It offers free general check-ups, vaccinations, TB-DOTS, and health programs for moms, kids, and the whole family. The center works closely with the City Health Office to bring medical missions and health services right to the community.
            It's here to help everyone stay healthy and live better every day.
        `;
    } else if (type === 'mission') {
        content.innerHTML = `
            Our mission is to provide quality, accessible, and compassionate healthcare services to all residents of Barangay Hugo Perez. 
            We are committed to promoting health, preventing disease, and ensuring the well-being of every individual and family in our community.
        `;
    } else if (type === 'vision') {
        content.innerHTML = `
            We envision a healthy, safe, and empowered community where every resident has access to proper healthcare, reliable medical services, 
            and health education — guided by care, integrity, and dedication from our health workers.
        `;
    } else if (type === 'history') {
        content.innerHTML = `
            The Health Center in Barangay Hugo Perez has been a vital part of the community for years, providing essential healthcare services 
            to residents. Through partnerships with the City Health Office and dedicated healthcare workers, it has grown to serve families with 
            expanded medical programs and continuous health education.
        `;
    }
}