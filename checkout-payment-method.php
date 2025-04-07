<?php
include 'header.php';

// Check if user is logged in and address is selected
if(!isset($_SESSION['userkullanici_mail']) || !isset($_SESSION['fatura_adres_id'])) {
    header("Location: checkout-address.php");
    exit();
}

// Get selected address details for display
$adres_sor = $db->prepare("SELECT * FROM fatura_adresleri WHERE fatura_adres_id = ?");
$adres_sor->execute([$_SESSION['fatura_adres_id']]);
$adres = $adres_sor->fetch(PDO::FETCH_ASSOC);
?>

<section class="checkout-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-steps">
                    <div class="step completed">1. Fatura Adresi</div>
                    <div class="step active">2. Ödeme Yöntemi</div>
                    <div class="step">3. Ödeme Bilgileri</div>
                </div>

                <!-- Selected Address Summary -->
                <div class="selected-address mb-4">
                    <h4>Seçili Fatura Adresi</h4>
                    <div class="address-box p-3 bg-light rounded">
                        <strong><?php echo $adres['ad_soyad']; ?></strong><br>
                        <?php echo $adres['adres']; ?><br>
                        <?php echo $adres['ilce'] . '/' . $adres['il']; ?><br>
                        Tel: <?php echo $adres['telefon']; ?>
                        <a href="checkout-address.php" class="btn btn-sm btn-outline-primary float-right">Değiştir</a>
                    </div>
                </div>

                <form action="nedmin/netting/islem.php" method="POST">
                    <div class="payment-method">
                        <h3 class="title">Ödeme Yöntemi Seçin</h3>
                        
                        <div class="payment-options">
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="odeme_yontemi" 
                                       value="kredi_karti" id="krediKarti" checked>
                                <label class="form-check-label" for="krediKarti">
                                    <i class="fas fa-credit-card"></i> Kredi/Banka Kartı
                                    <small class="d-block text-muted">Güvenli ödeme ile anında kurslara erişin</small>
                                </label>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="odeme_yontemi" 
                                       value="havale" id="havale">
                                <label class="form-check-label" for="havale">
                                    <i class="fas fa-university"></i> Havale/EFT
                                    <small class="d-block text-muted">Ödemeniz onaylandıktan sonra kurslara erişebilirsiniz</small>
                                </label>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" name="odeme_yontemi_kaydet" class="btn btn-primary btn-block">
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
    border-radius: 8px;
}

.step {
    flex: 1;
    text-align: center;
    padding: 10px;
    color: #6c757d;
}

.step.active {
    color: #007bff;
    font-weight: bold;
}

.step.completed {
    color: #28a745;
}

.payment-options {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.payment-options .form-check {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: white;
    margin-bottom: 15px;
}

.payment-options .form-check:hover {
    border-color: #007bff;
    cursor: pointer;
}

.payment-options .form-check-input {
    margin-top: 8px;
}

.payment-options i {
    font-size: 24px;
    margin-right: 10px;
    color: #007bff;
}
</style>

<?php include 'footer.php'; ?> 