<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Prendiamo l'ID del post dal link
$post_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

// --- MODIFICA FONDAMENTALE ---
// Controlliamo se c'è un indirizzo di ritorno specificato nell'URL
// Se c'è (es: view_post.php?id=5...), usiamo quello. Altrimenti usiamo 'feed.php'.
$redirect_url = $_GET['back'] ?? 'feed.php'; 
// -----------------------------

if ($post_id) {
    // 1. Controlliamo se il like esiste già
    $stmt = $pdo->prepare("SELECT id FROM like_post WHERE post_id = ? AND utente_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existing_like = $stmt->fetch();

    if ($existing_like) {
        // 2. SE ESISTE -> LO TOGLIAMO (Unlike)
        $delete = $pdo->prepare("DELETE FROM like_post WHERE id = ?");
        $delete->execute([$existing_like['id']]);
    } else {
        // 3. SE NON ESISTE -> LO AGGIUNGIAMO (Like)
        $insert = $pdo->prepare("INSERT INTO like_post (post_id, utente_id, data_like) VALUES (?, ?, NOW())");
        $insert->execute([$post_id, $user_id]);
    }
}

// Torniamo alla pagina dinamica (o Feed o Dettaglio)
header("Location: " . $redirect_url);
exit;