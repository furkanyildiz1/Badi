<?php 
include 'header.php';

try {

    $adminkullanicisor=$db->prepare("SELECT * FROM kullanici WHERE kullanici_mail=:kullanici_mail");
    $adminkullanicisor->execute([
      'kullanici_mail' => $_SESSION['kullanici_mail']
    ]);
    $adminkullanicicek=$adminkullanicisor->fetch(PDO::FETCH_ASSOC);
    // İstatistikleri çekelim
    $istatistikkullaniciSor = $db->prepare("SELECT COUNT(*) as toplam FROM kullanici");
    $istatistikkullaniciSor->execute();
    $istatistikkullaniciSayi = $istatistikkullaniciSor->fetch(PDO::FETCH_ASSOC)['toplam'];

    $istatistikegitmenSor = $db->prepare("SELECT COUNT(*) as toplam FROM egitmen");
    $istatistikegitmenSor->execute();
    $istatistikegitmenSayi = $istatistikegitmenSor->fetch(PDO::FETCH_ASSOC)['toplam'];

    $istatistikkursSor = $db->prepare("SELECT COUNT(*) as toplam FROM kurslar");
    $istatistikkursSor->execute();
    $istatistikkursSayi = $istatistikkursSor->fetch(PDO::FETCH_ASSOC)['toplam'];

    // Son 7 günlük ziyaretçi istatistiği
    $istatistikziyaretSor = $db->prepare("SELECT COUNT(*) as toplam FROM site_ziyaret WHERE ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $istatistikziyaretSor->execute();
    $istatistikziyaretSayi = $istatistikziyaretSor->fetch(PDO::FETCH_ASSOC)['toplam'];

    // New statistics
    // 1. Bekleyen Siparişler
    $bekleyenSiparisler = $db->prepare("SELECT COUNT(*) as toplam FROM faturalar WHERE odeme_durumu = 'beklemede'");
    $bekleyenSiparisler->execute();
    $bekleyenSiparisSayi = $bekleyenSiparisler->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 1.5 İptal Edilen Siparişler
    $iptalSiparisler = $db->prepare("SELECT COUNT(*) as toplam FROM faturalar WHERE odeme_durumu = 'iptal_edildi'");
    $iptalSiparisler->execute();
    $iptalSiparisSayi = $iptalSiparisler->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 2. Son 1 Ay İçerisinde Gelen Siparişler
    $sonAySiparisler = $db->prepare("SELECT COUNT(*) as toplam FROM faturalar WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH);");
    $sonAySiparisler->execute();
    $sonAySiparisSayi = $sonAySiparisler->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 3. Toplam Siparişler
    $toplamSiparisler = $db->prepare("SELECT COUNT(*) as toplam FROM faturalar");
    $toplamSiparisler->execute();
    $toplamSiparisSayi = $toplamSiparisler->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 4. Son 1 Ay İçerisinde Satılan Kurs Sayısı
    $sonAyKursSatis = $db->prepare("
        SELECT COUNT(*) as toplam 
        FROM faturalar s
        JOIN satilan_kurslar sk ON sk.fatura_id = s.fatura_id
        WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND s.odeme_durumu = 'onaylandi';
    ");
    $sonAyKursSatis->execute();
    $sonAyKursSatisSayi = $sonAyKursSatis->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 5. Toplam Satılan Kurs Sayısı
    $toplamKursSatis = $db->prepare("
        SELECT COUNT(*) as toplam 
        FROM faturalar s
        JOIN satilan_kurslar sk ON sk.fatura_id = s.fatura_id
        WHERE s.odeme_durumu = 'onaylandi';
    ");
    $toplamKursSatis->execute();
    $toplamKursSatisSayi = $toplamKursSatis->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 6. Son 1 Ay İçerisinde Elde Edilen Gelir
    $sonAyGelir = $db->prepare("
        SELECT COALESCE(SUM(toplam_tutar), 0) as toplam 
        FROM faturalar 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
        AND odeme_durumu = 'onaylandi';
    ");
    $sonAyGelir->execute();
    $sonAyGelirToplam = $sonAyGelir->fetch(PDO::FETCH_ASSOC)['toplam'];

    // 7. Toplam Elde Edilen Gelir
    $toplamGelir = $db->prepare("
        SELECT COALESCE(SUM(toplam_tutar), 0) as toplam 
        FROM faturalar 
        WHERE odeme_durumu = 'onaylandi';
    ");
    $toplamGelir->execute();
    $toplamGelirToplam = $toplamGelir->fetch(PDO::FETCH_ASSOC)['toplam'];
} catch(PDOException $e) {
    // Hata durumunda varsayılan değerler
    $istatistikkullaniciSayi = 0;
    $istatistikegitmenSayi = 0;
    $istatistikkursSayi = 0;
    $istatistikziyaretSayi = 0;
    
    // Hata logla
    error_log("Dashboard istatistik hatası: " . $e->getMessage());

    // Error handling
    $bekleyenSiparisSayi = 0;
    $iptalSiparisSayi = 0;
    $sonAySiparisSayi = 0;
    $toplamSiparisSayi = 0;
    $sonAyKursSatisSayi = 0;
    $toplamKursSatisSayi = 0;
    $sonAyGelirToplam = 0;
    $toplamGelirToplam = 0;
    error_log("Dashboard istatistik hatası: " . $e->getMessage());
}
?>

<div class="right_col" role="main">
    <!-- Hoşgeldiniz Bölümü -->
    <div class="welcome-section">
        <h1>Hoş Geldiniz, <strong><?php echo $adminkullanicicek['kullanici_ad']; ?></strong></h1>
        <p class="welcome-text">Kontrol paneline hoş geldiniz. Site istatistiklerini buradan takip edebilirsiniz.</p>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row">
        <!-- Toplam Kullanıcı Kartı -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd;">
                    <i class="fa fa-users" style="color: #2196f3;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $istatistikkullaniciSayi; ?></h3>
                    <p>Toplam Kullanıcı</p>
                </div>
            </div>
        </div>

        <!-- Toplam Eğitmen Kartı -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9;">
                    <i class="fa fa-graduation-cap" style="color: #4caf50;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $istatistikegitmenSayi; ?></h3>
                    <p>Toplam Eğitmen</p>
                </div>
            </div>
        </div>

        <!-- Toplam Kurs Kartı -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #f3e5f5;">
                    <i class="fa fa-book" style="color: #9c27b0;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $istatistikkursSayi; ?></h3>
                    <p>Toplam Kurs</p>
                </div>
            </div>
        </div>

        <!-- Haftalık Ziyaret Kartı -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0;">
                    <i class="fa fa-eye" style="color: #ff9800;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $istatistikziyaretSayi; ?></h3>
                    <p>Haftalık Ziyaret</p>
                </div>
            </div>
        </div>

        <!-- Bekleyen Siparişler -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0;">
                    <i class="fa fa-clock" style="color: #ff9800;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $bekleyenSiparisSayi; ?></h3>
                    <p>Bekleyen Siparişler</p>
                </div>
            </div>
        </div>

        <!-- İptal Edilen Siparişler -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ffebee;">
                    <i class="fa fa-times-circle" style="color: #f44336;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $iptalSiparisSayi; ?></h3>
                    <p>İptal Edilen Siparişler</p>
                </div>
            </div>
        </div>

        <!-- Son 1 Ay Siparişler -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8eaf6;">
                    <i class="fa fa-shopping-cart" style="color: #3f51b5;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $sonAySiparisSayi; ?></h3>
                    <p>Son 1 Ay Siparişler</p>
                </div>
            </div>
        </div>

        <!-- Toplam Siparişler -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fce4ec;">
                    <i class="fa fa-shopping-bag" style="color: #e91e63;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $toplamSiparisSayi; ?></h3>
                    <p>Toplam Siparişler</p>
                </div>
            </div>
        </div>

        <!-- Son 1 Ay Satılan Kurslar -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e0f2f1;">
                    <i class="fa fa-book" style="color: #009688;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $sonAyKursSatisSayi; ?></h3>
                    <p>Son 1 Ay Satılan Kurslar</p>
                </div>
            </div>
        </div>

        <!-- Toplam Satılan Kurslar -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #f3e5f5;">
                    <i class="fa fa-graduation-cap" style="color: #9c27b0;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $toplamKursSatisSayi; ?></h3>
                    <p>Toplam Satılan Kurslar</p>
                </div>
            </div>
        </div>

        <!-- Son 1 Ay Gelir -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9;">
                    <i class="fa fa-money-bill" style="color: #4caf50;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($sonAyGelirToplam, 2, ',', '.'); ?> ₺</h3>
                    <p>Son 1 Ay Gelir</p>
                </div>
            </div>
        </div>

        <!-- Toplam Gelir -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #f1f8e9;">
                    <i class="fa fa-wallet" style="color: #8bc34a;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($toplamGelirToplam, 2, ',', '.'); ?> ₺</h3>
                    <p>Toplam Gelir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Aktiviteler -->
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Son Aktiviteler</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fa fa-user-plus text-success"></i>
                            <span>Yeni bir kullanıcı kayıt oldu</span>
                            <small>2 dakika önce</small>
                        </div>
                        <div class="activity-item">
                            <i class="fa fa-graduation-cap text-info"></i>
                            <span>Yeni bir eğitmen başvurusu yapıldı</span>
                            <small>15 dakika önce</small>
                        </div>
                        <div class="activity-item">
                            <i class="fa fa-book text-warning"></i>
                            <span>Yeni bir kurs eklendi</span>
                            <small>1 saat önce</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-section {
    margin-bottom: 30px;
}

.welcome-section h1 {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

.welcome-text {
    color: #666;
    margin-bottom: 20px;
}

.stat-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.stat-icon i {
    font-size: 20px;
}

.stat-info h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
}

.stat-info p {
    margin: 5px 0 0;
    color: #666;
    font-size: 14px;
}

.activity-list {
    padding: 10px 0;
}

.activity-item {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item i {
    width: 30px;
    text-align: center;
    margin-right: 10px;
}

.activity-item span {
    flex: 1;
    color: #333;
}

.activity-item small {
    color: #999;
    font-size: 12px;
}
</style>

<?php include 'footer.php'; ?>