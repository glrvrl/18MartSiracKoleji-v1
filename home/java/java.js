// SLİDER MANUEL KONTROL JAVASCRİPT
document.addEventListener('DOMContentLoaded', function() {
  
  // SLİDER OTOMATİK GEÇİŞİ KAPAT
  const heroCarousel = document.getElementById('heroCarousel');
  
  if (heroCarousel) {
    // Bootstrap Carousel instance oluştur - OTOMATİK GEÇİŞ KAPALI
    const carousel = new bootstrap.Carousel(heroCarousel, {
      interval: false, // Otomatik geçişi KAPAT
      wrap: true,
      ride: false
    });
    
    // MANUEL KONTROL MESAJI EKLE
    const manualMessage = document.createElement('div');
    manualMessage.className = 'manual-control-message';
    manualMessage.innerHTML = '<i class="fas fa-hand-point-up"></i> Slaytları değiştirmek için butonlara tıklayın';
    heroCarousel.appendChild(manualMessage);
    
    // BUTONLARA TIKLAMA EFEKTLERİ EKLE
    const prevButton = heroCarousel.querySelector('.carousel-control-prev');
    const nextButton = heroCarousel.querySelector('.carousel-control-next');
    const indicators = heroCarousel.querySelectorAll('.carousel-indicators [data-bs-target]');
    
    // ÖNCEKİ BUTON
    if (prevButton) {
      prevButton.addEventListener('click', function(e) {
        e.preventDefault();
        // Buton efekti
        slideButtonEffect(this);
        // Slider'ı değiştir
        carousel.prev();
      });
    }
    
    // SONRAKİ BUTON
    if (nextButton) {
      nextButton.addEventListener('click', function(e) {
        e.preventDefault();
        // Buton efekti
        slideButtonEffect(this);
        // Slider'ı değiştir
        carousel.next();
      });
    }
    
    // İNDİKATÖR BUTONLARI
    indicators.forEach(indicator => {
      indicator.addEventListener('click', function(e) {
        e.preventDefault();
        // Buton efekti
        slideButtonEffect(this);
      });
    });
    
    // BUTON TIKLAMA EFEKTİ FONKSİYONU
    function slideButtonEffect(button) {
      // Animasyon efekti
      button.style.transform = 'translateY(-50%) scale(0.9)';
      button.style.backgroundColor = 'var(--secondary-color)';
      
      setTimeout(() => {
        button.style.transform = 'translateY(-50%) scale(1.1)';
      }, 150);
      
      setTimeout(() => {
        button.style.transform = 'translateY(-50%)';
        button.style.backgroundColor = '';
      }, 300);
    }
    
    // SLİDER İÇERİĞİ ANİMASYONU
    heroCarousel.addEventListener('slide.bs.carousel', function(e) {
      const activeSlide = e.relatedTarget;
      const heroContent = activeSlide.querySelector('.hero-content');
      
      // İçerik animasyonunu sıfırla
      heroContent.style.opacity = '0';
      heroContent.style.transform = 'translateY(30px)';
      
      // Yeni animasyon başlat
      setTimeout(() => {
        heroContent.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        heroContent.style.opacity = '1';
        heroContent.style.transform = 'translateY(0)';
      }, 300);
    });
    
    // KLAVYE KONTROLLERİ
    document.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') {
        // Sol ok - önceki slayt
        if (prevButton) {
          prevButton.click();
        }
        e.preventDefault();
      } else if (e.key === 'ArrowRight') {
        // Sağ ok - sonraki slayt
        if (nextButton) {
          nextButton.click();
        }
        e.preventDefault();
      } else if (e.key >= '1' && e.key <= '3') {
        // 1-3 arası rakamlar - direkt slayta git
        const slideIndex = parseInt(e.key) - 1;
        const targetIndicator = indicators[slideIndex];
        if (targetIndicator) {
          targetIndicator.click();
        }
        e.preventDefault();
      }
    });
    
    // TOUCH/SWIPE DESTEĞİ (MOBİL)
    let touchStartX = 0;
    let touchEndX = 0;
    
    heroCarousel.addEventListener('touchstart', function(e) {
      touchStartX = e.changedTouches[0].screenX;
    });
    
    heroCarousel.addEventListener('touchend', function(e) {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    });
    
    function handleSwipe() {
      const swipeThreshold = 50;
      
      if (touchEndX < touchStartX - swipeThreshold) {
        // Sola swipe - next
        if (nextButton) nextButton.click();
      }
      
      if (touchEndX > touchStartX + swipeThreshold) {
        // Sağa swipe - prev
        if (prevButton) prevButton.click();
      }
    }
    
    // 30 SANİYE HAREKETSİZ KALINCA İPUCU GÖSTER
    let idleTimer;
    const IDLE_TIMEOUT = 30000; // 30 saniye
    
    function resetIdleTimer() {
      clearTimeout(idleTimer);
      idleTimer = setTimeout(showHint, IDLE_TIMEOUT);
    }
    
    function showHint() {
      // Slider üzerinde ipucu göster
      const hint = document.createElement('div');
      hint.innerHTML = `
        <div style="
          position: absolute;
          bottom: 100px;
          left: 50%;
          transform: translateX(-50%);
          background: rgba(0,0,0,0.8);
          color: white;
          padding: 12px 20px;
          border-radius: 10px;
          z-index: 100;
          text-align: center;
          backdrop-filter: blur(10px);
          border: 2px solid var(--secondary-color);
          animation: bounceHint 2s infinite;
          font-size: 14px;
        ">
          <i class="fas fa-arrow-left"></i>
          Slaytları değiştirmek için butonları kullanın
          <i class="fas fa-arrow-right"></i>
        </div>
      `;
      
      heroCarousel.appendChild(hint);
      
      // 4 saniye sonra kaldır
      setTimeout(() => {
        if (hint.parentNode) {
          hint.remove();
        }
      }, 4000);
      
      // Bounce animasyonu
      const style = document.createElement('style');
      style.textContent = `
        @keyframes bounceHint {
          0%, 100% { transform: translateX(-50%) translateY(0); }
          50% { transform: translateX(-50%) translateY(-8px); }
        }
      `;
      document.head.appendChild(style);
    }
    
    // Fare ve dokunma hareketlerini izle
    heroCarousel.addEventListener('mousemove', resetIdleTimer);
    heroCarousel.addEventListener('touchstart', resetIdleTimer);
    heroCarousel.addEventListener('click', resetIdleTimer);
    
    // Başlangıçta timer'ı başlat
    resetIdleTimer();
  }
  
  // HEADER SCROLL ETKİSİ (Opsiyonel)
  const navbar = document.querySelector('.navbar');
  
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
});
document.addEventListener("DOMContentLoaded", function () {

  const heroCarousel = document.getElementById("heroCarousel");

  if (!heroCarousel) return;

  // BOOTSTRAP SLIDER
  const carousel = new bootstrap.Carousel(heroCarousel, {
    interval: 2000,   // ⏱️ 2 saniye
    ride: 'carousel',
    pause: false,     // hover durdurmaz
    wrap: true,
    touch: true       // 📱 TELEFON SWIPE AKTİF
  });

  /* KLAVYE DESTEĞİ (İSTEĞE BAĞLI) */
  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") carousel.prev();
    if (e.key === "ArrowRight") carousel.next();
  });

});
// HEADER SCROLL ETKİSİ
window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

