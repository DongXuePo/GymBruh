<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// se c'è un indirizzo di ritorno specificato usiamo quello, senno feed.php
$redirect_url = $_GET['back'] ?? 'feed.php'; 

if ($post_id) {
    // controllo se il like esiste già
    $stmt = $pdo->prepare("SELECT id FROM like_post WHERE post_id = ? AND utente_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existing_like = $stmt->fetch();

    if ($existing_like) {
        // SE ESISTE LO TOGLIAMO
        $delete = $pdo->prepare("DELETE FROM like_post WHERE id = ?");
        $delete->execute([$existing_like['id']]);
    } else {
        // SE NON ESISTE LO AGGIUNGIAMO 
        $insert = $pdo->prepare("INSERT INTO like_post (post_id, utente_id, data_like) VALUES (?, ?, NOW())");
        $insert->execute([$post_id, $user_id]);
    }
}

header("Location: " . $redirect_url);
exit;