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
    <title>Candidate dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Candidate dashboard</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../logout.php">Log out</a>
    </nav>
</header>
<main>
    <h2>Welcome<?= $profile ? ', ' . htmlspecialchars($profile['full_name']) : '' ?></h2>
    <ul class="dash">
        <li><a href="../candidate/profile.php">View my profile</a></li>
        <li><a href="../candidate/edit_profile.php">Edit my profile</a></li>
    </ul>
</main>
</body>
</html>
