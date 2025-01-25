<?php

include 'nedmin/netting/baglan.php';
include 'header.php';

$blogblogsor = $db->prepare("SELECT * FROM blog");
$blogblogsor->execute();


$bloggkategorisor = $db->prepare("SELECT * FROM blog_kategori");
$bloggkategorisor->execute();

$bloggkategorisorr = $db->prepare("SELECT kategori_ad FROM blog_kategori");
$bloggkategorisorr->execute();
$bloggkategoricekk = $bloggkategorisorr->fetch(PDO::FETCH_ASSOC);
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

<!-- Start Blog Area with SideBar -->
<div class="edu-blog-area pt-70 pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
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
                                    <a href="blog-details.php?blog_id=<?php echo $blogblogcek['blog_id']; ?>"
                                        class="d-block">
                                        <img src="<?php echo $blogblogcek['blog_resimyol']; ?>" alt="image">
                                    </a>
                                    <div class="cr-tag">
                                        <a href="#"><span><?php echo $blogkategoricek['kategori_ad']; ?></span></a>
                                    </div>
                                </div>
                                <div class="content">
                                    <ul class="cr-items">
                                        <li><a href="#"><i
                                                    class='bx bx-user'></i><span><?php echo $blogblogcek['yazar_ad']; ?></span></a>
                                        </li>
                                        <li><i class='bx bx-star'></i><span><?php echo $blogblogcek['blog_tarih']; ?></span>
                                        </li>
                                    </ul>
                                    <!-- ID'yi tıklanan başlığa da ekliyoruz -->
                                    <h3><a
                                            href="blog-details.php?blog_id=<?php echo $blogblogcek['blog_id']; ?>"><?php echo $blogblogcek['blog_ad']; ?></a>
                                    </h3>
                                    <a class="blog-btn"
                                        href="blog-details.php?blog_id=<?php echo $blogblogcek['blog_id']; ?>">Devamını
                                        oku...</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <aside class="blog-sidebar-widgets">
                    <div class="widget widget-search">
                        <form class="search-form">
                            <label>
                                <input type="search" class="search-field" placeholder="Ara...">
                            </label>
                            <button class="widget-search-btn" type="submit"><i class="bx bx-search-alt"></i></button>
                        </form>
                    </div>

                    <div class="widget widget-catgory">
                        <h3 class="widget-title">Kategoriler</h3>
                        <ul>
                            <?php while ($bloggkategoricek = $bloggkategorisor->fetch(PDO::FETCH_ASSOC)) { ?>
                                <a href="#">
                                    <li><span><?php echo $bloggkategoricek['kategori_ad']; ?></span> <i
                                            class='bx bx-chevron-right'></i></li>
                                </a>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="widget widget-recent-blog">
                        <h3 class="widget-title">Son Eklenen Bloglar</h3>
                        <?php
                        // Veritabanından en son eklenen 3 blogu çek
                        $sonBloglarSor = $db->prepare("SELECT blog_id, blog_ad, blog_resimyol FROM blog ORDER BY blog_tarih DESC LIMIT 3");
                        $sonBloglarSor->execute();

                        // Verileri döngüyle yazdır
                        while ($sonBlog = $sonBloglarSor->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <article class="item">
                                <!-- Blog resmi ve bağlantısı -->
                                <a href="blog-details.php?blog_id=<?php echo $sonBlog['blog_id']; ?>" class="thumb">
                                    <img src="<?php echo $sonBlog['blog_resimyol']; ?>" alt="Blog Resmi">
                                </a>
                                <div class="info">
                                    <!-- Blog başlığı ve bağlantısı -->
                                    <h4 class="title">
                                        <a href="blog-details.php?blog_id=<?php echo $sonBlog['blog_id']; ?>">
                                            <?php echo $sonBlog['blog_ad']; ?>
                                        </a>
                                    </h4>
                                    <!-- "Devamını Oku" bağlantısı -->
                                    <a href="blog-details.php?blog_id=<?php echo $sonBlog['blog_id']; ?>"
                                        class="re-blog-btn">Devamını Oku</a>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
<!-- End Blog Area -->

<?php include 'footer.php'; ?>