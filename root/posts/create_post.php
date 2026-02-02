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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>root/assets/style.css">
    <style>
        /* Stile specifico per il menu a griglia */
        .grid-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .menu-card {
            background: white;
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #333;
            border: 2px solid #eee;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 180px;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            border-color: #333;
        }

        .icon {
            font-size: 3.5em;
            margin-bottom: 15px;
            display: block;
        }

        .label {
            font-size: 1.2em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container" style="max-width: 800px; margin-top: 40px; text-align: center;">
    
    <h1>Nuovo Allenamento 💪</h1>
    <p style="color: #666; font-size: 1.1em;">Che cosa hai fatto oggi?</p>

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