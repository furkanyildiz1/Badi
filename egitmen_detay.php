    <?php 

    include 'header.php';

    $egitmen_id = $_GET['egitmen_id'];
    $egitmencek = $db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
    $egitmencek->execute(['egitmen_id' => $egitmen_id]);
    $egitmen = $egitmencek->fetch(PDO::FETCH_ASSOC);

    $toplam_ogrenci_sayisi = 0;

    $kurssor = $db->prepare("SELECT * FROM kurslar WHERE egitmen_id=:egitmen_id");
    $kurssor->execute(['egitmen_id' => $egitmen_id]);

    $kurslar = $db->prepare("SELECT * FROM kurslar WHERE egitmen_id=:egitmen_id");
    $kurslar->execute(['egitmen_id' => $egitmen_id]);
    $kurslar_listesi = $kurslar->fetchAll(PDO::FETCH_ASSOC);

    foreach ($kurslar_listesi as $kurs) {
        $ogr_sayisor = $db->prepare("SELECT ogr_sayisi FROM ogr_sayi WHERE kurs_id=:kurs_id");
        $ogr_sayisor->execute(['kurs_id' => $kurs['kurs_id']]);
        $ogr_sayi = $ogr_sayisor->fetch(PDO::FETCH_ASSOC);
        
        if ($ogr_sayi && isset($ogr_sayi['ogr_sayisi'])) {
            $toplam_ogrenci_sayisi += $ogr_sayi['ogr_sayisi'];
        }
    }

    $kursSayisiSor = $db->prepare("SELECT COUNT(*) as kurs_sayisi FROM kurslar WHERE egitmen_id = :egitmen_id");
    $kursSayisiSor->execute(['egitmen_id' => $egitmen_id]);
    $kursSayisiCek = $kursSayisiSor->fetch(PDO::FETCH_ASSOC);

    $kursSayisi = $kursSayisiCek['kurs_sayisi'] ?? 0;
    $kurs_sayisi = count($kurslar_listesi);

    foreach ($kurslar_listesi as $kurs) {
    // Her kursun puan ortalamasını hesaplamak
    $puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
    $puansor->execute(['kurs_id' => $kurs['kurs_id']]);
    $puancek = $puansor->fetch(PDO::FETCH_ASSOC);

    $ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır
    $toplam_puan += $ortalama_puan;

    $egitmen_puani = $kurs_sayisi > 0 ? round($toplam_puan / $kurs_sayisi, 2) : 0;
}

     ?>
        <!-- Start Page Title Area -->
        <section class="page-title-area item-bg1">
            <div class="container">
                <div class="page-title-content">
                    <h2>Eğitmen Hakkında</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Anasayfa</a></li>
                        <li class="breadcrumb-item"></li>
                        <li class="primery-link">Eğitmen Detay</li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- End Page Title Area -->

        <!-- Start Instructor Single Details Area -->
        <div class="instructor-single-details ptb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="instructor-sidebar-sticky">
                            <div class="image">
                                <img src="<?php echo $egitmen['egitmen_resimyol']; ?>" alt="image">
                            </div>
                            <div class="content">
                                <div class="instructor-content">
                                    <h3><?php echo $egitmen['egitmen_adsoyad']; ?></h3>
                                    <p><?php echo $egitmen['egitmen_rol']; ?></p>
                                </div>
                                <div class="instructor-info">
                                    <ul>
                                        <li><i class='bx bx-star'></i> <span><strong>Eğitmenin Puanı:</strong> <?php echo $egitmen_puani ?> </span></li>
                                    </ul>
                                </div>
                                <div class="instructor-social">
                                    <h4>Eğitmeni Takip Et:</h4>
                                    <ul>
                                        <li><a href="<?php echo $egitmen['egitmen_medyabir']; ?>"><img src="assets/img/icon/fb.svg" alt="icon"></a></li>
                                        <li><a href="<?php echo $egitmen['egitmen_medyaiki']; ?>"><img src="assets/img/icon/ins.svg" alt="icon"></a></li>
                                        <li><a href="<?php echo $egitmen['egitmen_medyauc']; ?>"><img src="assets/img/icon/tw.svg" alt="icon"></a></li>
                                        <li><a href="<?php echo $egitmen['egitmen_medyadort']; ?>"><img src="assets/img/icon/pn.svg" alt="icon"></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="instructor-single-content">
                            <h2><?php echo $egitmen['egitmen_adsoyad']; ?></h2>
                            <p class="sub-title"><?php echo $egitmen['egitmen_rol']; ?></p>

                            <?php echo $egitmen['egitmen_hakkinda']; ?>

                            <div class="edu-counter-area02 ptb-100">
                                <div class="row justify-content-center">
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="counter-box02">
                                            <img src="assets/img/icon/counter-1.svg" alt="icon">
                                            <h3><span class="odometer" data-count="<?php echo $toplam_ogrenci_sayisi; ?>">00</span></h3>
                                            <p>Aktif Öğrenci Sayısı</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="counter-box02">
                                            <img src="assets/img/icon/counter-2.svg" alt="icon">
                                            <h3><span class="odometer" data-count="<?php echo $kursSayisi; ?>">00</span></h3>
                                            <p>Kurs Sayısı</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="counter-box02">
                                            <img src="assets/img/icon/counter-3.svg" alt="icon">
                                            <h3><span class="odometer" data-count="20">00</span></h3>
                                            <p>(YAPIM ASAMASINDA) <!-- sertifikalı kurslar --></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edu-courses-area pb-70">
                                <div class="edu-section-title text-start">
                                    <h2>Eğitmenin Popüler <span class="shape02">Kursları</span></h2>
                                </div>
                                <div class="row justify-content-center">
                                    <?php while($kurscek=$kurssor->fetch(PDO::FETCH_ASSOC)) { 

                                        $altkategorisor=$db->prepare("SELECT * FROM alt_kategoriler WHERE alt_kategori_id=:alt_kategori_id");
                                        $altkategorisor->execute([
                                            'alt_kategori_id' => $kurscek['alt_kategori_id']
                                        ]);
                                        $altkategoricek=$altkategorisor->fetch(PDO::FETCH_ASSOC);

                                        $ogrsayisor = $db->prepare("SELECT * FROM ogr_sayi WHERE kurs_id=:kurs_id");
                                        $ogrsayisor->execute([
                                            'kurs_id' => $kurscek['kurs_id']
                                        ]);
                                        $ogrsayicek = $ogrsayisor->fetch(PDO::FETCH_ASSOC);

                                        $puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
                                        $puansor->execute([
                                            'kurs_id' => $kurscek['kurs_id']
                                        ]);
                                        $puancek = $puansor->fetch(PDO::FETCH_ASSOC);

                                        $ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır

                                    ?>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="single-courses-box">
                                            <div class="image">
                                                <a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>" class="d-block">
                                                    <img src="<?php echo $kurscek['resim_yol']; ?>" alt="image">
                                                </a>
                                                <div class="cr-tag">
                                                    <a href="#"><span><?php echo $altkategoricek['ad']; ?></span></a>
                                                </div>
                                            </div>
                                            <div class="content">
                                                <span class="cr-price" ><?php echo number_format($kurscek['fiyat'], 0, '.', ''); ?> TL</span>
                                                <h3><a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>"><?php echo $kurscek['baslik']; ?></a></h3>
                                                <ul class="cr-items" >
                                                    <li>
                                                        <i class='bx bx-user'></i> 
                                                        <span>
                                                            <?php echo isset($ogrsayicek['ogr_sayisi']) && $ogrsayicek['ogr_sayisi'] ? $ogrsayicek['ogr_sayisi'] : 0; ?> Öğrenci
                                                        </span>
                                                    </li>
                                                    <li><i class='bx bx-time-five'></i> <span><?php echo $kurscek['sure']; ?> Saat</span></li>
                                                    <li>
                                                        <i class='bx bx-star'></i> 
                                                        <span>
                                                            <?php echo isset($ortalama_puan) && $ortalama_puan ? $ortalama_puan : 0; ?> Puan
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                        <?php } ?>
                                    <div class="section-button">
                                        <a href="#" class="default-btn">Tüm Kurslar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <!-- End Instructor Single Details Area -->

        <?php include 'footer.php'; ?>