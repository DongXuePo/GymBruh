<?php
require_once "../config.php";

// 1. PROTEZIONE: Se non sei loggato, vai via
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

// 2. RECUPERO DATI
$post_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($post_id) {
    
    // 3. CONTROLLO DI SICUREZZA (Fondamentale!)
    // Verifichiamo se il post esiste E se appartiene all'utente loggato.
    $stmt = $pdo->prepare("SELECT id, utente_id FROM post WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post) {
        if ($post['utente_id'] == $user_id) {
            
            // 4. PULIZIA DEL DATABASE
            // Prima di cancellare il post, dobbiamo cancellare le cose collegate
            // per evitare errori nel database (Foreign Key Constraints)
            
            // A. Cancella tutti i LIKE di questo post
            $stmt_likes = $pdo->prepare("DELETE FROM like_post WHERE post_id = ?");
            $stmt_likes->execute([$post_id]);

            // B. Cancella tutti i COMMENTI di questo post
            $stmt_comments = $pdo->prepare("DELETE FROM commento WHERE post_id = ?");
            $stmt_comments->execute([$post_id]);

            // C. Infine, CANCELLA IL POST
            $stmt_delete = $pdo->prepare("DELETE FROM post WHERE id = ?");
            $stmt_delete->execute([$post_id]);

        } else {
            // Se provi a cancellare un post non tuo
            die("Non hai i permessi per cancellare questo post!");
        }
    }
}

// 5. RITORNO AL PROFILO
// Usiamo header per tornare indietro. 
// Nota: Se vuoi tornare al Feed invece che al profilo, cambia in ../posts/feed.php
header("Location: " . BASE_URL . "root/user/profile.php");
exit;