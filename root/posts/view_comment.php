<?php
// posts/view_comments.php
require_once "../config.php";

// 1. PROTEZIONE
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

// 2. PRENDIAMO L'ID DEL POST
$post_id = $_GET['id'] ?? null;
if (!$post_id) die("Post non trovato.");

// 3. QUERY PER IL POST ORIGINALE
$stmt_post = $pdo->prepare("SELECT contenuto FROM post WHERE id = ?");
$stmt_post->execute([$post_id]);
$post_originale = $stmt_post->fetch();

// 4. QUERY PER I COMMENTI
$sql = "SELECT c.*, u.username, u.avatar 
        FROM commento c 
        JOIN users u ON c.utente_id = u.id 
        WHERE c.post_id = ? 
        ORDER BY c.data_commento ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$commenti = $stmt->fetchAll();
?>

<?php
require_once "../config.php";

// 1. PROTEZIONE
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

// 2. PRENDIAMO L'ID DEL POST
$post_id = $_GET['id'] ?? null;
if (!$post_id) die("Post non trovato.");

// 3. QUERY PER IL POST ORIGINALE
$stmt_post = $pdo->prepare("SELECT contenuto FROM post WHERE id = ?");
$stmt_post->execute([$post_id]);
$post_originale = $stmt_post->fetch();

// 4. QUERY PER I COMMENTI
$sql = "SELECT c.*, u.username, u.avatar 
        FROM commento c 
        JOIN users u ON c.utente_id = u.id 
        WHERE c.post_id = ? 
        ORDER BY c.data_commento ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$commenti = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Commenti - GymBruh</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<div class="container">

    <!-- Post originale -->
    <div class="post-preview">
        <small>Stai commentando il post:</small>
        <p>"<?= nl2br(htmlspecialchars($post_originale['contenuto'] ?? '')) ?>"</p>
    </div>

    <h3>Commenti (<?= count($commenti) ?>)</h3>

    <!-- Card commenti -->
    <div class="comments-card">
        <?php if (count($commenti) > 0): ?>
            <?php foreach ($commenti as $c): ?>
                <div class="comment-row">
                    <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($c['avatar']) ?>" class="comment-avatar">
                    <div class="comment-content">
                        <div>
                            <span class="comment-username"><?= htmlspecialchars($c['username']) ?></span>
                            <span class="comment-date"><?= date("d/m H:i", strtotime($c['data_commento'])) ?></span>
                        </div>
                        <div class="comment-text"><?= nl2br(htmlspecialchars($c['testo'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-comments">Ancora nessun commento. Rompi il ghiaccio!</div>
        <?php endif; ?>

        <form action="add_comment.php" method="POST" class="comment-form">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <input type="text" name="testo" placeholder="Scrivi la tua risposta..." required>
            <button type="submit">Invia</button>
        </form>
    </div>
    <a href="feed.php" class="back-feed">← Torna al Feed</a>

</div>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>


