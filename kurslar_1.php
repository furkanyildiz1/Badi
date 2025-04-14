<?php

    include 'nedmin/netting/baglan.php';
    include 'header.php';

    $kategorisorr=$db->prepare("SELECT * FROM kategoriler");
    $kategorisorr->execute();

    $toplamKursSorgu = $db->query("SELECT COUNT(*) as toplam FROM kurslar");
    $toplamKurs = $toplamKursSorgu->fetch(PDO::FETCH_ASSOC)['toplam'];
    if($toplamKurs > 8){
        $gosterilenKursSayisi = 8;
    }else{
        $gosterilenKursSayisi = $toplamKurs;
    }

    // Add these variables near the top after other PHP queries
    $sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
    $limit = 8;
    $offset = ($sayfa - 1) * $limit;

    // Add these variables for filtering and sorting
    $kategori_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : 0;
    $siralama = isset($_GET['siralama']) ? $_GET['siralama'] : '';

    // Modify the kurssor query to include filtering and sorting
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
                            <p id="courseCountDisplay"><?php echo "{$toplamKurs} kurstan {$gosterilenKursSayisi} tanesi gösteriliyor"; ?></p>
                        </div>
                        <div class="col-lg-6 col-md-5 ordering">
                            <div class="filter-wrapper d-flex flex-wrap justify-content-end">
                                <div class="filter-item mb-2 mb-md-0 me-md-2">
                                    <label for="siralamaFilter" class="filter-label d-none d-md-block mb-1">Sıralama</label>
                                    <div class="select-container">
                                        <select id="siralamaFilter" class="form-select custom-select" onchange="filterAndSort()">
                                            <option value="">Sırala</option>
                                            <option value="puan" <?php echo ($siralama == 'puan') ? 'selected' : ''; ?>><i class="fas fa-star me-1"></i> Puana Göre</option>
                                            <option value="ogrenci" <?php echo ($siralama == 'ogrenci') ? 'selected' : ''; ?>><i class="fas fa-users me-1"></i> Öğrenci Sayısına Göre</option>
                                            <option value="sure" <?php echo ($siralama == 'sure') ? 'selected' : ''; ?>><i class="fas fa-clock me-1"></i> Kurs Uzunluğuna Göre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-item">
                                    <label for="kategoriFilter" class="filter-label d-none d-md-block mb-1">Kategori</label>
                                    <div class="select-container">
                                        <select id="kategoriFilter" class="form-select custom-select" onchange="filterAndSort()">
                                            <option value="0">Tüm Kategoriler</option>
                                            <?php while($kategoricekk=$kategorisorr->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $kategoricekk['kategori_id'] ?>" 
                                                <?php echo ($kategori_id == $kategoricekk['kategori_id']) ? 'selected' : ''; ?>>
                                                <?php echo $kategoricekk['ad']; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">

                    <?php while($kurscek=$kurssor->fetch(PDO::FETCH_ASSOC)) {

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
                                    <?php 
                                    // Display course type
                                    $kurs_tur = $kurscek['kurs_tur'];
                                    $tur_label = '';
                                    $tur_class = '';
                                    
                                    switch($kurs_tur) {
                                        case 'canli':
                                            $tur_label = 'Canlı';
                                            $tur_class = 'bg-primary';
                                            break;
                                        case 'online':
                                            $tur_label = 'Online';
                                            $tur_class = 'bg-success';
                                            break;
                                        case 'yuzyuze':
                                            $tur_label = 'Yüz Yüze';
                                            $tur_class = 'bg-warning';
                                            break;
                                        default:
                                            $tur_label = 'Kurs';
                                            $tur_class = 'bg-secondary';
                                    }
                                    ?>
                                    <div class="course-type-badge <?php echo $tur_class; ?>"><?php echo $tur_label; ?></div>
                                </div>
                                <div class="content">
                                    <div class="content-herd">
                                        <span class="cr-price" ><?php echo $kurscek['kurs_fiyat'] > 0 ? $kurscek['kurs_fiyat'] . ' TL' : 'Ücretsiz'; ?></span>
                                        <div class="rating">
                                        <span class="rating-score"><?php echo number_format($kurscek['puan'], 1, ',', ''); ?></span>
                                        <?php
                                        $filledStars = floor($kurscek['puan']); // Tam dolu yıldızlar
                                        $halfStar = ($kurscek['puan'] - $filledStars) >= 0.5; // Yarım yıldız kontrolü
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
                                            <?php echo $kurscek['ogrenci_sayi'] ?> Öğrenci
                                        </span></li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>

                    <?php } ?>

                    <div class="section-button text-center">
                        <?php if ($toplamKurs > ($sayfa * $limit)) { ?>
                            <a href="javascript:void(0)" class="default-btn" id="loadMoreBtn" data-page="<?php echo $sayfa; ?>">
                                Daha Fazla <i class='bx bx-revision'></i>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End EduMim Courses Area -->

       <?php include 'footer.php'; ?>
       
<script>
function filterAndSort() {
    const kategoriId = document.getElementById('kategoriFilter').value;
    const siralama = document.getElementById('siralamaFilter').value;
    
    // Build the URL with current parameters
    let url = new URL(window.location.href);
    url.searchParams.set('kategori_id', kategoriId);
    if (siralama) {
        url.searchParams.set('siralama', siralama);
    } else {
        url.searchParams.delete('siralama');
    }
    url.searchParams.set('sayfa', '1'); // Reset to first page when filtering/sorting
    
    window.location.href = url.toString();
}

// Update the existing loadMoreBtn event listener to maintain filters when loading more
document.getElementById('loadMoreBtn').addEventListener('click', function() {
    const currentPage = parseInt(this.getAttribute('data-page'));
    const nextPage = currentPage + 1;
    const limit = <?php echo $limit; ?>;
    const totalCourses = <?php echo $toplamKurs; ?>;
    
    // Add current filter and sort parameters to the AJAX request
    const kategoriId = document.getElementById('kategoriFilter').value;
    const siralama = document.getElementById('siralamaFilter').value;
    
    let url = `kurslar_ajax.php?sayfa=${nextPage}`;
    if (kategoriId > 0) url += `&kategori_id=${kategoriId}`;
    if (siralama) url += `&siralama=${siralama}`;
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.querySelector('.section-button').insertAdjacentHTML('beforebegin', data);
            this.setAttribute('data-page', nextPage);
            
            const shownCourses = Math.min(nextPage * limit, totalCourses);
            document.getElementById('courseCountDisplay').textContent = 
                `${totalCourses} kurstan ${shownCourses} tanesi gösteriliyor`;
            
            if (totalCourses <= (nextPage * limit)) {
                this.style.display = 'none';
            }
        });
});
</script>

