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

// Costruiamo l'URL corrente per dire a Like e Commenti di tornare QUI e non al feed
// Usiamo urlencode per evitare problemi con i caratteri speciali
$current_page_url = "view_post.php?id=" . $workout_id . "&tipo=" . $tipo;
$encoded_back_url = urlencode($current_page_url);

// 1. RECUPERIAMO DATI POST
$sql_post = "SELECT id, contenuto, img1, img2, img3, data_pubblicazione FROM post WHERE workout_id = ?";
$stmt_post = $pdo->prepare($sql_post);
$stmt_post->execute([$workout_id]);
$post_data = $stmt_post->fetch();

if (!$post_data) { die("Post non trovato."); }

$post_id = $post_data['id']; 
$commento_utente = $post_data['contenuto'] ?? "Nessuna descrizione.";

// 2. RECUPERIAMO I DETTAGLI TECNICI
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

// 3. RECUPERO DATI SOCIAL
// A. Like totali
$stmt_likes = $pdo->prepare("SELECT COUNT(*) FROM like_post WHERE post_id = ?");
$stmt_likes->execute([$post_id]);
$num_likes = $stmt_likes->fetchColumn();

// B. Ho messo like?
$stmt_me = $pdo->prepare("SELECT COUNT(*) FROM like_post WHERE post_id = ? AND utente_id = ?");
$stmt_me->execute([$post_id, $current_user_id]);
$liked_by_me = $stmt_me->fetchColumn() > 0;

