<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\CandidateRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$keyword    = trim($_GET['q'] ?? '');
$candidates = $keyword === '' ? CandidateRepository::listAll() : CandidateRepository::searchByKeyword($keyword);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search candidates</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Search candidates</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../employer/dashboard.php">Dashboard</a>
        <a href="../logout.php">Log out</a>
    </nav>
</header>
<main>
    <form method="GET" class="search-form">
        <input name="q" placeholder="Search by name or field of study..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (empty($candidates)): ?>
        <p>No candidates found<?= $keyword ? ' for "' . htmlspecialchars($keyword) . '"' : '' ?>.</p>
    <?php else: ?>
        <ul class="job-list">
            <?php foreach ($candidates as $candidate): ?>
                <li>
                    <strong><?= htmlspecialchars($candidate['full_name']) ?></strong>
                    — <?= htmlspecialchars($candidate['field_of_study'] ?? '') ?>
                    (<?= (int)$candidate['years_experience'] ?> yrs)
                    &lt;<?= htmlspecialchars($candidate['email'] ?? '') ?>&gt;
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
