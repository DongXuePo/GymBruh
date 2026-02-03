<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$target_id = $_GET['id'] ?? $_SESSION['user_id'];
$type = $_GET['type'] ?? 'follower';

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$target_id]);
$target_user = $stmt->fetch();

if (!$target_user) die("Utente non trovato");

if ($type === 'follower') {
    $titolo = "Follower di " . htmlspecialchars($target_user['username']);
    $sql = "SELECT u.* FROM users u 
            JOIN follower f ON u.id = f.follower_id 
            WHERE f.following_id = ?";
} else {
    $titolo = "Persone seguite da " . htmlspecialchars($target_user['username']);
    $sql = "SELECT u.* FROM users u 
            JOIN follower f ON u.id = f.following_id 
            WHERE f.follower_id = ?";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$target_id]);
$users_list = $stmt->fetchAll();

require_once "../includes/header.php";
?>





    <div class="container userlist-container">
    
    <a href="profile.php?id=<?= $target_id ?>" class="back-link">← Torna al profilo</a>
    <h3 class="userlist-title"><?= $titolo ?></h3>

    <div class="card userlist-card">
        
        <?php if (count($users_list) > 0): ?>
            <?php foreach ($users_list as $user): ?>
                <div class="userlist-row">
                    <div class="userlist-info">
                        <a href="profile.php?id=<?= $user['id'] ?>">
                            <img src="../assets/img/avatars/<?= htmlspecialchars($user['avatar']) ?>" class="userlist-avatar">
                        </a>
                        
                        <a href="profile.php?id=<?= $user['id'] ?>" class="userlist-username">
                            <?= htmlspecialchars($user['username']) ?>
                        </a>
                    </div>

                    <a href="profile.php?id=<?= $user['id'] ?>" class="userlist-btn">
                        Profilo >
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="userlist-empty">
                Nessun utente trovato in questa lista.
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>




