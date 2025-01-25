<?php 

    include 'header.php';

$kurs_id = $_GET['kurs_id'];
$kurscek = $db->prepare("SELECT * FROM kurslar WHERE kurs_id=:kurs_id");
$kurscek->execute(['kurs_id' => $kurs_id]);
$kurs = $kurscek->fetch(PDO::FETCH_ASSOC);

if (!$kurs) {
    echo "<div class='alert alert-danger'>Bu kursa ait bir veri bulunamadı.</div>";
    include 'footer.php';
    exit; // Kodun devamını çalıştırma
}

$kategorisor = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id=:kategori_id");
$kategorisor->execute([
    'kategori_id' => $kurs['kategori_id']
]);
$kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC);

$altkategorisor = $db->prepare("SELECT * FROM alt_kategoriler WHERE alt_kategori_id=:alt_kategori_id");
$altkategorisor->execute([
    'alt_kategori_id' => $kurs['alt_kategori_id']
]);
$altkategoricek = $altkategorisor->fetch(PDO::FETCH_ASSOC);

$seviyesor = $db->prepare("SELECT * FROM kurs_seviye WHERE kurs_seviye_id=:kurs_seviye_id");
$seviyesor->execute([
    'kurs_seviye_id' => $kurs['kurs_seviye_id']
]);
$seviyecek = $seviyesor->fetch(PDO::FETCH_ASSOC);

$ogrsayisor = $db->prepare("SELECT * FROM ogr_sayi WHERE kurs_id=:kurs_id");
$ogrsayisor->execute([
    'kurs_id' => $kurs['kurs_id']
]);
$ogrsayicek = $ogrsayisor->fetch(PDO::FETCH_ASSOC);

$egitmensor=$db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
$egitmensor->execute([
    'egitmen_id' => $kurs['egitmen_id']
]);
$egitmencek=$egitmensor->fetch(PDO::FETCH_ASSOC);
$egitmen_id=$egitmencek['egitmen_id'];

$puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
$puansor->execute([
    'kurs_id' => $kurs['kurs_id']
]);
$puancek = $puansor->fetch(PDO::FETCH_ASSOC);

$ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır


function kontrol($veri) {
    return $veri ? htmlspecialchars($veri) : "Veri bulunamadı";
}


$kategorisor = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id=:kategori_id");
$kategorisor->execute([
    'kategori_id' => $kurs['kategori_id']
]);
$kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC);

$ogrsayisor = $db->prepare("SELECT * FROM ogr_sayi WHERE kurs_id=:kurs_id");
$ogrsayisor->execute([
    'kurs_id' => $kurs['kurs_id']
]);
$ogrsayicek = $ogrsayisor->fetch(PDO::FETCH_ASSOC);

$kursSayisiSor = $db->prepare("SELECT COUNT(*) as kurs_sayisi FROM kurslar WHERE egitmen_id = :egitmen_id");
$kursSayisiSor->execute(['egitmen_id' => $egitmen_id]);
$kursSayisiCek = $kursSayisiSor->fetch(PDO::FETCH_ASSOC);

$kursSayisi = $kursSayisiCek['kurs_sayisi'] ?? 0; // Eğer sonuç yoksa varsayılan 0 olarak ayarlanır

$kurslar = $db->prepare("SELECT kurs_id FROM kurslar WHERE egitmen_id=:egitmen_id");
$kurslar->execute(['egitmen_id' => $egitmen_id]);
$kurslar_listesi = $kurslar->fetchAll(PDO::FETCH_ASSOC);

$query = $db->prepare("SELECT * FROM kurs_icerik WHERE kurs_id =:kurs_id");
$query->execute(['kurs_id' => $kurs['kurs_id']]);

$toplam_ogrenci_sayisi = 0;

