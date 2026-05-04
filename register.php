<?php
require_once __DIR__ . '/bootstrap.php';

use App\Auth;
use App\Session;

Session::start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role']     ?? '';

    if ($email === '' || $password === '' || $role === '') {
        $error = 'All fields are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $userId = Auth::register($email, $password, $role);
        if ($userId === null) {
            $error = 'Email already in use, or invalid role.';
        } else {
            // Auto-login after register.
            Auth::attempt($email, $password);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header><h1>Register</h1></header>
<main>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <label>I am a...
            <select name="role" required>
                <option value="">-- choose --</option>
                <option value="candidate">Candidate (job seeker)</option>
                <option value="employer">Employer (company)</option>
            </select>
        </label>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a>.</p>
</main>
</body>
</html>
