<?php

    include 'nedmin/netting/baglan.php';
    include 'header.php';

    $kategorisorr=$db->prepare("SELECT * FROM kategoriler");
    $kategorisorr->execute();

    $toplamKursSorgu = $db->query("SELECT COUNT(*) as toplam FROM kurslar");
    $toplamKurs = $toplamKursSorgu->fetch(PDO::FETCH_ASSOC)['toplam'];
    $gosterilenKursSayisi = 8;
?>

        <!-- Start EduMim Page Title Area -->

        <section class="page-title-area item-bg1">
            <div class="container">
                <div class="page-title-content">
                    <h2>Kurslar</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Anasayfa</a></li>
                        <li class="breadcrumb-item"></li>
                        <li class="primery-link">Kurslar</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- End EduMim Page Title Area -->

         <!-- Start EduMim Courses Area -->
         <div class="edu-courses-area pt-70 pb-100">
            <div class="container">

                <div class="edu-grid-sorting">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-7 result-count">
                            <a href="kurslar_2.php" class="courbtn "><i class='bx bx-grid-alt'></i></a>
                            <a href="kurslar_1.php" class="courbtn active-courbtn"><i class='bx bx-list-ul'></i></a>
                            <p><?php echo "<p>{$toplamKurs} kurstan {$gosterilenKursSayisi} tanesi gösteriliyor</p>"; ?></p>
                        </div>
                        <div class="col-lg-6 col-md-5 ordering">
                            <div class="select-box">
                                <label></label>
                                <select>
                                    <option selected disabled>Filtre: Lütfen Seçiniz</option>
                                    <?php while($kategoricekk=$kategorisorr->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $kategoricekk['kategori_id'] ?>"><?php echo $kategoricekk['ad']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">

                    <?php while($kurscek=$kurssor->fetch(PDO::FETCH_ASSOC)) {

                     $puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
                     $puansor->execute([
                         'kurs_id' => $kurscek['kurs_id']
                     ]);
                     $puancek = $puansor->fetch(PDO::FETCH_ASSOC);
                     
                     $ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0;

                     $puansayisor = $db->prepare("SELECT COUNT(*) as toplam FROM kurs_puan WHERE kurs_id = :kurs_id");
                     $puansayisor->execute([
                         'kurs_id' => $kurscek['kurs_id']
                     ]);
                     $puansayicek = $puansayisor->fetch(PDO::FETCH_ASSOC);
                     $ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0;

                     $kategorisor = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id=:kategori_id");
                     $kategorisor->execute([
                         'kategori_id' => $kurscek['kategori_id']
                     ]);
                     $kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC);

                     $altkategorisor = $db->prepare("SELECT * FROM alt_kategoriler WHERE alt_kategori_id=:alt_kategori_id");
                     $altkategorisor->execute([
                         'alt_kategori_id' => $kurscek['alt_kategori_id']
                     ]);
                     $altkategoricek = $altkategorisor->fetch(PDO::FETCH_ASSOC);

                     $ogrsayisor = $db->prepare("SELECT * FROM ogr_sayi WHERE kurs_id=:kurs_id");
                     $ogrsayisor->execute([
                         'kurs_id' => $kurscek['kurs_id']
                     ]);
                     $ogrsayicek = $ogrsayisor->fetch(PDO::FETCH_ASSOC);

                     $egitmensor=$db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
                     $egitmensor->execute([
                         'egitmen_id' => $kurscek['egitmen_id']
                     ]);
                     $egitmencek=$egitmensor->fetch(PDO::FETCH_ASSOC);

                     ?>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>" class="single-courses-link">
                            <div class="single-courses-box02">
                                <div class="image">
                                    <img src="<?php echo $kurscek['resim_yol']; ?>" alt="image">
                                </div>
                                <div class="content">
                                    <div class="content-herd">
                                        <span class="cr-price" ><?php echo $kurscek['fiyat']; ?> TL</span>
                                        <div class="rating">
                                        <span class="rating-score"><?php echo number_format($ortalama_puan, 1, ',', ''); ?></span>
                                        <?php
                                        $filledStars = floor($ortalama_puan); // Tam dolu yıldızlar
                                        $halfStar = ($ortalama_puan - $filledStars) >= 0.5; // Yarım yıldız kontrolü
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
                                    </div>
                                    
                                    <h3><?php echo $kurscek['baslik']; ?></h3>
                                    <ul class="cr-items" >
                                        <li><i class='bx bx-time'></i> <span><?php echo $kurscek['sure'] ?> Saat</span> </li>
                                        <li><i class='bx bx-user'></i> <span>
                                            <?php echo isset($ogrsayicek['ogr_sayisi']) && $ogrsayicek['ogr_sayisi'] ? $ogrsayicek['ogr_sayisi'] : 0; ?> Öğrenci
                                        </span></li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>

                    <?php } ?>

                    <div class="section-button">
                        <a href="#" class="default-btn">Daha Fazla <i class='bx bx-revision'></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End EduMim Courses Area -->

       <?php include 'footer.php'; ?>
       