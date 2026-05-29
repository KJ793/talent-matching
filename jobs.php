<?php
require_once __DIR__ . '/bootstrap.php';

use App\JobRepository;
use App\Session;

Session::start();

$params = [
    'keyword'            => trim($_GET['q'] ?? ''),
    'work_mode'          => trim($_GET['work_mode'] ?? ''),
    'location'           => trim($_GET['location'] ?? ''),
    'required_education' => trim($_GET['education'] ?? ''),
    'min_experience'     => trim($_GET['min_experience'] ?? ''),
];

$hasFilters = array_filter($params, static fn ($v) => $v !== '');
$jobs       = $hasFilters ? JobRepository::search($params) : JobRepository::listAll();

$workModes  = ['Remote', 'On-site', 'Hybrid'];
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
        <input name="q" placeholder="Keyword (title, description, skills)..."
               value="<?= htmlspecialchars($params['keyword']) ?>">
        <select name="work_mode">
            <option value="">Any work mode</option>
            <?php foreach ($workModes as $mode): ?>
                <option value="<?= $mode ?>" <?= $params['work_mode'] === $mode ? 'selected' : '' ?>>
                    <?= $mode ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input name="location" placeholder="Location"
               value="<?= htmlspecialchars($params['location']) ?>">
        <input name="education" placeholder="Required education"
               value="<?= htmlspecialchars($params['required_education']) ?>">
        <input name="min_experience" type="number" min="0" placeholder="Max years required"
               value="<?= htmlspecialchars($params['min_experience']) ?>">
        <button type="submit">Search</button>
        <?php if ($hasFilters): ?>
            <a href="jobs.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($jobs)): ?>
        <p>No jobs match those filters.</p>
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
