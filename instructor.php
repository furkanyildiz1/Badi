<?php 
include 'header.php';

// Get all active instructors
$egitmensor = $db->prepare("SELECT * FROM egitmen");
$egitmensor->execute();
?>

<!-- Start Page Title Area -->
<div class="page-title-area bg-wall pt-190 pb-100" style="margin-top: 0; padding-bottom: 0;">
    <div class="container">
        <div class="page-title-content">
            <h2>Eğitmenlerimiz</h2>
            <ul class="breadcrumb-nav">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li class="active">Eğitmenler</li>
            </ul>
        </div>
    </div>
</div>
<!-- End Page Title Area -->

<!-- Start Instructor Area -->
<section class="instructor-area pt-100 pb-70">
    <div class="container">
        <div class="row justify-content-center">
            <?php while($egitmencek = $egitmensor->fetch(PDO::FETCH_ASSOC)) { 
                // Get instructor's course count
                $kurs_say = $db->prepare("SELECT COUNT(*) as kurs_sayisi FROM kurslar WHERE egitmen_id = :egitmen_id");
                $kurs_say->execute(['egitmen_id' => $egitmencek['egitmen_id']]);
                $kurs_sayisi = $kurs_say->fetch(PDO::FETCH_ASSOC)['kurs_sayisi'];
                
                // Get instructor's total students
                $ogrenci_say = $db->prepare("
                    SELECT COUNT(DISTINCT s.user_id) as ogrenci_sayisi 
                    FROM faturalar s 
                    JOIN satilan_kurslar sk ON sk.fatura_id = s.fatura_id 
                    JOIN kurslar k ON sk.kurs_id = k.kurs_id 
                    WHERE k.egitmen_id = :egitmen_id
                ");
                $ogrenci_say->execute(['egitmen_id' => $egitmencek['egitmen_id']]);
                $ogrenci_sayisi = $ogrenci_say->fetch(PDO::FETCH_ASSOC)['ogrenci_sayisi'];
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="single-instructor">
                        <div class="instructor-image">
                            <a href="egitmen_detay.php?egitmen_id=<?php echo $egitmencek['egitmen_id']; ?>">
                                <img src="<?php echo $egitmencek['egitmen_resimyol']; ?>" alt="<?php echo $egitmencek['egitmen_adsoyad']; ?>">
                            </a>

                            <ul class="social">
                                <?php if($egitmencek['egitmen_medyabir']) { ?>
                                    <li><a href="https://<?php echo $egitmencek['egitmen_medyabir']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                <?php } ?>
                                <?php if($egitmencek['egitmen_medyaiki']) { ?>
                                    <li><a href="https://<?php echo $egitmencek['egitmen_medyaiki']; ?>" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                <?php } ?>
                                <?php if($egitmencek['egitmen_medyauc']) { ?>
                                    <li><a href="https://<?php echo $egitmencek['egitmen_medyauc']; ?>" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                <?php } ?>
                                <?php if($egitmencek['egitmen_medyadort']) { ?>
                                    <li><a href="https://<?php echo $egitmencek['egitmen_medyadort']; ?>" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="instructor-content">
                            <h3>
                                <a href="egitmen_detay.php?egitmen_id=<?php echo $egitmencek['egitmen_id']; ?>">
                                    <?php echo $egitmencek['egitmen_adsoyad']; ?>
                                </a>
                            </h3>
                            <span><?php echo $egitmencek['egitmen_rol']; ?></span>

                            <div class="instructor-info">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6 col-6">
                                        <div class="instructor-info-box">
                                            <i class="fa-solid fa-book"></i>
                                            <h3><?php echo $kurs_sayisi; ?></h3>
                                            <span>Kurs</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-6">
                                        <div class="instructor-info-box">
                                            <i class="fa-solid fa-users"></i>
                                            <h3><?php echo $ogrenci_sayisi; ?></h3>
                                            <span>Öğrenci</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p><?php echo substr($egitmencek['egitmen_hakkinda'], 0, 150); ?>...</p>

                            <a href="egitmen_detay.php?egitmen_id=<?php echo $egitmencek['egitmen_id']; ?>" class="default-btn">
                                <i class="fa-solid fa-user"></i> Profili Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<!-- End Instructor Area -->

<style>
.single-instructor {
    background-color: #ffffff;
    border-radius: 5px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.single-instructor:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
}

.instructor-image {
    position: relative;
    margin-bottom: 25px;
    overflow: hidden;
    border-radius: 5px;
}

.instructor-image img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 5px;
}

.instructor-image .social {
    padding: 0;
    margin: 0;
    list-style-type: none;
    position: absolute;
    left: 0;
    right: 0;
    bottom: -100px;
    text-align: center;
    transition: all 0.3s ease;
    background: rgba(0, 0, 0, 0.7);
    padding: 10px 0;
}

.single-instructor:hover .social {
    bottom: 0;
}

.instructor-image .social li {
    display: inline-block;
    margin: 0 4px;
}

.instructor-image .social li a {
    color: #ffffff;
    font-size: 18px;
    transition: all 0.3s ease;
}

.instructor-image .social li a:hover {
    color: #ff6b6b;
}

.instructor-content h3 {
    font-size: 22px;
    margin-bottom: 10px;
}

.instructor-content h3 a {
    color: #333;
    text-decoration: none;
}

.instructor-content span {
    color: #666;
    font-size: 15px;
    display: block;
    margin-bottom: 15px;
}

.instructor-info {
    margin: 20px 0;
    padding: 20px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.instructor-info-box {
    text-align: center;
}

.instructor-info-box i {
    font-size: 24px;
    color: #ff6b6b;
    margin-bottom: 10px;
}

.instructor-info-box h3 {
    font-size: 20px;
    margin: 0;
    color: #333;
}

.instructor-info-box span {
    font-size: 14px;
    color: #666;
}

.instructor-content p {
    color: #666;
    margin: 15px 0;
    line-height: 1.6;
}

.default-btn {
    display: inline-block;
    padding: 12px 25px;
    background-color: #ff6b6b;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.default-btn:hover {
    background-color: #ff5252;
    color: #fff;
}

.default-btn i {
    margin-right: 5px;
}

.breadcrumb-nav {
    padding: 0;
    margin: 15px 0 0;
    list-style: none;
    display: flex;
    align-items: center;
    gap: 10px;
}

.breadcrumb-nav li {
    position: relative;
    color: #666;
    font-size: 16px;
}

.breadcrumb-nav li:not(:last-child)::after {
    content: '/';
    margin-left: 10px;
    color: #666;
}

.breadcrumb-nav li a {
    color: #ff6b6b;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-nav li a:hover {
    color: #ff5252;
}

.breadcrumb-nav li.active {
    color: #666;
}

.page-title-content h2 {
    font-size: 36px;
    margin-bottom: 0;
    color: #333;
}
</style>

<?php include 'footer.php'; ?> 