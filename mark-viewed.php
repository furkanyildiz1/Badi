<?php
session_start();
include 'nedmin/netting/baglan.php';

// Only for authenticated users
if(!isset($_SESSION['userkullanici_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$user_id = $_SESSION['userkullanici_id'];
$bolum_id = isset($_POST['bolum_id']) ? intval($_POST['bolum_id']) : 0;

if($bolum_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid content ID']);
    exit;
}

try {
    // Get course ID for this content
    $courseQuery = $db->prepare("
        SELECT km.kurs_id 
        FROM kurs_bolumleri kb
        JOIN kurs_modulleri km ON kb.modul_id = km.modul_id
        WHERE kb.bolum_id = ?
    ");
    $courseQuery->execute([$bolum_id]);
    $course = $courseQuery->fetch(PDO::FETCH_ASSOC);
    
    if(!$course) {
        throw new Exception('Content not found');
    }
    
    $kurs_id = $course['kurs_id'];
    
    // Check if user is enrolled in this course
    $enrollCheck = $db->prepare("
        SELECT * FROM satilan_kurslar sk
        JOIN faturalar f ON sk.fatura_id = f.fatura_id
        WHERE f.user_id = ? AND sk.kurs_id = ? AND f.odeme_durumu = 'onaylandi'
    ");
    $enrollCheck->execute([$user_id, $kurs_id]);
    
    if($enrollCheck->rowCount() == 0) {
        throw new Exception('Not enrolled in this course');
    }
    
    // Check if already viewed
    $viewCheck = $db->prepare("
        SELECT * FROM kurs_izleme_kayitlari
        WHERE user_id = ? AND bolum_id = ?
    ");
    $viewCheck->execute([$user_id, $bolum_id]);
    
    if($viewCheck->rowCount() == 0) {
        // Mark as viewed
        $markViewed = $db->prepare("
            INSERT INTO kurs_izleme_kayitlari
            (user_id, kurs_id, bolum_id, izlenme_tarihi)
            VALUES (?, ?, ?, NOW())
        ");
        $markViewed->execute([$user_id, $kurs_id, $bolum_id]);
    }
    
    echo json_encode(['success' => true]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
