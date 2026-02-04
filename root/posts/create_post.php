<?php
require_once __DIR__ . "/../config.php";

// 1. PROTEZIONE
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

<style>
/* ===== CONTAINER ===== */
.container {
    max-width: 800px;
    margin: 50px auto;
    text-align: center;
}

/* ===== TITOLO ===== */
.container h1 {
    font-size: 2.2em;
    margin-bottom: 10px;
}

.container p {
    font-size: 1.1em;
    color: #666;
    margin-bottom: 30px;
}

/* ===== MENU GRIGLIA ===== */
.grid-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 25px;
    justify-items: center;
}

/* ===== CARD ===== */
.menu-card {
    background: #fff;
    padding: 30px 20px;
    border-radius: 14px;
    text-align: center;
    text-decoration: none;
    color: #333;
    border: 2px solid #eee;
    transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 180px;
    width: 100%;
    max-width: 200px;
}

.menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.12);
    border-color: #ff6b00;
}

/* ===== ICON ===== */
.icon {
    font-size: 3.8em;
    margin-bottom: 18px;
    display: block;
}

/* ===== LABEL ===== */
.label {
    font-size: 1.2em;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #333;
}


</style>
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">
    <h1>Nuovo Allenamento 💪</h1>
    <p>Che cosa hai fatto oggi?</p>

    <div class="grid-menu">
        <a href="../workout/palestra.php" class="menu-card">
            <span class="icon">🏋️</span>
            <span class="label">Palestra</span>
        </a>

        <a href="../workout/corsa.php" class="menu-card">
            <span class="icon">🏃</span>
            <span class="label">Corsa</span>
        </a>

        <a href="../workout/nuoto.php" class="menu-card">
            <span class="icon">🏊</span>
            <span class="label">Nuoto</span>
        </a>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>
