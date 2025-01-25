<?php
error_reporting(0); // Hata gösterimini kapatmak için kullanılır
include 'header.php'; 

// Kurs detay bilgileri veritabanından alınmalıdır. Örneğin:
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

$puansor = $db->prepare("SELECT AVG(puan) as ortalama_puan FROM kurs_puan WHERE kurs_id = :kurs_id");
$puansor->execute([
    'kurs_id' => $kurs['kurs_id']
]);
$puancek = $puansor->fetch(PDO::FETCH_ASSOC);

$ortalama_puan = $puancek['ortalama_puan'] ? round($puancek['ortalama_puan'], 2) : 0; // 0 ise 0 yazdır


function kontrol($veri) {
    return $veri ? htmlspecialchars($veri) : "Veri bulunamadı";
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
                        <h2><strong><?php echo kontrol($kurs['baslik']); ?></strong> Detayları</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="form-horizontal form-label-left">

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kategori:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($kategoricek['ad']); ?> </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Alt Kategori:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($altkategoricek['ad']); ?> </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Açıklama:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($kurs['aciklama']); ?> </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Süresi:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($kurs['sure']); ?> </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Fiyatı:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($kurs['fiyat']); ?> TL</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Seviye:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($seviyecek['seviye_ad']); ?> </p>
                                </div>
                            </div>

                            <!-- Fancybox kütüphaneleri -->
                            <link href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.css" rel="stylesheet">
                            <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.umd.js"></script>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Resmi:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <?php if (!empty($kurs['resim_yol'])): ?>
                                        <a href="../../<?php echo $kurs['resim_yol']; ?>" data-fancybox="gallery" class="btn btn-primary">
                                            Resmi görüntülemek için tıklayınız
                                        </a>
                                    <?php else: ?>
                                        <p class="form-control-static">Veri bulunamadı</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Ön İzleme Videosu:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <?php if (!empty($kurs['video_yol'])): ?>
                                        <a href="../../<?php echo $kurs['video_yol']; ?>" data-fancybox data-type="video" class="btn btn-info">
                                            Videoyu izlemek için tıklayınız
                                        </a>
                                    <?php else: ?>
                                        <p class="form-control-static">Veri bulunamadı</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                           <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Yüklenme Tarihi:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static">
                                        <?php
                                        $olusturmaTarihi = $kurs['olusturma_tarihi'];
                                        setlocale(LC_TIME, 'tr_TR.UTF-8');
                                        $formattedDate = strftime('%d %B %Y %H:%M:%S', strtotime($olusturmaTarihi)); // 28 Aralık 2024 21:32:37
                                        echo $formattedDate;
                                        ?>
                                    </p>
                                </div>
                            </div>



                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Son Güncellenme Tarihi:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static">
                                        <?php
                                        $guncellemeTarihi = $kurs['guncelleme_tarihi'];
                                        setlocale(LC_TIME, 'tr_TR.UTF-8');
                                        $formattedDate = strftime('%d %B %Y %H:%M:%S', strtotime($guncellemeTarihi)); // 28 Aralık 2024 21:32:37
                                        echo $formattedDate;
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <div class="x_title">
                                <h2><strong>Satın Alım </strong>Detayları</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Puanı:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
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
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Öğrenci Sayısı:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="form-control-static"> <?php echo kontrol($ogrsayicek['ogr_sayisi']); ?> Öğrenci</p>
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
