<?php
include 'header.php';

// Check if user is logged in and previous steps are completed
if(!isset($_SESSION['userkullanici_mail']) || !isset($_SESSION['fatura_adres_id']) || !isset($_SESSION['odeme_yontemi']) || $_SESSION['odeme_yontemi'] != 'havale') {
    header("Location: checkout-payment-method.php");
    exit();
}

// Get selected address
$adres_sor = $db->prepare("SELECT * FROM fatura_adresleri WHERE fatura_adres_id = ?");
$adres_sor->execute([$_SESSION['fatura_adres_id']]);
$adres = $adres_sor->fetch(PDO::FETCH_ASSOC);

// Bank account details (you can move these to database if needed)
$banka_hesaplari = $db->query("SELECT * FROM banka_hesaplari WHERE durum = 1");

?>

<section class="checkout-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-steps">
                    <div class="step completed">1. Fatura Adresi</div>
                    <div class="step completed">2. Ödeme Yöntemi</div>
                    <div class="step active">3. Havale Bilgileri</div>
                </div>

                <!-- Selected Address Summary -->
                <div class="selected-address mb-4">
                    <h4>Fatura Adresi</h4>
                    <div class="address-box p-3 bg-light rounded">
                        <strong><?php echo $adres['ad_soyad']; ?></strong><br>
                        <?php echo $adres['adres']; ?><br>
                        <?php echo $adres['ilce'] . '/' . $adres['il']; ?><br>
                        Tel: <?php echo $adres['telefon']; ?>
                        <a href="checkout-address.php" class="btn btn-sm btn-outline-primary float-right">Değiştir</a>
                    </div>
                </div>

                <div class="havale-details">
                    <h3 class="title">Havale/EFT Bilgileri</h3>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Aşağıdaki banka hesaplarından birine ödemenizi yaptıktan sonra "Siparişi Tamamla" butonuna tıklayın. 
                        Ödemeniz onaylandıktan sonra kurslara erişiminiz açılacaktır.
                    </div>

                    <div class="bank-accounts">
                        <?php while($hesap = $banka_hesaplari->fetch(PDO::FETCH_ASSOC)) { ?>
                            <div class="bank-account-box p-4 mb-3 bg-light rounded">
                                <h5><?php echo $hesap['banka_adi']; ?></h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Hesap Sahibi:</strong></td>
                                        <td><?php echo $hesap['hesap_sahibi']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>IBAN:</strong></td>
                                        <td>
                                            <?php echo $hesap['iban']; ?>
                                            <button class="btn btn-sm btn-outline-primary ml-2" 
                                                    onclick="copyToClipboard('<?php echo $hesap['iban']; ?>')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Şube Kodu:</strong></td>
                                        <td><?php echo $hesap['sube_kodu']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hesap No:</strong></td>
                                        <td><?php echo $hesap['hesap_no']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                    <?php 
                        $check = true;
                        while($check){
                            $fatura_no = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
                            $check = $db->query("SELECT 1 FROM faturalar WHERE fatura_no = '$fatura_no'")->fetch(PDO::FETCH_ASSOC);
                        }
                    ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Havale/EFT yaparken açıklama kısmına sipariş numaranızı (<?php echo $fatura_no; ?>) yazmayı unutmayınız.
                    </div>
                    <form action="nedmin/netting/islem.php" method="POST">
                        <div class="form-group mt-4">
                            <input type="hidden" name="fatura_no" value="<?php echo $fatura_no; ?>">
                            <button type="submit" name="siparisi_tamamla" class="btn btn-primary btn-block">
                                Siparişi Tamamla
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <?php include 'includes/order-summary.php'; ?>
            </div>
        </div>
    </div>
</section>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('IBAN kopyalandı!');
    });
}
</script>

<style>
/* ... previous styles ... */

.bank-account-box {
    border: 1px solid #dee2e6;
}

.bank-account-box table {
    margin-bottom: 0;
}

.bank-account-box td {
    padding: 8px 0;
}
</style>

<?php include 'footer.php'; ?> 