foreach ($kurslar_listesi as $kurs) {
    $ogr_sayisor = $db->prepare("SELECT ogr_sayisi FROM ogr_sayi WHERE kurs_id=:kurs_id");
    $ogr_sayisor->execute(['kurs_id' => $kurs['kurs_id']]);
    $ogr_sayi = $ogr_sayisor->fetch(PDO::FETCH_ASSOC);
    
    if ($ogr_sayi && isset($ogr_sayi['ogr_sayisi'])) {
        $toplam_ogrenci_sayisi += $ogr_sayi['ogr_sayisi'];
    }
}

$toplam_puan = 0;
$kurs_sayisi = count($kurslar_listesi);

    $toplam_puan += $ortalama_puan;

$kurscek = $db->prepare("SELECT * FROM kurslar WHERE kurs_id=:kurs_id");
$kurscek->execute(['kurs_id' => $kurs_id]);
$kurs = $kurscek->fetch(PDO::FETCH_ASSOC);

$egitmen_puani = $kurs_sayisi > 0 ? round($toplam_puan / $kurs_sayisi, 2) : 0;

$sonKursSor = $db->prepare("SELECT * FROM kurslar ORDER BY olusturma_tarihi DESC LIMIT 3");
$sonKursSor->execute();

 ?>
 <br><br><br>

<style type="text/css">
    .rating {
    display: flex;
    align-items: center;
    font-size: 0.9em;
    color: #f39c12; /* Yıldız rengi */
}

.rating-score {
    margin-right: 10px;
    font-weight: bold;
    color: orange; /* Puan rengi */
}

.rating i {
    margin: 0 2px; /* Yıldızlar arası boşluk */
}

