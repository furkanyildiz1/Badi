<?php

include 'nedmin/netting/baglan.php';
include 'header.php';

$blogblogsor=$db->prepare("SELECT * FROM blog");
$blogblogsor->execute();

?>

<!-- Start Page Title Area -->
<section class="page-title-area item-bg1">
    <div class="container">
        <div class="page-title-content">
            <h2>Blog</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Anasayfa</a></li>
                <li class="breadcrumb-item"></li>
                <li class="primery-link">Blog</li>
            </ul>
        </div>
    </div>
</section>
<!-- End Page Title Area -->

<!-- Start Blog Area -->
<div class="edu-blog-area ptb-100">
    <div class="container">
        <div class="row justify-content-center">

            <?php while ($blogblogcek = $blogblogsor->fetch(PDO::FETCH_ASSOC)) {

                $blogkategorisor = $db->prepare("SELECT kategori_ad FROM blog_kategori WHERE blogkategori_id = :blogkategori_id");
                $blogkategorisor->execute([
                    'blogkategori_id' => $blogblogcek['blog_kategori_id']
                ]);
                $blogkategoricek = $blogkategorisor->fetch(PDO::FETCH_ASSOC);

                ?>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-blog-box">
                        <div class="image">
                            <!-- ID değerini URL'ye ekliyoruz -->
                            <a href="blog-details.php?blog_id=<?php echo $blogblogcek['blog_id']; ?>" class="d-block">
                                <img src="<?php echo $blogblogcek['blog_resimyol']; ?>" alt="image">
                            </a>
                            <div class="cr-tag">
                                <a href="#"><span><?php echo $blogkategoricek['kategori_ad']; ?></span></a>
                            </div>
                        </div>
                        <div class="content">
                            <ul class="cr-items">
                                <li><a href="#"><i
                                            class='bx bx-user'></i><span><?php echo $blogblogcek['yazar_ad']; ?></span></a></li>
                                <li><i class='bx bx-star'></i><span><?php echo $blogblogcek['blog_tarih']; ?></span></li>
                            </ul>
                            <!-- ID'yi tıklanan başlığa da ekliyoruz -->
                            <h3><a
                                    href="blog-details.php?blog_id=<?php echo $blogblogcek['blog_id']; ?>"><?php echo $blogblogcek['blog_ad']; ?></a>
                            </h3>
                            <a class="blog-btn" href="blog-details.php?blog_id=<?php echo $blogblogcek['blog_id']; ?>">Devamını
                                oku...</a>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<!-- End Blog Area -->


<?php include 'footer.php'; ?>