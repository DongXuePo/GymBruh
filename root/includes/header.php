<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* ===== HEADER / NAVBAR ===== */


* {
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
}


.navbar {
    background: linear-gradient(90deg, #111, #1a1a1a);
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
}

.navbar-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* LOGO */
.logo {
    font-size: 1.4rem;
    font-weight: 900;
    letter-spacing: 1px;
    color: #fff;
}


.logo-accent {
    color: #ff6b00;
}

/* NAV */
.navbar-nav {
    display: flex;
    align-items: center;
    gap: 22px;
}

.navbar-nav a {
    color: #eaeaea;
    text-decoration: none;
    font-weight: 500;
    position: relative;
    padding: 4px 0;
    transition: color 0.2s ease;
}

.navbar-nav a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    width: 0;
    height: 2px;
    background: #ff6b00;
    transition: width 0.25s ease;
}

.navbar-nav a:hover {
    color: #ff6b00;
}

.navbar-nav a:hover::after {
    width: 100%;
}

/* AVATAR */
.navbar-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ff6b00;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.navbar-avatar:hover {
    transform: scale(1.08);
    box-shadow: 0 0 0 3px rgba(255,107,0,0.3);
}
</style>




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