</style>

        <!-- Start EduMim Page Title Area -->
        <section class="page-title-area item-bg1">
            <div class="container">
                <div class="page-title-content">
                    <h2>Kurs Detay</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Sayfalar</a></li>
                        <li class="breadcrumb-item"></li>
                        <li class="primery-link">Kurs Detay</li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- End EduMim Page Title Area -->

        <!-- Start Edu Single Course Area -->
        <div class="single-course-area ptb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="single-course-desc">
                            <div class="single-course-image">
                                <img src="<?php echo $kurs['resim_yol']; ?>" alt="image">
                            </div>
                            <div class="single-course-content">
                                <p class="course-catgy"><?php echo $altkategoricek['ad']; ?></p>
                                <h2><?php echo $kurs['baslik']; ?></h2>

                                <div class="user-details">
                                    <img src="<?php echo $egitmencek['egitmen_resimyol']; ?>" alt="image">
                                    <p><span>Eğitmen :</span> <a href="#"><?php echo $egitmencek['egitmen_adsoyad']; ?></a></p>
                                    <p class="course-date"><span>Son Güncelleme:</span> <a href="#">
                                    <?php setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');
                                    echo strftime('%d %B %Y', strtotime($kurs['guncelleme_tarihi'])); ?>
                                    </a></p>
                                </div>
                            </div>
                            <div class="course-tabs">
                                <nav>
                                    <div class="nav course-nav" id="nav-tab" role="tablist">
                                        <button class="course-link active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true">Genel</button>
                                        <button class=" course-link" id="nav-carriculum-tab" data-bs-toggle="tab" data-bs-target="#nav-carriculum" type="button" role="tab" aria-controls="nav-carriculum" aria-selected="false">İçerik</button>
                                        <button class=" course-link" id="nav-instructor-tab" data-bs-toggle="tab" data-bs-target="#nav-instructor" type="button" role="tab" aria-controls="nav-instructor" aria-selected="false">Eğitmen</button>
                                        <button class=" course-link" id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews" type="button" role="tab" aria-controls="nav-reviews" aria-selected="false">Yorumlar</button>
                                    </div>
                                </nav>
                                <div class="single-course-tab" id="nav-tabContent">
                                    <div class="overview-panel fade active show" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                        <div class="overview-content cmb-30">
                                            <h3 class="course-desc-heading">Kurs Detayları</h3>

                                            <?php echo $kurs['aciklama']; ?>

                                        </div>
                        
                                    </div>
                                    <div class="carriculum-panel fade" id="nav-carriculum" role="tabpanel" aria-labelledby="nav-carriculum-tab">
                                        <div class="carriculum-content cmb-30">
                                            <h3 class="course-desc-heading">Kurs Hakkında</h3>
                                            <ul>
                                                <li><p><strong>Seviye:</strong> <?php echo $seviyecek['seviye_ad']; ?></p></li>
                                                <li><p><strong>Dil:</strong> Türkçe</p></li>
                                                <li><p><strong>Süre:</strong> <?php echo $kurs['sure']; ?> Saat</p></li>
                                            </ul>
                                        </div>

                                        <div class="accordion" id="accordionFlushExample">
                                            <?php 
                                            $say = 0;
                                            while ($kurs_icerik = $query->fetch(PDO::FETCH_ASSOC)) {
                                                $say++;
                                            ?>
                                                <div class="accordion-item lesson-item">
                                                    <h2 class="lesson-header" id="flush-heading<?php echo $say; ?>">
                                                        <button class="accordion-button lession-button collapsed" type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#flush-collapse<?php echo $say; ?>" 
                                                                aria-expanded="false" 
                                                                aria-controls="flush-collapse<?php echo $say; ?>">
                                                            <?php echo htmlspecialchars($kurs_icerik['icerik_ad']); ?> 
                                                            <span><?php echo htmlspecialchars($kurs_icerik['icerik_ders_sayi']); ?> Ders</span>
                                                        </button>
                                                    </h2>
                                                    <div id="flush-collapse<?php echo $say; ?>" 
                                                         class="accordion-collapse collapse lesson-collapse" 
                                                         aria-labelledby="flush-heading<?php echo $say; ?>" 
                                                         data-bs-parent="#accordionFlushExample">
                                                        <div class="lesson-item-body">
                                                            <h3>İçerik Açıklaması</h3>
                                                            <p><?php echo $kurs_icerik['icerik_aciklama']; ?></p>
                                                            <h4><?php echo htmlspecialchars($kurs_icerik['icerik_ders_sayi']); ?> Ders İçeriyor</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                            } 
                                            ?>
                                        </div>


                                    </div>
                                    <div class="instructor-panel fade" id="nav-instructor" role="tabpanel" aria-labelledby="nav-instructor-tab">
                                        <div class="single-instructor-content">
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="instructor-img">
                                                        <img src="<?php echo $egitmencek['egitmen_resimyol']; ?>" alt="image">
                                                    </div>
                                                </div>
                                                <div class="col-lg-7">
                                                    <div class="single-instructor-info">
                                                        <h2><?php echo $egitmencek['egitmen_adsoyad']; ?></h2>
                                                        <p class="sub-title" ><?php echo $egitmencek['egitmen_rol']; ?></p>
                                                        <ul>
                                                            <li><img src="assets/img/icon/file2.svg" alt="icon"> <span><?php echo $kurs_sayisi ?>+ Kurs</span> </li>
                                                            <li><img src="assets/img/icon/user2.svg" alt="icon"> <span><?php echo $toplam_ogrenci_sayisi ?>+ Öğrenci</span> </li>
                                                            <li><img src="assets/img/icon/star.svg" alt="icon"> <span>İşinin Uzmanı</span> </li>
                                                            <li><img src="assets/img/icon/like.svg" alt="icon"> <span><?php echo $egitmen_puani ?> Ortalama Puan</span> </li>
                                                        </ul>
                                                        <ul class="social-links">
                                                            <li><a href="<?php echo $egitmencek['egitmen_medyabir']; ?>"><img src="assets/img/social/ln.svg" alt="icon"></a></li>
                                                            <li><a href="<?php echo $egitmencek['egitmen_medyaiki']; ?>"><img src="assets/img/social/twiiter.svg" alt="icon"></a></li>
                                                            <li><a href="<?php echo $egitmencek['egitmen_medyauc']; ?>"><img src="assets/img/social/instra.svg" alt="icon"></a></li>
                                                            <li><a href="<?php echo $egitmencek['egitmen_medyadort']; ?>"><img src="assets/img/social/youtube.svg" alt="icon"></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="single-instructor-desc">
                                                
                                                <?php echo $egitmencek['egitmen_hakkinda']; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="reviews-panel fade" id="nav-reviews" role="tabpanel" aria-labelledby="nav-reviews-tab">
                                        <div class="lesseon-review-section">
                                            <div class="student-reating">
                                                <div class="row">
                                                    <div class="col-lg-8 col-md-6">
                                                        <div class="lession-review-items">
                                                            <ul>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                            </ul>
                                                            <div class="progress-section">
                                                                <div class="progress">
                                                                    <div class="progress-bar psc02" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="67" style="max-width: 67%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="progress-title">
                                                                <span class="title">67%</span>
                                                            </div>
                                                        </div>
                                                        <div class="lession-review-items">
                                                            <ul>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                            </ul>
                                                            <div class="progress-section">
                                                                <div class="progress">
                                                                    <div class="progress-bar psc02" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="67" style="max-width: 67%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="progress-title">
                                                                <span class="title">67%</span>
                                                            </div>
                                                        </div>
                                                        <div class="lession-review-items">
                                                            <ul>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                            </ul>
                                                            <div class="progress-section">
                                                                <div class="progress">
                                                                    <div class="progress-bar psc02" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="67" style="max-width: 67%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="progress-title">
                                                                <span class="title">67%</span>
                                                            </div>
                                                        </div>
                                                        <div class="lession-review-items">
                                                            <ul>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                            </ul>
                                                            <div class="progress-section">
                                                                <div class="progress">
                                                                    <div class="progress-bar psc02" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="67" style="max-width: 67%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="progress-title">
                                                                <span class="title">67%</span>
                                                            </div>
                                                        </div>
                                                        <div class="lession-review-items">
                                                            <ul>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                            </ul>
                                                            <div class="progress-section">
                                                                <div class="progress">
                                                                    <div class="progress-bar psc02" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="67" style="max-width: 67%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="progress-title">
                                                                <span class="title">67%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="lession-total-review">
                                                            <h3>4.9</h3>
                                                            <ul>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                                <li><i class="bx bxs-star disstar"></i></li>
                                                            </ul>
                                                            <p>(2 Review)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="student-review-section">
                                                <h4>Reviews</h4>
                                                <div class="student-review-items">
                                                    <img src="assets/img/all-img/cmnt-1.png" alt="image">
                                                    <ul>
                                                        <li><i class="bx bxs-star"></i></li>
                                                        <li><i class="bx bxs-star"></i></li>
                                                        <li><i class="bx bxs-star"></i></li>
                                                        <li><i class="bx bxs-star disstar"></i></li>
                                                        <li><i class="bx bxs-star disstar"></i></li>
                                                    </ul>
                                                    <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.</p>
                                                    <h3>Daniel Smith</h3>
                                                    <span>Jan 24, 2023</span>
                                                </div>
                                                <div class="student-review-items">
                                                    <img src="assets/img/all-img/cmnt-2.png" alt="image">
                                                    <ul>
                                                        <li><i class="bx bxs-star"></i></li>
                                                        <li><i class="bx bxs-star"></i></li>
                                                        <li><i class="bx bxs-star"></i></li>
                                                        <li><i class="bx bxs-star disstar"></i></li>
                                                        <li><i class="bx bxs-star disstar"></i></li>
                                                    </ul>
                                                    <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration.</p>
                                                    <h3>Daniel Smith</h3>
                                                    <span>Jan 24, 2023</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="single-course-sidebar">
                            <div class="course-widget">
                                <div class="course-video">
                                    <a href="<?php echo $kurs['video_yol']; ?>" class="popup-youtube">
                                        <img src="<?php echo $kurs['resim_yol']; ?>" alt="image">
                                        <i class='bx bx-play cr-video-btn'></i>
                                    </a>
                                </div>
                                <div class="sidebar-content">
                                    <h3><?php echo number_format($kurs['fiyat'], 0, '.', ''); ?> TL</h3>
                                    <a href="#" class="default-btn course-btn">Şimdi Al</a>
                                    <ul class="courses-details">
                                        <li><div class="icon"><img src="assets/img/icon/user.svg" alt="icon"> Eğitmen</div> <p><?php echo $egitmencek['egitmen_adsoyad']; ?></p></li>
                                        <li><div class="icon"><img src="assets/img/icon/star.svg" alt="icon"> Puan</div> <p><?php echo $ortalama_puan; ?> Puan</p></li>
                                        <li><div class="icon"><img src="assets/img/icon/clock.svg" alt="icon"> Kurs Süresi</div> <p><?php echo $kurs['sure']; ?> Saat</p></li>
                                        <li><div class="icon"><img src="assets/img/icon/user2.svg" alt="icon"> Öğrenci Sayısı</div> <p><?php echo isset($ogrsayicek['ogr_sayisi']) && $ogrsayicek['ogr_sayisi'] ? $ogrsayicek['ogr_sayisi'] : 0; ?>
                                            Öğrenci</p></li>
                                        <li><div class="icon"><img src="assets/img/icon/target.svg" alt="icon"> Kurs Seviyesi</div> <p><?php echo $seviyecek['seviye_ad']; ?></p></li>
                                        <li><div class="icon"><img src="assets/img/icon/web.svg" alt="icon"> Dil</div> <p>Türkçe</p></li>
                                    </ul>
                                    <ul class="course-shared">
                                        <li class="title">Paylaş:</li>
                                        <li><a href="#"><img src="assets/img/icon/fb.svg" alt="icon"></a></li>
                                        <li><a href="#"><img src="assets/img/icon/tw.svg" alt="icon"></a></li>
                                        <li><a href="#"><img src="assets/img/icon/ins.svg" alt="icon"></a></li>
                                        <li><a href="#"><img src="assets/img/icon/pn.svg" alt="icon"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="widget widget-resent-course">
                                <h3 class="widget-title">Son Eklenen Kurslar</h3>
                                <?php while($sonKursCek=$sonKursSor->fetch(PDO::FETCH_ASSOC)) {
                                    $puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
                                    $puansor->execute([
                                        'kurs_id' => $sonKursCek['kurs_id']
                                    ]);
                                    $puancek = $puansor->fetch(PDO::FETCH_ASSOC);

                                    $ortalamaa_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır
                                 ?>
                                <article class="item">
                                    <a href="#" class="thumb"><img src="<?php echo $sonKursCek['resim_yol']; ?>" alt="iamge"></a>
                                    <div class="rating">
                                        <span class="rating-score"><?php echo number_format($ortalamaa_puan, 1, ',', ''); ?></span>
                                        <?php
                                        $filledStars = floor($ortalamaa_puan); // Tam dolu yıldızlar
                                        $halfStar = ($ortalamaa_puan - $filledStars) >= 0.5; // Yarım yıldız kontrolü
                                        $emptyStars = 5 - $filledStars - ($halfStar ? 1 : 0); // Boş yıldızlar

                                        // Dolu yıldızları ekle
                                        for ($i = 0; $i < $filledStars; $i++) {
                                            echo '<i class="fas fa-star"></i>';
                                        }

                                        // Yarım yıldız ekle
                                        if ($halfStar) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        }

                                        // Boş yıldızları ekle
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <div class="info">
                                        <h4 class="title">
                                            <a href="#">
                                                <?php 
                                                $maxLength = 20; // Maksimum karakter sayısı
                                                $baslik = $sonKursCek['baslik']; // Başlık
                                                echo strlen($baslik) > $maxLength 
                                                    ? substr($baslik, 0, $maxLength) . '...' 
                                                    : $baslik;
                                                ?>
                                            </a>
                                        </h4>
                                        <span><?php echo $sonKursCek['fiyat']; ?> TL</span>
                                    </div>
                                </article>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Edu Single Course Area -->


        <?php include 'footer.php'; ?>