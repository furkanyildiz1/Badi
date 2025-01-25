<?php
error_reporting(0);
include 'nedmin/netting/baglan.php';
include 'header.php';

$iletisimayarsor=$db->prepare("SELECT * FROM ayar WHERE ayar_id=:ayar_id");
$iletisimayarsor->execute([
    'ayar_id' => 0
]);
$iletisimayarcek = $iletisimayarsor->fetch(PDO::FETCH_ASSOC);


?>

<?php
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


<!-- Start Page Title Area -->
<section class="page-title-area item-bg1">
    <div class="container">
        <div class="page-title-content">
            <h2>İletişim</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Anasayfa</a></li>
                <li class="breadcrumb-item"></li>
                <li class="primery-link">İletişim</li>
            </ul>
        </div>
    </div>
</section>
<!-- End Page Title Area -->

<!-- Start Contact Area -->
<section class="edu-contact-area ptb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-12">
                <div class="edu-content">
                    <div class="edu-contact-title">
                        <h2><span class="shape02">Sizin</span> İçin Buradayız</h2>
                        <p>Bize ulaşın, sorularınızı yanıtlamak ve projelerinizde size destek olmak için buradayız.</p>
                    </div>
                    <div class="edu-contact-info-box">
                        <div class="icon">
                            <img src="assets/img/svg/mail.svg" alt="icon">
                        </div>
                        <h3>Bize Mail Gönderin:</h3>
                        <p><a href="mailto:<?php echo $iletisimayarcek['ayar_mail']; ?>"><?php echo $iletisimayarcek['ayar_mail']; ?></a></p>
                    </div>

                    <div class="edu-contact-info-box">
                        <div class="icon">
                            <img src="assets/img/svg/call.svg" alt="icon">
                        </div>
                        <h3>Bizi Arayın:</h3>
                        <p><a href="tel:+9<?php echo $iletisimayarcek['ayar_gsm']; ?>"><?php echo $iletisimayarcek['ayar_gsm']; ?></a></p>
                    </div>

                    <div class="edu-contact-info-box">
                        <div class="icon">
                            <img src="assets/img/svg/map.svg" alt="icon">
                        </div>
                        <h3>Ofis Adresimiz: </h3>
                        <p><?php echo $iletisimayarcek['ayar_adres']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12">
                <div class="contact-form">
                    <form id="contactForm" method="POST" action="nedmin/netting/islem.php">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <input type="text"  name="iletisim_adsoyad" class="form-control" id="name" required
                                        data-error="Lütfen adınızı ve soyadınızı giriniz!" placeholder="İsim Soyisim">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <input type="email"  name="iletisim_mail" class="form-control" id="email" required
                                        data-error="Lütfen e-mail adresinizi giriniz!" placeholder="E-mail">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <input type="number" name="iletisim_tel" class="form-control" id="phone_number" required
                                        data-error="Lütfen telefon numarınızı giriniz!"  placeholder="Telefon no*">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="web_link" required
                                        data-error="Lütfen konuyu giriniz!" name="iletisim_konu" placeholder="Konu">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <textarea id="message" class="form-control" cols="30" rows="6"
                                        required data-error="Lütfen mesajınızı giriniz!"
                                        placeholder="Mesajınızı buraya yazınız.." name="iletisim_mesaj"></textarea>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <button name="iletisim_form_gonder" type="submit" class="default-btn"><i class='bx bx-paper-plane'></i> Gönder </button>
                                <div id="msgSubmit" class="h3 text-center hidden"></div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container mb-5">
        <div class="row align-items-center">
            <div class="col-lg-12 col-md-12">
                <iframe style="border:none; border-radius: 30px;"
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d24058.552091933154!2d29.004425!3d41.083876!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab6614539b20f%3A0x53b644f7597c6634!2zw4dlbGlrdGVwZSwgxLBzbWV0IMSwbsO2bsO8IENkLiBObzoxMSwgMzQ0MTMgS8OixJ_EsXRoYW5lL8Swc3RhbmJ1bCwgVMO8cmtpeWU!5e0!3m2!1sen!2sus!4v1726851033003!5m2!1sen!2sus"
                    width="100%" height="450" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>

            </div>
        </div>
    </div>
</section>

<!-- End Contact Area -->

<?php include 'footer.php'; ?>
