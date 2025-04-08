<?php 
include 'header.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$kullanici = $db->prepare("SELECT * FROM kullanici WHERE kullanici_id = ?");
$kullanici->execute([$_SESSION['userkullanici_id']]);
$kullanici_detay = $kullanici->fetch(PDO::FETCH_ASSOC);

// Get user's billing addresses
$adresler = $db->prepare("SELECT * FROM fatura_adresleri WHERE user_id = ? ORDER BY varsayilan DESC");
$adresler->execute([$_SESSION['userkullanici_id']]);
?>

<style>
.profile-page {
    padding-top: 100px;
    padding-bottom: 50px;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.profile-section {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.profile-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.profile-content {
    padding: 20px;
}

.address-card {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    position: relative;
}

.address-card.default {
    border-color: #28a745;
}

.default-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.form-group {
    margin-bottom: 1rem;
}

.profile-image-container {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.profile-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px;
    background: rgba(0,0,0,0.5);
    border-bottom-left-radius: 50%;
    border-bottom-right-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s;
}

.profile-image-container:hover .image-overlay {
    opacity: 1;
}

.btn-link {
    text-decoration: none;
    color: inherit;
}

.btn-link:hover {
    text-decoration: none;
    color: inherit;
}

.profile-header .fa-chevron-down {
    transition: transform 0.3s;
}

.profile-header [aria-expanded="true"] .fa-chevron-down {
    transform: rotate(180deg);
}
</style>

<div class="profile-page">
    <div class="container">
        <div class="row">
            <!-- SOL TARAFTAKİ KULLANICI PANELİ -->
            <div class="col-md-3">
                <div class="profile-section">
                    <div class="profile-header">
                        <h5>Kullanıcı Panelim</h5>
                    </div>
                    <div class="profile-content">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('my-courses.php'); return false;">
                                    Kurslarım
                                </a>
                            </li>
<!-- DEĞİŞCEK SERTİFİKA KISMI YOK -->
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('sertifikalarim.php'); return false;">
                                    Sertifikalarım
                                </a>
                            </li>
                            <!-- ADRES KISMI BOUNSİTETÜ DEKİ GİBİ AMA BİZDEKİ KODA ENTEGRE ET -->
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('adreslerim.php'); return false;">
                                    Adreslerim
                                </a>
                            </li>
                        </ul>

                        <h5 class="mt-4">Sipariş Detayları</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('siparislerim.php'); return false;">
                                    Siparişlerim
                                </a>
                            </li>
                            <!-- KUPON SAYFASI YOK BOUNSİTETÜDEN ALINDI YAPILABİLİR -->
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('kuponlarim.php'); return false;">
                                    Kuponlarım
                                </a>
                            </li>
                        </ul>

                        <h5 class="mt-4">Hesap Ayarları</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('profile.php'); return false;">
                                    Genel Ayarlarım
                                </a>
                            </li>
                            <!-- DROPDOWN DEĞİL SAĞ PANELE GETİR VE FONKSİYONU ÇEK -->
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('sifremi-guncelle.php'); return false;">
                                    Şifremi Güncelle
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link">
                                    Çıkış Yap
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- SAĞ TARAFTAKİ İÇERİK ALANI -->
            <div class="col-md-9">
                <!-- Dinamik içerik yüklenecek alan sağ kısmı etkilenliştir-->
                <div id="content-container">
                    <div class="profile-section">
                        <div class="profile-header">
                            <h4>Profil Bilgileri</h4>
                        </div>
                        <div class="profile-content">
                            <!-- Profil Resmi -->
                            <div class="text-center mb-4">
                                <div class="profile-image-container">
                                    <img src="<?php 
                                        echo $kullanici_detay['kullanici_resim'] 
                                            ? $kullanici_detay['kullanici_resim'] 
                                            : 'dimg/user/default.jpg'; 
                                    ?>" 
                                    class="profile-image" alt="Profil Resmi">
                                    <div class="image-overlay">
                                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#profileImageModal">
                                            <i class="fa fa-camera"></i> Değiştir
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Profil Bilgileri Güncelleme Formu -->
                            <form action="nedmin/netting/islem.php" method="POST">
                                <div class="form-group">
                                    <label>Ad</label>
                                    <input type="text" name="kullanici_ad" class="form-control" 
                                           value="<?php echo $kullanici_detay['kullanici_ad']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Soyad</label>
                                    <input type="text" name="kullanici_soyad" class="form-control" 
                                           value="<?php echo $kullanici_detay['kullanici_soyad']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>E-posta</label>
                                    <input type="email" class="form-control" 
                                           value="<?php echo $kullanici_detay['kullanici_mail']; ?>" readonly>
                                    <small class="text-muted">E-posta adresi değiştirilemez.</small>
                                </div>
                                <div class="form-group">
                                    <label>Telefon</label>
                                    <input type="tel" name="kullanici_gsm" class="form-control" 
                                           pattern="[0-9]*" placeholder="05xxxxxxxxx" maxlength="11"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           value="<?php echo $kullanici_detay['kullanici_tel']; ?>">
                                </div>
                                <button type="submit" name="kullanici_bilgi_guncelle" class="btn btn-primary">
                                    Bilgileri Güncelle
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Image Modal -->
<div class="modal fade" id="profileImageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil Fotoğrafını Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="nedmin/netting/islem.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Yeni Fotoğraf Seç</label>
                        <input type="file" name="kullanici_resim" class="form-control" required accept="image/*">
                        <small class="text-muted">
                            Maksimum dosya boyutu: 1MB<br>
                            İzin verilen formatlar: JPG, PNG, JPEG
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="kullanici_resim_guncelle" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript: Dinamik içerik yüklemesi sağ panel için gerekliş düzenleme istekelrimi burda tıoparlıcam -->
 <!-- HATALI KISIM İNCLUDE DAN DOLAYI EN ÜST NAV BAR GELİYOR ONU DOM LA ÇIKARMAYI DENE -->
<script>
function loadContent(url) {
    // Sağ paneldeki içerik alanını seçiyoruz.
    const container = document.getElementById('content-container');
    
    // Yüklenmekte olduğuna dair mesaj gösterelim
    container.innerHTML = '<p>Yükleniyor...</p>';

    // PHP dosyasından içeriği fetch ile getiriyoruz.
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('İstek başarısız: ' + response.status);
            }
            return response.text();
        })
        .then(html => {
            // Gelen HTML içeriğini sağ paneldeki alana yüklüyoruz.
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('İçerik yüklenirken hata oluştu: ', error);
            container.innerHTML = '<p>İçerik yüklenemedi.</p>';
        });
}
</script>

