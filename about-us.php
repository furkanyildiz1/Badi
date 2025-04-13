<?php

include 'nedmin/netting/baglan.php';
include 'header.php';

$hakkimizdasor = $db->prepare("SELECT * FROM hakkimizda WHERE hakkimizda_id=:hakkimizda_id");
$hakkimizdasor->execute([
    'hakkimizda_id' => 0
]);
$hakkimizdacek = $hakkimizdasor->fetch(PDO::FETCH_ASSOC);

$hakkimizdamarkasor = $db->prepare("SELECT * FROM markalar WHERE markavitrin_durum=:markavitrin_durum");
$hakkimizdamarkasor->execute([
    'markavitrin_durum' => 1
]);

$ssssor = $db->prepare("SELECT * FROM sss ORDER BY sss_sira ASC");
$ssssor->execute();

?>

<!-- Start Page Title Area -->
<section class="page-title-area item-bg1">
    <div class="container">
        <div class="page-title-content">
            <h2>Hakkımızda</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Anasayfa</a></li>
                <li class="breadcrumb-item"></li>
                <li class="primery-link">Hakkımızda</li>
            </ul>
        </div>
    </div>
</section>
<!-- End Page Title Area -->

<!-- Start About Area -->
<div class="edu-about-area ptb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-12">
                <div class="edu-about-image">
                    <img src="assets/img/all-img/about5.png" alt="banner-img">
                </div>
            </div>
            <div class="col-lg-5 col-md-12">
                <div class="edu-about-content">
                    <h2> <span class="shape02"><?php echo $hakkimizdacek['hakkimizda_baslik'] ?></span></h2>
                    <p><?php echo $hakkimizdacek['hakkimizda_icerik'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End About Area -->

<!-- Start Services Area -->
<div class="edu-services-area ptb-100">
    <div class="container">
        <div class="edu-section-title">
            <h2>Neden <span class="shape02">Badi Akademi</span></h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-sm-6 col-md-6">
                <div class="edu-services-box">
                    <div class="icon">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <h3>Her Yerden Öğrenin</h3>
                    <p>İnternet bağlantınız olan her yerden, masaüstü, mobil veya tablet cihazlarınızla kolayca eğitim
                        alın.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-md-6">
                <div class="edu-services-box">
                    <div class="icon">
                        <i class='bx bx-chalkboard'></i>
                    </div>
                    <h3>Kapsamlı ve Güncel İçerik</h3>
                    <p>Alanında uzman eğitmenlerden oluşturulan kapsamlı ve güncel ders içerikleriyle bilgi birikiminizi
                        sürekli geliştirin.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-md-6">
                <div class="edu-services-box">
                    <div class="icon">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <h3>Esnek ve Uygun Fiyatlı Eğitim</h3>
                    <p>İhtiyaçlarınıza uygun esnek öğrenme seçenekleri ve bütçenize uygun fiyatlarla kaliteli eğitime
                        ulaşın.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Services Area Area -->


<!-- Start platfroom Area -->
<div class="edu-platfrom-area ptb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <div class="edu-platfrom-content">
                    <h2> <span class="shape02">Vizyonumuz</span> </h2>
                    <p><?php echo $hakkimizdacek['hakkimizda_vizyon'] ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <div class="edu-platfrom-image">
                    <img src="assets/img/all-img/about2.png" alt="banner-img">
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <div class="edu-platfrom-content">
                    <h2> <span class="shape02">Misyonumuz</span> </h2>
                    <p><?php echo $hakkimizdacek['hakkimizda_misyon'] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End platfrom Area -->



<!-- Start Courses Block Area -->
<div class="edu-courseBlock-area pt-70 pb-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="single-courseBlock-box cblockBg mb-30">
                    <div class="content">
                        <p class="sub-title">Kariyerinde bir adım ileri!</p>
                        <h3><span class="shape02">Yüz Yüze</span> Eğitimler</h3>
                        <p>Gerçek ortamda birebir etkileşimle öğrenmenin gücünü keşfedin!</p>
                        <a href="#" class="default-btn">Daha Fazla</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="single-courseBlock-box cblockBg02">
                    <div class="content">
                        <p class="sub-title">Kariyerinde bir adım ileri!</p>
                        <h3><span class="shape02">Çevrimiçi</span> Eğitimler</h3>
                        <p>Zaman ve mekândan bağımsız, öğrenmek artık çok kolay!</p>
                        <a href="#" class="default-btn primery-black">Daha Fazla</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Courses Block Area  -->

<!-- Start Video and Brand Area -->
<div class="edu-video-area">
    <div class="brands-area sectionBg07 ptb-100">
        <div class="container">
            <div class="brands-title">
                <h3>1000+ <span class="mini-shape">Güvenilir</span> Referans</h3>
            </div>
            <!-- Swiper Container -->
            <div class="marka-swiper">
                <div class="swiper-wrapper">
                    <?php while ($hakkimizdamarkacek = $hakkimizdamarkasor->fetch(PDO::FETCH_ASSOC)) { ?>
                        <div class="swiper-slide swiper-marka">
                            <a href="#"><img src="<?php echo $hakkimizdamarkacek['marka_resimyol']; ?>" alt="brands"></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Video Area -->

<!-- Start Faq Area -->
<div id="faq-section" class="edu-faq-area sectionBg15 ptb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <div class="edu-faq-content">
                    <h2>Sıkça Sorulan <span class="shape02">Sorular</span></h2>
                </div>
                <div class="accordion edu-faqs-list" id="accordionFlushExample">
                    <?php

                    $say = 0;

                    while ($ssscek = $ssssor->fetch(PDO::FETCH_ASSOC)) {
                        $say++;
                        ?>
                        <div class="accordion-item faq-item">
                            <h2 class="faq-header" id="flush-heading<?php echo $say; ?>">
                                <button class="accordion-button faq-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#flush-collapse<?php echo $say; ?>"
                                    aria-expanded="false" aria-controls="flush-collapse<?php echo $say; ?>">
                                    <?php echo $ssscek['sss_soru']; ?>
                                </button>
                            </h2>
                            <div id="flush-collapse<?php echo $say; ?>" class="accordion-collapse collapse faq-collapse"
                                aria-labelledby="flush-heading<?php echo $say; ?>" data-bs-parent="#accordionFlushExample">
                                <div class="faq-item-body">
                                    <p>
                                        <?php echo $ssscek['sss_aciklama']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="edu-about-image">
                    <img src="assets/img/all-img/faq.png" alt="banner-img">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Faq Area -->

<?php include 'footer.php'; ?>