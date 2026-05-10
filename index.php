<?php
require_once __DIR__ . '/bootstrap.php';

use App\Session;

Session::start();
$loggedIn = Session::isLoggedIn();
$role     = Session::role();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intelligent Talent Matching Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1>Intelligent Talent Matching Platform</h1>
    <nav>
        <?php if ($loggedIn): ?>
            <span>Logged in as <?= htmlspecialchars($role) ?></span>
            <?php if ($role === 'candidate'): ?>
                <a href="candidate/dashboard.php">Dashboard</a>
            <?php elseif ($role === 'employer'): ?>
                <a href="employer/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <?php if (!$loggedIn): ?>
        <p>Welcome. Please <a href="login.php">log in</a> or <a href="register.php">register</a> to get started.</p>
    <?php else: ?>
        <p>You are logged in. Use the dashboard to manage your account.</p>
    <?php endif; ?>
</main>
</body>
</html>
