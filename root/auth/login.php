<?php

require_once "../config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "posts/feed.php");
    exit;
}

$errore = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Query per cercare l'utente
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Verifica password
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // LOGIN RIUSCITO: SALVIAMO LA SESSIONE
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];

        header("Location: " . BASE_URL . "posts/feed.php");
        exit;

    } else {
        $errore = "Username o Password sbagliati.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accedi - GymBruh</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="container" style="margin-top: 50px; max-width: 400px; margin-left: auto; margin-right: auto;">
        
        <h2 style="text-align: center;">Accedi 🔐</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'ok'): ?>
            <p style="color: green; text-align: center;">Registrazione ok! Ora accedi.</p>
        <?php endif; ?>

        <?php if ($errore): ?>
            <p style="color: red; text-align: center; font-weight: bold;"><?php echo $errore; ?></p>
        <?php endif; ?>

        <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <form method="POST">
                <label>Username</label><br>
                <input type="text" name="username" placeholder="Il tuo username" style="width: 100%; padding: 8px;" required><br><br>

                <label>Password</label><br>
                <input type="password" name="password" placeholder="La tua password" style="width: 100%; padding: 8px;" required><br><br>

                <button type="submit" class="btn" style="width: 100%; padding: 10px; cursor: pointer;">ACCEDI</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 15px;">
            <p>Non sei iscritto? <a href="register.php">Registrati</a></p>
        </div>
        
    </div>

    <?php include "../includes/footer.php"; ?>

</body>
</html>