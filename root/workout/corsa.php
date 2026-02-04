<?php
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $km = $_POST['km'];
    $minuti = $_POST['minuti'];
    $descrizione = $_POST['descrizione'];

    if (!empty($km) && !empty($descrizione)) {
        
        $tempo_secondi = !empty($minuti) ? ($minuti * 60) : 0;

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

            $stmt = $pdo->prepare("INSERT INTO workouts (utente_id, tipo, data) VALUES (?, 'corsa', NOW())");
            $stmt->execute([$_SESSION['user_id']]);
            $workout_id = $pdo->lastInsertId();

            $stmt_corsa = $pdo->prepare("INSERT INTO workout_corsa (workout_id, distanza_km, tempo_secondi) VALUES (?, ?, ?)");
            $stmt_corsa->execute([$workout_id, $km, $tempo_secondi]);

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
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container workout-container">
        <h2>Registra Corsa 🏃</h2>

        <?php if($errore): ?>
            <p class="workout-error"><?= $errore ?></p>
        <?php endif; ?>

        <div class="card workout-card">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="workout-row">
                    <div class="workout-group">
                        <label>Distanza (Km)</label>
                        <input type="number" name="km" step="0.01" placeholder="es. 5.5"
                               class="workout-input" required>
                    </div>
                    
                    <div class="workout-group">
                        <label>Durata (Minuti)</label>
                        <input type="number" name="minuti" placeholder="es. 45"
                               class="workout-input">
                    </div>
                </div>


                <br>                
                <h3>Descrizione Social</h3>
                <textarea name="descrizione" rows="3"
                          placeholder="Dove hai corso? Sensazioni?"
                          class="workout-textarea" required></textarea>
                
                <br><br>
                <label>Aggiungi foto (max 3):</label>
                <input type="file" name="immagini[]" multiple accept="image/*"
                       class="workout-file">

                <br><br>
                <button type="submit" class="btn" style="width: 100%;">PUBBLICA</button>
            </form>
        </div>
        
        <br>
        <a href="<?= BASE_URL ?>root/posts/create_post.php" class="workout-back">← Indietro</a>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>

</html>