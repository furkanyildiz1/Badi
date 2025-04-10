<?php 
include 'header.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get user's orders with pagination
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = 10;
$offset = ($sayfa - 1) * $limit;

$siparisler = $db->prepare("
    SELECT 
        f.*,
        COUNT(sk.satis_id) as kurs_sayisi,
        fa.ad_soyad as fatura_adsoyad,
        fa.adres,
        fa.il,
        fa.ilce
    FROM faturalar f
    LEFT JOIN satilan_kurslar sk ON f.fatura_id = sk.fatura_id
    LEFT JOIN fatura_adresleri fa ON f.fatura_adres_id = fa.fatura_adres_id
    WHERE f.user_id = ?
    GROUP BY f.fatura_id
    ORDER BY f.created_at DESC
    LIMIT $offset, $limit
");
$siparisler->execute([$_SESSION['userkullanici_id']]);

// Get total orders count for pagination
$total = $db->prepare("SELECT COUNT(*) FROM faturalar WHERE user_id = ?");
$total->execute([$_SESSION['userkullanici_id']]);
$total_orders = $total->fetchColumn();
$total_pages = ceil($total_orders / $limit);


?>

<style>
.orders-page {
    padding-top: 100px;
    padding-bottom: 50px;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.order-card {
    margin-bottom: 20px;
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

/* Highlight for unread orders */
.order-card.border-danger {
    border-left: 4px solid #dc3545 !important;
    background-color: #fff8f8;
}

/* Animation for the new badge */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.badge.bg-danger {
    animation: pulse 1.5s infinite;
}

.order-header {
    background-color: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.order-header:hover {
    background-color: #f0f0f0;
}

.order-header .fa-chevron-down {
    transition: transform 0.2s;
}

.order-header[aria-expanded="true"] .fa-chevron-down {
    transform: rotate(180deg);
}

.order-details {
    padding: 20px;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
}

.status-beklemede { background-color: #ffc107; color: #000; }
.status-onaylandi { background-color: #28a745; color: #fff; }
.status-iptal_edildi { background-color: #dc3545; color: #fff; }

.pagination {
    margin-top: 30px;
}

.bank-account {
    padding: 10px;
    border-radius: 5px;
    background-color: white;
}

.bank-account:hover {
    background-color: #f8f9fa;
}

.btn-outline-info {
    padding: 2px 8px;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    color: white;
}
</style>

<div class="orders-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Siparişlerim</h2>
            </div>
        </div>

        <?php if($siparisler->rowCount() > 0): ?>
            <?php while($siparis = $siparisler->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="card order-card <?php echo ($siparis['bildirim_okundu'] == 0) ? 'border-danger' : ''; ?>">
                    <div class="order-header d-flex justify-content-between align-items-center" 
                         role="button" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#order_<?php echo $siparis['fatura_id']; ?>"
                         aria-expanded="false">
                        <div>
                            <strong>Sipariş No:</strong> <?php echo $siparis['fatura_no']; ?>
                            <?php if($siparis['bildirim_okundu'] == 0): ?>
                                <span class="badge bg-danger ms-2">Yeni</span>
                            <?php endif; ?>
                            <br>
                            <small class="text-muted">
                                <?php echo date('d.m.Y H:i', strtotime($siparis['created_at'])); ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="status-badge status-<?php echo $siparis['odeme_durumu']; ?>">
                                <?php 
                                    switch($siparis['odeme_durumu']) {
                                        case 'beklemede':
                                            echo 'Ödeme Bekliyor';
                                            break;
                                        case 'onaylandi':
                                            echo 'Tamamlandı';
                                            break;
                                        case 'iptal_edildi':
                                            echo 'İptal Edildi';
                                            break;
                                    }
                                ?>
                            </span>
                            <br>
                            <strong><?php echo number_format($siparis['toplam_tutar'], 2); ?> TL</strong>
                            <i class="fas fa-chevron-down ms-2"></i>
                        </div>
                    </div>
                    
                    <div class="collapse" id="order_<?php echo $siparis['fatura_id']; ?>">
                        <div class="order-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Sipariş Detayları</h5>
                                    <p>
                                        <strong>Ödeme Yöntemi:</strong> 
                                        <?php echo $siparis['odeme_yontemi'] == 'kredi_karti' ? 'Kredi Kartı' : 'Havale/EFT'; ?>
                                    </p>
                                    <p>
                                        <strong>Kurs Sayısı:</strong> 
                                        <?php echo $siparis['kurs_sayisi']; ?>
                                    </p>
                                    <?php if($siparis['odeme_durumu'] == 'beklemede' && $siparis['odeme_yontemi'] == 'havale'): ?>
                                        <div class="mb-3">
                                            <button class="btn btn-info btn-sm" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#bankAccounts_<?php echo $siparis['fatura_id']; ?>" 
                                                    aria-expanded="false">
                                                <i class="fas fa-university me-1"></i> Banka Hesap Bilgilerini Göster
                                            </button>
                                        </div>
                                        <div class="collapse" id="bankAccounts_<?php echo $siparis['fatura_id']; ?>">
                                            <div class="card card-body bg-light border-info">
                                                <h6 class="mb-3">Banka Hesap Bilgileri</h6>
                                                <?php
                                                $banka_hesaplari = $db->query("SELECT * FROM banka_hesaplari WHERE durum = 1");
                                                while($hesap = $banka_hesaplari->fetch(PDO::FETCH_ASSOC)):
                                                ?>
                                                
                                                <!-- Ziraat Bankası -->
                                                <div class="bank-account mb-3">
                                                    <strong class="d-block"><?php echo $hesap['banka_adi']; ?></strong>
                                                    <small class="text-muted d-block">Hesap Sahibi: <?php echo $hesap['hesap_sahibi']; ?></small>
                                                    <div class="d-flex align-items-center mt-1">
                                                        <span class="me-2"><?php echo $hesap['iban']; ?></span>
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                onclick="copyToClipboard(this, '<?php echo $hesap['iban']; ?>')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <?php endwhile; ?>
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle"></i> 
                                                        Havale/EFT yaparken açıklama kısmına sipariş numaranızı (<?php echo $siparis['fatura_no']; ?>) yazmayı unutmayınız.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h5>Fatura Adresi</h5>
                                    <p>
                                        <?php echo $siparis['fatura_adsoyad']; ?><br>
                                        <?php echo $siparis['adres']; ?><br>
                                        <?php echo $siparis['ilce'] . '/' . $siparis['il']; ?>
                                    </p>
                                </div>
                            </div>

                            <?php
                            $kurslar = $db->prepare("
                                SELECT k.* 
                                FROM satilan_kurslar sk
                                JOIN kurslar k ON sk.kurs_id = k.kurs_id
                                WHERE sk.fatura_id = ?
                            ");
                            $kurslar->execute([$siparis['fatura_id']]);
                            ?>

                            <div class="mt-4">
                                <h5>Satın Alınan Kurslar</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Kurs</th>
                                                <th>Fiyat</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($kurs = $kurslar->fetch(PDO::FETCH_ASSOC)): ?>
                                                <tr>
                                                    <td><?php echo $kurs['baslik']; ?></td>
                                                    <td><?php echo number_format($kurs['fiyat'], 2); ?> TL</td>
                                                    <td class="text-end">
                                                        <?php if($siparis['odeme_durumu'] == 'onaylandi'): ?>
                                                            <a href="kurs.php?kurs_id=<?php echo $kurs['kurs_id']; ?>" 
                                                               class="btn btn-sm btn-primary">
                                                                Kursa Git
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <nav aria-label="Sipariş sayfaları">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $sayfa ? 'active' : ''; ?>">
                                <a class="page-link" href="?sayfa=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h3>Henüz Siparişiniz Bulunmuyor</h3>
                    <p class="text-muted">Hemen yeni bir kurs satın alarak öğrenmeye başlayın!</p>
                    <a href="kurslar_1.php" class="btn btn-primary">Kursları İncele</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Make sure jQuery is loaded first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function copyToClipboard(button, text) {
    navigator.clipboard.writeText(text).then(() => {
        // Change button icon temporarily
        const icon = button.querySelector('i');
        icon.classList.remove('fa-copy');
        icon.classList.add('fa-check');
        
        // Revert back after 1.5 seconds
        setTimeout(() => {
            icon.classList.remove('fa-check');
            icon.classList.add('fa-copy');
        }, 1500);
    });
}
$(document).ready(function() {
    console.log("Document ready, binding click events");
    
    // Mark order as read when expanded
    $('.order-header').on('click', function() {
        console.log("Order header clicked");
        
        // Fix: Get orderId directly from the data-bs-target attribute
        let targetSelector = $(this).attr('data-bs-target');
        console.log("Target selector:", targetSelector);
        
        if (!targetSelector) {
            console.error("No target selector found");
            return;
        }
        
        // Extract the ID from the selector (e.g., "#order_123" → "123")
        let orderId = targetSelector.split('_')[1];
        console.log("Order ID:", orderId);
        
        if (!orderId) {
            console.error("Could not extract order ID");
            return;
        }
        
        // Mark as read via AJAX
        $.ajax({
            url: 'mark-order-read.php',
            method: 'POST',
            data: { fatura_id: orderId },
            success: function(response) {
                console.log("AJAX response:", response);
                // Remove the "New" badge and border when successfully marked as read
                $(targetSelector).closest('.order-card').removeClass('border-danger');
                $(targetSelector).closest('.order-card').find('.badge.bg-danger').fadeOut();
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?> 