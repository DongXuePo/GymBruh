<?php
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$logged_user_id = $_SESSION['user_id'];
$profile_id = $_GET['id'] ?? $logged_user_id;
$is_me = ($logged_user_id == $profile_id);

$errore = "";
$successo = "";

if ($is_me && isset($_POST['cambia_avatar']) && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $estensione = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $consentite = ['jpg','jpeg','png','gif'];

        if (in_array($estensione, $consentite)) {
            $nome = "avatar_" . $logged_user_id . "_" . time() . "." . $estensione;
            $dest = __DIR__ . "/../assets/img/avatars/" . $nome;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$nome, $logged_user_id]);
                $_SESSION['avatar'] = $nome;
                $successo = "Avatar aggiornato!";
            } else {
                $errore = "Errore upload.";
            }
        } else {
            $errore = "Formato non valido.";
        }
    }
}

if ($is_me && isset($_POST['salva_bio'])) {
    $bio = trim($_POST['bio']);
    $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->execute([$bio, $logged_user_id]);
    $successo = "Bio aggiornata!";
}

$stmt = $pdo->prepare("
    SELECT u.*,
    (SELECT COUNT(*) FROM post WHERE utente_id = u.id) AS tot_posts,
    (SELECT COUNT(*) FROM follower WHERE following_id = u.id) AS followers_count,
    (SELECT COUNT(*) FROM follower WHERE follower_id = u.id) AS following_count
    FROM users u WHERE u.id = ?
");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) die("Utente non trovato.");

$ti_seguo = false;
if (!$is_me) {
    $stmt = $pdo->prepare("SELECT 1 FROM follower WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$logged_user_id, $profile_id]);
    $ti_seguo = $stmt->fetch() ? true : false;
}

$stmt = $pdo->prepare("
    SELECT p.*, w.tipo, w.id AS workout_real_id
    FROM post p
    LEFT JOIN workouts w ON p.workout_id = w.id
    WHERE p.utente_id = ?
    ORDER BY p.data_pubblicazione DESC
");
$stmt->execute([$profile_id]);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo di <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">

    <style>

    </style>
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container profile-wrapper">

    <?php if ($successo): ?>
        <div class="alert-success"><?= $successo ?></div>
    <?php endif; ?>

    <?php if ($errore): ?>
        <div class="alert-error"><?= $errore ?></div>
    <?php endif; ?>

    <div class="card profile-card-local">

        <div class="profile-avatar-local">
            <img src="../assets/img/avatars/<?= htmlspecialchars($user['avatar']) ?>">

            <?php if ($is_me): ?>
                <form method="POST" enctype="multipart/form-data">
                    <label for="file-upload" class="btn">📷 Cambia</label>
                    <input id="file-upload" type="file" name="avatar" hidden onchange="this.form.submit()">
                    <input type="hidden" name="cambia_avatar" value="1">
                </form>
            <?php endif; ?>
        </div>

        <div style="flex:1;">
            <div class="profile-header-local">
                <h2><?= htmlspecialchars($user['username']) ?></h2>

                <?php if (!$is_me): ?>
                    <a href="follow.php?id=<?= $user['id'] ?>" class="btn <?= $ti_seguo ? 'btn-unfollow' : 'btn-follow' ?>">
                        <?= $ti_seguo ? "Smetti di seguire" : "Segui" ?>
                    </a>
                <?php endif; ?>
            </div>

            <p class="profile-date-local">
                Iscritto dal <?= date("d/m/Y", strtotime($user['created_at'])) ?>
            </p>

            <div class="stats-local">
                <div class="stat-box">
                    <div class="stat-number"><?= $user['tot_posts'] ?></div>
                    <div>Post</div>
                </div>
                <div class="stat-box">
                    <a href="lista_user.php?id=<?= $user['id'] ?>&type=follower">
                        <div class="stat-number"><?= $user['followers_count'] ?></div>
                        <div>Follower</div>
                    </a>
                </div>
                <div class="stat-box">
                    <a href="lista_user.php?id=<?= $user['id'] ?>&type=following">
                        <div class="stat-number"><?= $user['following_count'] ?></div>
                        <div>Seguiti</div>
                    </a>
                </div>
            </div>

            <?php if ($is_me): ?>
                <form method="POST">
                    <textarea name="bio" rows="2" class="bio-textarea-local"><?= htmlspecialchars($user['bio']) ?></textarea>
                    <button type="submit" name="salva_bio" class="btn">Salva Bio</button>
                </form>
            <?php else: ?>
                <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <h3>Allenamenti di <?= htmlspecialchars($user['username']) ?></h3>

    <?php foreach ($posts as $post): ?>
        <div class="card post-card-local">
            <div class="post-header-local">
                <strong><?= date("d/m/Y", strtotime($post['data_pubblicazione'])) ?></strong>
                <?php if ($post['tipo']): ?>
                    <span class="post-type-local"><?= strtoupper($post['tipo']) ?></span>
                <?php endif; ?>
            </div>

            <p><?= nl2br(htmlspecialchars($post['contenuto'])) ?></p>

            <?php if ($post['workout_real_id']): ?>
                <a class="post-link-local" href="../posts/view_post.php?id=<?= $post['workout_real_id'] ?>&tipo=<?= $post['tipo'] ?>">
                    🔎 Vedi Scheda
                </a>
            <?php endif; ?>

            <?php if ($is_me): ?>
                <a class="post-delete-local" href="../posts/cancella_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Eliminare?')">🗑️</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (count($posts) === 0): ?>
        <div class="empty-posts-local">
            <h3>Nessun allenamento trovato 😢</h3>
            <?php if ($is_me): ?>
                <p>Inizia subito a tracciare i tuoi progressi!</p>
                <a href="../posts/create_post.php" class="btn">💪 Inizia Ora</a>
            <?php else: ?>
                <p>Questo utente non ha ancora pubblicato nulla.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>
