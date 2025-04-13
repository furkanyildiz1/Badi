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

// Mevcut adresleri çekiyoruz
$adresSorgu = $db->prepare("SELECT * FROM fatura_adresleri WHERE user_id = ? ORDER BY varsayilan DESC, fatura_adres_id ASC");
$adresSorgu->execute([$_SESSION['userkullanici_id']]);
$adresler = $adresSorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Genel stil ayarları */
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

/* Sol panel menü stilleri */
.nav-link {
    cursor: pointer;
}

/* İki ayrı içerik kapsayıcısı */
#inline-container,
#external-container {
    padding: 0;
}
#external-container {
    display: none;
}

/* Gizleme sınıfı */
.hide {
    display: none;
}

/* Adreslerim alanı */
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

.add-address-button {
    margin-top: 20px;
    text-align: right;
}

/* Profil resim stilleri */
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

/* Şifre güncelle formu stili */
.password-form .form-group {
    margin-bottom: 1rem;
}
</style>

<div class="profile-page">
    <div class="container">
        <div class="row">
            <!-- Sol Panel: Kullanıcı Paneli -->
            <div class="col-md-3">
                <div class="profile-section">
                    <div class="profile-header">
                        <h5>Kullanıcı Panelim</h5>
                    </div>
                    <div class="profile-content">
                        <ul class="nav flex-column">
                            <!-- Harici içerik yükleyenler -->
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('my-courses.php'); return false;">
                                    Kurslarım
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('sertifikalarim.php'); return false;">
                                    Sertifikalarım
                                </a>
                            </li>
                            <!-- Inline (sayfa içi) içerik yükleyenler -->
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="showInlineSection('adreslerim'); return false;">
                                    Adreslerim
                                </a>
                            </li>
                        </ul>
                        <h5 class="mt-3">Sipariş Detayları</h5>
                        <ul class="nav flex-column">
                        <li class="nav-item">
                                <a href="#" class="nav-link" onclick="loadContent('siparislerim.php'); return false;">
                                    Siparişlerim
                                </a>
                            </li>
                        </ul>
                        
                        <h5 class="mt-4">Hesap Ayarları</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="showInlineSection('profile_info'); return false;">
                                    Genel Ayarlarım
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="showInlineSection('sifre_guncelle'); return false;">
                                    Şifremi Güncelle
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link">Çıkış Yap</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: İçerik Alanı -->
            <div class="col-md-9">
                <div id="content-container">
                    <!-- Inline içerikler için kapsayıcı -->
                    <div id="inline-container">
                        <!-- Profil Bilgileri Bölümü -->
                        <div id="profile_info_section">
                            <div class="profile-section">
                                <div class="profile-header">
                                    <h4>Profil Bilgileri</h4>
                                </div>
                                <div class="profile-content">
                                    <!-- Profil Resmi -->
                                    <div class="text-center mb-4">
                                        <div class="profile-image-container">
                                            <img src="<?php echo $kullanici_detay['kullanici_resim'] ? $kullanici_detay['kullanici_resim'] : 'dimg/user/default.jpg'; ?>" class="profile-image" alt="Profil Resmi">
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
                                            <input type="text" name="kullanici_ad" class="form-control" value="<?php echo $kullanici_detay['kullanici_ad']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Soyad</label>
                                            <input type="text" name="kullanici_soyad" class="form-control" value="<?php echo $kullanici_detay['kullanici_soyad']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>E-posta</label>
                                            <input type="email" class="form-control" value="<?php echo $kullanici_detay['kullanici_mail']; ?>" readonly>
                                            <small class="text-muted">E-posta adresi değiştirilemez.</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Telefon</label>
                                            <input type="tel" name="kullanici_gsm" class="form-control" pattern="[0-9]*" placeholder="05xxxxxxxxx" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="<?php echo $kullanici_detay['kullanici_tel']; ?>">
                                        </div>
                                        <button type="submit" name="kullanici_bilgi_guncelle" class="btn btn-primary">
                                            Bilgileri Güncelle
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Adreslerim Bölümü -->
                        <div id="adreslerim_section" class="hide">
                            <div class="profile-section">
                                <div class="profile-header">
                                    <h4>Adreslerim</h4>
                                </div>
                                <div class="profile-content">
                                    <?php if(count($adresler) > 0): ?>
                                        <?php foreach($adresler as $adres): ?>
                                            <div class="address-card <?php echo $adres['varsayilan'] ? 'default' : '' ?>">
                                                <strong><?php echo htmlspecialchars($adres['ad_soyad']); ?></strong><br>
                                                <?php echo htmlspecialchars($adres['adres']); ?><br>
                                                <?php echo htmlspecialchars($adres['ilce'] . " / " . $adres['il']); ?><br>
                                                Tel: <?php echo htmlspecialchars($adres['telefon']); ?><br>
                                                <?php if($adres['varsayilan'] == 1): ?>
                                                    <div class="default-badge">Varsayılan</div>
                                                <?php endif; ?>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-info" onclick="editAddress(<?php echo $adres['fatura_adres_id']; ?>)">Düzenle</button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteAddress(<?php echo $adres['fatura_adres_id']; ?>)">Sil</button>
                                                    <?php if(!$adres['varsayilan']): ?>
                                                        <button class="btn btn-sm btn-primary" onclick="makeDefault(<?php echo $adres['fatura_adres_id']; ?>)">Varsayılan Yap</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>Henüz adres eklenmemiş.</p>
                                    <?php endif; ?>
                                    <div class="add-address-button">
                                        <button class="btn btn-primary" onclick="showAddAddressModal()">+ Yeni Adres Ekle</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Şifremi Güncelle Bölümü -->
                        <div id="sifre_guncelle_section" class="hide">
                            <div class="profile-section password-form">
                                <div class="profile-header">
                                    <h4>Şifremi Güncelle</h4>
                                </div>
                                <div class="profile-content">
                                    <form action="nedmin/netting/islem.php" method="POST" onsubmit="return validatePasswordUpdate();">
                                        <div class="form-group mb-3">
                                            <label for="eski_sifre">Mevcut Şifre</label>
                                            <input type="password" class="form-control" id="eski_sifre" name="eski_sifre" placeholder="Mevcut Şifreniz" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="yeni_sifre">Yeni Şifre</label>
                                            <input type="password" class="form-control" id="yeni_sifre" name="yeni_sifre" placeholder="Yeni Şifreniz" required onkeyup="calcPasswordStrength(this.value)">
                                            <small class="text-muted">Şifre Güvenlik Seviyesi: <span id="password_strength_level">-</span></small>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="yeni_sifre_tekrar">Yeni Şifre Tekrar</label>
                                            <input type="password" class="form-control" id="yeni_sifre_tekrar" name="yeni_sifre_tekrar" placeholder="Yeni Şifrenizi Tekrar Giriniz" required>
                                        </div>
                                        <button type="submit" name="kullanici_sifre_guncelle" class="btn btn-primary">
                                            Şifremi Güncelle
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Harici içeriklerin yükleneceği kapsayıcı -->
                    <div id="external-container"></div>
                </div> <!-- #content-container sonu -->
            </div> <!-- col-md-9 sonu -->
        </div> <!-- row sonu -->
    </div> <!-- container sonu -->
