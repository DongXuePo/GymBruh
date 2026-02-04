<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$workout_id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? 'palestra';

if (!$workout_id) { die("Allenamento non trovato."); }

// costruzione url corrente per tornare qui e non al feed
// urlencode per evitare problemi con i caratteri speciali
$current_page_url = "view_post.php?id=" . $workout_id . "&tipo=" . $tipo;
$encoded_back_url = urlencode($current_page_url);

// RECUPERO DATI POST
$sql_post = "SELECT id, contenuto, img1, img2, img3, data_pubblicazione FROM post WHERE workout_id = ?";
$stmt_post = $pdo->prepare($sql_post);
$stmt_post->execute([$workout_id]);
$post_data = $stmt_post->fetch();

if (!$post_data) { die("Post non trovato."); }

$post_id = $post_data['id']; 
$commento_utente = $post_data['contenuto'] ?? "Nessuna descrizione.";

// RECUPERO DETTAGLI TECNICI
$dettagli = [];

if ($tipo === 'palestra') {
    $sql = "SELECT wpe.*, list.name, list.muscles 
            FROM workout_palestra_esercizi wpe
            JOIN list_gym_workout list ON wpe.esercizio_id = list.id
            WHERE wpe.workout_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$workout_id]);
    $dettagli = $stmt->fetchAll();

} elseif ($tipo === 'nuoto') {
    $sql = "SELECT * FROM workout_nuoto WHERE workout_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$workout_id]);
    $dettagli = $stmt->fetchAll();

} elseif ($tipo === 'corsa') {
    $sql = "SELECT * FROM workout_corsa WHERE workout_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$workout_id]);
    $dettagli = $stmt->fetchAll();
}

// RECUPERO DATI SOCIAL
// like totali
$stmt_likes = $pdo->prepare("SELECT COUNT(*) FROM like_post WHERE post_id = ?");
$stmt_likes->execute([$post_id]);
$num_likes = $stmt_likes->fetchColumn();

// controllo se ho messo like
$stmt_me = $pdo->prepare("SELECT COUNT(*) FROM like_post WHERE post_id = ? AND utente_id = ?");
$stmt_me->execute([$post_id, $current_user_id]);
$liked_by_me = $stmt_me->fetchColumn() > 0;

// Commenti
$sql_comments = "SELECT c.testo, c.data_commento, u.username, u.avatar, u.id as user_id 
                 FROM commento c 
                 JOIN users u ON c.utente_id = u.id 
                 WHERE c.post_id = ? 
                 ORDER BY c.data_commento ASC";
$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute([$post_id]);
$lista_commenti = $stmt_comments->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettagli Allenamento - GymBruh</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">



</head>

<body>

<?php require_once "../includes/header.php"; ?>

