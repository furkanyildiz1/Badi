<?php
include 'nedmin/netting/baglan.php';

$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = 8;
$offset = ($sayfa - 1) * $limit;
$kategori_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : 0;
$siralama = isset($_GET['siralama']) ? $_GET['siralama'] : '';

// Build the query with filtering and sorting
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

while($kurscek=$kurssor->fetch(PDO::FETCH_ASSOC)) {
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
                            <span class="rating-score"><?php echo number_format($kurscek['puan'], 1, ',', ''); ?></span>
                            <?php
                            $filledStars = floor($kurscek['puan']);
                            $halfStar = ($kurscek['puan'] - $filledStars) >= 0.5;
                            $emptyStars = 5 - $filledStars - ($halfStar ? 1 : 0);

                            for ($i = 0; $i < $filledStars; $i++) {
                                echo '<i class="fas fa-star"></i>';
                            }
                            if ($halfStar) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            }
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
                            <?php echo $kurscek['ogrenci_sayi'] ?> Öğrenci
                        </span></li>
                    </ul>
                </div>
            </div>
        </a>
    </div>

<?php } ?> 