<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";

// GESTIONE SALVATAGGIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recuperiamo i dati SINGOLI (non sono più array)
    $stile = $_POST['stile']; 
    $distanza = $_POST['distanza'];
    $tempo = $_POST['tempo'];
    $descrizione = $_POST['descrizione'];

    // Controllo campi obbligatori
    if (!empty($stile) && !empty($distanza) && !empty($descrizione)) {
        
        try {
            $pdo->beginTransaction();

            // A. CREA WORKOUT MADRE (Genera l'ID)
            $stmt = $pdo->prepare("INSERT INTO workouts (utente_id, tipo, data) VALUES (?, 'nuoto', NOW())");
            $stmt->execute([$_SESSION['user_id']]);
            $workout_id = $pdo->lastInsertId();

            // B. SALVA IL DETTAGLIO NUOTO (Singolo inserimento)
            // Se il tempo è vuoto, mettiamo 0
            $tempo_finale = !empty($tempo) ? $tempo : 0;

            $stmt_dettaglio = $pdo->prepare("INSERT INTO workout_nuoto (workout_id, stile, distanza_m, tempo_secondi) VALUES (?, ?, ?, ?)");
            $stmt_dettaglio->execute([$workout_id, $stile, $distanza, $tempo_finale]);

            // C. CREA POST SOCIAL
            $stmt_post = $pdo->prepare("INSERT INTO post (utente_id, contenuto, workout_id, data_pubblicazione) VALUES (?, ?, ?, NOW())");
            $stmt_post->execute([$_SESSION['user_id'], $descrizione, $workout_id]);

            $pdo->commit();
            header("Location: " . BASE_URL . "root/posts/feed.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            // Se l'errore è "Duplicate entry", diamo un messaggio più chiaro (anche se ora non dovrebbe succedere)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errore = "Errore: Hai già registrato i dettagli per questo allenamento.";
            } else {
                $errore = "Errore tecnico: " . $e->getMessage();
            }
        }
    } else {
        $errore = "Compila stile, distanza e descrizione!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Workout Nuoto</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container" style="max-width: 600px; margin-top: 30px;">
        <h2>Registra Sessione Nuoto 🏊</h2>
        <?php if($errore): ?><p style="color: red;"><?php echo $errore; ?></p><?php endif; ?>

        <div class="card" style="padding: 20px;">
            <form method="POST">
                
                <label>Stile:</label>
                <select name="stile" style="width: 100%; padding: 8px; margin-bottom: 10px;" required>
                    <option value="">-- Seleziona --</option>
                    <option value="Stile Libero">Stile Libero (Crawl)</option>
                    <option value="Dorso">Dorso</option>
                    <option value="Rana">Rana</option>
                    <option value="Delfino">Delfino</option>
                    <option value="Misto">Misto</option>
                    <option value="Tavoletta">Tavoletta / Gambe</option>
                </select>

                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Distanza Totale (m)</label>
                        <input type="number" name="distanza" placeholder="es. 1000" style="width: 100%; padding: 8px;" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Tempo Totale (sec)</label>
                        <input type="number" name="tempo" placeholder="es. 1800" style="width: 100%; padding: 8px;">
                    </div>
                </div>

                <hr>
                
                <h3>Descrizione Social</h3>
                <textarea name="descrizione" rows="3" placeholder="Com'era l'acqua? Sensazioni?" style="width: 100%; padding: 8px;" required></textarea>
                
                <br><br>
                <button type="submit" class="btn" style="width: 100%;">PUBBLICA</button>
            </form>
        </div>
        
        <br>
        <a href="<?php echo BASE_URL; ?>root/posts/create_post.php">← Indietro</a>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>