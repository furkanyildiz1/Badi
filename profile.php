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
            <!-- Profile Info Section -->
            <div class="col-md-6">
                <div class="profile-section">
                    <div class="profile-header">
                        <h4>Profil Bilgileri</h4>
                    </div>
                    <div class="profile-content">
                        <!-- Add profile picture section -->
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

                <!-- Password Change Section - Now Collapsible -->
                <div class="profile-section">
                    <div class="profile-header">
                        <button class="btn btn-link w-100 text-left d-flex justify-content-between align-items-center" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#passwordSection" 
                                aria-expanded="false">
                            <h4 class="mb-0">Şifre Değiştir</h4>
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse" id="passwordSection">
                        <div class="profile-content">
                            <form action="nedmin/netting/islem.php" method="POST">
                                <div class="form-group">
                                    <label>Mevcut Şifre</label>
                                    <div class="input-group">
                                        <input type="password" name="eski_sifre" class="form-control" id="eski_sifre" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('eski_sifre')">
                                            <i class="fa fa-eye" id="eski_sifre_icon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Yeni Şifre</label>
                                    <div class="input-group">
                                        <input type="password" name="yeni_sifre" class="form-control" id="yeni_sifre" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('yeni_sifre')">
                                            <i class="fa fa-eye" id="yeni_sifre_icon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Yeni Şifre (Tekrar)</label>
                                    <div class="input-group">
                                        <input type="password" name="yeni_sifre_tekrar" class="form-control" id="yeni_sifre_tekrar" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('yeni_sifre_tekrar')">
                                            <i class="fa fa-eye" id="yeni_sifre_tekrar_icon"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" name="kullanici_sifre_guncelle" class="btn btn-warning">
                                    Şifreyi Değiştir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Addresses Section -->
            <div class="col-md-6">
                <div class="profile-section">
                    <div class="profile-header d-flex justify-content-between align-items-center">
                        <h4>Fatura Adresleri</h4>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newAddressModal">
                            <i class="fa fa-plus"></i> Yeni Adres
                        </button>
                    </div>
                    <div class="profile-content">
                        <?php if($adresler->rowCount() > 0): ?>
                            <?php while($adres = $adresler->fetch(PDO::FETCH_ASSOC)): ?>
                                <div class="address-card <?php echo $adres['varsayilan'] ? 'default' : ''; ?>">
                                    <?php if($adres['varsayilan']): ?>
                                        <span class="default-badge">Varsayılan</span>
                                    <?php endif; ?>
                                    
                                    <h5><?php echo $adres['ad_soyad']; ?></h5>
                                    <p class="mb-1"><?php echo $adres['adres']; ?></p>
                                    <p class="mb-1"><?php echo $adres['ilce'] . '/' . $adres['il']; ?></p>
                                    <p class="mb-2"><?php echo $adres['telefon']; ?></p>
                                    
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="editAddress(<?php echo $adres['fatura_adres_id']; ?>)">
                                            Düzenle
                                        </button>
                                        <?php if(!$adres['varsayilan']): ?>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="makeDefault(<?php echo $adres['fatura_adres_id']; ?>)">
                                                Varsayılan Yap
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteAddress(<?php echo $adres['fatura_adres_id']; ?>)">
                                                Sil
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">Henüz kayıtlı adresiniz bulunmuyor.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Address Modal -->
<div class="modal fade" id="newAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Adres Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="nedmin/netting/islem.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ad Soyad</label>
                        <input type="text" name="ad_soyad" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="tel" name="telefon" class="form-control" 
                               pattern="[0-9]*" placeholder="05xxxxxxxxx" maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label>TC No</label>
                        <input type="text" name="tc_no" class="form-control" 
                               pattern="[0-9]*" placeholder="xxxxxxxxxxx" maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label>Adres</label>
                        <textarea name="adres" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İl</label>
                                <input type="text" name="il" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İlçe</label>
                                <input type="text" name="ilce" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Posta Kodu</label>
                        <input type="text" name="posta_kodu" class="form-control" required>
                    </div>
                    <div class="form-check mt-3">
                        <input type="checkbox" name="varsayilan" class="form-check-input" id="defaultCheck">
                        <label class="form-check-label" for="defaultCheck">
                            Varsayılan adres olarak kaydet
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="yeni_adres_ekle" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this modal after the newAddressModal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adres Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="nedmin/netting/islem.php" method="POST">
                <input type="hidden" name="fatura_adres_id" id="edit_adres_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ad Soyad</label>
                        <input type="text" name="ad_soyad" id="edit_ad_soyad" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="tel" name="telefon" id="edit_telefon" class="form-control" 
                               pattern="[0-9]*" placeholder="05xxxxxxxxx" maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label>TC No</label>
                        <input type="text" name="tc_no" id="edit_tc_no" class="form-control" 
                               pattern="[0-9]*" placeholder="xxxxxxxxxxx" maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label>Adres</label>
                        <textarea name="adres" id="edit_adres" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İl</label>
                                <input type="text" name="il" id="edit_il" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İlçe</label>
                                <input type="text" name="ilce" id="edit_ilce" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Posta Kodu</label>
                        <input type="text" name="posta_kodu" id="edit_posta_kodu" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="adres_duzenle" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
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

<script>
function editAddress(addressId) {
    // Fetch address details via AJAX
    fetch('nedmin/netting/islem.php?adres_getir=' + addressId)
        .then(response => response.json())
        .then(data => {
            // Populate the edit modal with address data
            document.getElementById('edit_adres_id').value = data.fatura_adres_id;
            document.getElementById('edit_ad_soyad').value = data.ad_soyad;
            document.getElementById('edit_telefon').value = data.telefon;
            document.getElementById('edit_tc_no').value = data.tc_no;
            document.getElementById('edit_adres').value = data.adres;
            document.getElementById('edit_il').value = data.il;
            document.getElementById('edit_ilce').value = data.ilce;
            document.getElementById('edit_posta_kodu').value = data.posta_kodu;

            // Show the modal
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