<!-- Ek JavaScript Fonksiyonları (adres düzenleme, şifre gösterme) -->
<script>
function editAddress(addressId) {
    fetch('nedmin/netting/islem.php?adres_getir=' + addressId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_adres_id').value = data.fatura_adres_id;
            document.getElementById('edit_ad_soyad').value = data.ad_soyad;
            document.getElementById('edit_telefon').value = data.telefon;
            document.getElementById('edit_tc_no').value = data.tc_no;
            document.getElementById('edit_adres').value = data.adres;
            document.getElementById('edit_il').value = data.il;
            document.getElementById('edit_ilce').value = data.ilce;
            document.getElementById('edit_posta_kodu').value = data.posta_kodu;

            var editModal = new bootstrap.Modal(document.getElementById('editAddressModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Adres bilgileri alınırken bir hata oluştu.');
        });
}

function makeDefault(addressId) {
    if(confirm('Bu adresi varsayılan olarak ayarlamak istediğinize emin misiniz?')) {
        window.location.href = 'nedmin/netting/islem.php?adres_varsayilan=' + addressId;
    }
}

function deleteAddress(addressId) {
    if(confirm('Bu adresi silmek istediğinize emin misiniz?')) {
        window.location.href = 'nedmin/netting/islem.php?adres_sil=' + addressId;
    }
}

function togglePasswordVisibility(inputId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(inputId + '_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include 'footer.php'; ?>
