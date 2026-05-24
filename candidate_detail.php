<?php
require_once __DIR__ . '/bootstrap.php';

use App\Auth;
use App\CandidateRepository;
use App\Session;

Session::start();
Auth::requireLogin('employer');

$id        = (int)($_GET['id'] ?? 0);
$candidate = CandidateRepository::findByUserId($id);

if (!$candidate) {
    http_response_code(404);
    die('Candidate not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($candidate['full_name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1><?= htmlspecialchars($candidate['full_name']) ?></h1>
    <nav><a href="candidates.php">&larr; Back to candidates</a></nav>
</header>
<main>
    <dl class="profile">
        <dt>Email</dt>               <dd><?= htmlspecialchars($candidate['email']) ?></dd>
        <dt>Contact</dt>             <dd><?= htmlspecialchars($candidate['contact']) ?></dd>
        <dt>Education</dt>           <dd><?= htmlspecialchars($candidate['education']) ?></dd>
        <dt>Field of study</dt>      <dd><?= htmlspecialchars($candidate['field_of_study']) ?></dd>
        <dt>Years of experience</dt> <dd><?= (int)$candidate['years_experience'] ?></dd>
        <dt>Skills</dt>              <dd><?= htmlspecialchars($candidate['skills'] ?? '') ?></dd>
        <dt>Work experience</dt>     <dd><?= nl2br(htmlspecialchars($candidate['work_experience'] ?? '')) ?></dd>
        <dt>Preferred work mode</dt> <dd><?= htmlspecialchars($candidate['preferred_work_mode'] ?? 'Any') ?></dd>
        <dt>Preferred location</dt>  <dd><?= htmlspecialchars($candidate['preferred_location'] ?? '') ?></dd>
    </dl>
</main>
</body>
</html>