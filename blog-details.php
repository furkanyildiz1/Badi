<?php include 'header.php';

// ID değerini al
if (isset($_GET['blog_id'])) {
    $blog_id = intval($_GET['blog_id']);

    // İlgili blogu çek
    $bloggsor = $db->prepare("SELECT * FROM blog WHERE blog_id = :blog_id");
    $bloggsor->execute(['blog_id' => $blog_id]);
    $bloggcek = $bloggsor->fetch(PDO::FETCH_ASSOC);

    // Blog bulunamazsa hata göster
    if (!$bloggcek) {
        echo "Blog bulunamadı.";
        exit;
    }
} else {
    echo "Geçerli bir blog seçilmedi.";
    exit;
}


$bloggkategorisor = $db->prepare("SELECT * FROM blog_kategori");
$bloggkategorisor->execute();

$bloggkategorisorr = $db->prepare("SELECT kategori_ad FROM blog_kategori WHERE blogkategori_id=:blogkategori_id");
$bloggkategorisorr->execute([
    'blogkategori_id' => $bloggcek['blog_kategori_id']
]);
$bloggkategoricekk = $bloggkategorisorr->fetch(PDO::FETCH_ASSOC);

?>

<!-- Start Page Title Area -->
<section class="page-title-area item-bg1">
    <div class="container">
        <div class="page-title-content">
            <h2>Blog Detay</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Anasayfa</a></li>
                <li class="breadcrumb-item"></li>
                <li class="primery-link">Blog Detay</li>
            </ul>
        </div>
    </div>
</section>
<!-- End Page Title Area -->

<!-- Start Blog Details Area with SideBar -->
<div class="edu-courses-area pt-70 pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="blog-details-desc">
                    <div class="article-image">
                        <img src="<?php echo $bloggcek['blog_resimyol'] ?>" alt="image">
                    </div>
                    <div class="article-content">
                        <div class="entry-meta">
                            <ul>
                                <li><i class='bx bx-user'></i> <a href="#"><?php echo $bloggcek['yazar_ad']; ?></a></li>
                                <li><i class='bx bx-calendar'></i>
                                    <span>
                                        <?php 
                                        // Yerel ayarları Türkçe olarak ayarla
                                        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');

                                        // Tarihi al ve istediğin formata çevir
                                        echo strftime('%d %B %Y', strtotime($bloggcek['blog_tarih'])); 
                                        ?>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <h2><?php echo $bloggcek['blog_ad']; ?></h2>

                        <?php echo $bloggcek['blog_aciklama']; ?>





                        <div class="article-footer">
                            <div class="article-tags">
                                <span>Etiketler:</span>
                                <a href="#"><?php echo $bloggkategoricekk['kategori_ad']; ?></a>
                                <a href="#"><?php echo $bloggcek['yazar_ad']; ?></a>
                            </div>

                        </div>
                    </div>
                    <!-- 
                            <div class="comments-area">
                                <h3 class="comments-title">2 Comments:</h3>
                                
                                <ol class="comment-list">
                                    <li class="comment">
                                        <article class="comment-body">
                                            <footer class="comment-meta">
                                                <div class="comment-author vcard">
                                                    <img src="assets/img/all-img/cmnt-1.png" class="avatar" alt="image">
                                                    <h4 class="fn">Ferira Watson</h4>

                                                    <div class="reply">
                                                        <a href="#" class="comment-reply-link"><i class='bx bx-share bx-flip-vertical' ></i> Reply</a>
                                                    </div>
                                                </div>
                                                <div class="comment-metadata">
                                                    <span>Oct 09, 2021</span>
                                                </div>
                                            </footer>
                                            <div class="comment-content">
                                                <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form</p>
                                                <div class="reply">
                                                    <a href="#" class="comment-reply-link"><i class='bx bx-share bx-flip-vertical' ></i> Reply</a>
                                                </div>
                                            </div>
                                        </article>
                                
                                        <ol class="children">
                                            <li class="comment">
                                                <article class="comment-body">
                                                    <footer class="comment-meta">
                                                        <div class="comment-author vcard">
                                                            <img src="assets/img/all-img/cmnt-2.png" class="avatar" alt="image">
                                                            <h4 class="fn">Steven Smith</h4>
                                                            <div class="reply">
                                                                <a href="#" class="comment-reply-link"><i class='bx bx-share bx-flip-vertical' ></i> Reply</a>
                                                            </div>
                                                        </div>
                                
                                                        <div class="comment-metadata">
                                                            <span>Oct 09, 2021</span>
                                                        </div>
                                                    </footer>
                                
                                                    <div class="comment-content">
                                                        <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form</p>
                                                        <div class="reply">
                                                            <a href="#" class="comment-reply-link"><i class='bx bx-share bx-flip-vertical' ></i> Reply</a>
                                                        </div>
                                                    </div>
                                                </article>
                                
                                                <ol class="children">
                                                    <li class="comment">
                                                        <article class="comment-body">
                                                            <footer class="comment-meta">
                                                                <div class="comment-author vcard">
                                                                    <img src="assets/img/all-img/cmnt-1.png" class="avatar" alt="image">
                                                                    <h4 class="fn">Sarah Taylor</h4>
                                                                    <div class="reply">
                                                                        <a href="#" class="comment-reply-link"><i class='bx bx-share bx-flip-vertical' ></i> Reply</a>
                                                                    </div>
                                                                </div>
                                
                                                                <div class="comment-metadata">
                                                                    <span>Oct 09, 2021</span>
                                                                </div>
                                                            </footer>
                                
                                                            <div class="comment-content">
                                                                <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form</p>
                                                                <div class="reply">
                                                                    <a href="#" class="comment-reply-link"><i class='bx bx-share bx-flip-vertical' ></i> Reply</a>
                                                                </div>
                                                            </div>
                                                        </article>
                                                    </li>
                                                </ol>
                                            </li>
                                        </ol>
                                    </li>
                                </ol>
                                <div class="comment-respond">
                                    <h3 class="comment-reply-title">Leave a Reply</h3>
                                
                                    <form class="comment-form">
                                        <p class="comment-notes">
                                            <span id="email-notes">Your email address will not be published.</span>
                                            Required fields are marked <span class="required">*</span>
                                        </p>
                                
                                        <p class="comment-form-author">
                                            <label>Name <span class="required">*</span></label>
                                            <input type="text" id="author" placeholder="Your Name*" name="author" required="required">
                                        </p>
                                
                                        <p class="comment-form-email">
                                            <label>Email <span class="required">*</span></label>
                                            <input type="email" id="email" placeholder="Your Email*" name="email" required="required">
                                        </p>
                                
                                        <p class="comment-form-url">
                                            <label>Website</label>
                                            <input type="url" id="url" placeholder="Website" name="url">
                                        </p>
                                
                                        <p class="comment-form-comment">
                                            <label>Comment</label>
                                            <textarea name="comment" id="comment" cols="45" placeholder="Your Comment..." rows="5" maxlength="65525" required="required"></textarea>
                                        </p>
                                
                                        <p class="comment-form-cookies-consent">
                                            <input type="checkbox" value="yes" name="wp-comment-cookies-consent" id="wp-comment-cookies-consent">
                                            <label for="wp-comment-cookies-consent">Save my name, email, and website in this browser for the next time I comment.</label>
                                        </p>
                                        
                                        <p class="form-submit">
                                            <input type="submit" name="submit" id="submit" class="submit" value="Post Comment">
                                        </p>
                                    </form>
                                </div>
                            </div>
                            -->
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