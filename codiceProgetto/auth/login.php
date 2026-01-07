<?php
require_once "../config.php";

// Se l'utente è già loggato, via! Lo mandiamo al profilo
if (isset($_SESSION['user_id'])) {
    header("Location: ../user/profile.php");
    exit;
}

$errore = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
   
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // 3. LOGIN RIUSCITO: SALVIAMO LA SESSIONE
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];

        header("Location: ../user/profile.php");
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
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Accedi 🔐</h2>

        <?php if (isset($_GET['registrazione'])): ?>
            <p style="color: green;">Registrazione ok! Ora accedi.</p>
        <?php endif; ?>

        <?php if ($errore): ?>
            <p style="color: red;"><?php echo $errore; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">ACCEDI</button>
        </form>

        <p>Non sei iscritto? <a href="register.php">Registrati</a></p>
        <p><a href="../index.php">Torna alla Home</a></p>
    </div>
</body>
</html>