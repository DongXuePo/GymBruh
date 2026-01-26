<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $testo = trim($_POST['testo']);
    
    if (!empty($post_id) && !empty($testo)) {
        $stmt = $pdo->prepare("INSERT INTO commento (post_id, utente_id, testo, data_commento) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$post_id, $_SESSION['user_id'], $testo]);
    }
}

// Torniamo al Feed (o alla pagina commenti se preferisci)
header("Location: feed.php");
exit;