<?php 
ob_start();
session_start();

error_reporting(0);

      include '../netting/baglan.php';
      include 'fonksiyon.php';

$ayarsor=$db->prepare("SELECT * FROM ayar WHERE ayar_id=:id");
$ayarsor->execute([
  'id' => 0
]);
$ayarcek=$ayarsor->fetch(PDO::FETCH_ASSOC);

$kullanicisor=$db->prepare("SELECT * FROM kullanici WHERE kullanici_mail=:kullanici_mail");
$kullanicisor->execute([
  'kullanici_mail' => $_SESSION['kullanici_mail']
]);
$say=$kullanicisor->rowCount();
$kullanicicek=$kullanicisor->fetch(PDO::FETCH_ASSOC);

  if($say==0)
  {
    header("Location: login.php?durum=izinsiz"); //izinsiz kullanıcılar icin engelleme
    exit;
  }

  $kategorisor=$db->prepare("SELECT * FROM kategoriler");
  $kategorisor->execute();

  $altkategorisor=$db->prepare("SELECT * FROM alt_kategoriler");
  $altkategorisor->execute();

  $seviyesor=$db->prepare("SELECT * FROM kurs_seviye");
  $seviyesor->execute();

  $eskikurssor=$db->prepare("SELECT * FROM kurslar");
  $eskikurssor->execute();
  $eskikurscek=$eskikurssor->fetch(PDO::FETCH_ASSOC);

  $kurssor=$db->prepare("SELECT * FROM kurslar");
  $kurssor->execute();

  $eskiegitmensor=$db->prepare("SELECT * FROM egitmen");
  $eskiegitmensor->execute();
  $eskiegitmencek=$eskiegitmensor->fetch(PDO::FETCH_ASSOC);

  $egitmensor=$db->prepare("SELECT * FROM egitmen");
  $egitmensor->execute();

 ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nosnippet, noimageindex">

    <title>BekerSOFTWARE | Admin Paneli</title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ck editor -->
      <script src="https://cdn.ckeditor.com/4.25.0-lts/standard/ckeditor.js"></script>

      <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <!-- yıldız -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
      /* Reset ve Temel Ayarlar */
      body {
        background: #f8f9fa;
        overflow-x: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      }

      /* Ana Container */
      .container.body {
        padding-top: 50px;
        position: relative;
        background: #f8f9fa;
      }

      /* Sidebar */
      .left_col {
        width: 250px;
        background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 999;
        overflow-y: auto;
        overflow-x: hidden;
        padding-top: 0;
      }

      /* Profil Bölümü */
      .profile {
        padding: 20px;
        margin-bottom: 10px;
        background: rgba(0, 0, 0, 0.1);
      }

      .profile_pic {
        display: inline-block;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-right: 15px;
        overflow: hidden;
        vertical-align: middle;
      }

      .profile_pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .profile_info {
        display: inline-block;
        vertical-align: middle;
      }

      .profile_info h2 {
        font-size: 16px;
        color: #fff;
        margin: 0;
        font-weight: 500;
      }

      .profile_info span {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
      }

      /* Menü Öğeleri */
      .nav.side-menu {
        margin: 0;
        padding: 0;
      }

      .nav.side-menu > li {
        position: relative;
        display: block;
        margin: 3px 10px;
      }

      .nav.side-menu > li > a {
        padding: 12px 15px;
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.85);
        border-radius: 6px;
        transition: all 0.3s ease;
      }

      .nav.side-menu > li > a i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
        font-size: 16px;
      }

      .nav.side-menu > li > a span {
        flex: 1;
      }

      .nav.side-menu > li.active > a,
      .nav.side-menu > li > a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
      }

      /* İçerik Alanı */
      .right_col {
        position: relative;
        margin-left: 250px;
        padding: 20px 30px;
        background: #f8f9fa;
      }

      /* Üst Menü */
      .top_nav {
        position: fixed;
        top: 0;
        right: 0;
        left: 250px;
        height: 60px;
        z-index: 998;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      }

      .nav_menu {
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
      }

      /* Menü Toggle Butonu - Sol Üst Köşe */
      .menu_toggle_container {
        position: fixed;
        top: 0;
        left: 250px;
        z-index: 999;
        padding: 18px 20px;
        background: transparent;
      }

      #menu_toggle {
        background: transparent;
        border: none;
        color: #2c3e50;
        font-size: 20px;
        cursor: pointer;
        padding: 5px;
        transition: all 0.3s ease;
      }

      #menu_toggle:hover {
        color: #3498db;
      }

      /* Kullanıcı Profil Alanı - Sağ Üst Köşe */
      .nav.navbar-nav {
        margin-left: auto;
      }

      .user-profile {
        display: flex;
        align-items: center;
        padding: 8px 15px;
        color: #2c3e50;
        font-weight: 500;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.3s ease;
      }

      .user-profile:hover {
        background: rgba(52, 152, 219, 0.1);
      }

      .user-profile img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
        border: 2px solid #3498db;
      }

      /* Sidebar Logo Alanını Kaldır */
      .nav_title {
        display: none;
      }

      /* Sayfa Başlığı */
      .page-title {
        padding: 0 0 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
      }

      .page-title .title_left h3 {
        margin: 0;
        font-size: 24px;
        color: #2c3e50;
        font-weight: 500;
      }

      /* Alt Menüler */
      .nav.child_menu {
        padding: 5px 0 5px 35px;
      }

      .nav.child_menu > li > a {
        padding: 8px 15px;
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        display: block;
        border-radius: 4px;
      }

      .nav.child_menu > li > a:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.05);
      }

      /* Responsive */
      @media (max-width: 991px) {
        .left_col {
          transform: translateX(-250px);
          transition: transform 0.3s ease;
        }

        .right_col {
          margin-left: 0;
          width: 100%;
          padding-top: 60px !important;
        }

        .menu_toggle_container {
          left: 0;
        }
        .top_nav {
          left: 0;
        }
      }

      /* İçerik alanının üst padding'ini düzelt */
      .right_col {
        padding-top: 60px !important;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .welcome-section {
          padding: 15px 20px;
          margin: -15px -20px 20px -20px;
        }

        .welcome-section h1 {
          font-size: 20px;
        }
      }
    </style>

    <script>
      $(document).ready(function() {
        // Menü toggle işlevi
        $('#menu_toggle').on('click', function(e) {
          e.preventDefault();
          $('body').toggleClass('menu-open');
        });

        // Responsive durumda menüye tıklandığında kapanma
        if($(window).width() < 992) {
          $('.nav.side-menu li a').on('click', function() {
            $('body').removeClass('menu-open');
          });
        }
      });
    </script>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="profile clearfix">
            <span>Beker Software Admin Paneli</span>
              <div class="profile_info">
                <span>Hoşgeldiniz,</span>
                <h2><?php echo $kullanicicek['kullanici_ad']; ?></h2>
              </div>
            </div>

            <br />

            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <ul class="nav side-menu">
                  <li><a href="index.php"><i class="fa fa-home"></i> Ana Sayfa </a></li>

                  <li>
                    <a href="istatistikler.php">
                      <i class="fa fa-chart-bar"></i> İstatistikler
                    </a>
                  </li>

                  <li><a><i class="fa fa-shopping-cart"></i> Alışveriş Yönetimi <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="siparisler.php">Tüm Siparişler</a></li>
                      <li><a href="kampanya-kodlari.php">Kampanya Kodları</a></li>
                      <li><a href="paytr-ayar.php">PayTR Ayarları</a></li>
                      <li><a href="banka-hesaplari.php">Banka Hesapları</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-cogs"></i>Site Ayarları<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="genel-ayar.php">Genel Ayarlar</a></li>
                      <li><a href="iletisim-ayar.php">İletişim Ayarları</a></li>
                      <li><a href="api-ayar.php">Api Ayarları</a></li>
                      <li><a href="sosyal-ayar.php">Sosyal Ayarlar</a></li>
                      <li><a href="mail-ayar.php">Mail Ayarları</a></li>
                      <li><a href="kvkk-gizlilik.php">KVKK ve Gizlilik</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-folder"></i>Kategori Ayarları<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="kategori.php">Kategoriler</a></li>
                      <li><a href="alt-kategori.php">Alt Kategoriler</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-book"></i>Kurs Ayarları<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="kurs.php">Kurslar</a></li>
                      <li><a href="vitrin.php">Vitrin</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-chalkboard-teacher"></i>Eğitmen İşlemleri<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="egitmen.php">Eğitmenler</a></li>
                      <li><a href="egitmen_vitrin.php">Vitrin</a></li>
                      <li><a href="egitmen-basvuru.php">Başvurular</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-copyright"></i>Marka Ayarları<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="marka.php">Markalar</a></li>
                      <li><a href="marka_vitrin.php">Vitrin</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-pencil-alt"></i>Blog Ayarları<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="blog.php">Bloglar</a></li>
                      <li><a href="blog_vitrin.php">Vitrin</a></li>
                      <li><a href="blog_kategori.php">Kategori İşlemleri</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-clipboard"></i>Ana Sayfa İşlemleri<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="anahakkimizda.php">Ana Hakkımızda İşlemleri</a></li>
                      <li><a href="anasayfa_veriler.php">Ana Sayfa Verileri</a></li>
                    </ul>
                  </li>

                  <li><a href="sss.php"><i class="fa fa-question"></i> SSS İşlemleri</a></li>

                  <li><a href="bulten.php"><i class="fa fa-newspaper"></i> Bülten Aboneleri</a></li>

                  <li><a href="iletisimform.php"><i class="fa fa-calendar"></i> İletişim Form İşlemleri</a></li>

                  <li><a href="hakkimizda.php"><i class="fa fa-info"></i> Hakkımızda İşlemleri</a></li>

                  <li><a href="kullanici.php"><i class="fa fa-user"></i> Kullanıcı İşlemleri </a></li>

                  <li><a href="menu.php"><i class="fa fa-list"></i> Menü İşlemleri </a></li>

                  <li><a href="slider.php"><i class="fa fa-image"></i> Slider İşlemleri </a></li>

                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="top_nav">
          <div class="nav_menu">
            <div class="menu_toggle_container">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            
            <ul class="nav navbar-nav">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <img src="<?php echo !empty($adminkullanicicek['kullanici_resim']) ? $adminkullanicicek['kullanici_resim'] : '../../assets/img/default-user.png'; ?>" alt="Kullanıcı Resmi">
                  <?php echo $kullanicicek['kullanici_adsoyad']; ?>
                  <span class="fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                  <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i> Güvenli Çıkış</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>

        
      </div>
    </div>

    <script>
      // Tam ekran fonksiyonu
      function toggleFullScreen() {
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen();
        } else {
          if (document.exitFullscreen) {
            document.exitFullscreen();
          }
        }
      }
    </script>
  </body>
</html>