<div class="container viewpost-container">

    <h2 class="viewpost-title">Dettagli: <?= ucfirst($tipo) ?> 📊</h2>

    <!-- Note utente -->
    <div class="card note-card">
        <h3 class="note-title">Note dell'utente:</h3>
        <p class="note-text">
            <?= nl2br(htmlspecialchars($commento_utente)) ?>
        </p>
    </div>

    <!-- Immagini post -->
    <?php 
        $post_imgs = [];
        if (!empty($post_data['img1'])) $post_imgs[] = $post_data['img1'];
        if (!empty($post_data['img2'])) $post_imgs[] = $post_data['img2'];
        if (!empty($post_data['img3'])) $post_imgs[] = $post_data['img3'];
    ?>
    <?php if (count($post_imgs) > 0): ?>
        <div class="card images-card">
            <?php foreach ($post_imgs as $img): ?>
                <img src="<?= BASE_URL ?>root/assets/img/post/<?= htmlspecialchars($img) ?>" 
                     class="post-img"
                     onclick="window.open(this.src, '_blank');">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Tabella dettagli workout -->
    <?php if (count($dettagli) > 0): ?>
        <div class="card table-card">
            <table class="workout-table">
                <thead>
                    <tr>
                        <?php if ($tipo === 'palestra'): ?>
                            <th class="table-left">Esercizio</th>
                            <th>Sets</th>
                            <th>Reps</th>
                            <th>Kg</th>
                        <?php elseif ($tipo === 'nuoto'): ?>
                            <th class="table-left">Stile</th>
                            <th>Distanza</th>
                            <th>Tempo</th>
                        <?php elseif ($tipo === 'corsa'): ?>
                            <th>Distanza (Km)</th>
                            <th>Tempo Totale</th>
                            <th>Ritmo (min/km)</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dettagli as $row): ?>
                        <tr>
                            <?php if ($tipo === 'palestra'): ?>
                                <td class="table-left">
                                    <strong><?= htmlspecialchars($row['name']); ?></strong><br>
                                    <small class="muscles"><?= htmlspecialchars($row['muscles']); ?></small>
                                </td>
                                <td><?= $row['sets']; ?></td>
                                <td><?= $row['reps']; ?></td>
                                <td><?= ($row['peso'] > 0) ? $row['peso'] : '-'; ?></td>
                            <?php elseif ($tipo === 'nuoto'): ?>
                                <td class="table-left"><strong><?= htmlspecialchars($row['stile']); ?></strong></td>
                                <td><?= $row['distanza_m']; ?> m</td>
                                <td><?= gmdate("i:s", $row['tempo_secondi']); ?> min</td>
                            <?php elseif ($tipo === 'corsa'): ?>
                                <td><?= $row['distanza_km']; ?> km</td>
                                <td><?= gmdate("H:i:s", $row['tempo_secondi']); ?></td>
                                <td>
                                    <?php 
                                        if ($row['distanza_km'] > 0 && $row['tempo_secondi'] > 0) {
                                            $ritmo_sec = $row['tempo_secondi'] / $row['distanza_km'];
                                            echo gmdate("i:s", $ritmo_sec) . " /km";
                                        } else {
                                            echo "-";
                                        }
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Sezione social -->
    <div class="card social-card">

        <div class="social-row">
            <?php $cuore_icon = $liked_by_me ? "❤️" : "🤍"; ?>
            <a href="like.php?id=<?= $post_id ?>&back=<?= $encoded_back_url ?>" class="like-btn">
                <span class="heart"><?= $cuore_icon ?></span> <?= $num_likes ?> Mi piace
            </a>

            <span class="dot">•</span>

            <span class="comments-count">💬 <?= count($lista_commenti) ?> Commenti</span>
        </div>

        <hr>

        <!-- Lista commenti -->
        <?php if (count($lista_commenti) > 0): ?>
            <?php foreach ($lista_commenti as $comm): ?>
                <div class="comment-row">
                    <a href="../user/profile.php?id=<?= $comm['user_id'] ?>">
                        <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($comm['avatar']) ?>" class="comment-avatar">
                    </a>
                    <div class="comment-bubble">
                        <a href="../user/profile.php?id=<?= $comm['user_id'] ?>" class="comment-username">
                            <?= htmlspecialchars($comm['username']) ?>
                        </a>
                        <p><?= nl2br(htmlspecialchars($comm['testo'])) ?></p>
                        <small class="comment-date"><?= date("d/m H:i", strtotime($comm['data_commento'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-comments">Nessun commento ancora. Sii il primo!</p>
        <?php endif; ?>

        <!-- Aggiungi commento -->
        <div class="comment-input-row">
            <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= $_SESSION['avatar'] ?? 'default.png' ?>" class="comment-avatar">
            <form action="add_comment.php" method="POST" class="comment-form">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <input type="hidden" name="back" value="<?= $current_page_url ?>">

                <input type="text" name="testo" placeholder="Scrivi un commento..." required class="comment-input">
                <button type="submit" class="comment-btn">➤</button>
            </form>
        </div>

    </div>
    <a href="feed.php" class="back-feed">← Torna al Feed</a>
</div>





<?php require_once "../includes/footer.php"; ?>
</body>



</html>