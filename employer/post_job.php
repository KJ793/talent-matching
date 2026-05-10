<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\EmployerRepository;
use App\JobRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$userId   = Session::userId();
$error    = null;
$employer = EmployerRepository::findByUserId($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Make sure the employer has a company profile first.
    if (!$employer) {
        EmployerRepository::save($userId, [
            'company_name' => $_POST['company_name'] ?? 'Unnamed Company',
            'company_info' => $_POST['company_info'] ?? '',
        ]);
    }

    if (empty($_POST['title']) || empty($_POST['description'])) {
        $error = 'Title and description are required.';
    } else {
        JobRepository::create($userId, $_POST);
        header('Location: ../employer/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a job</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Post a job</h1>
    <nav><a href="../employer/dashboard.php">Dashboard</a></nav>
</header>
<main>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST" class="auth-form">
        <?php if (!$employer): ?>
            <h3>Company info (first time only)</h3>
            <label>Company name <input name="company_name" required></label>
            <label>Company info <textarea name="company_info"></textarea></label>
            <hr>
        <?php endif; ?>

        <label>Job title <input name="title" required></label>
        <label>Description <textarea name="description" required rows="5"></textarea></label>
        <label>Required education <input name="required_education"></label>
        <label>Required skills (comma separated) <input name="required_skills"></label>
        <label>Years of experience required
            <input type="number" name="years_experience" min="0" value="0">
        </label>
        <label>Work mode
            <select name="work_mode">
                <option>On-site</option>
                <option>Remote</option>
                <option>Hybrid</option>
            </select>
        </label>
        <label>Location <input name="location"></label>
        <button type="submit">Publish</button>
    </form>
</main>
</body>
</html>
