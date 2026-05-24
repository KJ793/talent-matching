<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\CandidateRepository;
use App\Session;

Session::start();
Auth::requireLogin('candidate');

$userId  = Session::userId();
$profile = CandidateRepository::findByUserId($userId) ?? [];
$error   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['full_name'])) {
        $error = 'Full name is required.';
    } else {
        CandidateRepository::save($userId, $_POST);
        header('Location: ../candidate/profile.php');
        exit;
    }
}

$workModes = ['Any', 'Remote', 'On-site', 'Hybrid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Edit profile</h1>
    <nav><a href="../candidate/dashboard.php">Dashboard</a></nav>
</header>
<main>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST" class="auth-form">
        <h3>Basic information</h3>
        <label>Full name
            <input name="full_name" required value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>">
        </label>
        <label>Contact
            <input name="contact" value="<?= htmlspecialchars($profile['contact'] ?? '') ?>">
        </label>
        <label>Education
            <input name="education" value="<?= htmlspecialchars($profile['education'] ?? '') ?>">
        </label>
        <label>Field of study
            <input name="field_of_study" value="<?= htmlspecialchars($profile['field_of_study'] ?? '') ?>">
        </label>
        <label>Years of experience
            <input type="number" name="years_experience" min="0" value="<?= (int)($profile['years_experience'] ?? 0) ?>">
        </label>

        <h3>Skills &amp; experience</h3>
        <label>Skills (comma separated)
            <input name="skills" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>"
                   placeholder="e.g. PHP, JavaScript, SQL, Docker">
        </label>
        <label>Work experience
            <textarea name="work_experience" rows="4" placeholder="Briefly describe your work history..."><?= htmlspecialchars($profile['work_experience'] ?? '') ?></textarea>
        </label>

        <h3>Preferences</h3>
        <label>Preferred work mode
            <select name="preferred_work_mode">
                <?php foreach ($workModes as $mode): ?>
                    <option value="<?= $mode ?>" <?= ($profile['preferred_work_mode'] ?? 'Any') === $mode ? 'selected' : '' ?>><?= $mode ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Preferred location
            <input name="preferred_location" value="<?= htmlspecialchars($profile['preferred_location'] ?? '') ?>"
                   placeholder="e.g. Sydney">
        </label>

        <button type="submit">Save</button>
    </form>
</main>
</body>
</html>
