<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recuperiamo i dati
    $km = $_POST['km'];
    $minuti = $_POST['minuti']; // L'utente scrive minuti
    $descrizione = $_POST['descrizione'];

    if (!empty($km) && !empty($descrizione)) {
        
        // CONVERSIONE: Minuti -> Secondi (per il DB)
        $tempo_secondi = !empty($minuti) ? ($minuti * 60) : 0;

        try {
            $pdo->beginTransaction();

            // A. CREA WORKOUT MADRE
            $stmt = $pdo->prepare("INSERT INTO workouts (utente_id, tipo, data) VALUES (?, 'corsa', NOW())");
            $stmt->execute([$_SESSION['user_id']]);
            $workout_id = $pdo->lastInsertId();

            // B. SALVA DETTAGLIO CORSA
            // La tabella vuole: workout_id, distanza_km, tempo_secondi
            $stmt_corsa = $pdo->prepare("INSERT INTO workout_corsa (workout_id, distanza_km, tempo_secondi) VALUES (?, ?, ?)");
            $stmt_corsa->execute([$workout_id, $km, $tempo_secondi]);

            // C. CREA POST SOCIAL
            $stmt_post = $pdo->prepare("INSERT INTO post (utente_id, contenuto, workout_id, data_pubblicazione) VALUES (?, ?, ?, NOW())");
            $stmt_post->execute([$_SESSION['user_id'], $descrizione, $workout_id]);

            $pdo->commit();
            header("Location: " . BASE_URL . "root/posts/feed.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errore = "Errore: " . $e->getMessage();
        }
    } else {
        $errore = "Inserisci almeno i Km e la descrizione!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Workout Corsa</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container" style="max-width: 600px; margin-top: 30px;">
        <h2>Registra Corsa 🏃</h2>
        <?php if($errore): ?><p style="color: red;"><?php echo $errore; ?></p><?php endif; ?>

        <div class="card" style="padding: 20px;">
            <form method="POST">
                
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Distanza (Km)</label>
                        <input type="number" name="km" step="0.01" placeholder="es. 5.5" style="width: 100%; padding: 10px; font-size: 1.2em;" required>
                    </div>
                    
                    <div style="flex: 1;">
                        <label>Durata (Minuti)</label>
                        <input type="number" name="minuti" placeholder="es. 45" style="width: 100%; padding: 10px; font-size: 1.2em;">
                    </div>
                </div>

                <hr>
                
                <h3>Descrizione Social</h3>
                <textarea name="descrizione" rows="3" placeholder="Dove hai corso? Sensazioni?" style="width: 100%; padding: 8px;" required></textarea>
                
                <br><br>
                <button type="submit" class="btn" style="width: 100%;">PUBBLICA CORSA</button>
            </form>
        </div>
        
        <br>
        <a href="<?php echo BASE_URL; ?>root/posts/create_post.php">← Indietro</a>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>