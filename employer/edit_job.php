<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\JobRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$userId = Session::userId();
$jobId  = (int)($_GET['id'] ?? 0);
$job    = JobRepository::find($jobId);

if (!$job || (int)$job['employer_id'] !== $userId) {
    http_response_code(404);
    die('Job not found.');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['title']) || empty($_POST['description'])) {
        $error = 'Title and description are required.';
    } else {
        JobRepository::update($jobId, $userId, $_POST);
        header('Location: ../employer/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit job</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Edit job</h1>
    <nav><a href="../employer/dashboard.php">Dashboard</a></nav>
</header>
<main>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST" class="auth-form">
        <label>Job title <input name="title" required value="<?= htmlspecialchars($job['title']) ?>"></label>
        <label>Description <textarea name="description" required rows="5"><?= htmlspecialchars($job['description']) ?></textarea></label>
        <label>Required education <input name="required_education" value="<?= htmlspecialchars($job['required_education']) ?>"></label>
        <label>Required skills <input name="required_skills" value="<?= htmlspecialchars($job['required_skills']) ?>"></label>
        <label>Years of experience required
            <input type="number" name="years_experience" min="0" value="<?= (int)$job['years_experience'] ?>">
        </label>
        <label>Work mode
            <select name="work_mode">
                <?php foreach (['On-site', 'Remote', 'Hybrid'] as $mode): ?>
                    <option <?= $job['work_mode'] === $mode ? 'selected' : '' ?>><?= $mode ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Location <input name="location" value="<?= htmlspecialchars($job['location']) ?>"></label>
        <button type="submit">Save</button>
    </form>
</main>
</body>
</html>
