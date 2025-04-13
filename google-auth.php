<?php
session_start();
require_once 'vendor/autoload.php';
include 'nedmin/netting/baglan.php';

// Google API configuration
$clientID = '889590437641-p0apfp812tfg0c64b4m4viec62gougu5.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://badiakademi.com/google-callback.php';

// Creating client request to Google
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Get authorization URL
$authUrl = $client->createAuthUrl();

// Redirect to Google login page
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;
?> 