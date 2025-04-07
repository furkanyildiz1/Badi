<?php
session_start();
include 'nedmin/netting/baglan.php';

$ayarsor = $db->prepare("SELECT * FROM ayar WHERE ayar_id=:ayar_id");
$ayarsor->execute([
    'ayar_id' => 0
]);
$ayarcek = $ayarsor->fetch(PDO::FETCH_ASSOC);

// Token doğrulama
$valid_token = false;
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    
    // Tokenı kontrol et
    $tokenSor = $db->prepare("SELECT * FROM password_reset WHERE token = :token AND email = :email AND token_expiry > NOW()");
    $tokenSor->execute([
        'token' => $token,
        'email' => $email
    ]);
    
    if ($tokenSor->rowCount() > 0) {
        $valid_token = true;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama - Badi Akademi</title>
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
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 40px;
            cursor: pointer;
            color: #888;
        }
        
        .form-group {
            position: relative;
        }
        
        .invalid-token {
            text-align: center;
            padding: 30px;
        }
        
        .invalid-token i {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        .invalid-token h3 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        
        .invalid-token p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="animated-background"></div>
    <div class="login-container">
        <div class="login-box">
            <?php if($valid_token): ?>
                <div class="login-header">
                    <img src="<?php echo $ayarcek['ayar_logo'] ?>" alt="Badi Akademi">
                    <h2>Şifre Sıfırlama</h2>
                    <p>Lütfen yeni şifrenizi belirleyin</p>
                </div>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form class="login-form" action="nedmin/netting/islem.php" method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="password">Yeni Şifre</label>
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Yeni şifrenizi girin" required minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password')"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Şifre Tekrar</label>
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Şifrenizi tekrar girin" required minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password_confirm')"></i>
                    </div>

                    <button type="submit" name="reset_password" class="login-btn">
                        <i class="fas fa-key"></i>
                        Şifremi Güncelle
                    </button>
                </form>

                <div class="login-footer">
                    <p>Şifrenizi hatırladınız mı? <a href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        Giriş Yap
                    </a></p>
                </div>
            <?php else: ?>
                <div class="invalid-token">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Geçersiz veya Süresi Dolmuş Bağlantı</h3>
                    <p>Şifre sıfırlama bağlantınız geçersiz veya süresi dolmuş. Lütfen yeni bir şifre sıfırlama talebi oluşturun.</p>
                    <a href="forgot-password.php" class="login-btn">
                        <i class="fas fa-redo"></i>
                        Yeniden Şifre Sıfırlama
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = passwordInput.nextElementSibling;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 