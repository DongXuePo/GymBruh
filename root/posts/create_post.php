<?php
// posts/create_post.php
require_once "../config.php";

// Protezione: Se non sei loggato, via al login
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scegli Allenamento - GymBruh</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="container" style="text-align: center; margin-top: 50px;">
        
        <h2>Che allenamento hai fatto oggi? 💪</h2>
        <p style="color: #666;">Scegli la categoria per continuare:</p>

        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-top: 40px;">
            
            <a href="../workout/palestra.php" class="card" style="text-decoration: none; color: black; padding: 40px; width: 150px; border: 2px solid #ddd; border-radius: 10px; transition: 0.3s;">
                <div style="font-size: 50px;">🏋️</div>
                <h3>Palestra</h3>
            </a>

            <a href="../workout/corsa.php" class="card" style="text-decoration: none; color: black; padding: 40px; width: 150px; border: 2px solid #ddd; border-radius: 10px; transition: 0.3s;">
                <div style="font-size: 50px;">🏃</div>
                <h3>Corsa</h3>
            </a>

            <a href="../workout/nuoto.php" class="card" style="text-decoration: none; color: black; padding: 40px; width: 150px; border: 2px solid #ddd; border-radius: 10px; transition: 0.3s;">
                <div style="font-size: 50px;">🏊</div>
                <h3>Nuoto</h3>
            </a>

        </div>
        
        <br><br><br>
        <a href="feed.php" style="color: #666;">← Torna al Feed</a>
    </div>

    <?php include "../includes/footer.php"; ?>

</body>
</html>