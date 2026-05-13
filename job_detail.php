<?php
require_once __DIR__ . '/bootstrap.php';

use App\JobRepository;
use App\Session;

Session::start();

$jobId = (int)($_GET['id'] ?? 0);
$job   = JobRepository::find($jobId);

if (!$job) {
    http_response_code(404);
    die('Job not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1><?= htmlspecialchars($job['title']) ?></h1>
    <nav><a href="jobs.php">&larr; Back to jobs</a></nav>
</header>
<main>
    <p class="muted">
        <?= htmlspecialchars($job['company_name'] ?? '') ?>
        — <?= htmlspecialchars($job['location']) ?>
        — <?= htmlspecialchars($job['work_mode']) ?>
    </p>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>

    <h3>Requirements</h3>
    <ul>
        <li>Education: <?= htmlspecialchars($job['required_education']) ?></li>
        <li>Skills: <?= htmlspecialchars($job['required_skills']) ?></li>
        <li>Experience: <?= (int)$job['years_experience'] ?> years</li>
    </ul>

    <h3>About the company</h3>
    <p><?= htmlspecialchars($job['company_info'] ?? '') ?></p>
</main>
</body>
</html>