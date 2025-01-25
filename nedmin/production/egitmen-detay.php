<?php
include 'header.php'; 

$egitmen_id = $_GET['egitmen_id'];
$egitmencek = $db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
$egitmencek->execute(['egitmen_id' => $egitmen_id]);
$egitmen = $egitmencek->fetch(PDO::FETCH_ASSOC);

if (!$egitmen) {
    echo "<div class='alert alert-danger'>Bu kursa ait bir veri bulunamadı.</div>";
    include 'footer.php';
    exit;
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

$puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
$puansor->execute([
    'kurs_id' => $kurs['kurs_id']
]);
$puancek = $puansor->fetch(PDO::FETCH_ASSOC);

$ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır

$kursSayisiSor = $db->prepare("SELECT COUNT(*) as kurs_sayisi FROM kurslar WHERE egitmen_id = :egitmen_id");
$kursSayisiSor->execute(['egitmen_id' => $egitmen_id]);
$kursSayisiCek = $kursSayisiSor->fetch(PDO::FETCH_ASSOC);

$kursSayisi = $kursSayisiCek['kurs_sayisi'] ?? 0; // Eğer sonuç yoksa varsayılan 0 olarak ayarlanır

$kurslar = $db->prepare("SELECT kurs_id FROM kurslar WHERE egitmen_id=:egitmen_id");
$kurslar->execute(['egitmen_id' => $egitmen_id]);
$kurslar_listesi = $kurslar->fetchAll(PDO::FETCH_ASSOC);

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

foreach ($kurslar_listesi as $kurs) {
    // Her kursun puan ortalamasını hesaplamak
    $puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
    $puansor->execute(['kurs_id' => $kurs['kurs_id']]);
    $puancek = $puansor->fetch(PDO::FETCH_ASSOC);

    $ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır
    $toplam_puan += $ortalama_puan;
}

$kurscek = $db->prepare("SELECT * FROM kurslar WHERE kurs_id=:kurs_id");
$kurscek->execute(['kurs_id' => $kurs_id]);
$kurs = $kurscek->fetch(PDO::FETCH_ASSOC);

$egitmen_puani = $kurs_sayisi > 0 ? round($toplam_puan / $kurs_sayisi, 2) : 0;

function kontrol($veri) {
    return $veri ? $veri : "Veri bulunamadı";
}
?>

<style type="text/css">
    .rating {
    display: flex;
    align-items: center;
    font-size: 1.5em;
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

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><strong><?php echo kontrol($egitmen['egitmen_adsoyad']); ?></strong> Detayları</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="form-horizontal form-label-left">

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Hakkında:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($egitmen['egitmen_hakkinda']); ?> </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Rol:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($egitmen['egitmen_rol']); ?> </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Medya 1:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($egitmen['egitmen_medyabir']); ?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Medya 2:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($egitmen['egitmen_medyaiki']); ?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Medya 3:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($egitmen['egitmen_medyauc']); ?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Medya 4:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($egitmen['egitmen_medyadort']); ?></p>
                                </div>
                            </div>

                            <!-- Fancybox kütüphaneleri -->
                            <link href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.css" rel="stylesheet">
                            <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.umd.js"></script>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Resmi:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <?php if (!empty($egitmen['egitmen_resimyol'])): ?>
                                        <a href="../../<?php echo $egitmen['egitmen_resimyol']; ?>" data-fancybox="gallery" class="btn btn-primary">
                                            Resmi görüntülemek için tıklayınız
                                        </a>
                                    <?php else: ?>
                                        <p class="form-control-static">Veri bulunamadı</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr>

                            <div class="x_title">
                                <h2><strong>Satın Alım </strong>Detayları</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmenin Kurs Sayısı:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo $kursSayisi; ?> Kurs</p>

                                    <?php 

                                            if ($kurslar) {
                                                foreach ($kurslar as $kurs) {
                                                    echo "<li>" . htmlspecialchars($kurs['baslik']) . "</li>";
                                                }
                                            } else {
                                                echo "Bu eğitmene ait kurs bulunmamaktadır.";
                                            }

                                     ?>

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Puanı:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="rating">
                                        <span class="rating-score"><?php echo number_format($egitmen_puani, 1, ',', ''); ?></span>
                                        <?php
                                        // Eğitmenin puanı
                                        $filledStars = floor($egitmen_puani); // Tam dolu yıldızlar
                                        $halfStar = ($egitmen_puani - $filledStars) >= 0.5; // Yarım yıldız kontrolü
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
                            </div>


                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Eğitmen Toplam Öğrenci Sayısı:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo $toplam_ogrenci_sayisi; ?> Öğrenci</p>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>
