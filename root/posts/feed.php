
<?php
// posts/feed.php
require_once __DIR__ . "/../config.php"; 

// 1. PROTEZIONE: Se non sei loggato, vai al login
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. QUERY INTELLIGENTE
// Questa query recupera TUTTO in un colpo solo:
// - Dati del post e dell'utente
// - Tipo di allenamento (se esiste)
// - Numero totale di like
// - Se TU hai messo like (liked_by_me)
// - Numero totale di commenti
$sql = "SELECT p.*, u.username, u.avatar, w.tipo, w.id as workout_real_id,
        (SELECT COUNT(*) FROM like_post WHERE post_id = p.id) as num_likes,
        (SELECT COUNT(*) FROM like_post WHERE post_id = p.id AND utente_id = $user_id) as liked_by_me,
        (SELECT COUNT(*) FROM commento WHERE post_id = p.id) as num_comments
        FROM post p 
        JOIN users u ON p.utente_id = u.id 
        LEFT JOIN workouts w ON p.workout_id = w.id
        ORDER BY p.data_pubblicazione DESC";

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container" style="max-width: 700px; margin-top: 20px;">

    <h2>Feed Allenamenti 🔥</h2>

    <?php if (count($posts) === 0): ?>
        <div style="text-align: center; margin-top: 40px;">
            <p>Tutto tace... Sii il primo a pubblicare un allenamento!</p>
            <a href="create_post.php" class="btn">Crea Post</a>
        </div>
    <?php endif; ?>

    <?php foreach ($posts as $post): ?>
        <div class="card post" style="margin-bottom: 25px; padding: 20px; border: 1px solid #ddd; background: #fff; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">

            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                <img src="<?php echo BASE_URL; ?>root/assets/img/avatars/<?php echo htmlspecialchars($post['avatar']); ?>"
                     style="width:50px; height:50px; border-radius:50%; object-fit: cover; border: 2px solid #eee;">
                
                <div>
                    <div style="font-weight: bold; font-size: 1.1em;"><?= htmlspecialchars($post['username']) ?></div>
                    <small style="color: #999;"><?= date("d/m/Y H:i", strtotime($post['data_pubblicazione'])) ?></small>
                </div>

                <?php if (!empty($post['tipo'])): ?>
                    <div style="margin-left: auto; background: #f0f2f5; padding: 5px 12px; border-radius: 20px; font-size: 0.85em; font-weight: bold; color: #555;">
                        <?php 
                            if($post['tipo'] == 'palestra') echo "🏋️ Palestra";
                            elseif($post['tipo'] == 'corsa') echo "🏃 Corsa";
                            elseif($post['tipo'] == 'nuoto') echo "🏊 Nuoto";
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <p style="font-size: 1.1em; line-height: 1.6; color: #333; margin-bottom: 20px;">
                <?= nl2br(htmlspecialchars($post['contenuto'])) ?>
            </p>

            <div class="actions" style="border-top: 1px solid #eee; padding-top: 15px; display: flex; align-items: center; gap: 20px;">
                
                <?php 
                    // Logica colore cuore
                    $cuore_icon = ($post['liked_by_me'] > 0) ? "❤️" : "🤍"; 
                    $cuore_style = ($post['liked_by_me'] > 0) ? "color: #e0245e; font-weight:bold;" : "color: #555;";
                ?>
                <a href="like.php?id=<?= $post['id'] ?>" style="text-decoration: none; font-size: 1.1em; <?= $cuore_style ?>">
                    <span style="font-size: 1.3em; vertical-align: middle;"><?= $cuore_icon ?></span> 
                    <?= $post['num_likes'] ?>
                </a>

                <a href="view_comment.php?id=<?= $post['id'] ?>" style="text-decoration: none; color: #555; font-size: 1.1em;">
                    <span style="font-size: 1.3em; vertical-align: middle;">💬</span> 
                    <?= $post['num_comments'] ?>
                </a>

                <?php if (!empty($post['workout_real_id'])): ?>
                    <a href="view_post.php?id=<?= $post['workout_real_id'] ?>&tipo=<?= $post['tipo'] ?>" 
                       style="margin-left: auto; background: #007bff; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9em;">
                       🔎 Vedi Scheda
                    </a>
                <?php endif; ?>

            </div>

            <div style="margin-top: 15px; background: #f9f9f9; padding: 10px; border-radius: 8px;">
                <form action="add_comment.php" method="POST" style="display: flex; gap: 10px;">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <input type="text" name="testo" placeholder="Scrivi un commento..." required 
                           style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.9em;">
                    <button type="submit" style="padding: 0 20px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        ➤
                    </button>
                </form>
            </div>

        </div>
    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>