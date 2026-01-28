<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="navbar">
    <div class="container">

        <!-- LOGO -->
        <div style="font-size:1.3rem; font-weight:800; letter-spacing:0.5px;">
            Gym<span style="color:#ff6b00;">Bruh</span>
        </div>

        <!-- NAV -->
        <nav style="display:flex; align-items:center; gap:20px;">
            <a href="<?= BASE_URL ?>root/posts/feed.php">Feed</a>
            <a href="<?= BASE_URL ?>root/posts/create_post.php">Nuovo Post</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>root/user/profile.php">Profilo</a>

                <!-- AVATAR -->
                <a href="<?= BASE_URL ?>root/user/profile.php">
                    <img src="<?= BASE_URL ?>root/assets/img/avatars/<?= htmlspecialchars($_SESSION['avatar'] ?? 'default.png') ?>"
                         alt="Avatar"
                         style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #ff6b00;">
                </a>

                <a href="<?= BASE_URL ?>root/auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>root/auth/login.php">Login</a>
            <?php endif; ?>
        </nav>

    </div>
</header>
