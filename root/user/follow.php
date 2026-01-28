<?php
require_once "../config.php";

// 1. PROTEZIONE
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$me_id = $_SESSION['user_id'];          // Io (che faccio l'azione)
$target_id = $_GET['id'] ?? null;       // Lui (che viene seguito)

// Controllo validità
if (!$target_id || $target_id == $me_id) {
    // Non puoi seguire nessuno o te stesso
    header("Location: profile.php"); 
    exit;
}

// 2. CONTROLLO SE SEGUI GIÀ
// Nota: Controlla se la tua tabella si chiama 'followers' o 'follower'. 
// Qui uso 'followers'. Se è diversa, cambiala nella query.
$stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND followed_id = ?");
$stmt->execute([$me_id, $target_id]);
$relazione = $stmt->fetch();

if ($relazione) {
    // 3. SE ESISTE -> SMETTI DI SEGUIRE (UNFOLLOW)
    $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
    $stmt->execute([$me_id, $target_id]);
} else {
    // 4. SE NON ESISTE -> SEGUI (FOLLOW)
    $stmt = $pdo->prepare("INSERT INTO followers (follower_id, followed_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$me_id, $target_id]);
}

// 5. TORNA AL PROFILO DI QUELLA PERSONA
header("Location: profile.php?id=" . $target_id);
exit;