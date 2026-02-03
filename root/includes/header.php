<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>root/assets/style.css">

</head>
<body>
    <header class="navbar">
    <div class="navbar-container">

        <!-- LOGO -->
        <div class="logo">
            Gym<span class="logo-accent">Bruh</span>
        </div>

        <!-- NAV -->
        <nav class="navbar-nav">
            <a href="<?= BASE_URL ?>root/posts/feed.php">Feed</a>
            <a href="<?= BASE_URL ?>root/posts/create_post.php">Nuovo Post</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>root/user/profile.php">Profilo</a>

                <a href="<?= BASE_URL ?>root/user/profile.php">
                    <img
                        src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($_SESSION['avatar'] ?? 'default.png') ?>"
                        alt="Avatar"
                        class="navbar-avatar"
                    >
                </a>

                <a href="<?= BASE_URL ?>root/auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>root/auth/login.php">Login</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

</body>
</html>

