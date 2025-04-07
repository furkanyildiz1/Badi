<?php 

// Add at the top of the file, before any output
header('Content-Type: text/html; charset=utf-8');

// For date formatting with Turkish months
setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'tr', 'turkish');

// Example date formatting code - adapt this to your specific date display code
function formatTurkishDate($dateString) {
    // Convert the date to Turkish format
    $date = new DateTime($dateString);
    
    // Custom Turkish month names to ensure proper encoding
    $turkishMonths = [
        'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
        'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'
    ];
    
    // Format with our custom month names
    $day = $date->format('j');
    $month = $turkishMonths[$date->format('n') - 1];
    $year = $date->format('Y');
    
    return "$day $month $year";
}

// Example usage (adjust to your code):
// echo formatTurkishDate('2025-02-28'); // Will output "28 Şubat 2025"

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


$egitmensor=$db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
$egitmensor->execute([
    'egitmen_id' => $kurs['egitmen_id']
]);
$egitmencek=$egitmensor->fetch(PDO::FETCH_ASSOC);
$egitmen_id=$egitmencek['egitmen_id'];


$ortalama_puan = $kurs['puan'] ? round($kurs['puan'], 2) : 0; // 0 ise 0 yazdır


function kontrol($veri) {
    return $veri ? htmlspecialchars($veri) : "Veri bulunamadı";
}


$kategorisor = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id=:kategori_id");
$kategorisor->execute([
    'kategori_id' => $kurs['kategori_id']
]);
$kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC);


$kursSayisiSor = $db->prepare("SELECT COUNT(*) as kurs_sayisi FROM kurslar WHERE egitmen_id = :egitmen_id");
$kursSayisiSor->execute(['egitmen_id' => $egitmen_id]);
$kursSayisiCek = $kursSayisiSor->fetch(PDO::FETCH_ASSOC);

$kursSayisi = $kursSayisiCek['kurs_sayisi'] ?? 0; // Eğer sonuç yoksa varsayılan 0 olarak ayarlanır

$kurslar = $db->prepare("SELECT kurs_id FROM kurslar WHERE egitmen_id=:egitmen_id");
$kurslar->execute(['egitmen_id' => $egitmen_id]);
$kurslar_listesi = $kurslar->fetchAll(PDO::FETCH_ASSOC);


$toplam_ogrenci_sayisi = 0;


$toplam_puan = 0;
$kurs_sayisi = count($kurslar_listesi);

    $toplam_puan += $ortalama_puan;

$kurscek = $db->prepare("SELECT * FROM kurslar WHERE kurs_id=:kurs_id");
$kurscek->execute(['kurs_id' => $kurs_id]);
$kurs = $kurscek->fetch(PDO::FETCH_ASSOC);

$egitmen_puani = $kurs_sayisi > 0 ? round($toplam_puan / $kurs_sayisi, 2) : 0;

$sonKursSor = $db->prepare("SELECT * FROM kurslar ORDER BY olusturma_tarihi DESC LIMIT 3");
$sonKursSor->execute();

$modulsor = $db->prepare("SELECT * FROM kurs_modulleri WHERE kurs_id = ? ORDER BY modul_sira");
$modulsor->execute([$kurs_id]);

