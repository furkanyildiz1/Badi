<?php
    $merchant_id = "XXX";
    $merchant_key = "XXX";
    $merchant_salt = "XXX";
    $merchant_oid = "XXX";

    $paytr_token = base64_encode(hash_hmac('sha256', $merchant_id . $merchant_oid . $merchant_salt, $merchant_key, true));

    $post_vals = array('merchant_id' => $merchant_id,
        'merchant_oid' => $merchant_oid,
        'paytr_token' => $paytr_token);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/durum-sorgu");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);
	
	//XXX: DİKKAT: lokal makinanızda "SSL certificate problem: unable to get local issuer certificate" uyarısı alırsanız eğer
    //aşağıdaki kodu açıp deneyebilirsiniz. ANCAK, güvenlik nedeniyle sunucunuzda (gerçek ortamınızda) bu kodun kapalı kalması çok önemlidir!
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
    $result = @curl_exec($ch);

    if (curl_errno($ch)) {
        echo curl_error($ch);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $result = json_decode($result, 1);

    if ($result[status] != 'success') {
        echo $result[err_no] . " - " . $result[err_msg];
        exit;
    }

    //sipariş tutarı
    echo $result[payment_amount] . " " . $result[currency] . "<br>";

    //müşteri ödeme tutarı
    echo $result[payment_total] . " " . $result[currency] . "<br>";

    //siparişteki iadeler (varsa)
    foreach ($result[returns] AS $return_success)
        print_r($return_success);
?>