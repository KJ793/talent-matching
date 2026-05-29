<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\CandidateRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$params = [
    'keyword'            => trim($_GET['q'] ?? ''),
    'education'          => trim($_GET['education'] ?? ''),
    'work_mode'          => trim($_GET['work_mode'] ?? ''),
    'preferred_location' => trim($_GET['location'] ?? ''),
    'min_experience'     => trim($_GET['min_experience'] ?? ''),
];

$hasFilters = array_filter($params, static fn ($v) => $v !== '');
$candidates = $hasFilters ? CandidateRepository::search($params) : CandidateRepository::listAll();

$workModes  = ['Remote', 'On-site', 'Hybrid'];
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
        <input name="q" placeholder="Keyword (name, skills, experience)..."
               value="<?= htmlspecialchars($params['keyword']) ?>">
        <input name="education" placeholder="Education level"
               value="<?= htmlspecialchars($params['education']) ?>">
        <select name="work_mode">
            <option value="">Any work mode</option>
            <?php foreach ($workModes as $mode): ?>
                <option value="<?= $mode ?>" <?= $params['work_mode'] === $mode ? 'selected' : '' ?>>
                    <?= $mode ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input name="location" placeholder="Preferred location"
               value="<?= htmlspecialchars($params['preferred_location']) ?>">
        <input name="min_experience" type="number" min="0" placeholder="Min years experience"
               value="<?= htmlspecialchars($params['min_experience']) ?>">
        <button type="submit">Search</button>
        <?php if ($hasFilters): ?>
            <a href="search_candidates.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($candidates)): ?>
        <p>No candidates match those filters.</p>
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
