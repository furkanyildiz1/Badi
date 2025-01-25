<?php
session_start();
include 'nedmin/netting/baglan.php';

$ayarsor = $db->prepare("SELECT * FROM ayar WHERE ayar_id=:ayar_id");
$ayarsor->execute([
    'ayar_id' => 0
]);
$ayarcek = $ayarsor->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Badi Akademi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-style.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: left;
        }

        .alert i {
            margin-right: 8px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
</head>
<body>
    <div class="animated-background"></div>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="<?php echo $ayarcek['ayar_logo'] ?>" alt="Badi Akademi">
                <h2>Hoş Geldiniz</h2>
                <p>Hesabınıza giriş yapın</p>
            </div>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['durum'])): ?>
                <?php if($_GET['durum'] == "onaybekliyor"): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i>
                        Hesabınız henüz onaylanmamış. Başvurunuz incelendikten sonra giriş yapabilirsiniz.
                    </div>
                <?php elseif($_GET['durum'] == "pasifhesap"): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-ban"></i>
                        Hesabınız pasif durumda. Lütfen yönetici ile iletişime geçin.
                    </div>
                <?php elseif($_GET['durum'] == "basarisizgiris"): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        E-posta veya şifre hatalı!
                    </div>
                <?php elseif($_GET['durum'] == "kayitbasarili"): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Kayıt işlemi başarılı! Giriş yapabilirsiniz.
                    </div>
                <?php elseif($_GET['durum'] == "egitmenonaybekliyor"): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Eğitmen başvurunuz alınmıştır. Başvurunuz onaylandıktan sonra giriş yapabilirsiniz.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form class="login-form" action="nedmin/netting/islem.php" method="POST">
                <div class="form-group">
                    <label for="email">E-posta Adresi</label>
                    <i class="far fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="ornek@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Şifre</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <a href="forgot-password.php" class="forgot-password">Şifremi Unuttum</a>
                </div>

                <button type="submit" name="kullanicigiris" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </button>
            </form>

            <div class="login-footer">
                <p>Hesabınız yok mu? <a href="register.php">
                    <i class="fas fa-user-plus"></i>
                    Kayıt Ol
                </a></p>
            </div>

            <div class="divider">
                <span>veya</span>
            </div>

            <button type="button" class="google-btn" onclick="googleLogin()">
                <i class="fab fa-google" style="color: #4285f4; font-size: 18px;"></i>
                Google ile Giriş Yap
            </button>
        </div>
    </div>

    <script>
        function googleLogin() {
            alert('Google ile giriş yapma özelliği yakında eklenecek!');
        }
    </script>
</body>
</html> 