</div>

<!-- Modallar -->

<!-- Profil Resim Güncelleme Modalı -->
<div class="modal fade" id="profileImageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil Fotoğrafını Güncelle</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button>
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

<!-- Yeni Adres Ekle Modalı -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="nedmin/netting/islem.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">Yeni Adres Ekle</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Ad Soyad</label>
                        <input type="text" name="ad_soyad" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Telefon</label>
                        <input type="text" name="telefon" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>T.C. Kimlik No</label>
                        <input type="text" name="tc_no" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Adres</label>
                        <textarea name="adres" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>İl</label>
                        <input type="text" name="il" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>İlçe</label>
                        <input type="text" name="ilce" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Posta Kodu</label>
                        <input type="text" name="posta_kodu" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="varsayilan" name="varsayilan">
                            <label class="form-check-label" for="varsayilan">
                                Varsayılan Olarak Ayarla
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" name="yeni_adres_ekle" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adres Düzenleme Modalı -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="nedmin/netting/islem.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">Adresi Düzenle</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="fatura_adres_id" id="edit_adres_id">
                    <div class="form-group mb-3">
                        <label>Ad Soyad</label>
                        <input type="text" name="edit_ad_soyad" class="form-control" id="edit_ad_soyad" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Telefon</label>
                        <input type="text" name="edit_telefon" class="form-control" id="edit_telefon" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>T.C. Kimlik No</label>
                        <input type="text" name="edit_tc_no" class="form-control" id="edit_tc_no">
                    </div>
                    <div class="form-group mb-3">
                        <label>Adres</label>
                        <textarea name="edit_adres" class="form-control" id="edit_adres" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>İl</label>
                        <input type="text" name="edit_il" class="form-control" id="edit_il" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>İlçe</label>
                        <input type="text" name="edit_ilce" class="form-control" id="edit_ilce" required>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="edit_varsayilan" name="edit_varsayilan">
                            <label class="form-check-label" for="edit_varsayilan">
                                Varsayılan Olarak Ayarla
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" name="fatura_adres_guncelle" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Fonksiyonları -->
<script>
// Harici içerik yükleme fonksiyonu: loadContent() çağrıldığında, inline-container gizlenip external-container içerisine içerik yüklenir.
function loadContent(url) {
    document.getElementById('inline-container').style.display = 'none';
    document.getElementById('external-container').style.display = 'block';
    const container = document.getElementById('external-container');
    container.innerHTML = '<p>Yükleniyor...</p>';
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('İstek başarısız: ' + response.status);
            }
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('İçerik yüklenirken hata:', error);
            container.innerHTML = '<p>İçerik yüklenemedi.</p>';
        });
}