// Check if user already owns or has pending order for this course
$kurs_durumu = null;
if(isset($_SESSION['userkullanici_id'])) {
    // Check completed orders
    $kurs_kontrol = $db->prepare("
        SELECT f.odeme_durumu 
        FROM satilan_kurslar sk
        JOIN faturalar f ON sk.fatura_id = f.fatura_id
        WHERE sk.kurs_id = ? AND f.user_id = ?
        ORDER BY f.created_at DESC
        LIMIT 1
    ");
    $kurs_kontrol->execute([$kurs['kurs_id'], $_SESSION['userkullanici_id']]);
    $sonuc = $kurs_kontrol->fetch(PDO::FETCH_ASSOC);

    if($sonuc) {
        if($sonuc['odeme_durumu'] == 'onaylandi') {
            $kurs_durumu = 'sahip';
        } elseif($sonuc['odeme_durumu'] == 'beklemede') {
            $kurs_durumu = 'beklemede';
        }
    }

    $is_cart = $db->prepare("SELECT * FROM sepet WHERE course_id = ? AND user_id = ?");
    $is_cart->execute([$kurs_id, $_SESSION['userkullanici_id']]);
    $is_cart_result = $is_cart->fetch(PDO::FETCH_ASSOC);

    if($is_cart_result) {
        $kurs_durumu = 'sepette';
    }
}

?>

<!-- Toast container - fixed position at top-right -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
  <?php if(isset($_GET['islem']) || isset($_GET['durum'])) { ?>
    <?php if(isset($_GET['islem']) && $_GET['islem'] == 'eklendi') { ?>
      <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" id="cartToast">
        <div class="d-flex">
          <div class="toast-body">
            <strong>Başarılı!</strong> Kurs sepetinize eklendi.
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    <?php } else if(isset($_GET['islem']) && $_GET['islem'] == 'silindi') { ?>
      <div class="toast align-items-center text-white bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" id="cartToast">
        <div class="d-flex">
          <div class="toast-body">
            <strong>Bilgi!</strong> Kurs sepetinizden çıkarıldı.
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    <?php } else if(isset($_GET['durum']) && $_GET['durum'] == 'sertifikasec') { ?>
      <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" id="cartToast">
        <div class="d-flex">
          <div class="toast-body">
            <strong>Hata!</strong> Lütfen en az bir sertifika seçeneği seçiniz.
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
</div>

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

.rating-label {
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
    color: #333;
}

.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 8px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    cursor: pointer;
    font-size: 24px;
    transition: all 0.2s ease;
}

.star-rating label .bx {
    color: #ddd;
}

.star-rating input:checked ~ label .bx,
.star-rating label:hover .bx,
.star-rating label:hover ~ label .bx {
    color: #f39c12;
    transform: scale(1.2);
}

.star-rating:hover label .bx {
    color: #ddd;
}

.star-rating:hover label:hover .bx,
.star-rating:hover label:hover ~ label .bx {
    color: #f39c12;
}

.course-lessons-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.lesson-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
}

.lesson-item:hover {
    background-color: #f8f9fa;
}

.lesson-title {
    display: flex;
    align-items: center;
    color: #444;
}

.lesson-duration {
    color: #666;
    font-size: 0.9em;
}

.course-curriculum-info {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.info-item {
    font-size: 1.1em;
    color: #444;
    padding: 10px;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #0056b3;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}

.certificate-options {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.cert-options-list, .additional-services-list {
    margin-top: 15px;
}

.form-check {
    background: white;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    margin-bottom: 10px !important;
}

.form-check-label {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-left: 10px;
}

.price {
    font-weight: bold;
    color: #0056b3;
}

/* Add styles for toast notifications */
.toast {
    opacity: 1 !important; /* Ensure toast is visible */
    min-width: 300px;
}

.toast-body {
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
}

/* Animation for toast */
@keyframes slideInRight {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

.toast {
    animation: slideInRight 0.3s ease-in-out;
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
                                    <p><span>Eğitmen :</span> <?php echo $egitmencek['egitmen_adsoyad']; ?></p>
                                    <p class="course-date"><span>Son Güncelleme:</span>
                                    <?php echo formatTurkishDate($kurs['guncelleme_tarihi']); ?>
                                    </p>
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

                                            <div class="description-panel fade show active" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab">
                                                <div class="course-description">
                                                    <?php 
                                                    // Ensure proper HTML rendering
                                                    echo stripslashes(html_entity_decode($kurs['aciklama'])); 
                                                    ?>
                                                </div>
                                            </div>

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

                                        <div class="accordion" id="courseAccordion">
                                            <?php 
                                            $toplam_ders = 0;
                                            $toplam_sure = 0;
                                            
                                            while($modulcek = $modulsor->fetch(PDO::FETCH_ASSOC)) {
                                                // Get sections for this module
                                                $bolumsor = $db->prepare("SELECT * FROM kurs_bolumleri WHERE modul_id = ? ORDER BY bolum_sira");
                                                $bolumsor->execute([$modulcek['modul_id']]);
                                                $bolumler = $bolumsor->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                // Count sections and total duration for this module
                                                $ders_sayisi = count($bolumler);
                                                $toplam_ders += $ders_sayisi;
                                            ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading<?php echo $modulcek['modul_id']; ?>">
                                                        <button class="accordion-button collapsed" type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#collapse<?php echo $modulcek['modul_id']; ?>" 
                                                                aria-expanded="false" 
                                                                aria-controls="collapse<?php echo $modulcek['modul_id']; ?>">
                                                            <div class="d-flex justify-content-between w-100">
                                                                <span><i class="fas fa-book-open me-2"></i> <?php echo htmlspecialchars($modulcek['modul_ad']); ?></span>
                                                                <span class="ms-auto"><?php echo $ders_sayisi; ?> Ders</span>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse<?php echo $modulcek['modul_id']; ?>" 
                                                         class="accordion-collapse collapse" 
                                                         aria-labelledby="heading<?php echo $modulcek['modul_id']; ?>" 
                                                         data-bs-parent="#courseAccordion">
                                                        <div class="accordion-body">
                                                            <ul class="course-lessons-list">
                                                                <?php foreach($bolumler as $bolum) { 
                                                                    // Convert duration to minutes for total calculation if needed
                                                                    if($bolum['bolum_sure_saat'] > 0 || $bolum['bolum_sure_dakika'] > 0) {
                                                                        $dakika = ($bolum['bolum_sure_saat'] * 60) + $bolum['bolum_sure_dakika'];
                                                                        $toplam_sure += $dakika;
                                                                    }
                                                                ?>
                                                                    <li class="lesson-item">
                                                                        <div class="lesson-title">
                                                                            <i class="fas fa-play-circle me-2"></i>
                                                                            <?php echo htmlspecialchars($bolum['bolum_ad']); ?>
                                                                        </div>
                                                                        <?php if($bolum['bolum_sure_saat'] > 0 || $bolum['bolum_sure_dakika'] > 0) { ?>
                                                                            <div class="lesson-duration">
                                                                                <i class="fas fa-clock me-1"></i>
                                                                                <?php 
                                                                                if($bolum['bolum_sure_saat'] > 0) {
                                                                                    echo $bolum['bolum_sure_saat'] . ' saat ';
                                                                                }
                                                                                if($bolum['bolum_sure_dakika'] > 0) {
                                                                                    echo $bolum['bolum_sure_dakika'] . ' dakika';
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="course-curriculum-info mt-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <i class="fas fa-book-reader me-2"></i>
                                                        Toplam <?php echo $toplam_ders; ?> Ders
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <i class="fas fa-clock me-2"></i>
                                                        Toplam Süre: <?php 
                                                            $saat = floor($toplam_sure / 60);
                                                            $dakika = $toplam_sure % 60;
                                                            echo $saat . ' saat ' . ($dakika > 0 ? $dakika . ' dakika' : '');
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
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
                                                            <li><img src="assets/img/icon/file2.svg" alt="icon"> <span><?php echo $kurs_sayisi ?> Kurs</span> </li>
                                                            <li><img src="assets/img/icon/user2.svg" alt="icon"> <span><?php echo $egitmencek['ogrenci_sayi'] ?> Öğrenci</span> </li>
                                                            <li><img src="assets/img/icon/like.svg" alt="icon"> <span><?php echo number_format($egitmencek['puan'], 2); ?> Ortalama Puan</span> </li>
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
                                    
                                    <div class="reviews-panel fade" id="nav-reviews" role="tabpanel" aria-labelledby="nav-reviews-tab">
                                        <div class="lesseon-review-section">
                                            <?php if(isset($_SESSION['userkullanici_mail'])) {
                                                $is_bought_stmt = $db->prepare("
                                                    SELECT 1 
                                                    FROM satilan_kurslar sk
                                                    INNER JOIN faturalar f ON sk.fatura_id = f.fatura_id
                                                    WHERE f.user_id = :user_id AND sk.kurs_id = :kurs_id
                                                    LIMIT 1
                                                ");
                                                
                                                $is_bought_stmt->execute([
                                                    'user_id' => $_SESSION['userkullanici_id'],
                                                    'kurs_id' => $_GET['kurs_id']
                                                ]);
                                                $yourm_stmt = $db->prepare("SELECT * FROM kurs_yorumlar WHERE user_id = :user_id AND kurs_id = :kurs_id");
                                                $yourm_stmt->execute([
                                                    'user_id' => $_SESSION['userkullanici_id'],
                                                    'kurs_id' => $_GET['kurs_id']
                                                ]);
                                                $yourm = $yourm_stmt->fetch(PDO::FETCH_ASSOC);
                                                $is_bought = $is_bought_stmt->fetch(PDO::FETCH_ASSOC);
                                                if($is_bought && !$yourm){
                                                ?>
                                                <!-- Comment Form -->
                                                <div class="comment-form mb-4">
                                                    <h4>Yorum Yap</h4>
                                                    <form action="nedmin/netting/islem.php" method="POST">
                                                        <input type="hidden" name="kurs_id" value="<?php echo $kurs_id; ?>">
                                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['userkullanici_id']; ?>">
                                                        
                                                        <div class="rating-select mb-3">
                                                            <label class="rating-label">Puanınız:</label>
                                                            <div class="star-rating">
                                                                <?php for($i = 5; $i >= 1; $i--) { ?>
                                                                    <input type="radio" name="puan" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                                                    <label for="star<?php echo $i; ?>">
                                                                        <i class="bx bxs-star"></i>
                                                                    </label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group mb-3">
                                                            <textarea class="form-control" name="yorum_metni" rows="4" placeholder="Yorumunuzu yazın..." required></textarea>
                                                        </div>
                                                        
                                                        <button type="submit" name="yorum_ekle" class="btn btn-primary">Yorum Gönder</button>
                                                    </form>
                                                </div>
                                                <?php } elseif(!$is_bought){?>
                                                    <div class="alert alert-info">
                                                        Yorum yapabilmek için kursa sahip olmanız gerekmektedir.
                                                    </div>
                                            <?php }} else { ?>
                                                <div class="alert alert-info">
                                                    Yorum yapabilmek için <a href="login.php">giriş yapın</a> veya <a href="register.php">üye olun</a>.
                                                </div>
                                            <?php } ?>

                                            <!-- Comments List -->
                                            <div class="student-review-section">
                                                <h4>Yorumlar</h4>
                                                <?php
                                                $yorumsor = $db->prepare("
                                                    SELECT ky.*, k.kullanici_ad, k.kullanici_soyad, k.kullanici_resim 
                                                    FROM kurs_yorumlar ky 
                                                    JOIN kullanici k ON ky.user_id = k.kullanici_id 
                                                    WHERE ky.kurs_id = :kurs_id AND ky.durum = '1' 
                                                    ORDER BY ky.yorum_tarihi DESC
                                                ");
                                                $yorumsor->execute(['kurs_id' => $_GET['kurs_id']]);

                                                if($yorumsor->rowCount() > 0) {
                                                    while($yorumcek = $yorumsor->fetch(PDO::FETCH_ASSOC)) { ?>
                                                        <div class="student-review-items">
                                                            <img src="<?php echo $yorumcek['kullanici_resim'] ? $yorumcek['kullanici_resim'] : 'assets/img/all-img/cmnt-1.png'; ?>" alt="image">
                                                            <div class="rating">
                                                                <span class="rating-score"><?php echo number_format($yorumcek['puan'], 1, ',', ''); ?></span>
                                                                <?php
                                                                $filledStars = floor($yorumcek['puan']); // Tam dolu yıldızlar
                                                                $halfStar = ($yorumcek['puan'] - $filledStars) >= 0.5; // Yarım yıldız kontrolü
                                                                $emptyStars = 5 - $filledStars - ($halfStar ? 1 : 0); // Boş yıldızlar

                                                                // Dolu yıldızlar
                                                                for ($i = 0; $i < $filledStars; $i++) {
                                                                    echo '<i class="bx bxs-star"></i>';
                                                                }

                                                                // Yarım yıldız
                                                                if ($halfStar) {
                                                                    echo '<i class="bx bxs-star-half"></i>';
                                                                }

                                                                // Boş yıldızlar
                                                                for ($i = 0; $i < $emptyStars; $i++) {
                                                                    echo '<i class="bx bx-star"></i>';
                                                                }
                                                                ?>
                                                            </div>
                                                            <p><?php echo htmlspecialchars($yorumcek['yorum_metni']); ?></p>
                                                            <h3><?php echo htmlspecialchars($yorumcek['kullanici_ad'] . ' ' . $yorumcek['kullanici_soyad']); ?></h3>
                                                            <span><?php echo date('d.m.Y', strtotime($yorumcek['yorum_tarihi'])); ?></span>
                                                        </div>
                                                    <?php }
                                                } else { ?>
                                                    <div class="alert alert-info">
                                                        Henüz hiç yorum yapılmamış.
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    
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
                                    <!-- Certificate and extras section with conditional display -->
                                    <?php 
                                    // Check if user should see certificate options
                                    $show_certificate_options = true;

                                    // If course is already in cart, hide certificate options
                                    if ($kurs_durumu === 'sepette') {
                                        $show_certificate_options = false;
                                    }

                                    // If user has an active order for this course, hide certificate options
                                    if ($kurs_durumu === 'beklemede') {
                                        $show_certificate_options = false;
                                    }

                                    // If user already owns this course, hide certificate options
                                    if ($kurs_durumu === 'sahip') {
                                        $show_certificate_options = false;
                                    }

                                    if ($show_certificate_options) {
                                    ?>
                                        <div class="certificate-options-container">
                                            <div class="alert alert-info">
                                                <strong>Önemli Bilgi:</strong> Kurs kaydınızı tamamlamak için en az bir sertifika seçeneği seçmeniz gerekmektedir. Transkript hizmetleri isteğe bağlıdır.
                                            </div>
                                            
                                            <h4 class="widget-title">Sertifika Seçenekleri</h4>
                                            <p class="text-danger mb-3">* En az bir sertifika seçmelisiniz</p>
                                            
                                            <div class="certificate-options">
                                                <?php if ($kurs['edevlet_cert_price'] > 0) { ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input cert-option" type="checkbox" id="edevlet_cert" 
                                                           data-type="edevlet_cert" data-price="<?php echo $kurs['edevlet_cert_price']; ?>">
                                                    <label class="form-check-label" for="edevlet_cert">
                                                        E-Devlet Sertifikası 
                                                        <span class="badge bg-primary" style="background-color: #007bff; color: white; padding: 3px 8px; border-radius: 4px; margin-left: 5px;">
                                                            <?php echo $kurs['edevlet_cert_price']; ?> TL
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                                
                                                <?php if ($kurs['tr_cert_price'] > 0) { ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input cert-option" type="checkbox" id="tr_cert" 
                                                           data-type="tr_cert" data-price="<?php echo $kurs['tr_cert_price']; ?>">
                                                    <label class="form-check-label" for="tr_cert">
                                                        Türkçe Sertifika 
                                                        <span class="badge bg-primary" style="background-color: #007bff; color: white; padding: 3px 8px; border-radius: 4px; margin-left: 5px;">
                                                            <?php echo $kurs['tr_cert_price']; ?> TL
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                                
                                                <?php if ($kurs['eng_cert_price'] > 0) { ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input cert-option" type="checkbox" id="eng_cert" 
                                                           data-type="eng_cert" data-price="<?php echo $kurs['eng_cert_price']; ?>">
                                                    <label class="form-check-label" for="eng_cert">
                                                        İngilizce Sertifika 
                                                        <span class="badge bg-primary" style="background-color: #007bff; color: white; padding: 3px 8px; border-radius: 4px; margin-left: 5px;">
                                                            <?php echo $kurs['eng_cert_price']; ?> TL
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            
                                            <h4 class="widget-title mt-4">Ek Hizmetler (İsteğe Bağlı)</h4>
                                            <div class="extra-services">
                                                <?php if ($kurs['tr_transcript_price'] > 0) { ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input cert-option" type="checkbox" id="tr_transcript" 
                                                           data-type="tr_transcript" data-price="<?php echo $kurs['tr_transcript_price']; ?>">
                                                    <label class="form-check-label" for="tr_transcript">
                                                        Türkçe Transkript 
                                                        <span class="badge bg-info" style="background-color: #17a2b8; color: white; padding: 3px 8px; border-radius: 4px; margin-left: 5px;">
                                                            <?php echo $kurs['tr_transcript_price']; ?> TL
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                                
                                                <?php if ($kurs['eng_transcript_price'] > 0) { ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input cert-option" type="checkbox" id="eng_transcript" 
                                                           data-type="eng_transcript" data-price="<?php echo $kurs['eng_transcript_price']; ?>">
                                                    <label class="form-check-label" for="eng_transcript">
                                                        İngilizce Transkript 
                                                        <span class="badge bg-info" style="background-color: #17a2b8; color: white; padding: 3px 8px; border-radius: 4px; margin-left: 5px;">
                                                            <?php echo $kurs['eng_transcript_price']; ?> TL
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            
                                            <div class="price-summary mt-3">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Toplam:</strong>
                                                    <span id="total-price">0.00 TL</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Add these hidden fields to both forms -->
                                            <form method="POST" action="nedmin/netting/islem.php" class="add-to-cart-form">
                                                <input type="hidden" name="course_id" value="<?php echo $kurs_id; ?>">
                                                <input type="hidden" name="selected_certs" class="selected-certs-input" value="">
                                                <input type="hidden" name="cert_total_price" class="cert-total-price-input" value="0">
                                                <button type="submit" name="addToCart" class="btn btn-primary" id="add-to-cart-btn" disabled style="width: 100%;">
                                                    Sepete Ekle
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="nedmin/netting/islem.php" class="buy-now-form mt-2">
                                                <input type="hidden" name="course_id" value="<?php echo $kurs_id; ?>">
                                                <input type="hidden" name="selected_certs" class="selected-certs-input" value="">
                                                <input type="hidden" name="cert_total_price" class="cert-total-price-input" value="0">
                                                <button type="submit" name="buyNow" class="btn btn-success" id="buy-now-btn" disabled style="width: 100%;">
                                                    Hemen Satın Al
                                                </button>
                                            </form>
                                        </div>
                                    <?php } else { 
                                        // User already has the course in cart, pending, or owned
                                        if ($kurs_durumu === 'sepette') { ?>
                                            <div class="alert alert-info">
                                                Bu kurs sepetinizde bulunmaktadır.
                                                <a href="cart.php" class="btn btn-primary" style="width: 100%;">
                                                    Sepete Git
                                                </a>
                                                <form method="POST" action="nedmin/netting/islem.php" class="mt-2">
                                                    <input type="hidden" name="kurs_id" value="<?php echo $kurs_id; ?>">
                                                    <button type="submit" name="removeFromCart" class="btn btn-danger" style="width: 100%;">
                                                        Sepetten Çıkar
                                                    </button>
                                                </form>
                                            </div>
                                        <?php } elseif ($kurs_durumu === 'beklemede') { ?>
                                            <div class="alert alert-warning">
                                                Bu kurs için bekleyen bir siparişiniz bulunmaktadır.
                                                <a href="siparislerim.php" class="btn btn-warning" style="width: 100%;">
                                                    Siparişlerimi Görüntüle
                                                </a>
                                            </div>
                                        <?php } elseif ($kurs_durumu === 'sahip') { ?>
                                            <div class="alert alert-success">
                                                Bu kursa zaten sahipsiniz.
                                                <a href="kurslarim.php" class="btn btn-success" style="width: 100%;">
                                                    Kurslarıma Git
                                                </a>
                                            </div>
                                        <?php }
                                    } ?>
                                    <ul class="courses-details">
                                        <li><div class="icon"><img src="assets/img/icon/user.svg" alt="icon"> Eğitmen</div> <p><?php echo $egitmencek['egitmen_adsoyad']; ?></p></li>
                                        <li><div class="icon"><img src="assets/img/icon/star.svg" alt="icon"> Puan</div> <p><?php echo number_format($kurs['puan'], 2); ?> Puan</p></li>
                                        <li><div class="icon"><img src="assets/img/icon/clock.svg" alt="icon"> Kurs Süresi</div> <p><?php echo $kurs['sure']; ?> Saat</p></li>
                                        <li><div class="icon"><img src="assets/img/icon/user2.svg" alt="icon"> Öğrenci Sayısı</div> <p><?php echo $kurs['ogrenci_sayi']; ?>
                                            Öğrenci</p></li>
                                        <li><div class="icon"><img src="assets/img/icon/target.svg" alt="icon"> Kurs Seviyesi</div> <p><?php echo $seviyecek['seviye_ad']; ?></p></li>
                                        <li><div class="icon"><img src="assets/img/icon/web.svg" alt="icon"> Dil</div> <p>Türkçe</p></li>
                                    </ul>
                                    <ul class="course-shared">
                                        <li class="title">Paylaş:</li>
                                        <?php
                                        // Get current URL and encode it
                                        $currentURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                        $encodedURL = urlencode($currentURL);
                                        $courseTitle = urlencode($kurs['baslik']);
                                        
                                        // Create WhatsApp share text
                                        $whatsappText = urlencode($kurs['baslik'] . ' ' . $currentURL);
                                        ?>
                                        
                                        <li>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encodedURL; ?>" 
                                            target="_blank" 
                                            class="facebook-share"
                                            onclick="window.open(this.href, 'facebook-share', 'width=580,height=296'); return false;">
                                                <img src="assets/img/icon/fb.svg" alt="Facebook">
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="https://twitter.com/intent/tweet?text=<?php echo $courseTitle; ?>&url=<?php echo $encodedURL; ?>" 
                                            target="_blank"
                                            class="twitter-share"
                                            onclick="window.open(this.href, 'twitter-share', 'width=550,height=420'); return false;">
                                                <img src="assets/img/icon/tw.svg" alt="Twitter">
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="https://www.pinterest.com/pin/create/button/?url=<?php echo $encodedURL; ?>&media=&description=<?php echo $courseTitle; ?>" 
                                            target="_blank"
                                            class="pinterest-share"
                                            onclick="window.open(this.href, 'pinterest-share', 'width=750,height=500'); return false;">
                                                <img src="assets/img/icon/pn.svg" alt="Pinterest">
                                            </a>
                                        </li>

                                        <li>
                                            <?php
                                            $isMobile = preg_match('/(android|iphone|ipad|mobile)/i', $_SERVER['HTTP_USER_AGENT']);
                                            $whatsappURL = $isMobile ? 'whatsapp://send?text=' : 'https://web.whatsapp.com/send?text=';
                                            ?>
                                            <a href="<?php echo $whatsappURL . $whatsappText; ?>" 
                                               target="_blank"
                                               class="whatsapp-share"
                                               data-action="share/whatsapp/share">
                                                <img src="assets/img/icon/wa.svg" alt="WhatsApp" style="width: 40px; height: 40px; display: block;">
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="widget widget-resent-course">
                                <h3 class="widget-title">Son Eklenen Kurslar</h3>
                                <?php while($sonKursCek=$sonKursSor->fetch(PDO::FETCH_ASSOC)) {
                                    $ortalamaa_puan = $sonKursCek['puan'] ? round($sonKursCek['puan'], 2) : 0; // 0 ise 0 yazdır
                                 ?>
                                <article class="item">
                                    <a href="kurs-detay.php?kurs_id=<?php echo $sonKursCek['kurs_id']; ?>" class="thumb"><img src="<?php echo $sonKursCek['resim_yol']; ?>" alt="iamge"></a>
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
                                            <a href="kurs-detay.php?kurs_id=<?php echo $sonKursCek['kurs_id']; ?>">
                                                <?php 
                                                $maxLength = 20; // Maksimum karakter sayısı
                                                $baslik = $sonKursCek['baslik']; // Başlık
                                                echo mb_strlen($baslik) > $maxLength 
                                                    ? mb_substr($baslik, 0, $maxLength) . '...' 
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

        <!-- Login Required Modal -->
        <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="loginRequiredModalLabel">Giriş Gerekli</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p id="loginRequiredMessage"></p>
              </div>
              <div class="modal-footer">
                <a href="login.php" class="btn btn-primary">Giriş Yap</a>
                <a href="register.php" class="btn btn-success">Üye Ol</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Add jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Our custom script -->
        <script>
        // Check if jQuery is loaded
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded!');
            document.body.innerHTML += '<div class="alert alert-danger">Error: jQuery is not loaded. Please check your page dependencies.</div>';
        } else {
            console.log('jQuery version ' + jQuery.fn.jquery + ' is loaded');
        }

        $(document).ready(function() {
            console.log('Document ready handler executing');
            
            // Handle certificate selection
            $('.cert-option').change(function() {
                console.log('Certificate option changed');
                let selectedCerts = [];
                let totalCertPrice = 0;
                let hasMandatoryCert = false;
                
                $('.cert-option:checked').each(function() {
                    selectedCerts.push($(this).data('type'));
                    totalCertPrice += parseFloat($(this).data('price'));
                    
                    // Check if this is a mandatory certificate (not a transcript)
                    const certType = $(this).data('type');
                    if (certType === 'edevlet_cert' || certType === 'tr_cert' || certType === 'eng_cert') {
                        hasMandatoryCert = true;
                    }
                });

                // Update total price display
                $('#total-price').text(totalCertPrice.toFixed(2) + ' TL');

                // Update both "Buy Now" and "Add to Cart" forms
                $('.buy-now-form, .add-to-cart-form').each(function() {
                    $(this).find('.selected-certs-input').val(selectedCerts.join(','));
                    $(this).find('.cert-total-price-input').val(totalCertPrice);
                });
                
                // Enable/disable Add to Cart and Buy Now buttons based on selection
                $('#add-to-cart-btn, #buy-now-btn').prop('disabled', !hasMandatoryCert);
            });

            // Debug form submission
            $('form').submit(function(e) {
                console.log('Form submitting with data:', {
                    selected_certs: $(this).find('.selected-certs-input').val(),
                    cert_total_price: $(this).find('.cert-total-price-input').val()
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('cartToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000 // Hide after 5 seconds
                });
                toast.show();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Add login check for cart and buy buttons
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const buyNowBtn = document.getElementById('buy-now-btn');
            const addToCartForm = document.querySelector('.add-to-cart-form');
            const buyNowForm = document.querySelector('.buy-now-form');
            
            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    <?php if(!isset($_SESSION['userkullanici_id'])) { ?>
                    e.preventDefault();
                    showLoginRequiredModal('sepete eklemek için');
                    <?php } ?>
                });
            }
            
            if (buyNowForm) {
                buyNowForm.addEventListener('submit', function(e) {
                    <?php if(!isset($_SESSION['userkullanici_id'])) { ?>
                    e.preventDefault();
                    showLoginRequiredModal('satın almak için');
                    <?php } ?>
                });
            }
            
            // Function to show login required modal
            function showLoginRequiredModal(action) {
                const message = document.getElementById('loginRequiredMessage');
                message.textContent = `Bu kursu ${action} giriş yapmanız gerekmektedir.`;
                
                const modal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
                modal.show();
            }
        });
        </script>

        <?php include 'footer.php'; ?>
    </body>
</html>