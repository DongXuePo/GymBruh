//register.php

<?php
require_once "../config.php";

$errore = "";

// SE IL MODULO È STATO INVIATO (METODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $bio = $_POST['bio']; 
    $avatar_default = 'avatar1.png'; // Foto di default (bisogna fare ancora scelta delle foto)

    // Controllo campi vuoti
    if (empty($username) || empty($password)) {
        $errore = "Compila username e password!";
    } else {
        // CONTROLLO SE ESISTE GIÀ L'UTENTE
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?"); //prepara la query (non la lancia subito, mettendo ? come placeholder (cioè verrà cambiato in futuro inserendo un altro dato))
        $stmt->execute([$username]); // mette $username al posto di ?
        
        if ($stmt->rowCount() > 0) {
            $errore = "Cambiare username";
        } else {
            //CREAZIONE UTENTE

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password_hash, bio, avatar) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$username, $password_hash, $bio, $avatar_default])) {
                header("Location: ../index.php");
                exit;
            } else {
                $errore = "Errore nel database. Riprova.";
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
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Unisciti al Team 💪</h2>
        
        <?php if ($errore): ?>
            <p style="color: red;"><?php echo $errore; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <textarea name="bio" placeholder="Scrivi una breve bio..."></textarea><br><br>
            <button type="submit">REGISTRATI</button>
        </form>

        <p>Hai già un account? <a href="login.php">Accedi</a></p>
        <p><a href="../index.php">Torna alla Home</a></p>
    </div>
</body>
</html>