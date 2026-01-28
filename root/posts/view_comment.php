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

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Commenti - GymBruh</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<!-- CONTENUTO PRINCIPALE -->
<div class="container" style="max-width: 600px; margin-top: 30px;">

    <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #333; margin: 20px 0; border-radius: 4px;">
        <small style="color: #666;">Stai commentando il post:</small>
        <p style="font-style: italic; margin-top: 5px;">
            "<?= nl2br(htmlspecialchars($post_originale['contenuto'] ?? '')) ?>"
        </p>
    </div>

    <h3>Commenti (<?= count($commenti) ?>)</h3>

    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #ddd; border-radius: 8px;">
        <?php if (count($commenti) > 0): ?>
            <?php foreach ($commenti as $c): ?>
                <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; gap: 15px;">
                    <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($c['avatar']) ?>" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div style="margin-bottom: 5px;">
                            <strong><?= htmlspecialchars($c['username']) ?></strong>
                            <small style="color: #999; margin-left: 10px;">
                                <?= date("d/m H:i", strtotime($c['data_commento'])) ?>
                            </small>
                        </div>
                        <div style="color: #333; line-height: 1.4;">
                            <?= nl2br(htmlspecialchars($c['testo'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="padding: 20px; text-align: center; color: #777;">Ancora nessun commento. Rompi il ghiaccio!</p>
        <?php endif; ?>

        <div style="background: #f4f4f4; padding: 15px; border-top: 1px solid #ddd;">
            <form action="add_comment.php" method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <input type="text" name="testo" placeholder="Scrivi la tua risposta..." required 
                       style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" class="btn" style="padding: 0 20px;">Invia</button>
            </form>
        </div>
    </div>

    <br>
    <a href="feed.php" 
       style="display: inline-block; padding: 8px 12px; background: #007bff; color: #fff; border-radius: 4px; text-decoration: none; margin-bottom: 15px; transition: background 0.3s;">
       ← Torna al Feed
    </a>

</div>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>
