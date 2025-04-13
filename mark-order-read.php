<?php
session_start();
include 'nedmin/netting/baglan.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_id']) || !isset($_POST['fatura_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

// Mark specific invoice as read
$update = $db->prepare("UPDATE faturalar SET bildirim_okundu = 1 
                      WHERE fatura_id = :fatura_id 
                      AND user_id = :user_id
                      AND bildirim_okundu = 0");
                      
$result = $update->execute([
    'fatura_id' => $_POST['fatura_id'],
    'user_id' => $_SESSION['userkullanici_id']
]);

echo json_encode(['success' => $result]); 