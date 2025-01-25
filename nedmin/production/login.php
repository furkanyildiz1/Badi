<?php 
error_reporting(0);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>BekerSOFTWARE CMS Yönetim Paneli</title>

  <!-- Bootstrap -->
  <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <!-- NProgress -->
  <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
  <!-- Animate.css -->
  <link href="../vendors/animate.css/animate.min.css" rel="stylesheet">

  <!-- Custom Theme Style -->
  <link href="../build/css/custom.min.css" rel="stylesheet">
</head>

<body class="login">
  <div class="login_wrapper">
    <div class="animate form login_form">
      <section class="login_content">
        <form action="../netting/islem.php" method="POST">
          <div class="login-header">
            <i class="fa fa-code fa-3x"></i>
            <h1>Yönetim Paneli</h1>
          </div>
            
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
              <input type="text" name="kullanici_mail" class="form-control" placeholder="E-posta Adresiniz" required />
            </div>
          </div>
            
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-lock"></i></span>
              <input type="password" name="kullanici_password" class="form-control" placeholder="Şifreniz" required />
            </div>
          </div>
            
          <div>
            <button type="submit" name="admingiris" class="btn btn-primary submit">
              <i class="fa fa-sign-in"></i> Giriş Yap
            </button>
          </div>

          <?php if(isset($_GET['durum'])): ?>
            <div class="alert <?php echo $_GET['durum'] == 'no' ? 'alert-danger' : 'alert-success'; ?> mt-3">
              <i class="fa <?php echo $_GET['durum'] == 'no' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
              <?php 
                if ($_GET['durum']=="no") {
                  echo "Kullanıcı Bulunamadı...";
                } elseif ($_GET['durum']=="exit") {
                  echo "Başarıyla Çıkış Yaptınız.";
                }
              ?>
            </div>
          <?php endif; ?>

          <div class="separator">
            <div class="login-footer">
              <h1>
                <i class="fa fa-code"></i> 
                Beker SOFTWARE
              </h1>
              <p>©2024 BekerSOFTWARE Admin Panel</p>
            </div>
          </div>
        </form>
      </section>
    </div>
  </div>
</body>
</html>
