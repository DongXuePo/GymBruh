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
    die("Errore nel caricamento esercizi: " . $e->getMessage());
}

// 3. GESTIONE DEL SALVATAGGIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ora questi sono ARRAY (liste di valori)
    $esercizi_ids = $_POST['esercizio_id']; 
    $sets_list = $_POST['sets'];
    $reps_list = $_POST['reps'];
    
    $descrizione = $_POST['descrizione'];

    // Controlliamo che almeno il primo esercizio e la descrizione ci siano
    if (!empty($esercizi_ids[0]) && !empty($descrizione)) {
        
        try {
            $pdo->beginTransaction();

            // A. Creiamo il WORKOUT GENERICO (La "busta" che contiene tutto)
            $sql_w = "INSERT INTO workouts (utente_id, tipo, data) VALUES (?, 'palestra', NOW())";
            $stmt_w = $pdo->prepare($sql_w);
            $stmt_w->execute([$_SESSION['user_id']]);
            $workout_id = $pdo->lastInsertId();

            // B. Salviamo TUTTI GLI ESERCIZI (Ciclo For)
            $sql_dettaglio = "INSERT INTO workout_palestra_esercizi (workout_id, esercizio_id, sets, reps) VALUES (?, ?, ?, ?)";
            $stmt_dettaglio = $pdo->prepare($sql_dettaglio);

            // Giriamo su tutti gli esercizi aggiunti
            for ($i = 0; $i < count($esercizi_ids); $i++) {
                
                $es_id = $esercizi_ids[$i];
                $s = $sets_list[$i];
                $r = $reps_list[$i];

                // Salviamo solo se l'esercizio è stato selezionato
                if (!empty($es_id)) {
                    $stmt_dettaglio->execute([$workout_id, $es_id, $s, $r]);
                }
            }

            // C. Salviamo il POST SOCIAL
            $sql_post = "INSERT INTO post (utente_id, contenuto, workout_id, data_pubblicazione) VALUES (?, ?, ?, NOW())";
            $stmt_post = $pdo->prepare($sql_post);
            $stmt_post->execute([$_SESSION['user_id'], $descrizione, $workout_id]);

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
    <style>
        .esercizio-row {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="container" style="max-width: 700px; margin-top: 30px;">
        <h2>Registra Allenamento: Palestra 🏋️</h2>
        
        <?php if($errore): ?>
            <p style="color: red;"><?php echo $errore; ?></p>
        <?php endif; ?>

        <div class="card" style="padding: 20px;">
            <form method="POST" id="workoutForm">
                
                <h3>Scheda Allenamento</h3>
                <div id="esercizi-container">
                    
                    <div class="esercizio-row">
                        <label>Esercizio:</label>
                        <select name="esercizio_id[]" style="width: 100%; padding: 8px; margin-bottom: 10px;" required>
                            <option value="">Seleziona</option>
                            <?php foreach ($lista_esercizi as $es): ?>
                                <option value="<?php echo $es['id']; ?>">
                                    <?php echo htmlspecialchars($es['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label>Sets</label>
                                <input type="number" name="sets[]" placeholder="4" style="width: 100%; padding: 8px;" required>
                            </div>
                            <div style="flex: 1;">
                                <label>Reps</label>
                                <input type="number" name="reps[]" placeholder="10" style="width: 100%; padding: 8px;" required>
                            </div>
                        </div>
                    </div>

                </div>

                <button type="button" onclick="aggiungiEsercizio()" class="btn" style="background: #eee; color: #333; width: 100%; margin-bottom: 20px;">
                    + Aggiungi un altro esercizio
                </button>

                <hr>

                <h3>Descrizione Social</h3>
                <textarea name="descrizione" rows="3" placeholder="Com'è andata?" style="width: 100%; padding: 8px;" required></textarea>

                <br><br>
                <button type="submit" class="btn" style="width: 100%;">PUBBLICA WORKOUT</button>
            </form>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>

    <script>
        function aggiungiEsercizio() {
            // 1. Prendo il contenitore
            const container = document.getElementById('esercizi-container');
            
            // 2. Prendo la prima riga come modello
            const primaRiga = container.getElementsByClassName('esercizio-row')[0];
            
            // 3. La clono
            const nuovaRiga = primaRiga.cloneNode(true);
            
            // 4. Pulisco i valori dei campi nella nuova riga (così è vuota)
            const inputs = nuovaRiga.getElementsByTagName('input');
            nuovaRiga.getElementsByTagName('select')[0].value = ""; // Resetta select
            inputs[0].value = ""; // Resetta sets
            inputs[1].value = ""; // Resetta reps
            
            // 5. La aggiungo in fondo
            container.appendChild(nuovaRiga);
        }
    </script>

</body>
</html>