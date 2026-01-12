<header class="navbar">
    <div class="container">
        <strong>GymBruh</strong>
        <nav>
            <a href="<?php echo BASE_URL; ?>root/posts/feed.php">Feed</a>
            <a href="<?php echo BASE_URL; ?>root/user/profile.php">Profilo</a>
            <a href="<?php echo BASE_URL; ?>root/posts/create_post.php">Nuovo Post</a>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>root/auth/login.php">Login</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>root/auth/logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</header>