<?php
require_once __DIR__ . '/bootstrap.php';

use App\JobRepository;
use App\Session;

Session::start();

$keyword = trim($_GET['q'] ?? '');
$jobs    = $keyword === '' ? JobRepository::listAll() : JobRepository::searchByKeyword($keyword);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse jobs</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1>Browse jobs</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if (Session::isLoggedIn()): ?>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <form method="GET" class="search-form">
        <input name="q" placeholder="Search job descriptions..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (empty($jobs)): ?>
        <p>No jobs found<?= $keyword ? ' for "' . htmlspecialchars($keyword) . '"' : '' ?>.</p>
    <?php else: ?>
        <ul class="job-list">
            <?php foreach ($jobs as $job): ?>
                <li>
                    <a href="job_detail.php?id=<?= (int)$job['id'] ?>">
                        <strong><?= htmlspecialchars($job['title']) ?></strong>
                    </a>
                    — <?= htmlspecialchars($job['company_name'] ?? '') ?>
                    (<?= htmlspecialchars($job['location']) ?>, <?= htmlspecialchars($job['work_mode']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>