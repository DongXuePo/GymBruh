<?php
// 1. CONFIGURAZIONE
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";

// 2. RECUPERO LISTA ESERCIZI
try {
    $stmt_list = $pdo->query("SELECT * FROM list_gym_workout ORDER BY name ASC");
    $lista_esercizi = $stmt_list->fetchAll();
} catch (PDOException $e) {
    die("Errore database: " . $e->getMessage());
}

// 3. GESTIONE DEL SALVATAGGIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $esercizi_ids = $_POST['esercizio_id'] ?? []; 
    $sets_list = $_POST['sets'] ?? [];
    $reps_list = $_POST['reps'] ?? [];
    $peso_list = $_POST['peso'] ?? []; 
    $descrizione = trim($_POST['descrizione']);

    if (!empty($esercizi_ids[0]) && !empty($descrizione)) {
        
        // --- GESTIONE IMMAGINI ---
        $immagini_caricate = [null, null, null]; 
        $cartella_destinazione = __DIR__ . "/../assets/img/post/";
        //verifico se il form ha inviato delle immagini
        if (isset($_FILES['immagini'])) {
            $files = $_FILES['immagini'];
            $count = count($files['name']);
            for ($i = 0; $i < min($count, 3); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $estensione = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    $permessi = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($estensione, $permessi)) {
                        $nuovo_nome = "post_" . $_SESSION['user_id'] . "_" . time() . "_" . $i . "." . $estensione;
                        //spostiamo la il file dalla cartella temporanea che crea php alla cartella vera e propria
                        if (move_uploaded_file($files['tmp_name'][$i], $cartella_destinazione . $nuovo_nome)) {
                            $immagini_caricate[$i] = $nuovo_nome;
                        }
                    }
                }
            }
        }

        try {
            //serve per non avere allenamenti vuoti o altro in caso di errori imprevisti
            $pdo->beginTransaction();

            // Workout
            $stmt_w = $pdo->prepare("INSERT INTO workouts (utente_id, tipo, data) VALUES (?, 'palestra', NOW())");
            $stmt_w->execute([$_SESSION['user_id']]);
            $workout_id = $pdo->lastInsertId();

            // Dettagli Esercizi
            $stmt_dettaglio = $pdo->prepare("INSERT INTO workout_palestra_esercizi (workout_id, esercizio_id, sets, reps, peso) VALUES (?, ?, ?, ?, ?)");
            
            for ($i = 0; $i < count($esercizi_ids); $i++) {
                if (!empty($esercizi_ids[$i])) {
                    $stmt_dettaglio->execute([$workout_id, $esercizi_ids[$i], $s, $r, $p]);
                }
            }

            // pubblicazione post
            $stmt_post = $pdo->prepare("INSERT INTO post (utente_id, contenuto, workout_id, img1, img2, img3, data_pubblicazione) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt_post->execute([$_SESSION['user_id'], $descrizione, $workout_id, $immagini_caricate[0], $immagini_caricate[1], $immagini_caricate[2]]);

            //salva
            $pdo->commit();
            header("Location: " . BASE_URL . "root/posts/feed.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errore = "Errore: " . $e->getMessage();
        }
    } else {
        $errore = "Inserisci almeno un esercizio e la descrizione!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Workout Palestra</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="container workout-container">
        <h2>Registra Allenamento: Palestra 🏋️</h2>
        
        <?php if($errore): ?>
            <div class="alert-error" style="background:#ffdddd; color:red; padding:10px; border-radius:8px; margin-bottom:15px;">
                <?php echo $errore; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" id="workoutForm" enctype="multipart/form-data">
                
                <h3>Scheda Allenamento</h3>

                <div id="esercizi-container">
                    
                    <div class="esercizio-row">
                        
                        <div class="mb-20">
                            <label>Esercizio:</label>
                            <select name="esercizio_id[]" required>
                                <option value="">-- Seleziona --</option>
                                <?php foreach ($lista_esercizi as $es): ?>
                                    <option value="<?php echo $es['id']; ?>">
                                        <?php echo htmlspecialchars($es['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="workout-row input-flex">
                            <div class="flex-1">
                                <label>Sets</label>
                                <input type="number" name="sets[]" placeholder="4" min="1" required>
                            </div>
                            <div class="flex-1">
                                <label>Reps</label>
                                <input type="number" name="reps[]" placeholder="10" min="1" required>
                            </div>
                            <div class="flex-1">
                                <label>Kg</label>
                                <input type="number" name="peso[]" placeholder="0" step="0.5" min="0">
                            </div>
                        </div>

                    </div>

                </div>

                <button type="button" onclick="aggiungiEsercizio()" class="btn" style="background: #e0e0e0; color: #333; width: 100%; margin-bottom: 20px;">
                    + Aggiungi un altro esercizio
                </button>

                <hr>

                <h3>Descrizione Social</h3>
                <textarea name="descrizione" rows="3" placeholder="Com'è andata? Sensazioni?" required></textarea>

                <br><br>
                <label>📸 Aggiungi foto (max 3):</label>
                <input type="file" name="immagini[]" multiple accept="image/*" style="margin-top: 5px;">

                <br><br>
                <button type="submit" class="btn" style="width: 100%;">PUBBLICA WORKOUT</button>
            </form>
        </div>
        
        <a href="../posts/create_post.php" class="workout-back">← Indietro</a>
    </div>

    <?php include "../includes/footer.php"; ?>

    <script>
        function aggiungiEsercizio() {
            const container = document.getElementById('esercizi-container');
            const righe = container.getElementsByClassName('esercizio-row');
            const primaRiga = righe[0];
            
            const nuovaRiga = primaRiga.cloneNode(true);
            
            const select = nuovaRiga.querySelector('select');
            if (select) select.value = "";
            
            const inputs = nuovaRiga.querySelectorAll('input');
            inputs.forEach(input => input.value = "");

            container.appendChild(nuovaRiga);
        }
    </script>

</body>
</html>