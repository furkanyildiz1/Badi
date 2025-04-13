<?php
include 'header.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get cart items
$cart_items = $db->prepare("
    SELECT k.*, s.id as sepet_id 
    FROM sepet s 
    JOIN kurslar k ON s.course_id = k.kurs_id 
    WHERE s.user_id = :user_id
");
$cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]);

// Get billing addresses
$adresler = $db->prepare("
    SELECT * FROM fatura_adresleri 
    WHERE user_id = :user_id 
    ORDER BY varsayilan DESC
");
$adresler->execute(['user_id' => $_SESSION['userkullanici_id']]);

// Get saved cards
$kartlar = $db->prepare("
    SELECT * FROM kayitli_kartlar 
    WHERE user_id = :user_id 
    ORDER BY varsayilan DESC
");
$kartlar->execute(['user_id' => $_SESSION['userkullanici_id']]);

// Calculate totals
$total = 0;
$cart_courses = $cart_items->fetchAll(PDO::FETCH_ASSOC);
foreach($cart_courses as $item) {
    $total += $item['fiyat'];
}

// Apply discount if coupon exists
$final_total = $total;
if(isset($_SESSION['kampanya_indirim'])) {
    $discount = $total * ($_SESSION['kampanya_indirim'] / 100);
    $final_total = $total - $discount;
}
?>

<!-- Start Checkout Area -->
<section class="checkout-area ptb-100">
    <div class="container">
        <form action="nedmin/netting/islem.php" method="POST" id="checkout-form">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Billing Address Section -->
                    <div class="billing-details">
                        <h3 class="title">Fatura Bilgileri</h3>
                        
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
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <button type="button" class="btn btn-outline-primary mb-4" data-toggle="collapse" 
                                data-target="#newAddressForm">
                            Yeni Adres Ekle
                        </button>

                        <div id="newAddressForm" class="collapse <?php echo $adresler->rowCount() == 0 ? 'show' : ''; ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Ad Soyad <span class="required">*</span></label>
                                        <input type="text" name="new_ad_soyad" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Telefon <span class="required">*</span></label>
                                        <input type="text" name="new_telefon" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>TC No</label>
                                        <input type="text" name="new_tc_no" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Adres <span class="required">*</span></label>
                                        <textarea name="new_adres" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>İl <span class="required">*</span></label>
                                        <input type="text" name="new_il" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>İlçe <span class="required">*</span></label>
                                        <input type="text" name="new_ilce" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Posta Kodu</label>
                                        <input type="text" name="new_posta_kodu" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="new_varsayilan" class="form-check-input">
                                        <label class="form-check-label">Varsayılan adresim olarak kaydet</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="payment-box mt-4">
                        <h3 class="title">Ödeme Bilgileri</h3>
                        
                        <?php if($kartlar->rowCount() > 0) { ?>
                            <div class="saved-cards mb-4">
                                <h4>Kayıtlı Kartlarınız</h4>
                                <?php while($kart = $kartlar->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="saved_card_id" 
                                               value="<?php echo $kart['kart_id']; ?>"
                                               <?php echo $kart['varsayilan'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            **** **** **** <?php echo substr($kart['kart_no'], -4); ?>
                                            <?php if($kart['varsayilan']) echo ' (Varsayılan)'; ?>
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
                                        <input type="text" name="new_kart_no" class="form-control">
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
                                        <input type="text" name="new_cvv" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="new_kart_varsayilan" class="form-check-input">
                                        <label class="form-check-label">Varsayılan kartım olarak kaydet</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-details">
                        <h3 class="title">Sipariş Özeti</h3>
                        <div class="order-table table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Kurs</th>
                                        <th scope="col">Toplam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cart_courses as $item) { ?>
                                        <tr>
                                            <td><?php echo $item['baslik']; ?></td>
                                            <td><?php echo number_format($item['fiyat'], 2); ?> TL</td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-right">Ara Toplam:</td>
                                        <td><?php echo number_format($total, 2); ?> TL</td>
                                    </tr>
                                    <?php if(isset($_SESSION['kampanya_indirim'])) { ?>
                                        <tr>
                                            <td class="text-right">İndirim:</td>
                                            <td>-<?php echo number_format($discount, 2); ?> TL</td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-right"><strong>Genel Toplam:</strong></td>
                                        <td><strong><?php echo number_format($final_total, 2); ?> TL</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" name="siparisi_tamamla" class="btn btn-primary btn-block">
                            Siparişi Tamamla
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- End Checkout Area -->

<style>
.checkout-area {
    background-color: #f8f9fa;
    padding: 50px 0;
}

.billing-details, .payment-box, .order-details {
    background-color: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.title {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.form-group {
    margin-bottom: 20px;
}

.required {
    color: red;
}

.saved-addresses, .saved-cards {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
}

.order-table th, .order-table td {
    vertical-align: middle;
}
</style>

<?php include 'footer.php'; ?> 