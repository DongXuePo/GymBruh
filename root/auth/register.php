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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f6f6f6, #e0e0e0);
            font-family: Arial, sans-serif;
        }
        .register-container {
            max-width: 450px;
            margin: 60px auto;
        }
        .register-card {
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .register-card:hover {
            transform: translateY(-3px);
        }
        .register-card h2 {
            text-align: center;
            color: #111;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        .register-card input, 
        .register-card textarea {
            width: 100%;
            padding: 12px 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .register-card input:focus,
        .register-card textarea:focus {
            outline: none;
            border-color: #ff6b00;
        }
        .register-card button.btn {
            width: 100%;
            padding: 12px;
            background: #ff6b00;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .register-card button.btn:hover {
            background: #e65c00;
        }
        .register-card .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .register-card .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-card .login-link a {
            color: #ff6b00;
            font-weight: bold;
            text-decoration: none;
        }
        .register-card .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <h2>Unisciti al Team 💪</h2>

        <?php if ($errore): ?>
            <div class="error"><?= $errore ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <textarea name="bio" placeholder="Breve Bio (opzionale)" rows="3" ></textarea>


            <button type="submit" class="btn">REGISTRATI</button>
        </form>

        <div class="login-link">
            Hai già un account? <a href="login.php">Accedi qui</a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
</body>
</html>
