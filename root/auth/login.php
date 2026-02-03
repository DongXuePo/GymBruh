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

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];

        header("Location: " . BASE_URL . "root/posts/feed.php");
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
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2>Accedi 🔐</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'ok'): ?>
            <div class="success">Registrazione completata! Ora accedi.</div>
        <?php endif; ?>

        <?php if ($errore): ?>
            <div class="error"><?= $errore ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">ACCEDI</button>
        </form>

        <div class="auth-link">
            Non sei iscritto? <a href="register.php">Registrati</a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
</body>
</html>
