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
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f6f6f6, #e0e0e0);
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 70px auto;
        }
        .login-card {
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .login-card:hover {
            transform: translateY(-3px);
        }
        .login-card h2 {
            text-align: center;
            color: #111;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        .login-card input {
            width: 100%;
            padding: 12px 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .login-card input:focus {
            outline: none;
            border-color: #ff6b00;
        }
        .login-card button.btn {
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
        .login-card button.btn:hover {
            background: #e65c00;
        }
        .login-card .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .login-card .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        .login-card .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-card .register-link a {
            color: #ff6b00;
            font-weight: bold;
            text-decoration: none;
        }
        .login-card .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
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

        <div class="register-link">
            Non sei iscritto? <a href="register.php">Registrati</a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
</body>
</html>
