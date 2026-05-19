<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\EmployerRepository;
use App\JobRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$userId   = Session::userId();
$employer = EmployerRepository::findByUserId($userId);
$jobs     = JobRepository::listByEmployer($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employer dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Employer dashboard</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../logout.php">Log out</a>
    </nav>
</header>
<main>
    <h2><?= $employer ? htmlspecialchars($employer['company_name']) : 'Welcome' ?></h2>

    <ul class="dash">
        <li><a href="../employer/post_job.php">+ Post a new job</a></li>
        <li><a href="../candidates.php">Browse candidates</a></li>
        <li><a href="../employer/recommendations.php">Recommended candidates</a></li>
    </ul>

    <h3>My job postings</h3>
    <?php if (empty($jobs)): ?>
        <p>You haven't posted any jobs yet.</p>
    <?php else: ?>
        <ul class="job-list">
            <?php foreach ($jobs as $job): ?>
                <li>
                    <strong><?= htmlspecialchars($job['title']) ?></strong>
                    — <?= htmlspecialchars($job['location']) ?>
                    <a href="../employer/edit_job.php?id=<?= (int)$job['id'] ?>">Edit</a>
                    <a href="../employer/delete_job.php?id=<?= (int)$job['id'] ?>" onclick="return confirm('Delete this posting?')">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
