<?php
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$sql = "SELECT p.*, u.username, u.avatar
        FROM post p
        JOIN users u ON p.utente_id = u.id
        ORDER BY p.data_pubblicazione DESC";

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Post - GymBruh</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
</head>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">

    <h2 style="margin-top: 20px;">Feed Allenamenti</h2>

    <?php if (count($posts) === 0): ?>
        <p>Nessun post. Scrivine uno tu!</p>
    <?php endif; ?>

    <?php foreach ($posts as $post): ?>
        <div class="card post" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background: #fff; border-radius: 8px;">

            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <img
                    src="<?php echo BASE_URL; ?>assets/img/avatars/<?php echo htmlspecialchars($post['avatar']); ?>"
                    alt="Avatar"
                    style="width:40px; height:40px; border-radius:50%; object-fit: cover;"
                >

                <span style="font-weight: bold;">
                    <?= htmlspecialchars($post['username']) ?>
                </span>

                <small style="color:#999; margin-left:auto;">
                    <?= date("d/m H:i", strtotime($post['data_pubblicazione'])) ?>
                </small>
            </div>

            <p style="font-size:1.1em; line-height:1.5;">
                <?= nl2br(htmlspecialchars($post['contenuto'])) ?>
            </p>

            <div style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">
                <button class="btn">Like</button>
                <button class="btn">Commenta</button>
            </div>

        </div>
    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

</body>
</html>
