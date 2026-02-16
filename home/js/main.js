/**
 * Siraç Koleji - Ana JavaScript Dosyası
 * Tüm site genelindeki dinamik efektler ve slider kontrolleri burada birleştirilmiştir.
 */

document.addEventListener("DOMContentLoaded", function () {

    // --- 1. NAVBAR SCROLL EFEKTİ ---
    const navbar = document.querySelector(".navbar");
    if (navbar) {
        window.addEventListener("scroll", function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // --- 2. ÇAPA LİNKLERİ İÇİN YUMUŞAK KAYDIRMA ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#' && targetId.startsWith('#')) {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // --- 3. YUKARI ÇIK BUTONU ---
    const backToTopButton = document.createElement('button');
    backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopButton.classList.add('back-to-top-button');
    document.body.appendChild(backToTopButton);

    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            backToTopButton.classList.add('visible');
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.classList.remove('visible');
            backToTopButton.style.display = 'none';
        }
    });

    backToTopButton.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // --- 4. HERO SLAYDER (CAROUSEL) KONTROLLERİ ---
    const heroCarousel = document.getElementById('heroCarousel');

    if (heroCarousel) {
        // Otomatik geçişli Bootstrap Carousel başlat
        const carousel = new bootstrap.Carousel(heroCarousel, {
            interval: 4000,
            wrap: true,
            ride: 'carousel',
            touch: true
        });

        // Manuel kontrol ipucu mesajı
        const manualMessage = document.createElement('div');
        manualMessage.className = 'manual-control-message';
        manualMessage.innerHTML = '<i class="fas fa-hand-point-up"></i> Slaytları değiştirmek için butonlara tıklayın';
        heroCarousel.appendChild(manualMessage);

        const prevBtn = heroCarousel.querySelector('.carousel-control-prev');
        const nextBtn = heroCarousel.querySelector('.carousel-control-next');
        const indicatorBtns = heroCarousel.querySelectorAll('.carousel-indicators [data-bs-target]');

        // Slayt değişiminde içerik animasyonu
        heroCarousel.addEventListener('slide.bs.carousel', function (e) {
            const nextSlide = e.relatedTarget;
            const content = nextSlide.querySelector('.hero-overlay-content');

            if (content) {
                content.style.opacity = '0';
                content.style.transform = 'translateY(-40%)';

                setTimeout(() => {
                    content.style.transition = 'all 0.8s cubic-bezier(0.165, 0.84, 0.44, 1)';
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(-50%)';
                }, 300);
            }
        });

        // Klavye ok tuşları ile kontrol
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') carousel.prev();
            if (e.key === 'ArrowRight') carousel.next();
        });
    }
});
