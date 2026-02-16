// Back to Top Button and Smooth Scroll Functionality
document.addEventListener("DOMContentLoaded", function () {
    // 1. Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // 2. Back to Top Button
    const backToTopButton = document.createElement('button');
    backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopButton.classList.add('back-to-top-button');
    document.body.appendChild(backToTopButton);

    // Style the button dynamically (if not in CSS) or rely on CSS class
    // We'll add basic styles here as a fallback or ensure CSS handles it
    // But better to have styles in CSS. We'll add the class 'back-to-top-button' styles to style.css later or assume individual page CSS handles it.
    // However, to be safe and "premium", let's suggest adding these styles to the main style.css or page css.
    // For now, let's keep the logic here.

    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            backToTopButton.style.display = 'block';
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.style.display = 'none';
            backToTopButton.classList.remove('visible');
        }
    });

    backToTopButton.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
