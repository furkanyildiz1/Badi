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

            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="forms-container">
                <!-- Öğrenci Formu -->
                <form class="login-form form-section active" id="studentForm" action="nedmin/netting/islem.php" method="POST">
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
                        <input type="tel" id="phone" name="phone" placeholder="05XX XXX XX XX" required pattern="[0-9]*" 
                               inputmode="numeric" onkeypress="return isNumberKey(event)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Şifre</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <i class="fas fa-eye toggle-password" data-target="password"></i>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Şifre Tekrar</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" required>
                            <i class="fas fa-eye toggle-password" data-target="password_confirm"></i>
                        </div>
                    </div>

                    <div class="terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">
                            <a href="#" id="termsBtn">Kullanım Koşulları</a>'nı ve <a href="#" id="privacyBtn">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.
                        </label>
                    </div>

                    <button type="submit" name="kullanicikaydet" class="login-btn">
                        <i class="fas fa-user-plus"></i>
                        Kayıt Ol
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

            <a href="google-auth.php" class="google-btn">
                <i class="fab fa-google" style="color: #4285f4; font-size: 18px;"></i>
                Google ile Kayıt Ol
            </a>
        </div>
    </div>

    <script>
    // Sayfa tamamen yüklendikten sonra tüm event listener'ları ekle
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });

        // Modal açma işlemleri
        document.getElementById('termsBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('termsModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Arka planın kaydırılmasını engelle
        });

        document.getElementById('privacyBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('privacyModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Arka planın kaydırılmasını engelle
        });

        // Modal kapatma fonksiyonu
        function closeModals() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
            document.body.style.overflow = ''; // Arka plan kaydırmayı tekrar etkinleştir
        }

        // Tüm kapatma butonlarına event listener ekle
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', closeModals);
        });

        // Modal arka planına tıklamayı dinle
        document.querySelectorAll('.modal-bg').forEach(bg => {
            bg.addEventListener('click', closeModals);
        });

        // Modal içeriğine tıklandığında kapanmasını engelle
        document.querySelectorAll('.modal-content').forEach(content => {
            content.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // ESC tuşunu dinle
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModals();
            }
        });
    });

    // Diğer JavaScript fonksiyonlar
    function googleRegister() {
        alert('Google ile kayıt özelliği yakında eklenecek!');
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    // Additional validation for paste events
    document.getElementById('phone').addEventListener('paste', function(e) {
        // Get pasted data via clipboard API
        let pastedData = (e.clipboardData || window.clipboardData).getData('text');
        if (pastedData.match(/[^0-9]/)) {
            // If pasted data contains non-numeric characters, process it
            e.preventDefault();
            this.value = pastedData.replace(/[^0-9]/g, '');
        }
    });
    </script>
    
    <style>
        .form-group {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 40px;
            cursor: pointer;
            color: #888;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
        }

        .modal-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-container {
            position: relative;
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-content {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: #e74c3c;
        }

        .modal-body {
            line-height: 1.6;
            color: #555;
        }

        .modal-body h4 {
            margin-top: 25px;
            margin-bottom: 10px;
            color: #333;
        }

        .modal-body ul {
            padding-left: 20px;
        }

        .modal-body li {
            margin-bottom: 8px;
        }

        .modal-footer {
            margin-top: 25px;
            text-align: right;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .modal-btn {
            background-color: #0093c4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .modal-btn:hover {
            background-color: #007ba3;
        }

        @media (max-width: 768px) {
            .modal-container {
                width: 95%;
                margin: 30px auto;
            }
            
            .modal-content {
                padding: 15px;
            }
        }
    </style>

    <!-- Kullanım Koşulları Modal -->
    <div id="termsModal" class="modal">
        <div class="modal-bg"></div>
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Kullanım Koşulları</h3>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Aşağıdaki kullanım koşulları, Badi Akademi web sitesi ve hizmetlerini kullanımınızı düzenlemektedir. Siteyi kullanarak bu koşulları kabul etmiş sayılırsınız.</p>
                    
                    <h4>1. Hesap Oluşturma ve Güvenlik</h4>
                    <p>Badi Akademi'de hesap oluşturduğunuzda:</p>
                    <ul>
                        <li>Doğru, eksiksiz ve güncel bilgiler sağlamanız gerekmektedir.</li>
                        <li>Şifrenizi güvenli tutmak ve hesabınızla ilgili tüm aktivitelerden sorumlu olmak sizin sorumluluğunuzdadır.</li>
                        <li>13 yaşından küçükseniz, ebeveyn veya vasi izni olmadan hesap oluşturamazsınız.</li>
                    </ul>

                    <h4>2. Kullanım Kuralları</h4>
                    <p>Badi Akademi hizmetlerini kullanırken:</p>
                    <ul>
                        <li>Türkiye Cumhuriyeti yasalarına ve uluslararası yasalara uygun davranmalısınız.</li>
                        <li>Diğer kullanıcıların haklarına saygı göstermelisiniz.</li>
                        <li>Platform üzerinde yayınlanan içerikleri izinsiz kopyalayamaz, çoğaltamaz veya dağıtamazsınız.</li>
                        <li>Spam, aldatıcı veya kötü niyetli içerik paylaşamazsınız.</li>
                    </ul>

                    <h4>3. Fikri Mülkiyet Hakları</h4>
                    <p>Badi Akademi'de yayınlanan tüm içerikler (kurslar, videolar, makaleler, görseller vb.) fikri mülkiyet hakları ile korunmaktadır. Bu içerikleri kişisel öğrenim amacı dışında kullanmak için yazılı izin almanız gerekmektedir.</p>

                    <h4>4. Ödeme ve İade Politikası</h4>
                    <p>Ücretli kurslar için:</p>
                    <ul>
                        <li>Ödemeler güvenli ödeme sistemleri üzerinden yapılmaktadır.</li>
                        <li>Satın alınan kurslar için ilk 14 gün içinde, kursun %30'undan fazlasını tamamlamadıysanız iade talep edebilirsiniz.</li>
                        <li>İade talepleriniz 5 iş günü içerisinde değerlendirilir.</li>
                    </ul>

                    <h4>5. Değişiklikler</h4>
                    <p>Badi Akademi, bu kullanım koşullarını önceden bildirmeksizin değiştirme hakkını saklı tutar. Değişiklikler sitemizde yayınlandığı tarihten itibaren geçerli olacaktır.</p>

                    <h4>6. İletişim</h4>
                    <p>Kullanım koşulları hakkında sorularınız için <strong>info@badiakademi.com</strong> adresine e-posta gönderebilirsiniz.</p>
                </div>
                <div class="modal-footer">
                    <button class="modal-btn close-modal">Anladım</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gizlilik Politikası Modal -->
    <div id="privacyModal" class="modal">
        <div class="modal-bg"></div>
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Gizlilik Politikası</h3>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Bu gizlilik politikası, Badi Akademi'nin kişisel verilerinizi nasıl topladığını, kullandığını ve koruduğunu açıklamaktadır.</p>
                    
                    <h4>1. Toplanan Bilgiler</h4>
                    <p>Badi Akademi aşağıdaki bilgileri toplayabilir:</p>
                    <ul>
                        <li><strong>Kişisel Bilgiler:</strong> Ad, soyad, e-posta adresi, telefon numarası gibi hesap oluşturma sırasında verdiğiniz bilgiler.</li>
                        <li><strong>Ödeme Bilgileri:</strong> Kredi kartı bilgileri (bu bilgiler güvenli ödeme sistemlerinde şifreli olarak saklanır).</li>
                        <li><strong>Kullanım Verileri:</strong> İzlediğiniz kurslar, tamamladığınız ödevler, platformdaki aktiviteleriniz.</li>
                        <li><strong>Teknik Veriler:</strong> IP adresi, tarayıcı türü, ziyaret saatleri, görüntülenen sayfalar.</li>
                    </ul>

                    <h4>2. Bilgilerin Kullanımı</h4>
                    <p>Topladığımız bilgileri şu amaçlarla kullanırız:</p>
                    <ul>
                        <li>Hesabınızı yönetmek ve size hizmet sağlamak</li>
                        <li>Öğrenim deneyiminizi kişiselleştirmek</li>
                        <li>Platform güvenliğini sağlamak</li>
                        <li>Yasal yükümlülükleri yerine getirmek</li>
                        <li>İlginizi çekebilecek kurs ve etkinlikler hakkında sizi bilgilendirmek (izin vermeniz halinde)</li>
                    </ul>

                    <h4>3. Bilgilerin Paylaşımı</h4>
                    <p>Kişisel bilgileriniz aşağıdaki durumlarda üçüncü taraflarla paylaşılabilir:</p>
                    <ul>
                        <li>Yasal bir zorunluluk olduğunda (mahkeme kararı vb.)</li>
                        <li>Hizmet sağlayıcılarımızla (ödeme işlemcileri, hosting sağlayıcıları vb.)</li>
                        <li>Açık izniniz olduğunda</li>
                    </ul>

                    <h4>4. Veri Güvenliği</h4>
                    <p>Kişisel verilerinizi korumak için endüstri standardı güvenlik önlemleri uyguluyoruz. SSL şifreleme, güvenli veri depolama ve düzenli güvenlik denetimleri bu önlemler arasındadır.</p>

                    <h4>5. Çerezler</h4>
                    <p>Badi Akademi, kullanıcı deneyimini iyileştirmek için çerezler kullanmaktadır. Tarayıcı ayarlarınızdan çerezleri devre dışı bırakabilirsiniz, ancak bu durumda sitenin bazı özellikleri düzgün çalışmayabilir.</p>

                    <h4>6. Haklarınız</h4>
                    <p>KVKK kapsamında aşağıdaki haklara sahipsiniz:</p>
                    <ul>
                        <li>Verilerinizin işlenip işlenmediğini öğrenme</li>
                        <li>Verilerinize erişim ve düzeltme talep etme</li>
                        <li>Verilerinizin silinmesini talep etme</li>
                        <li>Verilerinizin işlenmesine itiraz etme</li>
                    </ul>

                    <h4>7. İletişim</h4>
                    <p>Gizlilik politikası hakkında sorularınız için <strong>privacy@badiakademi.com</strong> adresine e-posta gönderebilirsiniz.</p>
                </div>
                <div class="modal-footer">
                    <button class="modal-btn close-modal">Anladım</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 