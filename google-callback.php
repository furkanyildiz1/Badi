<?php
// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'vendor/autoload.php';
include 'nedmin/netting/baglan.php';

// Google API configuration
$clientID = '889590437641-p0apfp812tfg0c64b4m4viec62gougu5.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://badiakademi.com/google-callback.php';

try {
    // Creating client request to Google
    $client = new Google\Client();
    $client->setClientId($clientID);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");
    
    // Handle the callback
    if (isset($_GET['code'])) {
        // Get token from Google
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);
        
        // Get user information
        $service = new Google\Service\Oauth2($client);
        $user = $service->userinfo->get();
        
        // Prepare user data
        $google_id = $user->getId();
        $email = $user->getEmail();
        $name = $user->getGivenName();
        $surname = $user->getFamilyName();
        $picture = $user->getPicture();
        
        // Check if the user exists
        $check = $db->prepare("SELECT * FROM kullanici WHERE google_id = :google_id OR kullanici_mail = :email");
        $check->execute([
            'google_id' => $google_id,
            'email' => $email
        ]);
        $user_exists = $check->fetch(PDO::FETCH_ASSOC);
        
        if ($user_exists) {
            // User exists, update Google ID if needed
            if (empty($user_exists['google_id'])) {
                $update = $db->prepare("UPDATE kullanici SET google_id = :google_id WHERE kullanici_mail = :email");
                $update->execute([
                    'google_id' => $google_id,
                    'email' => $email
                ]);
            }
            
            // Store Google auth data in session temporarily
            $_SESSION['google_auth'] = [
                'kullanici_id' => $user_exists['kullanici_id'],
                'kullanici_mail' => $user_exists['kullanici_mail'],
                'kullanici_ad' => $user_exists['kullanici_ad'],
                'kullanici_soyad' => $user_exists['kullanici_soyad'],
                'google_id' => $google_id
            ];
            
            // Redirect to islem.php with a specific action
            header("Location: nedmin/netting/islem.php?google_login=1");
            exit;
            
        } else {
            // New user, create account
            $create = $db->prepare("INSERT INTO kullanici SET 
                kullanici_ad = :ad,
                kullanici_soyad = :soyad,
                kullanici_mail = :email,
                kullanici_tel = :telefon,
                google_id = :google_id,
                kullanici_durum = 'active',
                kullanici_zaman = NOW()");
                
            $insert = $create->execute([
                'ad' => $name,
                'soyad' => $surname,
                'email' => $email,
                'telefon' => '',
                'google_id' => $google_id
            ]);
            
            if ($insert) {
                // Get the new user ID
                $user_id = $db->lastInsertId();
                
                // Store Google auth data in session temporarily
                $_SESSION['google_auth'] = [
                    'kullanici_id' => $user_id,
                    'kullanici_mail' => $email,
                    'kullanici_ad' => $name,
                    'kullanici_soyad' => $surname,
                    'google_id' => $google_id
                ];
                
                // Redirect to islem.php with a specific action
                header("Location: nedmin/netting/islem.php?google_register=1");
                exit;
                
            } else {
                // Error creating user
                $_SESSION['error'] = "Google ile kayıt oluşturulurken bir hata oluştu.";
                header("Location: login.php");
                exit;
            }
        }
    } else {
        // Error in Google authentication
        $_SESSION['error'] = "Google ile giriş sırasında bir hata oluştu.";
        header("Location: login.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Sistem hatası: " . $e->getMessage();
    header("Location: login.php");
    exit;
}
?> 