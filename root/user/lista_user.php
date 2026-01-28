<?php
// users/user_list.php
require_once "../config.php";

// 1. PROTEZIONE
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// 2. RECUPERO PARAMETRI
$target_id = $_GET['id'] ?? $_SESSION['user_id']; // Di chi vogliamo vedere la lista?
$type = $_GET['type'] ?? 'follower'; // Cosa vogliamo vedere? 'follower' o 'following'

// 3. RECUPERO NOME UTENTE (Per il titolo della pagina)
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$target_id]);
$target_user = $stmt->fetch();

if (!$target_user) die("Utente non trovato");

// 4. LOGICA DELLA QUERY
// Qui decidiamo se prendere chi TI SEGUE o chi TU SEGUI
if ($type === 'follower') {
    $titolo = "Follower di " . htmlspecialchars($target_user['username']);
    // Prendi gli utenti che hanno messo follow A te (following_id sei tu)
    // Nota: Uso la tabella 'follower' e le colonne 'follower_id' / 'followed_id' (o 'following_id')
    // Adatto al tuo DB: Assumo che 'follower_id' è chi segue, 'followed_id' è chi è seguito.
    $sql = "SELECT u.* FROM users u 
            JOIN follower f ON u.id = f.follower_id 
            WHERE f.following_id = ?";
} else {
    $titolo = "Persone seguite da " . htmlspecialchars($target_user['username']);
    // Prendi gli utenti CHE tu segui (follower_id sei tu)
    $sql = "SELECT u.* FROM users u 
            JOIN follower f ON u.id = f.following_id 
            WHERE f.follower_id = ?";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$target_id]);
$users_list = $stmt->fetchAll();

require_once "../includes/header.php";
?>

<div class="container" style="max-width: 600px; margin-top: 30px;">
    
    <a href="profile.php?id=<?= $target_id ?>">← Torna al profilo</a>
    <h3><?= $titolo ?></h3>

    <div class="card" style="padding: 0; overflow: hidden;">
        
        <?php if (count($users_list) > 0): ?>
            <?php foreach ($users_list as $user): ?>
                
                <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <a href="profile.php?id=<?= $user['id'] ?>">
                            <img src="../assets/img/avatars/<?= htmlspecialchars($user['avatar']) ?>" 
                                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                        </a>
                        
                        <a href="profile.php?id=<?= $user['id'] ?>" style="text-decoration: none; color: #333; font-weight: bold; font-size: 1.1em;">
                            <?= htmlspecialchars($user['username']) ?>
                        </a>
                    </div>

                    <a href="profile.php?id=<?= $user['id'] ?>" class="btn" style="padding: 5px 15px; font-size: 0.9em; background: #eee; color: #333;">
                        Profilo >
                    </a>

                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div style="padding: 20px; text-align: center; color: #777;">
                Nessun utente trovato in questa lista.
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>