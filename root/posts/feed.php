<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../includes/header.php";
?>


<div class="container">

<?php
$posts = [
    [
        'username' => 'dong_fit',
        'content' => 'Allenamento gambe distruttivo oggi. Squat e affondi.',
        'likes' => 12,
        'comments' => 4
    ],
    [
        'username' => 'gymbro99',
        'content' => 'Cardio + addome. Mai saltare.',
        'likes' => 8,
        'comments' => 1
    ]
];
?>

<?php foreach ($posts as $post): ?>
    <div class="card post">

        <!-- HEADER POST -->
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <img src="https://via.placeholder.com/40"
                 alt="avatar"
                 style="width:40px; height:40px; border-radius:50%;">
            <span class="user"><?= htmlspecialchars($post['username']) ?></span>
        </div>

        <!-- CONTENUTO -->
        <p style="line-height:1.4; margin-bottom:10px;">
            <?= htmlspecialchars($post['content']) ?>
        </p>

        <!-- AZIONI -->
        <div class="actions" style="display:flex; align-items:center; gap:10px;">
            <button class="btn">Like</button>
            <button class="btn">Commenta</button>
            <span style="color:#666;">
                <?= $post['likes'] ?> likes · <?= $post['comments'] ?> commenti
            </span>
        </div>

    </div>
<?php endforeach; ?>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>

