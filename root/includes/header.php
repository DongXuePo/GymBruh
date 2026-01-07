<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>GymBruh</title>
    <link rel="stylesheet" href="<?= isset($base_url) ? $base_url.'assets/style.css' : '../assets/style.css' ?>">
</head>
<body>

<header class="navbar">
    <div class="container">
        <strong>GymBruh</strong>
        <nav>
            <a href="<?= isset($base_url) ? $base_url.'posts/feed.php' : '../posts/feed.php' ?>">Feed</a>
            <a href="<?= isset($base_url) ? $base_url.'user/profile.php' : '../user/profile.php' ?>">Profilo</a>
            <a href="<?= isset($base_url) ? $base_url.'posts/create_post.php' : '../posts/create_post.php' ?>">Nuovo Post</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?= isset($base_url) ? $base_url.'auth/login.php' : '../auth/login.php' ?>">Login</a>
            <?php else: ?>
                <a href="<?= isset($base_url) ? $base_url.'auth/logout.php' : '../auth/logout.php' ?>">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
