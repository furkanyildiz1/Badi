<?php
include 'header.php';

// Check if user is logged in and previous steps are completed
if(!isset($_SESSION['userkullanici_mail']) || !isset($_SESSION['fatura_adres_id']) || !isset($_SESSION['odeme_yontemi']) || $_SESSION['odeme_yontemi'] != 'kredi_karti') {
    header("Location: checkout-payment-method.php");
    exit();
}

// Get saved cards
$kartlar = $db->prepare("SELECT * FROM kayitli_kartlar WHERE user_id = ? ORDER BY varsayilan DESC");
$kartlar->execute([$_SESSION['userkullanici_id']]);

// Get selected address
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
                    <div class="step completed">2. Ödeme Yöntemi</div>
                    <div class="step active">3. Ödeme Bilgileri</div>
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

                <form action="" method="POST" id="payment-form">
                    <div class="payment-details">
                        <h3 class="title">Kart Bilgileri</h3>

                        <?php if($kartlar->rowCount() > 0) { ?>
                            <div class="saved-cards mb-4">
                                <h4>Kayıtlı Kartlarınız</h4>
                                <?php while($kart = $kartlar->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="kart_id" 
                                               value="<?php echo $kart['kart_id']; ?>" 
                                               <?php echo $kart['varsayilan'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            <i class="fas fa-credit-card"></i>
                                            <?php 
                                            $masked_card = str_repeat('*', 12) . substr($kart['kart_no'], -4);
                                            echo $kart['kart_sahibi'] . ' - ' . $masked_card; 
                                            if($kart['varsayilan']) echo ' (Varsayılan)'; 
                                            ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <button type="button" class="btn btn-outline-primary mb-4" data-toggle="collapse" 
                                data-target="#newCardForm">
                            Yeni Kart Ekle
                        </button>

                        <div id="newCardForm" class="collapse <?php echo $kartlar->rowCount() == 0 ? 'show' : ''; ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Kart Üzerindeki İsim <span class="required">*</span></label>
                                        <input type="text" name="new_kart_sahibi" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Kart Numarası <span class="required">*</span></label>
                                        <input type="text" name="new_kart_no" class="form-control" maxlength="16">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Son Kullanma Tarihi <span class="required">*</span></label>
                                        <input type="text" name="new_son_kullanim" class="form-control" placeholder="AA/YY">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>CVV <span class="required">*</span></label>
                                        <input type="text" name="new_cvv" class="form-control" maxlength="3">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="new_kart_varsayilan" class="form-check-input" value="1">
                                        <label class="form-check-label">Bu kartı kaydet</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" name="siparisi_tamamla" class="btn btn-primary btn-block">
                                Ödemeyi Tamamla
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
/* ... previous styles ... */

.saved-cards .form-check {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: white;
    margin-bottom: 15px;
}

.saved-cards .form-check:hover {
    border-color: #007bff;
    cursor: pointer;
}

.saved-cards i {
    color: #007bff;
    margin-right: 10px;
}
</style>

<?php
// After your existing payment form validation

if(isset($_POST['siparisi_tamamla']) && $_SESSION['odeme_yontemi'] == 'kredi_karti') {
    
    // Get order details from session/database
    $check = true;
    while($check){
        $siparis_no = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        $check = $db->query("SELECT 1 FROM faturalar WHERE fatura_no = '$fatura_no'")->fetch(PDO::FETCH_ASSOC);
    }
    $total_amount = $_SESSION['sepet_toplam'] * 100; // Convert to kuruş
    
    // Get user info
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_email = $_SESSION['userkullanici_mail'];
    $user_name = $adres['ad_soyad'];
    $user_address = $adres['adres'];
    $user_phone = $adres['telefon'];
    
    // PayTR required parameters
    $merchant_id = 'XXXXX';
    $merchant_key = 'XXXXX';
    $merchant_salt = 'XXXXX';
    
    // Create order in database first
    $siparis = $db->prepare("INSERT INTO siparisler SET
        kullanici_id = ?,
        siparis_no = ?,
        fatura_adres_id = ?,
        odeme_yontemi = ?,
        toplam_tutar = ?,
        siparis_durum = ?,
        odeme_durum = ?");
        
    $insert = $siparis->execute([
        $_SESSION['userkullanici_id'],
        $siparis_no,
        $_SESSION['fatura_adres_id'],
        'kredi_karti',
        $_SESSION['sepet_toplam'],
        'beklemede',
        'beklemede'
    ]);

    if(!$insert) {
        header("Location:../siparis-basarisiz.php");
        exit;
    }

    // Current domain for callbacks
    $site_domain = "https://".$_SERVER['HTTP_HOST'];
    
    $merchant_ok_url = $site_domain."/siparis-basarili.php";
    $merchant_fail_url = $site_domain."/siparis-basarisiz.php";
    
    // Create basket for PayTR
    $sepet_urunler = $db->prepare("SELECT * FROM sepet WHERE kullanici_id = ?");
    $sepet_urunler->execute([$_SESSION['userkullanici_id']]);
    
    $basket_items = array();
    while($urun = $sepet_urunler->fetch(PDO::FETCH_ASSOC)) {
        $basket_items[] = array(
            $urun['urun_ad'],
            $urun['urun_fiyat'] * 100, // Convert to kuruş
            $urun['urun_adet']
        );
    }
    
    $user_basket = base64_encode(json_encode($basket_items));
    
    // Generate token
    $hash_str = $merchant_id . $user_ip . $siparis_no . $user_email . $total_amount . $user_basket . "0" . "0" . "TL" . "0";
    $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
    
    $post_vals = array(
        'merchant_id' => $merchant_id,
        'user_ip' => $user_ip,
        'merchant_oid' => $siparis_no,
        'email' => $user_email,
        'payment_amount' => $total_amount,
        'paytr_token' => $paytr_token,
        'user_basket' => $user_basket,
        'debug_on' => 1,
        'no_installment' => 0,
        'max_installment' => 0,
        'user_name' => $user_name,
        'user_address' => $user_address,
        'user_phone' => $user_phone,
        'merchant_ok_url' => $merchant_ok_url,
        'merchant_fail_url' => $merchant_fail_url,
        'timeout_limit' => 30,
        'currency' => "TL",
        'test_mode' => 0
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    
    $result = curl_exec($ch);
    
    if(curl_errno($ch))
        die("PAYTR IFRAME connection error. err:".curl_error($ch));
    
    curl_close($ch);
    
    $result = json_decode($result, 1);
    
    if($result['status'] == 'success') {
        $token = $result['token'];
        
        // Store token in session for verification
        $_SESSION['paytr_token'] = $token;
        $_SESSION['siparis_no'] = $siparis_no;
?>

<!-- PayTR iframe -->
<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
<iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $token;?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
<script>iFrameResize({},'#paytriframe');</script>

<?php
    } else {
        header("Location:siparis-basarisiz.php?durum=token-error");
        exit;
    }
}
?>

<?php include 'footer.php'; ?> 