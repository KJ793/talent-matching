<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Auth;
use App\CandidateRepository;
use App\Session;

Session::start();
Auth::requireLogin('candidate');

$profile = CandidateRepository::findByUserId(Session::userId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>My Profile</h1>
    <nav>
        <a href="../candidate/dashboard.php">Dashboard</a>
        <a href="../logout.php">Log out</a>
    </nav>
</header>
<main>
    <?php if (!$profile): ?>
        <p>You haven't created a profile yet.</p>
        <p><a href="edit_profile.php">Create profile</a></p>
    <?php else: ?>
        <dl class="profile">
            <dt>Full name</dt>           <dd><?= htmlspecialchars($profile['full_name']) ?></dd>
            <dt>Contact</dt>             <dd><?= htmlspecialchars($profile['contact']) ?></dd>
            <dt>Education</dt>           <dd><?= htmlspecialchars($profile['education']) ?></dd>
            <dt>Field of study</dt>      <dd><?= htmlspecialchars($profile['field_of_study']) ?></dd>
            <dt>Years of experience</dt> <dd><?= (int)$profile['years_experience'] ?></dd>
            <dt>Skills</dt>              <dd><?= htmlspecialchars($profile['skills'] ?? '') ?></dd>
            <dt>Work experience</dt>     <dd><?= nl2br(htmlspecialchars($profile['work_experience'] ?? '')) ?></dd>
            <dt>Preferred work mode</dt> <dd><?= htmlspecialchars($profile['preferred_work_mode'] ?? 'Any') ?></dd>
            <dt>Preferred location</dt>  <dd><?= htmlspecialchars($profile['preferred_location'] ?? '') ?></dd>
        </dl>
        <p><a href="edit_profile.php">Edit profile</a></p>
    <?php endif; ?>
</main>
</body>
</html>
