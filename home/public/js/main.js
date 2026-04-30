/**
 * Sirac Koleji - Ana JavaScript Dosyası
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
    let backToTopButton = document.getElementById('backToTop');

    if (!backToTopButton) {
        backToTopButton = document.createElement('div');
        backToTopButton.id = 'backToTop';
        backToTopButton.className = 'back-to-top';
        backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
        backToTopButton.title = 'Yukarı Çık';
        document.body.appendChild(backToTopButton);
    }

    window.addEventListener('scroll', function () {
        if (window.scrollY > 400) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
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

    // --- 5. HOVER İLE DROPDOWN AÇMA (Masaüstü) ---
    // Fare nav-item veya dropdown-menu üzerindeyken açık kalır
    if (window.innerWidth >= 992) {
        document.querySelectorAll('.nav-item.dropdown').forEach(function (item) {
            let leaveTimer = null;

            function openMenu() {
                clearTimeout(leaveTimer);
                const toggle = item.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    const bsDropdown = bootstrap.Dropdown.getOrCreateInstance(toggle);
                    bsDropdown.show();
                }
            }

            function scheduleClose() {
                leaveTimer = setTimeout(function () {
                    const toggle = item.querySelector('[data-bs-toggle="dropdown"]');
                    if (toggle) {
                        const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                        if (bsDropdown) bsDropdown.hide();
                    }
                }, 300); // 300ms — yeterli geçiş süresi
            }

            // Nav-item (toggle dahil) hover
            item.addEventListener('mouseenter', openMenu);
            item.addEventListener('mouseleave', scheduleClose);

            // Dropdown-menu içine girilince timer iptal et
            const menu = item.querySelector('.dropdown-menu');
            if (menu) {
                menu.addEventListener('mouseenter', function () {
                    clearTimeout(leaveTimer);
                });
                menu.addEventListener('mouseleave', scheduleClose);
            }
        });
    }

    // --- 6. OTOMATİK ERKEN KAYIT POPUP ---
    const autoPopup = document.getElementById("registration-popup");
    if (autoPopup) {
        const closeBtn = autoPopup.querySelector(".auto-popup-close");

        // Sayfa yüklendikten 500ms sonra aç
        setTimeout(() => {
            autoPopup.classList.add("show");
            
            // 2 saniye sonra otomatik kapat (Kullanıcı isteği üzerine)
            setTimeout(() => {
                autoPopup.classList.remove("show");
            }, 2500); // 500ms animasyon + 2000ms görünürlük
        }, 500);

        // Manuel kapatma
        if (closeBtn) {
            closeBtn.onclick = () => autoPopup.classList.remove("show");
        }
        autoPopup.onclick = (e) => {
            if (e.target === autoPopup) autoPopup.classList.remove("show");
        };
    }
});
