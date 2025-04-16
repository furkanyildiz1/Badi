<?php
header('Content-Type: text/html; charset=utf-8');
include 'nedmin/netting/baglan.php';
include 'header.php';

// Giriş kontrolü
if (!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// kurs_id kontrolü
if (!isset($_GET['kurs_id']) || empty($_GET['kurs_id'])) {
    header("Location: my-courses.php");
    exit();
}

$kurs_id = intval($_GET['kurs_id']);
$user_id = $_SESSION['userkullanici_id'];

// Kurs satın alma ve ödeme onayı kontrolü
$enrollmentCheck = $db->prepare("
    SELECT sk.*, f.odeme_durumu 
    FROM satilan_kurslar sk
    JOIN faturalar f ON sk.fatura_id = f.fatura_id
    WHERE f.user_id = ? AND sk.kurs_id = ? AND f.odeme_durumu = 'onaylandi'
");
$enrollmentCheck->execute([$user_id, $kurs_id]);

if ($enrollmentCheck->rowCount() == 0) {
    header("Location: my-courses.php?error=not_enrolled");
    exit();
}

// Kurs bilgilerini çek
$courseQuery = $db->prepare("
    SELECT k.*, ks.seviye_ad
    FROM kurslar k
    LEFT JOIN kurs_seviye ks ON k.kurs_seviye_id = ks.kurs_seviye_id
    WHERE k.kurs_id = ?
");
$courseQuery->execute([$kurs_id]);
$course = $courseQuery->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header("Location: my-courses.php?error=course_not_found");
    exit();
}

// Kurs modüllerini çek
$modulQuery = $db->prepare("
    SELECT * 
    FROM kurs_modulleri 
    WHERE kurs_id = ? 
    ORDER BY modul_sira ASC
");
$modulQuery->execute([$kurs_id]);
$modules = $modulQuery->fetchAll(PDO::FETCH_ASSOC);

// Aktif içerik kontrolü
$active_bolum_id = isset($_GET['bolum_id']) ? intval($_GET['bolum_id']) : 0;
$activeContent = null;
if ($active_bolum_id > 0) {
    $contentQuery = $db->prepare("
        SELECT kb.*, km.modul_ad 
        FROM kurs_bolumleri kb
        JOIN kurs_modulleri km ON kb.modul_id = km.modul_id
        WHERE kb.bolum_id = ? AND km.kurs_id = ?
    ");
    $contentQuery->execute([$active_bolum_id, $kurs_id]);
    $activeContent = $contentQuery->fetch(PDO::FETCH_ASSOC);
    if (!$activeContent) {
        $active_bolum_id = 0;
    }
}
// Eğer aktif içerik seçilmemişse, ilk dersi çek
if ($active_bolum_id == 0 && count($modules) > 0) {
    $firstContentQuery = $db->prepare("
        SELECT kb.* 
        FROM kurs_bolumleri kb
        JOIN kurs_modulleri km ON kb.modul_id = km.modul_id
        WHERE km.kurs_id = ?
        ORDER BY km.modul_sira ASC, kb.bolum_sira ASC
        LIMIT 1
    ");
    $firstContentQuery->execute([$kurs_id]);
    $firstContent = $firstContentQuery->fetch(PDO::FETCH_ASSOC);
    if ($firstContent) {
        $active_bolum_id = $firstContent['bolum_id'];
        $activeContent = $firstContent;
    }
}

// İzlenen içerik kaydı (basit ilerleme takibi)
if ($active_bolum_id > 0) {
    $checkViewed = $db->prepare("
        SELECT * FROM kurs_izleme_kayitlari 
        WHERE user_id = ? AND bolum_id = ?
    ");
    $checkViewed->execute([$user_id, $active_bolum_id]);
    if ($checkViewed->rowCount() == 0) {
        $insertView = $db->prepare("
            INSERT INTO kurs_izleme_kayitlari 
            (user_id, kurs_id, bolum_id, izlenme_tarihi) 
            VALUES (?, ?, ?, NOW())
        ");
        $insertView->execute([$user_id, $kurs_id, $active_bolum_id]);
    }
}

// Kurs genel ilerleme hesaplama
$progressQuery = $db->prepare("
    SELECT 
        (SELECT COUNT(*) FROM kurs_bolumleri kb 
         JOIN kurs_modulleri km ON kb.modul_id = km.modul_id 
         WHERE km.kurs_id = ?) as total_lessons,
        (SELECT COUNT(*) FROM kurs_izleme_kayitlari 
         WHERE user_id = ? AND kurs_id = ?) as viewed_lessons
");
$progressQuery->execute([$kurs_id, $user_id, $kurs_id]);
$progress = $progressQuery->fetch(PDO::FETCH_ASSOC);
$progressPercentage = 0;
if ($progress['total_lessons'] > 0) {
    $progressPercentage = round(($progress['viewed_lessons'] / $progress['total_lessons']) * 100);
}
?>

<div class="course-player">
    <div class="container-fluid">
        <div class="row">
            <!-- Yan Menü: Kurs İçerikleri -->
            <div class="col-md-3 course-sidebar">
                <div class="course-sidebar-header">
                    <h4><?php echo $course['baslik']; ?></h4>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>İlerleme</span>
                            <span><?php echo $progressPercentage; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $progressPercentage; ?>%;"></div>
                        </div>
                    </div>
                </div>
                <div class="course-modules">
                    <?php foreach ($modules as $modul): ?>
                        <?php
                        // Her modüle ait dersleri çek
                        $bolumQuery = $db->prepare("
                            SELECT kb.*, 
                                   (SELECT COUNT(*) FROM kurs_izleme_kayitlari 
                                    WHERE user_id = ? AND bolum_id = kb.bolum_id) as viewed
                            FROM kurs_bolumleri kb 
                            WHERE kb.modul_id = ? 
                            ORDER BY kb.bolum_sira ASC
                        ");
                        $bolumQuery->execute([$user_id, $modul['modul_id']]);
                        $bolumler = $bolumQuery->fetchAll(PDO::FETCH_ASSOC);
                        // Tamamlanan ders sayısını hesapla
                        $completed = 0;
                        foreach ($bolumler as $bolum) {
                            if ($bolum['viewed'] > 0) $completed++;
                        }
                        $moduleProgress = count($bolumler) > 0 ? round(($completed / count($bolumler)) * 100) : 0;
                        ?>
                        <div class="module">
                            <!-- Özel "data-target" özniteliğini kullanıyoruz; Bootstrap’ın native toggle özelliğini devre dışı bırakıyoruz -->
                            <div 
                                class="module-header <?php echo ($activeContent && $activeContent['modul_id'] == $modul['modul_id']) ? '' : 'collapsed'; ?>" 
                                role="button" tabindex="0"
                                data-target="#module-<?php echo $modul['modul_id']; ?>" 
                                aria-expanded="<?php echo ($activeContent && $activeContent['modul_id'] == $modul['modul_id']) ? 'true' : 'false'; ?>" 
                                aria-controls="module-<?php echo $modul['modul_id']; ?>"
                            >
                                <div class="module-title">
                                    <span><?php echo $modul['modul_ad']; ?></span>
                                    <div class="module-info">
                                        <span><?php echo count($bolumler); ?> ders</span>
                                        <span><?php echo $completed; ?> tamamlandı</span>
                                    </div>
                                </div>
                                <div class="module-progress">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $moduleProgress; ?>%;"></div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div 
                                id="module-<?php echo $modul['modul_id']; ?>" 
                                class="collapse <?php echo ($activeContent && $activeContent['modul_id'] == $modul['modul_id']) ? 'show' : ''; ?>"
                                aria-labelledby="module-<?php echo $modul['modul_id']; ?>"
                            >
                                <div class="module-content">
                                    <?php foreach ($bolumler as $bolum): ?>
                                        <a href="kurs-izle.php?kurs_id=<?php echo $kurs_id; ?>&bolum_id=<?php echo $bolum['bolum_id']; ?>" 
                                           class="lesson-item <?php echo ($active_bolum_id == $bolum['bolum_id']) ? 'active' : ''; ?>">
                                            <div class="lesson-icon">
                                                <?php if ($bolum['viewed'] > 0): ?>
                                                    <i class="fas fa-check-circle"></i>
                                                <?php else:
                                                    $icon = 'fa-play-circle';
                                                    switch ($bolum['icerik_tipi']) {
                                                        case 'pdf': $icon = 'fa-file-pdf'; break;
                                                        case 'scorm':
                                                        case 'h5p': $icon = 'fa-cube'; break;
                                                        case 'presentation': $icon = 'fa-file-powerpoint'; break;
                                                        case 'url': $icon = 'fa-link'; break;
                                                        case 'embed': $icon = 'fa-code'; break;
                                                    }
                                                ?>
                                                    <i class="fas <?php echo $icon; ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="lesson-details">
                                                <div class="lesson-title"><?php echo $bolum['bolum_ad']; ?></div>
                                                <?php if ($bolum['bolum_sure_saat'] > 0 || $bolum['bolum_sure_dakika'] > 0): ?>
                                                <div class="lesson-duration">
                                                    <?php 
                                                    if ($bolum['bolum_sure_saat'] > 0) echo $bolum['bolum_sure_saat'] . 'sa ';
                                                    if ($bolum['bolum_sure_dakika'] > 0) echo $bolum['bolum_sure_dakika'] . 'dk';
                                                    ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- İçerik Görüntüleme Alanı -->
            <div class="col-md-9 content-viewer">
                <div class="content-header">
                    <div class="content-navigation">
                        <a href="my-courses.php" class="back-button">
                            <i class="fas fa-arrow-left"></i> Kurslarıma Dön
                        </a>
                        <?php
                        // Önceki ve sonraki içerik bağlantıları
                        $navQuery = $db->prepare("
                            SELECT kb.bolum_id, kb.bolum_ad, kb.modul_id, 
                                   (SELECT GROUP_CONCAT(km.modul_sira, '.', kb2.bolum_sira) 
                                    FROM kurs_bolumleri kb2 
                                    JOIN kurs_modulleri km ON kb2.modul_id = km.modul_id
                                    WHERE kb2.bolum_id = kb.bolum_id) as sort_order
                            FROM kurs_bolumleri kb
                            JOIN kurs_modulleri km ON kb.modul_id = km.modul_id
                            WHERE km.kurs_id = ?
                            ORDER BY km.modul_sira ASC, kb.bolum_sira ASC
                        ");
                        $navQuery->execute([$kurs_id]);
                        $allContent = $navQuery->fetchAll(PDO::FETCH_ASSOC);
                        
                        $currentIndex = 0;
                        foreach ($allContent as $index => $content) {
                            if ($content['bolum_id'] == $active_bolum_id) {
                                $currentIndex = $index;
                                break;
                            }
                        }
                        $prevContent = ($currentIndex > 0) ? $allContent[$currentIndex - 1] : null;
                        $nextContent = ($currentIndex < count($allContent) - 1) ? $allContent[$currentIndex + 1] : null;
                        ?>
                        <div class="content-nav-buttons">
                            <?php if ($prevContent): ?>
                            <a href="kurs-izle.php?kurs_id=<?php echo $kurs_id; ?>&bolum_id=<?php echo $prevContent['bolum_id']; ?>" class="nav-button prev">
                                <i class="fas fa-chevron-left"></i> Önceki
                            </a>
                            <?php endif; ?>
                            <?php if ($nextContent): ?>
                            <a href="kurs-izle.php?kurs_id=<?php echo $kurs_id; ?>&bolum_id=<?php echo $nextContent['bolum_id']; ?>" class="nav-button next">
                                Sonraki <i class="fas fa-chevron-right"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($activeContent): ?>
                    <h2 class="content-title"><?php echo $activeContent['bolum_ad']; ?></h2>
                    <?php endif; ?>
                </div>
                <?php if ($activeContent): ?>
                <div class="content-container">
                    <?php
                    // İçerik tipine göre gösterim
                    switch ($activeContent['icerik_tipi']) {
                        case 'youtube':
                            $video_id = trim($activeContent['video_url']);
                            echo '<div class="video-container youtube-container">';
                            echo '<iframe 
                                    id="youtubeEmbed"
                                    width="100%" 
                                    height="500" 
                                    src="https://www.youtube.com/embed/' . $video_id . '" 
                                    frameborder="0" 
                                    allowfullscreen>
                                  </iframe>';
                            echo '<div class="video-fallback">';
                            echo '<p>Video görüntülenemiyor. Aşağıdaki seçenekleri deneyebilirsiniz:</p>';
                            echo '<a href="https://www.youtube.com/watch?v=' . $video_id . '" target="_blank" class="btn btn-primary">YouTube\'da İzle</a>';
                            echo '</div></div>';
                            break;
                        case 'vimeo':
                            echo '<div class="video-container vimeo-container">';
                            echo '<iframe src="https://player.vimeo.com/video/' . $activeContent['video_url'] . '" width="100%" height="600" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                            echo '</div>';
                            break;
                        case 'bunnystream':
                            echo '<div class="video-container bunny-container">';
                            echo $activeContent['embed_kodu'];
                            echo '<div class="video-fallback">If the video doesn\'t load, please contact support.</div>';
                            echo '</div>';
                            break;
                        case 'url':
                            echo '<div class="url-container">';
                            echo '<p>Bu içerik harici bir kaynağa bağlantıdır. Aşağıdaki bağlantıya tıklayarak içeriğe ulaşabilirsiniz:</p>';
                            echo '<a href="' . $activeContent['video_url'] . '" target="_blank" class="btn btn-primary">İçeriği Görüntüle</a>';
                            echo '</div>';
                            break;
                        case 'scorm':
                            // SCORM içeriği entegrasyonu
                            break;
                        case 'h5p':
                            $h5p_path = '';
                            if (preg_match('/src="([^"]+)"/', $activeContent['embed_kodu'], $matches)) {
                                $h5p_path = $matches[1];
                            }
                            echo '<div class="h5p-container">';
                            if (!empty($h5p_path)) {
                                echo '<iframe src="h5p-renderer.php?h5p=' . urlencode($h5p_path) . '" width="100%" height="500" frameborder="0" allowfullscreen></iframe>';
                            } else {
                                echo '<div class="error-message">H5P file path could not be extracted.</div>';
                            }
                            echo '</div>';
                            break;
                        case 'pdf':
                            echo '<div class="pdf-container">';
                            echo str_replace('/uploads', 'uploads', $activeContent['embed_kodu']);
                            echo '</div>';
                            break;
                        case 'presentation':
                            echo '<div class="presentation-container">';
                            echo str_replace('/uploads', 'uploads', $activeContent['embed_kodu']);
                            echo '</div>';
                            break;
                        case 'embed':
                            echo '<div class="embed-container">';
                            echo str_replace('/uploads', 'uploads', $activeContent['embed_kodu']);
                            echo '</div>';
                            break;
                        default:
                            echo '<div class="error-container"><p>Bu içerik türü görüntülenemiyor veya desteklenmiyor.</p></div>';
                    }
                    ?>
                </div>
                <?php else: ?>
                <div class="no-content">
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        <h3>Henüz içerik seçilmedi</h3>
                        <p>Lütfen sol menüden bir ders seçin.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Stil Ayarları -->
<style>
/* Genel stiller */
.course-player {
    min-height: 100vh;
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
    margin-top: 75px;
}

.course-sidebar {
    height: 100vh;
    background: #fff;
    border-right: 1px solid #e5e5e5;
    overflow-y: auto;
    position: sticky;
    top: 0;
    padding: 0;
}

.course-sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #e5e5e5;
    background: #f8f9fa;
}

.course-sidebar-header h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
}

.progress-container {
    margin-top: 15px;
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
    background-color: #00b074;
}

/* Modül stilleri */
.course-modules {
    padding: 10px 0;
}

.module {
    margin-bottom: 5px;
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: all 0.3s;
}

.module-header:hover {
    background-color: #f8f9fa;
    border-left-color: #ddd;
}

.module-title span {
    font-weight: 600;
    font-size: 14px;
    color: #333;
}

.module-info {
    display: flex;
    font-size: 12px;
    color: #6c757d;
    margin-top: 3px;
}

.module-info span {
    margin-right: 10px;
}

.module-progress {
    width: 50px;
    margin: 0 10px;
}

.module-header i {
    color: #aaa;
    transition: transform 0.3s;
}

.module-header.collapsed i {
    transform: rotate(0deg);
}

.module-header:not(.collapsed) i {
    transform: rotate(180deg);
}

/* Ders stilleri */
.module-content {
    padding: 5px 0;
}

.lesson-item {
    display: flex;
    align-items: center;
    padding: 10px 20px 10px 30px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.lesson-item:hover {
    background-color: #f5f5f5;
    text-decoration: none;
    color: #333;
}

.lesson-item.active {
    background-color: #e8f4f0;
    border-left-color: #00b074;
}

.lesson-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.lesson-icon i {
    font-size: 16px;
    color: #aaa;
}

.lesson-item.active .lesson-icon i,
.lesson-item:hover .lesson-icon i {
    color: #00b074;
}

.lesson-icon i.fa-check-circle {
    color: #00b074;
}

.lesson-details {
    flex-grow: 1;
}

.lesson-title {
    font-size: 14px;
    margin-bottom: 3px;
}

.lesson-duration {
    font-size: 12px;
    color: #6c757d;
}

/* İçerik görüntüleme stilleri */
.content-viewer {
    padding: 0;
    background: #fff;
    min-height: 100vh;
}

.content-header {
    padding: 20px;
    border-bottom: 1px solid #e5e5e5;
}

.content-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.back-button {
    color: #333;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
}

.back-button:hover {
    color: #00b074;
}

.content-nav-buttons {
    display: flex;
    gap: 10px;
}

.nav-button {
    padding: 6px 12px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s;
}

.nav-button:hover {
    background: #e9ecef;
    text-decoration: none;
    color: #333;
}

.content-title {
    font-size: 22px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.content-container {
    padding: 20px;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.url-container,
.pdf-container,
.presentation-container,
.embed-container {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 5px;
    text-align: center;
}

.no-content {
    display: flex;
    align-items: center;
    justify-content: center;
    height: calc(100vh - 80px);
}

.empty-state {
    text-align: center;
    padding: 40px;
}

.empty-state i {
    font-size: 40px;
    color: #e9ecef;
    margin-bottom: 15px;
}

.empty-state h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 14px;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .course-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        z-index: 1000;
        transition: left 0.3s;
    }
    
    .course-sidebar.show {
        left: 0;
    }
    
    .content-viewer {
        width: 100%;
    }
    
    .sidebar-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #00b074;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1001;
    }
}

</style>

<!-- Bootstrap 5 JS (Popper Dahil) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome (ikonlar) -->
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Özel toggle işlemi: data-target özniteliğini kullanıyoruz
    document.querySelectorAll('.module-header').forEach(function(header) {
        header.addEventListener('click', function(e) {
            e.preventDefault();
            var targetSelector = header.getAttribute('data-target');
            var collapseElem = document.querySelector(targetSelector);
            // Eğer daha önce collapse instance'ı oluşturulmamışsa
            var collapseInstance = bootstrap.Collapse.getInstance(collapseElem);
            if (!collapseInstance) {
                collapseInstance = new bootstrap.Collapse(collapseElem, {toggle: false});
            }
            collapseInstance.toggle();
        });
        // Klavyeden Enter tuşu da çalışsın
        header.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                header.click();
            }
        });
    });

    // collapse olaylarını dinleyip header'ın durumunu güncelleyelim
    document.querySelectorAll('.collapse').forEach(function(collapseElem) {
        collapseElem.addEventListener('shown.bs.collapse', function () {
            var header = document.querySelector('[data-target="#' + collapseElem.id + '"]');
            if (header) {
                header.classList.remove('collapsed');
                header.setAttribute('aria-expanded', 'true');
            }
        });
        collapseElem.addEventListener('hidden.bs.collapse', function () {
            var header = document.querySelector('[data-target="#' + collapseElem.id + '"]');
            if (header) {
                header.classList.add('collapsed');
                header.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // Mobil sidebar toggle
    if (window.innerWidth <= 768) {
        const contentViewer = document.querySelector('.content-viewer');
        const sidebarToggle = document.createElement('button');
        sidebarToggle.className = 'sidebar-toggle';
        sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
        contentViewer.appendChild(sidebarToggle);
        const sidebar = document.querySelector('.course-sidebar');
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
    
    // İçerik izlenme kaydı (örneğin 10 saniye sonra)
    function markAsViewed() {
        const activeContentId = <?php echo $active_bolum_id > 0 ? $active_bolum_id : 0; ?>;
        if (activeContentId > 0) {
            fetch('mark-viewed.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `bolum_id=${activeContentId}`
            });
        }
    }
    setTimeout(markAsViewed, 10000);
});
</script>

<?php include 'footer.php'; ?>
