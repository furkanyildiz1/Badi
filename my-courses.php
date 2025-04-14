<?php 
header('Content-Type: text/html; charset=utf-8');
session_start();

include 'nedmin/netting/baglan.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get user's courses with credential information
$kurslar = $db->prepare("
    SELECT 
        k.*,
        f.created_at as erisim_tarihi,
        f.fatura_no,
        f.odeme_durumu,
        sk.satis_id
    FROM satilan_kurslar sk
    JOIN kurslar k ON sk.kurs_id = k.kurs_id
    JOIN faturalar f ON sk.fatura_id = f.fatura_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$kurslar->execute([$_SESSION['userkullanici_id']]);
?>

<style>
.my-courses {
    padding-top: 30px;
    padding-bottom: 50px;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

.page-subtitle {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 30px;
}

/* Search and filter section */
.filter-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.search-box {
    position: relative;
    width: 300px;
}

.search-box input {
    width: 100%;
    padding: 10px 15px;
    padding-right: 40px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 12px;
    color: #aaa;
}

/* Course list styling */
.course-list {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.course-item {
    display: flex;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
}

.course-item:last-child {
    border-bottom: none;
}

.course-item:hover {
    background-color: #f9f9f9;
}

.course-image {
    width: 80px;
    height: 60px;
    border-radius: 4px;
    object-fit: cover;
    margin-right: 15px;
    flex-shrink: 0;
}

.course-info {
    flex-grow: 1;
}

.course-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    display: block;
}

.course-meta {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #6c757d;
}

.course-meta span {
    display: flex;
    align-items: center;
    margin-right: 15px;
}

.course-meta i {
    margin-right: 5px;
    font-size: 14px;
}

.course-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-shrink: 0;
}

/* Progress bar styling */
.progress-container {
    width: 200px;
    margin-left: 20px;
    flex-shrink: 0;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
}

.progress {
    height: 6px;
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 10px;
}

/* Course type styling */
.course-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 15px;
    flex-shrink: 0;
}

.badge-online {
    background-color: #e3fcef;
    color: #00b074;
}

.badge-canli {
    background-color: #e5f1ff;
    color: #0066ff;
}

.badge-yuzyuze {
    background-color: #fff4e5;
    color: #ff9500;
}

/* Button styling */
.btn-custom {
    padding: 6px 15px;
    font-size: 13px;
    border-radius: 4px;
    font-weight: 500;
}

.btn-view {
    background-color: #00b074;
    color: white;
}

.btn-view:hover {
    background-color: #00956a;
    color: white;
}

.payment-pending {
    background-color: #ffe5e5;
    color: #ff3636;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

/* Empty state */
.no-courses {
    text-align: center;
    padding: 50px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.no-courses i {
    font-size: 40px;
    color: #e9ecef;
    margin-bottom: 15px;
}

.no-courses h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.no-courses p {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 20px;
}
</style>

<div class="my-courses">
    <div class="container">
        <h1 class="page-title">Kurslarım</h1>
        <p class="page-subtitle">Satın aldığınız ve erişim sağlayabileceğiniz kursları içerir.</p>
        
        <div class="filter-section">
            <div class="search-box">
                <input type="text" placeholder="Kurs ara..." id="courseSearch">
                <i class="fas fa-search"></i>
            </div>
        </div>
        
        <?php if($kurslar->rowCount() > 0): ?>
            <div class="course-list">
                <?php while($kurs = $kurslar->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    // Mock progress data (random for now)
                    $progress = mt_rand(0, 100);
                    
                    // Set appropriate badge and style based on course type
                    $kurs_tur = $kurs['kurs_tur'];
                    $badge_class = '';
                    $badge_text = '';
                    
                    switch($kurs_tur) {
                        case 'online':
                            $badge_class = 'badge-online';
                            $badge_text = 'Online';
                            break;
                        case 'canli':
                            $badge_class = 'badge-canli';
                            $badge_text = 'Canlı';
                            break;
                        case 'yuzyuze':
                            $badge_class = 'badge-yuzyuze';
                            $badge_text = 'Yüz Yüze';
                            break;
                        default:
                            $badge_class = 'badge-secondary';
                            $badge_text = 'Kurs';
                    }
                    ?>
                    
                    <div class="course-item">
                        <img src="<?php echo $kurs['resim_yol']; ?>" alt="<?php echo $kurs['baslik']; ?>" class="course-image">
                        
                        <span class="course-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                        
                        <div class="course-info">
                            <a href="kurs-detay.php?kurs_id=<?php echo $kurs['kurs_id']; ?>" class="course-title"><?php echo $kurs['baslik']; ?></a>
                            <div class="course-meta">
                                <span><i class="far fa-clock"></i> <?php echo $kurs['sure']; ?> Saat</span>
                                <span><i class="far fa-user"></i> <?php echo $kurs['ogrenci_sayi']; ?> Öğrenci</span>
                                <span><i class="far fa-calendar-alt"></i> <?php echo date('d.m.Y', strtotime($kurs['erisim_tarihi'])); ?></span>
                            </div>
                        </div>
                        
                        <?php if($kurs_tur == 'online'): ?>
                        <div class="progress-container">
                            <div class="progress-info">
                                <span>İlerleme</span>
                                <span><?php echo $progress; ?>%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $progress; ?>%; background-color: #00b074;"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="course-actions">
                            <?php if($kurs['odeme_durumu'] == "onaylandi"): ?>
                                <a href="kurs-detay.php?kurs_id=<?php echo $kurs['kurs_id']; ?>" class="btn btn-custom btn-view">
                                    <i class="fas fa-play-circle"></i> Kursa Git
                                </a>
                            <?php else: ?>
                                <span class="payment-pending">
                                    <i class="fas fa-exclamation-circle"></i> Ödeme Bekleniyor
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-courses">
                <i class="fas fa-book-open"></i>
                <h3>Henüz Kursunuz Bulunmuyor</h3>
                <p>Hemen yeni bir kurs satın alarak öğrenmeye başlayın!</p>
                <a href="courses.php" class="btn btn-primary">Kursları İncele</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Course search functionality
    const searchInput = document.getElementById('courseSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const courseItems = document.querySelectorAll('.course-item');
            
            courseItems.forEach(function(item) {
                const title = item.querySelector('.course-title').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
