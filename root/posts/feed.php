<?php
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.*, u.username, u.avatar, w.tipo, w.id as workout_real_id,
        (SELECT COUNT(*) FROM like_post WHERE post_id = p.id) as num_likes,
        (SELECT COUNT(*) FROM like_post WHERE post_id = p.id AND utente_id = $user_id) as liked_by_me,
        (SELECT COUNT(*) FROM commento WHERE post_id = p.id) as num_comments
        FROM post p
        JOIN users u ON p.utente_id = u.id
        LEFT JOIN workouts w ON p.workout_id = w.id
        ORDER BY p.data_pubblicazione DESC";

$posts = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Feed - GymBruh</title>

<style>

</style>


</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">

    <h2 class="feed-title">Feed Allenamenti 🔥</h2>

    <?php foreach ($posts as $post): ?>
        <div class="card">

            <div class="post-header">
                <a href="../user/profile.php?id=<?= $post['utente_id'] ?>">
                    <img class="post-avatar"
                         src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($post['avatar']) ?>">
                </a>

                <div class="post-user">
                    <a href="../user/profile.php?id=<?= $post['utente_id'] ?>">
                        <?= htmlspecialchars($post['username']) ?>
                    </a>
                    <div class="post-date">
                        <?= date("d/m/Y H:i", strtotime($post['data_pubblicazione'])) ?>
                    </div>
                </div>

                <?php if ($post['tipo']): ?>
                    <div class="post-type">
                        <?= ucfirst($post['tipo']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['contenuto'])) ?>
            </div>

            <?php
            $imgs = array_filter([
                $post['img1'] ?? null,
                $post['img2'] ?? null,
                $post['img3'] ?? null
            ]);
            ?>

            <?php if ($imgs): ?>
                <div class="post-images">
                    <?php foreach ($imgs as $img): ?>
                        <img src="<?= BASE_URL ?>root/assets/img/post/<?= htmlspecialchars($img) ?>"
                            onclick="window.open(this.src)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <div class="post-actions">
                <a href="like.php?id=<?= $post['id'] ?>"
                   class="<?= $post['liked_by_me'] ? 'liked' : '' ?>">
                    ❤️ <?= $post['num_likes'] ?>
                </a>

                <a href="view_comment.php?id=<?= $post['id'] ?>">
                    💬 <?= $post['num_comments'] ?>
                </a>

                <?php if ($post['workout_real_id']): ?>
                    <a class="view-workout"
                       href="view_post.php?id=<?= $post['workout_real_id'] ?>&tipo=<?= $post['tipo'] ?>">
                        🔎 Vedi Scheda
                    </a>
                <?php endif; ?>
            </div>

            <div class="comment-box">
                <form action="add_comment.php" method="POST">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <input type="text" name="testo" placeholder="Scrivi un commento..." required>
                    <button>➤</button>
                </form>
            </div>

        </div>
    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

</body>
</html>
