<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$post_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($post_id) {
    
    // controllo che il post esiste E se appartiene all'utente loggato.
    $stmt = $pdo->prepare("SELECT id, utente_id FROM post WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post) {
        if ($post['utente_id'] == $user_id) {
            
            
            // cancella like
            $stmt_likes = $pdo->prepare("DELETE FROM like_post WHERE post_id = ?");
            $stmt_likes->execute([$post_id]);

            // cancella commenti
            $stmt_comments = $pdo->prepare("DELETE FROM commento WHERE post_id = ?");
            $stmt_comments->execute([$post_id]);

            // cabcella post
            $stmt_delete = $pdo->prepare("DELETE FROM post WHERE id = ?");
            $stmt_delete->execute([$post_id]);

        } else {
            die("Non hai i permessi per cancellare questo post");
        }
    }
}

header("Location: " . BASE_URL . "root/user/profile.php");
exit;