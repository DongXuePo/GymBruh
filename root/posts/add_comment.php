<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Default: se qualcosa va storto, torna al feed
$redirect_url = 'feed.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $testo = trim($_POST['testo']);
    
    // --- MODIFICA FONDAMENTALE ---
    // Recuperiamo l'indirizzo di ritorno dal campo nascosto <input type="hidden" name="back">
    if (isset($_POST['back']) && !empty($_POST['back'])) {
        $redirect_url = $_POST['back'];
    }
    // -----------------------------
    
    if (!empty($post_id) && !empty($testo)) {
        $stmt = $pdo->prepare("INSERT INTO commento (post_id, utente_id, testo, data_commento) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$post_id, $_SESSION['user_id'], $testo]);
    }
}

// Torniamo alla pagina dinamica
header("Location: " . $redirect_url);
exit;