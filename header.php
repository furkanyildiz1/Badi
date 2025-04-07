<?php 
header('Content-Type: text/html; charset=utf-8');
session_start();

include 'nedmin/netting/baglan.php';

$ayarsor = $db->prepare("SELECT * FROM ayar WHERE ayar_id=:ayar_id");
$ayarsor->execute([
    'ayar_id' => 0
]);
$ayarcek = $ayarsor->fetch(PDO::FETCH_ASSOC);

$menusor = $db->prepare("SELECT * FROM menu WHERE menu_durum=:durum ORDER BY menu_sira ASC");
$menusor->execute([
    'durum' => 1]);

$mobilmenusor = $db->prepare("SELECT * FROM menu ORDER BY menu_sira ASC");
$mobilmenusor->execute();

$altkategorisor = $db->prepare("SELECT * FROM alt_kategoriler");
$altkategorisor->execute();

$kurssor=$db->prepare("SELECT * FROM kurslar WHERE vitrin_durum=:vitrin_durum");
$kurssor->execute([
    'vitrin_durum' => 1
]);

$markasor=$db->prepare("SELECT * FROM markalar WHERE markavitrin_durum=:markavitrin_durum");
$markasor->execute([
    'markavitrin_durum' => 1
]);

$anahakkimizdasor=$db->prepare("SELECT * FROM anahakkimizda");
$anahakkimizdasor->execute();
$anahakkimizdacek=$anahakkimizdasor->fetch(PDO::FETCH_ASSOC);

$egitmensorr=$db->prepare("SELECT * FROM egitmen WHERE egitmen_vitrin=:egitmen_vitrin");
$egitmensorr->execute([
    'egitmen_vitrin' => 1
]);

$blogsor=$db->prepare("SELECT * FROM blog WHERE blog_vitrin=:blog_vitrin");
$blogsor->execute([
    'blog_vitrin' => 1
]);

// User Agent Parser fonksiyonu
function parse_user_agent($userAgent) {
    $browserList = array(
        'Chrome', 'Firefox', 'Safari', 'Opera', 'MSIE', 'Trident', 'Edge'
    );
    $osList = array(
        'Windows' => 'Windows',
        'iPhone' => 'iOS',
        'iPad' => 'iOS',
        'Android' => 'Android',
        'Linux' => 'Linux',
        'Macintosh' => 'MacOS'
    );

    // Varsayılan değerler
    $browser = 'Diğer';
    $platform = 'Diğer';

    // Tarayıcı tespiti
    foreach ($browserList as $browserName) {
        if (strpos($userAgent, $browserName) !== false) {
            $browser = $browserName;
            break;
        }
    }

    // İşletim sistemi tespiti
    foreach ($osList as $key => $value) {
        if (strpos($userAgent, $key) !== false) {
            $platform = $value;
            break;
        }
    }

    // Edge için özel kontrol
    if (strpos($userAgent, 'Edg') !== false) {
        $browser = 'Edge';
    }

    // IE için özel kontrol
    if ($browser == 'Trident' || $browser == 'MSIE') {
        $browser = 'Internet Explorer';
    }

    return array(
        'browser' => $browser,
        'platform' => $platform
    );
}

// Ziyaretçi kaydı için fonksiyon
function ziyaretciKaydet($db) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'];
        $tarayici = $_SERVER['HTTP_USER_AGENT'];
        
        // Tarayıcı ve cihaz tespiti
        $parser = parse_user_agent($tarayici);
        $browser = $parser['browser'];
        $os = $parser['platform'];
        
        // Mobil cihaz kontrolü
        $cihaz = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$tarayici)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($tarayici,0,4))) ? 'mobile' : 'desktop';
        
        // Günlük kontrol
        $kontrolSor = $db->prepare("SELECT COUNT(*) as sayi FROM site_ziyaret 
                                   WHERE ziyaret_ip = :ip 
                                   AND DATE(ziyaret_tarih) = CURDATE()");
        $kontrolSor->execute(['ip' => $ip]);
        $kontrol = $kontrolSor->fetch(PDO::FETCH_ASSOC);

        if($kontrol['sayi'] == 0) {
            $ziyaretEkle = $db->prepare("INSERT INTO site_ziyaret SET 
                ziyaret_ip = :ip,
                ziyaret_tarayici = :tarayici,
                ziyaret_cihaz = :cihaz,
                ziyaret_os = :os,
                ziyaret_tarih = NOW()");
                
            $ziyaretEkle->execute([
                'ip' => $ip,
                'tarayici' => $browser,
                'cihaz' => $cihaz,
                'os' => $os
            ]);
        }
    } catch(PDOException $e) {
        error_log("Ziyaretçi kayıt hatası: " . $e->getMessage());
    }
}