<style>
/* Filtreleme ve sıralama için özel stiller */
.filter-wrapper {
    width: 100%;
}

.filter-item {
    width: 100%;
}

.filter-label {
    font-size: 14px;
    font-weight: 500;
    color: #555;
}

.select-container {
    position: relative;
}

.custom-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding: 10px 35px 10px 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
    background-color: #fff;
    font-size: 14px;
    font-weight: 500;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.custom-select:hover, .custom-select:focus {
    border-color: #0093c4;
    box-shadow: 0 3px 8px rgba(0,147,196,0.1);
}

.filter-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #0093c4;
    pointer-events: none;
}

/* Responsive ayarlar */
@media (min-width: 768px) {
    .filter-item {
        width: auto;
        min-width: 180px;
    }
}

@media (max-width: 767px) {
    .filter-item {
        margin-bottom: 10px;
    }
    
    .custom-select {
        padding: 8px 35px 8px 12px;
    }
}

/* Fixed Course Type Badge Styles for kurslar_1.php */
.single-courses-box02 .image {
    position: relative;
    overflow: hidden;
}

.single-courses-box02 .image .course-type-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    color: white;
    font-size: 13px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 50px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    z-index: 5;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.single-courses-box02 .image .course-type-badge:before {
    content: "";
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.8);
    margin-right: 2px;
}

.single-courses-box02 .image:hover .course-type-badge {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.single-courses-box02 .image .bg-primary {
    background: linear-gradient(135deg, #2980b9, #3498db);
}

.single-courses-box02 .image .bg-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
}

.single-courses-box02 .image .bg-warning {
    background: linear-gradient(135deg, #f39c12, #f1c40f);
    color: #333;
}

.single-courses-box02 .image .bg-secondary {
    background: linear-gradient(135deg, #575757, #7f8c8d);
}
</style>
       