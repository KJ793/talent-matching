<?php
require_once __DIR__ . '/bootstrap.php';

use App\Auth;
use App\Session;
use App\UserRepository;

Session::start();
Auth::requireLogin();

$userId = Session::userId();
$current = UserRepository::getMembership($userId);
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = $_POST['membership'] ?? '';
    if (in_array($target, ['free', 'premium'], true)) {
        UserRepository::setMembership($userId, $target);
        $current = $target;
        $message = 'Membership updated to ' . htmlspecialchars($target) . '.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Membership</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1>Membership</h1>
    <nav><a href="index.php">Home</a></nav>
</header>
<main>
    <?php if ($message): ?><p class="success"><?= $message ?></p><?php endif; ?>

    <p>Your current membership: <strong><?= htmlspecialchars($current) ?></strong></p>

    <h3>Plans</h3>
    <ul>
        <li><strong>Free</strong> — up to 10 recommended jobs/candidates.</li>
        <li><strong>Premium</strong> — unlimited recommendations.</li>
    </ul>

    <p class="muted">
        This is a project prototype, so upgrading and downgrading is instant
        and does not involve real payment. Pick whichever you'd like to try.
    </p>

    <form method="POST" class="auth-form">
        <label>Choose a plan
            <select name="membership">
                <option value="free"    <?= $current === 'free'    ? 'selected' : '' ?>>Free</option>
                <option value="premium" <?= $current === 'premium' ? 'selected' : '' ?>>Premium</option>
            </select>
        </label>
        <button type="submit">Update</button>
    </form>
</main>
</body>
</html>
