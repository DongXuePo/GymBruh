<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$workout_id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? 'palestra';

if (!$workout_id) { die("Allenamento non trovato."); }

// 1. RECUPERIAMO IL COMMENTO SOCIAL (Dalla tabella 'post')
// Cerchiamo il post collegato a questo workout ID
$sql_commento = "SELECT contenuto FROM post WHERE workout_id = ?";
$stmt_commento = $pdo->prepare($sql_commento);
$stmt_commento->execute([$workout_id]);
$post_data = $stmt_commento->fetch();
$commento_utente = $post_data['contenuto'] ?? "Nessuna descrizione.";

// 2. RECUPERIAMO I DETTAGLI TECNICI (Come prima)
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

require_once "../includes/header.php";
?>

<div class="container" style="max-width: 600px; margin-top: 30px;">
    <a href="feed.php">← Torna al Feed</a>
    
    <h2>Dettagli: <?php echo ucfirst($tipo); ?> 📊</h2>

    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; background-color: #fffcf5;">
        <h3 style="margin-top: 0; font-size: 1.1em;">Note dell'utente:</h3>
        <p style="font-size: 1.1em; line-height: 1.6; font-style: italic; color: #333;">
            <?= nl2br(htmlspecialchars($commento_utente)) ?>
        </p>
    </div>

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
                                <td style="padding:12px; text-align:center;">
                                    <?= gmdate("i:s", $row['tempo_secondi']); ?> min
                                </td>

                            <?php elseif ($tipo === 'corsa'): ?>
                                <td style="padding: 12px; text-align:center; font-size: 1.2em; font-weight: bold;">
                                    <?= $row['distanza_km']; ?> km
                                </td>
                                <td style="padding:12px; text-align:center;">
                                    <?= gmdate("H:i:s", $row['tempo_secondi']); ?>
                                </td>
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

    <?php else: ?>
        <p>Nessun dettaglio tecnico trovato.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/footer.php"; ?>