<?php
// 1. CONFIGURAZIONE (SEMPRE PRIMA DI TUTTO)
require_once "../config.php";

$errore = "";

// 2. LOGICA PHP (Gestione del click su "Registrati")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Pulizia input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $bio = $_POST['bio']; 
    $avatar_default = 'avatar1.png'; // Avatar predefinito

    // Controllo campi vuoti
    if (empty($username) || empty($password)) {
        $errore = "Compila username e password!";
    } else {
        // Controllo se esiste già l'utente
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $errore = "Questo username è già preso. Scegline un altro.";
        } else {
            // CREAZIONE UTENTE
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password_hash, bio, avatar) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$username, $password_hash, $bio, $avatar_default])) {
                // REGISTRAZIONE OK -> Manda al login con messaggio di successo
                header("Location: login.php?msg=ok");
                exit;
            } else {
                $errore = "Errore generico nel database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrati - GymBruh</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="container" style="margin-top: 50px; max-width: 500px; margin-left: auto; margin-right: auto;">
        
        <h2 style="text-align: center;">Unisciti al Team 💪</h2>
        
        <?php if ($errore): ?>
            <p style="color: red; text-align: center; font-weight: bold;"><?php echo $errore; ?></p>
        <?php endif; ?>

        <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <form method="POST">
                <label>Username</label><br>
                <input type="text" name="username" style="width: 100%; padding: 8px;" required><br><br>

                <label>Password</label><br>
                <input type="password" name="password" style="width: 100%; padding: 8px;" required><br><br>

                <label>Breve Bio (Opzionale)</label><br>
                <textarea name="bio" placeholder="Es: Amo il calisthenics..." style="width: 100%; padding: 8px;" rows="3"></textarea><br><br>

                <button type="submit" class="btn" style="width: 100%; padding: 10px; cursor: pointer;">REGISTRATI</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 15px;">
            <p>Hai già un account? <a href="login.php">Accedi qui</a></p>
        </div>

    </div>

    <?php include "../includes/footer.php"; ?>

</body>
</html>