<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>

<!-- Custom JS -->
<script src="/assets/js/public.js"></script>

<!-- Initialize AOS (Animate On Scroll) -->
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
</script>

<!-- Back to Top Button -->
<script>
    document.getElementById('backToTop')?.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

<!-- Smooth Scroll for Anchor Links -->
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
</script>

<!-- Google Analytics (opcional) -->
<?php if (!empty($config['google_analytics_id'])): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $config['google_analytics_id'] ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= $config['google_analytics_id'] ?>');
</script>
<?php endif; ?>

<!-- Facebook Pixel (opcional) -->
<?php if (!empty($config['facebook_pixel_id'])): ?>
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= $config['facebook_pixel_id'] ?>');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id=<?= $config['facebook_pixel_id'] ?>&ev=PageView&noscript=1"/>
</noscript>
<?php endif; ?>

<?php if (!empty($additionalJS)): ?>
<!-- JavaScript Adicional -->
<?= $additionalJS ?>
<?php endif; ?>

</body>
</html>