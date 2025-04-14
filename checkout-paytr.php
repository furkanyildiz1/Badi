<?php
require_once __DIR__ . '/vendor/autoload.php';
include 'header.php';

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'paytr_debug.log');
error_reporting(E_ALL);

// Test log
error_log("Starting PayTR process at " . date('Y-m-d H:i:s'));
use Mews\PayTr\Payment;
use Mews\PayTr\Order;
use Mews\PayTr\Config;

// Function to get real IP address
function getIpAddress() {
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = '127.0.0.1';
    

    error_log("IP Address: " . $ipaddress);
    return $ipaddress;
}

// Check if user is logged in and previous steps are completed
if(!isset($_SESSION['userkullanici_mail']) || !isset($_SESSION['fatura_adres_id']) || !isset($_SESSION['odeme_yontemi']) || $_SESSION['odeme_yontemi'] != 'kredi_karti') {
    header("Location: checkout-payment-method.php");
    exit();
}

// Get selected address
$adres_sor = $db->prepare("SELECT * FROM fatura_adresleri WHERE fatura_adres_id = ?");
$adres_sor->execute([$_SESSION['fatura_adres_id']]);
$adres = $adres_sor->fetch(PDO::FETCH_ASSOC);

// Generate unique order number
$check = true;
while($check){
    $siparis_no = 'INV' . date('Ymd') . rand(1000, 9999);
    $check = $db->query("SELECT 1 FROM faturalar WHERE fatura_no = '$siparis_no'")->fetch(PDO::FETCH_ASSOC);
}

// Calculate cart total with coupon
$cart_items = $db->prepare("SELECT k.*, s.id as sepet_id, s.selected_certs, s.cert_total_price 
    FROM sepet s 
    JOIN kurslar k ON s.course_id = k.kurs_id 
    WHERE s.user_id = :user_id");
$cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]);

$ara_toplam = 0;
$basket_items = array();
while($item = $cart_items->fetch(PDO::FETCH_ASSOC)) {
    $item_total = 0;
    // Add certificates if selected
    
    $ara_toplam += $item['cert_total_price'];
}

// Apply campaign discount if exists
$_SESSION['ara_toplam'] = $ara_toplam;
$toplam_tutar = $ara_toplam;
if(isset($_SESSION['kampanya_indirim'])) {
    if($_SESSION['kampanya_tur'] == 'yuzde') {
        $indirim_tutari = $ara_toplam * ($_SESSION['kampanya_indirim'] / 100);
    } else {
        $indirim_tutari = $_SESSION['kampanya_indirim'];
    }
    $toplam_tutar = $ara_toplam - $indirim_tutari;
    $_SESSION['indirim_tutari'] = $indirim_tutari;
}


