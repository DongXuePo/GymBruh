<?php
require_once "../config.php";

$errore = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $bio = $_POST['bio'];
    $avatar_default = 'avatar1.png';

    if (empty($username) || empty($password)) {
        $errore = "Compila username e password!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $errore = "Questo username è già preso. Scegline un altro.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password_hash, bio, avatar) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$username, $password_hash, $bio, $avatar_default])) {
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
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2>Unisciti al Team 💪</h2>

        <?php if ($errore): ?>
            <div class="error"><?= $errore ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <textarea name="bio" placeholder="Breve Bio (opzionale)" rows="3"></textarea>

            <button type="submit" class="btn">REGISTRATI</button>
        </form>

        <div class="auth-link">
            Hai già un account? <a href="login.php">Accedi qui</a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
</body>
</html>
