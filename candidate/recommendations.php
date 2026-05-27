<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\Recommender;
use App\Session;
use App\UserRepository;

Session::start();
Auth::requireLogin('candidate');

$userId    = Session::userId();
$results   = Recommender::jobsForCandidate($userId);
$isPremium = UserRepository::isPremium($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recommended jobs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Recommended jobs</h1>
    <nav>
        <a href="../candidate/dashboard.php">Dashboard</a>
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
        <p>No matches yet. Try completing your profile.</p>
    <?php else: ?>
        <ul class="job-list">
            <?php foreach ($results as $r): $job = $r['job']; ?>
                <li>
                    <a href="../job_detail.php?id=<?= (int)$job['id'] ?>">
                        <strong><?= htmlspecialchars($job['title']) ?></strong>
                    </a>
                    — <?= htmlspecialchars($job['company_name'] ?? '') ?>
                    (<?= htmlspecialchars($job['location']) ?>, <?= htmlspecialchars($job['work_mode']) ?>)
                    <span class="muted">match score: <?= (int)$r['score'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
