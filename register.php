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
    <title>Kayıt Ol - Badi Akademi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-style.css">
</head>
<body>
    <div class="animated-background"></div>
    <div class="login-container register-container">
        <div class="login-box">
            <div class="login-header">
                <img src="<?php echo $ayarcek['ayar_logo'] ?>" alt="Badi Akademi">
                <h2>Kayıt Ol</h2>
                <p>Hemen ücretsiz hesap oluşturun</p>
            </div>

            <!-- Slider ekliyoruz -->
            <div class="register-type">
                <div class="register-type-option active" data-type="student">
                    <i class="fas fa-user-graduate"></i> Öğrenci
                </div>
                <div class="register-type-option" data-type="teacher">
                    <i class="fas fa-chalkboard-teacher"></i> Eğitmen
                </div>
                <div class="slider"></div>
            </div>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="forms-container">
                <!-- Öğrenci Formu -->
                <form class="login-form form-section" id="studentForm" action="nedmin/netting/islem.php" method="POST">
                    <input type="hidden" name="user_type" value="student">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname">Ad</label>
                            <i class="far fa-user"></i>
                            <input type="text" id="firstname" name="firstname" placeholder="Adınız" required>
                        </div>

                        <div class="form-group">
                            <label for="lastname">Soyad</label>
                            <i class="far fa-user"></i>
                            <input type="text" id="lastname" name="lastname" placeholder="Soyadınız" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-posta Adresi</label>
                        <i class="far fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon Numarası</label>
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="phone" name="phone" placeholder="05XX XXX XX XX" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Şifre</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Şifre Tekrar</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Öğrenci için ek alanlar -->

                    <div class="terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">
                            <a href="#">Kullanım Koşulları</a>'nı ve <a href="#">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.
                        </label>
                    </div>

                    <button type="submit" name="kullanicikaydet" class="login-btn">
                        <i class="fas fa-user-plus"></i>
                        Öğrenci Olarak Kayıt Ol
                    </button>
                </form>

                <!-- Eğitmen Formu -->
                <form class="login-form form-section" id="teacherForm" action="nedmin/netting/islem.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="user_type" value="teacher">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname_teacher">Ad</label>
                            <i class="far fa-user"></i>
                            <input type="text" id="firstname_teacher" name="firstname" placeholder="Adınız" required>
                        </div>

                        <div class="form-group">
                            <label for="lastname_teacher">Soyad</label>
                            <i class="far fa-user"></i>
                            <input type="text" id="lastname_teacher" name="lastname" placeholder="Soyadınız" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email_teacher">E-posta Adresi</label>
                        <i class="far fa-envelope"></i>
                        <input type="email" id="email_teacher" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_teacher">Telefon Numarası</label>
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="phone_teacher" name="phone" placeholder="05XX XXX XX XX" required>
                    </div>

                    <!-- Eğitmen için ek alanlar -->
                    <div class="form-group">
                        <label for="expertise">Uzmanlık Alanı</label>
                        <i class="fas fa-graduation-cap"></i>
                        <input type="text" id="expertise" name="expertise" placeholder="Uzmanlık Alanınız" required>
                    </div>

                    <div class="form-group">
                        <label for="experience">Kendinizden Biraz Bahsediniz</label>
                        <i class="fas fa-briefcase"></i>
                        <input type="text" id="experience" name="experience" placeholder="..." required>
                    </div>

                    <!-- CV Yükleme alanını experience input'undan önce ekleyelim -->
                    <div class="form-group">
                        <label for="cv">CV/Özgeçmiş (PDF)</label>
                        <i class="fas fa-file-pdf"></i>
                        <input type="file" 
                               id="cv" 
                               name="cv" 
                               accept=".pdf"
                               required 
                               class="file-input"
                               onchange="validateFileSize(this)">
                        <small class="file-info">Maksimum dosya boyutu: 2MB</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password_teacher">Şifre</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_teacher" name="password" placeholder="••••••••" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm_teacher">Şifre Tekrar</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirm_teacher" name="password_confirm" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="terms">
                        <input type="checkbox" id="terms_teacher" name="terms" required>
                        <label for="terms_teacher">
                            <a href="#">Kullanım Koşulları</a>'nı ve <a href="#">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.
                        </label>
                    </div>

                    <button type="submit" name="egitmenrekaydet" class="login-btn">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Eğitmen Olarak Kayıt Ol
                    </button>
                </form>
            </div>

            <div class="login-footer">
                <p>Zaten hesabınız var mı? <a href="login.php">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </a></p>
            </div>

            <div class="divider">
                <span>veya</span>
            </div>

            <button type="button" class="google-btn" onclick="googleRegister()">
                <i class="fab fa-google" style="color: #4285f4; font-size: 18px;"></i>
                Google ile Kayıt Ol
            </button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const options = document.querySelectorAll('.register-type-option');
        const slider = document.querySelector('.slider');
        const studentForm = document.getElementById('studentForm');
        const teacherForm = document.getElementById('teacherForm');
        const formsContainer = document.querySelector('.forms-container');

        // İlk form görünür olsun
        studentForm.classList.add('active');

        options.forEach(option => {
            option.addEventListener('click', function() {
                // Aktif class'ları kaldır
                options.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                // Her iki formu da gizle ve active/passive classlarını temizle
                studentForm.classList.remove('active', 'passive');
                teacherForm.classList.remove('active', 'passive');

                if (this.dataset.type === 'teacher') {
                    slider.classList.add('slide-right');
                    teacherForm.classList.add('active');
                    studentForm.classList.add('passive');
                } else {
                    slider.classList.remove('slide-right');
                    studentForm.classList.add('active');
                    teacherForm.classList.add('passive');
                }

                // Container yüksekliğini güncelle
                const activeForm = this.dataset.type === 'teacher' ? teacherForm : studentForm;
                formsContainer.style.height = `${activeForm.offsetHeight}px`;
            });
        });

        // Sayfa yüklendiğinde container yüksekliğini ayarla
        formsContainer.style.height = `${studentForm.offsetHeight}px`;
    });

    function googleRegister() {
        alert('Google ile kayıt özelliği yakında eklenecek!');
    }

    function validateFileSize(input) {
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        const fileSize = input.files[0]?.size || 0;
        
        if (fileSize > maxSize) {
            alert('Dosya boyutu 2MB\'dan küçük olmalıdır!');
            input.value = ''; // Dosya seçimini temizle
            return false;
        }
        
        // Seçilen dosya adını göster
        const fileName = input.files[0]?.name || 'Dosya seçilmedi';
        input.parentElement.querySelector('.file-info').textContent = fileName;
        return true;
    }
    </script>

    <style>
    /* CV input için özel stiller */
    .file-input {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
        background: #fff;
    }

    .file-input::-webkit-file-upload-button {
        background: #007bff;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    .file-info {
        display: block;
        margin-top: 5px;
        color: #666;
        font-size: 0.8em;
    }
    </style>
</body>
</html> 