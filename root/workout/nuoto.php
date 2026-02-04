<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";

// GESTIONE SALVATAGGIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recuperiamo i dati SINGOLI
    $stile = $_POST['stile']; 
    $distanza = $_POST['distanza'];
    $tempo = $_POST['tempo'];
    $descrizione = $_POST['descrizione'];

    // Controllo campi obbligatori
    if (!empty($stile) && !empty($distanza) && !empty($descrizione)) {
        
        $immagini_caricate = [null, null, null]; 
        
        if (isset($_FILES['immagini'])) {
            $files = $_FILES['immagini'];
            $count = count($files['name']);
            
            for ($i = 0; $i < min($count, 3); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $estensione = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    $permessi = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($estensione, $permessi)) {
                        $nuovo_nome = "post_" . $_SESSION['user_id'] . "_" . time() . "_" . $i . "." . $estensione;
                        $destinazione = __DIR__ . "/../assets/img/post/" . $nuovo_nome;
                        
                        if (move_uploaded_file($files['tmp_name'][$i], $destinazione)) {
                            $immagini_caricate[$i] = $nuovo_nome;
                        }
                    }
                }
            }
        }

        try {
            //serve per non avere allenamenti vuoti o altro in caso di errori imprevisti
            $pdo->beginTransaction();

            // WORKOUT MADRE (Genera l'ID)
            $stmt = $pdo->prepare("INSERT INTO workouts (utente_id, tipo, data) VALUES (?, 'nuoto', NOW())");
            //mette questo al posto del ? 
            $stmt->execute([$_SESSION['user_id']]);
            $workout_id = $pdo->lastInsertId();

            $tempo_finale = !empty($tempo) ? $tempo : 0;

            $stmt_dettaglio = $pdo->prepare("INSERT INTO workout_nuoto (workout_id, stile, distanza_m, tempo_secondi) VALUES (?, ?, ?, ?)");
            $stmt_dettaglio->execute([$workout_id, $stile, $distanza, $tempo_finale]);

            // CREA POST
            $sql_post = "INSERT INTO post (utente_id, contenuto, workout_id, img1, img2, img3, data_pubblicazione) 
                         VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt_post = $pdo->prepare($sql_post);
            
            $stmt_post->execute([
                $_SESSION['user_id'], 
                $descrizione, 
                $workout_id,
                $immagini_caricate[0],
                $immagini_caricate[1],
                $immagini_caricate[2]
            ]);

            //salva
            $pdo->commit();
            header("Location: " . BASE_URL . "root/posts/feed.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
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

    <div class="container workout-container">
        <h2 class="mb-20">Registra Sessione Nuoto 🏊</h2>
        <?php if($errore): ?>
            <p class="workout-error"><?= $errore ?></p>
        <?php endif; ?>

        <div class="card workout-card">
            <form method="POST" enctype="multipart/form-data">

                <label>Stile:</label>
                <select name="stile" class="workout-select" required>
                    <option value="">-- Seleziona --</option>
                    <option value="Stile Libero">Stile Libero (Crawl)</option>
                    <option value="Dorso">Dorso</option>
                    <option value="Rana">Rana</option>
                    <option value="Delfino">Delfino</option>
                    <option value="Misto">Misto</option>
                    <option value="Tavoletta">Tavoletta / Gambe</option>
                </select>

                <div class="workout-row">
                    <div class="workout-group">
                        <label>Distanza Totale (m)</label>
                        <input type="number" name="distanza" placeholder="es. 1000" class="workout-input" required>
                    </div>
                    <div class="workout-group">
                        <label>Tempo Totale (sec)</label>
                        <input type="number" name="tempo" placeholder="es. 1800" class="workout-input">
                    </div>
                </div>

                <hr>

                <h3>Descrizione Social</h3>
                <textarea name="descrizione" rows="3" placeholder="Com'era l'acqua? Sensazioni?" class="workout-textarea" required></textarea>

                <label>Aggiungi foto (max 3):</label>
                <input type="file" name="immagini[]" multiple accept="image/*" class="workout-file">

<button type="submit" class="btn" style="width: 100%;">PUBBLICA</button>    
            </form>
        </div>

        <a href="<?php echo BASE_URL; ?>root/posts/create_post.php" class="workout-back mt-20">← Indietro</a>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>
