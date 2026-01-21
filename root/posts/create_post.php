<?php
// 1. CONFIGURAZIONE
require_once "../config.php";

// 2. PROTEZIONE: Se non sei loggato, vai al login
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";

// 3. SE HAI PREMUTO "PUBBLICA"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recuperiamo il testo scritto
    $contenuto = trim($_POST['contenuto']);
    
    // Controlliamo che non sia vuoto
    if (!empty($contenuto)) {
        
        // 4. INSERIMENTO NEL DATABASE
        // ATTENZIONE: Se la tua tabella si chiama 'post' (singolare), correggi 'posts' in 'post' qui sotto!
        // Non serve inserire data_pubblicazione se il DB ha "CURRENT_TIMESTAMP" come default (dovrebbe averlo).
        // Non inseriamo workout_id per ora (lasciamo NULL).
        
        $sql = "INSERT INTO post (utente_id, contenuto) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Eseguiamo la query passando l'ID dell'utente loggato e il testo
        if ($stmt->execute([$_SESSION['user_id'], $contenuto])) {
            
            // SUCCESSO: Rimandiamo al Feed per vedere il nuovo post
            header("Location: " . BASE_URL . "posts/feed.php");
            exit;
            
        } else {
            $errore = "Errore durante il salvataggio nel database.";
        }
    } else {
        $errore = "Non puoi pubblicare un post vuoto!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Post - GymBruh</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="container" style="margin-top: 50px; max-width: 600px; margin-left: auto; margin-right: auto;">
        
        <h2>Scrivi un nuovo post ✍️</h2>
        
        <?php if($errore): ?>
            <p style="color: red; font-weight: bold;"><?php echo $errore; ?></p>
        <?php endif; ?>

        <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <form method="POST">
                
                <label>Cosa hai allenato oggi?</label><br>
                
                <textarea name="contenuto" rows="5" placeholder="Es: Oggi ho distrutto i pettorali..." style="width: 100%; padding: 10px; margin-top: 5px; box-sizing: border-box;" required></textarea>
                
                <br><br>

                <button type="submit" class="btn" style="padding: 10px 20px; cursor: pointer;">PUBBLICA</button>
                
                <a href="<?php echo BASE_URL; ?>posts/feed.php" style="margin-left: 15px;">Annulla</a>
            </form>
        </div>

    </div>

    <?php include "../includes/footer.php"; ?>

</body>
</html>