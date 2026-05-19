<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\Recommender;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$results = Recommender::candidatesForEmployer(Session::userId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recommended candidates</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Recommended candidates</h1>
    <nav>
        <a href="../employer/dashboard.php">Dashboard</a>
        <a href="../logout.php">Log out</a>
    </nav>
</header>
<main>
    <p class="muted">Top matches across your active job postings (up to <?= Recommender::TOP_K ?>).</p>

    <?php if (empty($results)): ?>
        <p>No candidate matches yet. Make sure you have at least one active job posting.</p>
    <?php else: ?>
        <ul class="candidate-list">
            <?php foreach ($results as $r): $c = $r['candidate']; ?>
                <li>
                    <a href="../candidate_detail.php?id=<?= (int)$c['user_id'] ?>">
                        <strong><?= htmlspecialchars($c['full_name']) ?></strong>
                    </a>
                    — <?= htmlspecialchars($c['field_of_study']) ?>,
                    <?= (int)$c['years_experience'] ?> yrs
                    <span class="muted">match score: <?= (int)$r['score'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
