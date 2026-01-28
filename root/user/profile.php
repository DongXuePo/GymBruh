<?php
require_once __DIR__ . "/../config.php";

// 1. PROTEZIONE
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$logged_user_id = $_SESSION['user_id']; // Chi sta guardando
$profile_id = $_GET['id'] ?? $logged_user_id; // Chi viene guardato (se manca ID, sono io)

$errore = "";
$successo = "";
$is_me = ($logged_user_id == $profile_id); // True se sto guardando il mio profilo

// 2. GESTIONE CAMBIO AVATAR (Solo se sono io)
if ($is_me && isset($_POST['cambia_avatar']) && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $estensione = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $estensioni_permesse = ['jpg','jpeg','png','gif'];
        if (in_array($estensione, $estensioni_permesse)) {
            $nuovo_nome = "avatar_" . $logged_user_id . "_" . time() . "." . $estensione;
            $destinazione = __DIR__ . "/../assets/img/avatars/" . $nuovo_nome;
            if (move_uploaded_file($file['tmp_name'], $destinazione)) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$nuovo_nome, $logged_user_id]);
                $_SESSION['avatar'] = $nuovo_nome; 
                $successo = "Avatar aggiornato!";
            } else { $errore = "Errore upload."; }
        } else { $errore = "Formato non valido."; }
    }
}

// 3. GESTIONE BIO (Solo se sono io)
if ($is_me && isset($_POST['salva_bio'])) {
    $nuova_bio = trim($_POST['bio']);
    $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->execute([$nuova_bio, $logged_user_id]);
    $successo = "Bio aggiornata!";
}

// 4. RECUPERA DATI PROFILO
// Nota: qui usi 'following_id' per contare, quindi la colonna si chiama così!
$sql_user = "SELECT u.*, 
            (SELECT COUNT(*) FROM post WHERE utente_id = u.id) as tot_posts,
            (SELECT COUNT(*) FROM follower WHERE following_id = u.id) as followers_count,
            (SELECT COUNT(*) FROM follower WHERE follower_id = u.id) as following_count
            FROM users u WHERE u.id = ?";
$stmt = $pdo->prepare($sql_user);
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) die("Utente non trovato.");