// Get PayTR settings from database
$paytr_ayar = $db->query("SELECT * FROM paytr_ayar WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$merchant_id    = $paytr_ayar['merchant_id'];
$merchant_key   = $paytr_ayar['merchant_key'];
$merchant_salt  = $paytr_ayar['merchant_salt'];

# User info
$email = $_SESSION['userkullanici_mail'];
$payment_amount = $toplam_tutar * 100; //Convert to cents
$merchant_oid = $siparis_no;
$user_name = $adres['ad_soyad'];
$user_address = $adres['adres'];
$user_phone = $adres['telefon'];

// Get current URL base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$base_path = '';
$base_url = $protocol . $host . $base_path;

error_log("Base URL: " . $base_url); // Debug log

# URLs
$merchant_ok_url = $base_url . "/siparis-basarili.php";
$merchant_fail_url = $base_url . "/siparis-basarisiz.php";

# Basket info
$user_basket = base64_encode(json_encode($basket_items));

# Optional
$debug_on = 1;
$test_mode = $paytr_ayar['test_mode'];
$no_installment = $paytr_ayar['installment_mode'];
$max_installment = $paytr_ayar['max_installment'];
$currency = "TL";

// Get user IP using our comprehensive function
$user_ip = getIpAddress();

// Debug log
error_log("Using IP for PayTR: " . $user_ip);

$timeout_limit = "30";

# Generate hash
$hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
$paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

$post_vals = array(
    'merchant_id' => $merchant_id,
    'user_ip' => $user_ip,
    'merchant_oid' => $merchant_oid,
    'email' => $email,
    'payment_amount' => $payment_amount,
    'paytr_token' => $paytr_token,
    'user_basket' => $user_basket,
    'debug_on' => $debug_on,
    'no_installment' => $no_installment,
    'max_installment' => $max_installment,
    'user_name' => $user_name,
    'user_address' => $user_address,
    'user_phone' => $user_phone,
    'merchant_ok_url' => $merchant_ok_url,
    'merchant_fail_url' => $merchant_fail_url,
    'notification_url' => $base_url . "/paytr-callback.php",
    'timeout_limit' => $timeout_limit,
    'currency' => $currency,
    'test_mode' => $test_mode
);

$fatura_ekle = $db->prepare("INSERT INTO temp_faturalar (
    user_id,
    fatura_no,
    fatura_adres_id,
    ara_toplam,
    kampanya_kodu_id,
    indirim_tutari,
    toplam_tutar,
    odeme_yontemi,
    odeme_durumu,
    created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");


$fatura_ekle->execute([
    $_SESSION['userkullanici_id'],
    $siparis_no,
    $_SESSION['fatura_adres_id'],
    $_SESSION['ara_toplam'],
    isset($_SESSION['kampanya_id']) ? $_SESSION['kampanya_id'] : null,
    isset($_SESSION['indirim_tutari']) ? $_SESSION['indirim_tutari'] : null,
    $payment_amount / 100,
    'kredi_karti',
    'beklemede'
]);

$_SESSION['son_siparis_no'] = $siparis_no;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$result = @curl_exec($ch);

if(curl_errno($ch)) {
    die("PAYTR IFRAME connection error. err:".curl_error($ch));
}

curl_close($ch);

// Add error checking for the API response
if(!$result) {
    die("PAYTR IFRAME connection error. Empty response received.");
}

// Decode JSON response
$result = json_decode($result, true);

// Debug the response
error_log("PayTR Response: " . print_r($result, true));

// Add proper error checking for the response array
if(isset($result['status']) && $result['status'] == 'success') {
    $token = $result['token'];
    
    // Store order info in session
    $_SESSION['pending_order'] = [
        'order_id' => $merchant_oid,
        'amount' => $payment_amount,
        'user_id' => $_SESSION['userkullanici_id'],
        'fatura_adres_id' => $_SESSION['fatura_adres_id']
    ];
} else {
    $error_reason = isset($result['reason']) ? $result['reason'] : 'Unknown error';
    die("PAYTR IFRAME failed. reason:" . $error_reason);
}


?>

<section class="checkout-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-steps">
                    <div class="step completed">1. Fatura Adresi</div>
                    <div class="step completed">2. Ödeme Yöntemi</div>
                    <div class="step active">3. Ödeme</div>
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

                <!-- iFrame code -->
                <div class="payment-iframe">
                    <script>
                    // Add a random parameter to prevent caching issues
                    const timestamp = new Date().getTime();
                    const iframeUrl = "https://www.paytr.com/odeme/guvenli/<?php echo $token;?>?t=" + timestamp;

                    // Replace the iframe section with this
                    document.write(`
                        <div style="width: 100%;margin: 0 auto;display: table;">
                            <script src="https://www.paytr.com/js/iframeResizer.min.js"><\/script>
                            <iframe src="${iframeUrl}" 
                                    id="paytriframe" 
                                    frameborder="0" 
                                    scrolling="no" 
                                    style="width: 100%;"></iframe>
                            <script>iFrameResize({}, '#paytriframe');<\/script>
                        </div>
                    `);
                    </script>
                </div>
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
</style>

<?php include 'footer.php'; ?> 
<?php 
unset($_SESSION['fatura_adres_id']);
unset($_SESSION['ara_toplam']);
unset($_SESSION['kampanya_id']);
unset($_SESSION['indirim_tutari']);
unset($_SESSION['kampanya_tur']);
unset($_SESSION['kampanya_indirim']);
unset($_SESSION['pending_order']); 
?>