// Admin paneli dışındaki sayfalarda ziyaretçi kaydı yap
if(!strpos($_SERVER['REQUEST_URI'], 'nedmin')) {
    ziyaretciKaydet($db);
}

?>

 <!doctype html>
<html lang="tr">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta Robots Etiketleri -->
    <meta name="robots" content="index, follow">
    <meta name="robots" content="noarchive">


    <!-- Open Graph Etiketleri -->
    <meta property="og:title" content="Badi Akademi">
    <meta property="og:description" content="Türkiye'nin en yenilikçi eğitim platformu.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Badi Akademi">


    <!-- Meta Kelimeleri  -->
    <meta name="keywords" content="<?php echo $ayarcek['ayar_keywords']; ?>">
    <meta name="description" content="<?php echo $ayarcek['ayar_description']; ?>">
    <meta name="author" content="<?php echo $ayarcek['ayar_author']; ?>">

    <!-- Güvenlik etiketleri -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="no-referrer">

    <!-- Performans İyileştirmeleri -->
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">

    

    <!-- Links of CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/boxicons.min.css" media="print" onload="this.onload=null;this.media='all';">
    <link rel="stylesheet" href="assets/css/odometer.min.css" media="print" onload="this.onload=null;this.media='all';">
    <link rel="stylesheet" href="assets/css/nice-select.min.css" media="print" onload="this.onload=null;this.media='all';">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css" media="print" onload="this.onload=null;this.media='all';">
    <link rel="stylesheet" href="assets/css/meanmenu.min.css" media="print" onload="this.onload=null;this.media='all';">
    <link rel="stylesheet" href="assets/css/magnific-popup.min.css" media="print" onload="this.onload=null;this.media='all';">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">


    
    

    <!-- title etiketi buraya -->
    <title><?php echo $ayarcek['ayar_title']; ?></title>
    <link rel="icon" type="image/png" href="<?php echo $ayarcek['ayar_favicon']; ?>">

    <!-- Add this CSS to the head section or your custom CSS file -->
    <style>
      .notification-badge {
        position: absolute;
        top: 10px;
        right: 0px;
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
      }

      .mobil-user-profile {
        padding: 15px;
        border-bottom: 1px solid rgba(43, 199, 248, 0.1);
        background: rgba(43, 199, 248, 0.05);
        border-radius: 8px;
      }

      .profile-header {
        display: flex;
        align-items: center;
        padding: 10px;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s ease;
      }

      .profile-header:hover {
        background: rgba(43, 199, 248, 0.1);
      }

      .profile-header i {
        color: #2bc7f8;
        margin: 0px 10px;
        transition: transform 0.3s ease;
      }

      .profile-header.active i.fa-chevron-down {
        transform: rotate(180deg);
      }

      .profile-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background: rgba(43, 199, 248, 0.05);
        border-radius: 8px;
        margin-top: 10px;
        box-shadow: 0 4px 6px rgba(43, 199, 248, 0.1);
      }

      .profile-menu.show {
        max-height: 300px;
      }

      .profile-menu a {
        padding: 12px 15px;
        color: #333 !important;
        text-decoration: none;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(43, 199, 248, 0.1);
      }

      .profile-menu a:last-child {
        border-bottom: none;
      }

      .profile-menu a:hover {
        background: rgba(43, 199, 248, 0.1);
        padding-left: 20px;
        color: #2bc7f8 !important;
      }

      .profile-menu i {
        color: #2bc7f8;
        margin-right: 10px;
        width: 20px;
        text-align: center;
      }

      .mobil-auth-buttons {
        padding: 15px;
        border-bottom: 1px solid rgba(43, 199, 248, 0.1);
      }

      .mobil-auth-buttons .btn-warning {
        background-color: #2bc7f8;
        border-color: #2bc7f8;
        color: #fff;
      }

      .mobil-auth-buttons .btn-warning:hover {
        background-color: #1ab6e8;
        border-color: #1ab6e8;
      }

      .mobil-auth-buttons .btn-outline-light {
        border-color: #2bc7f8;
        color: #2bc7f8;
      }

      .mobil-auth-buttons .btn-outline-light:hover {
        background-color: rgba(43, 199, 248, 0.1);
        border-color: #2bc7f8;
        color: #2bc7f8;
      }

      .notification-badge {
        background-color: #2bc7f8;
        color: white;
        padding: 3px 8px;
        border-radius: 50px;
        font-size: 0.7rem;
        position: absolute;
        right: 15px;
      }
    </style>