// 5. SE NON SONO IO: CONTROLLO SE LO SEGUO GIÀ
$ti_seguo = false;
if (!$is_me) {
    // *** CORREZIONE QUI SOTTO ***
    // 1. Uso SELECT * (invece di id)
    // 2. Uso following_id (invece di followed_id) perché è così nel tuo DB
    $stmt = $pdo->prepare("SELECT * FROM follower WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$logged_user_id, $profile_id]);
    $ti_seguo = $stmt->fetch() ? true : false;
}

// 6. RECUPERA POST DELL'UTENTE VISUALIZZATO
$sql_posts = "SELECT p.*, w.tipo, w.id as workout_real_id,
              (SELECT COUNT(*) FROM like_post WHERE post_id = p.id) as num_likes,
              (SELECT COUNT(*) FROM commento WHERE post_id = p.id) as num_comments
              FROM post p 
              LEFT JOIN workouts w ON p.workout_id = w.id
              WHERE p.utente_id = ?
              ORDER BY p.data_pubblicazione DESC";
$stmt_posts = $pdo->prepare($sql_posts);
$stmt_posts->execute([$profile_id]);
$my_posts = $stmt_posts->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo di <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
    <style>
        .stat-box { text-align: center; flex: 1; border-right: 1px solid #ddd; }
        .stat-box:last-child { border-right: none; }
        .stat-number { font-size: 1.5em; font-weight: bold; color: #333; }
        .stat-label { font-size: 0.8em; color: #777; text-transform: uppercase; }
        .btn-follow { background: #0095f6; color: white; width: 100%; margin-top: 10px; }
        .btn-unfollow { background: #efefef; color: black; border: 1px solid #ccc; width: 100%; margin-top: 10px; }
    </style>
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container" style="max-width: 800px; margin-top: 30px;">
    
    <?php if ($successo): ?><div style="background:#ddffdd; color:green; padding:10px; border-radius:5px; margin-bottom:15px;"><?= $successo ?></div><?php endif; ?>
    <?php if ($errore): ?><div style="background:#ffdddd; color:red; padding:10px; border-radius:5px; margin-bottom:15px;"><?= $errore ?></div><?php endif; ?>

    <div class="card" style="padding: 20px; display: flex; gap: 20px;">
        
        <div style="text-align: center; width: 150px;">
            <img src="../assets/img/avatars/<?= htmlspecialchars($user['avatar']) ?>"
                 style="width:120px; height:120px; border-radius:50%; object-fit:cover; border: 4px solid #f0f0f0;">
            
            <?php if ($is_me): ?>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
                    <label for="file-upload" class="btn" style="font-size: 0.8em; padding: 5px; cursor:pointer;">📷 Cambia</label>
                    <input id="file-upload" type="file" name="avatar" style="display:none;" onchange="this.form.submit()">
                    <input type="hidden" name="cambia_avatar" value="1">
                </form>
            <?php endif; ?>
        </div>

        <div style="flex: 1;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0;"><?= htmlspecialchars($user['username']) ?></h2>
                
                <?php if (!$is_me): ?>
                    <a href="follow.php?id=<?= $user['id'] ?>" class="btn <?= $ti_seguo ? 'btn-unfollow' : 'btn-follow' ?>">
                        <?= $ti_seguo ? "Smetti di seguire" : "Segui" ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <p style="color:#888; font-size:0.9em;">Iscritto dal <?= date("d/m/Y", strtotime($user['created_at'])) ?></p>

            <div style="display: flex; background: #f9f9f9; padding: 10px; border-radius: 8px; margin: 15px 0;">
                <div class="stat-box">
                    <div class="stat-number"><?= $user['tot_posts'] ?></div>
                    <div class="stat-label">Post</div>
                </div>
                <div class="stat-box">
                    <a href="lista_user.php?id=<?= $user['id'] ?>&type=follower" style="text-decoration: none; color: inherit;">
                        <div class="stat-number"><?= $user['followers_count'] ?></div>
                        <div class="stat-label">Follower</div>
                    </a>
                </div>
                <div class="stat-box">
                    <a href="lista_user.php?id=<?= $user['id'] ?>&type=following" style="text-decoration: none; color: inherit;">
                        <div class="stat-number"><?= $user['following_count'] ?></div>
                        <div class="stat-label">Seguiti</div>
                    </a>
                </div>
            </div>

            <?php if ($is_me): ?>
                <form method="POST">
                    <textarea name="bio" rows="2" style="width:100%; border:1px solid #ddd; padding:5px;"><?= htmlspecialchars($user['bio']) ?></textarea>
                    <button type="submit" name="salva_bio" class="btn" style="font-size:0.8em; margin-top:5px;">Salva Bio</button>
                </form>
            <?php else: ?>
                <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <hr style="margin: 30px 0; border-top: 1px solid #eee;">
    <h3>Allenamenti di <?= htmlspecialchars($user['username']) ?></h3>

    <?php foreach ($my_posts as $post): ?>
        <div class="card" style="padding: 15px; margin-bottom: 15px; border-left: 4px solid #333;">
            <div style="display:flex; justify-content:space-between;">
                <strong><?= date("d/m/Y", strtotime($post['data_pubblicazione'])) ?></strong>
                <?php if(!empty($post['tipo'])) echo "<span style='background:#eee; padding:2px 8px; border-radius:10px; font-size:0.8em;'>".strtoupper($post['tipo'])."</span>"; ?>
            </div>
            <p><?= nl2br(htmlspecialchars($post['contenuto'])) ?></p>
            
            <?php if (!empty($post['workout_real_id'])): ?>
                <a href="../posts/view_post.php?id=<?= $post['workout_real_id'] ?>&tipo=<?= $post['tipo'] ?>" style="color:#007bff; text-decoration:none;">🔎 Vedi Scheda</a>
            <?php endif; ?>

            <?php if ($is_me): ?>
                <a href="../posts/cancella_post.php?id=<?= $post['id'] ?>" style="color:red; float:right;" onclick="return confirm('Eliminare?')">🗑️</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <?php if (count($my_posts) == 0): ?>
        <div style="text-align: center; padding: 40px; background: #fff; border: 2px dashed #ddd; border-radius: 12px; margin-top: 20px;">
            <h3 style="color: #333;">Nessun allenamento trovato 😢</h3>
            
            <?php if ($is_me): ?>
                <p style="color: #666; margin-bottom: 25px;">Inizia subito a tracciare i tuoi progressi!</p>
                <a href="../posts/create_post.php" class="btn" style="padding: 12px 25px; font-size: 1.1em;">
                    💪 Inizia Ora
                </a>
            <?php else: ?>
                <p style="color: #666;">Questo utente non ha ancora pubblicato nulla.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>