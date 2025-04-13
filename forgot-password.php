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
    <title>Şifremi Unuttum - Badi Akademi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-style.css">
    <link rel="icon" type="image/png" href="<?php echo $ayarcek['ayar_favicon']; ?>">

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
                <h2>Şifremi Unuttum</h2>
                <p>Şifrenizi sıfırlamak için e-posta adresinizi girin</p>
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
                <?php if($_GET['durum'] == "mailbulunamadi"): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Bu e-posta adresine ait bir hesap bulunamadı.
                    </div>
                <?php elseif($_GET['durum'] == "mailgonderildi"): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Şifre sıfırlama bağlantısı e-posta adresinize gönderildi. Lütfen e-posta kutunuzu kontrol edin.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form class="login-form" action="nedmin/netting/islem.php" method="POST">
                <div class="form-group">
                    <label for="email">E-posta Adresi</label>
                    <i class="far fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="E-posta adresinizi girin" required>
                </div>

                <button type="submit" name="forgot_password" class="login-btn">
                    <i class="fas fa-paper-plane"></i>
                    Şifre Sıfırlama Bağlantısı Gönder
                </button>
            </form>

            <div class="login-footer">
                <p>Şifrenizi hatırladınız mı? <a href="login.php">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </a></p>
            </div>
        </div>
    </div>
</body>
</html> 