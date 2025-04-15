<?php 
include 'header.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get cart items
$cart_items = $db->prepare("
    SELECT k.*, s.id as sepet_id, s.selected_certs, s.cert_total_price 
    FROM sepet s 
    JOIN kurslar k ON s.course_id = k.kurs_id 
    WHERE s.user_id = :user_id
");
$cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]);

// Calculate total
$total = 0;
$cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]); // Reset query results
while($item = $cart_items->fetch(PDO::FETCH_ASSOC)) {// Base course price
    if(!empty($item['selected_certs'])) {
        $total += floatval($item['cert_total_price']); // Add certificate prices
    }
}

// Reset again for display loop
$cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]);

// Calculate final total
if(isset($_SESSION['kampanya_indirim'])) {
    if($_SESSION['kampanya_tur'] == 'yuzde') {
        $final_total = $total - ($total * ($_SESSION['kampanya_indirim'] / 100));
    } else {
        $final_total = $total - $_SESSION['kampanya_indirim'];
    }
} else {
    $final_total = $total;
}

// Check if it's free
$is_free = $final_total <= 0;
?>

<!-- Start Page Title Area -->
<section class="page-title-area item-bg1">
    <div class="container">
        <div class="page-title-content">
            <h2>Sepetim</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
                <li class="breadcrumb-item active">Sepetim</li>
            </ul>
        </div>
    </div>
</section>
<!-- End Page Title Area -->

