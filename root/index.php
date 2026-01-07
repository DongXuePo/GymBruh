<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/includes/header.php";
?>

<div class="container">

    <!-- BOX CREAZIONE POST -->
    <?php include __DIR__ . "/posts/create_post_form.php"; ?>

    <!-- FEED -->
    <?php include __DIR__ . "/posts/feed.php"; ?>

</div>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
