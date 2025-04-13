<?php
include 'header.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get billing addresses
$adresler = $db->prepare("
    SELECT * FROM fatura_adresleri 
    WHERE user_id = :user_id 
    ORDER BY varsayilan DESC
");
$adresler->execute(['user_id' => $_SESSION['userkullanici_id']]);
?>

<section class="checkout-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-steps">
                    <div class="step active">1. Fatura Adresi</div>
                    <div class="step">2. Ödeme Yöntemi</div>
                    <div class="step">3. Ödeme Bilgileri</div>
                </div>

                <form action="nedmin/netting/islem.php" method="POST" id="address-form">
                    <div class="billing-details">
                        <h3 class="title">Fatura Adresi Seçimi</h3>
                        
                        <?php if($adresler->rowCount() > 0) { ?>
                            <div class="saved-addresses mb-4">
                                <h4>Kayıtlı Adresleriniz</h4>
                                <?php while($adres = $adresler->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="fatura_adres_id" 
                                               value="<?php echo $adres['fatura_adres_id']; ?>" 
                                               <?php echo $adres['varsayilan'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            <strong><?php echo $adres['ad_soyad']; ?></strong><br>
                                            <?php echo $adres['adres']; ?><br>
                                            <?php echo $adres['ilce'] . '/' . $adres['il']; ?>
                                            <?php if($adres['varsayilan']) echo ' (Varsayılan)'; ?>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-outline-primary float-right" 
                                                onclick="editAddress(<?php echo htmlspecialchars(json_encode($adres)); ?>)">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <button type="button" class="btn btn-outline-primary mb-4" id="newAddressButton">
                            Yeni Fatura Adresi Ekle
                        </button>

                        <div id="newAddressForm" style="display: none;" class="<?php echo $adresler->rowCount() == 0 ? 'show' : ''; ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Ad Soyad <span class="required">*</span></label>
                                        <input type="text" name="new_ad_soyad" class="form-control" <?php echo $adresler->rowCount() == 0 ? 'required' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Telefon <span class="required">*</span></label>
                                        <input type="tel" name="new_telefon" class="form-control" 
                                               pattern="[0-9]*" placeholder="05xxxxxxxxx" maxlength="11"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" <?php echo $adresler->rowCount() == 0 ? 'required' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>TC No / Vergi No</label>
                                        <input type="text" name="new_tc_no" class="form-control" 
                                               pattern="[0-9]*" placeholder="xxxxxxxxxxx" maxlength="11"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" <?php echo $adresler->rowCount() == 0 ? 'required' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Adres <span class="required">*</span></label>
                                        <textarea name="new_adres" class="form-control" <?php echo $adresler->rowCount() == 0 ? 'required' : ''; ?>></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>İl <span class="required">*</span></label>
                                        <input type="text" name="new_il" class="form-control" <?php echo $adresler->rowCount() == 0 ? 'required' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>İlçe <span class="required">*</span></label>
                                        <input type="text" name="new_ilce" class="form-control" <?php echo $adresler->rowCount() == 0 ? 'required' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="new_varsayilan" class="form-check-input" value="1">
                                        <label class="form-check-label">Varsayılan fatura adresim olarak kaydet</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" name="fatura_adres_kaydet" class="btn btn-primary btn-block">
                                Devam Et
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <?php include 'includes/order-summary.php'; ?>
            </div>
        </div>
    </div>
</section>

<style>
.checkout-steps {
    display: flex;
    margin-bottom: 30px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.step {
    flex: 1;
    text-align: center;
    padding: 12px 5px;
    font-weight: 500;
    color: #6c757d;
    position: relative;
    transition: all 0.3s ease;
    z-index: 1;
}

.step:not(:last-child):after {
    content: '';
    position: absolute;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    background-color: #f8f9fa;
    border-top: 2px solid #dee2e6;
    border-right: 2px solid #dee2e6;
    transform: translateY(-50%) rotate(45deg);
    z-index: 2;
}

.step.active {
    color: #007bff;
    font-weight: bold;
    background-color: rgba(0, 123, 255, 0.05);
    border-radius: 6px;
}

.step.completed {
    color: #28a745;
}

.step.completed:before {
    content: '✓';
    display: inline-block;
    margin-right: 5px;
    font-weight: bold;
}

@media (max-width: 576px) {
    .step {
        font-size: 12px;
        padding: 10px 2px;
    }
    
    .step:not(:last-child):after {
        width: 12px;
        height: 12px;
    }
}

/* Modal kapatma butonu için özel stiller */
.modal .close {
    position: relative;
    width: 30px;
    height: 30px;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
    border: none;
    border-radius: 50%;
    opacity: 1;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    outline: none;
    cursor: pointer;
}

.modal .close:hover {
    background-color: #e9ecef;
    transform: rotate(90deg);
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.modal .close span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    font-weight: 300;
    color: #495057;
    text-shadow: none;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    background-color: #f8f9fa;
}

.modal-content {
    border: none;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    padding: 15px 20px;
    background-color: #f8f9fa;
}
</style>

<!-- Add this modal at the end of the file, before closing body tag -->
<div class="modal fade" id="editAddressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adres Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="nedmin/netting/islem.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="fatura_adres_id" id="edit_fatura_adres_id">
                    <div class="form-group">
                        <label>Ad Soyad <span class="required">*</span></label>
                        <input type="text" name="edit_ad_soyad" id="edit_ad_soyad" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Telefon <span class="required">*</span></label>
                        <input type="tel" name="edit_telefon" id="edit_telefon" class="form-control" 
                               pattern="[0-9]*" placeholder="05xxxxxxxxx" maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label>TC No / Vergi No</label>
                        <input type="text" name="edit_tc_no" id="edit_tc_no" class="form-control" 
                               pattern="[0-9]*" placeholder="xxxxxxxxxxx" maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-group">
                        <label>Adres <span class="required">*</span></label>
                        <textarea name="edit_adres" id="edit_adres" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>İl <span class="required">*</span></label>
                        <input type="text" name="edit_il" id="edit_il" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>İlçe <span class="required">*</span></label>
                        <input type="text" name="edit_ilce" id="edit_ilce" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="edit_varsayilan" id="edit_varsayilan" class="form-check-input" value="1">
                        <label class="form-check-label">Varsayılan fatura adresim olarak kaydet</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" name="fatura_adres_guncelle" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add these before the closing body tag -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function editAddress(address) {
    document.getElementById('edit_fatura_adres_id').value = address.fatura_adres_id;
    document.getElementById('edit_ad_soyad').value = address.ad_soyad;
    document.getElementById('edit_telefon').value = address.telefon || '';
    document.getElementById('edit_tc_no').value = address.tc_no || '';
    document.getElementById('edit_adres').value = address.adres;
    document.getElementById('edit_il').value = address.il;
    document.getElementById('edit_ilce').value = address.ilce;
    document.getElementById('edit_varsayilan').checked = address.varsayilan == 1;
    
    $('#editAddressModal').modal('show');
}

$(document).ready(function() {
    // Handle form toggle
    $('#newAddressButton').click(function() {
        var form = $('#newAddressForm');
        var button = $(this);
        
        if (form.is(':visible')) {
            form.slideUp();
            button.text('Yeni Fatura Adresi Ekle');
        } else {
            form.slideDown();
            button.text('Formu Gizle');
        }
    });

    // Show form if no addresses
    if ($('#newAddressForm').hasClass('show')) {
        $('#newAddressForm').show();
    }

    // Fix for modal close buttons
    $('.modal .close, .modal .btn-secondary').click(function() {
        $(this).closest('.modal').modal('hide');
    });
});
</script>

<?php include 'footer.php'; ?> 