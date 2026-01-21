<?php
require_once __DIR__ . "/../config.php";

// Blocca utenti non loggati
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "root/auth/login.php");
    exit;
}

$errore = "";
$successo = "";

// Gestione cambio avatar
if (isset($_POST['cambia_avatar']) && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $nome_tmp = $file['tmp_name'];
        $nome_file = basename($file['name']);
        $estensione = strtolower(pathinfo($nome_file, PATHINFO_EXTENSION));

        // Controllo estensione
        $estensioni_permesse = ['jpg','jpeg','png','gif'];
        if (!in_array($estensione, $estensioni_permesse)) {
            $errore = "Formato non supportato. Usa JPG, PNG o GIF.";
        } else {
            // Nome unico
            $nuovo_nome = "avatar_" . $_SESSION['user_id'] . "." . $estensione;
            $destinazione = __DIR__ . "/../assets/img/avatars/" . $nuovo_nome;

            if (move_uploaded_file($nome_tmp, $destinazione)) {
                // Aggiorna DB
                $sql = "UPDATE users SET avatar = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nuovo_nome, $_SESSION['user_id']]);

                // Aggiorna sessione
                $_SESSION['avatar'] = $nuovo_nome;
                $successo = "Avatar aggiornato correttamente!";
            } else {
                $errore = "Errore nel caricamento del file.";
            }
        }
    } else {
        $errore = "Errore nell'upload del file.";
    }
}

// Recupera dati utente
$sql = "SELECT username, bio, avatar, created_at FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utente non trovato.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo - GymBruh</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container" style="margin-top:50px; max-width:600px; margin-left:auto; margin-right:auto;">

    <h2>Profilo di <?php echo htmlspecialchars($user['username']); ?></h2>

    <div class="card" style="padding:20px; border:1px solid #ddd; border-radius:8px; display:flex; gap:20px; align-items:center;">
        
        <!-- Avatar -->
        <img src="<?php echo BASE_URL; ?>assets/img/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" 
             alt="Avatar" style="width:100px; height:100px; border-radius:50%; object-fit:cover;">

        <div>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
            <p><strong>Registrato il:</strong> <?php echo date("d/m/Y", strtotime($user['created_at'])); ?></p>

            <!-- Form per cambiare avatar -->
            <form method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                <label for="avatar">Cambia avatar:</label>
                <input type="file" name="avatar" accept="image/*" required>
                <button type="submit" name="cambia_avatar" class="btn" style="margin-left:10px;">Salva</button>
            </form>

            <?php if ($errore): ?>
                <p style="color:red; font-weight:bold;"><?php echo $errore; ?></p>
            <?php endif; ?>

            <?php if ($successo): ?>
                <p style="color:green; font-weight:bold;"><?php echo $successo; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top:20px;">
        <a href="<?php echo BASE_URL; ?>root/posts/feed.php" class="btn">Torna al Feed</a>
        <a href="<?php echo BASE_URL; ?>root/posts/create_post.php" class="btn" style="margin-left:10px;">Nuovo Post</a>
    </div>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>
