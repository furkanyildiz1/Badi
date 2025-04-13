<?php
error_reporting(0);
if ($_GET['durum'] == "ok") { ?>
    <script>
        Swal.fire({
            title: 'Başarılı!',
            text: 'İşlem başarıyla tamamlandı.',
            icon: 'success',
            confirmButtonText: 'Tamam',
            confirmButtonColor: '#ffb000',
            background: '#f9f9f9',
            customClass: {
                popup: 'shadow-lg rounded',
                title: 'text-primary',
                content: 'text-dark',
            },
        });
    </script>
<?php } else if ($_GET['durum'] == "no") { ?>
        <script>
            Swal.fire({
                title: 'Hata!',
                text: 'İşlem başarısız oldu. Lütfen tekrar deneyin.',
                icon: 'error',
                confirmButtonText: 'Tamam',
                confirmButtonColor: '#d33',
                background: '#f9f9f9',
                customClass: {
                    popup: 'shadow-lg rounded',
                    title: 'text-danger',
                    content: 'text-dark',
                },
            });
        </script>

<?php } ?>

<!-- Start Footer Area -->
<footer class="edu-footer-area">
    <div class="container">
        <div class="footer-top-area ptb-100">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <a href="index.php" class="logo">
                            <img src="assets/img/logo/badi akademi.png" alt="image">
                        </a>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
                            been the industry's...</p>
                        <ul class="social-links">
                            <li><a href="<?php echo $ayarcek['ayar_facebook']; ?>" target="_blank" aria-label="Facebook sayfamızı ziyaret et"><i class='bx bxl-facebook'></i></a></li>
                            <li><a href="<?php echo $ayarcek['ayar_google']; ?>" target="_blank" aria-label="LinkedIn profilimizi ziyaret et"><i class='bx bxl-linkedin'></i></a></li>
                            <li><a href="<?php echo $ayarcek['ayar_twitter']; ?>" target="_blank" aria-label="Instagram sayfamızı ziyaret et"><i class='bx bxl-instagram'></i></a></li>
                            <li><a href="<?php echo $ayarcek['ayar_youtube']; ?>" target="_blank" aria-label="YouTube kanalımızı ziyaret et"><i class='bx bxl-youtube'></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget pl-5">
                        <h3>Hızlı Erişim</h3>
                        <ul class="links-list">
                            <li><a href="index.php">Anasayfa</a></li>
                            <li><a href="kurslar_1.php">Kurslarımız</a></li>
                            <li><a href="blog.php">Blog</a></li>
                            <li><a href="about-us.php#faq-section">Sıkça Sorulan Sorular</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h3>Hakkımızda</h3>
                        <ul class="links-list">
                            <li><a href="about-us.php">Hakkımızda</a></li>
                            <li><a href="contact.php">İletişim</a></li>
                            <li><a href="kvkk-gizlilik.php#gizlilik">Gizlilik Politikası</a></li>
                            <li><a href="kvkk-gizlilik.php#kvkk">Kvkk</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h3>Bültene Abone Olun</h3>
                        <div class="footer-newsletter-info">
                            <p>En güncel haberleri kaçırmamak için siz de bültenimize abone olun! </p>

                            <form class="newsletter-form" method="POST" action="nedmin/netting/islem.php"
                                data-toggle="validator">
                                <label><i class='bx bx-envelope-open'></i></label>
                                <input type="text" name="bulten_mail" class="input-newsletter"
                                    placeholder="E-mail adresinizi giriniz" required autocomplete="off">
                                <button name="bulten_kaydet" type="submit" class="default-btn"><i
                                        class='bx bx-paper-plane'></i>
                                    Üye Ol</button>
                                <div id="validator-newsletter" class="form-result"></div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pr-line"></div>
        <div class="footer-bottom-area">
            <p>© Badi 2025 Tüm Hakları Saklıdır </p>
        </div>
    </div>
</footer>
<!-- End Footer Area -->

<!-- Links of JS files -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/magnific-popup.min.js"></script>
<script src="assets/js/nice-select.min.js"></script>
<script src="assets/js/jquery.mixitup.min.js"></script>
<script src="assets/js/appear.min.js"></script>
<script src="assets/js/sticky-sidebar.min.js"></script>
<script src="assets/js/odometer.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/meanmenu.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>


<script>

    // WOW.js Başlatma
    new WOW().init();

    const menu = document.getElementById('sideMenu');
    const menuButton = document.querySelector('.menu-button');

    function toggleMenu() {
        menu.classList.toggle('open');
    }

    // Menü dışına tıklanınca kapat
    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target) && !menuButton.contains(event.target)) {
            menu.classList.remove('open');
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const swiper = new Swiper(".marka-swiper", {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 30,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                },
            },
        });
    });
</script>
</body>

</html>