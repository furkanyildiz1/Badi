<?php
session_start();
// Set error logging with correct path
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'paytr_debug.log');
error_reporting(E_ALL);

// Test if logging works immediately
error_log("\n=== New Callback Request ===");
error_log("PayTR Callback File Loaded at: " . date('Y-m-d H:i:s'));

// Log request method and all variables
error_log("REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("Raw POST data: " . file_get_contents("php://input"));
error_log("POST variables: " . print_r($_POST, true));
error_log("GET variables: " . print_r($_GET, true));

include 'nedmin/netting/baglan.php';

// Get the data from either POST or GET
$data = $_POST ?: $_GET;

if (!empty($data)) {
    error_log("Processing POST data");
    
    // Get PayTR settings from database
    $paytr_ayar = $db->query("SELECT * FROM paytr_ayar WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    $merchant_key = $paytr_ayar['merchant_key'];
    $merchant_salt = $paytr_ayar['merchant_salt'];
    
    // Verify hash
    $hash = base64_encode(hash_hmac('sha256', $data['merchant_oid'].$merchant_salt.$data['status'].$data['total_amount'], $merchant_key, true));
    
    error_log("Hash Verification:");
    error_log("Generated Hash: " . $hash);
    error_log("Received Hash: " . ($data['hash'] ?? 'no hash received'));
    
    if($hash != $data['hash']) {
        error_log("Hash verification failed");
        die('PAYTR notification failed: bad hash');
    }
    
    if($data['status'] == 'success') {
        error_log("Payment successful, attempting database operations");
        try {

            // Sonra faturalar tablosuna kaydet
            /*$fatura_ekle = $db->prepare("INSERT INTO faturalar (
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
                $data['merchant_oid'],
                $_SESSION['fatura_adres_id'],
                $_SESSION['ara_toplam'],
                $_SESSION['kampanya_id'],
                $_SESSION['indirim_tutari'],
                $data['total_amount'] / 100,
                'kredi_karti',
                'onaylandi'
            ]);*/
            $faturacek = $db->prepare("SELECT * FROM temp_faturalar WHERE fatura_no = ?");
            $faturacek->execute([$data['merchant_oid']]);
            $fatura = $faturacek->fetch(PDO::FETCH_ASSOC);

            $fatura_onayla = $db->prepare("INSERT INTO faturalar (
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
            $fatura_onayla->execute([
                $fatura['user_id'],
                $fatura['fatura_no'],
                $fatura['fatura_adres_id'],
                $fatura['ara_toplam'],
                $fatura['kampanya_kodu_id'],
                $fatura['indirim_tutari'],
                $fatura['toplam_tutar'],
                'kredi_karti',
                'onaylandi'
            ]);

            $fatura_onayla_id = $db->lastInsertId();

            $temp_fatura_sil = $db->prepare("DELETE FROM temp_faturalar WHERE fatura_no = ?");
            $temp_fatura_sil->execute([$data['merchant_oid']]);

            $sepetcek = $db->prepare("SELECT * FROM sepet WHERE user_id = ?");
            $sepetcek->execute([$fatura['user_id']]);




            $insert_satilan = $db->prepare("INSERT INTO satilan_kurslar (
                fatura_id,
                kurs_id,
                has_kurum_cert,
                has_uni_cert,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

            while($sepet = $sepetcek->fetch(PDO::FETCH_ASSOC)) {
                $kurum_cert = 0;
                $uni_cert = 0;
                if($sepet['selected_certs'] == 'kurum_cert') {
                    $kurum_cert = 1;
                }
                if($sepet['selected_certs'] == 'uni_cert') {
                    $uni_cert = 1;
                }
                if($sepet['selected_certs'] == 'both_cert') {
                    $kurum_cert = 1;
                    $uni_cert = 1;
                }
            $insert_satilan->execute([
                $fatura_onayla_id,
                $sepet['course_id'],
                $kurum_cert,
                $uni_cert
            ]);
            }
            // Sepeti temizle
            $sepet_temizle = $db->prepare("DELETE FROM sepet WHERE user_id = ?");
            $sepet_temizle->execute([$fatura['user_id']]);
            
            error_log("Order and invoice created successfully");
            
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
        }
    } else {
        error_log("Payment failed. Status: " . $data['status']);
        $fatura_iptal = $db->prepare("UPDATE faturalar SET odeme_durumu = 'iptal_edildi' WHERE fatura_id = ?");
        $fatura_iptal->execute([$data['merchant_oid']]);
    }
    
    error_log("Sending OK response to PayTR");
    echo "OK";
} else {
    error_log("No data received in callback");
    echo "FAIL";
}

error_log("=== End Callback Request ===\n");
?> 