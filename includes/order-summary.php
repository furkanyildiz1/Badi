<?php
// Get cart items
$cart_items = $db->prepare("
    SELECT k.*, s.id as sepet_id, s.selected_certs, s.cert_total_price 
    FROM sepet s 
    JOIN kurslar k ON s.course_id = k.kurs_id 
    WHERE s.user_id = :user_id
");
$cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]);
$cart_courses = $cart_items->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$ara_toplam = 0;
foreach($cart_courses as $item) {
    if(!empty($item['selected_certs'])) {
        $ara_toplam += floatval($item['cert_total_price']); // Add certificate prices
    }
}

// Calculate discount if exists
$indirim_tutari = 0;
if(isset($_SESSION['kampanya_indirim'])) {
    if($_SESSION['kampanya_tur'] == 'yuzde') {
        $indirim_tutari = $ara_toplam * ($_SESSION['kampanya_indirim'] / 100);
    } else {
        $indirim_tutari = $_SESSION['kampanya_indirim'];
    }
}
$toplam_tutar = $ara_toplam - $indirim_tutari;
?>

<div class="order-summary bg-white p-4 rounded shadow-sm">
    <h4 class="mb-3">Sipariş Özeti</h4>
    
    <!-- Cart Items -->
    <div class="cart-items mb-4">
        <?php foreach($cart_courses as $item) { ?>
            <div class="cart-item d-flex flex-column mb-3">
                <div class="d-flex justify-content-between">
                    <span class="course-title"><?php echo $item['baslik']; ?></span>
                </div>
                
                <?php if(!empty($item['selected_certs'])) { ?>
                    <div class="selected-certificates mt-2">
                        <?php 
                        $certs = $item['selected_certs'];
                        
                            echo '<div class="cert-item d-flex justify-content-between">';
                            switch($certs) {
                                case 'kurum_cert':
                                    echo '<small class="text-muted">- Kurum Onaylı Sertifika</small>';
                                    echo '<small class="text-muted">' . number_format($item['kurum_onayli_sertifika_fiyat'], 2) . ' TL</small>';
                                    break;
                                case 'uni_cert':
                                    echo '<small class="text-muted">- Üniversite Onaylı Sertifika</small>';
                                    echo '<small class="text-muted">' . number_format($item['uni_onayli_sertifika_fiyat'], 2) . ' TL</small>';
                                    break;
                                case 'both_cert':
                                    echo '<small class="text-muted">- Kurum & Üniversite Onaylı Sertifika</small>';
                                    echo '<small class="text-muted">' . number_format($item['sertifikalar_birlikte_fiyat'], 2) . ' TL</small>';
                                    break;
                            }
                            echo '</div>';
                        
                        ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <!-- Summary Items -->
    <div class="summary-items">
        <div class="summary-item d-flex justify-content-between mb-2">
            <span>Ara Toplam:</span>
            <span><?php echo number_format($ara_toplam, 2); ?> TL</span>
        </div>
        
        <?php if(isset($_SESSION['kampanya_indirim'])) { ?>
            <div class="summary-item d-flex justify-content-between mb-2 text-success">
                <span>Kampanya İndirimi:</span>
                <span>-<?php echo number_format($indirim_tutari, 2); ?> TL</span>
            </div>
        <?php } ?>

        <div class="summary-item d-flex justify-content-between mb-3 pt-2 border-top">
            <strong>Toplam:</strong>
            <strong><?php echo number_format($toplam_tutar, 2); ?> TL</strong>
        </div>
    </div>
</div>

<style>
.order-summary {
    position: sticky;
    top: 20px;
}

.cart-items {
    max-height: 300px;
    overflow-y: auto;
}

.cart-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.course-title {
    font-weight: 500;
}

.selected-certificates {
    font-size: 0.9em;
    padding-left: 15px;
}

.cert-item {
    margin-top: 2px;
}

.summary-items {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}
</style> 