// Inline içerik bölümleri arasında geçiş: profile_info, adreslerim, sifre_guncelle
function showInlineSection(section) {
    // Harici kapsayıcıyı gizle
    document.getElementById('external-container').style.display = 'none';
    // Inline kapsayıcısını göster
    document.getElementById('inline-container').style.display = 'block';
    // Tüm inline bölümleri gizle
    document.getElementById('profile_info_section').style.display = 'none';
    document.getElementById('adreslerim_section').style.display = 'none';
    document.getElementById('sifre_guncelle_section').style.display = 'none';
    
    if (section === 'profile_info') {
        document.getElementById('profile_info_section').style.display = 'block';
    } else if (section === 'adreslerim') {
        document.getElementById('adreslerim_section').style.display = 'block';
    } else if (section === 'sifre_guncelle') {
        document.getElementById('sifre_guncelle_section').style.display = 'block';
    }
}

// Şifre güç kontrolü örneği
function calcPasswordStrength(password) {
    let strengthText = "Çok Zayıf";
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    switch (strength) {
        case 0:
        case 1:
            strengthText = "Çok Zayıf";
            break;
        case 2:
            strengthText = "Zayıf";
            break;
        case 3:
            strengthText = "Orta";
            break;
        case 4:
        case 5:
            strengthText = "Güçlü";
            break;
        default:
            strengthText = "Çok Zayıf";
    }
    document.getElementById('password_strength_level').textContent = strengthText;
}

// Şifre güncelleme formu gönderim ön kontrolü
function validatePasswordUpdate() {
    const yeniSifre = document.getElementById('yeni_sifre').value;
    const yeniSifreTekrar = document.getElementById('yeni_sifre_tekrar').value;
    if (yeniSifre !== yeniSifreTekrar) {
        alert('Yeni şifre ve şifre tekrar alanı eşleşmiyor!');
        return false;
    }
    return true;
}

// Yeni adres ekleme modalını aç
function showAddAddressModal() {
    var addModal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    addModal.show();
}

// Adres düzenleme modalını açmak (JSON veriyi modal inputlarına yerleştiriyoruz)
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
            document.getElementById('edit_varsayilan').checked = (data.varsayilan == 1);
            var editModal = new bootstrap.Modal(document.getElementById('editAddressModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Adres bilgisi yüklenemedi:', error);
            alert('Adres bilgisi alınırken bir hata oluştu.');
        });
}

// Varsayılan adres yap
function makeDefault(addressId) {
    if(confirm('Bu adresi varsayılan olarak ayarlamak istediğinize emin misiniz?')) {
        window.location.href = 'nedmin/netting/islem.php?adres_varsayilan=' + addressId;
    }
}

// Adres sil
function deleteAddress(addressId) {
    if(confirm('Bu adresi silmek istediğinize emin misiniz?')) {
        window.location.href = 'nedmin/netting/islem.php?adres_sil=' + addressId;
    }
}

// Sayfa yüklendiğinde inline-container, varsayılan olarak profil bilgileri gösterilsin.
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('inline-container').style.display = 'block';
    document.getElementById('external-container').style.display = 'none';
    showInlineSection('profile_info');
});
</script>

<?php include 'footer.php'; ?>