<!-- Start Cart Area -->
<section class="cart-area ptb-100">
    <div class="container">
        <?php if($cart_items->rowCount() > 0) { ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-items">
                        <?php 
                        $total_price = 0;
                        while($item = $cart_items->fetch(PDO::FETCH_ASSOC)) {  // Base course price
                            if(!empty($item['selected_certs'])) {
                                $total_price += $item['cert_total_price'];
                            }
                        ?>
                            <div class="cart-item">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="item-image">
                                            <img src="<?php echo $item['resim_yol']; ?>" alt="<?php echo $item['baslik']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="item-details">
                                            <h4><?php echo $item['baslik']; ?></h4>
                                        </div>
                                        <div class="cart-item-details">
                                            <?php 
                                            if(!empty($item['selected_certs'])) {
                                                $certs = $item['selected_certs'];
                                                echo "<div class='selected-certs'>";
                                                echo "<strong>Seçilen Sertifikalar:</strong><br>";
                                                switch($certs) {
                                                    case 'kurum_cert':
                                                        echo "Kurum Onaylı Sertifika<br>";
                                                            break;
                                                        case 'uni_cert':
                                                            echo "Üniversite Onaylı Sertifika<br>";
                                                            break;
                                                        case 'both_cert':
                                                            echo "Kurum & Üniversite Onaylı Sertifika<br>";
                                                            break;
                                                    
                                                }
                                                echo "</div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="item-price">
                                            <span><?php echo number_format($item['cert_total_price'], 2); ?> TL</span>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="item-remove">
                                            <a href="nedmin/netting/islem.php?action=removeCourseFromCart&kurs_id=<?php echo $item['kurs_id']; ?>" 
                                               class="remove-btn">
                                                <i class="fa-solid fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h3>Sepet Özeti</h3>
                        
                        <!-- Coupon Form -->
                        <div class="coupon-section mb-4">
                            <?php if(isset($_SESSION['kampanya_indirim'])) { ?>
                                <div class="alert alert-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php 
                                            if($_SESSION['kampanya_tur'] == 'yuzde') {
                                                echo 'Uygulanan İndirim: %' . $_SESSION['kampanya_indirim'];
                                            } else {
                                                echo 'Uygulanan İndirim: ' . number_format($_SESSION['kampanya_indirim'], 2, ',', '.') . ' TL';
                                            }
                                            ?>
                                        </div>
                                        <a href="nedmin/netting/islem.php?action=removeCoupon" 
                                           class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <form action="nedmin/netting/islem.php" method="POST" class="coupon-form">
                                    <div class="input-group">
                                        <input type="text" name="kampanya_kodu" class="form-control" placeholder="Kampanya Kodu" required>
                                        <div class="input-group-append">
                                            <button type="submit" name="kampanya_uygula" class="btn btn-primary">Uygula</button>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>
                            
                            <?php 
                            if(isset($_SESSION['kampanya_error'])) {
                                echo '<div class="alert alert-danger mt-2">'.$_SESSION['kampanya_error'].'</div>';
                                unset($_SESSION['kampanya_error']);
                            }
                            if(isset($_SESSION['kampanya_success'])) {
                                echo '<div class="alert alert-success mt-2">'.$_SESSION['kampanya_success'].'</div>';
                                unset($_SESSION['kampanya_success']);
                            }
                            ?>
                        </div>

                        <!-- Updated Summary Items -->
                        <div class="summary-items">
                            <div class="summary-item">
                                <span>Ara Toplam:</span>
                                <span class="price"><?php echo number_format($total, 2, ',', '.'); ?> TL</span>
                            </div>
                            
                            <?php if(isset($_SESSION['kampanya_indirim'])) { ?>
                            <div class="summary-item text-success">
                                <span>Kampanya İndirimi:</span>
                                <span class="price">
                                    <?php 
                                    if($_SESSION['kampanya_tur'] == 'yuzde') {
                                        $indirim_tutari = $total * ($_SESSION['kampanya_indirim'] / 100);
                                        echo '-' . number_format($indirim_tutari, 2, ',', '.') . ' TL';
                                    } else {
                                        $indirim_tutari = $_SESSION['kampanya_indirim'];
                                        echo '-' . number_format($indirim_tutari, 2, ',', '.') . ' TL';
                                    }
                                    ?>
                                </span>
                            </div>
                            <?php } ?>

                            <div class="summary-item total">
                                <span><strong>Toplam:</strong></span>
                                <span class="price"><strong>
                                    <?php 
                                    echo number_format($final_total, 2, ',', '.'); 
                                    ?> TL
                                </strong></span>
                            </div>
                        </div>

                        <?php if($is_free): ?>
                            <!-- For free orders -->
                            <form method="POST" action="nedmin/netting/islem.php">
                                <button type="submit" name="completePaymentFree" class="btn btn-primary" style="width: 100%;">
                                    Ücretsiz Tamamla
                                </button>
                            </form>
                        <?php else: ?>
                            <!-- For paid orders -->
                            <a href="checkout-address.php" class="btn btn-primary" style="width: 100%;">
                                Ödemeye Geç
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="empty-cart text-center">
                <i class="fa-solid fa-shopping-cart fa-3x mb-3"></i>
                <h3>Sepetiniz Boş</h3>
                <p>Sepetinizde henüz bir kurs bulunmamaktadır.</p>
                <a href="index.php" class="default-btn">Kurslara Göz At</a>
            </div>
        <?php } ?>
    </div>
</section>
<!-- End Cart Area -->

<style>
.cart-item {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.item-image img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 6px;
}

.item-details h4 {
    margin-bottom: 10px;
    font-size: 18px;
    color: #333;
}

.item-details p {
    color: #666;
    font-size: 14px;
}

.item-price span {
    font-size: 18px;
    font-weight: 600;
    color: #2A3F54;
}

.remove-btn {
    color: #dc3545;
    font-size: 18px;
    transition: all 0.3s ease;
}

.remove-btn:hover {
    color: #c82333;
}

.cart-summary {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.cart-summary h3 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.coupon-section {
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
}

.coupon-form .input-group {
    margin-top: 10px;
}

.summary-items {
    margin: 20px 0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 5px 0;
}

.summary-item.total {
    border-top: 2px solid #eee;
    padding-top: 15px;
    margin-top: 15px;
}

.alert {
    padding: 8px;
    font-size: 14px;
    margin-top: 10px;
}

.empty-cart {
    padding: 50px 20px;
}

.empty-cart i {
    color: #ddd;
    margin-bottom: 20px;
}

.empty-cart h3 {
    margin-bottom: 10px;
    color: #333;
}

.empty-cart p {
    color: #666;
    margin-bottom: 20px;
}

.selected-certs {
    font-size: 0.9em;
    color: #666;
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.selected-certs strong {
    color: #333;
    display: block;
    margin-bottom: 5px;
}
</style>

<?php include 'footer.php'; ?> 