// C. Commenti
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
    <style>
        .social-section { margin-top: 30px; border-top: 1px solid #ddd; paddingTop: 20px; }
        .comment-row { display: flex; gap: 10px; margin-bottom: 15px; }
        .comment-avatar { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        .comment-bubble { background: #f0f2f5; padding: 10px 15px; border-radius: 15px; font-size: 0.9em; flex: 1; }
    </style>
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<div class="container" style="max-width: 600px; margin-top: 30px;">

    <h2>Dettagli: <?= ucfirst($tipo) ?> 📊</h2>

    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; background-color: #fffcf5;">
        <h3 style="margin-top: 0; font-size: 1.1em;">Note dell'utente:</h3>
        <p style="font-size: 1.1em; line-height: 1.6; font-style: italic; color: #333;">
            <?= nl2br(htmlspecialchars($commento_utente)) ?>
        </p>
    </div>

    <?php 
        $post_imgs = [];
        if (!empty($post_data['img1'])) $post_imgs[] = $post_data['img1'];
        if (!empty($post_data['img2'])) $post_imgs[] = $post_data['img2'];
        if (!empty($post_data['img3'])) $post_imgs[] = $post_data['img3'];
    ?>

    <?php if (count($post_imgs) > 0): ?>
        <div class="card" style="padding: 10px; margin-bottom: 20px; display: flex; gap: 10px; overflow-x: auto;">
            <?php foreach ($post_imgs as $img): ?>
                <img src="<?= BASE_URL ?>root/assets/img/post/<?= htmlspecialchars($img) ?>" 
                     style="height: 200px; border-radius: 8px; border: 1px solid #ddd; cursor: pointer;"
                     onclick="window.open(this.src, '_blank');">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (count($dettagli) > 0): ?>
        <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #ddd; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f4f4f4;">
                    <tr>
                        <?php if ($tipo === 'palestra'): ?>
                            <th style="padding:12px; text-align:left;">Esercizio</th>
                            <th style="padding:12px;">Sets</th>
                            <th style="padding:12px;">Reps</th>
                            <th style="padding:12px;">Kg</th>
                        <?php elseif ($tipo === 'nuoto'): ?>
                            <th style="padding:12px; text-align:left;">Stile</th>
                            <th style="padding:12px;">Distanza</th>
                            <th style="padding:12px;">Tempo</th>
                        <?php elseif ($tipo === 'corsa'): ?>
                            <th style="padding:12px; text-align: center;">Distanza (Km)</th>
                            <th style="padding:12px; text-align: center;">Tempo Totale</th>
                            <th style="padding:12px; text-align: center;">Ritmo (min/km)</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dettagli as $row): ?>
                        <tr style="border-bottom: 1px solid #eee; background: white;">
                            <?php if ($tipo === 'palestra'): ?>
                                <td style="padding: 12px;">
                                    <strong><?= htmlspecialchars($row['name']); ?></strong><br>
                                    <small style="color:#888;"><?= htmlspecialchars($row['muscles']); ?></small>
                                </td>
                                <td style="padding:12px; text-align:center;"><?= $row['sets']; ?></td>
                                <td style="padding:12px; text-align:center;"><?= $row['reps']; ?></td>
                                <td style="padding:12px; text-align:center;"><?= ($row['peso'] > 0) ? $row['peso'] : '-'; ?></td>
                            <?php elseif ($tipo === 'nuoto'): ?>
                                <td style="padding: 12px;"><strong><?= htmlspecialchars($row['stile']); ?></strong></td>
                                <td style="padding:12px; text-align:center;"><?= $row['distanza_m']; ?> m</td>
                                <td style="padding:12px; text-align:center;"><?= gmdate("i:s", $row['tempo_secondi']); ?> min</td>
                            <?php elseif ($tipo === 'corsa'): ?>
                                <td style="padding: 12px; text-align:center; font-size: 1.2em; font-weight: bold;"><?= $row['distanza_km']; ?> km</td>
                                <td style="padding:12px; text-align:center;"><?= gmdate("H:i:s", $row['tempo_secondi']); ?></td>
                                <td style="padding:12px; text-align:center;">
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

    <div class="card" style="padding: 20px; margin-top: 20px;">
        
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
            <?php 
                $cuore_icon = $liked_by_me ? "❤️" : "🤍"; 
                $cuore_style = $liked_by_me ? "color: #e0245e; font-weight:bold;" : "color: #555;";
            ?>
            <a href="like.php?id=<?= $post_id ?>&back=<?= $encoded_back_url ?>" style="text-decoration: none; font-size: 1.2em; <?= $cuore_style ?>">
                <span style="font-size: 1.5em; vertical-align: middle;"><?= $cuore_icon ?></span> 
                <?= $num_likes ?> Mi piace
            </a>
            
            <span style="color: #999;">•</span>
            
            <span style="color: #555; font-size: 1.1em;">
                💬 <?= count($lista_commenti) ?> Commenti
            </span>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

        <?php if (count($lista_commenti) > 0): ?>
            <?php foreach ($lista_commenti as $comm): ?>
                <div class="comment-row">
                    <a href="../user/profile.php?id=<?= $comm['user_id'] ?>">
                        <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($comm['avatar']) ?>" class="comment-avatar">
                    </a>
                    <div class="comment-bubble">
                        <a href="../user/profile.php?id=<?= $comm['user_id'] ?>" style="text-decoration: none; color: #333; font-weight: bold;">
                            <?= htmlspecialchars($comm['username']) ?>
                        </a>
                        <p style="margin: 5px 0;"><?= nl2br(htmlspecialchars($comm['testo'])) ?></p>
                        <small style="color: #999; font-size: 0.8em;"><?= date("d/m H:i", strtotime($comm['data_commento'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #999; font-style: italic;">Nessun commento ancora. Sii il primo!</p>
        <?php endif; ?>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= $_SESSION['avatar'] ?? 'default.png' ?>" class="comment-avatar">
            <form action="add_comment.php" method="POST" style="flex: 1; display: flex; gap: 10px;">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                
                <input type="hidden" name="back" value="<?= $current_page_url ?>">

                <input type="text" name="testo" placeholder="Scrivi un commento..." required 
                       style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none;">
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 0 15px; border-radius: 20px; cursor: pointer;">➤</button>
            </form>
        </div>

    </div>

</div>

<br>

<a href="feed.php" 
   style="display: inline-block; padding: 8px 12px; background: #007bff; color: #fff; border-radius: 4px; text-decoration: none; margin-bottom: 15px; transition: background 0.3s;">
   ← Torna al Feed
</a>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>