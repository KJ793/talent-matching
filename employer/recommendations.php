<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\Recommender;
use App\Session;
use App\UserRepository;

Session::start();
Auth::requireLogin('employer');

$userId    = Session::userId();
$results   = Recommender::candidatesForEmployer($userId);
$isPremium = UserRepository::isPremium($userId);
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
    <?php if ($isPremium): ?>
        <p class="muted">Showing all <?= count($results) ?> matches (premium membership — unlimited).</p>
    <?php else: ?>
        <p class="muted">Showing your top <?= min(count($results), Recommender::TOP_K) ?> matches.
            <a href="../membership.php">Upgrade to premium</a> for unlimited recommendations.</p>
    <?php endif; ?>

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
