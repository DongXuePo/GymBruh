<?php
require_once "../config.php";

// 1. PROTEZIONE
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$me_id = $_SESSION['user_id'];          // Io
$target_id = $_GET['id'] ?? null;       // Lui

// Controllo validità
if (!$target_id || $target_id == $me_id) {
    header("Location: profile.php"); 
    exit;
}

// 2. CONTROLLO ESISTENZA
// *** CORREZIONE: cambiato 'SELECT id' in 'SELECT *' ***
$stmt = $pdo->prepare("SELECT * FROM follower WHERE follower_id = ? AND following_id = ?");
$stmt->execute([$me_id, $target_id]);
$relazione = $stmt->fetch();

if ($relazione) {
    // 3. SE ESISTE -> UNFOLLOW (Cancelliamo)
    $stmt = $pdo->prepare("DELETE FROM follower WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$me_id, $target_id]);
} else {
    // 4. SE NON ESISTE -> FOLLOW (Inseriamo)
    $stmt = $pdo->prepare("INSERT INTO follower (follower_id, following_id, data_follow) VALUES (?, ?, NOW())");
    $stmt->execute([$me_id, $target_id]);
}

// 5. TORNA AL PROFILO
header("Location: profile.php?id=" . $target_id);
exit;