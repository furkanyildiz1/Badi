<?php
include 'nedmin/netting/baglan.php';

// Pagination and filtering variables
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = 8;
$offset = ($sayfa - 1) * $limit;

// Filtering and sorting variables
$kategori_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : 0;
$siralama = isset($_GET['siralama']) ? $_GET['siralama'] : '';
$requestTotalCount = isset($_GET['totalCount']) && $_GET['totalCount'] === 'true';

// Get total course count for the filter
$totalCountQuery = "SELECT COUNT(*) as toplam FROM kurslar";
if ($kategori_id > 0) {
    $totalCountQuery .= " WHERE kategori_id = :kategori_id";
}

$totalCountStmt = $db->prepare($totalCountQuery);
if ($kategori_id > 0) {
    $totalCountStmt->bindParam(':kategori_id', $kategori_id, PDO::PARAM_INT);
}
$totalCountStmt->execute();
$toplamKurs = $totalCountStmt->fetch(PDO::FETCH_ASSOC)['toplam'];

// Build the query with filters and sorting
$sql = "SELECT * FROM kurslar";
if ($kategori_id > 0) {
    $sql .= " WHERE kategori_id = :kategori_id";
}

// Add sorting
switch ($siralama) {
    case 'puan':
        $sql .= " ORDER BY puan DESC";
        break;
    case 'ogrenci':
        $sql .= " ORDER BY ogrenci_sayi DESC";
        break;
    case 'sure':
        $sql .= " ORDER BY sure DESC";
        break;
}

$sql .= " LIMIT :limit OFFSET :offset";

$kurssor = $db->prepare($sql);
if ($kategori_id > 0) {
    $kurssor->bindParam(':kategori_id', $kategori_id, PDO::PARAM_INT);
}
$kurssor->bindParam(':limit', $limit, PDO::PARAM_INT);
$kurssor->bindParam(':offset', $offset, PDO::PARAM_INT);
$kurssor->execute();

// Calculate how many courses are being displayed in total
$displayCount = min(($sayfa * $limit), $toplamKurs);
$hasMore = $toplamKurs > $displayCount;

// Start output buffering to capture HTML
ob_start();

// Generate course HTML
while($kurscek=$kurssor->fetch(PDO::FETCH_ASSOC)) {
    $ortalama_puan = $kurscek['puan'] ? round($kurscek['puan'], 2) : 0;

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

    $egitmensor=$db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
    $egitmensor->execute([
        'egitmen_id' => $kurscek['egitmen_id']
    ]);
    $egitmencek=$egitmensor->fetch(PDO::FETCH_ASSOC);
?>
<div class="col-lg-4 col-md-6 col-sm-6">
    <div class="single-courses-box">
        <div class="image">
            <a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>" class="d-block">
                <img src="<?php echo $kurscek['resim_yol'] ?>" alt="image">
            </a>
            <div class="cr-tag">
                <a href="#"><span><?php echo $altkategoricek['ad']; ?></span></a>
            </div>
        </div>
        <div class="content">
            <span class="cr-price" ><?php echo $kurscek['fiyat']; ?> TL</span>
            <h3><a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>"><?php echo $kurscek['baslik']; ?></a></h3>
            <ul class="cr-items" >
                <li><i class='bx bx-user'></i> <span>
                        <?php echo isset($kurscek['ogrenci_sayi']) && $kurscek['ogrenci_sayi'] ? $kurscek['ogrenci_sayi'] : 0; ?> Öğrenci
                    </span> </li>
                <li><i class='bx bx-time-five'></i> <span><?php echo $kurscek['sure'] ?> Saat</span></li>
                <li><i class='bx bx-star' ></i> <span><?php echo $ortalama_puan ?> puan</span></li>
            </ul>
        </div>
    </div>
</div>
<?php
}

// Get the HTML output
$html = ob_get_clean();

// Return JSON with metadata if requested, otherwise just the HTML
if ($requestTotalCount) {
    echo json_encode([
        'html' => $html,
        'totalCount' => $toplamKurs,
        'displayCount' => $displayCount,
        'hasMore' => $hasMore
    ]);
} else {
    echo $html;
}
?> 