<?php 
include 'header.php';

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'paytr_debug.log');
error_reporting(E_ALL);
// Debug log
error_log("Order Success Page Loaded");

// Get the order details from PayTR response
if(isset($_SESSION['son_siparis_no'])){
    $fatura_no = $_SESSION['son_siparis_no'];
}else{
    $fatura_no = $_GET['merchant_oid'] ?? null;
}
error_log("Looking for order: " . $fatura_no);

if ($fatura_no) {
    // Get order details from database
    $fatura_sor = $db->prepare("SELECT * FROM faturalar WHERE fatura_no = ?");
    $fatura_sor->execute([$fatura_no]);
    $fatura = $fatura_sor->fetch(PDO::FETCH_ASSOC);
    
    error_log("Order details: " . print_r($fatura, true));
}

?>

<section class="checkout-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php if ($fatura): ?>
                    <div class="alert alert-success">
                        <h4>Ödemeniz Başarıyla Tamamlandı!</h4>
                        <p>Sipariş numaranız: <?php echo htmlspecialchars($fatura_no); ?></p>
                        <p>Toplam Tutar: <?php echo number_format($fatura['toplam_tutar'], 2); ?> TL</p>
                        <p>Satın aldığınız kursları "Kurslarım" sayfasından görüntüleyebilirsiniz.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <p>Sipariş detayları bulunamadı.</p>
                    </div>
                <?php endif; ?>
                <div class="text-center mt-4">
                    <a href="my-courses.php" class="btn btn-primary">Kurslarıma Git</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?> 