</head>

<body>
    
    <!-- SweetAlert2 Kullanıcıya uyarı mesajı için -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Start Badi Top Navbar Area -->
    <div class="edu-top-navbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="top-nav-left">
                        <ul>
                            <!-- sabit hat numarası buraya -->
                            <li><a href="tel:<?php echo $ayarcek['ayar_gsm']; ?>"><i class="fa fa-phone"></i>
                                    <?php echo $ayarcek['ayar_gsm']; ?></a></li>
                            <li>Hizmetlerimiz hakkında bilgi almak için bizi arayın.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="top-nav-right">
                        <ul class="contact-list">
                            <!-- mail adresi buraya -->
                            <li><a href="/iletisim"><i class="fa fa-envelope"></i>
                                    <?php echo $ayarcek['ayar_mail']; ?></a></li>
                            <!-- adres buraya gelecek -->
                            <li><a href="/iletisim"><i class="fa-solid fa-location-dot"></i>
                                    <?php echo $ayarcek['ayar_adres']; ?></a>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Top Navbar Area -->

    <!-- Start Navbar Area -->
    <div class="edu-navbar-area navbar-area edu-navbar-area2">
        <div class="edumim-nav">
            <div class="container">
                <nav class="navbar navbar-expand-md navbar-light">
                    <!-- Logo kodları buraya eklenecek -->
                    <a class="navbar-brand" href="index.php"><img src="<?php echo $ayarcek['ayar_logo'] ?>"
                            alt="logo"></a>
                    <div class="navbar-collapse mean-menu">
                        <ul class="navbar-nav">
                            <?php while ($menucek = $menusor->fetch(PDO::FETCH_ASSOC)) { ?>
                                <li class="nav-item"><a href="<?php echo $menucek['menu_url']; ?>"
                                        class="nav-link"><?php echo $menucek['menu_ad']; ?></a>
                                </li>
                            <?php } ?>
                        </ul>

                        <!-- User Authentication Section -->
                        <ul class="navbar-nav ms-auto">
                            <?php if(isset($_SESSION['userkullanici_mail'])) { 
                                // Query to count unread invoices - fixed to use user_id
                                $unreadInvoicesQuery = $db->prepare("SELECT COUNT(*) as count FROM faturalar 
                                                                   WHERE user_id = :user_id 
                                                                   AND bildirim_okundu = 0");
                                $unreadInvoicesQuery->execute([
                                    'user_id' => $_SESSION['userkullanici_id']
                                ]);
                                $unreadCount = $unreadInvoicesQuery->fetch(PDO::FETCH_ASSOC)['count'];
                            ?>
                                <!-- Logged in state -->
                                <li class="nav-item">
                                    <span style="margin-right: 15px;">
                                        <?php
                                        // Extract first name by splitting at the first space
                                        $name_parts = explode(' ', $_SESSION['userkullanici_adsoyad'], 2);
                                        echo "Hoş Geldiniz, " . $name_parts[0]; // Display only the first part (first name)
                                        ?>
                                    </span>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-user"></i>
                                        <?php if($unreadCount > 0): ?>
                                        <span class="badge rounded-pill bg-danger notification-badge">
                                            <?php echo $unreadCount; ?>
                                        </span>
                                        <?php endif; ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="profile.php">Profilim</a></li>
                                        <li><a class="dropdown-item" href="my-courses.php">Kurslarım</a></li>
                                        <li><a class="dropdown-item position-relative" href="siparislerim.php">
                                            Siparişlerim
                                            <?php if($unreadCount > 0): ?>
                                            <span class="badge rounded-pill bg-danger float-end">
                                                <?php echo $unreadCount; ?>
                                            </span>
                                            <?php endif; ?>
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <!-- Logged out state -->
                                <li class="nav-item">
                                    <a class="nav-link" href="login.php">Giriş Yap</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="register.php">Üye Ol</a>
                                </li>
                            <?php } ?>
                            <?php if(isset($_SESSION['userkullanici_mail'])) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cart.php">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="nav-wp">
                        <ul>
                            <li class="nav-wp-li">
                                <p>Sorunuz mu var?</p>
                                <a href="wa.me/+905449122234"><span class="fa-brands fa-whatsapp"></span> <?php echo $ayarcek['ayar_tel']; ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="nav-btn">
                        <ul>
                            <li><button type="button" class="menu-button" onclick="toggleMenu()" aria-label="Menüyü aç/kapat"><i
                                        class="fa-solid fa-bars"></i> </button>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <div class="mobil-menu" id="sideMenu">
        <nav class="navbar mobil-navbar">
            <a class="navbar-brand" href="index.php"><img src="<?php echo $ayarcek['ayar_logo'] ?>" alt="logo"></a>
            
            <!-- Kullanıcı Profil Bölümü -->
            <?php if(isset($_SESSION['userkullanici_mail'])) { ?>
                <div class="mobil-user-profile">
                    <div class="profile-header" onclick="toggleProfileMenu()">
                        <i class="fa-solid fa-user"></i>
                        <span style="color: #fff; flex-grow: 1;">
                            <?php
                            $name_parts = explode(' ', $_SESSION['userkullanici_adsoyad'], 2);
                            echo "Hoş Geldiniz, " . $name_parts[0];
                            ?>
                        </span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="profile-menu" id="profileMenu">
                        <a href="profile.php">
                            <i class="fa-solid fa-user-circle"></i>
                            <span>Profilim</span>
                        </a>
                        <a href="my-courses.php">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>Kurslarım</span>
                        </a>
                        <a href="siparislerim.php">
                            <i class="fa-solid fa-shopping-bag"></i>
                            <span>Siparişlerim</span>
                            <?php if($unreadCount > 0): ?>
                                <span class="notification-badge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="cart.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span>Sepetim</span>
                        </a>
                        <a href="logout.php">
                            <i class="fa-solid fa-sign-out-alt"></i>
                            <span>Çıkış Yap</span>
                        </a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="mobil-auth-buttons">
                    <a href="login.php" class="btn btn-outline-light w-100 mb-2">
                        <i class="fa-solid fa-sign-in-alt me-2"></i> Giriş Yap
                    </a>
                    <a href="register.php" class="btn btn-warning w-100">
                        <i class="fa-solid fa-user-plus me-2"></i> Üye Ol
                    </a>
                </div>
            <?php } ?>

            <div class="navbar-collapse mean-menu">
                <ul class="navbar-nav">
                    <?php while ($mobilmenucek = $mobilmenusor->fetch(PDO::FETCH_ASSOC)) { ?>
                        <li class="nav-item"><a href="<?php echo $mobilmenucek['menu_url']; ?>"
                                class="nav-link"><?php echo $mobilmenucek['menu_ad']; ?></a>
                    <?php } ?>
                </ul>
            </div>
        </nav>
        <div class="mobil-menu-iletisim">
            <ul>
                <li>
                    <p>Sorunuz mu var?</p>
                    <a href="https://wa.me/+905449122234"><span class="fa-brands fa-whatsapp"></span> <?php echo $ayarcek['ayar_tel']; ?> </a>
                </li>
                <li>
                    <p>İletişim Adresi</p>
                    <a href="contact.php"><i class="fa-solid fa-location-dot"></i>
                        <?php echo $ayarcek['ayar_adres']; ?></a>
                </li>
                <li><a href="tel:08504806684"><i class="fa fa-phone"></i> <?php echo $ayarcek['ayar_gsm']; ?></a></li>
                <li><a href="contact.php"><i class="fa fa-envelope"></i> <?php echo $ayarcek['ayar_mail']; ?></a></li>
            </ul>
        </div>
    </div>
    
    <!-- End Navbar Area -->

    <script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        const header = document.querySelector('.profile-header');
        menu.classList.toggle('show');
        header.classList.toggle('active');
    }
    </script>
</body>
</html>